# Carga da base principal (analor_0_0_1)

Tudo que é preciso para atualizar a tabela `analor_0_0_1` (instância
Cloud SQL `analor-pg`) a partir do `.sql` exportado pelo dono do site.

## Como subir uma base nova (dono do site)

1. Abra o bucket no console:
   https://console.cloud.google.com/storage/browser/analor-bases?project=handy-vortex-458519-u5
2. Clique em **Fazer upload** e escolha o arquivo `.sql` (ex.: `UNIV1_290926.sql`).
   Use um nome novo a cada base; os arquivos antigos ficam guardados
   no bucket e servem para voltar a uma base anterior (basta subir de
   novo o arquivo antigo com outro nome, ex. `UNIV1_290926_v2.sql`).
3. A carga começa sozinha e leva cerca de 1 minuto. Acompanhe em:
   https://console.cloud.google.com/cloud-build/builds?project=handy-vortex-458519-u5
   - **verde**: a base nova já está no site;
   - **vermelho**: nada foi alterado, o site continua com a base
     anterior. Abra o build e veja o passo `carrega`: a mensagem diz o
     CAS, a coluna e o valor com problema. Corrija o arquivo e suba de novo.

Só arquivos terminados em `.sql` disparam a carga.

## O que a carga faz

`atualiza_base.py` lê o `.sql` (Latin-1 ou UTF-8), valida cada valor
contra o tipo da sua coluna, converte `''` em NULL nos campos numéricos
e amplia colunas `numeric` pequenas demais sem alterar valores. Depois,
numa única transação:

- monta a base nova em `analor_0_0_1_novo`;
- recusa a carga se ela tiver menos de 80% das linhas da atual
  (arquivo cortado);
- troca: a atual vira `analor_0_0_1_anterior` e a nova vira
  `analor_0_0_1`;
- refaz o `GRANT SELECT` para o usuário IAM da API.

Qualquer erro desfaz tudo. Para voltar na hora à base anterior:

```sql
BEGIN;
ALTER TABLE analor_0_0_1 RENAME TO analor_0_0_1_tmp;
ALTER TABLE analor_0_0_1_anterior RENAME TO analor_0_0_1;
ALTER TABLE analor_0_0_1_tmp RENAME TO analor_0_0_1_anterior;
COMMIT;
```

## Rodar localmente

Com `analor-2.0/api/.env` preenchido (usuário `analor-app`):

```
python db/atualiza_base.py BASE.sql              # só valida (offline)
python db/atualiza_base.py BASE.sql --simular    # roda no banco e desfaz
python db/atualiza_base.py BASE.sql --aplicar    # roda no banco e confirma
```

As variáveis do `.env` precisam estar no ambiente (o script não lê o
arquivo sozinho).

## Infraestrutura

| Recurso | Nome |
|---|---|
| Bucket | `gs://analor-bases` (notificação `OBJECT_FINALIZE` → tópico Pub/Sub `analor-bases`) |
| Trigger do Cloud Build | `analor-carga-base` (config inline = `carga.yaml`) |
| Imagem | `southamerica-east1-docker.pkg.dev/handy-vortex-458519-u5/analor-react-app-repository/analor_carga:latest` |
| Service account | `analor-carga-sa` (Cloud SQL Client, leitura do bucket e da imagem, acesso ao secret) |
| Secret | `analor-db-pass` (senha do `analor-app`) |

Ao alterar **`atualiza_base.py`**, publique a imagem de novo:

```
gcloud builds submit --tag southamerica-east1-docker.pkg.dev/handy-vortex-458519-u5/analor-react-app-repository/analor_carga:latest analor-2.0/db
```

Ao alterar **`carga.yaml`**, atualize o trigger:

```
gcloud builds triggers import --source=<export editado>
```

(ou `gcloud builds triggers export analor-carga-base --destination=t.yaml`,
cole o novo conteúdo em `build:` e importe).

Ao trocar a **senha do `analor-app`**:

```
gcloud secrets versions add analor-db-pass --data-file=-   # digite a senha e Ctrl+D
```

Para dar acesso a alguém (subir arquivos e ver o resultado) — já
concedido ao dono do site, `msierpe1@gmail.com`:

```
gcloud storage buckets add-iam-policy-binding gs://analor-bases --member=user:EMAIL --role=roles/storage.objectAdmin
gcloud storage buckets add-iam-policy-binding gs://analor-bases --member=user:EMAIL --role=roles/storage.legacyBucketReader
gcloud projects add-iam-policy-binding handy-vortex-458519-u5 --member=user:EMAIL --role=roles/browser
gcloud projects add-iam-policy-binding handy-vortex-458519-u5 --member=user:EMAIL --role=roles/cloudbuild.builds.viewer
gcloud projects add-iam-policy-binding handy-vortex-458519-u5 --member=user:EMAIL --role=roles/logging.viewer
```
