output "alb_sg_id" {
  description = "ALBセキュリティグループID"
  value       = aws_security_group.alb.id
}

output "ecs_api_sg_id" {
  description = "ECS APIセキュリティグループID"
  value       = aws_security_group.ecs_api.id
}

output "ecs_console_sg_id" {
  description = "ECS ConsoleセキュリティグループID"
  value       = aws_security_group.ecs_console.id
}

output "rds_sg_id" {
  description = "RDSセキュリティグループID"
  value       = aws_security_group.rds.id
}

