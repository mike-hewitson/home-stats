<html>
    <?php

    require("init.php");

    require("header.php");

    if (isset($_GET['id']) and is_numeric($_GET['id'])) {
        //get data for chart
        $getTraffic = $db->prepare('SELECT * FROM (SELECT * FROM qtraffic WHERE device_id = ? ORDER BY timestamp DESC LIMIT 48) AS bob ORDER BY timestamp');
        $getTraffic->bindValue(1, $_GET['id']);
        $getTraffic->execute();
        $chartData = '';
        $results = $getTraffic->fetchAll();
        foreach ($results as $res) {
            if(!isset($res['timestamp'])) continue;
            //set to Google Chart data format

            $date = date_create($res['timestamp']);
            date_timezone_set($date, timezone_open('Africa/Johannesburg'));
            $chartData .= "['".$date->format('d M H:i')."',"
                         .round(($res['work']/1024/1024),1).","
                         .round(($res['entertainment']/1024/1024),1).","
                         .round(($res['therest']/1024/1024),1).","
                         .round(($res['test']/1024/1024),1)."],";
        }

    ?>
    <head>
        <style>
         table, th, td {
             border: 1px solid black;
             border-collapse: collapse;
         }
         table {
             border-spacing: 5px;
             width:700px;
         }
         th, td {
             text-align: right;
             padding: 5px;
         }
        </style>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
         google.charts.load('current', {'packages':['bar']});
         google.charts.setOnLoadCallback(drawChart);

         function drawChart() {
             var data = google.visualization.arrayToDataTable([
                 ['Date/Time', 'Work (Mb)', 'Entertainment (Mb)', 'Default (Mb)', 'Test (Mb)'],
                 <?php echo $chartData;?>
             ]);

             var options = {
                 chart: {
                     title: 'Traffic Stats',
                     subtitle: 'Last 48 hours'
                 },
                 isStacked: true

             };

             var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

             chart.draw(data, google.charts.Bar.convertOptions(options));
         }
        </script>
    </head>
    <body>
        <div id="columnchart_material" style="width: 700px; height: 500px;"></div>
    </body>

    <?php

    require("stats.php");

    }
    ?>
</html>
