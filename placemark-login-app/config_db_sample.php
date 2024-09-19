<?php
// データベース接続設定
define('DB_HOST', 'localhost');    // ホスト名
define('DB_USER', 'root');         // ユーザー名
define('DB_PASSWORD', '');         // パスワード
define('DB_NAME', 'travel_memo_db'); // データベース名

// DSNの生成
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";

// データベース接続の例外処理
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>
