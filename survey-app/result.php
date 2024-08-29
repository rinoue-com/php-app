<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>アンケート結果</title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            packages: ['corechart', 'geochart'],
            mapsApiKey: 'YOUR_GOOGLE_MAPS_API_KEY'
        });
        google.charts.setOnLoadCallback(drawCharts);

        <?php
        // PHPでデータを読み込む
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

        $prefectureData = [];

        if (($file = fopen("responses.csv", "r")) !== FALSE) {
            // ヘッダ行を読み飛ばす
            fgetcsv($file);

            while (($data = fgetcsv($file)) !== FALSE) {
                $satisfactionData[$data[6]]++;
                $travelFrequencyData[$data[4]]++;
                $planningExtentData[$data[5]]++;

                // 都道府県名から「都」「府」「県」を除去
                $prefecture = str_replace(['都', '府', '県'], '', $data[3]);
                if (array_key_exists($prefecture, $prefectureData)) {
                    $prefectureData[$prefecture]++;
                } else {
                    $prefectureData[$prefecture] = 1;
                }
            }
            fclose($file);
        }
        ?>

        // グラフ描画関数
        function drawCharts() {
            drawSatisfactionChart('ColumnChart');
            drawTravelFrequencyChart('ColumnChart');
            drawPlanningExtentChart('ColumnChart');
            drawPrefectureMap();
        }

        // 満足度のグラフ描画
        function drawSatisfactionChart(chartType) {
            var data = google.visualization.arrayToDataTable([
                ['満足度', '回答数'],
                <?php
                foreach ($satisfactionData as $satisfaction => $count) {
                    echo "['$satisfaction', $count],";
                }
                ?>
            ]);

            var options = {
                title: '満足度の分布',
                hAxis: {title: '満足度'},
                vAxis: {minValue: 0}
            };

            var chart;
            if (chartType === 'PieChart') {
                chart = new google.visualization.PieChart(document.getElementById('satisfaction_chart'));
                document.getElementById('satisfactionPie').classList.add('active');
                document.getElementById('satisfactionColumn').classList.remove('active');
            } else {
                chart = new google.visualization.ColumnChart(document.getElementById('satisfaction_chart'));
                document.getElementById('satisfactionColumn').classList.add('active');
                document.getElementById('satisfactionPie').classList.remove('active');
            }
            chart.draw(data, options);
        }

        // 旅行回数のグラフ描画
        function drawTravelFrequencyChart(chartType) {
            var data = google.visualization.arrayToDataTable([
                ['旅行回数', '回答数'],
                <?php
                foreach ($travelFrequencyData as $frequency => $count) {
                    echo "['$frequency', $count],";
                }
                ?>
            ]);

            var options = {
                title: '1年に旅行に行く回数の分布',
                hAxis: {title: '旅行回数'},
                vAxis: {minValue: 0}
            };

            var chart;
            if (chartType === 'PieChart') {
                chart = new google.visualization.PieChart(document.getElementById('travelFrequency_chart'));
                document.getElementById('travelFrequencyPie').classList.add('active');
                document.getElementById('travelFrequencyColumn').classList.remove('active');
            } else {
                chart = new google.visualization.ColumnChart(document.getElementById('travelFrequency_chart'));
                document.getElementById('travelFrequencyColumn').classList.add('active');
                document.getElementById('travelFrequencyPie').classList.remove('active');
            }
            chart.draw(data, options);
        }

        // 旅行計画のグラフ描画
        function drawPlanningExtentChart(chartType) {
            var data = google.visualization.arrayToDataTable([
                ['旅行計画', '回答数'],
                <?php
                foreach ($planningExtentData as $planning => $count) {
                    echo "['$planning', $count],";
                }
                ?>
            ]);

            var options = {
                title: '旅行前にスケジュールをどの程度考えるかの分布',
                hAxis: {title: '旅行計画'},
                vAxis: {minValue: 0}
            };

            var chart;
            if (chartType === 'PieChart') {
                chart = new google.visualization.PieChart(document.getElementById('planningExtent_chart'));
                document.getElementById('planningExtentPie').classList.add('active');
                document.getElementById('planningExtentColumn').classList.remove('active');
            } else {
                chart = new google.visualization.ColumnChart(document.getElementById('planningExtent_chart'));
                document.getElementById('planningExtentColumn').classList.add('active');
                document.getElementById('planningExtentPie').classList.remove('active');
            }
            chart.draw(data, options);
        }

        // 都道府県別の分布マップ描画
        function drawPrefectureMap() {
            var data = google.visualization.arrayToDataTable([
                ['都道府県', '回答数'],
                <?php
                foreach ($prefectureData as $prefecture => $count) {
                    echo "['$prefecture', $count],";
                }
                ?>
            ]);

            var options = {
                region: 'JP',
                displayMode: 'regions',
                resolution: 'provinces',
                // colorAxis: {colors: ['#e7f0fa', '#08306b']}
            };

            var chart = new google.visualization.GeoChart(document.getElementById('prefecture_map'));
            chart.draw(data, options);
        }

        // グラフの種類を切り替える関数
        function toggleSatisfactionChart(type) {
            drawSatisfactionChart(type);
        }

        function toggleTravelFrequencyChart(type) {
            drawTravelFrequencyChart(type);
        }

        function togglePlanningExtentChart(type) {
            drawPlanningExtentChart(type);
        }

        // 表の表示/非表示を切り替える関数
        function toggleTable() {
            var table = document.getElementById("dataTable");
            var button = document.getElementById("toggleButton");
            if (table.style.display === "none") {
                table.style.display = "table";
                button.textContent = "データを非表示";
            } else {
                table.style.display = "none";
                button.textContent = "データを表示";
            }
        }
    </script>
</head>
<body>
    <div class="result-container">
        <h1>アンケート結果</h1>

        <!-- 表の表示/非表示切り替えボタン -->
        <button id="toggleButton" onclick="toggleTable()">データを非表示</button>
        <table id="dataTable" border="1">
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
                // ヘッダ行を読み飛ばす
                fgetcsv($file);

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
        <button id="satisfactionColumn" class="chart-toggle-btn active" onclick="toggleSatisfactionChart('ColumnChart')">棒グラフ</button>
        <button id="satisfactionPie" class="chart-toggle-btn" onclick="toggleSatisfactionChart('PieChart')">円グラフ</button>
        <div id="satisfaction_chart" style="width: 100%; height: 500px;"></div>
        
        <h2>1年に旅行に行く回数の分布</h2>
        <button id="travelFrequencyColumn" class="chart-toggle-btn active" onclick="toggleTravelFrequencyChart('ColumnChart')">棒グラフ</button>
        <button id="travelFrequencyPie" class="chart-toggle-btn" onclick="toggleTravelFrequencyChart('PieChart')">円グラフ</button>
        <div id="travelFrequency_chart" style="width: 100%; height: 500px;"></div>
        
        <h2>旅行前にスケジュールをどの程度考えるかの分布</h2>
        <button id="planningExtentColumn" class="chart-toggle-btn active" onclick="togglePlanningExtentChart('ColumnChart')">棒グラフ</button>
        <button id="planningExtentPie" class="chart-toggle-btn" onclick="togglePlanningExtentChart('PieChart')">円グラフ</button>
        <div id="planningExtent_chart" style="width: 100%; height: 500px;"></div>

        <h2>都道府県別の分布</h2>
        <div id="prefecture_map" style="width: 100%; height: 600px;"></div>
    </div>
</body>
</html>
