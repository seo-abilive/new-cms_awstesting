variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "alb_arn" {
  description = "VPC Origin とする ALB の ARN"
  type        = string
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}
