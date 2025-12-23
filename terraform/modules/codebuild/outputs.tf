output "api_build_project_name" {
  description = "API CodeBuild Project名"
  value       = aws_codebuild_project.api.name
}

output "console_build_project_name" {
  description = "Console CodeBuild Project名"
  value       = aws_codebuild_project.console.name
}

output "api_build_project_arn" {
  description = "API CodeBuild Project ARN"
  value       = aws_codebuild_project.api.arn
}

output "console_build_project_arn" {
  description = "Console CodeBuild Project ARN"
  value       = aws_codebuild_project.console.arn
}

output "migration_build_project_name" {
  description = "Migration CodeBuild Project名"
  value       = aws_codebuild_project.migration.name
}

