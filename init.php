<?php
//set local timezone
date_default_timezone_set('Etc/UTC');

// Create a new database, if the file doesn't exist and open it for reading/writing.

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
$db->query ('CREATE TABLE IF NOT EXISTS devices (
    id SERIAL PRIMARY KEY,
    sn TEXT,
    comment VARCHAR,
    last_check timestamp without time zone,
    last_tx INTEGER,
    last_rx INTEGER
 )');


// Base table for detailed traffic
$db->query('CREATE TABLE IF NOT EXISTS traffic (
    id SERIAL PRIMARY KEY,
    device_id INTEGER,
    timestamp timestamp without time zone,
    tx INTEGER,
    rx INTEGER
)');


// Base table for detailed traffic for queues
$db->query('CREATE TABLE IF NOT EXISTS qtraffic (
    id SERIAL PRIMARY KEY,
    device_id INTEGER,
    timestamp timestamp without time zone,
    work INTEGER,
    entertainment INTEGER,
    default INTEGER
)');


