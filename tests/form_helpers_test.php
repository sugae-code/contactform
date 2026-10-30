<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/form_helpers.php';

function assert_same(mixed $actual, mixed $expected, string $label): void
{
    if ($actual !== $expected) {
        throw new RuntimeException($label . ': got ' . var_export($actual, true));
    }
}
function expect_rejected(array $post, string $label): void
{
    try {
        validate_contact($post);
        throw new RuntimeException($label . ': invalid data was accepted');
    } catch (InvalidArgumentException $expected) {
        // Correctly rejected.
    }
}

$valid = ['name' => '山田 太郎', 'mail' => 'taro@example.com', 'age' => '24', 'comments' => "お問い合わせです。\n次の行"];
assert_same(validate_contact($valid), $valid, 'valid submission');
assert_same(escape_html('<script>"\'&</script>'), '&lt;script&gt;&quot;&#039;&amp;&lt;/script&gt;', 'HTML text escaping');
expect_rejected(array_diff_key($valid, ['name' => true]), 'missing name');
expect_rejected(array_replace($valid, ['name' => ['not a string']]), 'array input');
expect_rejected(array_replace($valid, ['mail' => 'not-an-email']), 'email validation');
expect_rejected(array_replace($valid, ['age' => '17']), 'minimum age');
expect_rejected(array_replace($valid, ['age' => '66']), 'maximum age');
expect_rejected(array_replace($valid, ['age' => '24; DROP TABLE contactform']), 'age injection');
expect_rejected(array_replace($valid, ['comments' => '']), 'empty comments');
expect_rejected(array_replace($valid, ['comments' => str_repeat('A', 5001)]), 'long comments');

// Render the actual confirmation page with attacker-controlled input.
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array_replace($valid, ['name' => 'Test" onfocus="alert(1)', 'comments' => '<img src=x onerror=alert(1)>']);
ob_start();
require dirname(__DIR__) . '/mail_confirm.php';
$html = ob_get_clean();
if (!str_contains($html, 'Test&quot; onfocus=&quot;alert(1)') ||
    !str_contains($html, '&lt;img src=x onerror=alert(1)&gt;') ||
    str_contains($html, '<img src=x onerror=alert(1)>')) {
    throw new RuntimeException('Confirmation HTML is not safely escaped.');
}

fwrite(STDOUT, "PASS: 11 validation/escaping checks + rendered confirmation XSS regression\n");
