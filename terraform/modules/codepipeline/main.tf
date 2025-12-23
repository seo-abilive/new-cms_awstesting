# S3バケット（CodePipeline用アーティファクトストア）
resource "aws_s3_bucket" "artifacts" {
  bucket = "${var.name_prefix}codepipeline-artifacts"

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}codepipeline-artifacts"
    }
  )
}

resource "aws_s3_bucket_versioning" "artifacts" {
  bucket = aws_s3_bucket.artifacts.id

  versioning_configuration {
    status = "Enabled"
  }
}

resource "aws_s3_bucket_server_side_encryption_configuration" "artifacts" {
  bucket = aws_s3_bucket.artifacts.id

  rule {
    apply_server_side_encryption_by_default {
      sse_algorithm = "AES256"
    }
  }
}

resource "aws_s3_bucket_public_access_block" "artifacts" {
  bucket = aws_s3_bucket.artifacts.id

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true
}

# CodePipeline
resource "aws_codepipeline" "main" {
  name     = "${var.name_prefix}pipeline"
  role_arn = var.service_role_arn

  artifact_store {
    location = aws_s3_bucket.artifacts.bucket
    type     = "S3"
  }

  stage {
    name = "Source"

    action {
      name             = "Source"
      category         = "Source"
      owner            = "ThirdParty"
      provider         = "GitHub"
      version          = "1"
      output_artifacts = ["source_output"]

      configuration = {
        Owner                = split("/", split("github.com/", var.github_repository_url)[1])[0]
        Repo                 = replace(split("/", split("github.com/", var.github_repository_url)[1])[1], ".git", "")
        Branch               = var.github_branch
        OAuthToken           = var.github_token
        PollForSourceChanges = "true"
      }
    }
  }

  stage {
    name = "Build"

    action {
      name             = "Build-API"
      category         = "Build"
      owner            = "AWS"
      provider         = "CodeBuild"
      input_artifacts  = ["source_output"]
      output_artifacts = ["api_build_output"]
      version          = "1"
      run_order        = 1

      configuration = {
        ProjectName = var.api_build_project_name
      }
    }

    action {
      name             = "Build-Console"
      category         = "Build"
      owner            = "AWS"
      provider         = "CodeBuild"
      input_artifacts  = ["source_output"]
      output_artifacts = ["console_build_output"]
      version          = "1"
      run_order        = 1

      configuration = {
        ProjectName = var.console_build_project_name
      }
    }
  }

  stage {
    name = "Deploy"

    action {
      name            = "Deploy-API"
      category        = "Deploy"
      owner           = "AWS"
      provider        = "ECS"
      input_artifacts = ["api_build_output"]
      version         = "1"
      run_order       = 1

      configuration = {
        ClusterName = var.cluster_name
        ServiceName = var.api_service_name
        FileName    = "imagedefinitions.json"
      }
    }

    action {
      name            = "Deploy-Console"
      category        = "Deploy"
      owner           = "AWS"
      provider        = "ECS"
      input_artifacts = ["console_build_output"]
      version         = "1"
      run_order       = 1

      configuration = {
        ClusterName = var.cluster_name
        ServiceName = var.console_service_name
        FileName    = "imagedefinitions.json"
      }
    }
  }

  stage {
    name = "Migrate"

    action {
      name            = "Run-Migration"
      category        = "Build"
      owner           = "AWS"
      provider        = "CodeBuild"
      input_artifacts = ["source_output"]
      version         = "1"
      run_order       = 1

      configuration = {
        ProjectName = var.migration_build_project_name
      }
    }
  }

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}pipeline"
    }
  )
}

