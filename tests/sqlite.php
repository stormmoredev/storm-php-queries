<?php

require_once 'autoload.php';

use data\ConnectionProvider;

$dbFilename = __DIR__ . "/../database.sqlite";
if (file_exists($dbFilename)) {
    unlink($dbFilename);
}
const CONNECTION_USER = "";
const CONNECTION_PASS = "";
const CONNECTION_STRING = "sqlite:".__DIR__."/../database.sqlite";

$connection = ConnectionProvider::getConnection();

$schema = file_get_contents(__DIR__ . "/data/sqlite.sql");
$data = file_get_contents(__DIR__ . "/data/data.sql");

$connection->executeCommands($schema);
$connection->executeCommands($data);

