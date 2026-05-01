<?php

/** contabilidadController
 *  @author Richard Sasvin
 * 
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/contabilidad.php");
$conta = new Contabilidad();
$service = $_REQUEST['service'];
switch ($service) {
    case 'getTipoOperacionesPartidas':
        $data = $conta->getTipoOperacionesPartidas();
        echo json_encode($data);
        break;
    case 'savePartida':
        $save = $conta->savePartida($_REQUEST, $_SESSION['idUsuarios'], $_SESSION['idEmpresa']);
        echo json_encode($save);
        break;
    case 'saveFormato':
        $save = $conta->saveFormato($_REQUEST, $_SESSION['idUsuarios'], $_SESSION['idEmpresa']);
        echo json_encode($save);
        break;
    case 'getIDP':
        $params['empresa'] = $_SESSION['nombreEmpresa'];
        $data = $conta->getIDP($params);
        echo json_encode($data);
        break;
    case 'getPartidaDetalle':
        $data = $conta->getLibroDiarioDetalle($_REQUEST['idPartida'], 'detallado');
        echo json_encode($data);
        break;
    case 'getFormatoDetalle':
        $data = $conta->getFormatoDetalle($_REQUEST['idFormato']);
        echo json_encode($data);
        break;
    case 'updatePartida':
        $update = $conta->updatePartida($_REQUEST, $_SESSION['idEmpresa']);
        echo json_encode($update);
        break;
    case 'updateFormato':
        $update = $conta->updateFormato($_REQUEST, $_SESSION['idEmpresa']);
        echo json_encode($update);
        break;
}