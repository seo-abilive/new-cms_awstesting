# API ドキュメント

このディレクトリには、CMS システムの API 仕様書が含まれています。

## MkDocs でビューアーを起動する方法

### 1. 必要なパッケージをインストール

```bash
cd api/docs
pip install -r requirements.txt
```

### 2. 開発サーバーを起動

```bash
mkdocs serve
```

ブラウザで `http://127.0.0.1:8000` にアクセスすると、ドキュメントを確認できます。

### 3. 静的サイトをビルド

```bash
mkdocs build
```

`site/` ディレクトリに静的サイトが生成されます。

### 4. GitHub Pages にデプロイ

```bash
mkdocs gh-deploy
```

## PlantUML シーケンス図について

ドキュメント内の PlantUML シーケンス図を表示するには、以下のいずれかの方法を使用できます：

1. **VS Code 拡張機能**: "PlantUML" 拡張機能をインストール
2. **オンラインビューアー**: [PlantUML Online Server](http://www.plantuml.com/plantuml/uml/) を使用
3. **ローカルサーバー**: PlantUML サーバーをローカルで起動

MkDocs で PlantUML を直接表示するには、追加の設定が必要です。

