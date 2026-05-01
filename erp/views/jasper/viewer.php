<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ini_set('display_errors', 0);
session_start();
require_once("../../models/config.php");
include_once('../../models/librerias/PHPJasperLibrary09/class/tcpdf/tcpdf.php');
include_once("../../models/librerias/PHPJasperLibrary09/class/PHPJasperXML.inc.php");
$server = Config::$host;
$db = $_SESSION['dbProject'];
$user = Config::$userDB;
$pass = Config::$pwdDB;

$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true;
//$PHPJasperXML->arrayParameter = array("parameter1" => 1);
$PHPJasperXML->load_xml_file("productos_3.jrxml");

$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>
