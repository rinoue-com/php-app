<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI チャットアプリ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
        .nav {
            display: flex;
            justify-content: center;
            background-color: #444;
            padding: 10px 0;
        }
        .nav a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
        }
        .nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AI チャットアプリ</h1>
    </div>
    <div class="nav">
        <a href="chat_app.php">AIチャット(標準)</a>
        <a href="vector_search.php">AIチャット(外部ファイルあり)</a>
        <a href="file_list_view.php">学習ファイル管理</a>
        <a href="logout.php">ログアウト</a>
        <a href="create_user.php">ユーザ作成</a>
    </div>
    <hr>
</body>
</html>
