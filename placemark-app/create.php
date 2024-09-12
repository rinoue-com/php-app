<?php
// config.phpをインクルードしてAPIキーを取得
include 'config.php';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>行きたい場所の登録フォーム</title>
    <link rel="stylesheet" href="style.css"> <!-- 外部CSSファイルを読み込む -->
</head>

<body>
    <h1>行きたい場所を登録する</h1>
    <form action="register.php" method="POST" id="registerForm"> <!-- 登録処理ファイルを指定 -->
        <label for="place_name">場所やイベントの名前:</label>
        <input type="text" id="place_name" name="place_name" required><br><br>

        <label for="location">場所検索:</label>
        <input type="text" id="location_input" placeholder="場所を検索"><br><br>

        <div id="map"></div><br>

        <!-- 緯度・経度・place_id を送信するための非表示フィールド -->
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <input type="hidden" id="place_id" name="place_id">
        <input type="hidden" id="location" name="location"> <!-- 一意の場所名 -->

        <div id="coordinates">緯度: <span id="lat_val"></span>, 経度: <span id="lng_val"></span></div>

        <label for="undecided">開催期間:</label>
        <input type="checkbox" id="undecided" name="undecided" value="1"> 未定<br><br>

        <div class="datetime-group">
            <div>
                <label for="start_date">開始日:</label>
                <input type="date" id="start_date" name="start_date"><br><br> <!-- 日付入力 -->
            </div>
            <div>
                <label for="end_date">終了日:</label>
                <input type="date" id="end_date" name="end_date"><br><br> <!-- 日付入力 -->
            </div>
        </div>

        <label for="visited_flag">訪問済み:</label>
        <input type="checkbox" id="visited_flag" name="visited_flag" value="1"><br><br>

        <label for="visited_date">訪問日:</label>
        <input type="date" id="visited_date" name="visited_date"><br><br> <!-- 日付入力 -->

        <label for="related_url">関連URL:</label>
        <input type="url" id="related_url" name="related_url"><br><br>

        <label for="memo">メモ:</label>
        <textarea id="memo" name="memo"></textarea><br><br>

        <button type="submit">登録</button>
    </form>

    <script>
        let map, marker, autocomplete;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 35.6804, lng: 139.7690 }, // 東京の位置
                zoom: 13
            });

            autocomplete = new google.maps.places.Autocomplete(document.getElementById("location_input"));
            autocomplete.bindTo("bounds", map);

            marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });

            autocomplete.addListener("place_changed", function() {
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

                // 場所の名前のみ取得（住所は含まない）
                let placeName = place.name || ''; // name がない場合の対応
                if (!placeName) {
                    // place.name が存在しない場合は types に基づいて最適な候補を取得
                    placeName = (place.types.includes('establishment') || place.types.includes('point_of_interest')) 
                                ? place.address_components[0].long_name : place.formatted_address.split(',')[0];
                }

                document.getElementById('location_input').value = placeName;

                // 緯度・経度、place_id、場所名を取得して非表示フィールドに設定
                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
                document.getElementById('place_id').value = place.place_id || ''; // 場所IDがない場合は空に
                document.getElementById('location').value = placeName; // 場所名をセット

                // 緯度・経度を表示
                document.getElementById("lat_val").innerText = place.geometry.location.lat();
                document.getElementById("lng_val").innerText = place.geometry.location.lng();
            });
        }

        // 未定が選択された場合の処理
        const undecidedCheckbox = document.getElementById('undecided');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        undecidedCheckbox.addEventListener('change', function() {
            if (this.checked) {
                startDateInput.disabled = true;
                endDateInput.disabled = true;
                startDateInput.value = ''; // 選択解除
                endDateInput.value = ''; // 選択解除
            } else {
                startDateInput.disabled = false;
                endDateInput.disabled = false;
            }
        });

        // 訪問済みフラグがONのときのみ訪問日を選択可能にする
        const visitedFlagCheckbox = document.getElementById('visited_flag');
        const visitedDateInput = document.getElementById('visited_date');

        visitedFlagCheckbox.addEventListener('change', function() {
            if (this.checked) {
                visitedDateInput.disabled = false;
            } else {
                visitedDateInput.disabled = true;
                visitedDateInput.value = ''; // 選択解除
            }
        });

        // ページ読み込み時に訪問日をデフォルトで無効化
        visitedDateInput.disabled = true;

        // エンターキーでの送信を無効化する処理
        document.getElementById('registerForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
                event.preventDefault(); // フォームの送信を無効化
            }
        });

        // Google Maps APIの読み込み
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_api_key; ?>&libraries=places&callback=initMap`;
        script.defer = true;
        document.head.appendChild(script);
    </script>
</body>

</html>
