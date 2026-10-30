# contactform

PHPとMySQLで作るシンプルなお問い合わせフォームのサンプルです。

## 動作の流れ

`index.html` → `mail_confirm.php`（確認・HTMLエスケープ）→ `insert.php`（再検証・DB保存）

## セットアップ

1. PHP 8.0以降、`pdo_mysql` 拡張、MySQLを用意してください。
2. 例として以下のテーブルを作成してください（すでにテーブルがある場合は既存定義と照合してください）。

   ```sql
   CREATE TABLE contactform (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(200) NOT NULL,
       mail VARCHAR(254) NOT NULL,
       age TINYINT UNSIGNED NOT NULL,
       comments TEXT NOT NULL
   ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Webサーバーの環境変数として、以下を設定してください。**DBの認証情報をGitにコミットしないでください。**

   ```text
   CONTACTFORM_DSN=mysql:host=127.0.0.1;dbname=lesson01;charset=utf8mb4
   CONTACTFORM_DB_USER=contactform_user
   CONTACTFORM_DB_PASSWORD=（DB専用ユーザーのパスワード）
   ```

4. `index.html` をPHP対応のWebサーバーから開いてください。

## セキュリティ上の注意

- 確認画面の本文とhidden属性値は `htmlspecialchars` でエスケープします。
- 入力は確認画面とDB保存時に検証します。直接 `insert.php` にPOSTしても検証されます。
- DB保存はプレースホルダーを使ったプリペアドステートメントで行います。
- DB接続エラーの詳細はブラウザーに表示しません。ログの取り扱いに注意してください。
- **公開サイトへの本番導入前**に、CSRF対策、レート制限・スパム対策、個人情報の保存/削除ポリシー、HTTPSなどを別途実装してください。このサンプルだけで本番運用が安全になるわけではありません。

## テスト

DBを用意しなくても、入力検証と確認画面のXSS回帰テストを実行できます。

```bash
php tests/form_helpers_test.php
```
