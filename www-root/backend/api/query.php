<?php
require_once '../php/db.php';

$db = new IronManDB();
$all = $db->query($_GET);
$json = json_encode($all, JSON_PRETTY_PRINT);
echo '<pre>'.$json.'</pre>';
