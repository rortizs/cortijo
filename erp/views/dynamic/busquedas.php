<?php
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../../models/config.php");
require_once("../../models/dynamic.php");
$dynamic = new Dynamic();
$dbC = Config::$dbD;
$dbS = $_SESSION['dbProject'];
$table = str_replace('vw_', '', $_REQUEST['table']);
$tableStructure = $dynamic->tableStructure($dbC, $dbS, $table);
//PROCESOS
// 1. SAVEDATA
if ($_REQUEST['flag'] == 1) {
    $campos = array();
    $valores = array();
    if ($table != 'roles' && $table != 'hrmTipoEvento') {
        $campos[] = 'idEmpresas';
        $valores[] = $_SESSION['idEmpresa'];
    }
    if ($table == 'usuarios') {
        $campos[] = 'pwd';
        $valores[] = md5($_REQUEST['pwd_real']);
    }
    for ($a = 0; $a < count($tableStructure); $a++) {
        if ($tableStructure[$a]['COLUMN_NAME'] != 'id' && $tableStructure[$a]['COLUMN_NAME'] != 'saldoLibros' && $tableStructure[$a]['COLUMN_NAME'] != 'saldoBanco') {
            $campos[] = $tableStructure[$a]['COLUMN_NAME'];
            switch ($tableStructure[$a]['COLUMN_NAME']) {
                case 'pwd':
                    $valores[] = md5($_REQUEST['pwd_real']);
                    break;
                case 'updated_at':
                    $valores[] = NULL;
                    break;
                case 'created_at':
                    $valores[] = $dynamic->timestamp;
                    break;
                case 'descripcion':
                    $valores[] = strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]);
                    break;
                case 'nombreComercial':
                    $valores[] = strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]);
                    break;
                case 'razonSocial':
                    $valores[] = strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]);
                    break;
                case 'abrev':
                    $valores[] = strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]);
                    break;
                case 'serie':
                    $valores[] = strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]);
                    break;
                case 'prefijo':
                    $valores[] = strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]);
                    break;
                case 'descLarga':
                    $valores[] = strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]);
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
                case 'init_at':
                    $valores[] = date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]));
                    break;
                case 'end_at':
                    $valores[] = date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]));
                    break;
                case 'fechaNac':
                    $valores[] = date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]));
                    break;
                case 'fechaIngreso':
                    $valores[] = date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]));
                    break;
                case 'fechaEgreso':
                    $valores[] = date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]));
                    break;
                case 'fechaHoraInicio':
                    $valores[] = date("Y-m-d H:i:s", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]));
                    break;
                case 'fechaHoraFin':
                    $valores[] = date("Y-m-d H:i:s", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]));
                    break;
                default:
                    $valores[] = $_REQUEST[$tableStructure[$a]['COLUMN_NAME']];
                    break;
            }
        }
    }
    //
    $saveData = $dynamic->saveData($dbS, $table, $campos, $valores);
    $message = $saveData;
}
//2. UPDATE RECORD
if ($_REQUEST['flag'] == 2) {
    $fieldsValues = "";
    if ($table !== 'empresas' && $table !== 'roles' && $table != 'hrmTipoEvento') {
        $fieldsValues .= "idEmpresas =" . "'" . $_SESSION['idEmpresa'] . "',";
    }
    if ($table == 'usuarios') {
        $fieldsValues .= "pwd =" . "'" . md5($_REQUEST['pwd_real']) . "',";
    }
    for ($a = 0; $a < count($tableStructure); $a++) {
        if ($tableStructure[$a]['COLUMN_NAME'] != 'id' && $tableStructure[$a]['COLUMN_NAME'] != 'image' && $tableStructure[$a]['COLUMN_NAME'] != 'codigo' && $tableStructure[$a]['COLUMN_NAME'] != 'saldoLibros' && $tableStructure[$a]['COLUMN_NAME'] != 'saldoBanco' && $tableStructure[$a]['COLUMN_NAME'] != 'estado') {
            switch ($tableStructure[$a]['COLUMN_NAME']) {
                case 'updated_at':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . $dynamic->timestamp . "',";
                    break;
                case 'descripcion':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]) . "',";
                    break;
                case 'nombreComercial':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]) . "',";
                    break;
                case 'razonSocial':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]) . "',";
                    break;
                case 'abrev':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]) . "',";
                    break;
                case 'serie':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]) . "',";
                    break;
                case 'prefijo':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]) . "',";
                    break;
                case 'descLarga':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . strtoupper($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]) . "',";
                    break;
                case 'init_at':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']])) . "',";
                    break;
                case 'end_at':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']])) . "',";
                    break;
                case 'fechaNac':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']])) . "',";
                    break;
                case 'fechaIngreso':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']])) . "',";
                    break;
                case 'fechaEgreso':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']])) . "',";
                    break;
                case 'fechaHoraInicio':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . date("Y-m-d H:i:s", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']])) . "',";
                    break;
                case 'fechaHoraFin':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . date("Y-m-d H:i:s", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']])) . "',";
                    break;
                case 'fechaVale':
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . date("Y-m-d", strtotime($_REQUEST[$tableStructure[$a]['COLUMN_NAME']])) . "',";
                    break;
                default:
                    $fieldsValues .= $tableStructure[$a]['COLUMN_NAME'] . "=" . "'" . $_REQUEST[$tableStructure[$a]['COLUMN_NAME']] . "',";
                    break;
            }
        }
    }
    $updateRecord = $dynamic->updateRecord($dbS, $table, substr($fieldsValues, 0, strlen($fieldsValues) - 1), $_REQUEST['campo'], $_REQUEST['valor']);
    $message = $updateRecord;
}
//3. DELETE RECORD
if ($_REQUEST['flag'] == 3) {
    $deleteRecord = $dynamic->deleteRecord($dbS, $table, $_REQUEST['id']);
    $message = $deleteRecord;
}
// END PROCESOS
?>
<div class="row">
    <div class="col-lg-12">
        <!-- CONTROLLERS -->
        <?php
        if ($_REQUEST['btnNew'] == 1) {
            ?>
            <button type="button" class="btn btn-success btn-sm" onclick="AddRecord('<?= $table; ?>',<?= $_REQUEST['btnNew']; ?>,<?= $_REQUEST['btnUpdate']; ?>,<?= $_REQUEST['btnDelete']; ?>, '<?= $_REQUEST['modulo']; ?>', '<?= $_REQUEST['opcion']; ?>');">
                <span class="fa fa-plus"></span> Nuevo
            </button>
            <?php
        }
        ?>
        <?php
        if ($_REQUEST['btnUpdate'] == 1) {
            ?>
            <button type="button" class="btn btn-warning btn-sm" onclick="updateRecord('<?= $table; ?>',<?= $_REQUEST['btnNew']; ?>,<?= $_REQUEST['btnUpdate']; ?>,<?= $_REQUEST['btnDelete']; ?>, '<?= $_REQUEST['modulo']; ?>', '<?= $_REQUEST['opcion']; ?>');">
                <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar 
            </button>
            <?php
        }
        ?>
        <?php
        if ($_REQUEST['btnDelete'] == 1) {
            ?>
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRecord('<?= $table; ?>',<?= $_REQUEST['btnNew']; ?>,<?= $_REQUEST['btnUpdate']; ?>,<?= $_REQUEST['btnDelete']; ?>, '<?= $_REQUEST['modulo']; ?>', '<?= $_REQUEST['opcion']; ?>');">
                <span class="fa fa-trash"></span> Eliminar
            </button>
            <?php
        }
        ?>
        <?php
        switch ($_REQUEST['table']) {
            case 'vw_comprasRequisicion':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="nuevaRequisicion();">
                    <i class="fa fa-plus"></i>&nbsp;Nueva Requisición
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="imprimirRequisicion();">
                    <i class="fa fa-print"></i>&nbsp;Re-Imprimir Requisición
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="gestionarRequisicion();">
                    <i class="fa fa-check"></i>&nbsp;Gestionar Requisición
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="importarRequisicionAOrden();">
                    <i class="fa fa-list-ol"></i>&nbsp;Importar Requisición a Orden
                </button>
                <?php
                break;
            case 'vw_comprasOrdenes':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="nuevaOrdenCompra();">
                    <i class="fa fa-plus"></i>&nbsp;Nueva Orden de Compra
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="updateOrdenCompra();">
                    <i class="fa fa-pencil"></i>&nbsp;Ver / Editar Orden de Compra
                </button>
                <!--
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarOrdenCompra();">
                    <i class="fa fa-trash"></i>&nbsp;Eliminar Orden de Compra
                </button>
                -->
                <button type="button" class="btn btn-info btn-sm" onclick="imprimirOrdenCompra();">
                    <i class="fa fa-print"></i>&nbsp;Imprimir Orden de Compra
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="gestionarOrdenCompra();">
                    <i class="fa fa-check"></i>&nbsp;Gestionar Orden de Compra
                </button>
                <?php
                break;
            case 'vw_compras':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadRecepcionCompras();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="updateCompra();">
                    <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                </button>
                <!--
                <button type="button" class="btn btn-info btn-sm" onclick="imprimirCompra();">
                    <i class="fa fa-print"></i>&nbsp;Re-Imprimir
                </button>-->
                <?php
                break;
            case 'vw_importaciones':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadRecepcionImportacion();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="consultarImportacion();">
                    <span class="fa fa-eye"></span> Consultar Importacion
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarImportacion();">
                    <span class="fa fa-trash"></span> Eliminar Importacion
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="imprimirImportacion();">
                    <i class="fa fa-print"></i>&nbsp;Re-Imprimir
                </button>
                <?php
                break;
            case 'vw_ventas':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadFacturacion();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="updateVenta();">
                    <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                </button>
                <!--
                <button class="btn btn-danger btn-sm" onclick="eliminarFactura();">
                    <i class="fa fa-trash"></i> Eliminar Registro
                </button>
                <button class="btn btn-danger btn-sm" onclick="anularFactura();">
                    <i class="fa fa-trash"></i> Anular Factura
                </button>
                -->
                <?php
                break;
            case 'vw_ajustes':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="ingresarAjuste();">
                    <i class="fa fa-plus"></i>&nbsp;Ingresar Ajuste
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="reImprimirAjuste();">
                    <i class="fa fa-print"></i>&nbsp;Re-Imprimir Ajuste
                </button>
                <?php
                break;
            case 'vw_productos':
                ?>
                <button type="button" class="btn btn-info btn-sm" onclick="loadComponentes();">
                    <i class="fa fa-list"></i>&nbsp;Componentes
                </button>
                <!--
                <button type="button" class="btn btn-danger btn-sm" onclick="loadCodigoBarra();">
                    <i class="fa fa-barcode" ></i>&nbsp;Imprimir Codigo De Barra
                </button>
                -->
                <?php
                break;
            case 'productos':
                ?>
                <button type="button" class="btn btn-info btn-sm" onclick="loadComponentes();">
                    <i class="fa fa-list"></i>&nbsp;Componentes
                </button>
                <?php
                break;
            case 'vw_ordenesCompra':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="crearOrdenCompra();">
                    <i class="fa fa-plus"></i>&nbsp;Crear Orden Compra
                </button>
                <?php
                break;
            case 'inventarioBodegas':
                break;
            case 'inventarioSucursales':
                break;
            case 'vw_traslados':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadTraslados();">
                    <i class="fa fa-plus"></i>&nbsp;Crear Traslado
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="detalleTraslado();">
                    <i class="fa fa-list"></i>&nbsp;Ingreso de Traslado
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="reImprimirTraslados();">
                    <i class="fa fa-print"></i>&nbsp;Re-imprimir Traslado
                </button>
                <?php
                break;
            case 'vw_clientes':
                ?>
                <div class="input-group" style="width: 700px !important; float: right;">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-danger btn-sm" onclick="estadoCuentaClientes();" style="width:100% !important;">
                            <i class="fa fa-file-pdf-o"></i>&nbsp;Imprimir Estado de Cuenta
                        </button>
                    </span>
                    <span class="input-group-btn">
                        <input type="date" class="form-control input-sm" id="fechaInicioCXC" placeholder="fecha inicial dd-mm-yyyy"/>
                    </span>
                    <span class="input-group-btn">
                        <input type="date" class="form-control input-sm" id="fechaFinCXC" placeholder="fecha final dd-mm-yyyy"/>
                    </span>
                </div>
                <?php
                break;
            case 'vw_proveedores':
                ?>
                <div class="input-group" style="width: 700px !important; float: right;">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-danger btn-sm" onclick="estadoCuentaProveedores();" style="width:100% !important;">
                            <i class="fa fa-file-pdf-o"></i>&nbsp;Imprimir Estado de Cuenta
                        </button>
                    </span>
                    <span class="input-group-btn">
                        <input type="text" class="form-control input-sm" id="fechaInicioCXP" placeholder="fecha inicial dd-mm-yyyy"/>
                    </span>
                    <span class="input-group-btn">
                        <input type="text" class="form-control input-sm" id="fechaFinCXP" placeholder="fecha final dd-mm-yyyy"/>
                    </span>
                </div>
                <?php
                break;
            case 'vw_hrmHorasExtras':
                ?>
                <button class="btn btn-info btn-sm" onclick="templateDownload('<?= $_REQUEST['table']; ?>');">
                    <i class="fa fa-download"></i> Descargar Plantilla
                </button>
                <div class="input-group m-bot15 col-lg-4 pull-right">
                    <span class="input-group-btn">
                        <button class="btn btn-success btn-sm" id="btnUpload" onclick="uploadTemplate('<?= $_REQUEST['table']; ?>');">
                            <i class="fa fa-upload"></i> Importar Plantilla
                        </button>
                    </span>
                    <input class="form-control input-sm" type="file" readonly="" id="my_file">
                </div>
                <?php
                break;
            case 'vw_hrmComisiones':
                ?>
                <button class="btn btn-info btn-sm" onclick="templateDownload('<?= $_REQUEST['table']; ?>');">
                    <i class="fa fa-download"></i> Descargar Plantilla
                </button>
                <div class="input-group m-bot15 col-lg-4 pull-right">
                    <span class="input-group-btn">
                        <button class="btn btn-success btn-sm" id="btnUpload" onclick="uploadTemplate('<?= $_REQUEST['table']; ?>');">
                            <i class="fa fa-upload"></i> Importar Plantilla
                        </button>
                    </span>
                    <input class="form-control input-sm" type="file" readonly="" id="my_file">
                </div>
                <?php
                break;
            case 'vw_hrmOtrosPagosDescuentos':
                ?>
                <button class="btn btn-info btn-sm" onclick="templateDownload('<?= $_REQUEST['table']; ?>');">
                    <i class="fa fa-download"></i> Descargar Plantilla
                </button>
                <div class="input-group m-bot15 col-lg-4 pull-right">
                    <span class="input-group-btn">
                        <button class="btn btn-success btn-sm" id="btnUpload" onclick="uploadTemplate('<?= $_REQUEST['table']; ?>');">
                            <i class="fa fa-upload"></i> Importar Plantilla
                        </button>
                    </span>
                    <input class="form-control input-sm" type="file" readonly="" id="my_file">
                </div>
                <?php
                break;
            case 'vw_hrmPrestamos':
                ?>
                <button class="btn btn-info btn-sm" onclick="templateDownload('<?= $_REQUEST['table']; ?>');">
                    <i class="fa fa-download"></i> Descargar Plantilla
                </button>
                <div class="input-group m-bot15 col-lg-4 pull-right">
                    <span class="input-group-btn">
                        <button class="btn btn-success btn-sm" id="btnUpload" onclick="uploadTemplate('<?= $_REQUEST['table']; ?>');">
                            <i class="fa fa-upload"></i> Importar Plantilla
                        </button>
                    </span>
                    <input class="form-control input-sm" type="file" readonly="" id="my_file">
                </div>
                <?php
                break;
            case 'vw_hrmAnticipos':
                ?>
                <button class="btn btn-info btn-sm" onclick="templateDownload('<?= $_REQUEST['table']; ?>');">
                    <i class="fa fa-download"></i> Descargar Plantilla
                </button>
                <div class="input-group m-bot15 col-lg-4 pull-right">
                    <span class="input-group-btn">
                        <button class="btn btn-success btn-sm" id="btnUpload" onclick="uploadTemplate('<?= $_REQUEST['table']; ?>');">
                            <i class="fa fa-upload"></i> Importar Plantilla
                        </button>
                    </span>
                    <input class="form-control input-sm" type="file" readonly="" id="my_file">
                </div>
                <?php
                break;
            case 'vw_partidas':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadPartida();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="loadPartida('edit');">
                    <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                </button>
                <?php
                break;
            case 'vw_formatos':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadFormato();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="loadFormato('edit');">
                    <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                </button>
                <?php
                break;
            case 'vw_nomenclatura':
                ?>
                <button type="button" class="btn btn-info btn-sm" onclick="imprimirNomenclatura();">
                    <i class="fa fa-print"></i>&nbsp;Imprimir
                </button>
                <?php
                break;
            case 'vw_cheques':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadCheques();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="getCheque();">
                    <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                </button>
                <?php
                break;
            case 'vw_depositos':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadDepositos();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDeposito();">
                    <span class="fa fa-trash"></span> Eliminar
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="reImprimirDocBancos(1);">
                    <span class="fa fa-print"></span> Re-Imprimir Documento
                </button>
                <?php
                break;
            case 'vw_notasDebitoBancos':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadNDBancos();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="getNDBancos();">
                    <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarNDBancos();">
                    <span class="fa fa-trash"></span> Eliminar
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="reImprimirDocBancos(3);">
                    <span class="fa fa-print"></span> Re-Imprimir Documento
                </button>
                <?php
                break;
            case 'vw_notasCreditoBancos':
                ?>
                <button type="button" class="btn btn-success btn-sm" onclick="loadNCBancos();">
                    <i class="fa fa-plus"></i>&nbsp;Nuevo
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="getNCBancos();">
                    <span class="fa fa-eye"></span> Ver | <span class="fa fa-pencil"></span> Editar
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarNCBancos();">
                    <span class="fa fa-trash"></span> Eliminar
                </button>
                <button type="button" class="btn btn-info btn-sm" onclick="reImprimirDocBancos(2);">
                    <span class="fa fa-print"></span> Re-Imprimir Documento
                </button>
                <?php
                break;
            case 'vw_hrmPoliticasPago':
                ?>
                <button class="btn btn-info btn-sm" onclick="templateDownload('<?= $_REQUEST['table']; ?>');">
                    <i class="fa fa-download"></i> Descargar Plantilla
                </button>
                <div class="input-group m-bot15 col-lg-4 pull-right">
                    <span class="input-group-btn">
                        <button class="btn btn-success btn-sm" id="btnUpload" onclick="uploadTemplate('<?= $_REQUEST['table']; ?>');">
                            <i class="fa fa-upload"></i> Importar Plantilla
                        </button>
                    </span>
                    <input class="form-control input-sm" type="file" readonly="" id="my_file">
                </div>
                <?php
                break;
            case 'vw_cajaChica':
                ?>
                <button class="btn btn-success btn-sm" onclick="crearCajaChica();">
                    <i class="fa fa-plus"></i> Nuevo
                </button>
                <button class="btn btn-info btn-sm" onclick="imprimirLiquidacionCajaChica();">
                    <i class="fa fa-print"></i> Cerrar e Imprimir Liquidacion
                </button>
                <button class="btn btn-warning btn-sm" onclick="abrirCajaChica();">
                    <i class="fa fa-check"></i> Abrir Liquidacion
                </button>
                <?php
                break;
            case 'vw_hrmPlanillas':
                ?>
                <button class="btn btn-info btn-sm" onclick="constructorPlanilla();">
                    <i class="fa fa-plus"></i> Campos Planilla
                </button>
                <?php
                break;
            case 'vw_corteCaja':
                ?>
                <button class="btn btn-info btn-sm" onclick="imprimirCorteCaja();">
                    <i class="fa fa-print"></i> Imprimir Corte Caja
                </button>
                <?php
                break;
        }
        ?>
    </div>
</div>
<!-- /CONTROLLERS -->
<?= $message; ?>
<div class="clear">&nbsp;</div>
<!--/ GRID-->
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <table id="dynamicTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <?php
                            switch ($_REQUEST['table']) {
                                case 'inventarioBodegas':
                                    ?>
                                    <td>No.</td>
                                    <td>Bodega</td>
                                    <td>Codigo Producto</td>
                                    <td>Descripcion Producto</td>
                                    <td>Ingreso</td>
                                    <td>Salida</td>
                                    <td>Saldo</td>
                                    <td>Unidad de Medida</td>
                                    <td>Fecha de Ingreso</td>
                                    <?php
                                    break;
                                case 'inventarioSucursales':
                                    ?>
                                    <td>Sucursal</td>
                                    <td>Codigo Producto</td>
                                    <td>Descripcion Producto</td>
                                    <td>Tipo de Producto</td>
                                    <td>Ingreso</td>
                                    <td>Salida</td>
                                    <td>Saldo</td>
                                    <td>Unidad de Medida</td>
                                    <td>Fecha de Ingreso</td>
                                    <?php
                                    break;
                                default:
                                    for ($a = 0; $a < count($tableStructure); $a++) {
                                        $commentField = explode(",", $tableStructure[$a]['COLUMN_COMMENT']);
                                        ?>
                                        <td><?= $commentField[0]; ?></td>
                                        <?php
                                    }
                                    break;
                            }
                            ?>
                        </tr>
                    </thead>
                    <thead class="filters">
                        <tr class="info">
                            <?php
                            switch ($_REQUEST['table']) {
                                case 'inventarioBodegas':
                                    ?>
                                    <td>No.</td>
                                    <td>Bodega</td>
                                    <td>Codigo Producto</td>
                                    <td>Descripcion Producto</td>
                                    <td>Ingreso</td>
                                    <td>Salida</td>
                                    <td>Saldo</td>
                                    <td>Unidad de Medida</td>
                                    <td>Fecha de Ingreso</td>
                                    <?php
                                    break;
                                case 'inventarioSucursales':
                                    ?>
                                    <td>Sucursal</td>
                                    <td>Codigo Producto</td>
                                    <td>Descripcion Producto</td>
                                    <td>Productos</td>
                                    <td>Ingreso</td>
                                    <td>Salida</td>
                                    <td>Saldo</td>
                                    <td>Unidad de Medida</td>
                                    <td>Fecha de Ingreso</td>
                                    <?php
                                    break;
                                default:
                                    for ($a = 0; $a < count($tableStructure); $a++) {
                                        $commentField = explode(",", $tableStructure[$a]['COLUMN_COMMENT']);
                                        ?>
                                        <td><?= $commentField[0]; ?></td>
                                        <?php
                                    }
                                    break;
                            }
                            ?>
                        </tr>
                    </thead>	
                </table>
                <!--/ GRID-->
            </div>
        </section>
    </div>
</div>