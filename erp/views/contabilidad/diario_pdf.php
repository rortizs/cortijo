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
switch ($_REQUEST['tipoReporte']) {
    case 1:
        $xml = simplexml_load_file("../jasper/diarioDetallado.jrxml");
        break;
    case 2:
        $xml = simplexml_load_file("../jasper/diarioResumido.jrxml");
        break;
    case 3:
        $xml = simplexml_load_file("../jasper/diarioPartidasNoCuadradas.jrxml");
        break;
}
$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter = array("idEmpresas" => $_SESSION['idEmpresa'], "yearInicial" => $_REQUEST['yearInicial'], "yearFinal" => $_REQUEST['yearFinal'], "mesInicial" => $_REQUEST['mesInicial'], "mesFinal" => $_REQUEST['mesFinal'], "folio" => intval($_REQUEST['folioInicio']));
$PHPJasperXML->xml_dismantle($xml);
//
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file

?>