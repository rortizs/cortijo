<?php

/** inventariosController
 *  @author Jonathan Juarez
 *  @version 2.1 20170831
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/config.php");
require_once("../models/dynamic.php");
require_once("../models/admin.php");
require_once("../models/inventarios.php");
require_once("../models/NumberToLetterConverter.class.php");
$dynamic = new Dynamic();
$admin = new Admin();
$inventarios = new Inventarios();
$converter = new NumberToLetterConverter();
if (empty($_SESSION['userName']) || empty($_SESSION['idRoles'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('error' => true, 'message' => 'Sesión expirada o no autorizada'));
    exit;
}
$dbC = Config::$dbD;
$dbS = $_SESSION['dbProject'];
$service = $_REQUEST['service'];
switch ($service) {
    case 'save':
        if (isset($_POST['table']) && $_POST['table'] == 'empresas') {
            $idPaises = '';
            if (isset($_POST['data']) && is_array($_POST['data'])) {
                foreach ($_POST['data'] as $campoEmpresa) {
                    if (isset($campoEmpresa['idPaises'])) {
                        $idPaises = $campoEmpresa['idPaises'];
                        break;
                    }
                }
            }
            $idPaisesNormalizado = trim((string) $idPaises);
            if ($idPaisesNormalizado === '' || !ctype_digit($idPaisesNormalizado)) {
                http_response_code(400);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array(
                    'error' => true,
                    'message' => 'Debe seleccionar un país válido antes de guardar la empresa; el país seleccionado no existe en el catálogo. Si Guatemala no aparece, primero debe reparar el catálogo de países.'
                ));
                exit;
            }
            $idPaises = (int) $idPaisesNormalizado;
            if ($idPaises <= 0 || !$admin->paisExiste($idPaises)) {
                http_response_code(400);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array(
                    'error' => true,
                    'message' => 'Debe seleccionar un país válido antes de guardar la empresa; el país seleccionado no existe en el catálogo. Si Guatemala no aparece, primero debe reparar el catálogo de países.'
                ));
                exit;
            }
            foreach ($_POST['data'] as $indiceCampo => $campoEmpresa) {
                if (isset($campoEmpresa['idPaises'])) {
                    $_POST['data'][$indiceCampo]['idPaises'] = (string) $idPaises;
                    break;
                }
            }
        }
        $tableStructure = $dynamic->tableStructure($dbC, $dbS, $_POST['table']);
        $campos = array();
        $valores = array();
        for ($a = 0; $a < count($tableStructure); $a++) {
            if ($tableStructure[$a]['COLUMN_NAME'] != 'id') {
                $campos[] = $tableStructure[$a]['COLUMN_NAME'];
                //$valores[] = $_POST['data'][($a - 1)][$tableStructure[$a]['COLUMN_NAME']];
                //
                switch ($tableStructure[$a]['COLUMN_NAME']) {
                    case 'pwd':
                        $valores[] = md5($_POST['data'][($a - 1)][$tableStructure[$a]['COLUMN_NAME']]);
                        break;
                    case 'created_at':
                        $valores[] = $dynamic->timestamp;
                        break;
                    case 'idEmpresas':
                        if ($_SESSION['idEmpresa'] != '') {
                            $valores[] = $_SESSION['idEmpresa'];
                        } else {
                            $valores[] = $_POST['data'][($a - 1)][$tableStructure[$a]['COLUMN_NAME']];
                        }
                        break;
                    case 'descripcion':
                        $valores[] = strtoupper($_POST['data'][($a - 1)][$tableStructure[$a]['COLUMN_NAME']]);
                        break;
                    case 'nombreComercial':
                        $valores[] = strtoupper($_POST['data'][($a - 1)][$tableStructure[$a]['COLUMN_NAME']]);
                        break;
                    case 'razonSocial':
                        $valores[] = strtoupper($_POST['data'][($a - 1)][$tableStructure[$a]['COLUMN_NAME']]);
                        break;
                    case 'codigo':
                        $codigo = "";
                        if ($_SESSION['idEmpresa'] != '') {
                            $codigo = $dynamic->codigos($table, $_SESSION['idEmpresa']);
                        } else {
                            $codigo = $dynamic->codigos($table, $_REQUEST['idEmpresas']);
                        }
                        $valores[] = $codigo;
                        break;
                    default:
                        $valores[] = $_POST['data'][($a - 1)][$tableStructure[$a]['COLUMN_NAME']];
                        break;
                }
            }
        }
        $saveData = $dynamic->saveData($dbS, $_POST['table'], $campos, $valores);
        break;
    case 'detalleTraslados':
        $detalleTraslados = $inventarios->detalleTraslados($_REQUEST['documento'], $_REQUEST['sucursal']);
        echo json_encode($detalleTraslados);
        break;
    case 'productoComponente':
        $infoProducto = $inventarios->getInfoProducto($_REQUEST['idProducto']);
        echo json_encode($infoProducto);
        break;
    case 'addItemOrdenCompra':
        $addItemOrdenCompra = $inventarios->addItemOrdenCompra($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($addItemOrdenCompra);
        break;
    case 'removeItemOrdenCompra':
        $removeItemOrdenCompra = $inventarios->removeItemOrdenCompra($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($removeItemOrdenCompra);
        break;
    case 'getOrdenCompraDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $data = $inventarios->getOrdenCompraDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'guardarOrdenCompra':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $decimals = $number[1];
        if (strlen($number[1]) === 1) {
            $decimals = $number[1] . '0';
        }
        $_REQUEST['totalEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $decimals . '/100 QUETZALES.';
        $data = $inventarios->guardarOrdenCompra($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarOrdenCompra':
        $data = $inventarios->eliminarOrdenCompra($_REQUEST);
        echo json_encode($data);
        break;
    case 'addItemCompra':
        $addItemCompra = $inventarios->addItemCompra($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($addItemCompra);
        break;
    case 'removeItemCompra':
        $remove = $inventarios->removeItemCompra($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($remove);
        break;
    case 'getCompraDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $data = $inventarios->getCompraDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'guardarCompra':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->guardarCompra($_REQUEST);
        echo json_encode($data);
        break;
    case 'addItemTraslado':
        $addItem = $inventarios->addItemTraslado($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($addItem);
        break;
    case 'getTrasladoDetalleUsuario':
        $data = $inventarios->getTrasladoDetalleUsuario($_SESSION['idUsuarios']);
        echo json_encode($data);
        break;
    case 'removeItemTraslado':
        $remove = $inventarios->removeItemTraslado($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($remove);
        break;
    case 'finalizarTraslado':
        $process = $inventarios->finalizarTraslado($_REQUEST, $_SESSION['idUsuarios'], $_SESSION['idEmpresa']);
        echo json_encode($process);
        break;
    case 'ingresarTraslado':
        $process = $inventarios->ingresarTraslado($_REQUEST, $_SESSION['idUsuarios'], $_SESSION['idEmpresa']);
        echo json_encode($process);
        break;
    case 'procesarAjuste':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $_REQUEST['documento'] = $_REQUEST['tipoDocumento'] . "-" . $_REQUEST['correlativo'];
        $process = $inventarios->procesarAjuste($_REQUEST);
        echo json_encode($process);
        break;
    case 'loadProductoByCodigo':
        $idEmpresas = $_SESSION['idEmpresa'];
        $data = $inventarios->loadProductoByCodigo($_REQUEST, $idEmpresas);
        echo json_encode($data);
        break;
    case'generarDescuentoDetalleCompra':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $data = $inventarios->generarDescuentoDetalleCompra($_REQUEST);
        break;
    case 'addItemRequisionCompra':
        $add = $inventarios->addItemRequisionCompra($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($add);
        break;
    case 'removeItemRequision':
        $remove = $inventarios->removeItemRequision($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($remove);
        break;
    case 'getRequisicionDetalle':
        $params['idUsuarios'] = $_SESSION['idUsuarios'];
        $params['idRequisicion'] = $_REQUEST['idRequisicion'];
        $data = $inventarios->getRequisicionDetalle($params);
        echo json_encode($data);
        break;
    case 'getRequisicion':
        $data = $inventarios->getRequisicion($_REQUEST['idRequisicion']);
        echo json_encode($data);
        break;
    case 'guardarRequisicion':
        $data = $inventarios->guardarRequisicion($_REQUEST, $_SESSION['idUsuarios'], $_SESSION['idEmpresa']);
        echo json_encode($data);
        break;
    case 'gestionarRC':
        $data = $inventarios->gestionarRC($_REQUEST);
        echo json_encode($data);
        break;
    case 'gestionarOC':
        $data = $inventarios->gestionarOC($_REQUEST);
        echo json_encode($data);
        break;
    case 'importarDetalleRequisicionaOrden':
        $data = $inventarios->importarDetalleRequisicionaOrden($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($data);
        break;
    case 'importarDetalleOCaCompras':
        $data = $inventarios->importarDetalleOCaCompras($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($data);
        break;
    case 'cancelarProcesoCompra':
        $process = $inventarios->cancelarProcesoCompra($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($process);
        break;
    case 'updateItemRequision':
        $process = $inventarios->updateItemRequision($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($process);
        break;
    case 'updateItemOrdenCompra':
        $process = $inventarios->updateItemOrdenCompra($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($process);
        break;
    case 'updateItemCompra':
        $process = $inventarios->updateItemCompra($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($process);
        break;
    case 'guardarVenta':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idSucursales'] = $_SESSION['idSucursalesS'] ?: $_REQUEST['idSucursales'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['total']);
        $numberD = explode(".", $_REQUEST['totalDolares']);
        $decimals = $number[1];
        $decimalsD = $numberD[1];
        if (strlen($number[1]) === 1) {
            $decimals = $number[1] . '0';
        }
        if (strlen($numberD[1]) === 1) {
            $decimalsD = $numberD[1] . '0';
        }
        $_REQUEST['totalEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $decimals . '/100 QUETZALES.';
        $_REQUEST['totalEnLetrasDolares'] = $converter->to_word($numberD[0]) . 'CON ' . $decimalsD . '/100 DOLARES.';
        $data = $inventarios->guardarVenta($_REQUEST);
        echo json_encode($data);
        break;
    case 'actualizarCompra':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->actualizarCompra($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarCompra':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->eliminarCompra($_REQUEST);
        echo json_encode($data);
        break;
    case 'actualizarVenta':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->actualizarVenta($_REQUEST);
        echo json_encode($data);
        break;
    case 'eliminarVenta':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->eliminarVenta($_REQUEST);
        echo json_encode($data);
        break;
    case 'getProductos':
        $data = $inventarios->getProductos();
        echo json_encode($data);
        break;
    case 'ingresoSeriesProducto':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $insert = $inventarios->ingresoSeriesProducto($_REQUEST);
        echo json_encode($insert);
        break;
    case 'getSeriesProducto':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->getSeriesProducto($_REQUEST);
        echo json_encode($data);
        break;
    case 'loadProductoSKU':
        $idEmpresas = $_SESSION['idEmpresa'];
        $data = $inventarios->loadProductoSKU($_REQUEST, $idEmpresas);
        echo json_encode($data);
        break;
    case 'addItemAjuste':
        $add = $inventarios->addItemAjuste($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($add);
        break;
    case 'getAjusteDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $_REQUEST['dbProject'] = $_SESSION['dbProject'];
        $data = $inventarios->getAjusteDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'removeItemAjuste':
        $remove = $inventarios->removeItemAjuste($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($remove);
        break;
    case 'removeSerieProducto':
        $remove = $inventarios->removeSerieProducto($_REQUEST);
        echo json_encode($remove);
        break;
    case 'confirmarPedido':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $_REQUEST['dbProject'] = $_SESSION['dbProject'];
        $process = $inventarios->confirmarPedido($_REQUEST);
        echo json_encode($process);
        break;
    case 'addItemImportacion':
        $add = $inventarios->addItemImportacion($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($add);
        break;
    case 'getImportacionesDetalle':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $data = $inventarios->getImportacionesDetalle($_REQUEST);
        echo json_encode($data);
        break;
    case 'removeItemImportacion':
        $remove = $inventarios->removeItemImportacion($_REQUEST);
        echo json_encode($remove);
        break;
    case 'addGastoImportacion':
        $valorFinal = $_REQUEST['valor'];
        $subtotal = '0.00';
        $iva = '0.00';
        if ($_REQUEST['idMoneda'] == 2) {
            $valorFinal = ($_REQUEST['valor'] * $_REQUEST['tipoCambio']);
        }
        if ($_REQUEST['idTipoDocumentoGasto'] == 2) {
            $subtotal = ($valorFinal / 1.12);
            $iva = ($valorFinal / 1.12) * 0.12;
        } else {
            $subtotal = $valorFinal;
        }
        $_REQUEST['valorFinal'] = $valorFinal;
        $_REQUEST['subtotal'] = $subtotal;
        $_REQUEST['iva'] = $iva;
        $add = $inventarios->addGastoImportacion($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($add);
        break;
    case 'getImportacionesGastos':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $data = $inventarios->getImportacionesGastos($_REQUEST);
        echo json_encode($data);
        break;
    case 'removeGastoImportacion':
        $remove = $inventarios->removeGastoImportacion($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($remove);
        break;
    case 'guardarImportacion':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->guardarImportacion($_REQUEST);
        echo json_encode($data);
        break;
    case 'updateItemImportacion':
        $remove = $inventarios->updateItemImportacion($_REQUEST);
        echo json_encode($remove);
        break;
    case 'eliminarImportacion':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->eliminarImportacion($_REQUEST);
        echo json_encode($data);
        break;
    case 'actualizarOrdenCompra':
        $_REQUEST['idUsuarios'] = $_SESSION['idUsuarios'];
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $number = explode(".", $_REQUEST['monto']);
        $_REQUEST['montoEnLetras'] = $converter->to_word($number[0]) . 'CON ' . $number[1] . '/100 ' . strtoupper($_REQUEST['moneda']) . '.';
        $data = $inventarios->actualizarOrdenCompra($_REQUEST);
        echo json_encode($data);
        break;
    case 'getOrdenCompraImport':
        $data = $inventarios->getOrdenCompraImport($_REQUEST);
        echo json_encode($data);
        break;
    case 'getFamiliaNivel1':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->getFamiliaNivel1($_REQUEST);
        echo json_encode($data);
        break;
    case 'getFamiliaNivel2':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->getFamiliaNivel2($_REQUEST);
        echo json_encode($data);
        break;
    case 'getFamiliaNivel3':
        $_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
        $data = $inventarios->getFamiliaNivel3($_REQUEST);
        echo json_encode($data);
        break;
    case 'updateImportacionesDetalle':
        $add = $inventarios->updateImportacionesDetalle($_REQUEST, $_SESSION['idUsuarios']);
        echo json_encode($add);
        break;
}
?>