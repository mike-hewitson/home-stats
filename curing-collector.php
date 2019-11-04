<?php
/*
Usage: /qcollector.php?s=<SENSOR>&t=<TEMPERATURE>&h=<HUMIDITY>
*/

require("curing-init.php");

// Check input data
if (isset($_GET['s'])
    and isset($_GET['t'])
    and isset($_GET['h'])){
    $sensor = substr($_GET['s'], 0, 12);
} else {
    echo 'fail';
    exit;
}

$t = $_GET['t'];
$h = $_GET['h'];

print("sensor " . $sensor . " t " . $t . " h ". $h);

//Insert reading data
$insertReading = $db->prepare('INSERT INTO readings (sensor, reading_timestamp, temperature, humidity)
    VALUES (:sensor, :time, :temperature, :humidity)');
$insertReading->bindValue(':sensor', $sensor);
$insertReading->bindValue(':time', date('Y-m-d H:i:s'));
$insertReading->bindValue(':temperature', $t);
$insertReading->bindValue(':humidity', $h);
$insertReading->execute();


echo ' reading data added';
