# Tissue

a.k.a. shikorism.net

シコリズムネットにて提供している夜のライフログサービスです。
(思想的には [shibafu528/SperMaster](https://github.com/shibafu528/SperMaster) の後継となります)

## 構成

- Laravel 5.5
- Bootstrap 4.0

## 実行環境

- PHP 7.1
- PostgreSQL 9.6

## 開発環境の構築

Docker を用いた開発環境の構築方法です。

1. `.env` ファイルを用意します。`.env.example` をコピーすることで用意ができます。

2. Docker イメージをビルドします

```
docker-compose build
```

3. Docker コンテナを起動します。

```
docker-compose up -d
```

4. Composer を使い必要なライブラリをインストールします。

```
docker-compose exec web composer install
```

5. 暗号化キーの作成と、データベースのマイグレーションを行います。

```
docker-compose exec web php artisan key:generate
docker-compose exec web php artisan migrate
```

これで準備は完了です。Tissue が動いていれば `http://localhost:4545/` でアクセスができます。

## 環境構築上の諸注意

- 初版時点では、DB サーバとして PostgreSQL を使うよう .env ファイルを設定するくらいです。
  当分、PostgreSQL から変える気はないので専用 SQL 等を平気で使います。
