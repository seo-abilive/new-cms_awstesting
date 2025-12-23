# ECSクラスター作成
resource "aws_ecs_cluster" "main" {
  name = var.cluster_name

  setting {
    name  = "containerInsights"
    value = "enabled"
  }

  tags = merge(
    var.tags,
    {
      Name = var.cluster_name
    }
  )
}

# API Task Definition
resource "aws_ecs_task_definition" "api" {
  family                   = var.api_task_definition.family
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  cpu                      = var.api_task_definition.cpu
  memory                   = var.api_task_definition.memory
  execution_role_arn       = var.api_task_definition.execution_role_arn
  task_role_arn            = var.api_task_definition.task_role_arn

  container_definitions = jsonencode([
    {
      name      = var.api_task_definition.container_name
      image     = var.api_task_definition.image
      essential = true

      portMappings = [
        {
          containerPort = var.api_task_definition.container_port
          protocol      = "tcp"
        }
      ]

      environment = [
        for key, value in var.api_task_definition.environment_variables : {
          name  = key
          value = value
        }
      ]

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          "awslogs-group"         = var.api_task_definition.log_group_name
          "awslogs-region"        = data.aws_region.current.name
          "awslogs-stream-prefix" = "ecs"
        }
      }

      # ECS 컨테이너 헬스체크 제거 - ALB 타겟 그룹 헬스체크만 사용
      # 컨테이너 헬스체크가 배포 실패의 주요 원인이 될 수 있으므로 제거
    }
  ])

  tags = merge(
    var.tags,
    {
      Name = var.api_task_definition.family
    }
  )
}

# Console Task Definition
resource "aws_ecs_task_definition" "console" {
  family                   = var.console_task_definition.family
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  cpu                      = var.console_task_definition.cpu
  memory                   = var.console_task_definition.memory
  execution_role_arn       = var.console_task_definition.execution_role_arn
  task_role_arn            = var.console_task_definition.task_role_arn

  container_definitions = jsonencode([
    {
      name      = var.console_task_definition.container_name
      image     = var.console_task_definition.image
      essential = true

      portMappings = [
        {
          containerPort = var.console_task_definition.container_port
          protocol      = "tcp"
        }
      ]

      environment = [
        for key, value in var.console_task_definition.environment_variables : {
          name  = key
          value = value
        }
      ]

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          "awslogs-group"         = var.console_task_definition.log_group_name
          "awslogs-region"        = data.aws_region.current.name
          "awslogs-stream-prefix" = "ecs"
        }
      }

      # ECS 컨테이너 헬스체크 제거 - ALB 타겟 그룹 헬스체크만 사용
    }
  ])

  tags = merge(
    var.tags,
    {
      Name = var.console_task_definition.family
    }
  )
}

# API Service
resource "aws_ecs_service" "api" {
  name            = var.api_service.name
  cluster         = aws_ecs_cluster.main.id
  task_definition = aws_ecs_task_definition.api.arn
  desired_count   = var.api_service.desired_count
  launch_type     = "FARGATE"

  # デプロイ設定（terraform-projectを参考）
  deployment_maximum_percent         = 200
  deployment_minimum_healthy_percent = 0  # ALBヘルスチェックのみ使用するため0%に設定

  # ヘルスチェック猶予期間（10分）- ALBヘルスチェックが安定화するまでの猶予期間
  health_check_grace_period_seconds = 900

  # Circuit Breakerを再度有効化（타임아웃 방지를 위해 롤백 허용）
  deployment_circuit_breaker {
    enable   = true
    rollback = true
  }

  network_configuration {
    subnets          = var.api_service.subnets
    security_groups  = var.api_service.security_groups
    assign_public_ip = true
  }

  load_balancer {
    target_group_arn = var.api_target_group_arn
    container_name   = var.api_task_definition.container_name
    container_port   = var.api_task_definition.container_port
  }

  depends_on = [var.api_target_group_arn]

  tags = merge(
    var.tags,
    {
      Name = var.api_service.name
    }
  )
}

# Console Service
resource "aws_ecs_service" "console" {
  name            = var.console_service.name
  cluster         = aws_ecs_cluster.main.id
  task_definition = aws_ecs_task_definition.console.arn
  desired_count   = var.console_service.desired_count
  launch_type     = "FARGATE"

  # デプロイ設定（terraform-projectを参考）
  deployment_maximum_percent         = 200
  deployment_minimum_healthy_percent = 50

  # ヘルスチェック猶予期間（5分）
  health_check_grace_period_seconds = 300

  # デプロイ失敗時の自動ロールバック（Circuit Breaker）
  deployment_circuit_breaker {
    enable   = true
    rollback = true
  }

  network_configuration {
    subnets          = var.console_service.subnets
    security_groups  = var.console_service.security_groups
    assign_public_ip = true
  }

  load_balancer {
    target_group_arn = var.console_target_group_arn
    container_name   = var.console_task_definition.container_name
    container_port   = var.console_task_definition.container_port
  }

  depends_on = [var.console_target_group_arn]

  tags = merge(
    var.tags,
    {
      Name = var.console_service.name
    }
  )
}

# 現在のリージョン取得
data "aws_region" "current" {}

