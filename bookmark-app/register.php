<?php
// データベース接続情報
$host = 'localhost';
$dbname = 'travel_memo_db';
$username = 'root';
$password = '';

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // フォームからのデータを取得
    $place_name = $_POST['place_name'];
    $location = $_POST['location'] ?? null; // 場所名
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $place_id = $_POST['place_id'] ?? null; // Google Place ID
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $undecided = isset($_POST['undecided']) ? 1 : 0;
    $related_url = $_POST['related_url'] ?? null;
    $memo = $_POST['memo'] ?? null;
    $visited_flag = isset($_POST['visited_flag']) ? 1 : 0;
    $visited_date = !empty($_POST['visited_date']) ? $_POST['visited_date'] : null;

    // SQLクエリの準備
    $sql = "INSERT INTO memo_places (place_name, location, latitude, longitude, place_id, start_date, end_date, undecided, related_url, memo, visited_flag, visited_date)
            VALUES (:place_name, :location, :latitude, :longitude, :place_id, :start_date, :end_date, :undecided, :related_url, :memo, :visited_flag, :visited_date)";

    // SQLステートメントの実行
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':place_name' => $place_name,
        ':location' => $location,
        ':latitude' => $latitude,
        ':longitude' => $longitude,
        ':place_id' => $place_id,
        ':start_date' => $start_date,
        ':end_date' => $end_date,
        ':undecided' => $undecided,
        ':related_url' => $related_url,
        ':memo' => $memo,
        ':visited_flag' => $visited_flag,
        ':visited_date' => $visited_date
    ]);

    echo "登録が完了しました！";

} catch (PDOException $e) {
    echo "データベース接続に失敗しました: " . $e->getMessage();
}
?>
