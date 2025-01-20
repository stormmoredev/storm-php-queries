<?php

require_once 'autoload.php';

use data\ConnectionProvider;

const CONNECTION_USER = "root";
const CONNECTION_PASS = "mariadb";
const CONNECTION_STRING = "mysql:host=localhost;port=7802;";


$connection = ConnectionProvider::getConnection();
$connection->execute("DROP DATABASE IF EXISTS storm_test");
$connection->execute("CREATE DATABASE storm_test");
$connection->execute("USE storm_test");

$schema = file_get_contents(__DIR__ . "/data/mariadb.sql");
$data = file_get_contents(__DIR__ . "/data/data.sql");

$connection->executeCommands($schema);
$connection->executeCommands($data);
