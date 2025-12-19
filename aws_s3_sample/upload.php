<?php
session_start();
require_once 'config.php';

$username = isset($_GET['username']) ? trim($_GET['username']) : '';
$message = '';
$messageType = '';

// ユーザー名検証
if (empty($username) || !validateUsername($username)) {
    header('Location: /');
    exit;
}

// S3バケット名を生成（S3の命名規則に準拠）
$bucketName = generateBucketName($bucketPrefix, $username, $awsUserId, $bucketSuffix);

// バケット存在確認
try {
    // doesBucketExistはリージョンを確認する必要があるため、headBucketを使用
    try {
        $s3Client->headBucket(['Bucket' => $bucketName]);
    } catch (Aws\S3\Exception\S3Exception $e) {
        if ($e->getStatusCode() === 404) {
            $message = "バケット '{$bucketName}'が存在しません。まずバケットを作成してください。";
            $messageType = 'error';
        } else {
            throw $e;
        }
    }
} catch (Exception $e) {
    $message = 'バケット確認中にエラーが発生しました: ' . $e->getMessage() . ' (バケット名: ' . $bucketName . ')';
    $messageType = 'error';
}

// 画像削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image']) && empty($message)) {
    $imageKey = $_POST['delete_image'] ?? '';

    if (!empty($imageKey)) {
        try {
            // S3から画像を削除
            $s3Client->deleteObject([
                'Bucket' => $bucketName,
                'Key' => $imageKey,
            ]);

            $_SESSION['delete_success'] = "画像が正常に削除されました: {$imageKey}";
            header('Location: /' . urlencode($username) . '/');
            exit;
        } catch (Exception $e) {
            $_SESSION['delete_error'] = '画像削除中にエラーが発生しました: ' . $e->getMessage();
            header('Location: /' . urlencode($username) . '/');
            exit;
        }
    }
}

// 画像アップロード処理（複数ファイル対応）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && empty($message)) {
    $files = $_FILES['image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $uploadedCount = 0;
    $errorMessages = [];
    $successPaths = [];

    // 複数ファイルを処理
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;

    for ($i = 0; $i < $fileCount; $i++) {
        // 単一ファイルの場合と複数ファイルの場合を処理
        if (is_array($files['name'])) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];
        } else {
            $file = $files;
            $i = $fileCount; // ループを1回だけ実行
        }

        // ファイルアップロード検証
        if ($file['error'] === UPLOAD_ERR_OK) {
            $fileType = mime_content_type($file['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $errorMessages[] = $file['name'] . ': 画像ファイルのみアップロードできます。（JPEG、PNG、GIF、WebP）';
                continue;
            }

            // 日付フォルダパス生成（yyyy/mm/dd）
            $datePath = date('Y/m/d');
            $fileName = basename($file['name']);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueFileName = uniqid() . '_' . time() . '_' . $i . '.' . $fileExtension;
            $s3Key = $datePath . '/' . $uniqueFileName;

            try {
                // S3にファイルアップロード（ACLは使用しない - バケットがACLを許可していない場合があるため）
                $result = $s3Client->putObject([
                    'Bucket' => $bucketName,
                    'Key' => $s3Key,
                    'Body' => fopen($file['tmp_name'], 'rb'),
                    'ContentType' => $fileType,
                ]);

                $uploadedCount++;
                $successPaths[] = $s3Key;
            } catch (Exception $e) {
                $errorMessages[] = $file['name'] . ': ' . $e->getMessage();
            }
        } else {
            $errorMessages[] = $file['name'] . ': ファイルアップロード中にエラーが発生しました。';
        }
    }

    // 結果メッセージを設定
    if ($uploadedCount > 0) {
        $message = "{$uploadedCount}件の画像が正常にアップロードされました！";
        if (!empty($errorMessages)) {
            $message .= " (" . count($errorMessages) . "件のエラー)";
        }
        $_SESSION['upload_success'] = $message;
    } else {
        $_SESSION['upload_error'] = !empty($errorMessages) ? implode('<br>', $errorMessages) : 'ファイルアップロード中にエラーが発生しました。';
    }

    // POST-Redirect-GETパターンでリロード時の再アップロードを防止
    header('Location: /' . urlencode($username) . '/');
    exit;
}

// セッションからメッセージを取得
if (isset($_SESSION['upload_success'])) {
    $message = $_SESSION['upload_success'];
    $messageType = 'success';
    unset($_SESSION['upload_success']);
} elseif (isset($_SESSION['upload_error'])) {
    $message = $_SESSION['upload_error'];
    $messageType = 'error';
    unset($_SESSION['upload_error']);
} elseif (isset($_SESSION['delete_success'])) {
    $message = $_SESSION['delete_success'];
    $messageType = 'success';
    unset($_SESSION['delete_success']);
} elseif (isset($_SESSION['delete_error'])) {
    $message = $_SESSION['delete_error'];
    $messageType = 'error';
    unset($_SESSION['delete_error']);
}

// アップロード済み画像一覧を取得
$uploadedImages = [];
if (empty($message) || $messageType !== 'error') {
    try {
        $result = $s3Client->listObjectsV2([
            'Bucket' => $bucketName,
        ]);

        if (isset($result['Contents'])) {
            foreach ($result['Contents'] as $object) {
                $key = $object['Key'];
                // 画像ファイルのみ表示
                if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $key)) {
                    // S3オブジェクトの通常URLを生成
                    $region = $_ENV['AWS_REGION'] ?? 'ap-northeast-2';
                    $imageUrl = "https://{$bucketName}.s3.{$region}.amazonaws.com/" . urlencode($key);

                    $uploadedImages[] = [
                        'key' => $key,
                        'url' => $imageUrl,
                        'size' => $object['Size'] ?? 0,
                        'lastModified' => isset($object['LastModified']) ? $object['LastModified'] : null,
                    ];
                }
            }
            // 日付の新しい順にソート
            usort($uploadedImages, function ($a, $b) {
                if ($a['lastModified'] && $b['lastModified']) {
                    return $b['lastModified']->getTimestamp() - $a['lastModified']->getTimestamp();
                }
                return 0;
            });
        }
    } catch (Exception $e) {
        // エラーは無視（画像一覧が取得できない場合）
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>画像アップロード - <?php echo htmlspecialchars($username); ?></title>
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
            background: #FAF8F3;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(166, 139, 123, 0.15);
            max-width: 600px;
            width: 100%;
            border: 1px solid rgba(212, 197, 185, 0.3);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }

        .username {
            text-align: center;
            color: #A68B7B;
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 18px;
        }

        .upload-area {
            border: 3px dashed #D4C5B9;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            margin-bottom: 20px;
            background: #F5F1E8;
        }

        .upload-area:hover {
            border-color: #C9A882;
            background: #F0E8DD;
        }

        .upload-area.dragover {
            border-color: #C9A882;
            background: #E8DCC8;
        }

        .upload-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .upload-text {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .upload-hint {
            color: #999;
            font-size: 14px;
        }

        input[type="file"] {
            display: none;
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

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #A68B7B;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .preview {
            margin-top: 20px;
            text-align: center;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .preview img {
            max-width: 150px;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .images-list {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }

        .images-list h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 20px;
            text-align: center;
        }

        .images-list-info {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .images-list-bucket {
            color: #A68B7B;
            font-weight: 600;
        }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .image-item {
            position: relative;
            border: 2px solid #E8DCC8;
            border-radius: 8px;
            overflow: hidden;
            background: #F5F1E8;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .image-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(166, 139, 123, 0.2);
        }

        .image-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }

        .image-item-info {
            padding: 8px;
            font-size: 11px;
            color: #666;
            word-break: break-all;
        }

        .image-item-delete {
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

        .image-item-delete:hover {
            background: rgba(220, 53, 69, 1);
            transform: scale(1.1);
        }

        .no-images {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📸 画像アップロード</h1>
        <div class="username">バケット名: <?php echo htmlspecialchars($username); ?></div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($message) || $messageType === 'success'): ?>
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📁</div>
                    <div class="upload-text">画像をドラッグするかクリックして選択（複数選択可能）</div>
                    <div class="upload-hint">JPEG、PNG、GIF、WebP対応</div>
                    <input type="file" id="imageInput" name="image[]" accept="image/*" multiple required>
                </div>

                <div class="preview" id="preview"></div>

                <button type="submit" id="submitBtn">アップロード</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($uploadedImages)): ?>
            <div class="images-list">
                <h2>📷 アップロード済み画像</h2>
                <div class="images-list-info">
                    <span class="images-list-bucket">バケット: <?php echo htmlspecialchars($bucketName); ?></span>
                </div>
                <div class="images-grid">
                    <?php foreach ($uploadedImages as $image): ?>
                        <div class="image-item">
                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('この画像を削除しますか？');">
                                <input type="hidden" name="delete_image" value="<?php echo htmlspecialchars($image['key']); ?>">
                                <button type="submit" class="image-item-delete" title="削除">×</button>
                            </form>
                            <img src="<?php echo htmlspecialchars($image['url']); ?>" alt="<?php echo htmlspecialchars($image['key']); ?>" loading="lazy">
                            <div class="image-item-info">
                                <?php echo htmlspecialchars($image['key']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="images-list">
                <h2>📷 アップロード済み画像</h2>
                <div class="images-list-info">
                    <span class="images-list-bucket">バケット: <?php echo htmlspecialchars($bucketName); ?></span>
                </div>
                <div class="no-images">まだ画像がアップロードされていません。</div>
            </div>
        <?php endif; ?>

        <a href="/" class="back-link">← ホームに戻る</a>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const imageInput = document.getElementById('imageInput');
        const preview = document.getElementById('preview');
        const uploadForm = document.getElementById('uploadForm');

        // クリックでファイル選択
        uploadArea.addEventListener('click', () => {
            imageInput.click();
        });

        // ドラッグ&ドロップ
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                showPreview(files);
            }
        });

        // ファイル選択時のプレビュー
        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                showPreview(e.target.files);
            }
        });

        function showPreview(files) {
            preview.innerHTML = '';
            const fileArray = Array.from(files);

            fileArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = `プレビュー ${index + 1}`;
                    img.style.cssText = 'max-width: 100%; max-height: 200px; border-radius: 8px; margin: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });

            if (fileArray.length > 0) {
                const countText = document.createElement('div');
                countText.textContent = `${fileArray.length}件の画像が選択されました`;
                countText.style.cssText = 'text-align: center; color: #666; margin-top: 10px; font-size: 14px;';
                preview.appendChild(countText);
            }
        }
    </script>
</body>

</html>