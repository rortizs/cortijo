<?php

/** bi controller
 * 
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/bi.php");
$bi = new BI();
$service = $_REQUEST['service'];
switch ($service) {
    case 'ventas':
        $params = $data = $bi->ventas($_REQUEST);
        echo json_encode($data);
        break;
}

