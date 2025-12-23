# API CodeBuild Project
resource "aws_codebuild_project" "api" {
  name          = var.api_build.name
  description   = "API Dockerイメージビルドプロジェクト"
  build_timeout = 60
  service_role  = var.api_build.service_role_arn

  artifacts {
    type = "NO_ARTIFACTS"
  }

  environment {
    compute_type                = "BUILD_GENERAL1_SMALL"
    image                       = "aws/codebuild/standard:7.0"
    type                        = "LINUX_CONTAINER"
    image_pull_credentials_type = "CODEBUILD"
    privileged_mode             = true

    environment_variable {
      name  = "AWS_DEFAULT_REGION"
      value = data.aws_region.current.name
    }

    environment_variable {
      name  = "AWS_ACCOUNT_ID"
      value = data.aws_caller_identity.current.account_id
    }

    environment_variable {
      name  = "IMAGE_REPO_NAME"
      value = split("/", var.api_build.ecr_repository)[1]
    }

    environment_variable {
      name  = "IMAGE_TAG"
      value = "latest"
    }
  }

  source {
    type            = "GITHUB"
    location        = var.api_build.repository_url
    git_clone_depth = 1

    git_submodules_config {
      fetch_submodules = false
    }

    buildspec = <<-BUILDSPEC
      version: 0.2
      phases:
        pre_build:
          commands:
            - echo Logging in to Amazon ECR...
            - aws ecr get-login-password --region $AWS_DEFAULT_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com
            - REPOSITORY_URI=$AWS_ACCOUNT_ID.dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com/$IMAGE_REPO_NAME
            - COMMIT_HASH=$(echo $CODEBUILD_RESOLVED_SOURCE_VERSION | cut -c 1-7)
            - IMAGE_TAG=$${COMMIT_HASH:-latest}
            - |
              # BaseイメージをECRに保存（Docker Hub rate limit回避）
              BASE_IMAGE_REPO=$AWS_ACCOUNT_ID.dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com/${var.name_prefix}base-images
              BASE_IMAGE="php:8.2-apache"
              BASE_IMAGE_TAG="php-8.2-apache"
              # ECRにイメージが存在するか確認
              if ! aws ecr describe-images --repository-name ${var.name_prefix}base-images --image-ids imageTag=$BASE_IMAGE_TAG --region $AWS_DEFAULT_REGION 2>/dev/null; then
                echo "ECRにベースイメージが存在しないため、Docker Hubから取得してECRにプッシュ..."
                if docker pull $BASE_IMAGE 2>/dev/null; then
                  docker tag $BASE_IMAGE $BASE_IMAGE_REPO:$BASE_IMAGE_TAG
                  docker push $BASE_IMAGE_REPO:$BASE_IMAGE_TAG
                  echo "ベースイメージをECRに保存しました"
                else
                  echo "警告: Docker Hubからの取得に失敗しました。ECRに既存イメージがあるか確認します"
                fi
              fi
              echo "ECRからベースイメージを取得..."
              if docker pull $BASE_IMAGE_REPO:$BASE_IMAGE_TAG 2>/dev/null; then
                docker tag $BASE_IMAGE_REPO:$BASE_IMAGE_TAG $BASE_IMAGE
                echo "ECRからベースイメージを取得しました"
              else
                echo "警告: ECRから取得に失敗。Docker Hubから直接取得を試行します"
                docker pull $BASE_IMAGE || echo "エラー: ベースイメージの取得に失敗しました"
              fi
        build:
          commands:
            - echo Build started on `date`
            - echo Building the Docker image...
            - cd ${var.api_build.build_context}
            - docker build -t $REPOSITORY_URI:latest -t $REPOSITORY_URI:$IMAGE_TAG -f Dockerfile .
            - cd $CODEBUILD_SRC_DIR
        post_build:
          commands:
            - echo Build completed on `date`
            - echo Pushing the Docker images...
            - docker push $REPOSITORY_URI:latest
            - docker push $REPOSITORY_URI:$IMAGE_TAG
            - echo Writing image definitions file...
            - printf '[{"name":"api","imageUri":"%s"}]' $REPOSITORY_URI:latest > imagedefinitions.json
            - |
              if [ ! -f imagedefinitions.json ]; then
                echo "ERROR: Failed to create imagedefinitions.json"
                exit 1
              fi
              echo "imagedefinitions.json contents:"
              cat imagedefinitions.json
      artifacts:
        files:
          - imagedefinitions.json
    BUILDSPEC
  }

  logs_config {
    cloudwatch_logs {
      group_name  = "/aws/codebuild/${var.api_build.name}"
      stream_name = "build-log"
    }
  }

  tags = merge(
    var.tags,
    {
      Name = var.api_build.name
    }
  )
}

# Console CodeBuild Project
resource "aws_codebuild_project" "console" {
  name          = var.console_build.name
  description   = "Console Dockerイメージビルドプロジェクト"
  build_timeout = 60
  service_role  = var.console_build.service_role_arn

  artifacts {
    type = "NO_ARTIFACTS"
  }

  environment {
    compute_type                = "BUILD_GENERAL1_SMALL"
    image                       = "aws/codebuild/standard:7.0"
    type                        = "LINUX_CONTAINER"
    image_pull_credentials_type = "CODEBUILD"
    privileged_mode             = true

    environment_variable {
      name  = "AWS_DEFAULT_REGION"
      value = data.aws_region.current.name
    }

    environment_variable {
      name  = "AWS_ACCOUNT_ID"
      value = data.aws_caller_identity.current.account_id
    }

    environment_variable {
      name  = "IMAGE_REPO_NAME"
      value = split("/", var.console_build.ecr_repository)[1]
    }

    environment_variable {
      name  = "IMAGE_TAG"
      value = "latest"
    }

    dynamic "environment_variable" {
      for_each = var.api_url != "" ? [1] : []
      content {
        name  = "VITE_API_ORIGIN"
        value = var.api_url
      }
    }
  }

  source {
    type            = "GITHUB"
    location        = var.console_build.repository_url
    git_clone_depth = 1

    git_submodules_config {
      fetch_submodules = false
    }

    buildspec = <<-BUILDSPEC
version: 0.2
phases:
  pre_build:
    commands:
      - echo Logging in to Amazon ECR...
      - aws ecr get-login-password --region $AWS_DEFAULT_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com
      - REPOSITORY_URI=$AWS_ACCOUNT_ID.dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com/$IMAGE_REPO_NAME
      - COMMIT_HASH=$(echo $CODEBUILD_RESOLVED_SOURCE_VERSION | cut -c 1-7)
      - IMAGE_TAG=$${COMMIT_HASH:-latest}
      - |
        # BaseイメージをECRに保存（Docker Hub rate limit回避）
        BASE_IMAGE_REPO=$AWS_ACCOUNT_ID.dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com/${var.name_prefix}base-images
        BASE_IMAGE="node:20"
        BASE_IMAGE_TAG="node-20"
        # ECRにイメージが存在するか確認
        if ! aws ecr describe-images --repository-name ${var.name_prefix}base-images --image-ids imageTag=$BASE_IMAGE_TAG --region $AWS_DEFAULT_REGION 2>/dev/null; then
          echo "ECRにベースイメージが存在しないため、Docker Hubから取得してECRにプッシュ..."
          if docker pull $BASE_IMAGE 2>/dev/null; then
            docker tag $BASE_IMAGE $BASE_IMAGE_REPO:$BASE_IMAGE_TAG
            docker push $BASE_IMAGE_REPO:$BASE_IMAGE_TAG
            echo "ベースイメージをECRに保存しました"
          else
            echo "警告: Docker Hubからの取得に失敗しました。ECRに既存イメージがあるか確認します"
          fi
        fi
        echo "ECRからベースイメージを取得..."
        if docker pull $BASE_IMAGE_REPO:$BASE_IMAGE_TAG 2>/dev/null; then
          docker tag $BASE_IMAGE_REPO:$BASE_IMAGE_TAG $BASE_IMAGE
          echo "ECRからベースイメージを取得しました"
        else
          echo "警告: ECRから取得に失敗。Docker Hubから直接取得を試行します"
          docker pull $BASE_IMAGE || echo "エラー: ベースイメージの取得に失敗しました"
        fi
      - echo Installing Node.js 20...
      - curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
      - apt-get install -y nodejs
      - |
        # 新しくインストールしたNode.js 20のパスをPATHの先頭に追加（以降のすべてのコマンドで有効）
        export PATH=/usr/bin:$PATH
        # 既存のNode.jsキャッシュを削除
        hash -r
        # Node.jsバージョンを確認
        node --version
        npm --version
  build:
    commands:
      - |
        # PATHを確実に設定（buildフェーズでも有効にするため）
        export PATH=/usr/bin:$PATH
      - echo Build started on `date`
      - echo Building the application...
      - cd ${var.console_build.build_context}
      - |
        # package-lock.jsonの有無に応じてnpm ciまたはnpm installを実行
        if [ -f package-lock.json ]; then
          npm ci
        else
          npm install
        fi
      - npm run build
      - |
        # distフォルダが生成されたことを確認
        if [ ! -d "dist" ]; then
          echo "ERROR: dist folder not found after build!"
          exit 1
        fi
        # node_modulesが存在することを確認
        if [ ! -d "node_modules" ]; then
          echo "ERROR: node_modules folder not found!"
          exit 1
        fi
        echo "Building the Docker image..."
        docker build -t $REPOSITORY_URI:latest -t $REPOSITORY_URI:$IMAGE_TAG -f Dockerfile .
      - cd $CODEBUILD_SRC_DIR
  post_build:
    commands:
      - echo Build completed on `date`
      - echo Pushing the Docker images...
      - docker push $REPOSITORY_URI:latest
      - docker push $REPOSITORY_URI:$IMAGE_TAG
      - echo Writing image definitions file...
      - printf '[{"name":"console","imageUri":"%s"}]' $REPOSITORY_URI:latest > imagedefinitions.json
      - |
        if [ ! -f imagedefinitions.json ]; then
          echo "ERROR: Failed to create imagedefinitions.json"
          exit 1
        fi
        echo "imagedefinitions.json contents:"
        cat imagedefinitions.json
artifacts:
  files:
    - imagedefinitions.json
BUILDSPEC
  }

  logs_config {
    cloudwatch_logs {
      group_name  = "/aws/codebuild/${var.console_build.name}"
      stream_name = "build-log"
    }
  }

  tags = merge(
    var.tags,
    {
      Name = var.console_build.name
    }
  )
}

# Migration CodeBuild Project（データベースマイグレーション実行用）
resource "aws_codebuild_project" "migration" {
  name          = "${var.name_prefix}migration"
  description   = "データベースマイグレーション実行プロジェクト"
  build_timeout = 10
  service_role  = var.api_build.service_role_arn

  artifacts {
    type = "NO_ARTIFACTS"
  }

  environment {
    compute_type                = "BUILD_GENERAL1_SMALL"
    image                       = "aws/codebuild/standard:7.0"
    type                        = "LINUX_CONTAINER"
    image_pull_credentials_type = "CODEBUILD"
    privileged_mode             = true

    environment_variable {
      name  = "AWS_DEFAULT_REGION"
      value = data.aws_region.current.name
    }

    environment_variable {
      name  = "CLUSTER_NAME"
      value = var.cluster_name
    }

    environment_variable {
      name  = "TASK_DEFINITION"
      value = var.api_task_definition_family
    }

    environment_variable {
      name  = "SUBNET_ID"
      value = var.subnet_ids[0]
    }

    environment_variable {
      name  = "SECURITY_GROUP_ID"
      value = var.security_group_id
    }
  }

  source {
    type      = "NO_SOURCE"
    buildspec = <<-BUILDSPEC
      version: 0.2
      phases:
        build:
          commands:
            - echo "データベースマイグレーションを実行します..."
            - |
              # ECS Taskを実行してマイグレーションを実行
              TASK_ARN=$(aws ecs run-task \
                --cluster $CLUSTER_NAME \
                --task-definition $TASK_DEFINITION \
                --launch-type FARGATE \
                --network-configuration "awsvpcConfiguration={subnets=[\"$SUBNET_ID\"],securityGroups=[\"$SECURITY_GROUP_ID\"],assignPublicIp=ENABLED}" \
                --overrides '{"containerOverrides":[{"name":"api","command":["php","artisan","migrate","--force"]}]}' \
                --query 'tasks[0].taskArn' \
                --output text)
              
              if [ -z "$TASK_ARN" ] || [ "$TASK_ARN" == "None" ]; then
                echo "エラー: マイグレーションTaskの起動に失敗しました"
                exit 1
              fi
              
              echo "マイグレーションTask ARN: $TASK_ARN"
              
              # Taskの完了を待つ
              echo "マイグレーションの完了を待機中..."
              aws ecs wait tasks-stopped \
                --cluster $CLUSTER_NAME \
                --tasks $TASK_ARN
              
              # Taskの終了コードを確認
              EXIT_CODE=$(aws ecs describe-tasks \
                --cluster $CLUSTER_NAME \
                --tasks $TASK_ARN \
                --query 'tasks[0].containers[0].exitCode' \
                --output text)
              
              if [ "$EXIT_CODE" != "0" ] && [ "$EXIT_CODE" != "null" ]; then
                echo "エラー: マイグレーションが失敗しました (終了コード: $EXIT_CODE)"
                exit 1
              fi
              
              echo "マイグレーションが正常に完了しました"
    BUILDSPEC
  }

  logs_config {
    cloudwatch_logs {
      group_name  = "/aws/codebuild/${var.name_prefix}migration"
      stream_name = "build-log"
    }
  }

  tags = merge(
    var.tags,
    {
      Name = "${var.name_prefix}migration"
    }
  )
}

# GitHub認証用Source Credential（CodePipeline経由で実行される場合は不要）
# CodePipelineがソースを取得してCodeBuildに渡すため、CodeBuildのsource credentialは使用されない

# 現在のリージョン取得
data "aws_region" "current" {}

# 現在のAWSアカウントID取得
data "aws_caller_identity" "current" {}

