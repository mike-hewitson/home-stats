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
$db->query ('CREATE TABLE IF NOT EXISTS "devices" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "sn" TEXT,
    "comment" VARCHAR,
    "last_check" DATETIME,
    "last_tx" INT,
    "last_rx" INT
)');

// Base table for detailed traffic
$db->query('CREATE TABLE IF NOT EXISTS "traffic" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "device_id" INT,
    "timestamp" DATETIME,
    "tx" INT,
    "rx" INT
)');

// $query = "INSERT INTO book VALUES ('$_POST[bookid]','$_POST[book_name]',
// '$_POST[author]','$_POST[publisher]','$_POST[dop]',
// '$_POST[price]')";

// $result = pg_query($query1);

// echo 'result ' . $result;
