<?php

/** cajaController
 *  @author Jonathan Juarez
 *  @version 2.0 20161209
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/agenciasViajes.php");
$agenciasViajes = new agenciasViajes();
$service = $_REQUEST['service']; 
switch ($service) { 
    case 'getBoleto':
         $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ? : $_SESSION['idEmpresas'];
        $data = $agenciasViajes->getBoleto($_REQUEST);
        echo json_encode($data);
        break;
    case 'getDetalleBoleto':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ? : $_SESSION['idEmpresas'];
        $data = $agenciasViajes->getDetalleBoleto($_REQUEST);
        echo json_encode($data);
        break;
    case 'agregarBoletoDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ? : $_SESSION['idEmpresas'];
        $data = $agenciasViajes->agregarBoletoDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarBoletoDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ? : $_SESSION['idEmpresas'];
        $data = $agenciasViajes->eliminarBoletoDetalle($_REQUEST);
        echo json_encode($data);
        break;
       case 'updateFeeDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ? : $_SESSION['idEmpresas'];
        $data = $agenciasViajes->updateFeeDetalle($_REQUEST);
        echo json_encode($data);
        break;
       case 'updateBoleto':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ? : $_SESSION['idEmpresas'];
        $data = $agenciasViajes->updateBoleto($_REQUEST);
        echo json_encode($data);
        break;
}