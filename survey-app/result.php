<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アンケート結果</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            // PHPでデータを読み込む
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

            // 満足度のグラフ
            var satisfactionData = google.visualization.arrayToDataTable([
                ['満足度', '回答数'],
                <?php
                foreach ($satisfactionData as $satisfaction => $count) {
                    echo "['$satisfaction', $count],";
                }
                ?>
            ]);

            var satisfactionOptions = {
                title: '満足度の分布',
                hAxis: {title: '満足度'},
                vAxis: {minValue: 0}
            };

            var satisfactionChart = new google.visualization.ColumnChart(document.getElementById('satisfaction_chart'));
            satisfactionChart.draw(satisfactionData, satisfactionOptions);

            // 旅行回数のグラフ
            var travelFrequencyData = google.visualization.arrayToDataTable([
                ['旅行回数', '回答数'],
                <?php
                foreach ($travelFrequencyData as $frequency => $count) {
                    echo "['$frequency', $count],";
                }
                ?>
            ]);

            var travelFrequencyOptions = {
                title: '1年に旅行に行く回数の分布',
                hAxis: {title: '旅行回数'},
                vAxis: {minValue: 0}
            };

            var travelFrequencyChart = new google.visualization.ColumnChart(document.getElementById('travelFrequency_chart'));
            travelFrequencyChart.draw(travelFrequencyData, travelFrequencyOptions);

            // 旅行計画のグラフ
            var planningExtentData = google.visualization.arrayToDataTable([
                ['旅行計画', '回答数'],
                <?php
                foreach ($planningExtentData as $planning => $count) {
                    echo "['$planning', $count],";
                }
                ?>
            ]);

            var planningExtentOptions = {
                title: '旅行前にスケジュールをどの程度考えるかの分布',
                hAxis: {title: '旅行計画'},
                vAxis: {minValue: 0}
            };

            var planningExtentChart = new google.visualization.ColumnChart(document.getElementById('planningExtent_chart'));
            planningExtentChart.draw(planningExtentData, planningExtentOptions);
        }
    </script>
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
        <div id="satisfaction_chart" style="width: 100%; height: 500px;"></div>
        
        <h2>1年に旅行に行く回数の分布</h2>
        <div id="travelFrequency_chart" style="width: 100%; height: 500px;"></div>
        
        <h2>旅行前にスケジュールをどの程度考えるかの分布</h2>
        <div id="planningExtent_chart" style="width: 100%; height: 500px;"></div>
    </div>
</body>
</html>
