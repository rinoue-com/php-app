<?php
include 'config_db.php'; // データベース接続
session_start();

// フォームデータの取得
$user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

// 入力値の確認
if (empty($user_id) || empty($username) || empty($password) || empty($password_confirm)) {
    header("Location: register.php?error=全てのフィールドを入力してください。");
    exit;
}

// パスワードの一致確認
if ($password !== $password_confirm) {
    header("Location: register.php?error=パスワードが一致しません。");
    exit;
}

// ユーザーIDの重複確認
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    header("Location: register.php?error=このユーザーIDは既に登録されています。");
    exit;
}

// パスワードのハッシュ化
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ユーザーの登録処理
$sql = "INSERT INTO users (user_id, username, password) VALUES (:user_id, :username, :password)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);

// 登録の成功・失敗を確認
if ($stmt->execute()) {
    // 成功した場合、ログインページへリダイレクト
    header('Location: login.php');
} else {
    // 失敗した場合、エラーメッセージを表示
    header('Location: register.php?error=登録中にエラーが発生しました。');
}
?>
