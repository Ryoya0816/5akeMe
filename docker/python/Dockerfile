FROM python:3.11-slim

WORKDIR /app

# システム依存パッケージ
RUN apt-get update && apt-get install -y \
    gcc \
    default-libmysqlclient-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Python依存パッケージ
COPY python/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# アプリケーションコード
COPY python/ .

# データ保存用ディレクトリ
RUN mkdir -p /app/data

# uvicorn でFastAPIを起動
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000", "--reload"]
