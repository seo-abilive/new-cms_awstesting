# 名前接頭辞（すべてのリソース名に付与）
# 空文字の場合はaws_prefixとgithub_branchから自動生成（例: "new-cms-aws-testing2-"）
variable "name_prefix" {
  description = "リソース名の接頭辞（空文字の場合はaws_prefixとgithub_branchから自動生成）"
  type        = string
  default     = ""
}

variable "aws_prefix" {
  description = "AWSリソース名のプレフィックス（name_prefixが空文字の場合に使用）"
  type        = string
  default     = "new-cms"
}

# AWS設定
variable "aws_profile" {
  description = "AWSプロファイル名"
  type        = string
  default     = ""
}

variable "aws_region" {
  description = "AWSリージョン"
  type        = string
  default     = "ap-northeast-1"
}

# VPC設定
variable "vpc_cidr" {
  description = "VPC CIDRブロック"
  type        = string
  default     = "10.0.0.0/16"
}

variable "public_subnet_cidrs" {
  description = "パブリックサブネットのCIDRブロック"
  type        = list(string)
  default     = ["10.0.0.0/24", "10.0.1.0/24"]
}

variable "private_subnet_cidrs" {
  description = "プライベートサブネットのCIDRブロック"
  type        = list(string)
  default     = ["10.0.2.0/24", "10.0.3.0/24"]
}

variable "availability_zones" {
  description = "利用可能なアベイラビリティゾーン"
  type        = list(string)
  default     = ["ap-northeast-1a", "ap-northeast-1c"]
}

# RDS設定
variable "db_name" {
  description = "データベース名"
  type        = string
  default     = "abicms_db"
}

variable "db_username" {
  description = "データベースユーザー名"
  type        = string
  default     = "admin"
}

variable "db_password" {
  description = "データベースパスワード"
  type        = string
  sensitive   = true
}

variable "enable_backup" {
  description = "データベースバックアップを有効にするかどうか（開発中はfalseで時間短縮）"
  type        = bool
  default     = false
}

# アプリケーション設定
variable "app_key" {
  description = "Laravel APP_KEY"
  type        = string
  sensitive   = true
}

variable "mail_password" {
  description = "メールパスワード"
  type        = string
  sensitive   = true
  default     = ""
}

variable "api_url" {
  description = "API URL（ALB DNS名を使用する場合は空文字、後で設定可能）"
  type        = string
  default     = ""
}

variable "console_allowed_hosts" {
  description = "Console許可ホスト（ALB DNS名を使用する場合は空文字、後で設定可能）"
  type        = string
  default     = ""
}

variable "frontend_url" {
  description = "フロントエンドURL（パスワードリセットなどで使用、空文字の場合はapi_urlから自動生成）"
  type        = string
  default     = ""
}

variable "gemini_api_endpoint" {
  description = "Gemini APIエンドポイント"
  type        = string
  default     = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent"
}

variable "login_max_attempts" {
  description = "ログイン最大試行回数"
  type        = string
  default     = "5"
}

variable "login_lockout_duration" {
  description = "ログインロックアウト時間（分）"
  type        = string
  default     = "15"
}

# ACM証明書ARN（オプション）
variable "certificate_arn" {
  description = "ACM証明書ARN（HTTPS用、オプション）"
  type        = string
  default     = ""
}

# GitHub設定
variable "github_repository_url" {
  description = "GitHubリポジトリURL"
  type        = string
}

variable "github_branch" {
  description = "GitHubブランチ"
  type        = string
  default     = "main"
}

variable "github_token" {
  description = "GitHub Personal Access Token"
  type        = string
  sensitive   = true
}

# タグ
variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default = {
    Project     = "new-cms"
    Environment = "testing"
    ManagedBy   = "Terraform"
  }
}

