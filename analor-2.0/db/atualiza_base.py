"""Valida e carrega a base principal (tabela analor_0_0_1) a partir do
.sql exportado pelo dono do site.

O .sql de origem traz um CREATE TABLE e um INSERT por composto, mas
não roda direto no Postgres: campos numéricos vazios vêm como '' (o
Postgres não converte '' para integer/numeric), o arquivo é Latin-1 e o
nome da tabela muda a cada exportação. Este script interpreta o
arquivo, valida cada valor contra o tipo da sua coluna e só então
carrega.

Uso:
    python atualiza_base.py BASE.sql              # só valida (offline)
    python atualiza_base.py BASE.sql --simular    # roda tudo no banco e desfaz
    python atualiza_base.py BASE.sql --aplicar    # roda tudo no banco e confirma

--simular/--aplicar usam as mesmas variáveis de ambiente da API
(INSTANCE_CONNECTION_NAME, DB_USER, DB_PASS, DB_NAME, DB_IAM_AUTH).

A carga acontece numa única transação: a base nova é montada em
analor_0_0_1_novo e só no final troca de nome com a atual (que fica
guardada como analor_0_0_1_anterior). Qualquer erro desfaz tudo e a
base em produção continua exatamente como estava.
"""
from __future__ import annotations

import argparse
import os
import re
import sys
from dataclasses import dataclass
from decimal import Decimal, InvalidOperation

TABELA = "analor_0_0_1"
USUARIO_API = "analor-api-sa@handy-vortex-458519-u5.iam"

# Colunas que a API (where_clause_builder) e o front leem pelo nome.
COLUNAS_OBRIGATORIAS = {
    "cas", "niup", "ncom", "ningles", "nlin", "flin", "pmol", "fmol",
    "carb", "hidr", "oxig", "nitr", "enxo", "clor", "brom", "iodo", "fluo",
    "pf", "pfcarac", "pe", "pecarac", "pfder",
    *(f"ms{i}" for i in range(1, 9)), *(f"ims{i}" for i in range(1, 9)),
    "smiles", "estrutura", "no_ifrj",
}

# Proteção contra arquivo truncado: a base nova precisa ter pelo menos
# esta fração das linhas da base atual.
FRACAO_MINIMA_LINHAS = 0.8

LINHAS_POR_INSERT = 500


class ErroBase(Exception):
    pass


@dataclass
class Coluna:
    nome: str
    tipo: str  # "char", "integer" ou "numeric"
    tamanho: int = 0  # char(n)
    precisao: int = 0  # numeric(p, s)
    escala: int = 0

    def ddl(self) -> str:
        if self.tipo == "char":
            return f"{self.nome} char({self.tamanho})"
        if self.tipo == "numeric":
            return f"{self.nome} numeric({self.precisao},{self.escala})"
        return f"{self.nome} integer"


def ler_texto(caminho: str) -> str:
    dados = open(caminho, "rb").read()
    try:
        return dados.decode("utf-8")
    except UnicodeDecodeError:
        return dados.decode("latin-1")


def separar_comandos(texto: str) -> list[str]:
    """Divide o arquivo em comandos por ';', ignorando ';' dentro de
    strings entre aspas simples."""
    comandos, atual, em_string = [], [], False
    for c in texto:
        if c == "'":
            em_string = not em_string  # '' (aspa escapada) alterna duas vezes
        if c == ";" and not em_string:
            comandos.append("".join(atual).strip())
            atual = []
        else:
            atual.append(c)
    if "".join(atual).strip():
        comandos.append("".join(atual).strip())
    return [c for c in comandos if c]


def ler_valores(corpo: str) -> list[str | None]:
    valores, i = [], 0
    while i < len(corpo):
        c = corpo[i]
        if c in " \t\r\n,":
            i += 1
        elif c == "'":
            j, partes = i + 1, []
            while True:
                if j >= len(corpo):
                    raise ErroBase("string sem aspa de fechamento")
                if corpo[j] == "'" and corpo[j + 1 : j + 2] == "'":
                    partes.append("'")
                    j += 2
                elif corpo[j] == "'":
                    break
                else:
                    partes.append(corpo[j])
                    j += 1
            valores.append("".join(partes))
            i = j + 1
        else:
            j = i
            while j < len(corpo) and corpo[j] != ",":
                j += 1
            bruto = corpo[i:j].strip()
            if bruto.lower() != "null":
                raise ErroBase(f"valor sem aspas: {bruto!r}")
            valores.append(None)
            i = j
    return valores


def ler_colunas(corpo: str) -> list[Coluna]:
    colunas = []
    for definicao in re.split(r",(?![^()]*\))", corpo):
        definicao = definicao.strip()
        if not definicao:
            continue
        m = re.fullmatch(
            r"(\w+)\s+(char|integer|numeric)\s*(?:\((\d+)(?:\s*,\s*(\d+))?\))?",
            definicao,
            re.I,
        )
        if not m:
            raise ErroBase(f"definição de coluna não reconhecida: {definicao!r}")
        nome, tipo, a, b = m.group(1).lower(), m.group(2).lower(), m.group(3), m.group(4)
        if tipo == "char":
            colunas.append(Coluna(nome, "char", tamanho=int(a or 1)))
        elif tipo == "numeric":
            colunas.append(Coluna(nome, "numeric", precisao=int(a), escala=int(b or 0)))
        else:
            colunas.append(Coluna(nome, "integer"))
    return colunas


def converter(coluna: Coluna, valor: str | None):
    """Converte um valor do arquivo para o tipo Python correspondente.
    Levanta ValueError com a explicação quando o valor é inválido."""
    if valor is None:
        return None
    if coluna.tipo == "char":
        if len(valor.rstrip(" ")) > coluna.tamanho:
            raise ValueError(f"texto com {len(valor)} caracteres, limite {coluna.tamanho}")
        return valor
    if valor.strip() == "":
        return None
    if coluna.tipo == "integer":
        if not re.fullmatch(r"[+-]?\d+", valor.strip()):
            raise ValueError("não é um número inteiro")
        return int(valor)
    try:
        return Decimal(valor.strip())
    except InvalidOperation:
        raise ValueError("não é um número") from None


def interpretar(caminho: str):
    """Retorna (colunas, linhas, avisos) ou levanta ErroBase com a lista
    de problemas encontrados."""
    colunas: list[Coluna] | None = None
    linhas_brutas: list[tuple[int, list]] = []
    erros: list[str] = []

    for n, comando in enumerate(separar_comandos(ler_texto(caminho)), 1):
        if re.match(r"drop\s+table\b", comando, re.I):
            continue  # a troca de tabela é feita por este script
        m = re.match(r"create\s+table\s+\w+\s*\((.*)\)\s*$", comando, re.I | re.S)
        if m:
            if colunas is not None:
                raise ErroBase("o arquivo tem mais de um CREATE TABLE")
            colunas = ler_colunas(m.group(1))
            continue
        m = re.match(r"insert\s+into\s+\w+\s+values\s*\((.*)\)\s*$", comando, re.I | re.S)
        if m:
            try:
                linhas_brutas.append((n, ler_valores(m.group(1))))
            except ErroBase as exc:
                erros.append(f"comando {n}: {exc}")
            continue
        erros.append(f"comando {n}: comando inesperado: {comando[:80]!r}")

    if colunas is None:
        raise ErroBase("o arquivo não tem CREATE TABLE")
    faltando = COLUNAS_OBRIGATORIAS - {c.nome for c in colunas}
    if faltando:
        erros.append(f"colunas usadas pelo site ausentes no CREATE TABLE: {sorted(faltando)}")

    idx_cas = next((i for i, c in enumerate(colunas) if c.nome == "cas"), 0)
    linhas = []
    for n, valores in linhas_brutas:
        if len(valores) != len(colunas):
            erros.append(f"comando {n}: {len(valores)} valores para {len(colunas)} colunas")
            continue
        linha = []
        for coluna, valor in zip(colunas, valores):
            try:
                linha.append(converter(coluna, valor))
            except ValueError as exc:
                erros.append(f"CAS {valores[idx_cas]} ({coluna.nome} = {valor!r}): {exc}")
        linhas.append(linha)

    if not linhas:
        erros.append("o arquivo não tem nenhum INSERT")
    if erros:
        raise ErroBase("\n".join(erros))

    avisos = ampliar_numericos(colunas, linhas, idx_cas)
    return colunas, linhas, avisos


def ampliar_numericos(colunas: list[Coluna], linhas: list[list], idx_cas: int) -> list[str]:
    """Um numeric(p,s) só guarda p-s dígitos antes da vírgula. Quando o
    arquivo traz valores maiores (ex.: 1000 em numeric(7,4)), a precisão
    da coluna é ampliada, sem alterar nenhum valor, e isso é avisado."""
    avisos = []
    for i, coluna in enumerate(colunas):
        if coluna.tipo != "numeric":
            continue
        limite = Decimal(10) ** (coluna.precisao - coluna.escala)
        grandes = [(linha[idx_cas], linha[i]) for linha in linhas if linha[i] is not None and abs(linha[i]) >= limite]
        if not grandes:
            continue
        digitos = max(len(str(int(abs(v)))) for _, v in grandes)
        antes = coluna.ddl()
        coluna.precisao = digitos + coluna.escala
        exemplos = ", ".join(f"{str(cas).strip()}={v}" for cas, v in grandes[:5])
        avisos.append(
            f"{antes} ampliada para numeric({coluna.precisao},{coluna.escala}): "
            f"{len(grandes)} valor(es) não cabiam (ex.: {exemplos})"
        )
    return avisos


def conectar():
    from google.cloud.sql.connector import Connector

    iam = os.environ.get("DB_IAM_AUTH", "false").strip().lower() == "true"
    connector = Connector()
    try:
        conexao = connector.connect(
            os.environ["INSTANCE_CONNECTION_NAME"],
            "pg8000",
            user=os.environ["DB_USER"],
            password=None if iam else os.environ["DB_PASS"],
            db=os.environ.get("DB_NAME", "postgres"),
            enable_iam_auth=iam,
        )
    except Exception:
        connector.close()
        raise
    return connector, conexao


def carregar(colunas: list[Coluna], linhas: list[list], confirmar: bool) -> None:
    novo, anterior = f"{TABELA}_novo", f"{TABELA}_anterior"
    connector, conexao = conectar()
    try:
        cur = conexao.cursor()  # pg8000 abre a transação no primeiro comando
        cur.execute("SET LOCAL lock_timeout = '30s'")
        cur.execute(f"DROP TABLE IF EXISTS {novo}")
        cur.execute(f"CREATE TABLE {novo} ({', '.join(c.ddl() for c in colunas)})")

        marcadores = "(" + ", ".join(["%s"] * len(colunas)) + ")"
        for inicio in range(0, len(linhas), LINHAS_POR_INSERT):
            lote = linhas[inicio : inicio + LINHAS_POR_INSERT]
            cur.execute(
                f"INSERT INTO {novo} VALUES " + ", ".join([marcadores] * len(lote)),
                [v for linha in lote for v in linha],
            )
            print(f"  {min(inicio + LINHAS_POR_INSERT, len(linhas))}/{len(linhas)} linhas inseridas")

        cur.execute(f"SELECT count(*) FROM {novo}")
        total_novo = cur.fetchone()[0]
        cur.execute("SELECT to_regclass(%s) IS NOT NULL", [TABELA])
        if cur.fetchone()[0]:
            cur.execute(f"SELECT count(*) FROM {TABELA}")
            total_atual = cur.fetchone()[0]
            print(f"  base atual: {total_atual} linhas | base nova: {total_novo} linhas")
            if total_novo < total_atual * FRACAO_MINIMA_LINHAS:
                raise ErroBase(
                    f"a base nova tem {total_novo} linhas, menos de "
                    f"{FRACAO_MINIMA_LINHAS:.0%} das {total_atual} atuais; arquivo incompleto?"
                )
            cur.execute(f"DROP TABLE IF EXISTS {anterior}")
            cur.execute(f"ALTER TABLE {TABELA} RENAME TO {anterior}")
        cur.execute(f"ALTER TABLE {novo} RENAME TO {TABELA}")
        cur.execute(f'GRANT SELECT ON {TABELA} TO "{USUARIO_API}"')

        if confirmar:
            conexao.commit()
            print(f"OK: {TABELA} atualizada ({total_novo} linhas). A versão anterior ficou em {anterior}.")
        else:
            conexao.rollback()
            print("OK: simulação completa sem erros. Nada foi alterado (rollback).")
    except Exception:
        conexao.rollback()
        raise
    finally:
        conexao.close()
        connector.close()


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("arquivo")
    modo = parser.add_mutually_exclusive_group()
    modo.add_argument("--simular", action="store_true", help="roda no banco e desfaz no final")
    modo.add_argument("--aplicar", action="store_true", help="roda no banco e confirma")
    args = parser.parse_args()

    try:
        colunas, linhas, avisos = interpretar(args.arquivo)
    except ErroBase as exc:
        print(f"ERRO: o arquivo não pode ser carregado:\n{exc}", file=sys.stderr)
        return 1

    print(f"Arquivo válido: {len(linhas)} linhas, {len(colunas)} colunas.")
    for aviso in avisos:
        print(f"AVISO: {aviso}")

    if args.simular or args.aplicar:
        try:
            carregar(colunas, linhas, confirmar=args.aplicar)
        except Exception as exc:
            print(f"ERRO no banco (nada foi alterado): {exc}", file=sys.stderr)
            return 1
    return 0


if __name__ == "__main__":
    sys.exit(main())
