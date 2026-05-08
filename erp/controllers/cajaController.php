<?php

/** cajaController
 *  @author Richard Sasvin
 *  @version 2.1 20260430
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/caja.php");
require_once("../models/NumberToLetterConverter.class.php");
$caja = new Caja();
$converter = new NumberToLetterConverter();
$service = $_REQUEST['service'];
switch ($service) {
    case 'fondoCaja':
        $data = $caja->getFondoCorte($_SESSION['idUsuarios'], $_SESSION['idSucursalesS']);
        echo json_encode($data);
        break;
    case 'getProductosVenta':
        $params['idUsuarios'] = $_SESSION['idUsuarios'];
        $params['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
        $params['idVenta'] = $_REQUEST['idVenta'];
        $params['dbProject'] = $_SESSION['dbProject'];
        $params['descuentoP'] = $_REQUEST['descuentoP'];
        $data = $caja->getProductosVenta($params);
        echo json_encode($data);
        break;
    case 'getProductosPedido':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['dbProject'] = $_SESSION['dbProject'];
        $data = $caja->getProductosPedido($_REQUEST);
        echo json_encode($data);
        break;
    case 'saveFondoCaja':
        $params['idSucursales'] = $_SESSION['idSucursalesS'];
        $params['idUsuarios'] = $_SESSION['idUsuarios'];
        $params['monto'] = $_REQUEST['monto'];
        $params['tasaCambio'] = $_REQUEST['tasaCambio'];
        $process = $caja->saveFondoCaja($params);
        echo json_encode($process);
        break;
    case 'agregarProductoVenta':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->agregarProductoVenta($_REQUEST);
        echo json_encode($process);
        break;
    case 'agregarProductoPedido':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->agregarProductoPedido($_REQUEST);
        echo json_encode($process);
        break;
    case 'eliminarProductoVenta':
        $params['idProducto'] = $_REQUEST['idProducto'];
        $params['item'] = $_REQUEST['item'];
        $params['idUsuarios'] = $_SESSION['idUsuarios'];
        $params['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
        $params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->eliminarProductoVenta($params);
        echo json_encode($process);
        break;
    case 'eliminarProductoPedido':
        $process = $caja->eliminarProductoPedido($_REQUEST);
        echo json_encode($process);
        break;
    case 'cerrarVenta':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
        $idEmpresas = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $number = explode(".", $_REQUEST['total']);
        $numberDolares = explode(".", $_REQUEST['totalDolares']);
        $decimals = $number[1];
        $decimalsDolares = $numberDolares[1];
        //
        if (strlen($number[1]) === 1) {
            $decimals = $number[1] . '0';
        } else if (strlen($number[1]) === 0) {
            $decimals = '00';
        } else {
            $decimals = $number[1];
        }
        //
        if (strlen($numberDolares[1]) === 1) {
            $decimalsDolares = $numberDolares[1] . '0';
        } else {
            $decimalsDolares = substr($numberDolares[1], 0, 2);
        }
        $_REQUEST['totalEnLetras'] = trim($converter->to_word($number[0])) . ' QUETZALES CON ' . $decimals . '/100.';
        $_REQUEST['totalEnLetrasDolares'] = $converter->to_word($numberDolares[0]) . 'DOLARES CON ' . $decimalsDolares . '/100.';
        if ($_SESSION['dbProject'] === 'erp_laesperanza' || $_SESSION['dbProject'] === 'erp_elrayo' || $_SESSION['dbProject'] === 'pos_ferroAgro') {
            $process = $caja->cerrarVentaINFILE($_REQUEST, $idUsuarios, $idSucursales, $idEmpresas);
        } else {
            $process = $caja->cerrarVenta($_REQUEST, $idUsuarios, $idSucursales, $idEmpresas);
        }

        echo json_encode($process);
        break;
    case 'generarPedido':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $idEmpresas = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->generarPedido($_REQUEST, $idUsuarios, $idSucursales, $idEmpresas);
        echo json_encode($process);
        break;
    case 'getTipoCambio':
        $data = $caja->getTipoCambio($_SESSION['idUsuarios'], $_SESSION['idSucursalesS']);
        echo json_encode($data);
        break;
    case 'anulacionFactura':
        $process = $caja->anulacionFacturaINFILE($_REQUEST, $_SESSION['idEmpresa'], $_SESSION['dbProject']);
        echo json_encode($process);
        break;
    case 'eliminarFactura':
        $process = $caja->eliminarFactura($_REQUEST, $_SESSION['idEmpresa'], $_SESSION['dbProject']);
        echo json_encode($process);
        break;
    case 'saveVale':
        $params['idUsuarios'] = $_SESSION['idUsuarios'];
        $params['idSucursales'] = $_SESSION['idSucursalesS'];
        $params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $params['solicitadoPor'] = $_REQUEST['solicitadoPor'];
        $params['monto'] = $_REQUEST['monto'];
        $params['observaciones'] = $_REQUEST['observaciones'];
        $process = $caja->saveVale($params);
        echo json_encode($process);
        break;
    case 'getTotalVales':
        $params['idUsuarios'] = $_SESSION['idUsuarios'];
        $params['idSucursales'] = $_SESSION['idSucursalesS'];
        $params['fechaCorte'] = $_REQUEST['fechaCorte'];
        $data = $caja->getTotalVales($params);
        echo json_encode($data);
        break;
    case 'generarCorte':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $idEmpresas = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $fechaCorte = $_REQUEST['fechaCorte'];
        $process = $caja->corteCaja($_REQUEST, $idUsuarios, $idSucursales, $idEmpresas, $fechaCorte);
        echo json_encode($process);
        break;
    case 'getTotalVenta':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $fechaCorte = $_REQUEST['fechaCorte'];
        $data = $caja->getTotalVentas($idSucursales, $idUsuarios, $fechaCorte);
        echo json_encode($data);
        break;
    case 'getTotalVentaTJ':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $fechaCorte = $_REQUEST['fechaCorte'];
        $data = $caja->getTotalVentasTJ($idSucursales, $idUsuarios, $fechaCorte);
        echo json_encode($data);
        break;
    case 'getTotalVentaExencion':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $fechaCorte = $_REQUEST['fechaCorte'];
        $data = $caja->getTotalVentaExencion($idSucursales, $idUsuarios, $fechaCorte);
        echo json_encode($data);
        break;
    case 'getTotalVentaCheques':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $fechaCorte = $_REQUEST['fechaCorte'];
        $data = $caja->getTotalVentaCheques($idSucursales, $idUsuarios, $fechaCorte);
        echo json_encode($data);
        break;
	case 'getTotalRecibos':
        $idUsuarios = $_SESSION['idUsuarios'];
        $fechaCorte = $_REQUEST['fechaCorte'];
		$idEmpresas = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->getTotalRecibos($idUsuarios, $fechaCorte, $idEmpresas);
        echo json_encode($data);
        break;	
    case 'getCierre':
        $response = "";
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $idCorte = isset($_REQUEST['idCorte']) ? (int)$_REQUEST['idCorte'] : 0;
        $getCorte = $caja->getCorte($idCorte);
        $getCierre = $caja->getCierre($idSucursales, $idUsuarios);
        $response[] = array(
            'fechaVenta' => $getCorte['created_at'] ?: '',
            'ventas' => $getCierre['ventas'] ?: '0.00',
            'efectivo' => $getCierre['efectivo'] ?: '0.00',
            'cambios' => $getCierre['cambios'] ?: '0.00',
            'tarjetas' => $getCierre['tarjetas'] ?: '0.00',
            'totalCorte' => $getCorte['totalCorte'] ?: '0.00',
            'totalVouchers' => $getCorte['totalVouchers'] ?: '0.00',
            'fondoCaja' => $getCorte['fondoCaja'] ?: '0.00',
            'totalVales' => $getCorte['totalVales'] ?: '0.00'
        );
        echo json_encode($response);
        break;
    case 'procesarCierre':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $fechaCierre = date("Y-m-d", strtotime($fechaCorte));
        $process = $caja->cerrarCaja($idUsuarios, $idSucursales, $fechaCierre);
        echo json_encode($process);
        break;
    case 'cancelarVenta':
        $idSucursales = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
        $idUsuarios = $_SESSION['idUsuarios'];
        $process = $caja->cancelarVenta($idSucursales, $idUsuarios, $params);
        echo json_encode($process);
        break;
    case 'cancelarPedido':
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_SESSION['idSucursales'];
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->cancelarPedido($_REQUEST);
        echo json_encode($process);
        break;
    case 'getPedido':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->getPedido($_REQUEST);
        echo json_encode($process);
        break;
    case 'consultarPedidos':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
        $process = $caja->consultarPedidos($_REQUEST);
        echo json_encode($process);
        break;
    case 'examenesPedido':
        $data = $caja->examenesPedido($_REQUEST);
        echo json_encode($data);
        break;
    case 'generarCotizacion':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idSucursales = $_SESSION['idSucursalesS'];
        $idEmpresas = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->generarCotizacion($_REQUEST, $idUsuarios, $idSucursales, $idEmpresas, $_SESSION['dbProject']);
        echo json_encode($process);
        break;
    case 'getProductosCotizacion':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->getProductosCotizacion($_REQUEST);
        echo json_encode($data);
        break;
    case 'agregarProductoCotizacion':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->agregarProductoCotizacion($_REQUEST);
        echo json_encode($process);
        break;
    case 'cancelarCotizacion':
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $process = $caja->cancelarCotizacion($_REQUEST);
        echo json_encode($process);
        break;
    case 'eliminarProductoCotizacion':
        $process = $caja->eliminarProductoCotizacion($_REQUEST);
        echo json_encode($process);
        break;
    case 'consultarCotizaciones':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->consultarCotizaciones($_REQUEST);
        echo json_encode($process);
        break;
    case 'getCotizacion':
        $process = $caja->getCotizacion($_REQUEST);
        echo json_encode($process);
        break;
    case 'consultarVales':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->consultarVales($_REQUEST);
        echo json_encode($process);
        break;
    case 'actualizarCotizacion':
        $process = $caja->actualizarCotizacion($_REQUEST);
        echo json_encode($process);
        break;
    case 'actualizarPedido':
        $process = $caja->actualizarPedido($_REQUEST);
        echo json_encode($process);
        break;
    case 'agregarBoletoDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $agenciasViajes->agregarBoletoDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarBoletoDetalle':
        $data = $agenciasViajes->eliminarBoletoDetalle($_REQUEST);
        echo json_encode($data);
    case 'updateItemVenta':
        $remove = $caja->updateItemVenta($_REQUEST);
        echo json_encode($remove);
        break;
    case 'agregarTomaMedidasDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $data = $caja->agregarTomaMedidasDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'getTomaMedidasDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->getTomaMedidasDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarTomaMedidasDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->eliminarTomaMedidasDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'generarTomaMedidas':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->generarTomaMedidas($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarTomaMedidas':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->eliminarTomaMedidas($_REQUEST);
        echo json_encode($data);
        break;
    case 'getTomaMedidas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->getTomaMedidas($_REQUEST);
        echo json_encode($data);
        break;
    case 'actualizarTomaMedidas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->actualizarTomaMedidas($_REQUEST);
        echo json_encode($data);
        break;
    case 'cancelarTomaMedidas':
        $idSucursales = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
        $idUsuarios = $_SESSION['idUsuarios'];
        $process = $caja->cancelarTomaMedidas($idSucursales, $idUsuarios);
        echo json_encode($process);
        break;
    case 'updateItemCotizacion':
        $remove = $caja->updateItemCotizacion($_REQUEST);
        echo json_encode($remove);
        break;
    case 'liquidarVales':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $number = explode(".", $_REQUEST['total']);
        $_REQUEST['totalEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 QUETZALES.';
        $_REQUEST['dbProject'] = $_SESSION['dbProject'];
		if($_SESSION['dbProject']==='pos_lafe'){
			$process = $caja->liquidarValesInfile($_REQUEST);
		}else{
			$process = $caja->liquidarVales($_REQUEST);
		}
        echo json_encode($process);
        break;
    case 'saveFactRecurrente':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->saveFactRecurrente($_REQUEST);
        echo json_encode($process);
        break;
    case 'getFactRecurrente':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->getFactRecurrente($_REQUEST);
        echo json_encode($process);
        break;
    case 'eliminarFactRecurrente':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $process = $caja->eliminarFactRecurrente($_REQUEST);
        echo json_encode($process);
        break;
    case 'eliminarRecibo':
        $process = $caja->eliminarRecibo($_REQUEST);
        echo json_encode($process);
        break;
    case 'consultaNIT':
        $data = $caja->consultaNIT($_REQUEST);
        echo json_encode($data);
        break;
    case 'generarNCFEL':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['dbProject'] = $_SESSION['dbProject'];
		if($_REQUEST['dbProject']==='erp_petromas'){
			$data = $caja->generarNCFEL_INFILE($_REQUEST);
		}else{
			$data = $caja->generarNCFEL($_REQUEST);
		}
        echo json_encode($data);
        break;
    case 'consultaNCFEL':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $caja->consultaNCFEL($_REQUEST);
        echo json_encode($data);
        break;
    case 'emitirFactura':
        $idUsuarios = $_SESSION['idUsuarios'];
        $idEmpresas = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $number = explode(".", $_REQUEST['total']);
        $numberDolares = explode(".", $_REQUEST['totalDolares']);
        $decimals = $number[1];
        $decimalsDolares = $numberDolares[1];
        //
        if (strlen($number[1]) === 1) {
            $decimals = $number[1] . '0';
        } else if (strlen($number[1]) === 0) {
            $decimals = '00';
        } else {
            $decimals = $number[1];
        }
        //
        $_REQUEST['totalEnLetras'] = $converter->to_word($number[0]) . 'QUETZALES CON ' . $decimals . '/100.';
        $_REQUEST['totalEnLetrasDolares'] = $converter->to_word($numberDolares[0]) . 'DOLARES CON ' . $decimalsDolares . '/100.';
        //
        switch ($_SESSION['dbProject']) {
        	case 'pos_alimentosmo':
                $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                break;
        	case 'erp_laesperanza':
                $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'erp_itza':
                if ($_REQUEST['tipoOperacion'] === '5') {
                    $process = $caja->emitirFacturaCamara($_REQUEST, $idUsuarios, $idEmpresas);
                } else {
                    $process = $caja->emitirFacturaCamara_normal($_REQUEST, $idUsuarios, $idEmpresas);
                }
                break;
            case 'erp_petromas':
                $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'erp_gruposeeg':
                $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'pos_gasLaTerminal':
                $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'erp_gasolinerasdexela':
                $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'pos_lafe':
                if ($_REQUEST['tipoOperacion'] === '5') {
                    $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                } else {
                    $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                }
                break;
            case 'erp_gcit':
                $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'pos_roca':
                $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'pos_aregua':
                $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'erp_corsesa':
                $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'pos_sanantonio':
                if ($_REQUEST['tipoOperacion'] === '5') {
                    $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                } else {
                    $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                }
                break;
            case 'erp_grupoperza':
                if ($_SESSION['nitEmpresa'] === '101817908' || $_SESSION['nitEmpresa'] === '101817673') {
                    $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                } else {
                    $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                }
                break;
            case 'erp_elohim':
                $process = $caja->emitirFacturaCombustibles($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'erp_suplegt':
                $process = $caja->emitirFacturaCombustibles($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'erp_supleXela':
                if ($idEmpresas === '3') {
                    $process = $caja->emitirFactura($_REQUEST, $idUsuarios, $idEmpresas);
                } else {
                    $process = $caja->emitirFacturaCombustibles($_REQUEST, $idUsuarios, $idEmpresas);
                }
                break;
            case 'erp_corporacionsancarlos':
                if ($_REQUEST['tipoOperacion'] === '5') {
                    $process = $caja->emitirFacturaCombustibles($_REQUEST, $idUsuarios, $idEmpresas);
                } else {
                    $process = $caja->emitirFactura($_REQUEST, $idUsuarios, $idEmpresas);
                }
                break;
            case 'pos_imperial':
                $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'erp_gasolinerasantafe':
                if ($idEmpresas === '1' || $idEmpresas === '4' || $idEmpresas === '5') {
                    $process = $caja->emitirFacturaINFILE($_REQUEST, $idUsuarios, $idEmpresas);
                } else {
                    $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                }
                break;
            case 'erp_mercadeoinmobiliario':
                $process = $caja->emitirFacturaINFILE_normal($_REQUEST, $idUsuarios, $idEmpresas);
                break;
            case 'erp_inversionesyproyectos':
                if ($_REQUEST['tipoOperacion'] === '5') {
                    $process = $caja->emitirFacturaCombustibles($_REQUEST, $idUsuarios, $idEmpresas);
                } else {
                    $process = $caja->emitirFactura($_REQUEST, $idUsuarios, $idEmpresas);
                }
                break;
            default:
                $process = $caja->emitirFactura($_REQUEST, $idUsuarios, $idEmpresas);
                break;
        }
        echo json_encode($process);
        break;
    case 'generarNCCompras':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $data = $caja->generarNCCompras($_REQUEST);
        echo json_encode($data);
        break;
    case 'cambiarEstadoOrden':
        $data = $caja->cambiarEstadoOrden($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarValeCaja':
        $process = $caja->eliminarValeCaja($_REQUEST);
        echo json_encode($process);
        break;
    case 'getPreciosProducto':
        $data = $caja->getPreciosProducto($_REQUEST);
        echo json_encode($data);
        break;
    case 'procesarPagare':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $data = $caja->procesarPagare($_REQUEST);
        echo json_encode($data);
        break;
    case 'consultaPagares':
        $data = $caja->consultaPagares($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarPagare':
        $data = $caja->eliminarPagare($_REQUEST);
        echo json_encode($data);
        break;
}