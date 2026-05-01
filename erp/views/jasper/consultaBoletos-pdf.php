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
 $tipoPago= 'like "%"';
$xml = simplexml_load_file("consultaBoletos.jrxml");
if ($_REQUEST['noPagare'] == "") {
    $_REQUEST['noPagare'] = 0;
}
if ($_REQUEST['noBoleto'] == "") {
    $_REQUEST['noBoleto'] = 0;
}
if ($_REQUEST['reserva'] == "") {
    $_REQUEST['reserva'] = 0;
}
if ($_REQUEST['proveedor'] == "") {
    $_REQUEST['proveedor'] = 0;
}
if ($_REQUEST['vendedor'] == "") {
    $_REQUEST['vendedor'] = 0;
}
if ($_REQUEST['cliente'] == "") {
    $_REQUEST['cliente'] = 0;
}
if ($_REQUEST['pasajero'] == "") {
    $_REQUEST['pasajero'] = 0;
}
if ($_REQUEST['lineaAerea'] == "") {
    $_REQUEST['lineaAerea'] = 0;
}
if ($_REQUEST['tipoPago'] == 1) {
     $tipoPago = "not in ('')";
}
if ($_REQUEST['tipoPago'] == 2) {
    $tipoPago = "in ('')";
}
$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true; 
$PHPJasperXML->arrayParameter = array("fechaInicio" => date("Y-m-d", strtotime($_REQUEST['fechaInicio'])), "fechaFin" => date("Y-m-d", strtotime($_REQUEST['fechaFin'])),
    "fechaInicio2" => $_REQUEST['fechaInicio'], "fechaFin2" => $_REQUEST['fechaFin'], "boleto" => $_REQUEST['noBoleto'],
    "pagare" => $_REQUEST['noPagare'], "reserva" => $_REQUEST['reserva'], "proveedor" => $_REQUEST['proveedor'],
    "vendedor" => $_REQUEST['vendedor'], "cliente" => $_REQUEST['cliente'], "pasajero" => $_REQUEST['pasajero'],
    "lineaAerea" => $_REQUEST['lineaAerea'],"tarjeta" =>  $tipoPago);
$PHPJasperXML->xml_dismantle($xml);
//echo var_dump($PHPJasperXML);
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>