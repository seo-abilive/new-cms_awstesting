variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "log_group_names" {
  description = "CloudWatch Log Group名のリスト"
  type        = list(string)
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

