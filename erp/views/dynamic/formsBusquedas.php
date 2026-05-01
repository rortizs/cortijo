<?php
/**
 * dynamic.php view
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
?>
<!--FORM-->
<div class="row">
    <?php
    $campos1 = '';
    $fieldEmpresa = '';
    if ($_SESSION['idEmpresa'] != '') {
        $fieldEmpresa = 'idEmpresas';
    }
    for ($a = 0; $a < count($tableStructure); $a++) {
        $commentField = explode(",", $tableStructure[$a]['COLUMN_COMMENT']);
        $requiredField = '';
        $idBox = '';
        if ($tableStructure[$a]['COLUMN_NAME'] != 'id' && $tableStructure[$a]['COLUMN_NAME'] != 'pwd' && $tableStructure[$a]['COLUMN_NAME'] != 'created_at' && $tableStructure[$a]['COLUMN_NAME'] != 'updated_at' && $tableStructure[$a]['COLUMN_NAME'] != 'image' && $tableStructure[$a]['COLUMN_NAME'] != 'codigo' && $tableStructure[$a]['COLUMN_NAME'] != $fieldEmpresa && $tableStructure[$a]['COLUMN_NAME'] != 'retensionISR' && $tableStructure[$a]['COLUMN_NAME'] != 'retensionTimbres' && $tableStructure[$a]['COLUMN_NAME'] != 'saldoFinal' && $tableStructure[$a]['COLUMN_NAME'] != 'winPreliminar' && $tableStructure[$a]['COLUMN_NAME'] != 'facturacion' && $tableStructure[$a]['COLUMN_NAME'] != 'valorDiario' && $tableStructure[$a]['COLUMN_NAME'] != 'diasMes' && $tableStructure[$a]['COLUMN_NAME'] != 'comisionVisanet' && $tableStructure[$a]['COLUMN_NAME'] != 'codigoCliente') {
            $campos1 .= $tableStructure[$a]['COLUMN_NAME'] . ",";
            if ($tableStructure[$a]['IS_NULLABLE'] == 'NO') {
                $names = explode(",", $tableStructure[$a]['COLUMN_COMMENT']);
                $required .= $tableStructure[$a]['COLUMN_NAME'] . ",";
                $requiredName .= $names[0] . ",";
                $requiredField = '<span style="color:red !important;">*</span>';
            }
            $editor = '';
            switch ($tableStructure[$a]['DATA_TYPE']) {
                case 'int':
                    $tableR = strtolower(substr($tableStructure[$a]['COLUMN_NAME'], 2, 1)) . substr($tableStructure[$a]['COLUMN_NAME'], 3, strlen($tableStructure[$a]['COLUMN_NAME']));
                    if ($tableR == 'medidas2') {
                        $tableR = str_replace('2', '', strtolower(substr($tableStructure[$a]['COLUMN_NAME'], 2, 1)) . substr($tableStructure[$a]['COLUMN_NAME'], 3, strlen($tableStructure[$a]['COLUMN_NAME'])));
                    }
                    $parametros = "";
                    if ($_SESSION['idEmpresa'] != '') {
                        if ($tableR != 'documentos' && $tableR != 'tipoCuenta' && $tableR != 'tipoCostos' && $tableR != 'paises' && $tableR != 'departamentos' && $tableR != 'municipios' && $tableR != 'tipoClientes' && $tableR != 'clasificacionClientes' && $tableR != 'listasPrecios' && $tableR != 'tipoProductos' && $tableR != 'fabricaProducto' && $tableR != 'statusProducto' && $tableR != 'roles' && $tableR != 'statusEmpleado' && $tableR != 'anos' && $tableR != 'meses' && $tableR != 'hrmStatusEmpleado' && $tableR != 'hrmOperacionPD' && $tableR != 'availableSale' && $tableR != 'pequenoContribuyente' && $tableR != 'tipoCuentaContable' && $tableR != 'tipoOperacionContable') {
                            $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'];
                        }
                        if ($tableR == 'nomenclatura') {
                            $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'] . ' and idTipoOperacionContable=2';
                        }
                        if ($tableR == 'centrosCosto') {
                            $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'] . ' and idTipoOperacionContable=2';
                        }
                    }
                    $dataTable = $dynamic->dataTable($dbS, $tableR, $parametros);
                    $selectTable = $dynamic->tableStructure($dbC, $dbS, $tableR);
                    $editor = "<select class='form-control input-sm' id='" . $tableStructure[$a]['COLUMN_NAME'] . "'>";
                    $editor .= "<option value='0'>[seleccione...]</option>";
                    for ($b = 0; $b < count($dataTable); $b++) {
                        switch ($tableR) {
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
                    $val = ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == '') ? $dynamic->date : $getRecord[$tableStructure[$a]['COLUMN_NAME']];
                    $editor = "<input class='form-control input-sm date' type='text' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='30' value='" . $val . "'/>";
                    break;
                case 'time':
                    $editor = "<input class='form-control input-sm' type='text' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='30' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                    break;
                case 'double':
                    $editor = "<input class='form-control input-sm' type='text' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='60' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                    break;
            }
            ?>
            <div class="col-xs-4" id="box-<?= $idBox; ?>">
                <?= $commentField[0]; ?>&nbsp; <?= $requiredField; ?>
                <?= $editor; ?>
            </div>
            <?php
        }
    }
    ?>
</div>
<div class="clear">&nbsp;</div>
<div col-xs-12>
    <button class="btn btn-primary btn-sm" onclick="saveRecordBusqueda('<?= substr($campos1, 0, strlen($campos1) - 1); ?>', '<?= $table; ?>', '<?= $_REQUEST['title']; ?>', '<?= substr($required, 0, strlen($required) - 1); ?>', '<?= substr($requiredName, 0, strlen($requiredName) - 1); ?>');">
        <span class="fa fa-floppy-o"></span> Guardar
    </button>
    <button class="btn btn-danger btn-sm" onclick='cancelarModal();'>
        <span class="fa fa-times"></span> Cancelar
    </button>
</div>
<!--END FORM-->
