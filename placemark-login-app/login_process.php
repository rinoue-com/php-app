<?php
session_start();
include 'config_db.php'; // データベース接続

// フォームから送信されたユーザーIDとパスワードを取得
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// ユーザーIDが存在するか確認
$sql = "SELECT * FROM users WHERE user_id = :user_id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ユーザーが見つかり、パスワードが一致する場合
if ($user && password_verify($password, $user['password'])) {
    // セッションにユーザー情報を保存
    $_SESSION['user_id'] = $user['user_id'];      // ログインに使ったユーザーID
    $_SESSION['username'] = $user['username'];    // 表示用のユーザー名
    $_SESSION['chk_ssid'] = session_id();         // セッションIDを保存
    header('Location: index.php'); // ログイン後のページにリダイレクト
    exit;
} else {
    // ログイン失敗
    header('Location: login.php?error=1');
    exit;
}
?>
