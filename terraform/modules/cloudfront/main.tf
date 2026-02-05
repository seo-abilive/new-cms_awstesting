# CloudFront VPC Origin（Private ALB をオリジンとして登録）
resource "aws_cloudfront_vpc_origin" "alb" {
  vpc_origin_endpoint_config {
    name                   = "${var.name_prefix}vpc-origin-alb"
    arn                    = var.alb_arn
    http_port              = 80
    https_port             = 443
    origin_protocol_policy = "http-only"

    origin_ssl_protocols {
      items    = ["TLSv1.2"]
      quantity = 1
    }
  }

  tags = var.tags
}

# CloudFront 配信（VPC Origin = Private ALB）
resource "aws_cloudfront_distribution" "main" {
  enabled             = true
  is_ipv6_enabled     = true
  comment             = "${var.name_prefix} CloudFront (VPC Origin -> Private ALB)"
  default_root_object = ""
  price_class         = "PriceClass_200"

  # VPC Origin 参照: domain_name は API 上「ドメイン名」が必須のためプレースホルダーを使用（実ルーティングは vpc_origin_id）
  origin {
    domain_name = "vpc-origin.cloudfront.net"
    origin_id   = "${var.name_prefix}alb-origin"

    vpc_origin_config {
      vpc_origin_id = aws_cloudfront_vpc_origin.alb.id
    }
  }

  # デフォルト: Console（/*）
  default_cache_behavior {
    allowed_methods        = ["GET", "HEAD", "OPTIONS", "PUT", "PATCH", "POST", "DELETE"]
    cached_methods        = ["GET", "HEAD", "OPTIONS"]
    target_origin_id      = "${var.name_prefix}alb-origin"
    viewer_protocol_policy = "allow-all"
    compress              = true

    cache_policy_id = "4135ea2d-6df8-44a3-9df3-4b5a84be39ad" # CachingDisabled（API/Console は動的）
  }

  # /api/* → API
  ordered_cache_behavior {
    path_pattern           = "/api/*"
    allowed_methods        = ["GET", "HEAD", "OPTIONS", "PUT", "PATCH", "POST", "DELETE"]
    cached_methods         = ["GET", "HEAD", "OPTIONS"]
    target_origin_id       = "${var.name_prefix}alb-origin"
    viewer_protocol_policy = "allow-all"
    compress               = true

    cache_policy_id = "4135ea2d-6df8-44a3-9df3-4b5a84be39ad" # CachingDisabled
  }

  restrictions {
    geo_restriction {
      restriction_type = "none"
      locations        = []
    }
  }

  viewer_certificate {
    cloudfront_default_certificate = true
  }

  tags = var.tags
}
