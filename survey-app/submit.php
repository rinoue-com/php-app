<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // フォームから送信されたデータを取得
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $prefecture = $_POST['prefecture'];
    $travel_frequency = $_POST['travel_frequency'];
    $planning_extent = $_POST['planning_extent'];
    $satisfaction = $_POST['satisfaction'];
    $feedback = $_POST['feedback'];

    // データをCSV形式で保存
    $file = fopen("responses.csv", "a");
    fputcsv($file, [$name, $email, $age, $prefecture, $travel_frequency, $planning_extent, $satisfaction, $feedback]);
    fclose($file);

    // フォーム送信後に表示するメッセージ
    echo "<h1>アンケートのご協力ありがとうございました！<h1><br>";

    echo '<p style="font-size: medium">以下のリンクから集計画面を確認できます（デモ用）。</p>
        <a href="result.php" style="font-size: medium">結果画面を表示</a>';
}
?>
