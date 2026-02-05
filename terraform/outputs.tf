# APIエンドポイントURL（demo/で使用、CloudFront 経由）
output "api_endpoint_url" {
  description = "APIエンドポイントURL（CloudFront 経由）"
  value       = "${local.effective_api_url}v1/"
}

# CloudFront URL（ブラウザでアクセスする際はこちらを使用）
output "cloudfront_url" {
  description = "CloudFront 配信 URL（アプリの入口）"
  value       = module.cloudfront.url_http
}

output "cloudfront_domain_name" {
  description = "CloudFront ドメイン名"
  value       = module.cloudfront.domain_name
}

# ALB DNS名（Private のため直接アクセス不可、CloudFront 経由のみ）
output "alb_dns_name" {
  description = "ALB DNS名（参考・Private のため直接アクセス不可）"
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

