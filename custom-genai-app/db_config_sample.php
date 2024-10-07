<?php
// データベース接続設定
define('DB_HOST', 'localhost');      // ホスト名
define('DB_USER', 'root');           // ユーザー名
define('DB_PASSWORD', '');           // パスワード
define('DB_NAME', 'chat_db');        // データベース名

// DSN（Data Source Name）の生成
define('DSN', "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8");
?>
