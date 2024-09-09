<?php
// config.phpをインクルードしてAPIキーを取得
include 'config.php';
?>

<?php
// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';

// GETパラメータからIDを取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo '無効なIDです。';
    exit;
}

// 詳細データ取得
$sql = 'SELECT * FROM memo_places WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$place = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$place) {
    echo 'データが見つかりません。';
    exit;
}

// 位置情報が登録されているか確認
$hasLocation = !empty($place['latitude']) && !empty($place['longitude']);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?>の詳細</title>
    <link rel="stylesheet" href="style.css"> <!-- CSSの読み込み -->
    <?php if ($hasLocation): ?>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>"></script> <!-- Google Maps API -->
    <?php endif; ?>
</head>
<body>
    <h1><?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?>の詳細</h1>
    <div class="detail-page">
        <p>場所の名前: <?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p>位置: <?php echo htmlspecialchars($place['location'], ENT_QUOTES, 'UTF-8'); ?></p>

        <!-- 位置情報マップ表示 or 「登録無し」表示 -->
        <?php if ($hasLocation): ?>
            <div id="map"></div>
            <p>緯度: <?php echo htmlspecialchars($place['latitude'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>経度: <?php echo htmlspecialchars($place['longitude'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php else: ?>
            <p>位置情報: 登録無し</p>
        <?php endif; ?>

        <!-- 開催期間の表示 -->
        <p>開催期間: 
            <?php if ($place['undecided'] == 1): ?>
                未定
            <?php else: ?>
                <?php echo htmlspecialchars($place['start_date'], ENT_QUOTES, 'UTF-8'); ?> ～ <?php echo htmlspecialchars($place['end_date'], ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </p>

        <p>関連URL: <a href="<?php echo htmlspecialchars($place['related_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($place['related_url'], ENT_QUOTES, 'UTF-8'); ?></a></p>
        <p>メモ: <?php echo htmlspecialchars($place['memo'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p>訪問済み: <?php echo $place['visited_flag'] ? 'はい' : 'いいえ'; ?></p>
        <p>訪問日: 
            <?php echo $place['visited_date'] ? htmlspecialchars($place['visited_date'], ENT_QUOTES, 'UTF-8') : '未訪問'; ?>
        </p>
        <p>登録日時: <?php echo htmlspecialchars($place['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
        <a href="list.php">一覧に戻る</a>
    </div>

    <!-- マップ表示用スクリプト (位置情報がある場合のみ) -->
    <?php if ($hasLocation): ?>
        <script>
            function initMap() {
                var lat = <?php echo $place['latitude']; ?>;
                var lng = <?php echo $place['longitude']; ?>;
                var location = { lat: lat, lng: lng };

                var map = new google.maps.Map(document.getElementById("map"), {
                    center: location,
                    zoom: 15
                });

                var marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: "<?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?>"
                });
            }

            // Google Mapsの初期化
            window.onload = function() {
                initMap();
            };
        </script>
    <?php endif; ?>
</body>
</html>
