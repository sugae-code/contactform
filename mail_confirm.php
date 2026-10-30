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
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>お問い合わせ内容確認</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
</head>
<body>
    <h1>お問い合わせ内容確認</h1>
    <div class="confirm">
        <p>お問い合わせ内容はこちらでよろしいでしょうか？<br>よろしければ「登録する」ボタンを押してください。</p>
        <p>名前<br><?= escape_html($contact['name']) ?></p>
        <p>メールアドレス<br><?= escape_html($contact['mail']) ?></p>
        <p>年齢<br><?= escape_html($contact['age']) ?>歳</p>
        <p>コメント<br><?= nl2br(escape_html($contact['comments']), false) ?></p>
        <div class="bottons">
            <form action="index.html" method="get">
                <input type="submit" class="button1" value="戻って修正する">
            </form>
            <form action="insert.php" method="post">
                <input type="submit" class="button2" value="登録する">
                <input type="hidden" name="name" value="<?= escape_html($contact['name']) ?>">
                <input type="hidden" name="mail" value="<?= escape_html($contact['mail']) ?>">
                <input type="hidden" name="age" value="<?= escape_html($contact['age']) ?>">
                <input type="hidden" name="comments" value="<?= escape_html($contact['comments']) ?>">
            </form>
        </div>
    </div>
</body>
</html>
