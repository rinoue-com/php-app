<?php
// 設定ファイルとデータベース接続ファイルの読み込み
require 'config.php';       // OpenAI API キーの設定ファイル
require 'db_connect.php';   // データベース接続用の共通ファイル

session_start();
include 'funcs.php'; // funcs.php をインクルードしてログイン状態を確認する関数を使用
sschk(); // ログイン状態の確認

// OpenAI APIの設定
$openai_api_key = OPENAI_API_KEY; // `config.php` から API キーを読み込み
$api_url = 'https://api.openai.com/v1/chat/completions'; // OpenAI エンドポイント

// チャットの送信・保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_message'])) {
    $user_message = trim($_POST['user_message']);

    // OpenAI API へのリクエストデータを準備
    $data = [
        'model' => 'gpt-4o-mini',  // モデル名を 'gpt-4o-mini' に設定
        'messages' => [
            ['role' => 'user', 'content' => $user_message]
        ],
        'max_tokens' => 100,
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n" .
                        "Authorization: Bearer $openai_api_key\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ],
    ];

    // APIリクエストを実行
    $context = stream_context_create($options);
    $response = file_get_contents($api_url, false, $context);
    $result = json_decode($response, true);

    // APIからの応答を取得
    $bot_response = $result['choices'][0]['message']['content'] ?? 'エラー: 応答がありませんでした。';

    // チャット履歴をデータベースに保存
    $stmt = $pdo->prepare("INSERT INTO chat_history (user_message, bot_response) VALUES (:user_message, :bot_response)");
    $stmt->bindParam(':user_message', $user_message);
    $stmt->bindParam(':bot_response', $bot_response);
    $stmt->execute();
}

// すべてのチャット履歴を取得
$query = $pdo->query("SELECT * FROM chat_history ORDER BY timestamp ASC");
$chat_history = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- チャット履歴の表示 -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャット履歴と質問アプリ</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <h2>チャット履歴</h2>
    <div>
        <?php
        if (empty($chat_history)) {
            echo "<p>まだチャット履歴はありません。</p>";
        } else {
            foreach ($chat_history as $row) {
                echo "<p><strong>ユーザー:</strong> " . htmlspecialchars($row['user_message']) . "<br>";
                echo "<strong>AI応答:</strong> " . htmlspecialchars($row['bot_response']) . "<br>";
                echo "<em>日時:</em> " . $row['timestamp'] . "</p><hr>";
            }
        }
        ?>
    </div>

    <!-- チャット入力フォーム -->
    <form method="POST" action="">
        <label for="user_message">質問を入力してください:</label><br>
        <input type="text" name="user_message" id="user_message" required><br><br>
        <button type="submit">送信</button>
    </form>

    <br>
    <!-- チャット履歴のエクスポートリンク -->
    <a href="export.php">チャット履歴をJSON形式でエクスポート</a>
</body>
</html>
