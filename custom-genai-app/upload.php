<?php
// アップロード先ディレクトリを指定
$upload_dir = 'uploads/';

// アップロードされたファイルの情報を取得
$file = $_FILES['file'];
$file_name = basename($file['name']);
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_error = $file['error'];
$file_type = mime_content_type($file_tmp);

// アップロードできるファイルサイズを指定（例: 5MB = 5 * 1024 * 1024 バイト）
$max_file_size = 5 * 1024 * 1024;

// アップロード可能なファイルタイプを指定
$allowed_types = ['application/pdf', 'text/csv', 'application/vnd.ms-excel', 'text/plain']; // CSV, PDF, TXT のMIMEタイプ

// エラーチェック
if ($file_error !== UPLOAD_ERR_OK) {
    echo "ファイルのアップロード中にエラーが発生しました。エラーコード: " . $file_error;
    exit;
}

// ファイルサイズチェック
if ($file_size > $max_file_size) {
    echo "ファイルサイズが5MBを超えています。";
    exit;
}

// ファイルタイプチェック
if (!in_array($file_type, $allowed_types)) {
    echo "アップロードできるファイルタイプはCSV, PDF, TXTのみです。";
    exit;
}

// アップロード先ディレクトリが存在しない場合は作成
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// ファイルの保存先パスを指定
$upload_file_path = $upload_dir . $file_name;

// ファイルをアップロード先ディレクトリに移動
if (move_uploaded_file($file_tmp, $upload_file_path)) {
    echo "ファイルのアップロードが完了しました。<br>";
    echo "ファイル名: " . htmlspecialchars($file_name) . "<br>";
    echo "保存場所: " . $upload_file_path . "<br>";

    // アップロードが完了したら、ファイル一覧ページにリダイレクト
    header("Location: file_list_view.php");
} else {
    echo "ファイルのアップロードに失敗しました。";
}
?>
