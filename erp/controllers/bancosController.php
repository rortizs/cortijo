<?php

/** bancosController
 *  @author Jonathan Juarez
 *  @version 2.0 20170511
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/bancos.php");
require_once("../models/NumberToLetterConverter.class.php");
$converter = new NumberToLetterConverter();
$bancos = new Bancos();
$service = $_REQUEST['service'];
switch ($service) {
    case 'guardarCheque':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 CTVS.';
        $data = $bancos->guardarCheque($_REQUEST);
        echo json_encode($data);
        break;
    case 'cxp':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->cxp($_REQUEST);
        echo json_encode($data);
        break;
    case 'cxc':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->cxc($_REQUEST);
        echo json_encode($data);
        break;
    case 'guardarDeposito':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 CTVS.';
        $data = $bancos->guardarDeposito($_REQUEST);
        echo json_encode($data);
        break;
    case 'guardarNDBancos':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 CTVS.';
        $data = $bancos->guardarNDBancos($_REQUEST);
        echo json_encode($data);
        break;
    case 'guardarNCBancos':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 CTVS.';
        $data = $bancos->guardarNCBancos($_REQUEST);
        echo json_encode($data);
        break;
    case 'impresionCheque':
        $data = $bancos->impresionCheque($_REQUEST);
        echo json_encode($data);
        break;
    case 'anularCheque':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->anularCheque($_REQUEST);
        echo json_encode($data);
        break;
    case 'conciliarCheque':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->conciliarCheque($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarCheque':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->eliminarCheque($_REQUEST);
        echo json_encode($data);
        break;
    case 'getFacturasCxp':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->getFacturasCxp($_REQUEST);
        echo json_encode($data);
        break;
    case 'getFacturasCxc':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->getFacturasCxc($_REQUEST);
        echo json_encode($data);
        break;
    case 'generarRecibo':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        //
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) .''. $_REQUEST['moneda'] . ' CON ' . $number[1] . '/100 CTVS.';
        //
        $data = $bancos->actualizarCXC($_REQUEST);
        echo json_encode($data);
        break;
    case 'importVentaCXC':
        $data = $bancos->getVentas();
        foreach ($data as $key => $value) {
            $saldosAcumuladosCXC = $bancos->saldosAcumuladosCXC($value['idClientes']);
            //echo json_encode($saldosAcumuladosCXC) . "<hr/>";
        }
        break;
    case 'eliminarDeposito':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->eliminarDeposito($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarNDBancos':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->eliminarNDBancos($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarNCBancos':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->eliminarNCBancos($_REQUEST);
        echo json_encode($data);
        break;
    case 'updateNCBancos':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 CTVS.';
        $data = $bancos->updateNCBancos($_REQUEST);
        echo json_encode($data);
        break;
    case 'updateNDBancos':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 CTVS.';
        $data = $bancos->updateNDBancos($_REQUEST);
        echo json_encode($data);
        break;
    case 'guardarCajaChica':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->guardarCajaChica($_REQUEST);
        echo json_encode($data);
        break;
    case 'cerrarCajaChica':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->cerrarCajaChica($_REQUEST);
        echo json_encode($data);
        break;
    case 'abrirCajaChica':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->abrirCajaChica($_REQUEST);
        echo json_encode($data);
        break;
    case 'actualizarCheque':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 CTVS.';
        $data = $bancos->actualizarCheque($_REQUEST);
        echo json_encode($data);
        break;
    case 'generarNotaCredito':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $bancos->actualizarCXC($_REQUEST);
        echo json_encode($data);
        break;
}