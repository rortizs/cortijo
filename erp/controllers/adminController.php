<?php

/** adminController
 *  @author Richard Sasvin
 * 
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/admin.php");
require_once("../models/dynamic.php");
$admin = new Admin();
$dynamic = new Dynamic();
$service = $_REQUEST['service'];
switch ($service) {
    case 'sessionAlive':
        $response = "";
        if (!empty($_SESSION['idEmpresa'])) {
            $response[] = array('message' => 'alive', 'time' => date('Y-m-d H:i:s'));
        } else {
            $response[] = array('message' => 'empty', 'time' => date('Y-m-d H:i:s'));
        }
        echo json_encode($response);
        break;
    case 'getEmpresas':
        $data = $admin->getEmpresas();
        echo json_encode($data);
        break;
    case 'getBodegas':
        $data = $admin->getBodegas($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'getSucursales':
        $data = $admin->getSucursales($_SESSION['idEmpresa'], $_REQUEST);
        echo json_encode($data);
        break;
    case 'getDocumentos':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idRoles'] = $_SESSION['idRoles'];
        $data = $admin->getDocumentos($_REQUEST);
        echo json_encode($data);
        break;
    case 'getDocumentosCorrelativo':
        $data = $admin->getDocumentosCorrelativo($_REQUEST);
        echo json_encode($data);
        break;
    case 'getProveedor':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->getProveedores($_REQUEST);
        echo json_encode($data);
        break;
    case 'getProveedorByNit':
        $data = $admin->getProveedoresByNit($_REQUEST);
        echo json_encode($data);
        break;
    case 'getCliente':
        $_REQUEST['nombreEmpresa'] = $_SESSION['nombreEmpresa'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->getClientes($_REQUEST);
        echo json_encode($data);
        break;
    case 'getVendedores':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $data = $admin->getVendedores($_REQUEST);
        echo json_encode($data);
        break;
    case 'getVendedoresLax':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $data = $admin->getVendedoresLax($_REQUEST);
        echo json_encode($data);
        break;
    case 'getProveedoresLax':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $data = $admin->getProveedoresLax($_REQUEST);
        echo json_encode($data);
        break;
    case 'getEmisores':
        $data = $admin->getEmisores();
        echo json_encode($data);
        break;
    case 'getCajeros':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $data = $admin->getCajeros($_REQUEST);
        echo json_encode($data);
        break;
    case 'loginAdmin':
        $data = $admin->loginAdmin($_REQUEST);
        echo json_encode($data);
        break;
    case 'loginApp':
        $data = $admin->loginApp($_REQUEST);
        echo json_encode($data);
        break;
    case 'getNomenclaruta':
        $params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $params['idNomenclatura'] = $_REQUEST['idNomenclatura'];
        $data = $admin->getNomenclatura($params);
        echo json_encode($data);
        break;
    case 'getFormArqueoBoveda':
        $params['parent'] = $_REQUEST['parent'];
        $data = $admin->getFormArqueoBoveda($params);
        echo json_encode($data);
        break;
    case 'getFormArqueoBovedaMesas':
        $params['parent'] = $_REQUEST['parent'];
        $data = $admin->getFormArqueoBovedaMesas($params);
        echo json_encode($data);
        break;
    case 'saveRowFormArqueoBoveda':
        $save = $admin->saveRowFormArqueoBoveda($_REQUEST);
        echo json_encode($save);
        break;
    case 'saveRowFormArqueoBovedaMesas':
        $save = $admin->saveRowFormArqueoBovedaMesas($_REQUEST);
        echo json_encode($save);
        break;
    case 'updateRowFormArqueoBoveda':
        $update = $admin->updateRowFormArqueoBoveda($_REQUEST);
        echo json_encode($update);
        break;
    case 'updateRowFormArqueoBovedaMesas':
        $update = $admin->updateRowFormArqueoBovedaMesas($_REQUEST);
        echo json_encode($update);
        break;
    case 'deleteRowFormArqueoBoveda':
        $update = $admin->deleteRowFormArqueoBoveda($_REQUEST);
        echo json_encode($update);
        break;
    case 'deleteRowFormArqueoBovedaMesas':
        $update = $admin->deleteRowFormArqueoBovedaMesas($_REQUEST);
        echo json_encode($update);
        break;
    case 'guardarArqueoMaquinas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $save = $admin->guardarArqueoMaquinas($_REQUEST);
        echo json_encode($save);
        break;
    case 'guardarArqueoMesas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $save = $admin->guardarArqueoMesas($_REQUEST);
        echo json_encode($save);
        break;
    case 'saldosInicialesBovedas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->saldosInicialesBovedas($_REQUEST);
        echo json_encode($data);
        break;
    case 'saldosInicialesBovedasMesas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->saldosInicialesBovedasMesas($_REQUEST);
        echo json_encode($data);
        break;
    case 'consultarCierre':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->consultarCierre($_REQUEST);
        echo json_encode($data);
        break;
    case 'consultarCierreMesas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->consultarCierreMesas($_REQUEST);
        echo json_encode($data);
        break;
    case 'updateArqueoMaquinas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $save = $admin->updateArqueoMaquinas($_REQUEST);
        echo json_encode($save);
        break;
    case 'updateArqueoMesas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $save = $admin->updateArqueoMesas($_REQUEST);
        echo json_encode($save);
        break;
    case 'getCorrelativoPartidas':
        $data = $admin->getCorrelativoPartidas($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'getFormulas':
        $data = $admin->getFormulas($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'getEmpleados':
        $data = $admin->getEmpleados($_REQUEST['idDepartamentos']);
        echo json_encode($data);
        break;
    case 'getUsuarios':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->getUsuarios($_REQUEST);
        echo json_encode($data);
        break;
    case 'getFamilias':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'];
        $data = $admin->getFamilias($_REQUEST);
        echo json_encode($data);
        break;
    case 'getFamiliaForm':
        $data = $dynamic->dataTable($_SESSION['dbProject'], $_REQUEST['table'], $_REQUEST['parametros']);
        echo json_encode($data);
        break;
    case 'resumenDocumentosOperados':
        $data = $admin->resumenDocumentosOperados($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'ventasPorMes':
        $anio = isset($_REQUEST['anio']) ? (int) $_REQUEST['anio'] : (int) date('Y');
        $data = $admin->ventasPorMes($_SESSION['idEmpresa'], $anio);
        echo json_encode($data);
        break;
    case 'getDteSaldo':
        $data = $admin->getDteSaldo($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'getComprasDte':
        $data = $admin->getComprasDte($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'guardarComprasDte':
        $data = $admin->guardarComprasDte($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarComprasDte':
        $data = $admin->eliminarComprasDte($_REQUEST['id']);
        echo json_encode($data);
        break;
    case 'getDtesPorMes':
        $anio = isset($_REQUEST['anio']) ? (int)$_REQUEST['anio'] : (int)date('Y');
        $data = $admin->getDtesPorMes($_SESSION['idEmpresa'], $anio);
        echo json_encode($data);
        break;
    case 'top10Productos':
        $data = $admin->top10Productos($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'resumenDepositos':
        $data = $admin->resumenDepositos($_REQUEST);
        echo json_encode($data);
        break;
}