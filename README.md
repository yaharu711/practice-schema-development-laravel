# 環境セットアップ手順（最小）

このリポジトリは Docker Compose 上で Laravel + Nginx + Postgres の最小構成を起動します。Laravel 本体は `src/` 配下に作成します。

## 前提

- Docker Desktop がインストール済み・起動中であること
- ホスト側の 5432 を使用中でも問題ありません（DB ポートは公開していません）

## 初回セットアップ

```bash
# 環境変数のテンプレートから作成（必要なら値を修正）
cp docker/app.env.example docker/app.env

# コンテナ起動 / ビルド
make up

# 空の src/ に Laravel を新規作成
make install

# アプリケーションキー生成（.env に書き込み）
make key

# 念のため設定キャッシュをクリア
docker compose exec app php artisan config:clear
```

- ブラウザで http://localhost:8080 を開き、Laravel 初期画面が表示されれば OK。

## OpenAPI 仕様の取得とスタブ生成（任意）

`make gen` はデフォルトで contract リポの Raw URL（`main`）を参照し、`generated/laravel/` に php-laravel スタブを生成します（生成物はコミットしません）。

```bash
# デフォルト（main）
make gen

# 仕様URLを指定したい場合（例: タグ固定 v0.1.0）
./scripts/gen-laravel.sh \
  "https://raw.githubusercontent.com/yaharu711/practice-todo-contract/v0.1.0/openapi/todo.yaml" \
  generated/laravel

# 生成物の確認（参考）
ls -la generated/laravel | head
```

## ディレクトリ構成（要点）

- `src/`: Laravel 本体（`make install` で作成）
- `docker/`: PHP / Nginx / 環境変数（`docker/app.env`）
- `generated/`: 生成物の出力先（Git 管理外、`.gitkeep` のみ）
- `scripts/gen-laravel.sh`: openapi-generator でスタブ生成
  - 入力は URL/ローカルファイルのどちらでも可（未指定なら Raw URL）

## 環境変数について

- 開発用: `docker/app.env.example` をコミットし、各自が `docker/app.env` を作成して利用します（`.gitignore` 済み）。
- `APP_KEY` はコンテナ注入しません。`make key` により `src/.env`（Git 管理外）に生成されます。
- ルート直下の `.env` は使用しません（混同回避のためコミット対象外）。
