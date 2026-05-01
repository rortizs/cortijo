<?php
session_start();
require_once("../../models/config.php");
require_once('../../models/librerias/PHPJasperLibrary09/class/tcpdf/tcpdf.php');
require_once("../../models/librerias/PHPJasperLibrary09/class/PHPJasperXML.inc.php");
//database connection details
if ($_POST['quincena'] == '1') {
    $fechaInicio = $_POST['periodo'] . "-" . $_POST['mes'] . "-01";
    $fechaFin = $_POST['periodo'] . "-" . $_POST['mes'] . "-15";
    $xml = simplexml_load_file("../jasper/reciboPlanilla1.jrxml");
} else {
    $fechaInicio = $_POST['periodo'] . "-" . $_POST['mes'] . "-16";
    $fechaFin = $_POST['periodo'] . "-" . $_POST['mes'] . "-30";
   $xml = simplexml_load_file("../jasper/reciboPlanilla2.jrxml");
}

$server = Config::$host;
$db = $_SESSION['dbProject'];
$user = Config::$userDB;
$pass = Config::$pwdDB;
//display errors should be off in the php.ini file
ini_set('display_errors', 1);
//setting the path to the created jrxml file 

$PHPJasperXML = new PHPJasperXML(); 
//$PHPJasperXML->debugsql=true;    
$PHPJasperXML->arrayParameter = array("fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin, "idHrmPlanilla" =>  $_REQUEST['idHrmPlanilla'],"idHrmDepartamentos" => $_REQUEST['idHrmDepartamentos']);
$PHPJasperXML->xml_dismantle($xml);
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
//echo var_dump($PHPJasperXML);
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file

?>   