# API 仕様書

CMS システムの API 仕様書です。

## 概要

このドキュメントでは、CMS システムで提供されるすべての API エンドポイントの詳細な仕様を説明します。

## API 一覧

### 管理画面 API

管理画面で使用する API エンドポイントです。認証が必要なエンドポイントが含まれます。

-   **[User API](admin/user.md)** - 認証・ユーザー管理
-   **[Contract API](admin/contract.md)** - 企業・施設管理
-   **[Content API](admin/content.md)** - コンテンツ管理
-   **[ContentModel API](admin/content_model.md)** - コンテンツモデル管理
-   **[ContentField API](admin/content_field.md)** - コンテンツフィールド管理
-   **[MediaLibrary API](admin/media_library.md)** - メディアライブラリ管理
-   **[ContactSetting API](admin/contact_setting.md)** - お問い合わせ設定管理
-   **[ActionLog API](admin/action_log.md)** - アクションログ

### フロントエンド API

公開サイトで使用する API エンドポイントです。認証は不要です。

-   **[Content API](front/content.md)** - 公開用コンテンツ・お問い合わせ

## 共通仕様

### 認証

管理画面 API の多くは認証が必要です。認証には Laravel Sanctum を使用しています。

### レスポンス形式

すべての API は以下の形式でレスポンスを返します：

```json
{
    "success": true,
    "timestamp": 1234567890,
    "payload": {
        // データ
    }
}
```

### エラーレスポンス

エラーが発生した場合、適切な HTTP ステータスコードとエラーメッセージが返されます。

### ページネーション

一覧取得 API では、以下の形式でページネーション情報が返されます：

```json
{
  "total": 100,
  "current": 1,
  "pages": 10,
  "limit": 10,
  "data": [...]
}
```
