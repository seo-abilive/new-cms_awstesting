<?php
// URLルーティング処理（PHP内蔵サーバー対応）
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$requestPath = trim($requestPath, '/');

// バケット名パターンに一致する場合はupload.phpにルーティング
// ユーザー名または完全なバケット名の両方に対応
if (!empty($requestPath) && preg_match('/^[a-zA-Z0-9._-]+$/', $requestPath)) {
    // ファイルが存在しない場合のみupload.phpにルーティング
    if (!file_exists(__DIR__ . '/' . $requestPath)) {
        // 完全なバケット名の場合はユーザー名を抽出
        require_once __DIR__ . '/config.php';

        // バケット名からユーザー名を抽出
        // 形式: {userId}-{username}-{suffix}
        $username = '';
        if (strpos($requestPath, $bucketPrefix) === 0) {
            // プレフィックス以降の部分からユーザー名を抽出
            $userPart = substr($requestPath, strlen($bucketPrefix));
            // サフィックスを除去
            if (!empty($bucketSuffix)) {
                $suffixPattern = '-' . $bucketSuffix;
                if (substr($userPart, -strlen($suffixPattern)) === $suffixPattern) {
                    $userPart = substr($userPart, 0, -strlen($suffixPattern));
                }
            }
            $username = $userPart;
        } else {
            // ユーザー名としてそのまま使用
            $username = $requestPath;
        }

        // ユーザー名이 유효한 경우에만 upload.php로 라우팅
        if (!empty($username) && validateUsername($username)) {
            $_GET['username'] = $username;
            require_once 'upload.php';
            exit;
        }
    }
}

session_start();
require_once 'config.php';

$message = '';
$messageType = '';
$buckets = [];

// 作成済みバケット一覧を取得
try {
    $result = $s3Client->listBuckets();
    $allBuckets = $result['Buckets'] ?? [];

    // 現在のプロジェクトで作成したバケットのみフィルタリング（プレフィックスで判定）
    // 形式: {userId}-{username}-{suffix}
    foreach ($allBuckets as $bucket) {
        $bucketName = $bucket['Name'];
        if (strpos($bucketName, $bucketPrefix) === 0) {
            // プレフィックス以降の部分からユーザー名を抽出
            $userPart = substr($bucketName, strlen($bucketPrefix));
            // サフィックスを除去
            if (!empty($bucketSuffix)) {
                $suffixPattern = '-' . $bucketSuffix;
                if (substr($userPart, -strlen($suffixPattern)) === $suffixPattern) {
                    $userPart = substr($userPart, 0, -strlen($suffixPattern));
                }
            }
            $buckets[] = [
                'name' => $bucketName,
                'username' => $userPart,
                'created' => $bucket['CreationDate'] ?? null,
            ];
        }
    }
} catch (Exception $e) {
    // バケット一覧取得エラーは無視（表示しない）
}

// バケット削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_bucket'])) {
    $bucketNameToDelete = $_POST['delete_bucket'] ?? '';

    if (!empty($bucketNameToDelete)) {
        try {
            // バケット内のすべてのオブジェクトを削除
            $objects = $s3Client->listObjectsV2(['Bucket' => $bucketNameToDelete]);
            if (isset($objects['Contents'])) {
                foreach ($objects['Contents'] as $object) {
                    $s3Client->deleteObject([
                        'Bucket' => $bucketNameToDelete,
                        'Key' => $object['Key'],
                    ]);
                }
            }

            // バケットを削除
            $s3Client->deleteBucket(['Bucket' => $bucketNameToDelete]);

            $_SESSION['delete_bucket_success'] = "バケット '{$bucketNameToDelete}'が正常に削除されました。";
            header('Location: /');
            exit;
        } catch (Exception $e) {
            $_SESSION['delete_bucket_error'] = 'バケット削除中にエラーが発生しました: ' . $e->getMessage();
            header('Location: /');
            exit;
        }
    }
}

// セッションからメッセージを取得
if (isset($_SESSION['delete_bucket_success'])) {
    $message = $_SESSION['delete_bucket_success'];
    $messageType = 'success';
    unset($_SESSION['delete_bucket_success']);
} elseif (isset($_SESSION['delete_bucket_error'])) {
    $message = $_SESSION['delete_bucket_error'];
    $messageType = 'error';
    unset($_SESSION['delete_bucket_error']);
}

// バケット作成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);

    // ユーザー名検証（英数字と特殊記号のみ許可）
    if (!validateUsername($username)) {
        $message = 'バケット名は英数字と特殊記号（_、-、.）のみ使用できます。日本語・中国語・韓国語などは使用できません。';
        $messageType = 'error';
    } else {
        // S3バケット名を生成（S3の命名規則に準拠）
        $bucketName = generateBucketName($bucketPrefix, $username, $awsUserId, $bucketSuffix);

        try {
            // バケットが既に存在するか確認
            if ($s3Client->doesBucketExist($bucketName)) {
                $message = "バケット '{$bucketName}'は既に存在します。";
                $messageType = 'warning';
            } else {
                // バケット作成
                $s3Client->createBucket([
                    'Bucket' => $bucketName,
                    'CreateBucketConfiguration' => [
                        'LocationConstraint' => $_ENV['AWS_REGION'] ?? 'ap-northeast-2',
                    ],
                ]);

                // Block Public Accessを無効化（すべてのチェックボックスをオフ）
                // これは画像を公開するために必要
                $publicAccessBlockError = '';
                try {
                    $s3Client->putPublicAccessBlock([
                        'Bucket' => $bucketName,
                        'PublicAccessBlockConfiguration' => [
                            'BlockPublicAcls' => false,
                            'IgnorePublicAcls' => false,
                            'BlockPublicPolicy' => false,
                            'RestrictPublicBuckets' => false,
                        ],
                    ]);
                } catch (Exception $blockException) {
                    // Block Public Access設定が失敗した場合はエラーメッセージに追加
                    error_log("Block Public Access設定エラー ({$bucketName}): " . $blockException->getMessage());
                    $publicAccessBlockError = " (警告: Block Public Access設定に失敗しました)";
                }

                // バケットポリシー設定（パブリック読み取り許可）
                // これにより画像が公開URLでアクセス可能になる
                $bucketPolicyError = '';
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

                    $s3Client->putBucketPolicy([
                        'Bucket' => $bucketName,
                        'Policy' => json_encode($bucketPolicy, JSON_UNESCAPED_SLASHES),
                    ]);
                } catch (Exception $policyException) {
                    // ポリシー設定が失敗した場合はエラーメッセージに追加
                    error_log("バケットポリシー設定エラー ({$bucketName}): " . $policyException->getMessage());
                    $bucketPolicyError = " (警告: バケットポリシー設定に失敗しました)";
                }

                $message = "バケット '{$bucketName}'が正常に作成されました！" . $publicAccessBlockError . $bucketPolicyError;
                $messageType = 'success';

                // バケット一覧を再取得
                try {
                    $result = $s3Client->listBuckets();
                    $allBuckets = $result['Buckets'] ?? [];
                    $buckets = [];

                    foreach ($allBuckets as $bucket) {
                        $bucketNameItem = $bucket['Name'];
                        if (strpos($bucketNameItem, $bucketPrefix) === 0) {
                            // プレフィックス以降の部分からユーザー名を抽出
                            $userPart = substr($bucketNameItem, strlen($bucketPrefix));
                            // サフィックスを除去
                            if (!empty($bucketSuffix)) {
                                $suffixPattern = '-' . $bucketSuffix;
                                if (substr($userPart, -strlen($suffixPattern)) === $suffixPattern) {
                                    $userPart = substr($userPart, 0, -strlen($suffixPattern));
                                }
                            }
                            $buckets[] = [
                                'name' => $bucketNameItem,
                                'username' => $userPart,
                                'created' => $bucket['CreationDate'] ?? null,
                            ];
                        }
                    }
                } catch (Exception $e) {
                    // エラーは無視
                }
            }
        } catch (Exception $e) {
            $message = 'バケット作成中にエラーが発生しました: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S3バケット作成</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #F5F1E8 0%, #E8DCC8 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .section {
            background: #FAF8F3;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(166, 139, 123, 0.15);
            border: 1px solid rgba(212, 197, 185, 0.3);
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .bucket-name-input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .bucket-name-prefix,
        .bucket-name-suffix {
            color: #8B7355;
            font-weight: 600;
            font-size: 16px;
            white-space: nowrap;
        }

        input[type="text"] {
            flex: 1;
            min-width: 200px;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #C9A882;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #C9A882 0%, #B8956A 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(201, 168, 130, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .message a {
            color: #A68B7B;
            text-decoration: none;
            font-weight: 600;
        }

        .message a:hover {
            text-decoration: underline;
        }

        .bucket-list h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
            text-align: center;
        }

        .bucket-item-wrapper {
            position: relative;
            margin-bottom: 10px;
        }

        .bucket-item {
            background: #F5F1E8;
            border: 2px solid #E8DCC8;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            color: #5A4A3A;
        }

        .bucket-item:hover {
            border-color: #C9A882;
            background: #F0E8DD;
            transform: translateX(5px);
        }

        .bucket-item-delete {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            z-index: 10;
        }

        .bucket-item-delete:hover {
            background: rgba(220, 53, 69, 1);
            transform: scale(1.1);
        }

        .bucket-item-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .bucket-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #C9A882 0%, #B8956A 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .bucket-icon:hover {
            transform: scale(1.1);
        }

        .bucket-item-name {
            font-weight: 600;
            color: #5A4A3A;
            font-size: 14px;
            word-break: break-all;
            flex: 1;
        }

        .bucket-preview {
            margin-top: 10px;
            padding: 10px;
            background: #F5F1E8;
            border-radius: 6px;
            font-size: 13px;
            color: #8B7355;
            border: 1px solid #E8DCC8;
        }

        .bucket-preview-label {
            font-size: 11px;
            color: #999;
            margin-bottom: 5px;
        }

        .no-buckets {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
        }

        .aws-console-section {
            text-align: center;
        }

        .aws-console-link {
            display: inline-block;
            padding: 12px 20px;
            background: linear-gradient(135deg, #C9A882 0%, #B8956A 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .aws-console-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(201, 168, 130, 0.4);
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- セクション1: S3バケット作成 -->
        <div class="section">
            <h1>🚀 S3バケット作成</h1>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">バケット名（英数字と特殊記号のみ）</label>
                    <div class="bucket-name-input-wrapper">
                        <span class="bucket-name-prefix"><?php echo htmlspecialchars($bucketPrefix); ?></span>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            required
                            pattern="[a-zA-Z0-9._-]+"
                            placeholder="例: john123, user_name, test-user"
                            autocomplete="off">
                        <?php if (!empty($bucketSuffix)): ?>
                            <span class="bucket-name-suffix">-<?php echo htmlspecialchars($bucketSuffix); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="bucket-preview" id="bucketPreview" style="display: none;">
                        <div class="bucket-preview-label">作成されるバケット名:</div>
                        <div id="bucketPreviewText"></div>
                    </div>
                </div>
                <button type="submit">バケット作成</button>
            </form>
        </div>

        <!-- セクション2: 作成済みバケット一覧 -->
        <div class="section">
            <h2>📦 作成済みバケット一覧</h2>
            <?php if (!empty($buckets)): ?>
                <?php foreach ($buckets as $bucket): ?>
                    <?php
                    $bucketLink = htmlspecialchars($bucket['name']);
                    $bucketLink = str_replace($bucketPrefix, '', $bucketLink);
                    $bucketLink = str_replace($bucketSuffix, '', $bucketLink);
                    $bucketLink = trim($bucketLink, '-');
                    ?>
                    <div class="bucket-item-wrapper">
                        <form method="POST" action="" style="display: inline;" onsubmit="return confirm('このバケットとすべての画像を削除しますか？この操作は元に戻せません。');">
                            <input type="hidden" name="delete_bucket" value="<?php echo htmlspecialchars($bucket['name']); ?>">
                            <button type="submit" class="bucket-item-delete" title="削除">×</button>
                        </form>
                        <div class="bucket-item">
                            <div class="bucket-item-content">
                                <a href="/<?php echo $bucketLink; ?>/" class="bucket-icon" title="バケットを開く">
                                    🪣
                                </a>
                                <a href="/<?php echo $bucketLink; ?>/" class="bucket-item-name" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($bucket['name']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-buckets">バケットがまだ作成されていません。</div>
            <?php endif; ?>
        </div>

        <!-- セクション3: AWS コンソール を開く -->
        <div class="section aws-console-section">
            <?php
            // AWSリージョンからS3コンソールURLを生成（東京リージョン: ap-northeast-1）
            $awsRegion = $_ENV['AWS_REGION'] ?? 'ap-northeast-2';
            // 東京リージョンに固定
            $s3ConsoleUrl = 'https://console.aws.amazon.com/s3/home?region=ap-northeast-1';
            ?>
            <a href="<?php echo htmlspecialchars($s3ConsoleUrl); ?>" target="_blank" class="aws-console-link">
                🔗 AWS コンソール を開く
            </a>
        </div>
    </div>

    <script>
        // バケット名プレビュー機能
        const usernameInput = document.getElementById('username');
        const bucketPreview = document.getElementById('bucketPreview');
        const bucketPreviewText = document.getElementById('bucketPreviewText');

        const bucketPrefix = '<?php echo htmlspecialchars($bucketPrefix); ?>';
        const bucketSuffix = '<?php echo htmlspecialchars($bucketSuffix); ?>';

        usernameInput.addEventListener('input', function() {
            const username = this.value.trim();
            if (username) {
                // ユーザー名を小文字に変換し、S3で許可されない文字を変換
                let sanitized = username.toLowerCase();
                sanitized = sanitized.replace(/[_.]/g, '-');
                sanitized = sanitized.replace(/-+/g, '-');
                sanitized = sanitized.replace(/^-|-$/g, '');

                // バケット名を生成（プレフィックス + ユーザー名 + サフィックス）
                let bucketName = bucketPrefix + sanitized;
                if (bucketSuffix) {
                    bucketName += '-' + bucketSuffix;
                }

                bucketPreviewText.textContent = bucketName;
                bucketPreview.style.display = 'block';
            } else {
                bucketPreview.style.display = 'none';
            }
        });
    </script>
</body>

</html>