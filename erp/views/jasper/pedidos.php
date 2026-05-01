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
switch ($db) {
    case 'pos_togasjulissa':
        $xml = simplexml_load_file("../jasper/pedidosTogas.jrxml");
        break;
    case 'erp_gsp':
        switch ($_SESSION['idEmpresa']) {
            case '3':
                $xml = simplexml_load_file("../jasper/pedidosQuitralco.jrxml");
                break;
            case '6':
                $xml = simplexml_load_file("../jasper/pedidosGSP.jrxml");
                break;
            default :
                $xml = simplexml_load_file("../jasper/cotizaciones.jrxml");
                break;
        }
        break;
    case 'pos_kasualcosmeticos':
        $xml = simplexml_load_file("../jasper/pedidosKasual.jrxml");
        break;
    case 'pos_asenersa':
        $xml = simplexml_load_file("../jasper/pedidosAsenersa.jrxml");
        break;
    default:
        $xml = simplexml_load_file("../jasper/pedidos.jrxml");
        break;
}
$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter = array("idPedido" => $_REQUEST['idPedido']);
$PHPJasperXML->xml_dismantle($xml);
//
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>