<?php
declare(strict_types=1);

/** Escape untrusted text before placing it in an HTML text node or attribute. */
function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Validate and normalize an incoming form submission.
 * Both the confirmation page and database endpoint must call this function:
 * HTML form validation and hidden inputs are not a security boundary.
 *
 * @return array{name: string, mail: string, age: string, comments: string}
 */
function validate_contact(array $post): array
{
    $result = [];
    foreach (['name', 'mail', 'age', 'comments'] as $key) {
        if (!isset($post[$key]) || !is_string($post[$key])) {
            throw new InvalidArgumentException('Missing or malformed field: ' . $key);
        }
        $result[$key] = trim($post[$key]);
        if (!preg_match('//u', $result[$key])) {
            throw new InvalidArgumentException('Invalid UTF-8 in field: ' . $key);
        }
    }

    if ($result['name'] === '' || strlen($result['name']) > 200) {
        throw new InvalidArgumentException('Name must be 1–200 bytes.');
    }
    if (strlen($result['mail']) > 254 || filter_var($result['mail'], FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException('Invalid email address.');
    }
    if (!ctype_digit($result['age']) || (int) $result['age'] < 18 || (int) $result['age'] > 65) {
        throw new InvalidArgumentException('Age must be between 18 and 65.');
    }
    if ($result['comments'] === '' || strlen($result['comments']) > 5000) {
        throw new InvalidArgumentException('Comment must be 1–5000 bytes.');
    }

    return $result;
}
