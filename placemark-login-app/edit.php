<?php
include 'funcs.php'; // funcs.php をインクルードしてログイン状態を確認する関数を使用
sschk(); // ログイン状態の確認

// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';
include 'config.php'; // Google Maps APIキーを取得

// GETパラメータからIDを取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo '無効なIDです。';
    exit;
}

// GETリクエスト時、既存データを取得
$sql = "SELECT * FROM memo_places WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$place = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$place) {
    echo 'データが見つかりません。';
    exit;
}

// 日付の形式を調整（YYYY-MM-DD形式に変換）
$start_date = !empty($place['start_date']) ? date('Y-m-d', strtotime($place['start_date'])) : '';
$end_date = !empty($place['end_date']) ? date('Y-m-d', strtotime($place['end_date'])) : '';
$visited_date = !empty($place['visited_date']) ? date('Y-m-d', strtotime($place['visited_date'])) : '';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>行きたい場所の編集</title>
    <link rel="stylesheet" href="style.css"> <!-- 外部CSSファイルを読み込む -->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>&libraries=places"></script> <!-- Google Maps API -->
</head>

<body>
    <h1>行きたい場所を編集する</h1>
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <label for="place_name">場所やイベントの名前:</label>
        <input type="text" id="place_name" name="place_name" value="<?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>

        <label for="location">場所検索:</label>
        <input type="text" id="location_input" name="location" value="<?php echo htmlspecialchars($place['location'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>

        <div id="map" style="width:100%; height:400px;"></div><br>

        <!-- 緯度・経度を送信するための非表示フィールド -->
        <input type="hidden" id="latitude" name="latitude" value="<?php echo htmlspecialchars($place['latitude'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo htmlspecialchars($place['longitude'], ENT_QUOTES, 'UTF-8'); ?>">

        <label for="undecided">開催期間:</label>
        <input type="checkbox" id="undecided" name="undecided" value="1" <?php echo $place['undecided'] == 1 ? 'checked' : ''; ?>> 未定<br><br>

        <div class="datetime-group">
            <div>
                <label for="start_date">開始日:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>"><br><br>
            </div>
            <div>
                <label for="end_date">終了日:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>"><br><br>
            </div>
        </div>

        <label for="visited_flag">訪問済み:</label>
        <input type="checkbox" id="visited_flag" name="visited_flag" value="1" <?php echo $place['visited_flag'] == 1 ? 'checked' : ''; ?>><br><br>

        <label for="visited_date">訪問日:</label>
        <input type="date" id="visited_date" name="visited_date" value="<?php echo $visited_date; ?>"><br><br>

        <label for="related_url">関連URL:</label>
        <input type="url" id="related_url" name="related_url" value="<?php echo htmlspecialchars($place['related_url'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>

        <label for="memo">メモ:</label>
        <textarea id="memo" name="memo"><?php echo htmlspecialchars($place['memo'], ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>

        <button type="submit">更新</button>
    </form>

    <script>
        let map, marker, autocomplete;

        function initMap() {
            const latitude = parseFloat(document.getElementById('latitude').value) || 35.6804;
            const longitude = parseFloat(document.getElementById('longitude').value) || 139.7690;

            const location = { lat: latitude, lng: longitude };

            map = new google.maps.Map(document.getElementById('map'), {
                center: location,
                zoom: 13
            });

            marker = new google.maps.Marker({
                position: location,
                map: map
            });

            autocomplete = new google.maps.places.Autocomplete(document.getElementById('location_input'));
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", function () {
                marker.setVisible(false);
                const place = autocomplete.getPlace();

                if (!place.geometry) {
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                marker.setPosition(place.geometry.location);
                marker.setVisible(true);

                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
            });
        }

        window.onload = initMap;
    </script>
</body>
</html>
