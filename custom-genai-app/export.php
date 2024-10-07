<?php
// 設定ファイルとデータベース接続ファイルの読み込み
require 'db_connect.php';   // データベース接続用の共通ファイル

// すべてのチャット履歴をデータベースから取得
$query = $pdo->query("SELECT * FROM chat_history ORDER BY timestamp ASC");
$chat_history = $query->fetchAll(PDO::FETCH_ASSOC);

// チャット履歴をJSON形式に変換してエクスポート
header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="chat_history.json"'); // ファイルダウンロード用
echo json_encode($chat_history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
