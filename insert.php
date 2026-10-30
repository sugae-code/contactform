<?php
declare(strict_types=1);
require_once __DIR__ . '/form_helpers.php';

header('Content-Type: text/html; charset=UTF-8');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('POSTで送信してください。');
}
try {
    $contact = validate_contact($_POST);
} catch (InvalidArgumentException $exception) {
    http_response_code(422);
    exit('入力内容に誤りがあります。前のページに戻って確認してください。');
}

try {
    $dsn = getenv('CONTACTFORM_DSN');
    if ($dsn === false || $dsn === '') {
        throw new RuntimeException('CONTACTFORM_DSN is not set.');
    }
    $pdo = new PDO(
        $dsn,
        getenv('CONTACTFORM_DB_USER') ?: '',
        getenv('CONTACTFORM_DB_PASSWORD') ?: '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_EMULATE_PREPARES => false]
    );
    $stmt = $pdo->prepare(
        'INSERT INTO contactform (name, mail, age, comments) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([
        $contact['name'], $contact['mail'], (int) $contact['age'], $contact['comments']
    ]);
} catch (Throwable $exception) {
    error_log('Contact form insert failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('登録できませんでした。時間をおいて再度お試しください。');
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <h1>お問い合わせフォーム</h1>
    <div class="confirm"><p>お問い合わせありがとうございました。<br>3営業日以内に担当者よりご連絡差し上げます。</p></div>
</body>
</html>
