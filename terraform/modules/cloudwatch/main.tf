# CloudWatch Log Groups作成
resource "aws_cloudwatch_log_group" "log_groups" {
  for_each = toset(var.log_group_names)

  name              = "/ecs/${each.value}"
  retention_in_days = 7

  # ログ暗号化を有効化（AWS管理キーを使用）
  kms_key_id = null # nullの場合はAWS管理キーが自動的に使用される

  tags = merge(
    var.tags,
    {
      Name = each.value
    }
  )
}

