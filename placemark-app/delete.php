<?php
// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';

// GETパラメータからIDを取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$deleteSuccess = false;

if ($id > 0) {
    // 削除処理
    $deleteSql = "DELETE FROM memo_places WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->bindValue(':id', $id, PDO::PARAM_INT);
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
