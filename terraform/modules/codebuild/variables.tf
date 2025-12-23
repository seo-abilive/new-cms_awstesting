variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "api_build" {
  description = "API CodeBuild設定"
  type = object({
    name             = string
    repository_url   = string
    ecr_repository   = string
    build_context    = string
    dockerfile_path  = string
    service_role_arn = string
    github_token     = string
  })
}

variable "console_build" {
  description = "Console CodeBuild設定"
  type = object({
    name             = string
    repository_url   = string
    ecr_repository   = string
    build_context    = string
    dockerfile_path  = string
    service_role_arn = string
    github_token     = string
  })
}

variable "api_url" {
  description = "API URL（VITE_API_ORIGIN環境変数として使用、空文字の場合は設定しない）"
  type        = string
  default     = ""
}

variable "cluster_name" {
  description = "ECSクラスター名（マイグレーション用）"
  type        = string
  default     = ""
}

variable "api_task_definition_family" {
  description = "API Task Definition Family（マイグレーション用）"
  type        = string
  default     = ""
}

variable "subnet_ids" {
  description = "サブネットIDリスト（マイグレーション用）"
  type        = list(string)
  default     = []
}

variable "security_group_id" {
  description = "セキュリティグループID（マイグレーション用）"
  type        = string
  default     = ""
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

