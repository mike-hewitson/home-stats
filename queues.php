<html>
    <?php

    require("init.php");

    require("header.php");

    if (isset($_GET['id']) and is_numeric($_GET['id'])) {
        //get device info
        $getDevice = $db->prepare('SELECT * FROM devices WHERE id = ?');
        $getDevice->bindValue(1, $_GET['id']);
        $result = $getDevice->execute();

        $device = $getDevice->fetch(PDO::FETCH_ASSOC);
        echo "<strong>Device Serial: ".$device['sn']."</strong> (".$device['comment'].")<br/>";
        echo "Last check time: ".$device['last_check']." <br/>";
        echo "Last results loaded: Tx:".number_format(($device['last_tx']/1024/1024),1)." Mb, Rx : ".number_format(($device['last_rx']/1024/1024),1)." Mb <br/>";
        echo "<br/>";

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
                         .round(($res['therest']/1024/1024),1)."],";
        }

    ?>
    <head>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
         google.charts.load('current', {'packages':['bar']});
         google.charts.setOnLoadCallback(drawChart);

         function drawChart() {
             var data = google.visualization.arrayToDataTable([
                 ['Date/Time', 'Work (Mb)', 'Entertainment (Mb)', 'Default (Mb)'],
                 <?php echo $chartData;?>
             ]);

             var options = {
                 chart: {
                     title: 'Traffic Stats',
                     subtitle: 'Last 24 hours'
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



    //get summary stats

    //get this day so far stats
    //query the db
    $daily = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $daily->bindValue(1, $_GET['id']);
    $daily->bindValue(2, date('Y-m-d 00:00:00'));
    $daily->bindValue(3, date('Y-m-d 23:59:59'));
    $daily->execute();

    $dailyTraffic = $daily->fetch();

    //display results
    echo "<strong>Today</strong><br/>";
    echo "From: ".date('Y-m-d 00:00:00')." to ".date('Y-m-d 23:59:59')."<br/>";
    echo "Work: ".number_format(($dailyTraffic['sumwork']/1024/1024),1)." Mb, ";
    echo "Entertainment: ".number_format(($dailyTraffic['sument']/1024/1024),1)." Mb, ";
    echo "The rest: ".number_format(($dailyTraffic['sumtherest']/1024/1024),1)." Mb, ";
    echo "Total: ".number_format((($dailyTraffic['sumwork']+$dailyTraffic['sument']+$dailyTraffic['sumtherest'])/1024/1024),1)." Mb </br>";
    echo "<br/>";

    //get yesterday stats

    //query the db

    $yesterday = new DateTime();
    $yesterday->modify("-1 day");

    $daily = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $daily->bindValue(1, $_GET['id']);
    $daily->bindValue(2,  $yesterday->format('Y-m-d 00:00:00'));
    $daily->bindValue(3, $yesterday->format('Y-m-d 23:59:59'));
    $daily->execute();

    $dailyTraffic = $daily->fetch();

    //display results
    echo "<strong>Yesterday</strong><br/>";
    echo "From: ".$yesterday->format('Y-m-d 00:00:00')." to ".$yesterday->format('Y-m-d 23:59:59')."<br/>";
    echo "Work: ".number_format(($dailyTraffic['sumwork']/1024/1024),1)." Mb, ";
    echo "Entertainment: ".number_format(($dailyTraffic['sument']/1024/1024),1)." Mb, ";
    echo "The rest: ".number_format(($dailyTraffic['sumtherest']/1024/1024),1)." Mb, ";
    echo "Total: ".number_format((($dailyTraffic['sumwork']+$dailyTraffic['sument']+$dailyTraffic['sumtherest'])/1024/1024),1)." Mb </br>";
    echo "<br/>";


    //get weekly stats
    //getting sunday and saturday dates for current week
    $today = new DateTime();
    $currentWeekDay = $today->format('w');
    $firstdayofweek = clone $today;
    $lastdayofweek = clone $today;

    ($currentWeekDay != '0')?$firstdayofweek->modify('last Sunday'):'';
    ($currentWeekDay != '6')?$lastdayofweek->modify('next Saturday'):'';

    #echo $firstdayofweek->format('Y-m-d 00:00:00').' to '.$lastdayofweek->format('Y-m-d 23:59:59');

    //query the db
    $weekly = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $weekly->bindValue(1, $_GET['id']);
    $weekly->bindValue(2, $firstdayofweek->format('Y-m-d 00:00:00'));
    $weekly->bindValue(3, $lastdayofweek->format('Y-m-d 23:59:59'));
    $weekly->execute();
    #print_r($weeklyTraffic->fetchArray(SQLITE3_ASSOC));
    $weeklyTraffic = $weekly->fetch();
    //display results
    echo "<strong>This Week</strong><br/>";
    echo "From: ".$firstdayofweek->format('Y-m-d 00:00:00')." to ".$lastdayofweek->format('Y-m-d 23:59:59')."<br/>";
    echo "Work: ".number_format(($weeklyTraffic['sumwork']/1024/1024/1024),1)." Gb, ";
    echo "Entertainment: ".number_format(($weeklyTraffic['sument']/1024/1024/1024),1)." Gb, ";
    echo "The rest: ".number_format(($weeklyTraffic['sumtherest']/1024/1024/1024),1)." Gb, ";
    echo "Total: ".number_format((($weeklyTraffic['sumwork']+$weeklyTraffic['sument']+$weeklyTraffic['sumtherest'])/1024/1024/1024),1)." Gb </br>";
    echo "<br/>";

    //get monthly stats
    //query the db
    $monthly = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $monthly->bindValue(1, $_GET['id']);
    $monthly->bindValue(2, date('Y-m-01 00:00:00'));
    $monthly->bindValue(3, date('Y-m-t 23:59:59'));
    $monthly->execute();

    $monthlyTraffic = $monthly->fetch();
    //display results
    echo "<strong>This Month</strong><br/>";
    echo "From: ".date('Y-m-01 00:00:00')." to ".date('Y-m-t 23:59:59')."<br/>";
    echo "Work: ".number_format(($monthlyTraffic['sumwork']/1024/1024/1024),1)." Gb, ";
    echo "Entertainment: ".number_format(($monthlyTraffic['sument']/1024/1024/1024),1)." Gb, ";
    echo "The Rest: ".number_format(($monthlyTraffic['sumtherest']/1024/1024/1024),1)." Gb, ";
    echo "Total: ".number_format((($monthlyTraffic['sumwork']+$monthlyTraffic['sument']+$monthlyTraffic['sumtherest'])/1024/1024/1024),1)." Gb </br>";
    echo "<br/>";

    }
    else {
        $result = $db->query('SELECT * FROM devices');
        if(empty($result->fetchAll())) {
            echo "No devices found.<br/>";
        }
        else {
            $result = $db->query('SELECT * FROM devices');
            foreach ($result as $device) {
                echo '<a href="?id='.$device['id'].'"><strong>'.$device['sn'].'</strong></a> ('.$device['comment'].') Last check: '.$device['last_check'].'<br/>';
            }
        }
    }
    ?>
</html>
