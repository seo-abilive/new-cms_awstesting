variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "github_repository_url" {
  description = "GitHubリポジトリURL"
  type        = string
}

variable "github_branch" {
  description = "GitHubブランチ"
  type        = string
}

variable "github_token" {
  description = "GitHub Personal Access Token"
  type        = string
  sensitive   = true
}

variable "api_build_project_name" {
  description = "API CodeBuild Project名"
  type        = string
}

variable "console_build_project_name" {
  description = "Console CodeBuild Project名"
  type        = string
}

variable "service_role_arn" {
  description = "CodePipeline Service Role ARN"
  type        = string
}

variable "cluster_name" {
  description = "ECSクラスター名"
  type        = string
}

variable "api_service_name" {
  description = "API ECS Service名"
  type        = string
}

variable "console_service_name" {
  description = "Console ECS Service名"
  type        = string
}

variable "migration_build_project_name" {
  description = "Migration CodeBuild Project名"
  type        = string
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

