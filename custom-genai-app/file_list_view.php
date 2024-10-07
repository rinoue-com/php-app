<?php
session_start();
include 'funcs.php'; // funcs.php をインクルードしてログイン状態を確認する関数を使用
sschk(); // ログイン状態の確認
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文書ファイルアップロードと一覧表示</title>
    <style>
        /* スタイルの設定 */
        body {
            font-family: Arial, sans-serif;
        }
        .menu {
            background-color: #f2f2f2;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }
        .menu h3 {
            margin: 0;
        }
        .upload-form, .file-list {
            margin: 20px 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <!-- 追加メニューの表示 -->
    <?php include 'header.php'; ?>

    <!-- アップロードフォーム -->
    <div class="upload-form">
        <h2>文書ファイルのアップロード</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label for="file">アップロードするファイルを選択してください (CSV, PDF, TXT):</label><br><br>
            <!-- ファイルの種類に txt を追加 -->
            <input type="file" name="file" id="file" accept=".csv,.pdf,.txt" required><br><br>
            <button type="submit">アップロード</button>
        </form>
    </div>

    <!-- アップロードされたファイルの一覧表示 -->
    <div class="file-list">
        <h2>アップロードされたファイルの一覧</h2>
        <?php include 'file_list.php'; ?> <!-- ファイル一覧を表示するPHPスクリプトをインクルード -->
    </div>
</body>
</html>
