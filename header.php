<?php
if (isset($_GET['id']) and is_numeric($_GET['id'])) {
    //get device info
    $getDevice = $db->prepare('SELECT * FROM devices WHERE id = ?');
    $getDevice->bindValue(1, $_GET['id']);
    $result = $getDevice->execute();

    $device = $getDevice->fetch(PDO::FETCH_ASSOC);
    $date = date_create($device['last_check']);
    date_timezone_set($date, timezone_open('Africa/Johannesburg'));

    echo "<strong>Device Serial: ".$device['sn']."</strong> (".$device['comment'].")<br/>";
    echo "Last check time: ".$date->format('d M H:i:sP')." <br/>";
    echo "Last results loaded: Tx: ".number_format(($device['last_tx']/1024/1024),1)." Mb, Rx : ".number_format(($device['last_rx']/1024/1024),1)." Mb <br/>";
    echo "<br/>";
};
