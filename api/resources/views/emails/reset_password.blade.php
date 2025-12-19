<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>パスワード再設定のご案内</title>
    <style>
        body { margin: 0; padding: 0; background: #ffffff; color: #111827; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .container { max-width: 560px; margin: 24px auto; padding: 0 16px; }
        .card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 12px; }
        .text { font-size: 14px; line-height: 1.7; margin: 0 0 16px; }
        .btn { display: inline-block; background: #2563eb; color: #ffffff; text-decoration: none; padding: 10px 16px; border-radius: 6px; font-size: 14px; }
        .muted { color: #6b7280; font-size: 12px; }
        .url { word-break: break-all; font-size: 12px; }
    </style>
    <!-- 背景装飾なしのシンプルテンプレート -->

</head>
<body>
<div class="container">
    <div class="card">
        <p class="title">パスワード再設定のご案内</p>
        <p class="text">以下のボタンをクリックして、パスワードの再設定を行ってください。</p>
        <p style="margin: 16px 0;">
            <a class="btn" href="{{ $resetUrl }}" target="_blank" rel="noopener">パスワードを再設定</a>
        </p>
        <p class="text muted">リンクが開けない場合は、以下のURLをブラウザにコピー＆ペーストしてください。</p>
        <p class="url">{{ $resetUrl }}</p>
        <hr style="border:none; border-top:1px solid #e5e7eb; margin: 20px 0;" />
        <p class="text muted">このリンクの有効期限は約60分です。期限が切れた場合は、もう一度お手続きをお願いいたします。</p>
        <p class="text muted">本メールにお心当たりがない場合は、このメールは破棄してください。</p>
    </div>
</div>
</body>
</html>


