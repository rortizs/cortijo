<?php

session_start();
require_once("../../models/config.php");
require_once('../../models/librerias/PHPJasperLibrary09/class/tcpdf/tcpdf.php');
require_once("../../models/librerias/PHPJasperLibrary09/class/PHPJasperXML.inc.php");
require_once("../../models/caja.php");
$caja = new Caja();
//database connection details
$server = Config::$host;
$db = $_SESSION['dbProject'];
$user = Config::$userDB;
$pass = Config::$pwdDB;
//display errors should be off in the php.ini file
ini_set('display_errors', 0);
//setting the path to the created jrxml file
switch ($_REQUEST['modulo']) {
    case 'ticket':
        $xml = simplexml_load_file("../jasper/ticket.jrxml");
        break;
    case 'normal':
        $xml = simplexml_load_file("../jasper/felINFILE.jrxml");
        break;
}
//
$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter = array("idVenta" => $_REQUEST['idVenta'], "reimpresion" => $_REQUEST['reimpresion']);
$PHPJasperXML->xml_dismantle($xml);
//
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>