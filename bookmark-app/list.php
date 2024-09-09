<?php
// DB接続
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'travel_memo_db';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// 検索キーワードの取得
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

// 検索クエリを作成
$sql = 'SELECT id, place_name, location, start_date, undecided, visited_flag, related_url FROM memo_places WHERE place_name LIKE :search OR location LIKE :search';
$stmt = $pdo->prepare($sql);
$searchParam = '%' . $searchKeyword . '%'; // 検索キーワードの曖昧検索用
$stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);

// SQL実行
$stmt->execute();
$places = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>行きたい場所一覧</title>
    <link rel="stylesheet" href="style.css"> <!-- CSSの読み込み -->
</head>
<body>
    <h1>行きたい場所一覧</h1>

    <!-- 検索フォーム -->
    <div class="search-container">
        <form method="GET" action="list.php" class="search-form">
            <input type="text" name="search" placeholder="イベント名や場所名で検索" value="<?php echo htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8'); ?>" class="search-input">
            <button type="submit" class="search-button">検索</button>
        </form>
    </div>

    <table border="1">
        <tr>
            <th>イベントの名前</th>
            <th>場所の名前</th>
            <th>開始日</th>
            <th>訪問済み</th>
            <th>参考URL</th>
            <th>詳細</th>
        </tr>
        <?php if (!empty($places)): ?>
            <?php foreach ($places as $place): ?>
            <tr>
                <td><?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($place['location'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?php
                    if ($place['undecided'] == 1) {
                        echo '未定';
                    } elseif ($place['start_date']) {
                        echo htmlspecialchars(date('Y-m-d', strtotime($place['start_date'])), ENT_QUOTES, 'UTF-8'); // 時刻を除外
                    } else {
                        echo '未定';
                    }
                    ?>
                </td>
                <td><?php echo $place['visited_flag'] == 1 ? '済' : ''; ?></td>
                <td>
                    <?php if (!empty($place['related_url'])): ?>
                        <a href="<?php echo htmlspecialchars($place['related_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">リンク</a>
                    <?php endif; ?>
                </td>
                <td><a href="detail.php?id=<?php echo $place['id']; ?>">詳細を見る</a></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">検索結果が見つかりませんでした。</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
