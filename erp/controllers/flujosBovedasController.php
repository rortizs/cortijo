<?php

/** adminController
 *  @autho
 * 
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/flujosBoveda.php");
$admin = new FlujosBoveda();
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
        $param['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $param['tipo'] = $_REQUEST['tipo'];
        $data = $admin->getDocumentos($param);
        echo json_encode($data);
        break;
    case 'getDocumentosCorrelativo':
        $data = $admin->getDocumentosCorrelativo($_REQUEST);
        echo json_encode($data);
        break;
    case 'getProveedor':
        $data = $admin->getProveedores($_REQUEST);
        echo json_encode($data);
        break;
    case 'getCliente':
        $params['nit'] = $_REQUEST['nit'];
        $params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->getClientes($params);
        echo json_encode($data);
        break;
    case 'getVendedores':
        $params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $params['idSucursales'] = $_SESSION['idSucursalesS'];
        $data = $admin->getVendedores($params);
        echo json_encode($data);
        break;
    case 'getEmisores':
        $data = $admin->getEmisores();
        echo json_encode($data);
        break;
    case 'getCajeros':
        $params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $params['idSucursales'] = $_SESSION['idSucursalesS'];
        $data = $admin->getCajeros($params);
        echo json_encode($data);
        break;
    case 'loginAdmin':
        $data = $admin->loginAdmin($_REQUEST);
        echo json_encode($data);
        break;
    case 'getNomenclaruta':
        $params['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $params['idNomenclatura'] = $_REQUEST['idNomenclatura'];
        $data = $admin->getNomenclatura($params);
        echo json_encode($data);
        break;
    case 'getFormArqueoBoveda':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->getFormArqueoBoveda($_REQUEST);
        echo json_encode($data);
        break;
    case 'getFormArqueoBovedaMesas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $data = $admin->getFormArqueoBovedaMesas($_REQUEST);
        echo json_encode($data);
        break;
    case 'saveRowFormArqueoBoveda':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $save = $admin->saveRowFormArqueoBoveda($_REQUEST);
        echo json_encode($save);
        break;
    case 'saveRowFormArqueoBovedaMesas':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
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
    case 'eliminarCierre':
        $data = $admin->eliminarCierre($_REQUEST);
        echo json_encode($data);
        break;
}