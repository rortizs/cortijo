<?php

session_start();
require_once("../../models/config.php");
require_once('../../models/librerias/PHPJasperLibrary09/class/tcpdf/tcpdf.php');
require_once("../../models/librerias/PHPJasperLibrary09/class/PHPJasperXML.inc.php");
//database connection details
$server = Config::$host;
$db = $_SESSION['dbProject'] ?: $_REQUEST['dbProject'];
$user = Config::$userDB;
$pass = Config::$pwdDB;
//display errors should be off in the php.ini file
ini_set('display_errors', 1);
//setting the path to the created jrxml file
$xml = "";
switch ($db) {
    //case 'erp_gsp':
    case 'erp_elcortijo':
        switch ($_SESSION['idEmpresa']) {
            case '1':
                $xml = simplexml_load_file("../jasper/cotizacionesQuitralco.jrxml");
                break;
            case '6':
                $xml = simplexml_load_file("../jasper/cotizacionesCDC.jrxml");
                break;
            default :
                $xml = simplexml_load_file("../jasper/cotizaciones.jrxml");
                break;
        }
        break;
    case 'pos_lolascloset':
        $xml = simplexml_load_file("../jasper/cotizacionLavanderiaSHP.jrxml");
        break;
    case 'erp_suple':
        $xml = simplexml_load_file("../jasper/cotizacionesSuple.jrxml");
        break;
    case 'pos_togasjulissa':
        $xml = simplexml_load_file("../jasper/cotizacionesTogas.jrxml");
        break;
    default:
         $xml = simplexml_load_file("../jasper/cotizaciones.jrxml");
        break;
}

$PHPJasperXML = new PHPJasperXML();
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter = array("idCotizacion" => $_REQUEST['idCotizacion']);
$PHPJasperXML->xml_dismantle($xml);
//
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
?>