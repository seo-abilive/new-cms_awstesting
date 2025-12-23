variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "ecr_repository_arns" {
  description = "ECRリポジトリARNのリスト"
  type        = list(string)
  default     = []
}

variable "cloudwatch_log_group_arns" {
  description = "CloudWatch Log Group ARNのリスト"
  type        = list(string)
  default     = []
}

variable "codestar_connection_arn" {
  description = "CodeStar Connection ARN（GitHub接続用、空文字の場合は全リソースに許可）"
  type        = string
  default     = ""
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

