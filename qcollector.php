<?php
/*
Usage: /qcollector.php?sn=<SERIAL NUMBER>&q1=<Q1 BYTES>&q2=<Q2 BYTES>&q3=<Q2 BYTES>
*/

require("init.php");

// Check input data
if (isset($_GET['sn'])
    and isset($_GET['q1'])
    and isset($_GET['q2'])
    and isset($_GET['q3'])
    and isset($_GET['q4'])){
    $device_serial = substr($_GET['sn'], 0, 12);
} else {
    echo 'fail';
    exit;
}

print("serial " . $device_serial);

// Check if device exists
$getDevice = $db->prepare("SELECT id, sn, comment FROM devices WHERE sn='".$device_serial."'");
$getDevice->execute();
$device = $getDevice->fetch(PDO::FETCH_ASSOC);

$q1 = explode('/',  $_GET['q1']);
$q2 = explode('/',  $_GET['q2']);
$q3 = explode('/',  $_GET['q3']);
$q4 = explode('/',  $_GET['q4']);


$work = $q1[0] + $q1[1];
$entertainment = $q2[0] + $q2[1];
$therest = $q3[0] + $q3[1];
$test = $q4[0] + $q4[1];

// $device = $result->fetchArray(SQLITE3_ASSOC);
if (empty($device)) {
    //Add new device
    $addDevice = $db->prepare('INSERT INTO devices (sn, last_check, last_tx, last_rx)
    VALUES (:serial, :time, :tx, :rx)');
    $addDevice->bindValue(':serial', $device_serial);
    $addDevice->bindValue(':time', date('Y-m-d H:i:s'));
    $addDevice->bindValue(':tx', $q1[0]);
    $addDevice->bindValue(':rx', $q1[1]);
    $addDevice->execute();
    $device['id'] = $db->lastInsertId();
}
else {
    //Update last received data
    $updateData = $db->prepare('UPDATE devices SET last_check=:time, last_tx=:tx, last_rx=:rx WHERE id=:id');
    $updateData->bindValue(':id', $device['id']);
    $updateData->bindValue(':time', date('Y-m-d H:i:s'));
    $updateData->bindValue(':tx', $q1[0]);
    $updateData->bindValue(':rx', $q1[1]);
    $updateData->execute();
}

//Update traffic data
$updateTraffic = $db->prepare('INSERT INTO qtraffic (device_id, timestamp, work, entertainment, therest, test)
    VALUES (:id, :time, :work, :entertainment, :therest, :test)');
$updateTraffic->bindValue(':id', $device['id']);
$updateTraffic->bindValue(':time', date('Y-m-d H:i:s'));
$updateTraffic->bindValue(':work', $work);
$updateTraffic->bindValue(':entertainment', $entertainment);
$updateTraffic->bindValue(':therest', $therest);
$updateTraffic->bindValue(':test', $test);
$updateTraffic->execute();

echo 'traffic data updated';
