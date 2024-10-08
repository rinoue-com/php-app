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
        });
        google.charts.setOnLoadCallback(drawCharts);

        <?php
        // ページングの設定
        $rowsPerPage = 10; // 1ページに表示する行数
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $rowsPerPage;

        // 全データを読み込み、該当ページのデータだけを表示
        $allData = [];
        if (($file = fopen("responses.csv", "r")) !== FALSE) {
            fgetcsv($file); // ヘッダをスキップ
            while (($data = fgetcsv($file)) !== FALSE) {
                $allData[] = $data;
            }
            fclose($file);
        }
        $totalRows = count($allData);
        $totalPages = ceil($totalRows / $rowsPerPage);
        $pageData = array_slice($allData, $offset, $rowsPerPage);

        // グラフ描画用データの初期化
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

        foreach ($allData as $data) {
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
        ?>

        // グラフの描画関数
        function drawCharts() {
            drawSatisfactionChart('ColumnChart');
            drawTravelFrequencyChart('ColumnChart');
            drawPlanningExtentChart('ColumnChart');
            drawPrefectureMap();
        }

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
                resolution: 'provinces'
            };

            var chart = new google.visualization.GeoChart(document.getElementById('prefecture_map'));
            chart.draw(data, options);
        }

        function toggleSatisfactionChart(type) {
            drawSatisfactionChart(type);
        }

        function toggleTravelFrequencyChart(type) {
            drawTravelFrequencyChart(type);
        }

        function togglePlanningExtentChart(type) {
            drawPlanningExtentChart(type);
        }

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

        function downloadCSV() {
            const link = document.createElement('a');
            link.href = 'responses.csv';
            link.download = 'responses.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</head>
<body>
    <div class="result-container">
        <h1>アンケート結果</h1>

        <!-- 表の表示/非表示切り替えボタン -->
        <button id="toggleButton" onclick="toggleTable()">データを非表示</button>
        <button id="downloadButton" onclick="downloadCSV()">CSVダウンロード</button>

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
                <th>詳細</th>
            </tr>
            <?php
            foreach ($pageData as $index => $data) {
                echo "<tr>";
                foreach ($data as $key => $field) {
                    echo "<td class='hidden'>" . htmlspecialchars($field) . "</td>";
                }
                // 詳細ボタンを追加
                echo "<td><a href='detail.php?row=" . ($offset + $index) . "'>詳細</a></td>";
                echo "</tr>";
            }
            ?>
        </table>

        <!-- ページングのリンク -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $currentPage): ?>
                    <span class="current-page"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>

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
