<?php
//set local timezone
date_default_timezone_set('Etc/UTC');

// Create a new database, if the file doesn't exist and open it for reading/writing.
echo 'My username is ' . $_ENV["DATABASE_URL"] . '!';

// The extension of the file is arbitrary.
$db = new PDO('uir:' . $_ENV["DATABASE_URL"]);

// Create tables.
// Base table for devices
$db->query('CREATE TABLE IF NOT EXISTS "devices" (
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
