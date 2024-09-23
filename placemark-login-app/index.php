<?php
//エラー表示
ini_set("display_errors", 1);

session_start();
include 'funcs.php'; // funcs.php をインクルードしてログイン状態を確認する関数を使用
sschk(); // ログイン状態の確認

// config_db.phpをインクルードして、データベース接続情報を取得
include 'config_db.php';

// 削除処理や登録・更新処理のステータスを取得
$deleteStatus = isset($_GET['delete_status']) ? $_GET['delete_status'] : null;
$registerStatus = isset($_GET['register_status']) ? $_GET['register_status'] : null;
$updateStatus = isset($_GET['update_status']) ? $_GET['update_status'] : null;

// 検索キーワードとフィルタの取得
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$showUndecided = isset($_GET['undecided']) ? 1 : 0;
$showVisited = isset($_GET['visited']) ? 1 : 0;
$showPastEvents = isset($_GET['past_events']) ? 1 : 0;
$startDateRange = isset($_GET['start_date_range']) ? $_GET['start_date_range'] : '';

$user_id = $_SESSION['user_id'];

// クリアボタンが押された場合、検索文字列をリセット
if (isset($_GET['clear'])) {
    header("Location: index.php"); // クエリパラメータを削除してリダイレクト
    exit;
}

// SQLクエリの準備
$sql = 'SELECT id, place_name, location, start_date, end_date, undecided, visited_flag, related_url FROM memo_places WHERE (place_name LIKE :search OR location LIKE :search) AND user_id = :user_id';

// 未定のものを除外するフィルタ
if ($showUndecided == 0) {
    $sql .= ' AND undecided = 0';
}

// 訪問済みフィルタ
if ($showVisited == 1) {
    $sql .= ' AND visited_flag = 1';
}

// 過去のイベントフィルタ
if ($showPastEvents == 0) {
    $sql .= ' AND (end_date IS NULL OR end_date >= NOW())';
}

// 開始日の範囲フィルタ（指定された日付以前の開始日を取得）
if (!empty($startDateRange)) {
    $sql .= ' AND start_date <= :start_date_range';
}

$stmt = $pdo->prepare($sql);
$searchParam = '%' . $searchKeyword . '%';
$stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);

if (!empty($startDateRange)) {
    $stmt->bindValue(':start_date_range', $startDateRange, PDO::PARAM_STR);
}

$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
// ログイン中のユーザー名を取得
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'ゲスト';

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var deleteStatus = "<?php echo $deleteStatus; ?>";
            var registerStatus = "<?php echo $registerStatus; ?>";
            var updateStatus = "<?php echo $updateStatus; ?>";
            var messageElement = $('#status-message');

            if (deleteStatus === "success") {
                messageElement.text("削除に成功しました。");
                messageElement.removeClass('error');
                messageElement.fadeIn();
            } else if (deleteStatus === "failure") {
                messageElement.text("削除に失敗しました。");
                messageElement.addClass('error');
                messageElement.fadeIn();
            }

            if (registerStatus === "success") {
                messageElement.text("登録が完了しました。");
                messageElement.removeClass('error');
                messageElement.fadeIn();
            }

            if (updateStatus === "success") {
                messageElement.text("更新が完了しました。");
                messageElement.removeClass('error');
                messageElement.fadeIn();
            }

            // 3秒後にメッセージをフェードアウトさせる
            setTimeout(function() {
                messageElement.fadeOut(1000);
            }, 3000);

            // クエリパラメータを削除してリロード時にメッセージが再表示されないようにする
            if (deleteStatus || registerStatus || updateStatus) {
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
     <!-- ユーザー名とログアウトボタンを表示するバー -->
     <div class="header-bar">
        <div class="username">こんにちは、<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>さん</div>
        <a href="logout.php" class="logout-button">ログアウト</a>
    </div>

    <!-- 統合された検索フォームとフィルタフォーム -->
    <div class="search-filter-container">
        <form method="GET" action="index.php" class="search-form">
            <!-- 検索フォーム部分 -->
            <div class="search-container">
                <input type="text" name="search" placeholder="イベント名や場所名で検索" value="<?php echo htmlspecialchars($searchKeyword, ENT_QUOTES, 'UTF-8'); ?>" class="search-input">
            </div>

            <!-- フィルタオプション -->
            <div class="filter-container">
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
            </div>

            <!-- 検索ボタンとクリアボタン -->
            <div class="search-buttons">
                <button type="submit" class="search-button">検索</button>
                <button type="submit" name="clear" value="1" class="clear-button">クリア</button>
            </div>
        </form>
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
            <th>編集</th>
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
                <td><a href="detail.php?id=<?php echo $place['id']; ?>">詳細</a></td>
                <td><a href="edit.php?id=<?php echo $place['id']; ?>">📝</a></td>
                <td><a href="delete.php?id=<?php echo $place['id']; ?>" onclick="return confirm('本当に削除しますか？')">🚮</a></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">検索結果が見つかりませんでした。</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
