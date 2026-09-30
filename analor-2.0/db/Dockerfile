FROM python:3.11-slim

WORKDIR /app
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt
COPY atualiza_base.py .

ENTRYPOINT ["python", "/app/atualiza_base.py"]
