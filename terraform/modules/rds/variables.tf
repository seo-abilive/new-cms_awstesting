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

variable "deletion_protection" {
  description = "RDSクラスターの削除保護を有効にするかどうか"
  type        = bool
  default     = false
}

variable "skip_final_snapshot" {
  description = "RDSクラスター削除時に最終スナップショットを作成するかどうか（false: 作成しない、true: 作成する）"
  type        = bool
  default     = false
}

variable "preferred_maintenance_window" {
  description = "RDSクラスターのメンテナンスウィンドウ（例: mon:19:00-mon:20:00）"
  type        = string
  default     = "mon:19:00-mon:20:00" # デフォルト: 夜中 4:00-5:00 (JST)
}

variable "auto_minor_version_upgrade" {
  description = "自動マイナーバージョンアップグレードを有効にするかどうか（false: 無効化して選択的メンテナンスを最小化、true: 有効化）"
  type        = bool
  default     = false
}

variable "high_availability" {
  description = "高可用性を有効にするかどうか（true: 2インスタンス、false: 1インスタンス）"
  type        = bool
  default     = true
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

