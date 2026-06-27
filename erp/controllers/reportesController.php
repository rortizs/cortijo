<?php

/** reportesController
 *
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once "../models/reportes.php";
$reportes = new Reportes();
$service = $_REQUEST['service'];
switch ($service) {
case 'consultarFacturas':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	//$_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
	$data = $reportes->consultaFacturas($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarFacturasDetallado':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
//        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
	$data = $reportes->consultarFacturasDetallado($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarFacturasLaxTravel':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
	$data = $reportes->consultaFacturasLaxTravel($_REQUEST);
	echo json_encode($data);
	break;
case 'ventasPorProducto':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->ventasPorProducto($_REQUEST);
	echo json_encode($data);
	break;
case 'ventasPorVendedor':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->ventasPorVendedor($_REQUEST);
	echo json_encode($data);
	break;
case 'ventasPorFamilia':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->ventasPorFamilia($_REQUEST);
	echo json_encode($data);
	break;
case 'ventasPorDocumento':
	$data = $reportes->consultaFacturas($_REQUEST);
	echo json_encode($data);
	break;
case 'historialCostos':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->historialCostos($_REQUEST);
	echo json_encode($data);
	break;
case 'consumoMateriales':
	$data = $reportes->consumoMateriales($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarBoletos':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarBoletos($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarTomaMedidas':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarTomaMedidas($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarVales':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarVales($_REQUEST);
	echo json_encode($data);
	break;
case 'eliminarVale':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->eliminarVale($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarValesLiquidar':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarValesLiquidar($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarRecibos':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarRecibos($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarFacturasSuple':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$_REQUEST['idSucursales'] = $_REQUEST['idSucursales'];
	$data = $reportes->consultaFacturasSuple($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarFacturasGeneral':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarFacturasGeneral($_REQUEST);
	echo json_encode($data);
	break;
case 'existencias':
	$_REQUEST['empresa'] = $_SESSION['nombreEmpresa'];
	$data = $reportes->existencias($_REQUEST);
	echo json_encode($data);
	break;
case 'existenciasHistorico':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->existenciasHistorico($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarDespachos':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
	$data = $reportes->consultarDespachos($_REQUEST);
	echo json_encode($data);
	break;
case 'getBombas':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->getBombas($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarFacturasKreativos':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarFacturasKreativos($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarDespachosP1':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarDespachosP1($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarCompras':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarCompras($_REQUEST);
	echo json_encode($data);
	break;
case 'comprasPorProducto':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->comprasPorProducto($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarFacturasResumen':
	$_REQUEST['nitEmpresa'] = $_SESSION['nitEmpresa'];
	$data = $reportes->consultarFacturasResumen($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarCortesCaja':
	$data = $reportes->consultarCortesCaja($_REQUEST);
	echo json_encode($data);
	break;
case 'getCorteGasolinera':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->getCorteGasolinera($_REQUEST);
	echo json_encode($data);
	break;
case 'ventasHoy':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->ventasHoy($_REQUEST);
	echo json_encode($data);
	break;
case 'totalDtes':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->totalDtes($_REQUEST);
	echo json_encode($data);
	break;
case 'ultimaTransaccion':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->ultimaTransaccion($_REQUEST);
	echo json_encode($data);
	break;
case 'top10hoy':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->top10hoy($_REQUEST);
	echo json_encode($data);
	break;
case 'top10mes':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->top10mes($_REQUEST);
	echo json_encode($data);
	break;
case 'top10mesCompras':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->top10mesCompras($_REQUEST);
	echo json_encode($data);
	break;
case 'ultimas10Transacciones':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->ultimas10Transacciones($_REQUEST);
	echo json_encode($data);
	break;
case 'ultimos7dias':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->ultimos7dias($_REQUEST);
	echo json_encode($data);
	break;
case 'currentYear':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->currentYear($_REQUEST);
	echo json_encode($data);
	break;
case 'consultarResumenCompras':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->consultarResumenCompras($_REQUEST);
	echo json_encode($data);
	break;
case 'comprasHoy':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->comprasHoy($_REQUEST);
	echo json_encode($data);
	break;
case 'utilidad':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->utilidad($_REQUEST);
	echo json_encode($data);
	break;
case 'resumenInventario':
	$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
	$data = $reportes->resumenInventario($_REQUEST);
	echo json_encode($data);
	break;
}
