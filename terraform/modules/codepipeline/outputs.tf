output "pipeline_name" {
  description = "CodePipeline名"
  value       = aws_codepipeline.main.name
}

output "pipeline_arn" {
  description = "CodePipeline ARN"
  value       = aws_codepipeline.main.arn
}

output "artifacts_bucket_name" {
  description = "アーティファクトS3バケット名"
  value       = aws_s3_bucket.artifacts.bucket
}

output "artifacts_bucket_arn" {
  description = "アーティファクトS3バケットARN"
  value       = aws_s3_bucket.artifacts.arn
}

