<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>詳細情報</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="detail-container">
        <h1>詳細情報</h1>

        <?php
        $rowIndex = isset($_GET['row']) ? (int)$_GET['row'] : 0;
        $allData = [];
        if (($file = fopen("responses.csv", "r")) !== FALSE) {
            fgetcsv($file); // ヘッダをスキップ
            while (($data = fgetcsv($file)) !== FALSE) {
                $allData[] = $data;
            }
            fclose($file);
        }

        // if (isset($_POST['delete'])) {
        //     // 該当行を削除
        //     unset($allData[$rowIndex]);
        //     // 再度CSVファイルに保存
        //     $file = fopen("responses.csv", "w");
        //     // ヘッダを書き込む
        //     fputcsv($file, ["名前", "メールアドレス", "年代", "住んでいる都道府県", "旅行回数", "旅行計画", "満足度", "フィードバック"]);
        //     foreach ($allData as $row) {
        //         fputcsv($file, $row);
        //     }
        //     fclose($file);
        //     echo "<p>データが削除されました。</p>";
        //     echo "<a href='result.php'>戻る</a>";
        //     exit;
        // }

        // 該当する行のデータを表示
        if ($rowIndex < count($allData)) {
            $data = $allData[$rowIndex];
            echo "<table border='1'>";
            echo "<tr><th>項目</th><th>内容</th></tr>";
            echo "<tr><td>名前</td><td>" . htmlspecialchars($data[0]) . "</td></tr>";
            echo "<tr><td>メールアドレス</td><td>" . htmlspecialchars($data[1]) . "</td></tr>";
            echo "<tr><td>年代</td><td>" . htmlspecialchars($data[2]) . "</td></tr>";
            echo "<tr><td>住んでいる都道府県</td><td>" . htmlspecialchars($data[3]) . "</td></tr>";
            echo "<tr><td>旅行回数</td><td>" . htmlspecialchars($data[4]) . "</td></tr>";
            echo "<tr><td>旅行計画</td><td>" . htmlspecialchars($data[5]) . "</td></tr>";
            echo "<tr><td>満足度</td><td>" . htmlspecialchars($data[6]) . "</td></tr>";
            echo "<tr><td>フィードバック</td><td>" . htmlspecialchars($data[7]) . "</td></tr>";
            echo "</table>";
        }
        ?>

        <!-- <form method="post" action="">
            <input type="submit" name="delete" value="削除" onclick="return confirm('このデータを削除してもよろしいですか？');">
        </form> -->

        <a href="result.php">戻る</a>
    </div>
</body>
</html>
