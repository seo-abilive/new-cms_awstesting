output "domain_name" {
  description = "CloudFront 配信のドメイン名"
  value       = aws_cloudfront_distribution.main.domain_name
}

output "hosted_zone_id" {
  description = "CloudFront の Hosted Zone ID"
  value       = aws_cloudfront_distribution.main.hosted_zone_id
}

output "distribution_id" {
  description = "CloudFront 配信 ID"
  value       = aws_cloudfront_distribution.main.id
}

output "url" {
  description = "CloudFront URL（https）"
  value       = "https://${aws_cloudfront_distribution.main.domain_name}"
}

output "url_http" {
  description = "CloudFront URL（http）"
  value       = "http://${aws_cloudfront_distribution.main.domain_name}"
}
