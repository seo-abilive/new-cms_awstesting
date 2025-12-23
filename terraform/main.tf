# Provider設定
terraform {
  required_version = ">= 1.0"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

provider "aws" {
  profile = var.aws_profile != "" ? var.aws_profile : null
  region  = var.aws_region
}

# ローカル値: name_prefixをaws_prefixとgithub_branchから動的に生成
locals {
  name_prefix = var.name_prefix != "" ? var.name_prefix : "${var.aws_prefix}-${var.github_branch}-"
}

# VPCモジュール
module "vpc" {
  source = "./modules/vpc"

  name_prefix = local.name_prefix
  vpc_cidr    = var.vpc_cidr

  public_subnet_cidrs  = var.public_subnet_cidrs
  private_subnet_cidrs = var.private_subnet_cidrs

  availability_zones = var.availability_zones

  tags = var.tags
}

# セキュリティグループモジュール
module "security_groups" {
  source = "./modules/security_groups"

  name_prefix = local.name_prefix
  vpc_id      = module.vpc.vpc_id

  tags = var.tags
}

# RDSモジュール
module "rds" {
  source = "./modules/rds"

  name_prefix = local.name_prefix
  vpc_id      = module.vpc.vpc_id

  subnet_ids = module.vpc.private_subnet_ids

  db_name     = var.db_name
  db_username = var.db_username
  db_password = var.db_password

  enable_backup = var.enable_backup

  skip_final_snapshot = var.skip_final_snapshot

  deletion_protection = var.deletion_protection

  preferred_maintenance_window = var.preferred_maintenance_window

  auto_minor_version_upgrade = var.auto_minor_version_upgrade

  high_availability = var.high_availability

  security_group_ids = [
    module.security_groups.rds_sg_id
  ]

  tags = var.tags
}

# ECRモジュール
module "ecr" {
  source = "./modules/ecr"

  name_prefix = local.name_prefix

  repository_names = [
    "${local.name_prefix}api",
    "${local.name_prefix}console",
    "${local.name_prefix}base-images"
  ]

  tags = var.tags
}

# CloudWatch Log Groupsモジュール
module "cloudwatch" {
  source = "./modules/cloudwatch"

  name_prefix = local.name_prefix

  log_group_names = [
    "${local.name_prefix}api",
    "${local.name_prefix}console"
  ]

  tags = var.tags
}

# IAMモジュール
module "iam" {
  source = "./modules/iam"

  name_prefix = local.name_prefix

  ecr_repository_arns = [
    module.ecr.repository_arns["${local.name_prefix}api"],
    module.ecr.repository_arns["${local.name_prefix}console"]
  ]

  cloudwatch_log_group_arns = [
    module.cloudwatch.log_group_arns["${local.name_prefix}api"],
    module.cloudwatch.log_group_arns["${local.name_prefix}console"]
  ]

  # CodeStar Connection ARN（CodePipelineモジュールが作成される前に空文字、後で更新可能）
  # 注意: 初回作成時は空文字で、CodePipelineモジュール作成後に更新が必要
  # 順序の問題を避けるため、IAMポリシーでは"*"で許可し、後で特定のConnection ARNに制限可能
  codestar_connection_arn = ""

  tags = var.tags
}

# ALBモジュール（ECSより先に作成）
module "alb" {
  source = "./modules/alb"

  name_prefix = local.name_prefix
  vpc_id      = module.vpc.vpc_id

  subnets = module.vpc.public_subnet_ids

  security_group_id = module.security_groups.alb_sg_id

  # Target Groups
  api_target_group = {
    name     = "${local.name_prefix}api-tg"
    port     = 80
    protocol = "HTTP"
    vpc_id   = module.vpc.vpc_id
  }

  console_target_group = {
    name     = "${local.name_prefix}console-tg"
    port     = 4173
    protocol = "HTTP"
    vpc_id   = module.vpc.vpc_id
  }

  # ECS Services（後で設定）
  api_service_name     = "${local.name_prefix}api-service"
  console_service_name = "${local.name_prefix}console-service"

  cluster_name = "${local.name_prefix}cluster"

  # ACM証明書ARN（オプション）
  certificate_arn = var.certificate_arn

  tags = var.tags
}

# ECSモジュール
module "ecs" {
  source = "./modules/ecs"

  name_prefix = local.name_prefix

  cluster_name = "${local.name_prefix}cluster"

  # API設定
  api_task_definition = {
    family             = "${local.name_prefix}api"
    container_name     = "api"
    image              = "${module.ecr.repository_urls["${local.name_prefix}api"]}:latest"
    container_port     = 80
    log_group_name     = module.cloudwatch.log_group_names["${local.name_prefix}api"]
    execution_role_arn = module.iam.ecs_task_execution_role_arn
    task_role_arn      = module.iam.ecs_task_role_arn
    cpu                = 512
    memory             = 1024
    environment_variables = merge(
      {
        # アプリケーション基本設定（.envから参照）
        APP_NAME               = "Laravel"
        APP_ENV                = "production"
        APP_KEY                = var.app_key
        APP_DEBUG              = "false"
        APP_LOCALE             = "en"
        APP_FALLBACK_LOCALE    = "en"
        APP_FAKER_LOCALE       = "en_US"
        APP_MAINTENANCE_DRIVER = "file"
        PHP_CLI_SERVER_WORKERS = "4"
        BCRYPT_ROUNDS          = "12"

        # ログ設定（.envから参照）
        # CloudWatchで確認できるようにstderrに出力
        LOG_CHANNEL              = "stderr"
        LOG_STACK                = "stderr"
        LOG_DEPRECATIONS_CHANNEL = "null"
        LOG_LEVEL                = "debug" # デバッグ用にdebugに設定（問題解決後はerrorに変更可能）

        # データベース設定（.envから参照）
        DB_CONNECTION = "mysql"
        DB_HOST       = module.rds.endpoint
        DB_PORT       = "3306"
        DB_DATABASE   = var.db_name
        DB_USERNAME   = var.db_username
        DB_PASSWORD   = var.db_password

        # セッション設定（.envから参照）
        SESSION_DRIVER   = "database" # データベースを使用（.envから参照）
        SESSION_LIFETIME = "120"
        SESSION_ENCRYPT  = "false"
        SESSION_PATH     = "/"
        SESSION_DOMAIN   = "null"

        # ブロードキャスト・ファイルシステム・キュー設定（.envから参照）
        BROADCAST_CONNECTION = "log"
        FILESYSTEM_DISK      = "local"
        QUEUE_CONNECTION     = "database" # データベースを使用（.envから参照）

        # キャッシュ設定（.envから参照）
        CACHE_STORE = "database" # データベースを使用（.envから参照）

        # Redis設定（.envから参照、使用しない場合はデフォルト値）
        REDIS_CLIENT   = "phpredis"
        REDIS_HOST     = "127.0.0.1"
        REDIS_PASSWORD = "null"
        REDIS_PORT     = "6379"

        # メール設定（.envから参照）
        MAIL_MAILER       = "smtp"
        MAIL_SCHEME       = "null"
        MAIL_HOST         = "sv856.xbiz.ne.jp"
        MAIL_PORT         = "587"
        MAIL_ENCRYPTION   = "tls"
        MAIL_USERNAME     = "info@abitestxsrv.xbiz.jp"
        MAIL_FROM_ADDRESS = "info@abitestxsrv.xbiz.jp"
        MAIL_FROM_NAME    = "Laravel"

        # AWS設定（.envから参照、必要に応じて設定）
        AWS_DEFAULT_REGION = "ap-northeast-1"

        # フロントエンドURL設定（パスワードリセットなどで使用）
        # api_urlが http://alb-url/api の場合、frontend_urlは http://alb-url になる
        FRONTEND_URL = var.frontend_url != "" ? var.frontend_url : (var.api_url != "" ? replace(var.api_url, "/api", "") : "")

        # Gemini API設定（.envから参照）
        GEMINI_API_ENDPOINT = var.gemini_api_endpoint != "" ? var.gemini_api_endpoint : "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent"

        # ログイン設定（.envから参照）
        LOGIN_MAX_ATTEMPTS     = var.login_max_attempts != "" ? var.login_max_attempts : "5"
        LOGIN_LOCKOUT_DURATION = var.login_lockout_duration != "" ? var.login_lockout_duration : "15"
      },
      var.api_url != "" ? { APP_URL = var.api_url } : {},
      var.mail_password != "" ? { MAIL_PASSWORD = var.mail_password } : {}
    )
  }

  # Console設定
  console_task_definition = {
    family             = "${local.name_prefix}console"
    container_name     = "console"
    image              = "${module.ecr.repository_urls["${local.name_prefix}console"]}:latest"
    container_port     = 4173
    log_group_name     = module.cloudwatch.log_group_names["${local.name_prefix}console"]
    execution_role_arn = module.iam.ecs_task_execution_role_arn
    task_role_arn      = module.iam.ecs_task_role_arn
    cpu                = 256
    memory             = 512
    environment_variables = merge(
      {
        VITE_APP_ENV = "production"
      },
      var.api_url != "" ? { VITE_API_ORIGIN = var.api_url } : {},
      var.console_allowed_hosts != "" ? { VITE_ALLOWED_HOSTS = var.console_allowed_hosts } : {}
    )
  }

  # Service設定
  api_service = {
    name            = "${local.name_prefix}api-service"
    desired_count   = var.high_availability ? 2 : 1 # 高可用性の場合は2つ、そうでない場合は1つ
    security_groups = [module.security_groups.ecs_api_sg_id]
    subnets         = module.vpc.public_subnet_ids
  }

  console_service = {
    name            = "${local.name_prefix}console-service"
    desired_count   = var.high_availability ? 2 : 1 # 高可用性の場合は2つ、そうでない場合は1つ
    security_groups = [module.security_groups.ecs_console_sg_id]
    subnets         = module.vpc.public_subnet_ids
  }

  # Target Group ARNs
  api_target_group_arn     = module.alb.api_target_group_arn
  console_target_group_arn = module.alb.console_target_group_arn

  tags = var.tags
}

# CodeBuildモジュール
module "codebuild" {
  source = "./modules/codebuild"

  name_prefix = local.name_prefix

  # API CodeBuild
  api_build = {
    name             = "${local.name_prefix}api-build"
    repository_url   = var.github_repository_url
    ecr_repository   = module.ecr.repository_urls["${local.name_prefix}api"]
    build_context    = "./api"
    dockerfile_path  = "api/Dockerfile"
    service_role_arn = module.iam.codebuild_role_arn
  }

  # Console CodeBuild
  console_build = {
    name             = "${local.name_prefix}console-build"
    repository_url   = var.github_repository_url
    ecr_repository   = module.ecr.repository_urls["${local.name_prefix}console"]
    build_context    = "./console"
    dockerfile_path  = "console/Dockerfile"
    service_role_arn = module.iam.codebuild_role_arn
  }

  # API URL（CodeBuildのVITE_API_ORIGIN環境変数として使用）
  api_url = var.api_url

  # マイグレーション用設定
  cluster_name               = module.ecs.cluster_name
  api_task_definition_family = "${local.name_prefix}api"
  subnet_ids                 = module.vpc.public_subnet_ids
  security_group_id          = module.security_groups.ecs_api_sg_id

  tags = var.tags
}

# CodePipelineモジュール
module "codepipeline" {
  source = "./modules/codepipeline"

  name_prefix = local.name_prefix

  github_repository_url = var.github_repository_url
  github_branch         = var.github_branch

  api_build_project_name       = module.codebuild.api_build_project_name
  console_build_project_name   = module.codebuild.console_build_project_name
  migration_build_project_name = module.codebuild.migration_build_project_name

  service_role_arn = module.iam.codepipeline_role_arn

  cluster_name         = module.ecs.cluster_name
  api_service_name     = module.ecs.api_service_name
  console_service_name = module.ecs.console_service_name

  tags = var.tags
}

