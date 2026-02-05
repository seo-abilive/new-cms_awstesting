alb_dns_name = "new-cms-main-alb-782639561.ap-northeast-1.elb.amazonaws.com"
api_endpoint_url = "http://new-cms-main-alb-782639561.ap-northeast-1.elb.amazonaws.com/api/v1/"
ecr_api_repository_url = "993704921089.dkr.ecr.ap-northeast-1.amazonaws.com/new-cms-main-api"
ecr_console_repository_url = "993704921089.dkr.ecr.ap-northeast-1.amazonaws.com/new-cms-main-console"
public_subnet_ids = [
  "subnet-0589114b38c1f93d1",
  "subnet-0fc1d9a4dbbca7709",
]
rds_endpoint = "new-cms-main-aurora-cluster.cluster-c1comq0y4pds.ap-northeast-1.rds.amazonaws.com"
rds_security_group_id = "sg-01f572b6d6e712001"
vpc_id = "vpc-04be013811a6cfe32"



주요 변경 사항
ALB는 Private(인터넷 직접 접근 불가)
CloudFront가 단일 진입점으로 동작
ALB 보안 그룹은 CloudFront managed prefix list만 허용
api_url 미설정 시 자동으로 CloudFront URL 사용
참고사항
CloudFront 배포는 약 15분 소요
VPC Origin 생성 후 "Deployed" 상태가 되어야 Distribution에서 사용 가능
ap-northeast-1의 경우 AZ apne1-az3는 VPC Origins 미지원 (다른 AZ 사용 권장)