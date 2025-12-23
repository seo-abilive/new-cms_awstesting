output "log_group_names" {
  description = "CloudWatch Log Group名のマップ"
  value       = { for k, v in aws_cloudwatch_log_group.log_groups : k => v.name }
}

output "log_group_arns" {
  description = "CloudWatch Log Group ARNのマップ"
  value       = { for k, v in aws_cloudwatch_log_group.log_groups : k => v.arn }
}

