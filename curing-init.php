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
// Table for readings
$db->query ('CREATE TABLE IF NOT EXISTS readings (
    id SERIAL PRIMARY KEY,
    sensor VARCHAR,
    reading_timestamp timestamp without time zone,
    temperature double precision,
    humidity double precision)');

// Prune older rows
$db->query ("DELETE FROM readings WHERE reading_timestamp < now() - interval '2 days'");


