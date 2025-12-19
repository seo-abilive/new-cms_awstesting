<?php

namespace App\Domain;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

/**
 * AWSサービス
 *
 * @property S3Client $s3Client
 * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html
 */
class AwsService
{
    protected $s3Client;
    protected $awsUserId;
    protected $awsBucketSuffix;
    protected $awsBucketPrefix;

    private function __construct()
    {
        $this->s3Client = new S3Client(config('aws.s3'));
        $this->awsUserId = config('aws.aws_user_id');
        $this->awsBucketSuffix = config('aws.aws_bucket_suffix');
        $awsBucketPrefix = '';
        if (!empty($this->awsUserId)) {
            $awsBucketPrefix = $this->awsUserId . '-';
        } else {
            $awsBucketPrefix = 'user-bucket-';
        }
        $this->awsBucketPrefix = $awsBucketPrefix;
    }

    public static function getInstance(): self
    {
        return new self();
    }

    /**
     * バケットの生成
     *
     * @param string $username
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function generateBucket(string $username): array
    {
        if (!$this->validateUsername($username)) {
            throw new \InvalidArgumentException('無効なユーザー名です。');
        }

        // バケット名を生成
        $bucketName = $this->generateBucketName($this->awsBucketPrefix, $username, $this->awsUserId, $this->awsBucketSuffix);

        try {
            // バケットがすでに存在するか
            if ($this->s3Client->doesBucketExist($bucketName)) {
                throw new \Exception('バケット「' . $bucketName . '」がすでに存在します。');
            }

            // バケットの作成
            $this->s3Client->createBucket([
                'Bucket' => $bucketName,
                'CreateBucketConfiguration' => [
                    'LocationConstraint' => config('aws.s3.region'),
                ],
            ]);

            // Block Public Accessを無効化（すべてのチェックボックスをオフ）
            // これは画像を公開するために必要
            try {
                $this->s3Client->putPublicAccessBlock([
                    'Bucket' => $bucketName,
                    'PublicAccessBlockConfiguration' => [
                        'BlockPublicAcls' => false,
                        'IgnorePublicAcls' => false,
                        'BlockPublicPolicy' => false,
                        'RestrictPublicBuckets' => false,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Block Public Access無効化エラー', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            // バケットポリシー設定（パブリック読み取り許可）
            // これにより画像が公開URLでアクセス可能になる
            try {
                $bucketPolicy = [
                    'Version' => '2012-10-17',
                    'Statement' => [
                        [
                            'Sid' => 'PublicReadGetObject',
                            'Effect' => 'Allow',
                            'Principal' => '*',
                            'Action' => 's3:GetObject',
                            'Resource' => "arn:aws:s3:::{$bucketName}/*",
                        ],
                    ],
                ];

                $this->s3Client->putBucketPolicy([
                    'Bucket' => $bucketName,
                    'Policy' => json_encode($bucketPolicy, JSON_UNESCAPED_SLASHES),
                ]);
            } catch (\Exception $e) {
                Log::error('バケットポリシー設定エラー', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return ['result' => true, 'bucket_name' => $bucketName];
        } catch (\Exception $e) {
            Log::error('バケット生成エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * バケットの削除
     */
    public function deleteBucket(string $username): array
    {
        if (!$this->validateUsername($username)) {
            throw new \InvalidArgumentException('無効なユーザー名です。');
        }

        // バケット名を生成
        $bucketName = $this->generateBucketName($this->awsBucketPrefix, $username, $this->awsUserId, $this->awsBucketSuffix);
        try {
            // バケット内のオブジェクトを削除
            $objects = $this->s3Client->listObjects(['Bucket' => $bucketName]);
            if (isset($objects['Contents'])) {
                foreach ($objects['Contents'] as $object) {
                    $this->s3Client->deleteObject(['Bucket' => $bucketName, 'Key' => $object['Key']]);
                }
            }

            // バケットを削除
            $this->s3Client->deleteBucket(['Bucket' => $bucketName]);

            return ['result' => true];
        } catch (\Exception $e) {
            Log::error('バケット削除エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * ファイルのアップロード
     */
    public function uploadFile(string $username, string $key, string $body, string $contentType = 'application/octet-stream'): array
    {
        if (!$this->validateUsername($username)) {
            throw new \InvalidArgumentException('無効なユーザー名です。');
        }

        // バケット名を生成
        $bucketName = $this->generateBucketName($this->awsBucketPrefix, $username, $this->awsUserId, $this->awsBucketSuffix);

        try {
            // ファイルをアップロード
            $this->s3Client->putObject([
                'Bucket' => $bucketName,
                'Key' => $key,
                'Body' => $body,
                'ContentType' => $contentType,
            ]);
        } catch (\Exception $e) {
            Log::error('ファイルアップロードエラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        return ['result' => true];
    }

    /**
     * ファイルのURLを取得
     */
    public function getFileUrl(string $username, string $key): string
    {
        if (!$this->validateUsername($username)) {
            throw new \InvalidArgumentException('無効なユーザー名です。');
        }

        $bucketName = $this->generateBucketName($this->awsBucketPrefix, $username, $this->awsUserId, $this->awsBucketSuffix);
        return $this->s3Client->getObjectUrl($bucketName, $key);
    }

    public function deleteFile(string $username, string $key): array
    {
        if (!$this->validateUsername($username)) {
            throw new \InvalidArgumentException('無効なユーザー名です。');
        }

        $bucketName = $this->generateBucketName($this->awsBucketPrefix, $username, $this->awsUserId, $this->awsBucketSuffix);

        try {
            $this->s3Client->deleteObject(['Bucket' => $bucketName, 'Key' => $key]);
        } catch (\Exception $e) {
            Log::error('ファイル削除エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        return ['result' => true];
    }

    /**
     * ユーザー名のバリデーション
     */
    public function validateUsername(string $username): bool
    {
        // 英数字と特殊記号（_、-、.）のみ許可
        return preg_match('/^[a-z0-9._-]+$/', $username) && \strlen($username) > 0 && mb_strlen($username) === \strlen($username);
    }

    /**
     * バケット名の生成
     */
    public function generateBucketName(string $prefix, string $username = '', string $userId = '', string $suffix = ''): string
    {
        // ユーザ名を小文字に
        $sanitized = strtolower($username);
        // アンダースコアとドットをハイフンに変換
        $sanitized = str_replace(['_', '.'], '-', $sanitized);
        // 連続するハイフンを1つに統合
        $sanitized = preg_replace('/-+/', '-', $sanitized);
        // 先頭と末尾のハイフンを削除
        $sanitized = trim($sanitized, '-');

        // バケット名を生成: {userId}-{username}-{suffix}
        $bucketName = $prefix . $sanitized;
        if (!empty($suffix)) {
            $bucketName .= '-' . $suffix;
        }

        return $bucketName;
    }
}
