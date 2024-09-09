<?php
// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';

// フォームからのデータを受け取る
$place_name = $_POST['place_name'];
$location = $_POST['location'];
$latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : NULL;
$longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : NULL;
$place_id = $_POST['place_id'];
$undecided = isset($_POST['undecided']) ? 1 : 0;
$related_url = $_POST['related_url'];
$memo = $_POST['memo'];
$visited_flag = isset($_POST['visited_flag']) ? 1 : 0;
$visited_date = !empty($_POST['visited_date']) ? $_POST['visited_date'] : NULL;

// 開催予定未定の場合、開始日時と終了日時をNULLにする
if ($undecided == 1) {
    $start_date = NULL;
    $end_date = NULL;
} else {
    // 未定ではない場合、入力された日時をそのまま使用。空の場合はNULLとして登録
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : NULL;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;
}

// SQLクエリの準備
$sql = "INSERT INTO memo_places (place_name, location, latitude, longitude, place_id, start_date, end_date, undecided, related_url, memo, visited_flag, visited_date)
        VALUES (:place_name, :location, :latitude, :longitude, :place_id, :start_date, :end_date, :undecided, :related_url, :memo, :visited_flag, :visited_date)";

$stmt = $pdo->prepare($sql);

// パラメータのバインド
$stmt->bindParam(':place_name', $place_name, PDO::PARAM_STR);
$stmt->bindParam(':location', $location, PDO::PARAM_STR);
$stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR);
$stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR);
$stmt->bindParam(':place_id', $place_id, PDO::PARAM_STR);

// 開始日時と終了日時のバインドは、NULLか値があるかをチェック
$stmt->bindValue(':start_date', $start_date, $start_date === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);
$stmt->bindValue(':end_date', $end_date, $end_date === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);

$stmt->bindParam(':undecided', $undecided, PDO::PARAM_INT);
$stmt->bindParam(':related_url', $related_url, PDO::PARAM_STR);
$stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
$stmt->bindParam(':visited_flag', $visited_flag, PDO::PARAM_INT);
$stmt->bindValue(':visited_date', $visited_date, $visited_date === NULL ? PDO::PARAM_NULL : PDO::PARAM_STR);

// データを実行して保存
if ($stmt->execute()) {
    echo 'データが正常に登録されました。';
} else {
    echo 'データの登録に失敗しました。';
}
?>
