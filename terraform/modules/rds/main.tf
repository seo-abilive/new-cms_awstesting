# DBサブネットグループ作成
resource "aws_db_subnet_group" "main" {
  name       = "${var.name_prefix}db-subnet-group"
  subnet_ids = var.subnet_ids

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}db-subnet-group"
    }
  )
}

# Aurora MySQLクラスターパラメータグループ作成
resource "aws_rds_cluster_parameter_group" "main" {
  name   = "${var.name_prefix}aurora-mysql-8-0"
  family = "aurora-mysql8.0"

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}aurora-mysql-8-0-params"
    }
  )
}

# Aurora MySQLクラスター作成
resource "aws_rds_cluster" "main" {
  cluster_identifier = "${var.name_prefix}aurora-cluster"

  engine         = "aurora-mysql"
  engine_version = "8.0.mysql_aurora.3.04.0" # 안정적인 버전 사용

  database_name   = var.db_name
  master_username = var.db_username
  master_password = var.db_password

  db_subnet_group_name   = aws_db_subnet_group.main.name
  vpc_security_group_ids = var.security_group_ids

  # バックアップ設定（enable_backupがtrueの場合は7日、falseの場合は最小の1日）
  # Aurora MySQLはbackup_retention_periodを0に設定できないため、最小値の1を使用
  backup_retention_period      = var.enable_backup ? 7 : 1
  preferred_backup_window      = "17:00-18:00"         # 夜中 2:00-3:00 (JST)
  preferred_maintenance_window = var.preferred_maintenance_window # デフォルト: 夜中 4:00-5:00 (JST)

  skip_final_snapshot       = !var.skip_final_snapshot # false: スナップショット作成しない、true: 作成する（通常の動作と逆）
  final_snapshot_identifier = var.skip_final_snapshot ? "${var.name_prefix}aurora-final-snapshot-${formatdate("YYYY-MM-DD-hhmm", timestamp())}" : null
  deletion_protection       = var.deletion_protection

  enabled_cloudwatch_logs_exports = ["error", "general", "slowquery"]

  db_cluster_parameter_group_name = aws_rds_cluster_parameter_group.main.name

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}aurora-cluster"
    }
  )
}

# Aurora MySQLインスタンス作成（高可用性のため2つのインスタンスを異なるAZに配置）
resource "aws_rds_cluster_instance" "main" {
  count              = 2 # 2つのインスタンスを作成（異なるAZに自動配置）
  identifier         = "${var.name_prefix}aurora-instance-${count.index + 1}"
  cluster_identifier = aws_rds_cluster.main.id
  instance_class     = "db.t3.medium"
  engine             = aws_rds_cluster.main.engine
  engine_version     = aws_rds_cluster.main.engine_version

  # 自動マイナーバージョンアップグレードを無効化（選択的メンテナンスを最小化）
  auto_minor_version_upgrade = var.auto_minor_version_upgrade

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}aurora-instance-${count.index + 1}"
    }
  )
}

