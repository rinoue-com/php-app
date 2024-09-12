<?php
// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームデータの取得
    $place_name = isset($_POST['place_name']) ? $_POST['place_name'] : '';
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    $latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] . ' 00:00:00' : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] . ' 00:00:00' : null;
    $undecided = isset($_POST['undecided']) ? (int)$_POST['undecided'] : 0;
    $related_url = isset($_POST['related_url']) ? $_POST['related_url'] : '';
    $memo = isset($_POST['memo']) ? $_POST['memo'] : '';
    $visited_flag = isset($_POST['visited_flag']) ? (int)$_POST['visited_flag'] : 0;
    $visited_date = !empty($_POST['visited_date']) ? $_POST['visited_date'] . ' 00:00:00' : null;

    // SQLインサート文の準備
    $sql = "INSERT INTO memo_places (place_name, location, latitude, longitude, start_date, end_date, undecided, related_url, memo, visited_flag, visited_date)
            VALUES (:place_name, :location, :latitude, :longitude, :start_date, :end_date, :undecided, :related_url, :memo, :visited_flag, :visited_date)";
    $stmt = $pdo->prepare($sql);

    // バインド処理をコンパクトに
    $stmt->bindValue(':place_name', $place_name, PDO::PARAM_STR);
    $stmt->bindValue(':location', $location, PDO::PARAM_STR);
    $stmt->bindValue(':latitude', $latitude !== null ? $latitude : null, $latitude !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':longitude', $longitude !== null ? $longitude : null, $longitude !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':start_date', $start_date !== null ? $start_date : null, $start_date !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':end_date', $end_date !== null ? $end_date : null, $end_date !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':visited_date', $visited_date !== null ? $visited_date : null, $visited_date !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':undecided', $undecided, PDO::PARAM_INT);
    $stmt->bindValue(':related_url', $related_url, PDO::PARAM_STR);
    $stmt->bindValue(':memo', $memo, PDO::PARAM_STR);
    $stmt->bindValue(':visited_flag', $visited_flag, PDO::PARAM_INT);

    // データを実行して保存
    if ($stmt->execute()) {
        echo 'データが正常に登録されました。<br>';
        echo '<a href="index.php">一覧ページに戻る</a>';  // 成功時のリンク
    } else {
        echo 'データの登録に失敗しました。<br>';
        echo '<a href="register.php">登録画面に戻る</a>';  // 失敗時のリンク
    }
}
?>
