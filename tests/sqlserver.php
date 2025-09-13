<?php

require_once 'autoload.php';

use data\ConnectionProvider;

const CONNECTION_USER = "sa";
const CONNECTION_PASS = "Sqlserver123";
const CONNECTION_STRING = "sqlsrv:server=localhost,7803";


$connection = ConnectionProvider::getConnection();
$connection->execute("DROP DATABASE IF EXISTS storm_test");
$connection->execute("CREATE DATABASE storm_test");
$connection->execute("USE storm_test");

$schema = file_get_contents(__DIR__ . "/data/sqlserver.sql");
$data = file_get_contents(__DIR__ . "/data/data.sql");

$connection->executeCommands($schema);
$connection->executeCommands($data);

