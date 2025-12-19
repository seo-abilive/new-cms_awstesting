<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン認証コード</title>
    <style>
        body { margin: 0; padding: 0; background: #ffffff; color: #111827; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .container { max-width: 560px; margin: 24px auto; padding: 0 16px; }
        .card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 12px; }
        .text { font-size: 14px; line-height: 1.7; margin: 0 0 16px; }
        .code-box { background: #f3f4f6; border: 2px solid #d1d5db; border-radius: 8px; padding: 16px; text-align: center; margin: 20px 0; }
        .code { font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #111827; font-family: 'Courier New', monospace; }
        .muted { color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <p class="title">ログイン認証コード</p>
        <p class="text">こんにちは、{{ $userName }}さん</p>
        <p class="text">ログイン認証コードをお送りします。以下のコードをログイン画面に入力してください。</p>
        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>
        <p class="text">このコードは30分間有効です。</p>
        <hr style="border:none; border-top:1px solid #e5e7eb; margin: 20px 0;" />
        <p class="text muted">本メールにお心当たりがない場合は、このメールは破棄してください。</p>
    </div>
</div>
</body>
</html>

