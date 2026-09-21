# Analor API

Backend Flask que expõe `/abrev` e `/search`, lendo do Cloud SQL
(Postgres) via o [Cloud SQL Python Connector](https://github.com/GoogleCloudPlatform/cloud-sql-python-connector).

## Estrutura

```
app.py                        entrypoint WSGI (gunicorn/waitress apontam para app:app)
analor_api/
  __init__.py                 app factory (Flask + CORS + logging)
  config.py                   configuração via variáveis de ambiente
  db.py                       engine SQLAlchemy + Cloud SQL Connector (pool de conexões)
  routes/                     camada HTTP (blueprints)
  services/                   validação e orquestração das requisições
  repositories/               acesso a dados (única camada que fala com o banco)
  query/                      construção parametrizada da cláusula WHERE
tests/                        testes do query builder
```

## Configuração

Nenhuma credencial fica no código. Tudo vem de variáveis de ambiente
(veja `.env.example`):

| Variável | Obrigatória | Descrição |
|---|---|---|
| `INSTANCE_CONNECTION_NAME` | sim | `project:region:instance` da instância Cloud SQL |
| `DB_USER` | sim | usuário do banco |
| `DB_PASS` | sim, exceto com `DB_IAM_AUTH=true` | senha do banco |
| `DB_NAME` | não (default `postgres`) | nome do banco |
| `DB_IAM_AUTH` | não (default `false`) | `true` para autenticar via IAM em vez de senha |
| `CORS_ORIGINS` | sim | origens permitidas, separadas por vírgula |

Faltando qualquer variável obrigatória, a aplicação falha ao subir com
uma mensagem clara, em vez de falhar silenciosamente numa requisição.

### Rodando localmente

```
cp .env.example .env   # preencha com as credenciais de um ambiente de dev/staging
pip install -r requirements-dev.txt
python app.py
```

### Testes

```
pip install -r requirements-dev.txt
pytest
```

## Deploy (Cloud Run + Artifact Registry)

A imagem é construída pelo `Dockerfile` e não inclui `.env`, `venv/`
nem `tests/` (veja `.dockerignore`).

```
gcloud builds submit --tag <REGION>-docker.pkg.dev/<PROJECT>/<REPO>/analor-api

gcloud run deploy analor-api \
  --image <REGION>-docker.pkg.dev/<PROJECT>/<REPO>/analor-api \
  --add-cloudsql-instances <INSTANCE_CONNECTION_NAME> \
  --set-env-vars INSTANCE_CONNECTION_NAME=<INSTANCE_CONNECTION_NAME>,DB_USER=<DB_USER>,DB_NAME=postgres,DB_IAM_AUTH=true,CORS_ORIGINS=https://analor.com.br
```

Recomendado: usar `DB_IAM_AUTH=true` com a conta de serviço do Cloud
Run mapeada para um usuário IAM do Postgres — assim não existe senha
de banco para armazenar ou vazar. Caso seja necessário usar senha,
guarde-a no Secret Manager e injete com `--set-secrets DB_PASS=<secret>:latest`
em vez de `--set-env-vars`.
