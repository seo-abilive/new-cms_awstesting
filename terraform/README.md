# Terraform AWS ECS 배포

`api/`와 `console/`을 AWS ECS Fargate에 배포하기 위한 Terraform 구성입니다.

## アーキテクチャ

- **VPC**: 10.0.0.0/16
- **Public Subnets**: 10.0.0.0/24, 10.0.1.0/24 (2つのAZ)
- **Private Subnets**: 10.0.2.0/24, 10.0.3.0/24 (RDS用)
- **ALB**: パスベースルーティング
  - `/api/*` → API Target Group
  - `/*` → Console Target Group
- **ECS Fargate**: API (ポート80) と Console (ポート5173)
- **RDS MySQL**: プライベートサブネットに配置
- **CI/CD**: GitHub → CodeBuild → ECR → ECS

## セットアップ

### 1. 変数ファイルの作成

`terraform.tfvars`ファイルを作成し、以下の変数を設定してください：

```hcl
aws_region = "ap-northeast-1"

db_password = "your-db-password"
app_key     = "your-laravel-app-key"

api_url              = "https://abitestxsrv.xbiz.jp/api"
console_allowed_hosts = "abitestxsrv.xbiz.jp"

github_repository_url = "https://github.com/your-org/new-cms"
github_branch         = "main"
github_token          = "your-github-token"

certificate_arn = "" # オプション: ACM証明書ARN（HTTPS用）
```

### 2. Terraformの初期化と適用

```bash
cd terraform/
terraform init
terraform plan
terraform apply
```

### 3. 出力値の確認

```bash
terraform output
```

重要な出力値：
- `alb_dns_name`: ALB DNS名（ブラウザでアクセス可能）
- `api_endpoint_url`: APIエンドポイントURL
- `rds_endpoint`: RDSエンドポイント

## リソース名

すべてのリソース名には `new-cms-aws-testing-` 接頭辞が付与されます。

例：
- VPC: `new-cms-aws-testing-vpc`
- ECSクラスター: `new-cms-aws-testing-cluster`
- ALB: `new-cms-aws-testing-alb`
- ECRリポジトリ: `new-cms-aws-testing-api`, `new-cms-aws-testing-console`

## パス構造

- **API**: `https://abitestxsrv.xbiz.jp/api/api/v1/`, `https://abitestxsrv.xbiz.jp/api/api/admin/`
- **Console**: `https://abitestxsrv.xbiz.jp/login/` など

## CI/CDパイプライン

GitHubにpushすると、自動的に以下が実行されます：

1. CodePipelineが変更を検出
2. CodeBuildがDockerイメージをビルド
3. ECRにイメージをプッシュ
4. ECS Serviceが自動的に新しいイメージをデプロイ

## 注意事項

1. **Consoleビルド**: CodeBuildで `npm run build` が実行され、ビルドされた `dist/` フォルダがDockerイメージに含まれます。

2. **APIマイグレーション**: Task起動時に自動実行されます。

3. **ACM証明書**: Route53は使用しません。ALB DNS名で直接アクセス可能です。HTTPSを使用する場合は、ACM証明書を手動で設定し、`certificate_arn` 変数に設定してください。

4. **コスト**: Fargate + RDS + ALB の使用量ベース課金です。

## モジュール構造

```
terraform/
├── main.tf                 # Provider設定、モジュール呼び出し
├── variables.tf           # 変数定義
├── outputs.tf             # 出力値定義
├── modules/
│   ├── vpc/               # VPC、サブネット、IGW、ルートテーブル
│   ├── security_groups/   # セキュリティグループ
│   ├── rds/               # RDS MySQL
│   ├── ecr/               # ECRリポジトリ
│   ├── ecs/               # ECSクラスター、Task Definitions、Services
│   ├── alb/               # ALB、Target Groups、Listeners
│   ├── iam/               # IAMロールとポリシー
│   ├── codebuild/         # CodeBuildプロジェクト
│   ├── codepipeline/      # CodePipeline
│   └── cloudwatch/        # CloudWatch Log Groups
└── README.md
```

## トラブルシューティング

### ECSタスクが起動しない

- CloudWatch Logsを確認してください
- セキュリティグループの設定を確認してください
- Task Definitionの環境変数を確認してください

### ALBからアクセスできない

- Target Groupのヘルスチェックを確認してください
- セキュリティグループでALBからECSへのトラフィックが許可されているか確認してください

### CodeBuildが失敗する

- GitHubトークンが有効か確認してください
- ECRへのアクセス権限を確認してください
- ビルドログをCloudWatch Logsで確認してください

