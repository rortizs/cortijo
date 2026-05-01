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
$xml = simplexml_load_file("consultaFacturacion.jrxml");

if ($_REQUEST['tipoFacturacion'] == "") {
    $_REQUEST['tipoFacturacion'] = 0;
}
if ($_REQUEST['documento'] == "") {
    $_REQUEST['documento'] = 0;
}
if ($_REQUEST['pagare'] == "") {
    $_REQUEST['pagare'] = 0;
}
if ($_REQUEST['vendedor'] == "") {
    $_REQUEST['vendedor'] = 0;
}

if ($_REQUEST['tipoVenta'] == 1) {
    $tipoVenta = "'Contado'";
} else if ($_REQUEST['tipoVenta'] == 2) {
    $tipoVenta = "'Credito'";
} else {
    $tipoVenta = '0';
}

if ($_REQUEST['cliente'] == "") {
    $_REQUEST['cliente'] = 0;
}
$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true; 
$PHPJasperXML->arrayParameter = array("fechaInicio" => date("Y-m-d", strtotime($_REQUEST['fechaInicio'])), "fechaFin" => date("Y-m-d", strtotime($_REQUEST['fechaFin'])),
    "fechaInicio2" => $_REQUEST['fechaInicio'], "fechaFin2" => $_REQUEST['fechaFin'], "pagare" => $_REQUEST['pagare'],
    "proveedor" => $_REQUEST['proveedor'], "vendedor" => $_REQUEST['vendedor'], "cliente" => $_REQUEST['cliente'],
    "documento" => $_REQUEST['documento'], "tipoFacturacion" => $_REQUEST['tipoFacturacion'], "tipoVenta" => $tipoVenta);
$PHPJasperXML->xml_dismantle($xml);
//echo var_dump($PHPJasperXML);
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>