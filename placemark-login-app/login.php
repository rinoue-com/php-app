<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css"> <!-- 外部CSSファイルを読み込む -->
</head>
<body>
    <h2>ログイン</h2>

    <!-- ログインフォーム -->
    <form action="login_process.php" method="POST">
        <label for="user_id">ユーザーID:</label>
        <input type="text" name="user_id" id="user_id" required><br><br>

        <label for="password">パスワード:</label>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">ログイン</button>
    </form>

    <!-- 新規登録ボタン -->
    <div style="text-align: center;">
        <a href="create_user.php" class="register-button">新規ユーザー登録</a>
    </div>

    <!-- ログアウト後のメッセージ表示 -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'loggedout'): ?>
        <div id="statusMessage" class="status-message">ログアウトしました</div>
    <?php endif; ?>

    <script>
        // ステータスメッセージをフェードアウトする処理
        window.onload = function() {
            var statusMessage = document.getElementById('statusMessage');
            if (statusMessage) {
                statusMessage.style.display = 'block';
                setTimeout(function() {
                    // 3秒後にフェードアウト
                    statusMessage.style.transition = 'opacity 1s';
                    statusMessage.style.opacity = '0';
                    setTimeout(function() {
                        statusMessage.style.display = 'none';
                    }, 1000);
                }, 3000);

                // URLからクエリパラメータを削除
                setTimeout(function() {
                    var url = new URL(window.location);
                    url.searchParams.delete('status');
                    window.history.replaceState(null, '', url);
                }, 0);
            }
        };
    </script>
</body>
</html>
