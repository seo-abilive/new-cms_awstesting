# Front Content API 仕様書

## 概要

フロントエンド向けコンテンツ API エンドポイントです。公開用のコンテンツ一覧取得、詳細取得、カテゴリ一覧取得、マークアップ取得などの機能を提供します。

---

## コンテンツ API

### 1. コンテンツ一覧取得

#### エンドポイント

```
GET /api/v1/{model_name}
```

#### HTTP メソッド

`GET`

#### 認証

不要

#### リクエストパラメータ

##### Path Parameters

| パラメータ名 | 型     | 必須 | 説明                   |
| ------------ | ------ | ---- | ---------------------- |
| model_name   | string | 必須 | コンテンツモデル名（エイリアス） |

##### Query Parameters

| パラメータ名 | 型      | 必須 | 説明                                                          |
| ------------ | ------- | ---- | ------------------------------------------------------------- |
| mode         | string  | 任意 | 取得モード（list: ページネーション、all: 全件取得、デフォルト: list） |
| current      | integer | 任意 | 現在のページ番号（mode=list の場合、デフォルト: 1）            |
| limit        | integer | 任意 | 1 ページあたりの件数（mode=list の場合、デフォルト: config 値） |
| criteria     | array   | 任意 | 検索条件                                                      |

#### レスポンス構造

##### 成功時 (200) - mode=list

```json
{
    "success": true,
    "timestamp": 1234567890,
    "all": 100,
    "current": 1,
    "limit": 10,
    "pages": 10,
    "contents": [
        {
            "id": 1,
            "model_id": 1,
            "values": [...],
            "categories": [...],
            ...
        }
    ]
}
```

##### 成功時 (200) - mode=all

```json
{
    "success": true,
    "timestamp": 1234567890,
    "contents": [
        {
            "id": 1,
            "model_id": 1,
            "values": [...],
            "categories": [...],
            ...
        }
    ]
}
```

#### 使用しているモデル・サービス

-   **モデル**: `App\Mod\Content\Domain\Models\Content`
-   **サービス**: `App\Mod\Content\Domain\FrontContentService`
-   **Action**: `App\Mod\Content\Actions\Front\V1\ListAction`

#### 処理の流れ

```plantuml
@startuml
participant Client
participant ListAction
participant AbstractFrontApiAction
participant FrontContentService
participant "Content Model"

Client -> ListAction: GET /api/v1/{model_name}
activate ListAction
ListAction -> AbstractFrontApiAction: __invoke()
activate AbstractFrontApiAction
AbstractFrontApiAction -> AbstractFrontApiAction: フロント用スコープ設定
AbstractFrontApiAction -> ListAction: callback()
deactivate AbstractFrontApiAction
ListAction -> FrontContentService: findList or findAll
activate FrontContentService
alt mode == 'all'
  FrontContentService -> "Content Model" : findAll()
  "Content Model" --> FrontContentService: all contents
else
  FrontContentService -> "Content Model" : findList()
  "Content Model" --> FrontContentService: paginated contents
end
FrontContentService --> ListAction: contents
deactivate FrontContentService
ListAction --> Client: JSON Response
deactivate ListAction
@enduml
```

#### 想定されるエラーケース

-   **404 Not Found**: コンテンツモデルが見つからない

---

### 2. コンテンツ詳細取得

#### エンドポイント

```
GET /api/v1/{model_name}/{id}
```

#### HTTP メソッド

`GET`

#### 認証

不要

#### リクエストパラメータ

##### Path Parameters

| パラメータ名 | 型      | 必須 | 説明                   |
| ------------ | ------- | ---- | ---------------------- |
| model_name   | string  | 必須 | コンテンツモデル名（エイリアス） |
| id           | integer | 必須 | コンテンツ ID          |

#### レスポンス構造

##### 成功時 (200)

```json
{
    "success": true,
    "timestamp": 1234567890,
    "contents": {
        "id": 1,
        "model_id": 1,
        "values": [
            {
                "field_id": "title",
                "value": "タイトル",
                "field": {...}
            }
        ],
        "categories": [
            {
                "id": 1,
                "title": "カテゴリ名"
            }
        ],
        ...
    },
    "sibLings": {
        "previous": {
            "id": 0,
            "title": "前の記事"
        },
        "next": {
            "id": 2,
            "title": "次の記事"
        }
    }
}
```

#### 使用しているモデル・サービス

-   **モデル**: `App\Mod\Content\Domain\Models\Content`
-   **サービス**: `App\Mod\Content\Domain\FrontContentService`
-   **Action**: `App\Mod\Content\Actions\Front\V1\DetailAction`

#### 処理の流れ

```plantuml
@startuml
participant Client
participant DetailAction
participant AbstractFrontApiAction
participant FrontContentService
participant "Content Model"

Client -> DetailAction: GET /api/v1/{model_name}/{id}
activate DetailAction
DetailAction -> AbstractFrontApiAction: __invoke()
activate AbstractFrontApiAction
AbstractFrontApiAction -> AbstractFrontApiAction: フロント用スコープ設定
AbstractFrontApiAction -> DetailAction: callback()
deactivate AbstractFrontApiAction
DetailAction -> FrontContentService: findDetail(request, id, ['categories', 'values.field.parentField'])
activate FrontContentService
FrontContentService -> "Content Model" : findOrFail(id)
"Content Model" --> FrontContentService: content
FrontContentService --> DetailAction: content
DetailAction -> FrontContentService: findPreviousAndNext(request, id)
FrontContentService -> "Content Model" : 前後の記事取得
"Content Model" --> FrontContentService: siblings
FrontContentService --> DetailAction: siblings
DetailAction --> Client: JSON Response
deactivate FrontContentService
deactivate DetailAction
@enduml
```

#### 想定されるエラーケース

-   **404 Not Found**: コンテンツが見つからない

---

### 3. カテゴリ一覧取得

#### エンドポイント

```
GET /api/v1/{model_name}/categories
```

#### HTTP メソッド

`GET`

#### 認証

不要

#### リクエストパラメータ

##### Path Parameters

| パラメータ名 | 型     | 必須 | 説明                   |
| ------------ | ------ | ---- | ---------------------- |
| model_name   | string | 必須 | コンテンツモデル名（エイリアス） |

##### Query Parameters

| パラメータ名 | 型    | 必須 | 説明     |
| ------------ | ----- | ---- | -------- |
| criteria     | array | 任意 | 検索条件 |

#### レスポンス構造

##### 成功時 (200)

```json
{
    "success": true,
    "timestamp": 1234567890,
    "payload": {
        "data": [
            {
                "id": 1,
                "title": "カテゴリ名",
                "model_id": 1,
                ...
            }
        ]
    }
}
```

#### 使用しているモデル・サービス

-   **モデル**: `App\Mod\Content\Domain\Models\ContentCategory`
-   **サービス**: `App\Mod\Content\Domain\FrontContentService`
-   **Action**: `App\Mod\Content\Actions\Front\V1\Categories\ListAction`

#### 処理の流れ

```plantuml
@startuml
participant Client
participant ListAction
participant AbstractFrontApiAction
participant FrontContentService
participant "ContentCategory Model"

Client -> ListAction: GET /api/v1/{model_name}/categories
activate ListAction
ListAction -> AbstractFrontApiAction: __invoke()
activate AbstractFrontApiAction
AbstractFrontApiAction -> AbstractFrontApiAction: フロント用スコープ設定
AbstractFrontApiAction -> ListAction: callback()
deactivate AbstractFrontApiAction
ListAction -> FrontContentService: findCategories(request)
activate FrontContentService
FrontContentService -> "ContentCategory Model" : where(criteria + scope)
"ContentCategory Model" --> FrontContentService: categories
FrontContentService --> ListAction: { data }
deactivate FrontContentService
ListAction --> Client: JSON Response
deactivate ListAction
@enduml
```

#### 想定されるエラーケース

-   **404 Not Found**: コンテンツモデルが見つからない

---

### 4. マークアップ一覧取得

#### エンドポイント

```
GET /api/v1/{model_name}/markup
```

#### HTTP メソッド

`GET`

#### 認証

不要

#### リクエストパラメータ

##### Path Parameters

| パラメータ名 | 型     | 必須 | 説明                   |
| ------------ | ------ | ---- | ---------------------- |
| model_name   | string | 必須 | コンテンツモデル名（エイリアス） |

##### Query Parameters

| パラメータ名 | 型      | 必須 | 説明                                          |
| ------------ | ------- | ---- | --------------------------------------------- |
| current      | integer | 任意 | 現在のページ番号（デフォルト: 1）             |
| limit        | integer | 任意 | 1 ページあたりの件数（デフォルト: config 値） |
| criteria     | array   | 任意 | 検索条件                                      |

#### レスポンス構造

##### 成功時 (200)

```json
{
    "success": true,
    "timestamp": 1234567890,
    "payload": {
        "total": 10,
        "current": 1,
        "pages": 1,
        "limit": 10,
        "data": [
            {
                "id": 1,
                "title": "マークアップ名",
                "content_model_id": 1,
                "markup": "...",
                ...
            }
        ]
    }
}
```

#### 使用しているモデル・サービス

-   **モデル**: `App\Mod\ContentModel\Domain\Models\ContentModelMarkup`
-   **サービス**: `App\Mod\ContentModel\Domain\ContentModelMarkupService`
-   **Action**: `App\Mod\Content\Actions\Front\V1\Markup\ListAction`

#### 処理の流れ

コンテンツ一覧取得と同じ

#### 想定されるエラーケース

-   **404 Not Found**: コンテンツモデルが見つからない

---

### 5. マークアップ詳細取得

#### エンドポイント

```
GET /api/v1/{model_name}/markup/{id}
```

#### HTTP メソッド

`GET`

#### 認証

不要

#### リクエストパラメータ

##### Path Parameters

| パラメータ名 | 型      | 必須 | 説明                   |
| ------------ | ------- | ---- | ---------------------- |
| model_name   | string  | 必須 | コンテンツモデル名（エイリアス） |
| id           | integer | 必須 | マークアップ ID        |

#### レスポンス構造

##### 成功時 (200)

```json
{
    "success": true,
    "timestamp": 1234567890,
    "payload": {
        "data": {
            "id": 1,
            "title": "マークアップ名",
            "content_model_id": 1,
            "markup": "...",
            ...
        }
    }
}
```

#### 使用しているモデル・サービス

-   **モデル**: `App\Mod\ContentModel\Domain\Models\ContentModelMarkup`
-   **サービス**: `App\Mod\ContentModel\Domain\ContentModelMarkupService`
-   **Action**: `App\Mod\Content\Actions\Front\V1\Markup\DetailAction`

#### 処理の流れ

コンテンツ詳細取得と同じ（前後の記事なし）

#### 想定されるエラーケース

-   **404 Not Found**: マークアップが見つからない

---

## お問い合わせ API

### 6. お問い合わせ設定取得

#### エンドポイント

```
GET /api/v1/contact/{token}
```

#### HTTP メソッド

`GET`

#### 認証

不要

#### リクエストパラメータ

##### Path Parameters

| パラメータ名 | 型     | 必須 | 説明           |
| ------------ | ------ | ---- | -------------- |
| token        | string | 必須 | お問い合わせ設定トークン |

#### レスポンス構造

##### 成功時 (200)

```json
{
    "success": true,
    "timestamp": 1234567890,
    "payload": {
        "data": {
            "id": 1,
            "title": "お問い合わせ設定名",
            "token": "abc123",
            "is_recaptcha": true,
            "recaptcha_site_key": "...",
            ...
        }
    }
}
```

#### 使用しているモデル・サービス

-   **モデル**: `App\Mod\ContactSetting\Domain\Models\ContactSetting`
-   **サービス**: `App\Mod\ContactSetting\Domain\ContactSettingService`
-   **Action**: `App\Mod\ContactSetting\Actions\Front\DetailAction`

#### 処理の流れ

```plantuml
@startuml
participant Client
participant DetailAction
participant AbstractFrontApiAction
participant ContactSettingService
participant "ContactSetting Model"

Client -> DetailAction: GET /api/v1/contact/{token}
activate DetailAction
DetailAction -> AbstractFrontApiAction: __invoke()
activate AbstractFrontApiAction
AbstractFrontApiAction -> AbstractFrontApiAction: フロント用スコープ設定
AbstractFrontApiAction -> DetailAction: callback()
deactivate AbstractFrontApiAction
DetailAction -> ContactSettingService: findDetailByToken(request, token)
activate ContactSettingService
ContactSettingService -> "ContactSetting Model" : where('token', token)
"ContactSetting Model" --> ContactSettingService: contactSetting
ContactSettingService --> DetailAction: contactSetting
deactivate ContactSettingService
DetailAction --> Client: JSON Response
deactivate DetailAction
@enduml
```

#### 想定されるエラーケース

-   **404 Not Found**: お問い合わせ設定が見つからない

---

### 7. お問い合わせ送信

#### エンドポイント

```
POST /api/v1/contact/{token}
```

#### HTTP メソッド

`POST`

#### 認証

不要

#### リクエストパラメータ

##### Path Parameters

| パラメータ名 | 型     | 必須 | 説明           |
| ------------ | ------ | ---- | -------------- |
| token        | string | 必須 | お問い合わせ設定トークン |

##### Body Parameters

| パラメータ名 | 型     | 必須 | 説明                                 |
| ------------ | ------ | ---- | ------------------------------------ |
| {field_name} | mixed  | 条件付き必須 | お問い合わせフィールド値（設定に基づく） |
| recaptcha_token | string | 条件付き必須 | reCAPTCHA トークン（reCAPTCHA 有効時） |

**注意**: リクエストパラメータはお問い合わせ設定のフィールド定義に基づいて動的に決まります。

#### バリデーション

-   お問い合わせ設定のフィールド定義に基づく動的バリデーション
-   reCAPTCHA トークンの検証（reCAPTCHA 有効時）

#### レスポンス構造

##### 成功時 (200)

```json
{
    "success": true,
    "timestamp": 1234567890,
    "payload": {
        "message": "お問い合わせを受け付けました。"
    }
}
```

#### 使用しているモデル・サービス

-   **モデル**: `App\Mod\ContactSetting\Domain\Models\ContactSetting`
-   **サービス**: `App\Mod\ContactSetting\Domain\ContactSettingService`
-   **Action**: `App\Mod\ContactSetting\Actions\Front\StoreAction`

#### 処理の流れ

```plantuml
@startuml
participant Client
participant StoreAction
participant AbstractFrontApiAction
participant ContactSettingService
participant "ContactSetting Model"
participant Mail
participant reCAPTCHA

Client -> StoreAction: POST /api/v1/contact/{token}
activate StoreAction
StoreAction -> AbstractFrontApiAction: __invoke()
activate AbstractFrontApiAction
AbstractFrontApiAction -> AbstractFrontApiAction: フロント用スコープ設定
AbstractFrontApiAction -> StoreAction: callback()
deactivate AbstractFrontApiAction
StoreAction -> ContactSettingService: storeSendMail(request, token)
activate ContactSettingService
ContactSettingService -> "ContactSetting Model" : where('token', token)
"ContactSetting Model" --> ContactSettingService: contactSetting
alt reCAPTCHA有効
  ContactSettingService -> reCAPTCHA: トークン検証
  reCAPTCHA --> ContactSettingService: 検証結果
end
ContactSettingService -> ContactSettingService: バリデーション
ContactSettingService -> Mail: メール送信
Mail --> ContactSettingService: 送信結果
alt 返信機能有効
  ContactSettingService -> Mail: 返信メール送信
end
ContactSettingService --> StoreAction: { message }
deactivate ContactSettingService
StoreAction --> Client: JSON Response
deactivate StoreAction
@enduml
```

#### 想定されるエラーケース

-   **400 Bad Request**: バリデーションエラー
    -   必須フィールドが未入力
    -   reCAPTCHA トークンが無効
-   **404 Not Found**: お問い合わせ設定が見つからない
-   **500 Internal Server Error**: その他のエラー
    -   メール送信失敗

