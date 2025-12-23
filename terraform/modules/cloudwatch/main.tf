# CloudWatch Log Groups作成
resource "aws_cloudwatch_log_group" "log_groups" {
  for_each = toset(var.log_group_names)

  name              = "/ecs/${each.value}"
  retention_in_days = 7

  tags = merge(
    var.tags,
    {
      Name = each.value
    }
  )
}

