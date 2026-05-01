<?php

session_start();
require_once("../../models/config.php");
require_once('../../models/librerias/PHPJasperLibrary09/class/tcpdf/tcpdf.php');
require_once("../../models/librerias/PHPJasperLibrary09/class/PHPJasperXML.inc.php");
//database connection details
$server = Config::$host;
$db = $_SESSION['dbProject'];
$user = Config::$userDB;
$pass = Config::$pwdDB;
//display errors should be off in the php.ini file
ini_set('display_errors', 1);
//setting the path to the created jrxml file
$yearFinal = "";
$mesFinal = "";
$xml = simplexml_load_file("../jasper/estadosDeCuentaClientes.jrxml");
$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter = array(
    "idcliente" => $_REQUEST['idclientes'],
    "idEmpresas"=>$_SESSION['idEmpresa'],
    "fechaInicio" => "'" . date("Y-m-d", strtotime($_REQUEST['fechaInicio'])) . "'", 
    "fechaFin" => "'" . date("Y-m-d", strtotime($_REQUEST['fechaFin'])) . "'",
    "fechaInicio2" => $_REQUEST['fechaInicio'],
    "fechaFin2" => $_REQUEST['fechaFin']);
//print_r($PHPJasperXML->arrayParameter);
$PHPJasperXML->xml_dismantle($xml);
//
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>