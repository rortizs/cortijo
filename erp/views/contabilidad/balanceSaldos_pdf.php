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
$xml = simplexml_load_file("../jasper/balanceSaldos.jrxml");
switch ($_REQUEST['tipoReporte']) {
    case 1:
        $yearFinal = $_REQUEST['yearInicial'];
        $mesFinal = $_REQUEST['mesInicial'];
        break;
    case 2:
        $yearFinal = $_REQUEST['yearFinal'];
        $mesFinal = $_REQUEST['mesFinal'];
        break;
}

$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter = array("idEmpresas" => $_SESSION['idEmpresa'], "yearInicial" => $_REQUEST['yearInicial'], "yearFinal" => $yearFinal, "mesInicial" => $_REQUEST['mesInicial'], "mesFinal" => $mesFinal);
$PHPJasperXML->xml_dismantle($xml);
//
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>