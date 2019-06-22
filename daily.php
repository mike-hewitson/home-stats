<html>
    <?php

    require("init.php");

    require("header.php");

    if (isset($_GET['id']) and is_numeric($_GET['id'])) {
        //get data for chart
        $getDayTraffic = $db->prepare("SELECT day, (SELECT sum(work) FROM qtraffic WHERE timestamp >= day AND timestamp < day + interval '1 day') as sumwork, (SELECT sum(entertainment) FROM qtraffic WHERE timestamp >= day AND timestamp < day + interval '1 day') as sument, (SELECT sum(therest) FROM qtraffic WHERE timestamp >= day AND timestamp < day + interval '1 day') as sumtherest, (SELECT sum(test) FROM qtraffic WHERE device_id = ? AND timestamp >= day AND timestamp < day + interval '1 day') as sumtest FROM generate_series(CURRENT_DATE, CURRENT_DATE -31, '-1 day'::interval) day ORDER BY day");
        $getDayTraffic->bindValue(1, $_GET['id']);
        $getDayTraffic->execute();

        $chartData = '';
        $results = $getDayTraffic->fetchAll();

        foreach ($results as $res) {
            if(!isset($res['day'])) continue;
            //set to Google Chart data format
            $chartData .= "['".date('d M', strtotime($res['day']))."',"
                         .round(($res['sumwork']/1024/1024),2).","
                         .round(($res['sument']/1024/1024),2).","
                         .round(($res['sumtherest']/1024/1024),2).","
                         .round(($res['sumtest']/1024/1024),2)."],";
        }

    ?>
    <head>
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
                     subtitle: 'Last 30 days'
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
