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
high_availability = false # 開発中はfalseで、単一インスタンス、本番環境では高可用性（RDS 2インスタンス、ECS 2タスク）

# アプリケーション設定
app_key       = "base64:izUY16xZiwXcLFItN0g8iKG9T+YDG93QNCsYZ9Auzoo="
mail_password = "W^}5GLWEcQsT"

# API/Console設定（ALB DNS名を使用する場合は空文字のまま、後で設定可能）
api_url = "http://new-cms-main-alb-1834578746.ap-northeast-1.elb.amazonaws.com/api/" # プロトコルを含めて設定（HTTP使用、/apiを含める）
# console_allowed_hosts = ""  # 空文字の場合はALB DNS名が自動的に使用されます

# GitHub設定
github_repository_url = "https://github.com/seo-abilive/new-cms_awstesting.git"
github_branch         = "main"

# ACM証明書ARN（オプション、HTTPSを使用する場合）
certificate_arn = "" # 証明書がある場合はARNを設定してください

