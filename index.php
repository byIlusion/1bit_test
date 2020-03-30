<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$conf = json_decode(file_get_contents("config.json"));
$conf->root = dirname(__FILE__);

include_once "app/app.php";
$app = new App($conf);
$app->run();

$app->end();

?>