<?php

    if (isset($_GET['id']) and is_numeric($_GET['id'])) {

       echo '<table>
      <tr>
        <th>Scope</th>
        <th>From</th>
        <th>To</th>
        <th>Work</th>
        <th>Entertainment</th>
        <th>Default</th>
        <th>Test</th>
        <th>Total</th>
      </tr>';

    //get summary stats

    //get this day so far stats
    //query the db
    $daily = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest, sum(test) as sumtest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $daily->bindValue(1, $_GET['id']);
    $daily->bindValue(2, date('Y-m-d 00:00:00'));
    $daily->bindValue(3, date('Y-m-d 23:59:59'));
    $daily->execute();
    $dailyTraffic = $daily->fetch();

    //display results
    echo "<tr><td><strong>Today</strong></td>";
    echo "<td>".date('Y-m-d')."</td><td>".date('Y-m-d')."</td>";
    echo "<td>".number_format(($dailyTraffic['sumwork']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($dailyTraffic['sument']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($dailyTraffic['sumtherest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($dailyTraffic['sumtest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format((($dailyTraffic['sumwork']+$dailyTraffic['sument']+$dailyTraffic['sumtherest']+$dailyTraffic['sumtest'])/1024/1024/1024),1)."</td>";
    echo "</tr>";

    //get yesterday stats

    //query the db

    $yesterday = new DateTime();
    $yesterday->modify("-1 day");

    $daily = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest, sum(test) as sumtest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $daily->bindValue(1, $_GET['id']);
    $daily->bindValue(2,  $yesterday->format('Y-m-d 00:00:00'));
    $daily->bindValue(3, $yesterday->format('Y-m-d 23:59:59'));
    $daily->execute();
    $dailyTraffic = $daily->fetch();

      //display results
    echo "<tr><td><strong>Yesterday</strong></td>";
    echo "<td>".$yesterday->format('Y-m-d')."</td><td>".$yesterday->format('Y-m-d')."</td>";
    echo "<td>".number_format(($dailyTraffic['sumwork']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($dailyTraffic['sument']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($dailyTraffic['sumtherest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($dailyTraffic['sumtest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format((($dailyTraffic['sumwork']+$dailyTraffic['sument']+$dailyTraffic['sumtherest']+$dailyTraffic['sumtest'])/1024/1024/1024),1)."</td>";
    echo "</tr>";

    //get weekly stats
    //getting sunday and saturday dates for current week
    $today = new DateTime();
    $currentWeekDay = $today->format('w');
    $firstdayofweek = clone $today;
    $lastdayofweek = clone $today;

    ($currentWeekDay != '1')?$firstdayofweek->modify('last Monday'):'';
    ($currentWeekDay != '0')?$lastdayofweek->modify('next Sunday'):'';

    //query the db
    $weekly = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest, sum(test) as sumtest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $weekly->bindValue(1, $_GET['id']);
    $weekly->bindValue(2, $firstdayofweek->format('Y-m-d 00:00:00'));
    $weekly->bindValue(3, $lastdayofweek->format('Y-m-d 23:59:59'));
    $weekly->execute();
    $weeklyTraffic = $weekly->fetch();

    echo "<tr><td><strong>This Week</strong></td>";
    echo "<td>".$firstdayofweek->format('Y-m-d')."</td><td>".$lastdayofweek->format('Y-m-d')."</td>";
    echo "<td>".number_format(($weeklyTraffic['sumwork']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($weeklyTraffic['sument']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($weeklyTraffic['sumtherest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($weeklyTraffic['sumtest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format((($weeklyTraffic['sumwork']+$weeklyTraffic['sument']+$weeklyTraffic['sumtherest']+$weeklyTraffic['sumtest'])/1024/1024/1024),1)."</td>";
    echo "</tr>";


    //get last weekly stats
    //getting sunday and saturday dates for current week
    $firstdayofweek->modify('-7 days');
    $lastdayofweek->modify('-7 days');

    //query the db
    $weekly = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest, sum(test) as sumtest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $weekly->bindValue(1, $_GET['id']);
    $weekly->bindValue(2, $firstdayofweek->format('Y-m-d 00:00:00'));
    $weekly->bindValue(3, $lastdayofweek->format('Y-m-d 23:59:59'));
    $weekly->execute();
    $weeklyTraffic = $weekly->fetch();

    //display results
    echo "<tr><td><strong>Last Week</strong></td>";
    echo "<td>".$firstdayofweek->format('Y-m-d')."</td><td>".$lastdayofweek->format('Y-m-d')."</td>";
    echo "<td>".number_format(($weeklyTraffic['sumwork']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($weeklyTraffic['sument']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($weeklyTraffic['sumtherest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($weeklyTraffic['sumtest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format((($weeklyTraffic['sumwork']+$weeklyTraffic['sument']+$weeklyTraffic['sumtherest']+$weeklyTraffic['sumtest'])/1024/1024/1024),1)."</td>";
    echo "</tr>";

    //get monthly stats
    //query the db
    $monthly = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest, sum(test) as sumtest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $monthly->bindValue(1, $_GET['id']);
    $monthly->bindValue(2, date('Y-m-01 00:00:00'));
    $monthly->bindValue(3, date('Y-m-t 23:59:59'));
    $monthly->execute();
    $monthlyTraffic = $monthly->fetch();

    echo "<br/>";

    //display results
    echo "<tr><td><strong>This Month</strong></td>";
    echo "<td>".date('Y-m-01')."</td><td>".date('Y-m-t')."</td>";
    echo "<td>".number_format(($monthlyTraffic['sumwork']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($monthlyTraffic['sument']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($monthlyTraffic['sumtherest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($monthlyTraffic['sumtest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format((($monthlyTraffic['sumwork']+$monthlyTraffic['sument']
                                +$monthlyTraffic['sumtherest']+$monthlyTraffic['sumtest'])/1024/1024/1024),1)."</td>";
    echo "</tr>";

    //get last months stats
    //query the db

    $beginningoflastmonth = clone $today;
    $beginningoflastmonth->modify('first day of last month');
    $endoflastmonth = clone $today;
    $endoflastmonth->modify('last day of last month');
    $monthly = $db->prepare('SELECT sum(work) as sumwork, sum(entertainment) as sument, sum(therest) as sumtherest, sum(test) as sumtest FROM qtraffic WHERE device_id = ? AND timestamp >= ? AND timestamp <= ?');
    $monthly->bindValue(1, $_GET['id']);
    $monthly->bindValue(2, $beginningoflastmonth->format('Y-m-d 00:00:00'));
    $monthly->bindValue(3, $endoflastmonth->format('Y-m-d 23:59:59'));
    $monthly->execute();
    $monthlyTraffic = $monthly->fetch();

    //display results
    echo "<tr><td><strong>Last Month</strong></td>";
    echo "<td>".$beginningoflastmonth->format('Y-m-01')."</td><td>"
               .$endoflastmonth->format('Y-m-t')."</td>";
    echo "<td>".number_format(($monthlyTraffic['sumwork']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($monthlyTraffic['sument']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($monthlyTraffic['sumtherest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format(($monthlyTraffic['sumtest']/1024/1024/1024),1)."</td>";
    echo "<td>".number_format((($monthlyTraffic['sumwork']+$monthlyTraffic['sument']
                                +$monthlyTraffic['sumtherest']+$monthlyTraffic['sumtest'])/1024/1024/1024),1)."</td>";
    echo "</tr></table>";

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
    };
