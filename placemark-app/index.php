<?php
// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';

// 削除処理のステータスを取得
$deleteStatus = isset($_GET['delete_status']) ? $_GET['delete_status'] : null;

// 検索キーワードとフィルタの取得
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$showUndecided = isset($_GET['undecided']) ? 1 : 0;
$showVisited = isset($_GET['visited']) ? 1 : 0;
$showPastEvents = isset($_GET['past_events']) ? 1 : 0;
$startDateRange = isset($_GET['start_date_range']) ? $_GET['start_date_range'] : '';

// クリアボタンが押された場合、検索文字列をリセット
if (isset($_GET['clear'])) {
    header("Location: index.php"); // クエリパラメータを削除してリダイレクト
    exit;
}

// SQLクエリの準備
$sql = 'SELECT id, place_name, location, start_date, end_date, undecided, visited_flag, related_url FROM memo_places WHERE (place_name LIKE :search OR location LIKE :search)';

// フィルタ処理
if ($showUndecided == 0) {
    $sql .= ' AND undecided = 0';
}
if ($showVisited == 1) {
    $sql .= ' AND visited_flag = 1';
}
if ($showPastEvents == 0) {
    $sql .= ' AND (end_date IS NULL OR end_date >= NOW())';
}
if (!empty($startDateRange)) {
    $sql .= ' AND start_date <= :start_date_range';
}

$stmt = $pdo->prepare($sql);
$searchParam = '%' . $searchKeyword . '%';
$stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);

if (!empty($startDateRange)) {
    $stmt->bindValue(':start_date_range', $startDateRange, PDO::PARAM_STR);
}

// SQL実行
$stmt->execute();
$places = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>行きたい場所一覧</title>
    <link rel="stylesheet" href="style.css"> <!-- CSSの読み込み -->
    <style>
        /* メッセージ表示用のスタイル */
        .status-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #3498db; /* ブルーの色合い */
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: none; /* 初期は非表示 */
        }
        .status-message.error {
            background-color: #e74c3c; /* エラーメッセージ用の赤色 */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var deleteStatus = "<?php echo $deleteStatus; ?>";
            var messageElement = $('#status-message');
            
            if (deleteStatus === "success") {
                messageElement.text("削除に成功しました。");
                messageElement.removeClass('error');
                messageElement.fadeIn(); // メッセージをフェードイン
            } else if (deleteStatus === "failure") {
                messageElement.text("削除に失敗しました。");
                messageElement.addClass('error');
                messageElement.fadeIn(); // メッセージをフェードイン
            }

            // 3秒後にメッセージをフェードアウトさせる
            setTimeout(function() {
                messageElement.fadeOut(1000); // 1秒かけてフェードアウト
            }, 3000);

            // クエリパラメータを削除してリロード時にメッセージが再表示されないようにする
            if (deleteStatus) {
                var newUrl = window.location.href.split('?')[0]; // クエリを削除したURL
                window.history.replaceState(null, null, newUrl); // URLをクエリなしに更新
            }
        });
    </script>
</head>
<body>
    <h1>行きたい場所一覧</h1>

    <!-- ステータスメッセージ -->
    <div id="status-message" class="status-message"></div>

    <div class="search-filter-container">
        <!-- 検索フォーム -->
        <div class="search-container">
            <form method="GET" action="index.php" class="search-form">
                <input type="text" name="search" placeholder="イベント名や場所名で検索" value="<?php echo htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8'); ?>" class="search-input">
                <div class="search-buttons">
                    <button type="submit" class="search-button">検索</button>
                    <button type="submit" name="clear" value="1" class="clear-button">クリア</button>
                </div>
            </form>
        </div>

        <!-- フィルタオプション -->
        <div class="filter-container">
            <form method="GET" action="index.php">
                <label>
                    <input type="checkbox" name="undecided" <?php echo $showUndecided ? 'checked' : ''; ?>> 未定
                </label>
                <label>
                    <input type="checkbox" name="visited" <?php echo $showVisited ? 'checked' : ''; ?>> 訪問済み
                </label>
                <label>
                    <input type="checkbox" name="past_events" <?php echo $showPastEvents ? 'checked' : ''; ?>> 過去のイベント
                </label>
                <label>
                    開始日の範囲:
                    <input type="date" name="start_date_range" value="<?php echo htmlspecialchars($startDateRange, ENT_QUOTES, 'UTF-8'); ?>">
                </label>
                <button type="submit" class="filter-button">フィルタ適用</button>
            </form>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <a href="create.php" class="btn btn-primary">新規追加</a> <!-- 登録画面へのリンク -->
    </div>

    <table border="1">
        <tr>
            <th>イベントの名前</th>
            <th>場所の名前</th>
            <th>開始日</th>
            <th>終了日</th>
            <th>訪問済み</th>
            <th>参考URL</th>
            <th>詳細</th>
            <th>削除</th>
        </tr>
        <?php if (!empty($places)): ?>
            <?php foreach ($places as $place): ?>
            <tr>
                <td><?php echo htmlspecialchars($place['place_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($place['location'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <?php
                    if ($place['undecided'] == 1) {
                        echo '未定';
                    } elseif ($place['start_date']) {
                        echo htmlspecialchars(date('Y-m-d', strtotime($place['start_date'])), ENT_QUOTES, 'UTF-8');
                    } else {
                        echo '未定';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ($place['undecided'] == 1) {
                        echo '未定';
                    } elseif ($place['end_date']) {
                        echo htmlspecialchars(date('Y-m-d', strtotime($place['end_date'])), ENT_QUOTES, 'UTF-8');
                    } else {
                        echo '未定';
                    }
                    ?>
                </td>
                <td><?php echo $place['visited_flag'] == 1 ? '済' : ''; ?></td>
                <td>
                    <?php if (!empty($place['related_url'])): ?>
                        <a href="<?php echo htmlspecialchars($place['related_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">リンク</a>
                    <?php endif; ?>
                </td>
                <td><a href="detail.php?id=<?php echo $place['id']; ?>">詳細を見る</a></td>
                <td><a href="delete.php?id=<?php echo $place['id']; ?>" onclick="return confirm('本当に削除しますか？')">削除</a></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">検索結果が見つかりませんでした。</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
