# ALB用セキュリティグループ
resource "aws_security_group" "alb" {
  name        = "${var.name_prefix}alb-sg"
  description = "Security group for ALB"
  vpc_id      = var.vpc_id

  # HTTP
  ingress {
    description = "HTTP"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  # HTTPS
  ingress {
    description = "HTTPS"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
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
      Name = "${var.name_prefix}alb-sg"
    }
  )
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

