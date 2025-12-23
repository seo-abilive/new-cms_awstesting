# APIエンドポイントURL（demo/で使用）
output "api_endpoint_url" {
  description = "APIエンドポイントURL（demo/で使用）"
  value       = var.api_url != "" ? "${var.api_url}/api/v1/" : "http://${module.alb.dns_name}/api/api/v1/"
}

# ALB DNS名
output "alb_dns_name" {
  description = "ALB DNS名（ブラウザでアクセス可能）"
  value       = module.alb.dns_name
}

# RDSエンドポイント
output "rds_endpoint" {
  description = "RDSエンドポイント"
  value       = module.rds.endpoint
}

# RDSセキュリティグループID
output "rds_security_group_id" {
  description = "RDSセキュリティグループID（demo EC2に接続時に使用）"
  value       = module.security_groups.rds_sg_id
}

# VPC ID
output "vpc_id" {
  description = "VPC ID（demo EC2配置時に使用）"
  value       = module.vpc.vpc_id
}

# パブリックサブネットID
output "public_subnet_ids" {
  description = "パブリックサブネットID（demo EC2配置時に使用）"
  value       = module.vpc.public_subnet_ids
}

# ECRリポジトリURL
output "ecr_api_repository_url" {
  description = "ECR APIリポジトリURL"
  value       = module.ecr.repository_urls["${local.name_prefix}api"]
}

output "ecr_console_repository_url" {
  description = "ECR ConsoleリポジトリURL"
  value       = module.ecr.repository_urls["${local.name_prefix}console"]
}

