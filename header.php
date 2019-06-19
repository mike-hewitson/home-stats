<?php
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
    };
 };
