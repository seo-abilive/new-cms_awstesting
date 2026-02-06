# CloudFront Function: 連続スラッシュを正規化（/api//sanctum/... → /api/sanctum/...）
resource "aws_cloudfront_function" "normalize_path" {
  name    = "${var.name_prefix}normalize-path"
  runtime = "cloudfront-js-2.0"
  code    = <<-FUNCTION
    function handler(event) {
      var request = event.request;
      var uri = request.uri;
      
      // 連続スラッシュを1つに正規化（先頭のスラッシュは保持）
      if (uri && uri.length > 0) {
        // 先頭がスラッシュの場合とそうでない場合を考慮
        var leadingSlash = uri.startsWith('/') ? '/' : '';
        var normalized = uri.replace(/\/+/g, '/');
        // 先頭のスラッシュが2つ 이상인 경우も1つに
        request.uri = normalized;
      }
      
      return request;
    }
  FUNCTION
  publish = true
  comment = "Normalize consecutive slashes in request path"
}

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
  # origin_request_policy_id: ビューアの Host をオリジンへ転送し、Laravel のセッションクッキーが CloudFront ドメインで設定されるようにする（419 CSRF 回避）
  default_cache_behavior {
    allowed_methods            = ["GET", "HEAD", "OPTIONS", "PUT", "PATCH", "POST", "DELETE"]
    cached_methods             = ["GET", "HEAD", "OPTIONS"]
    target_origin_id            = "${var.name_prefix}alb-origin"
    viewer_protocol_policy      = "allow-all"
    compress                    = true
    cache_policy_id             = "4135ea2d-6df8-44a3-9df3-4b5a84be39ad" # CachingDisabled（API/Console は動的）
    origin_request_policy_id    = "216adef6-5c7f-47e4-b989-5492eafa07d3" # AllViewer（Host 含む全ヘッダをオリジンへ）

    function_association {
      event_type   = "viewer-request"
      function_arn = aws_cloudfront_function.normalize_path.arn
    }
  }

  # /api/* → API
  ordered_cache_behavior {
    path_pattern                = "/api/*"
    allowed_methods             = ["GET", "HEAD", "OPTIONS", "PUT", "PATCH", "POST", "DELETE"]
    cached_methods             = ["GET", "HEAD", "OPTIONS"]
    target_origin_id            = "${var.name_prefix}alb-origin"
    viewer_protocol_policy      = "allow-all"
    compress                    = true
    cache_policy_id             = "4135ea2d-6df8-44a3-9df3-4b5a84be39ad" # CachingDisabled
    origin_request_policy_id    = "216adef6-5c7f-47e4-b989-5492eafa07d3" # AllViewer（Host 含む全ヘッダをオリジンへ）

    function_association {
      event_type   = "viewer-request"
      function_arn = aws_cloudfront_function.normalize_path.arn
    }
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
