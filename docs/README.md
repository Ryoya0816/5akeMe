# データベースドキュメント生成ガイド

このプロジェクトでは、[tbls](https://github.com/k1LoW/tbls)を使用してデータベースのテーブル定義書とER図を自動生成します。

## tblsのインストール

### macOS (Homebrew)

```bash
brew install tbls
```

### その他のOS

[公式リリースページ](https://github.com/k1LoW/tbls/releases)から適切なバイナリをダウンロードしてインストールしてください。

## 使用方法

### 1. データベースの起動

まず、Docker Composeでデータベースを起動します：

```bash
docker compose up -d db
```

### 2. ドキュメントの生成

#### テーブル定義書とER図の両方を生成

```bash
make docs
```

または、直接tblsコマンドを実行：

```bash
tbls doc --config tbls.yml
tbls er --config tbls.yml
```

#### ER図のみ生成

```bash
make docs-er
```

または：

```bash
tbls er --config tbls.yml
```

#### テーブル定義書のみ生成

```bash
make docs-doc
```

または：

```bash
tbls doc --config tbls.yml
```

### 3. 生成されたファイルの確認

生成されたドキュメントは以下のディレクトリに保存されます：

- **テーブル定義書**: `docs/schema/` ディレクトリ内のMarkdownファイル
- **ER図**: `docs/schema/er.svg`

## 設定ファイル

設定は `tbls.yml` で管理されています。データベース接続情報を変更する場合は、このファイルを編集してください。

現在の設定：
- **データベース**: MySQL 8.0
- **ホスト**: 127.0.0.1:3306
- **データベース名**: laravel
- **ユーザー名**: laravel
- **パスワード**: laravel

## 注意事項

- データベースが起動していることを確認してからドキュメントを生成してください
- テーブルにコメントが設定されている場合、それらがドキュメントに反映されます
- ER図はSVG形式で生成されます
