# ALB作成
resource "aws_lb" "main" {
  name               = "${var.name_prefix}alb"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [var.security_group_id]
  subnets            = var.subnets

  enable_deletion_protection = false

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}alb"
    }
  )
}

# API Target Group
resource "aws_lb_target_group" "api" {
  name        = var.api_target_group.name
  port        = var.api_target_group.port
  protocol    = var.api_target_group.protocol
  vpc_id      = var.api_target_group.vpc_id
  target_type = "ip"

  health_check {
    enabled             = true
    healthy_threshold   = 2
    unhealthy_threshold = 10 # 최대값 (일시적 오류 허용)
    timeout             = 10
    interval            = 30
    path                = "/healthz" # DB 연결 없이 항상 200 반환하는 엔드포인트 사용
    protocol            = "HTTP"
    matcher             = "200" # 200 응답만 허용
    port                = "traffic-port"
  }

  deregistration_delay = 30

  lifecycle {
    create_before_destroy = true
  }

  tags = merge(
    var.tags,
    {
      Name = var.api_target_group.name
    }
  )
}

# Console Target Group
resource "aws_lb_target_group" "console" {
  name        = var.console_target_group.name
  port        = var.console_target_group.port
  protocol    = var.console_target_group.protocol
  vpc_id      = var.console_target_group.vpc_id
  target_type = "ip"

  health_check {
    enabled             = true
    healthy_threshold   = 2
    unhealthy_threshold = 2
    timeout             = 5
    interval            = 30
    # Vite previewのbaseが /console/dist/ のため、
    # ヘルスチェックも /console/dist/ に対して実行する
    path     = "/console/dist/"
    protocol = "HTTP"
    # リダイレクトなども許可するため 200〜399 を許容
    matcher = "200-399"
  }

  deregistration_delay = 30

  lifecycle {
    create_before_destroy = true
  }

  tags = merge(
    var.tags,
    {
      Name = var.console_target_group.name
    }
  )
}

# HTTP Listener（証明書がない場合は直接ルーティング、証明書がある場合はHTTPSにリダイレクト）
resource "aws_lb_listener" "http" {
  load_balancer_arn = aws_lb.main.arn
  port              = "80"
  protocol          = "HTTP"

  # 証明書がある場合はHTTPSにリダイレクト、ない場合はConsoleにフォワード
  default_action {
    type = var.certificate_arn != "" ? "redirect" : "forward"

    dynamic "redirect" {
      for_each = var.certificate_arn != "" ? [1] : []
      content {
        port        = "443"
        protocol    = "HTTPS"
        status_code = "HTTP_301"
      }
    }

    dynamic "forward" {
      for_each = var.certificate_arn == "" ? [1] : []
      content {
        target_group {
          arn = aws_lb_target_group.console.arn
        }
      }
    }
  }
}

# HTTPS Listener（証明書が設定されている場合）
resource "aws_lb_listener" "https" {
  count = var.certificate_arn != "" ? 1 : 0

  load_balancer_arn = aws_lb.main.arn
  port              = "443"
  protocol          = "HTTPS"
  ssl_policy        = "ELBSecurityPolicy-TLS13-1-2-2021-06"
  certificate_arn   = var.certificate_arn

  # デフォルトアクション: Console Target Group（/*ルールより優先度が低い）
  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.console.arn
  }
}

# HTTPS Listener Rule: /api/* → API Target Group（優先度1）
resource "aws_lb_listener_rule" "api" {
  count = var.certificate_arn != "" ? 1 : 0

  listener_arn = aws_lb_listener.https[0].arn
  priority     = 1

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.api.arn
  }

  condition {
    path_pattern {
      values = ["/api/*"]
    }
  }
}

# HTTPS Listener Rule: /* → Console Target Group（優先度2、デフォルト）
resource "aws_lb_listener_rule" "console" {
  count = var.certificate_arn != "" ? 1 : 0

  listener_arn = aws_lb_listener.https[0].arn
  priority     = 2

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.console.arn
  }

  condition {
    path_pattern {
      values = ["/*"]
    }
  }

  depends_on = [aws_lb_target_group.console]
}

# HTTP Listener Rule: /api/* → API Target Group（証明書がない場合のフォールバック）
resource "aws_lb_listener_rule" "api_http" {
  count = var.certificate_arn == "" ? 1 : 0

  listener_arn = aws_lb_listener.http.arn
  priority     = 1

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.api.arn
  }

  condition {
    path_pattern {
      values = ["/api/*"]
    }
  }
}

# HTTP Listener Rule: /* → Console Target Group（証明書がない場合のフォールバック）
resource "aws_lb_listener_rule" "console_http" {
  count = var.certificate_arn == "" ? 1 : 0

  listener_arn = aws_lb_listener.http.arn
  priority     = 2

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.console.arn
  }

  condition {
    path_pattern {
      values = ["/*"]
    }
  }

  depends_on = [aws_lb_target_group.console]
}


