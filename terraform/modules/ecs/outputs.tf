output "cluster_id" {
  description = "ECSクラスターID"
  value       = aws_ecs_cluster.main.id
}

output "cluster_name" {
  description = "ECSクラスター名"
  value       = aws_ecs_cluster.main.name
}

output "api_service_name" {
  description = "API Service名"
  value       = aws_ecs_service.api.name
}

output "console_service_name" {
  description = "Console Service名"
  value       = aws_ecs_service.console.name
}

output "api_task_definition_arn" {
  description = "API Task Definition ARN"
  value       = aws_ecs_task_definition.api.arn
}

output "console_task_definition_arn" {
  description = "Console Task Definition ARN"
  value       = aws_ecs_task_definition.console.arn
}

