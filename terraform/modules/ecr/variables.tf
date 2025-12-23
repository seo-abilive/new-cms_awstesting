variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "repository_names" {
  description = "ECRリポジトリ名のリスト"
  type        = list(string)
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

