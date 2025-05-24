# このファイルについて

Tissue開発者向けに、開発環境をバージョンアップする際に留意すべき事項をまとめたドキュメントです。

新規に開発環境を構築、または再構築を行う際には読む必要はありません。

## 読み方

最後に作業をした時期より後のトピックを、古いものから時系列順に読み進めてください。

## 書き方

- トピックは上が最新になるように並べてください。
- ヘッダーは `${年}-${月}: ${一言程度の概要}` の形式で書いてください。
- Issueがある場合は必ずリンクを含めてください。

# トピック

## 2025-05: テスト用データベースの追加

- Issue: https://github.com/shikorism/tissue/issues/734

上記IssueのPRマージ以降、開発用データベースが新規作成された際にテスト用データベースが別途作成されるようになりました。  
ただし、既存の環境については**自動で作成されません**。この状態で `composer test` を実行すると、データベース接続エラーになってしまいます。

下記の手順でテスト用データベースを作成することができます。

```sh
docker compose exec db psql -U tissue -c "CREATE DATABASE tissue_test"
```

## 2021-11: PostgreSQL 14へのアップグレード

- Issue: https://github.com/shikorism/tissue/issues/752

Docker Composeを使用して開発環境を構築している場合、下記の手順でデータベースをアップグレードしてください。

```sh
# 操作前にcompose.yamlのimageタグを10-alpineに変更
docker compose exec db pg_dump -Fc -U tissue tissue > tissue-pg10
docker compose down
docker volume rm tissue_db
# compose.yamlのimageタグを14-alpineに変更
docker compose up -d
docker compose exec db pg_restore -Fc -d tissue -U tissue < tissue-pg10
```

データを維持する必要がない場合、より簡単な下記の手順を使用することもできます。

```sh
docker-compose down
docker volume rm tissue_db
```
