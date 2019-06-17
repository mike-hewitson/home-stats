<html>
    <?php

    require("init.php");

    if (isset($_GET['id']) and is_numeric($_GET['id'])) {
        //get device info
        $getDevice = $db->prepare('SELECT * FROM devices WHERE id = ?');
        $getDevice->bindValue(1, $_GET['id']);
        $result = $getDevice->execute();

        $device = $getDevice->fetch(PDO::FETCH_ASSOC);
        echo "<strong>Device Serial: ".$device['sn']."</strong> (".$device['comment'].")<br/>";
        echo "Last check time: ".$device['last_check']." <br/>";
        echo "Last results: TX:".round(($device['last_tx']/1024/1024),2)." Mb RX : ".round(($device['last_rx']/1024/1024),2)." Mb <br/>";
        echo "<br/>";

        //get data for chart
        $getTraffic = $db->prepare('SELECT timestamp, tx, rx FROM traffic WHERE device_id = ? ORDER BY timestamp DESC LIMIT 24');
        $getTraffic->bindValue(1, $_GET['id']);
        $getTraffic->execute();
        $chartData = '';
        $results = $getTraffic->fetchAll();

        foreach ($results as $res) {
            if(!isset($res['timestamp'])) continue;
            //set to Google Chart data format
            $chartData .= "['".date('d M H:i', strtotime($res['timestamp']))."',".round(($res['tx']/1024/1024),2).",".round(($res['rx']/1024/1024),2)."],";
        }

    ?>
    <head>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
         google.charts.load('current', {'packages':['bar']});
         google.charts.setOnLoadCallback(drawChart);

         function drawChart() {
             var data = google.visualization.arrayToDataTable([
                 ['Date/Time', 'TX (Mb)', 'RX (Mb)'],
                 <?php echo $chartData;?>
             ]);

             var options = {
                 chart: {
                     title: 'Traffic Stats',
                     subtitle: 'Last 24 hours',
                 }
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

    //get daily stats
    //query the db
    $daily = $db->prepare('SELECT sum(tx) as sumtx, sum(rx) as sumrx FROM traffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $daily->bindValue(1, $_GET['id']);
    $daily->bindValue(2, date('Y-m-d 00:00:00'));
    $daily->bindValue(3, date('Y-m-d 23:59:59'));
    $daily->execute();

    $dailyTraffic = $daily->fetch();

    //display results
    echo "<strong>Today</strong><br/>";
    echo "From: ".date('Y-m-d 00:00:00')." to ".date('Y-m-d 23:59:59')."<br/>";
    echo "TX: ".round(($dailyTraffic['sumtx']/1024/1024),2)." Mb ";
    echo "RX: ".round(($dailyTraffic['sumrx']/1024/1024),2)." Mb ";
    echo "Total: ".round((($dailyTraffic['sumtx']+$dailyTraffic['sumrx'])/1024/1024),2)." Mb </br>";
    echo "<br/>";


    $yesterday = new DateTime();
    $yesterday->modify("-1 day");

    //get yesterday stats
    //query the db
    $daily = $db->prepare('SELECT sum(tx) as sumtx, sum(rx) as sumrx FROM traffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $daily->bindValue(1, $_GET['id']);
    $daily->bindValue(2,  $yesterday->format('Y-m-d 00:00:00'));
    $daily->bindValue(3, $yesterday->format('Y-m-d 23:59:59'));
    $daily->execute();

    $dailyTraffic = $daily->fetch();

    //display results
    echo "<strong>Yesterday</strong><br/>";
    echo "From: ".$yesterday->format('Y-m-d 00:00:00')." to ".$yesterday->format('Y-m-d 23:59:59')."<br/>";
    echo "TX: ".round(($dailyTraffic['sumtx']/1024/1024),2)." Mb ";
    echo "RX: ".round(($dailyTraffic['sumrx']/1024/1024),2)." Mb ";
    echo "Total: ".round((($dailyTraffic['sumtx']+$dailyTraffic['sumrx'])/1024/1024),2)." Mb </br>";
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
    $weekly = $db->prepare('SELECT sum(tx) as sumtx, sum(rx) as sumrx FROM traffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $weekly->bindValue(1, $_GET['id']);
    $weekly->bindValue(2, $firstdayofweek->format('Y-m-d 00:00:00'));
    $weekly->bindValue(3, $lastdayofweek->format('Y-m-d 23:59:59'));
    $weekly->execute();
    #print_r($weeklyTraffic->fetchArray(SQLITE3_ASSOC));
    $weeklyTraffic = $weekly->fetch();
    //display results
    echo "<strong>This Week</strong><br/>";
    echo "From: ".$firstdayofweek->format('Y-m-d 00:00:00')." to ".$lastdayofweek->format('Y-m-d 23:59:59')."<br/>";
    echo "TX: ".round(($weeklyTraffic['sumtx']/1024/1024),2)." Mb ";
    echo "RX: ".round(($weeklyTraffic['sumrx']/1024/1024),2)." Mb ";
    echo "Total: ".round((($weeklyTraffic['sumtx']+$weeklyTraffic['sumrx'])/1024/1024),2)." Mb </br>";
    echo "<br/>";

    //get monthly stats
    //query the db
    $monthly = $db->prepare('SELECT sum(tx) as sumtx, sum(rx) as sumrx FROM traffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $monthly->bindValue(1, $_GET['id']);
    $monthly->bindValue(2, date('Y-m-01 00:00:00'));
    $monthly->bindValue(3, date('Y-m-t 23:59:59'));
    $monthly->execute();

    $monthlyTraffic = $monthly->fetch();
    //display results
    echo "<strong>This Month</strong><br/>";
    echo "From: ".date('Y-m-01 00:00:00')." to ".date('Y-m-t 23:59:59')."<br/>";
    echo "TX: ".round(($monthlyTraffic['sumtx']/1024/1024),2)." Mb ";
    echo "RX: ".round(($monthlyTraffic['sumrx']/1024/1024),2)." Mb ";
    echo "Total: ".round((($monthlyTraffic['sumtx']+$monthlyTraffic['sumrx'])/1024/1024),2)." Mb </br>";
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
