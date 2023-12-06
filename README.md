# Tissue

a.k.a. shikorism.net

シコリズムネットにて提供している夜のライフログサービスです。
(思想的には [shibafu528/SperMaster](https://github.com/shibafu528/SperMaster) の後継となります)

## 構成

- Laravel 9
- Bootstrap 4.5.0

## 実行環境

- PHP 8.0
- PostgreSQL 14

> [!WARNING]
> 2021年11月以前に環境を構築したことがある場合、データベースのバージョンアップ作業が必要です！  
> [開発環境向けの移行手順](https://github.com/shikorism/tissue/issues/752#issuecomment-939257394) を参考にしてください。

## 開発環境の構築

Docker を用いた開発環境の構築方法です。

1. `.env` ファイルを用意します。`.env.example` をコピーすることで用意ができます。

2. Docker イメージをビルドします

```
docker compose build
```

3. Docker コンテナを起動します。

```
docker compose up -d
```

4. Composer と yarn を使い必要なライブラリをインストールします。

```
docker compose exec web composer install
docker compose exec web yarn install
```

5. 暗号化キーの作成と、データベースのマイグレーションおよびシーディングを行います。

```
docker compose exec web php artisan key:generate
docker compose exec web php artisan migrate
docker compose exec web php artisan db:seed
```

6. OAuth2サーバ設定の初期化を行います。

```
docker compose exec web php artisan passport:install
```

コマンドを実行すると、次のようなメッセージが出力されます。**この内容は控えておいてください。**

```
Personal access client created successfully.
Here is your new client secret. This is the only time it will be shown so don't lose it!

Client ID: 1
Client secret: xxxxxxxx
Password grant client created successfully.
Here is your new client secret. This is the only time it will be shown so don't lose it!

Client ID: 2
Client secret: yyyyyyyy
```

7. `.env` ファイルにPersonal access token発行用の設定を追加します。  
   直前の手順のメッセージから `Personal access client created successfully` の直後に出力されている ID と secret を `PASSPORT_PERSONAL_ACCESS_CLIENT_ID` と `PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET` に設定します。

```
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=1
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=xxxxxxxx
```

8. ファイルに書き込めるように権限を設定します。

```
docker compose exec web chown -R www-data /var/www/html/storage
```

9. アセットをビルドします。

```
docker compose exec web yarn dev
```


10. 最後に `.env` を読み込み直すために起動し直します。

```
docker compose up -d
```

これで準備は完了です。Tissue が動いていれば `http://localhost:4545/` でアクセスができます。

## デバッグ実行

```
docker compose -f docker compose.yml -f docker compose.debug.yml up -d
```

で起動することにより、DB のポート`5432`を開放してホストマシンから接続できるようになります。

## アセットのリアルタイムビルド
`yarn watch`を使うとソースファイルを監視して差分があると差分ビルドしてくれます。フロント開発時は活用しましょう。
```
docker compose run --rm web yarn watch
```

もしファイル変更時に更新されない場合は`yarn watch-poll`を試してみてください。  
現在Docker環境でのHMRはサポートしてません。Docker外ならおそらく動くでしょう。  
その他詳しくはlaravel-mixのドキュメントなどを当たってください。

## phpunit によるテスト

変更をしたらPull Requestを投げる前にテストが通ることを確認してください。  
テストは以下のコマンドで実行できます。

```
docker compose exec web composer test
```
