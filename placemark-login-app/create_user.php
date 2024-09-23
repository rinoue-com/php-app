<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="style.css"> <!-- 外部CSSファイルを読み込む -->
</head>
<body>
    <h2>ユーザー登録</h2>
    <form action="register_user.php" method="POST">
        <label for="user_id">ユーザーID:</label>
        <input type="text" name="user_id" id="user_id" required><br><br>

        <label for="username">ユーザー名:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">パスワード:</label>
        <input type="password" name="password" id="password" required><br><br>

        <label for="password_confirm">パスワード確認:</label>
        <input type="password" name="password_confirm" id="password_confirm" required><br><br>

        <button type="submit">登録</button>
    </form>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;">登録に失敗しました。<?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
</body>
</html>
