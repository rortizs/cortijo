<?php

/** planillaController
 * 
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/planilla.php");
$planilla = new Planilla();
$service = $_REQUEST['service'];
switch ($service) {
    case 'getHrmDepartamentos2':
        $data = $planilla->getHrmDepartamentos2($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'getHrmDepartamentos':
        $data = $planilla->getDepartamentos($_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'getHrmEmpleados':
        $data = $planilla->getHrmEmpleados($_REQUEST, $_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'cerrarPlanilla':
        $process = $planilla->cerrarPlanilla($_REQUEST, $_SESSION['idUsuarios'], $_SESSION['idEmpresa']);
        echo json_encode($process);
        break;
    case 'consultaCierre':
        $_REQUEST['idEmpresa'] = $_SESSION['idEmpresa'];
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $process = $planilla->consultaCierre($_REQUEST);
        echo json_encode($process);
        break;
    case 'consultarCierre':
        $process = $planilla->consultarCierre($_REQUEST);
        echo json_encode($process);
        break;
    case 'loadDepartamentos':
        $process = $planilla->getHrmDepartamentos($_REQUEST['idHrmPlanillas'], $_SESSION['idEmpresa'], $_REQUEST['idHrmDepartamentos']);
        echo json_encode($process);
        break;
    case 'guardarMarcaje':
        $process = $planilla->guardarMarcaje($_REQUEST);
        echo json_encode($process);
        break;
    case 'updateMarcaje':
        $process = $planilla->updateMarcaje($_REQUEST);
        echo json_encode($process);
        break;
    case 'subirExpediente':
        $ext = explode('.', $_FILES['file']['name']);
        $extension = $ext[1];
        $file = $_REQUEST['idHrmEmpleados'] . '-' . date('YmdHis') . '.' . $extension;
        //
        $path = dirname(__DIR__) . '/assets/expedientes/' . $file;
        try {
            $upload = move_uploaded_file($_FILES['file']['tmp_name'], $path);
            $_REQUEST['idEmpresa'] = $_SESSION['idEmpresa'];
            $_REQUEST['file'] = $file;
            $process = $planilla->subirExpediente($_REQUEST);
            echo json_encode($process);
        } catch (Exception $e) {
            $response[] = array('message' => $e->getMessage());
            echo json_encode($response);
        }
        break;
    case 'hrmEmpleadosExpediente':
        $_REQUEST['idEmpresa'] = $_SESSION['idEmpresa'];
        $data = $planilla->hrmEmpleadosExpediente($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarExpediente':
        try {
            $path = dirname(__DIR__) . '/assets/expedientes/' . $_REQUEST['file'];
            unlink($path);
            $process = $planilla->eliminarExpediente($_REQUEST);
            echo json_encode($process);
        } catch (Exception $e) {
            $response[] = array('message' => $e->getMessage());
            echo json_encode($response);
        }
        break;
    case 'subirFotoFicha':
        $ext = explode('.', $_FILES['file']['name']);
        $extension = $ext[1];
        $file = $_REQUEST['idHrmEmpleados'] . '-' . date('YmdHis') . '.' . $extension;
        //
        $path = dirname(__DIR__) . '/assets/images/empleados/' . $file;
        try {
            $upload = move_uploaded_file($_FILES['file']['tmp_name'], $path);
            $_REQUEST['idEmpresa'] = $_SESSION['idEmpresa'];
            $_REQUEST['file'] = $file;
            $process = $planilla->subirFotoFicha($_REQUEST);
            echo json_encode($process);
        } catch (Exception $e) {
            $response[] = array('message' => $e->getMessage());
            echo json_encode($response);
        }
        break;
    case 'subirFotoFichaCliente':
        $ext = explode('.', $_FILES['file']['name']);
        $extension = $ext[1];
        $file = $_REQUEST['idClientes'] . '-' . date('YmdHis') . '.' . $extension;
        //
        $path = dirname(__DIR__) . '/assets/images/clientes/' . $file;
        try {
            $upload = move_uploaded_file($_FILES['file']['tmp_name'], $path);
            $_REQUEST['idEmpresa'] = $_SESSION['idEmpresa'];
            $_REQUEST['file'] = $file;
            $process = $planilla->subirFotoFichaCliente($_REQUEST);
            echo json_encode($process);
        } catch (Exception $e) {
            $response[] = array('message' => $e->getMessage());
            echo json_encode($response);
        }
        break;
    case 'clientesDocumentosAdjuntos':
        $_REQUEST['idEmpresa'] = $_SESSION['idEmpresa'];
        $data = $planilla->clientesDocumentosAdjuntos($_REQUEST);
        echo json_encode($data);
        break;
    case 'subirClientesDocumentosAdjuntos':
        $ext = explode('.', $_FILES['file']['name']);
        $extension = $ext[1];
        $file = $_REQUEST['idClientes'] . '-' . date('YmdHis') . '.' . $extension;
        //
        $path = dirname(__DIR__) . '/assets/docClientes/' . $file;
        try {
            $upload = move_uploaded_file($_FILES['file']['tmp_name'], $path);
            $_REQUEST['idEmpresa'] = $_SESSION['idEmpresa'];
            $_REQUEST['file'] = $file;
            $process = $planilla->subirClientesDocumentosAdjuntos($_REQUEST);
            echo json_encode($process);
        } catch (Exception $e) {
            $response[] = array('message' => $e->getMessage());
            echo json_encode($response);
        }
        break;
    case 'eliminarClientesDocumentosAdjuntos':
        try {
            $path = dirname(__DIR__) . '/assets/docClientes/' . $_REQUEST['file'];
            unlink($path);
            $process = $planilla->eliminarClientesDocumentosAdjuntos($_REQUEST);
            echo json_encode($process);
        } catch (Exception $e) {
            $response[] = array('message' => $e->getMessage());
            echo json_encode($response);
        }
        break;
    case 'subirFotoFichaProducto':
        $ext = explode('.', $_FILES['file']['name']);
        $extension = $ext[1];
        $file = $_REQUEST['idProductos'] . '-' . date('YmdHis') . '.' . $extension;
        //
        $path = dirname(__DIR__) . '/assets/images/productos/' . $file;
        try {
            $upload = move_uploaded_file($_FILES['file']['tmp_name'], $path);
            $_REQUEST['idEmpresa'] = $_SESSION['idEmpresa'];
            $_REQUEST['file'] = $file;
            $process = $planilla->subirFotoFichaProducto($_REQUEST);
            echo json_encode($process);
        } catch (Exception $e) {
            $response[] = array('message' => $e->getMessage());
            echo json_encode($response);
        }
        break;
    case 'saveCampoConstructorPlanilla':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $process = $planilla->saveCampoConstructorPlanilla($_REQUEST);
        echo json_encode($process);
        break;
    case 'updateCampoConstructorPlanilla':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $process = $planilla->updateCampoConstructorPlanilla($_REQUEST);
        echo json_encode($process);
        break;
    case 'deleteCampoConstructorPlanilla':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $process = $planilla->deleteCampoConstructorPlanilla($_REQUEST);
        echo json_encode($process);
        break;
    case 'getCampoConstructorPlanilla':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $process = $planilla->getCampoConstructorPlanilla($_REQUEST);
        echo json_encode($process);
        break;
    default :
        $_POST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_POST['idEmpresas'] = $_SESSION['idEmpresa'];
        $process = $planilla->saveSabanaPlanilla($_POST);
        echo json_encode($process);
        break;
}
