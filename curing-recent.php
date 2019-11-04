<html>
    <?php

    require("curing-init.php");

    $getReadings = $db->prepare('SELECT * FROM (SELECT * FROM readings ORDER BY id DESC LIMIT 720) AS bob ORDER BY id ASC');
    $getReadings->execute();

    $chartData = '';
    $results = $getReadings->fetchAll();

    foreach ($results as $res) {
        if(!isset($res['reading_timestamp'])) continue;
        //set to Google Chart data format

        $date = date_create($res['reading_timestamp']);
        date_timezone_set($date, timezone_open('Africa/Johannesburg'));
        $chartData .= "['".$date->format('d M H:i')."',"
                          .round(($res['temperature']) ,1).","
                          .round(($res['humidity']) ,1)."],";
    }
    ?>
    <head>
        <title>Curing Recent</title>
        <style>
         table, th, td {
             border: 1px solid black;
             border-collapse: collapse;
         }
         table {
             border-spacing: 5px;
             width:1000px;
         }
         th, td {
             text-align: right;
             padding: 5px;
         }
        </style>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                 ['Date/Time', 'Temperature', 'Humidity'],
                 <?php echo $chartData;?>
             ]);

            var options = {

                title: 'Curing Fridge - last 12 hours',
                width: 1000,
                height: 700,
                curveType: 'function',             };

             var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

             chart.draw(data, options);
         }
        </script>
    </head>
    <body>
        <div id="curve_chart" style="width: 1200px; height: 800px;"></div>
    </body>

    ?>
</html>
