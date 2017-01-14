<?php
require_once '../php/db.php';

$db = new IronManDB();
//$all = $db->getAll();
$json = json_encode($all, JSON_PRETTY_PRINT);
echo '<pre>'.$json.'</pre>';
