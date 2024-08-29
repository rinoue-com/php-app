<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アンケート結果</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="result-container">
        <h1>アンケート結果</h1>
        <table border="1">
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>年代</th>
                <th>住んでいる都道府県</th>
                <th>旅行回数</th>
                <th>旅行計画</th>
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
        
        <h2>1年に旅行に行く回数の分布</h2>
        <canvas id="travelFrequencyChart"></canvas>
        
        <h2>旅行前にスケジュールをどの程度考えるかの分布</h2>
        <canvas id="planningExtentChart"></canvas>
        
        <script>
            <?php
            $satisfactionData = [
                "非常に満足" => 0,
                "満足" => 0,
                "普通" => 0,
                "不満" => 0,
                "非常に不満" => 0
            ];
            
            $travelFrequencyData = [
                "1回未満" => 0,
                "1〜2回" => 0,
                "3〜4回" => 0,
                "5回以上" => 0
            ];
            
            $planningExtentData = [
                "全く考えない" => 0,
                "少し考える" => 0,
                "かなり考える" => 0,
                "細かく計画する" => 0
            ];
            
            if (($file = fopen("responses.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($file)) !== FALSE) {
                    $satisfactionData[$data[6]]++;
                    $travelFrequencyData[$data[4]]++;
                    $planningExtentData[$data[5]]++;
                }
                fclose($file);
            }
            ?>
            
            const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
            const satisfactionChart = new Chart(satisfactionCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(<?php echo json_encode($satisfactionData); ?>),
                    datasets: [{
                        label: '満足度',
                        data: Object.values(<?php echo json_encode($satisfactionData); ?>),
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

            const travelFrequencyCtx = document.getElementById('travelFrequencyChart').getContext('2d');
            const travelFrequencyChart = new Chart(travelFrequencyCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(<?php echo json_encode($travelFrequencyData); ?>),
                    datasets: [{
                        label: '旅行回数',
                        data: Object.values(<?php echo json_encode($travelFrequencyData); ?>),
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
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

            const planningExtentCtx = document.getElementById('planningExtentChart').getContext('2d');
            const planningExtentChart = new Chart(planningExtentCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(<?php echo json_encode($planningExtentData); ?>),
                    datasets: [{
                        label: '旅行計画',
                        data: Object.values(<?php echo json_encode($planningExtentData); ?>),
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
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
    </div>
</body>
</html>
