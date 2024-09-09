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

// GETパラメータからIDを取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo '無効なIDです。';
    exit;
}

// 詳細データ取得
$sql = 'SELECT * FROM places WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$place = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$place) {
    echo 'データが見つかりません。';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?>の詳細</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?>の詳細</h1>
    <p>場所の名前: <?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p>位置: <?php echo htmlspecialchars($place['location'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p>開催期間: 
        <?php
        if ($place['event_period_start'] && $place['event_period_end']) {
            echo htmlspecialchars($place['event_period_start'], ENT_QUOTES, 'UTF-8') . ' ～ ' .
                 htmlspecialchars($place['event_period_end'], ENT_QUOTES, 'UTF-8');
        } else {
            echo '未定';
        }
        ?>
    </p>
    <p>関連URL: <a href="<?php echo htmlspecialchars($place['related_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($place['related_url'], ENT_QUOTES, 'UTF-8'); ?></a></p>
    <p>メモ: <?php echo htmlspecialchars($place['memo'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p>訪問済み: <?php echo $place['visited_flag'] ? 'はい' : 'いいえ'; ?></p>
    <p>訪問日: 
        <?php echo $place['visited_date'] ? htmlspecialchars($place['visited_date'], ENT_QUOTES, 'UTF-8') : '未訪問'; ?>
    </p>
    <p>登録日時: <?php echo htmlspecialchars($place['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="list.php">一覧に戻る</a>
</body>
</html>
