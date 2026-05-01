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
$xml = "";
$PHPJasperXML = new PHPJasperXML();
switch ($_REQUEST['documento']) {
    case 1:
        $xml = simplexml_load_file("../jasper/deposito.jrxml");
        $PHPJasperXML->arrayParameter = array("idDeposito" => $_REQUEST['idDeposito']);
        break;
    case 2:
        $xml = simplexml_load_file("../jasper/notaCreditoBancos.jrxml");
        $PHPJasperXML->arrayParameter = array("idNCBancos" => $_REQUEST['idNCBancos']);
        break;
    case 3:
        $xml = simplexml_load_file("../jasper/notaDebitoBancos.jrxml");
        $PHPJasperXML->arrayParameter = array("idNDBancos" => $_REQUEST['idNDBancos']);
        break;
}
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->xml_dismantle($xml);
//
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>