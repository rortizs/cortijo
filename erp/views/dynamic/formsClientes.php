<?php
/**
 * dynamic.php form empleados
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
require_once("../../models/config.php");
require_once("../../models/dynamic.php");
$dynamic = new Dynamic();
$dbC = Config::$dbD;
$dbS = $_SESSION['dbProject'];
$table = $_REQUEST['table'];
$field = $_REQUEST['field'];
$value = $_REQUEST['value'];
$flag = $_REQUEST['flag'];
$tableStructure = $dynamic->tableStructure($dbC, $dbS, $table);
$getRecord = $dynamic->getRecord($dbS, $table, $field, $value);
$img = 'assets/images/clientes/' . $getRecord['image'];
if (!$getRecord['image']) {
    $img = '../../assets/images/empleados/user.png';
}
$getRecordView = $dynamic->getRecord($dbS, 'vw_hrmEmpleados', $field, $value);
$group1 = "";
$group2 = "";
for ($a = 0; $a < count($tableStructure); $a++) {
    $commentField = explode(",", $tableStructure[$a]['COLUMN_COMMENT']);
    $requiredField = '';
    $idBox = '';
    if ($tableStructure[$a]['COLUMN_NAME'] != 'id' && $tableStructure[$a]['COLUMN_NAME'] != 'image' && $tableStructure[$a]['COLUMN_NAME'] != 'created_at' && $tableStructure[$a]['COLUMN_NAME'] != 'updated_at') {
        $campos1 .= $tableStructure[$a]['COLUMN_NAME'] . ",";
        if ($tableStructure[$a]['IS_NULLABLE'] == 'NO') {
            $names = explode(",", $tableStructure[$a]['COLUMN_COMMENT']);
            $required .= $tableStructure[$a]['COLUMN_NAME'] . ",";
            $requiredName .= $names[0] . ",";
            $requiredField = '<span style="color:red !important;">*</span>';
        }
        $editor = '';
        $idBox = $tableStructure[$a]['COLUMN_NAME'];
        switch ($tableStructure[$a]['DATA_TYPE']) {
            case 'int':
                $tableR = strtolower(substr($tableStructure[$a]['COLUMN_NAME'], 2, 1)) . substr($tableStructure[$a]['COLUMN_NAME'], 3, strlen($tableStructure[$a]['COLUMN_NAME']));
                if ($tableR == 'medidas2') {
                    $tableR = str_replace('2', '', strtolower(substr($tableStructure[$a]['COLUMN_NAME'], 2, 1)) . substr($tableStructure[$a]['COLUMN_NAME'], 3, strlen($tableStructure[$a]['COLUMN_NAME'])));
                }
                $parametros = "";
                if ($_SESSION['idEmpresa'] != '') {
                    if ($tableR != 'documentos' && $tableR != 'tipoCuenta' && $tableR != 'tipoCostos' && $tableR != 'paises' && $tableR != 'departamentos' && $tableR != 'municipios' && $tableR != 'tipoClientes' && $tableR != 'listasPrecios' && $tableR != 'tipoProductos' && $tableR != 'fabricaProducto' && $tableR != 'statusProducto' && $tableR != 'roles' && $tableR != 'statusEmpleado' && $tableR != 'anos' && $tableR != 'meses' && $tableR != 'hrmStatusEmpleado' && $tableR != 'hrmOperacionPD' && $tableR != 'availableSale' && $tableR != 'pequenoContribuyente' && $tableR != 'tipoCuentaContable' && $tableR != 'tipoOperacionContable' && $tableR != 'availableCreditLimit' && $tableR != 'availablePrint' && $tableR != 'hrmTipoPago' && $tableR != 'metodoDias' && $tableR != 'hrmTipoEvento' && $tableR != 'hrmStatusEmpleado' && $tableR != 'hrmTipoContrato' && $tableR != 'combustibles' && $tableR != 'hrmCentroTrabajoSeguroSocial' && $tableR != 'hrmEstadosCiviles' && $tableR != 'hrmTipoDocID' && $tableR != 'hrmMunicipios' && $tableR != 'hrmGeneros' && $tableR != 'hrmEtnias' && $tableR != 'hrmIdiomas' && $tableR != 'hrmTipoJornada' && $tableR != 'utilizaSerie' && $tableR != 'agenteRetenedor') {
                        $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'];
                    }
                    if ($tableR == 'nomenclatura') {
                        $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'] . ' and idTipoOperacionContable=2';
                    }
                    if ($tableR == 'centrosCosto') {
                        $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'] . ' and idTipoOperacionContable=2';
                    }
                    if ($tableR == 'hrmEmpleados') {
                        $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'] . ' and idHrmStatusEmpleado=1 ORDER BY primerNombre asc';
                    }
                    if ($tableR == 'roles') {
                        $parametros = 'where id!=1';
                    }
                    if ($tableR == 'paises') {
                        $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'];
                    }
                }
                $dataTable = $dynamic->dataTable($dbS, $tableR, $parametros);
                $selectTable = $dynamic->tableStructure($dbC, $dbS, $tableR);
                $editor = "<select class='form-control input-sm selectpicker btn-sm' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' data-live-search='true'>";
                $editor .= "<option value=''>[seleccione...]</option>";
                for ($b = 0; $b < count($dataTable); $b++) {
                    switch ($tableR) {
                        case 'clasificacionClientes':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            }
                            break;
                        case 'nomenclatura':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            }
                            break;
                        case 'centrosCosto':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            }
                            break;
                        case 'hrmEmpleados':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b]['codigoEmpleado'] . " - " . $dataTable[$b]['primerNombre'] . " " . $dataTable[$b]['segundoNombre'] . " " . $dataTable[$b]['primerApellido'] . " " . $dataTable[$b]['segundoApellido'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b]['codigoEmpleado'] . " - " . $dataTable[$b]['primerNombre'] . " " . $dataTable[$b]['segundoNombre'] . " " . $dataTable[$b]['primerApellido'] . " " . $dataTable[$b]['segundoApellido'] . "</option>";
                            }
                            break;
                        case 'hrmMunicipios':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b]['codigoMunicipio'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b]['codigoMunicipio'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            }
                        case 'hrmDepartamentos':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b]['codigoDepto'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b]['codigoDepto'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            }
                            break;
                        case 'hrmPuestos':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b]['codigoPuesto'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b]['codigoPuesto'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            }
                            break;
                        case 'hrmPagosDescuentos':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b]['codigoPD'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b]['codigoPD'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            }
                            break;
                        case 'hrmCentroTrabajoSeguroSocial':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b]['codigo'] . " - " . $dataTable[$b]['nombre'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b]['codigo'] . " - " . $dataTable[$b]['nombre'] . "</option>";
                            }
                            break;
                        case 'bancos':
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b]['codigoBanco'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b]['codigoBanco'] . " - " . $dataTable[$b]['descripcion'] . "</option>";
                            }
                            break;
                        default:
                            if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . "</option>";
                            } else {
                                $editor .= "<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . "</option>";
                            }
                            break;
                    }
                }
                $editor .= "</select>";
                break;
            case 'varchar':
                $transformText = "";
                switch ($tableStructure[$a]['COLUMN_NAME']) {
                    case 'descripcion':
                        $transformText = "text-uppercase";
                        break;
                    case 'nombreComercial':
                        $transformText = "text-uppercase";
                        break;
                    case 'razonSocial':
                        $transformText = "text-uppercase";
                        break;
                    case 'abrev':
                        $transformText = "text-uppercase";
                        break;
                    case 'prefijo':
                        $transformText = "text-uppercase";
                        break;
                    case 'descLarga':
                        $transformText = "text-uppercase";
                        break;
                    case 'serie':
                        $transformText = "text-uppercase";
                        break;
                }
                if ($tableStructure[$a]['COLUMN_NAME'] == 'pwd_real') {
                    $editor = "<input class='form-control input-sm " . $transformText . "' type='password' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='30' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                } else {
                    $editor = "<input class='form-control input-sm " . $transformText . "' type='text' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='60' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                }
                break;
            case 'text':
                $editor = "<textarea class='form-control input-sm' id='" . $tableStructure[$a]['COLUMN_NAME'] . "'>" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "</textarea>";
                break;
            case 'date':
                $val = date("d-m-Y", strtotime(($getRecord[$tableStructure[$a]['COLUMN_NAME']] == '') ? $dynamic->date : $getRecord[$tableStructure[$a]['COLUMN_NAME']]));
                $editor = "<input class='form-control input-sm date' type='text' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='30' value='" . $val . "'/>";
                break;
            case 'time':
                $editor = "<input class='form-control input-sm time' type='text' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='30' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                break;
            case 'double':
                $editor = "<input class='form-control input-sm' type='text' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='60' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                break;
        }
        if ($a <= 30) {
            $group1 .= '<div class="col-xs-' . $commentField[1] . '" id="box-' . $idBox . '">';
            $group1 .= '<label id="lbl-' . $tableStructure[$a]['COLUMN_NAME'] . '">' . $commentField[0] . '&nbsp;' . $requiredField . '</label> ' . $editor;
            $group1 .= '</div>';
        }
        if ($a > 30) {
            $group2 .= '<div class="col-xs-' . $commentField[1] . '" id="box-' . $idBox . '>';
            $group2 .= '<label id="lbl-' . $tableStructure[$a]['COLUMN_NAME'] . '">' . $commentField[0] . '&nbsp;' . $requiredField . '</label> ' . $editor;
            $group2 .= '</div>';
        }
    }
}
$periodos = array(
    "Mensual", "Bimestral", "Trimestral", "Semestral", "Anual"
);
$tipoCuotas = array(
    "Indefinida", "Periodo"
);
?>
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <div class="panel-body profile-information">
                <div class="col-md-3">
                    <div class="profile-pic text-center">
                        <input type="image" id="preview" src="<?= $img; ?>" class="img-circle"/>
                        <input type="file" id="photo" style="display: none"/>
                        <input type="hidden" id="idClientes" value="<?= $_REQUEST['value']; ?>"/>
                    </div>
                    <center>
                        <button onclick="subirFotoFichaCliente();" id="btnUpload" class="btn btn-primary btn-sm" style="width: 50% !important;">
                            Subir Foto <i class="fa fa-cloud-upload"></i>
                        </button>
                    </center>
                </div>
                <div class="col-lg-9">
                    <div class="profile-desk">
                        <h1>
                            Nombre: <?= $getRecord['nombreC']; ?><br/>
                            Codigo: <?= $getRecord['codigoC']; ?>
                        </h1>
                        <br/>
                        <b>Direccion:</b> <?= $getRecord['direccionC']; ?><br/>
                        <b>NIT:</b> <?= $getRecord['nitC']; ?>/ <b>Telefono:</b> <?= $getRecord['telefonoC']; ?>
                    </div>
                    <div class="clear">&nbsp;</div>
                    <div class="col-lg-12">
                        <?php
                        if ($flag == 1) {
                            ?>
                            <button type="button" class="btn btn-primary btn-sm" onclick="saveRecord('<?= substr($campos1, 0, strlen($campos1) - 1); ?>', '<?= $table; ?>',<?= $_REQUEST['btnNew']; ?>,<?= $_REQUEST['btnUpdate']; ?>,<?= $_REQUEST['btnDelete']; ?>, '<?= $_REQUEST['modulo']; ?>', '<?= $_REQUEST['opcion']; ?>', '<?= substr($required, 0, strlen($required) - 1); ?>', '<?= substr($requiredName, 0, strlen($requiredName) - 1); ?>');">
                                <span class="fa fa-cloud-upload"></span> Guardar
                            </button>
                            <button class='btn btn-danger btn-sm' onclick='cancelarModal();'>
                                <i class='fa fa-trash'></i> Cancelar
                            </button>
                            <?php
                        } else if ($flag == 2) {
                            ?>
                            <button type="button" class="btn btn-warning btn-sm" onclick="saveUpdateRecord('<?= substr($campos1, 0, strlen($campos1) - 1); ?>', '<?= $table; ?>', '<?= $_REQUEST['field']; ?>', '<?= $_REQUEST['value']; ?>',<?= $_REQUEST['btnNew']; ?>,<?= $_REQUEST['btnUpdate']; ?>,<?= $_REQUEST['btnDelete']; ?>, '<?= $_REQUEST['modulo']; ?>', '<?= $_REQUEST['opcion']; ?>', '<?= substr($required, 0, strlen($required) - 1); ?>', '<?= substr($requiredName, 0, strlen($requiredName) - 1); ?>');">
                                <span class="fa fa-cloud-upload"></span> Actualizar
                            </button>
                            <button class='btn btn-danger btn-sm' onclick="loadData('vw_clientes', 'CXC', 'Clientes', 1, 1, 0);">
                                <i class='fa fa-trash'></i> Cancelar / Regresar
                            </button>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading tab-bg-dark-navy-blue">
                <ul class="nav nav-tabs nav-justified ">
                    <li class="active">
                        <a data-toggle="tab" href="#informacionGeneral">
                            <i class="fa fa-user"></i> Informacion General
                        </a>
                    </li>
<!--                    <li>
                        <a data-toggle="tab" href="#datosEmpleo">
                            <i class="fa fa-briefcase"></i> Datos de Empleo
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#datosConyuge">
                            <i class="fa fa-briefcase"></i> Datos de Conyuge
                        </a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#referenciasPersonales">
                            <i class="fa fa-briefcase"></i> Referencias Personales
                        </a>
                    </li>-->
                    <li id="tabsPagosCuotas">
                        <a data-toggle="tab" href="#pagosCuotas" onclick="getFactRecurrente();">
                            <i class="fa fa-briefcase"></i> Cuotas/Pagos
                        </a>
                    </li>
                    <li id="tabsDocumentosAdjuntos">
                        <a data-toggle="tab" href="#documentoAdjuntos" onclick="clientesDocumentosAdjuntos();">
                            <i class="fa fa-archive"></i> Documentos Adjuntos
                        </a>
                    </li>
                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content tasi-tab">
                    <div id="informacionGeneral" class="tab-pane active">
                        <div class="row">
                            <?= $group1; ?>
                        </div>
                    </div>
<!--                    <div id="datosEmpleo" class="tab-pane ">
                        <div class="row">
                            <?= $group2; ?>
                        </div>
                    </div>
                    <div id="datosConyuge" class="tab-pane ">
                        <div class="row">
                            <?= $group2; ?>
                        </div>
                    </div>
                    <div id="referenciasPersonales" class="tab-pane ">
                        <div class="row">
                            <?= $group2; ?>
                        </div>
                    </div>-->
                    <div id="pagosCuotas" class="tab-pane ">
                        <div class="row">
                            <div class="col-lg-2">
                                <label>Código Producto</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_productos', 'Consulta Productos', 'factRecurrente');"> 
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                    <input type="text" class="form-control input-sm facturacion" id="codigo">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label>Descripción</label>
                                <input type="hidden" id="idProducto" class="form-control input-sm"/>
                                <input type="text" class="form-control input-sm" id="descProducto" readonly="">
                            </div>
                            <div class="col-lg-2">
                                <label>Monto</label>
                                <input type="text" class="form-control input-sm" id="monto">
                            </div>
                            <div class="col-lg-2">
                                <label>Tipo Cuota</label>
                                <select id="idTipoCuota" class="form-control input-sm">
                                    <option value="">[Seleccione...]</option>
                                    <?php
                                    foreach ($tipoCuotas as $key => $value) {
                                        ?>
                                        <option value="<?= ($key + 1); ?>"><?= $value; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label>Periodo de Pago</label>
                                <select id="idPeriodoPago" class="form-control input-sm">
                                    <option value="">[Seleccione...]</option>
                                    <?php
                                    foreach ($periodos as $key => $value) {
                                        ?>
                                        <option value="<?= ($key + 1); ?>"><?= $value; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label>Fecha Inicio</label>
                                <input type="text" class="form-control input-sm date" id="fechaInicio">
                            </div>
                            <div class="col-lg-2">
                                <label>Fecha Fin</label>
                                <input type="text" class="form-control input-sm date" id="fechaFin">
                            </div>
                            <div class="col-lg-2">
                                <label>No. Cuotas</label>
                                <input type="text" class="form-control input-sm" id="noCuotas">
                            </div>
                            <div class="col-lg-12">
                                &nbsp;<br/>
                                <button class='btn btn-primary btn-sm' onclick='saveFactRecurrente();'>
                                    <i class='fa fa-cloud-upload'></i> Guardar
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            &nbsp;<br/>
                            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td class="text-center">Producto</td>
                                        <td class="text-center">Monto</td>
                                        <td class="text-center">Tipo Cuota</td>
                                        <td class="text-center">Periodo Pago</td>
                                        <td class="text-center">Fecha Inicio</td>
                                        <td class="text-center">Fecha Fin</td>
                                        <td class="text-center">No. Cuotas</td>
                                    </tr>
                                </thead>
                                <tbody id="detalleFR">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="documentoAdjuntos" class="tab-pane ">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <label>Nombre</label>
                                        <input type="text" id="nameFile" class="form-control input-sm"/>
                                    </div>
                                    <div class="col-lg-5">
                                        <label>Archivo</label>
                                        <input type="file" id="file" class="form-control input-sm"/>
                                    </div>
                                    <div class="col-lg-2">
                                        &nbsp;<br/>
                                        <button class='btn btn-primary btn-sm' onclick='subirClientesDocumentosAdjuntos();'>
                                            <i class='fa fa-cloud-upload'></i> Subir
                                        </button>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="text-center">Nombre</td>
                                            <td class="text-center">Archivo</td>
                                            <td class="text-center">Fecha y Hora</td>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>