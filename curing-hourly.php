<html>
    <?php

    require("curing-init.php");

    $getReadings = $db->prepare("with hours as (select generate_series(date_trunc('hour', now()) - '3 day'::interval, date_trunc('hour', now()),'1 hour'::interval) as hour)
                                 select hours.hour, avg(readings.temperature) as avg_temp, avg(readings.humidity) as avg_hum from hours
                                 left join readings on date_trunc('hour', readings.reading_timestamp) = hours.hour
                                 group by 1");
    $getReadings->execute();
    $results = $getReadings->fetchAll();

    $chartData1 = '';
    $chartData2 = '';

    foreach ($results as $res) {
        if(!isset($res['hour'])) continue;
        //set to Google Chart data format

        $date = date_create($res['hour']);
        date_timezone_set($date, timezone_open('Africa/Johannesburg'));
        $chartData1 .= "['".$date->format('d M H:i')."',"
                           .round(($res['avg_temp']) ,1)."],";
        $chartData2 .= "['".$date->format('d M H:i')."',"
                           .round(($res['avg_hum']) ,1)."],";
    }

    ?>
    <head>
        <title>Curing Averages</title>
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
        google.charts.setOnLoadCallback(drawChart1);
        google.charts.setOnLoadCallback(drawChart2);

        function drawChart1() {
            var data = google.visualization.arrayToDataTable([
                 ['Date/Time', 'Temperature'],
                 <?php echo $chartData1;?>
             ]);
            var options = {
                title: 'Curing Fridge Temperature - averages over last 3 days',
                width: 1000,
                height: 700,
                curveType: 'function'
            };
            var chart = new google.visualization.LineChart(document.getElementById('curve_chart1'));

            chart.draw(data, options);
         }

        function drawChart2() {
            var data = google.visualization.arrayToDataTable([
                 ['Date/Time', 'Humidity'],
                 <?php echo $chartData2;?>
             ]);
            var options = {
                title: 'Curing Fridge Humidity - averages over last 3 days',
                width: 1000,
                height: 700,
                curveType: 'function',
             };
             var chart = new google.visualization.LineChart(document.getElementById('curve_chart2'));

             chart.draw(data, options);
        }
        </script>
    </head>
    <body>
        <div id="curve_chart2" style="width: 1200px; height: 800px;"></div>
        <div id="curve_chart1" style="width: 1200px; height: 800px;"></div>
    </body>

    ?>
</html>
