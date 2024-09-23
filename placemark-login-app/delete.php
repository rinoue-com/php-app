<?php
include 'funcs.php'; // funcs.php をインクルードしてログイン状態を確認する関数を使用
sschk(); // ログイン状態の確認

// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';

// GETパラメータからIDを取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$deleteSuccess = false;

if ($id > 0) {
    // 削除処理
    $deleteSql = "DELETE FROM memo_places WHERE id = :id AND user_id = :user_id";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $deleteStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
    if ($deleteStmt->execute()) {
        $deleteSuccess = true;
    }
}

// 成功または失敗のクエリパラメータを付けてリダイレクト
if ($deleteSuccess) {
    header("Location: index.php?delete_status=success");
} else {
    header("Location: index.php?delete_status=failure");
}
exit;
