<?php

require_once 'autoload.php';

use data\ConnectionProvider;

const CONNECTION_USER = "postgres";
const CONNECTION_PASS = "postgres";
const CONNECTION_STRING = "pgsql:host=localhost;port=7800;dbname=postgres";


$connection = ConnectionProvider::getConnection();
$connection->execute("DROP SCHEMA IF EXISTS storm_test CASCADE");
$connection->execute("CREATE SCHEMA storm_test");
$connection->execute("SET search_path = storm_test");

$schema = file_get_contents(__DIR__ . "/data/postgres.sql");
$data = file_get_contents(__DIR__ . "/data/data.sql");

$connection->executeCommands($schema);
$connection->executeCommands($data);

