variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "vpc_id" {
  description = "VPC ID"
  type        = string
}

variable "subnet_ids" {
  description = "DBサブネットIDのリスト"
  type        = list(string)
}

variable "db_name" {
  description = "データベース名"
  type        = string
}

variable "db_username" {
  description = "データベースユーザー名"
  type        = string
}

variable "db_password" {
  description = "データベースパスワード"
  type        = string
  sensitive   = true
}

variable "security_group_ids" {
  description = "セキュリティグループIDのリスト"
  type        = list(string)
}

variable "enable_backup" {
  description = "バックアップを有効にするかどうか"
  type        = bool
  default     = true
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

