output "endpoint" {
  description = "Auroraクラスターエンドポイント"
  value       = aws_rds_cluster.main.endpoint
}

output "reader_endpoint" {
  description = "Auroraクラスターリーダーエンドポイント"
  value       = aws_rds_cluster.main.reader_endpoint
}

output "address" {
  description = "Auroraクラスターアドレス"
  value       = aws_rds_cluster.main.endpoint
}

output "port" {
  description = "Auroraクラスターポート"
  value       = aws_rds_cluster.main.port
}

output "db_name" {
  description = "データベース名"
  value       = aws_rds_cluster.main.database_name
}

