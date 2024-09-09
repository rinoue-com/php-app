
<?php
//エラー表示
ini_set("display_errors", 1);

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
$sql = 'SELECT id, place_name, start_date, end_date, visited_flag FROM memo_places';
$stmt = $pdo->query($sql);
$places = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>行きたい場所一覧</title>
</head>
<body>
    <h1>行きたい場所一覧</h1>
    <table border="1">
        <tr>
            <th>場所の名前</th>
            <th>開催期間</th>
            <th>訪問済み</th>
            <th>詳細</th>
        </tr>
        <?php foreach ($places as $place): ?>
        <tr>
            <td><?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>
                <?php
                if ($place['start_date'] && $place['end_date']) {
                    echo htmlspecialchars($place['start_date'], ENT_QUOTES, 'UTF-8') . ' ～ ' .
                         htmlspecialchars($place['end_date'], ENT_QUOTES, 'UTF-8');
                } else {
                    echo '未定';
                }
                ?>
            </td>
            <td><?php echo $place['visited_flag'] ? 'はい' : 'いいえ'; ?></td>
            <td><a href="detail.php?id=<?php echo $place['id']; ?>">詳細を見る</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
