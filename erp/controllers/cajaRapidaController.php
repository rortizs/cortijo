<?php

session_start();
date_default_timezone_set("America/Guatemala");
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=utf-8');
require_once ("../models/cajaRapida.php");
require_once ("../models/NumberToLetterConverter.class.php");
$general = new CajaRapida();
$converter = new NumberToLetterConverter();
$service = $_REQUEST['service'];
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresas'];
$_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
$_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
switch ($service) {
    case 'getCategorias' :
        $data = $general->getCategorias($_REQUEST);
        echo json_encode($data);
        break;
    case 'getProductosCategorias' :
        $data = $general->getProductosCategorias($_REQUEST);
        echo json_encode($data);
        break;
    case 'agregarProductoVenta' :
        $data = $general->agregarProductoVenta($_REQUEST);
        echo json_encode($data);
        break;
    case 'loadProductosVenta' :
        $data = $general->loadProductosVenta($_REQUEST);
        echo json_encode($data);
        break;
    case 'cancelarVenta' :
        $data = $general->cancelarVenta($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarProductoVenta' :
        $data = $general->eliminarProductoVenta($_REQUEST);
        echo json_encode($data);
        break;
    case 'finalizarVenta':
        $number = explode(".", $_REQUEST['total']);
        $decimals = $number[1];
        if (strlen($number[1]) === 1) {
            $decimals = $number[1] . '0';
        }
        $_REQUEST['totalEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $decimals . '/100 QUETZALES.';
        $process = $general->finalizarVenta($_REQUEST);
        echo json_encode($process);
        break;
    case 'getDocumentoFacturacion' :
        $data = $general->getDocumentoFacturacion($_REQUEST);
        echo json_encode($data);
        break;
}