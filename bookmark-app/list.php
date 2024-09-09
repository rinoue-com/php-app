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

// データ取得
$sql = 'SELECT id, place_name, location, start_date, undecided, visited_flag, related_url FROM memo_places';
$stmt = $pdo->query($sql);
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
    <table border="1">
        <tr>
            <th>イベントの名前</th>
            <th>場所の名前</th>
            <th>開始日</th>
            <th>訪問済み</th>
            <th>参考URL</th>
            <th>詳細</th>
        </tr>
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
    </table>
</body>
</html>
