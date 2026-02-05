# CloudFront マネージドプレフィックスリスト（VPC Origin 経由のトラフィックのみ ALB へ許可）
data "aws_ec2_managed_prefix_list" "cloudfront_origin" {
  filter {
    name   = "prefix-list-name"
    values = ["com.amazonaws.global.cloudfront.origin-facing"]
  }
}

# ALB用セキュリティグループ（Private ALB: CloudFront VPC Origin からのみ受信）
# ingress は aws_security_group_rule で管理（SG のルール数上限に当たらないよう、追加のみ）
resource "aws_security_group" "alb" {
  name        = "${var.name_prefix}alb-sg"
  description = "Security group for ALB"
  vpc_id      = var.vpc_id

  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}alb-sg"
    }
  )
}

# ALB SG: HTTP/HTTPS（CloudFront VPC Origin からのみ・1 ルールで 80/443 を許可し SG 上限 60 件対策）
resource "aws_security_group_rule" "alb_http_https" {
  type              = "ingress"
  description       = "HTTP/HTTPS from CloudFront VPC Origin"
  from_port         = 80
  to_port           = 443
  protocol          = "tcp"
  security_group_id = aws_security_group.alb.id
  prefix_list_ids   = [data.aws_ec2_managed_prefix_list.cloudfront_origin.id]
}

# API ECS用セキュリティグループ
resource "aws_security_group" "ecs_api" {
  name        = "${var.name_prefix}ecs-api-sg"
  description = "Security group for API ECS"
  vpc_id      = var.vpc_id

  # ALBからのHTTPトラフィックのみ許可
  ingress {
    description     = "HTTP traffic from ALB"
    from_port       = 80
    to_port         = 80
    protocol        = "tcp"
    security_groups = [aws_security_group.alb.id]
  }

  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}ecs-api-sg"
    }
  )
}

# Console ECS用セキュリティグループ
resource "aws_security_group" "ecs_console" {
  name        = "${var.name_prefix}ecs-console-sg"
  description = "Security group for Console ECS"
  vpc_id      = var.vpc_id

  # ALBからのHTTPトラフィックのみ許可
  ingress {
    description     = "HTTP traffic from ALB"
    from_port       = 4173
    to_port         = 4173
    protocol        = "tcp"
    security_groups = [aws_security_group.alb.id]
  }

  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}ecs-console-sg"
    }
  )
}

# RDS用セキュリティグループ
resource "aws_security_group" "rds" {
  name        = "${var.name_prefix}rds-sg"
  description = "Security group for RDS"
  vpc_id      = var.vpc_id

  # ECS APIセキュリティグループからのMySQLトラフィックのみ許可
  ingress {
    description     = "MySQL traffic from ECS API"
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.ecs_api.id]
  }

  egress {
    description = "Allow all outbound traffic"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}rds-sg"
    }
  )
}

