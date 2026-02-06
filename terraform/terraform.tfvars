# name
aws_prefix = "new-cms"

# AWS設定
aws_profile = "seo-jg" # seo-jg SSO 프로필 사용
aws_region  = "ap-northeast-1"

# RDS設定
db_password                = "YourSecurePassword123!"
enable_backup              = false # バックアップ：開発中はfalseで時間短縮、本番環境ではtrueに設定(false 1日、 true 7日) 夜中 2:00-3:00 (JST)
deletion_protection        = false # 誤削除防止:開発中はfalseで時間短縮、本番環境ではtrueに設定
skip_final_snapshot        = false # スナップショット作成:開発中はfalseで削除時にスナップショット作成しない、本番環境ではtrueで作成する
auto_minor_version_upgrade = false # 自動マイナーバージョンアップグレード: falseで無効化（選択的メンテナンスを最小化）、trueで有効化

# 高可用性設定
high_availability = false # 開発中はfalseで、単一インスタンス、本番環境では高可用性（Aurora Serverless v2 + ECS Auto Scaling）

# Aurora Serverless v2設定（high_availability=trueの場合のみ有効）
serverless_min_capacity = 0.5 # 最小容量（ACU単位、0.5〜128）
serverless_max_capacity = 16  # 最大容量（ACU単位、0.5〜128）

# ECS Auto Scaling設定（high_availability=trueの場合のみ有効）
ecs_autoscaling_min_capacity = 1  # 最小タスク数
ecs_autoscaling_max_capacity = 10 # 最大タスク数

# アプリケーション設定
app_key       = "base64:izUY16xZiwXcLFItN0g8iKG9T+YDG93QNCsYZ9Auzoo="
mail_password = "W^}5GLWEcQsT"

# API/Console設定（空文字の場合は CloudFront URL を使用＝ブラウザは CloudFront 経由で API にアクセス）
api_url = "" # Private ALB + CloudFront 構成のため、ブラウザからは CloudFront 経由のみ。空で effective_api_url = CloudFront/api/
# console_allowed_hosts = ""  # 空文字の場合はALB DNS名が自動的に使用されます

# GitHub設定
github_repository_url = "https://github.com/seo-abilive/new-cms_awstesting.git"
github_branch         = "main"

# ACM証明書ARN（オプション、HTTPSを使用する場合）
certificate_arn = "" # 証明書がある場合はARNを設定してください

