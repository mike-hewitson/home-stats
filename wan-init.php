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
    ltrim($url["path"], "/")));

// Create tables.
// Base table for devices
$db->query ('CREATE TABLE IF NOT EXISTS devices (
    id SERIAL PRIMARY KEY,
    sn TEXT,
    comment VARCHAR,
    last_check timestamp without time zone,
    last_tx bigint,
    last_rx bigint)');


// Base table for detailed traffic
$db->query('CREATE TABLE IF NOT EXISTS traffic (
    id SERIAL PRIMARY KEY,
    device_id INTEGER,
    timestamp timestamp without time zone,
    tx bigint,
    rx bigint)');


// Base table for detailed traffic for queues
$db->query('CREATE TABLE IF NOT EXISTS qtraffic (
    id SERIAL PRIMARY KEY,
    device_id INTEGER,
    timestamp timestamp without time zone,
    work bigint,
    entertainment bigint,
    therest bigint,
    test bigint)');

// Prune older rows
$db->query ("DELETE FROM qtraffic WHERE timestamp < now() - interval '2 monthss'");
$db->query ("DELETE FROM traffic WHERE timestamp < now() - interval '2 monthss'");



