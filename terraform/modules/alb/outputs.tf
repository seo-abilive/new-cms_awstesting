output "dns_name" {
  description = "ALB DNS名"
  value       = aws_lb.main.dns_name
}

output "arn" {
  description = "ALB ARN"
  value       = aws_lb.main.arn
}

output "api_target_group_arn" {
  description = "API Target Group ARN"
  value       = aws_lb_target_group.api.arn
}

output "console_target_group_arn" {
  description = "Console Target Group ARN"
  value       = aws_lb_target_group.console.arn
}

output "zone_id" {
  description = "ALB Zone ID"
  value       = aws_lb.main.zone_id
}

