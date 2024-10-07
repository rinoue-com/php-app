<?php
// アップロード先ディレクトリを指定
$upload_dir = 'uploads/';

// 削除リクエストがあった場合の処理
if (isset($_GET['delete'])) {
    $file_to_delete = basename($_GET['delete']);
    $file_path = $upload_dir . $file_to_delete;

    // ファイルが存在する場合、削除を実行
    if (file_exists($file_path)) {
        unlink($file_path);
        echo "<p>ファイル「{$file_to_delete}」を削除しました。</p>";
    } else {
        echo "<p>ファイルが存在しません。</p>";
    }
}

// アップロードディレクトリが存在しない場合はメッセージを表示
if (!is_dir($upload_dir)) {
    echo "アップロードされたファイルはまだありません。";
    exit;
}

// ディレクトリ内のファイル一覧を取得
$files = scandir($upload_dir);

// ファイル一覧をフィルタリング（. と .. を除外）
$files = array_filter($files, function($file) {
    return !in_array($file, ['.', '..']);
});

// ファイルが存在しない場合の処理
if (empty($files)) {
    echo "アップロードされたファイルはまだありません。";
    exit;
}

// ファイル一覧を表形式で表示
echo "<table>";
echo "<tr><th>ファイル名</th><th>ファイルサイズ</th><th>アップロード日</th><th>ダウンロード</th><th>削除</th></tr>";

foreach ($files as $file) {
    $file_path = $upload_dir . $file;
    $file_size = filesize($file_path);
    $upload_date = date("Y-m-d H:i:s", filemtime($file_path));
    echo "<tr>";
    echo "<td>" . htmlspecialchars($file) . "</td>";
    echo "<td>" . number_format($file_size / 1024, 2) . " KB</td>";
    echo "<td>" . $upload_date . "</td>";
    echo "<td><a href='$file_path' download>ダウンロード</a></td>";
    // 削除リンクを追加
    echo "<td><a href='file_list.php?delete=" . urlencode($file) . "' onclick='return confirm(\"本当に削除しますか？\");'>削除</a></td>";
    echo "</tr>";
}

echo "</table>";
?>
