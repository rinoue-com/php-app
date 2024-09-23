<?php
session_start(); // セッションを開始

// ログインしている場合、ログイン後のページへリダイレクト
if (isset($_SESSION['chk_ssid'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css"> <!-- 外部CSSファイルを読み込む -->
</head>
<body>
    <h2>ログイン</h2>
    <form action="login_process.php" method="POST">
        <label for="user_id">ユーザーID:</label>
        <input type="text" name="user_id" id="user_id" required><br><br>

        <label for="password">パスワード:</label>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">ログイン</button>
    </form>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;">ユーザーIDまたはパスワードが違います。</p>
    <?php endif; ?>
</body>
</html>