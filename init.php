<?php
//set local timezone
date_default_timezone_set('Etc/UTC');

// Create a new database, if the file doesn't exist and open it for reading/writing.

echo 'My username is ' . $_ENV["DATABASE_URL"] . '!';

// $conn = pg_connect(getenv("DATABASE_URL"));

// The extension of the file is arbitrary.
$url = parse_url(getenv("DATABASE_URL"));

$db = new PDO("pgsql:" . sprintf(
     "host=%s;port=%s;user=%s;password=%s;dbname=%s",
     $url["host"],
     $url["port"],
     $url["user"],
     $url["pass"],
     ltrim($url["path"], "/")
 ));

// Create tables.
// Base table for devices
$result=$db->query ('CREATE TABLE IF NOT EXISTS devices (
    id SERIAL PRIMARY KEY,
    sn TEXT,
    comment VARCHAR,
    last_check DATE,
    last_tx INTEGER,
    last_rx INTEGER
 )');


// Base table for detailed traffic
$result=$db->query('CREATE TABLE IF NOT EXISTS traffic (
    id SERIAL PRIMARY KEY,
    device_id INTEGER,
    timestamp DATE,
    tx INTEGER,
    rx INTEGER
)');



echo 'stuff' . $result;

