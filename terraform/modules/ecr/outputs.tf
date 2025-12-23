output "repository_urls" {
  description = "ECRリポジトリURLのマップ"
  value       = { for k, v in aws_ecr_repository.repositories : k => v.repository_url }
}

output "repository_arns" {
  description = "ECRリポジトリARNのマップ"
  value       = { for k, v in aws_ecr_repository.repositories : k => v.arn }
}

output "repository_names" {
  description = "ECRリポジトリ名のマップ"
  value       = { for k, v in aws_ecr_repository.repositories : k => v.name }
}

