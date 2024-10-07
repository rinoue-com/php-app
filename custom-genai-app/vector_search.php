<?php
// 共通の設定ファイルを読み込み
require 'config.php';
require 'db_connect.php';
require 'header.php';  // 共通のヘッダを読み込み

session_start();
include 'funcs.php'; // funcs.php をインクルードしてログイン状態を確認する関数を使用
sschk(); // ログイン状態の確認

// OpenAI APIの設定
$openai_api_key = OPENAI_API_KEY;
$openai_url = 'https://api.openai.com/v1/chat/completions'; // OpenAI APIエンドポイント

// ベクトル検索APIのエンドポイント
$vector_search_url = 'https://example.com/proxyfunction-1';

// デバッグモードを初期化
$debug_mode = false;

// デバッグモードのチェックボックスがオンの場合にデバッグ表示を有効化
if (isset($_POST['debug_mode'])) {
    $debug_mode = true;
}

// チャットの送信・保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_message'])) {
    $user_message = trim($_POST['user_message']);

    // ベクトル検索APIにクエリを送信
    $vector_search_data = ['query' => $user_message];
    $vector_search_options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($vector_search_data),
        ],
    ];

    // ベクトル検索APIリクエストを実行
    $vector_search_context = stream_context_create($vector_search_options);
    $vector_search_response = file_get_contents($vector_search_url, false, $vector_search_context);
    $vector_search_result = json_decode($vector_search_response, true);

    // ベクトル検索結果を取得
    $related_data = '';
    if (isset($vector_search_result['results'])) {
        foreach ($vector_search_result['results'] as $result) {
            $related_data .= $result['document'] . "\n";
        }
    }

    // ユーザーの質問にベクトル検索結果を組み込む形式
    $modified_user_message = $user_message . "\n\n以下のデータを参考に回答してください。\n" . $related_data;

    // OpenAI API へのリクエストデータを準備
    $data = [
        'model' => 'gpt-4o-mini',  // モデル名を指定
        'messages' => [
            ['role' => 'user', 'content' => $modified_user_message],  // 修正したユーザーの質問を含める
        ],
        'max_tokens' => 100,
    ];

    // デバッグモードが有効な場合、デバッグ情報を表示
    if ($debug_mode) {
        echo "<h3>デバッグ情報 - ベクトル検索結果:</h3>";
        echo "<pre>" . htmlspecialchars(print_r($vector_search_result, true)) . "</pre>";

        echo "<h3>デバッグ情報 - AIへの送信内容:</h3>";
        echo "<pre>" . htmlspecialchars(print_r($data, true)) . "</pre>";
    }

    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n" .
                        "Authorization: Bearer $openai_api_key\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ],
    ];

    // OpenAI APIリクエストを実行
    $context = stream_context_create($options);
    $response = file_get_contents($openai_url, false, $context);
    $result = json_decode($response, true);

    // APIからの応答を取得
    $bot_response = $result['choices'][0]['message']['content'] ?? 'エラー: 応答がありませんでした。';

    // デバッグモードが有効な場合、AIの応答内容もデバッグ表示
    if ($debug_mode) {
        echo "<h3>デバッグ情報 - AIからの応答内容:</h3>";
        echo "<pre>" . htmlspecialchars(print_r($result, true)) . "</pre>";
    }

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
    <title>ベクトル検索付きAIチャットアプリ</title>
</head>
<body>
    <h2>ベクトル検索付きAIチャット履歴</h2>
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

        <!-- デバッグモードのチェックボックス -->
        <label for="debug_mode">デバッグモードを有効にする</label>
        <input type="checkbox" name="debug_mode" id="debug_mode" <?php echo ($debug_mode ? 'checked' : ''); ?>><br><br>

        <button type="submit">送信</button>
    </form>
</body>
</html>
