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
nem `tests/` (veja `.dockerignore`). Deploy manual, fora da pipeline
(veja seção **CI/CD** abaixo para o deploy automático):

```
gcloud builds submit --tag southamerica-east1-docker.pkg.dev/handy-vortex-458519-u5/analor-react-app-repository/analor_api_0.0.1

gcloud run deploy analor-api-0-0-1 \
  --image southamerica-east1-docker.pkg.dev/handy-vortex-458519-u5/analor-react-app-repository/analor_api_0.0.1 \
  --region us-central1 \
  --service-account analor-api-sa@handy-vortex-458519-u5.iam.gserviceaccount.com \
  --add-cloudsql-instances handy-vortex-458519-u5:us-central1:analor-pg \
  --update-env-vars "^;^INSTANCE_CONNECTION_NAME=handy-vortex-458519-u5:us-central1:analor-pg;DB_USER=analor-api-sa@handy-vortex-458519-u5.iam;DB_NAME=postgres;DB_IAM_AUTH=true;CORS_ORIGINS=https://analor-front-0-0-1-41236692482.us-central1.run.app,https://analor.com.br"
```

(O Artifact Registry `analor-react-app-repository` fica em
`southamerica-east1`, região diferente de onde os serviços do Cloud
Run rodam — `us-central1` — e isso é normal, cada um tem sua própria
região. O `^;^` no `--update-env-vars` troca o separador de `,` para
`;`, já que `CORS_ORIGINS` tem vírgulas dentro do próprio valor.)

### IAM Database Authentication — passo a passo

Em vez de senha, o Cloud Run autentica no Postgres usando a identidade
da própria service account (Automatic IAM Database Authentication). O
código já suporta isso (`DB_IAM_AUTH=true` em `config.py`/`db.py`) —
falta só a configuração do lado do GCP/Cloud SQL:

1. **Habilite IAM auth na instância** (requer reinício):
   ```
   gcloud sql instances patch analor-pg \
     --database-flags cloudsql.iam_authentication=on
   ```

2. **Crie a service account dedicada do backend** (se ainda não existir):
   ```
   gcloud iam service-accounts create analor-api-sa \
     --display-name "Analor API (Cloud Run)"
   ```

3. **Conceda os papéis do Cloud SQL para essa service account**:
   ```
   gcloud projects add-iam-policy-binding handy-vortex-458519-u5 \
     --member "serviceAccount:analor-api-sa@handy-vortex-458519-u5.iam.gserviceaccount.com" \
     --role "roles/cloudsql.client"

   gcloud projects add-iam-policy-binding handy-vortex-458519-u5 \
     --member "serviceAccount:analor-api-sa@handy-vortex-458519-u5.iam.gserviceaccount.com" \
     --role "roles/cloudsql.instanceUser"
   ```

4. **Crie o usuário IAM dentro do Postgres**, usando o e-mail completo
   da service account:
   ```
   gcloud sql users create analor-api-sa@handy-vortex-458519-u5.iam.gserviceaccount.com \
     --instance=analor-pg \
     --type=cloud_iam_service_account
   ```
   O Postgres remove o sufixo `.gserviceaccount.com` do nome de
   usuário internamente — é por isso que `DB_USER` na aplicação deve
   ser `analor-api-sa@handy-vortex-458519-u5.iam` (sem o sufixo).

5. **Conceda as permissões SQL para esse novo usuário** (conectando
   como um usuário com privilégio, ex. `analor-app` ou `postgres`,
   uma única vez):
   ```sql
   GRANT SELECT ON abrev, analor_0_0_1 TO "analor-api-sa@handy-vortex-458519-u5.iam";
   ```
   (Criar o usuário IAM no Cloud SQL não concede automaticamente
   nenhum privilégio nas tabelas — isso é feito via `GRANT` normal do
   Postgres.)

6. **Aponte o Cloud Run para essa service account e ligue o IAM auth**:
   ```
   gcloud run services update analor-api-0-0-1 \
     --region us-central1 \
     --service-account analor-api-sa@handy-vortex-458519-u5.iam.gserviceaccount.com \
     --update-env-vars "^;^DB_USER=analor-api-sa@handy-vortex-458519-u5.iam;DB_IAM_AUTH=true"
   ```
   (A pipeline de CI/CD abaixo já faz isso a cada deploy — esse passo
   manual é só para a primeira migração ou para revalidar fora dela.)

7. **Teste** (`/abrev` ou `/search`) e confira os logs do Cloud Run —
   se a conexão falhar por permissão, o erro do pg8000/connector
   aparece ali.

8. **Revogue a senha antiga**: depois de confirmar que a IAM auth
   funciona, remova o usuário de senha antigo (`analor-app`) ou pelo
   menos troque a senha dele no Cloud SQL, já que ela esteve exposta
   no histórico do git.

## CI/CD

Duas Cloud Build triggers (backend e frontend), cada uma disparada só
quando os arquivos do respectivo serviço mudam na branch
`front-remake`, apontando para `analor-2.0/api/cloudbuild.yaml` e
`analor-2.0/cloudbuild.yaml`.

### Configuração inicial (uma vez só)

1. Conecte o repositório GitHub ao Cloud Build (Console → Cloud Build
   → Triggers → *Connect Repository*, ou `gcloud builds repositories
   create` se já usa a conexão 2ª geração). Isso exige OAuth no
   navegador, então precisa ser feito por você.

2. Dê à service account do Cloud Build permissão para publicar no
   Artifact Registry e fazer deploy no Cloud Run:
   ```
   PROJECT_NUMBER=$(gcloud projects describe handy-vortex-458519-u5 --format='value(projectNumber)')
   CB_SA="${PROJECT_NUMBER}@cloudbuild.gserviceaccount.com"

   gcloud projects add-iam-policy-binding handy-vortex-458519-u5 \
     --member "serviceAccount:${CB_SA}" --role "roles/run.admin"
   gcloud projects add-iam-policy-binding handy-vortex-458519-u5 \
     --member "serviceAccount:${CB_SA}" --role "roles/artifactregistry.writer"
   gcloud projects add-iam-policy-binding handy-vortex-458519-u5 \
     --member "serviceAccount:${CB_SA}" --role "roles/iam.serviceAccountUser"
   ```

3. Crie as duas triggers:
   ```
   gcloud builds triggers create github \
     --name="analor-api-release" \
     --repo-name="analor" --repo-owner="osierpe" \
     --branch-pattern="^front-remake$" \
     --build-config="analor-2.0/api/cloudbuild.yaml" \
     --included-files="analor-2.0/api/**"

   gcloud builds triggers create github \
     --name="analor-front-release" \
     --repo-name="analor" --repo-owner="osierpe" \
     --branch-pattern="^front-remake$" \
     --build-config="analor-2.0/cloudbuild.yaml" \
     --included-files="analor-2.0/**" \
     --ignored-files="analor-2.0/api/**"
   ```

4. Os `substitutions` no topo de cada `cloudbuild.yaml` já apontam
   para o estado real do projeto (`analor-react-app-repository` em
   `southamerica-east1`, serviços `analor-api-0-0-1` / `analor-front-0-0-1`
   em `us-central1`) — ajuste só se algo mudar.

A partir daí, todo push em `front-remake` que tocar em
`analor-2.0/api/**` builda, testa (`pytest`) e faz deploy do backend;
um push que tocar no restante de `analor-2.0/**` builda e faz deploy
do frontend.
