<?php
// データベース設定ファイルを読み込み
require 'db_config.php';

// データベース接続の例外処理
try {
    // PDO インスタンスの作成
    $pdo = new PDO(DSN, DB_USER, DB_PASSWORD);
    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 接続エラー時のメッセージ
    die('データベース接続に失敗しました: ' . $e->getMessage());
}
?>
