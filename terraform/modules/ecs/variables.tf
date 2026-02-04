variable "name_prefix" {
  description = "リソース名の接頭辞"
  type        = string
}

variable "cluster_name" {
  description = "ECSクラスター名"
  type        = string
}

variable "api_task_definition" {
  description = "API Task Definition設定"
  type = object({
    family                   = string
    container_name           = string
    image                    = string
    container_port           = number
    log_group_name           = string
    execution_role_arn       = string
    task_role_arn            = string
    cpu                      = number
    memory                   = number
    environment_variables    = map(string)
    secrets                  = optional(list(object({
      name      = string
      valueFrom = string
    })), [])
  })
}

variable "console_task_definition" {
  description = "Console Task Definition設定"
  type = object({
    family                   = string
    container_name           = string
    image                    = string
    container_port           = number
    log_group_name           = string
    execution_role_arn       = string
    task_role_arn            = string
    cpu                      = number
    memory                   = number
    environment_variables    = map(string)
  })
}

variable "api_service" {
  description = "API Service設定"
  type = object({
    name            = string
    desired_count   = number
    security_groups = list(string)
    subnets         = list(string)
  })
}

variable "console_service" {
  description = "Console Service設定"
  type = object({
    name            = string
    desired_count   = number
    security_groups = list(string)
    subnets         = list(string)
  })
}

variable "api_target_group_arn" {
  description = "API Target Group ARN"
  type        = string
}

variable "console_target_group_arn" {
  description = "Console Target Group ARN"
  type        = string
}

variable "enable_autoscaling" {
  description = "Auto Scalingを有効にするかどうか"
  type        = bool
  default     = false
}

variable "autoscaling_min_capacity" {
  description = "Auto Scalingの最小タスク数"
  type        = number
  default     = 1
}

variable "autoscaling_max_capacity" {
  description = "Auto Scalingの最大タスク数"
  type        = number
  default     = 10
}

variable "tags" {
  description = "リソースに付与するタグ"
  type        = map(string)
  default     = {}
}

