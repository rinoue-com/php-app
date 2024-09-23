<?php
include 'funcs.php'; // funcs.php をインクルードしてログイン状態を確認する関数を使用
sschk(); // ログイン状態の確認

// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';

// POSTリクエストで受け取ったIDを取得
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo '無効なIDです。';
    exit;
}

// 動的に更新するフィールドを準備
$fields = [];
$params = [];

if (!empty($_POST['place_name'])) {
    $fields[] = "place_name = :place_name";
    $params[':place_name'] = $_POST['place_name'];
}

if (!empty($_POST['location'])) {
    $fields[] = "location = :location";
    $params[':location'] = $_POST['location'];
}

if (!empty($_POST['latitude'])) {
    $fields[] = "latitude = :latitude";
    $params[':latitude'] = $_POST['latitude'];
}

if (!empty($_POST['longitude'])) {
    $fields[] = "longitude = :longitude";
    $params[':longitude'] = $_POST['longitude'];
}

if (!empty($_POST['start_date'])) {
    $fields[] = "start_date = :start_date";
    $params[':start_date'] = $_POST['start_date'] . ' 00:00:00';
}

if (!empty($_POST['end_date'])) {
    $fields[] = "end_date = :end_date";
    $params[':end_date'] = $_POST['end_date'] . ' 00:00:00';
}

if (isset($_POST['undecided'])) {
    $fields[] = "undecided = :undecided";
    $params[':undecided'] = (int)$_POST['undecided'];
}

if (!empty($_POST['related_url'])) {
    $fields[] = "related_url = :related_url";
    $params[':related_url'] = $_POST['related_url'];
}

if (!empty($_POST['memo'])) {
    $fields[] = "memo = :memo";
    $params[':memo'] = $_POST['memo'];
}

if (isset($_POST['visited_flag'])) {
    $fields[] = "visited_flag = :visited_flag";
    $params[':visited_flag'] = (int)$_POST['visited_flag'];
}

if (!empty($_POST['visited_date'])) {
    $fields[] = "visited_date = :visited_date";
    $params[':visited_date'] = $_POST['visited_date'] . ' 00:00:00';
}

// もし更新するフィールドが何もなければ、何もしないで終了
if (empty($fields)) {
    echo '更新するフィールドがありません。';
    exit;
}

// 動的にUPDATEクエリを生成
$sql = "UPDATE memo_places SET " . implode(', ', $fields) . " WHERE id = :id";
$stmt = $pdo->prepare($sql);
$params[':id'] = $id;

// クエリの実行
if ($stmt->execute($params)) {
    // 更新成功時に一覧画面にリダイレクト
    header("Location: index.php?update_status=success");
    exit;
} else {
    echo 'データの更新に失敗しました。';
}
?>
