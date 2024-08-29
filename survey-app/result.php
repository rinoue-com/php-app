<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アンケート結果</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>アンケート結果</h1>
    <table border="1">
        <tr>
            <th>名前</th>
            <th>年齢</th>
            <th>満足度</th>
            <th>フィードバック</th>
        </tr>
        <?php
        if (($file = fopen("responses.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($file)) !== FALSE) {
                echo "<tr>";
                foreach ($data as $field) {
                    echo "<td>" . htmlspecialchars($field) . "</td>";
                }
                echo "</tr>";
            }
            fclose($file);
        }
        ?>
    </table>

    <h2>満足度の分布</h2>
    <canvas id="satisfactionChart"></canvas>
    <script>
        <?php
        $satisfactionData = array_fill(1, 5, 0);
        if (($file = fopen("responses.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($file)) !== FALSE) {
                $satisfactionData[$data[2]]++;
            }
            fclose($file);
        }
        ?>
        const ctx = document.getElementById('satisfactionChart').getContext('2d');
        const satisfactionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1', '2', '3', '4', '5'],
                datasets: [{
                    label: '満足度',
                    data: <?php echo json_encode(array_values($satisfactionData)); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
