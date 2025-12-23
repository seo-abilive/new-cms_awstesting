variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "vpc_id" {
  description = "VPC ID"
  type        = string
}

variable "subnets" {
  description = "ALBを配置するサブネットIDのリスト"
  type        = list(string)
}

variable "security_group_id" {
  description = "ALB用セキュリティグループID"
  type        = string
}

variable "api_target_group" {
  description = "API Target Group設定"
  type = object({
    name     = string
    port     = number
    protocol = string
    vpc_id   = string
  })
}

variable "console_target_group" {
  description = "Console Target Group設定"
  type = object({
    name     = string
    port     = number
    protocol = string
    vpc_id   = string
  })
}

variable "api_service_name" {
  description = "API ECS Service名"
  type        = string
}

variable "console_service_name" {
  description = "Console ECS Service名"
  type        = string
}

variable "cluster_name" {
  description = "ECSクラスター名"
  type        = string
}

variable "certificate_arn" {
  description = "ACM証明書ARN（オプション）"
  type        = string
  default     = ""
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

