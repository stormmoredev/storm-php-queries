<?php

require_once "data/ConnectionProvider.php";
require_once "data/helpers.php";

foreach (glob(__DIR__ . "/data/models/**/*.php") as $filename)
{
    require_once $filename;
}

foreach (glob(__DIR__ . "/data/models/*.php") as $filename)
{
    require_once $filename;
}