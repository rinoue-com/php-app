<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>IoTプラットフォーム利用者アンケート</title>
</head>
<body>
    <h1>IoTプラットフォーム利用者アンケート</h1>
    <form action="submit.php" method="post">
        <label for="name">名前:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="age">年齢:</label><br>
        <input type="number" id="age" name="age" required><br><br>
        
        <label for="satisfaction">満足度（1-5）:</label><br>
        <input type="number" id="satisfaction" name="satisfaction" min="1" max="5" required><br><br>
        
        <label for="feedback">フィードバック:</label><br>
        <textarea id="feedback" name="feedback" rows="4" cols="50"></textarea><br><br>
        
        <input type="submit" value="送信">
    </form>
</body>
</html>