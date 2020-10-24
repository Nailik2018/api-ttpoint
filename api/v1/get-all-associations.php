<?php

require_once "../../collections/DataBase.php";

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$db = new DataBase();
$db->connection();

print_r($db->sqlSelectStatement("SELECT * FROM association"))

?>