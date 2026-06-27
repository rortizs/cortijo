<?php
/**
 * dynamic.php view
 * @author Jonathan Juarez
 * @version 1.0 20140122
 */
session_start();
require_once("../../models/config.php");
require_once("../../models/dynamic.php");
$dynamic = new Dynamic();
$dbC = Config::$dbD;
$dbS = $_SESSION['dbProject'];
$table = "";
if ($_REQUEST['table'] == 'marcasView') {
    $table = "marcas";
} else {
    $table = $_REQUEST['table'];
}
$field = $_REQUEST['field'];
$value = $_REQUEST['value'];
$flag = $_REQUEST['flag'];
$tableStructure = $dynamic->tableStructure($dbC, $dbS, $table);
$getRecord = $dynamic->getRecord($dbS, $table, $field, $value);
?>
<!--FORM-->
<table>
    <?php
    $campos1 = '';
    $fieldEmpresa = '';
    if ($_SESSION['idEmpresa'] != '') {
        $fieldEmpresa = 'idEmpresas';
    }
    for ($a = 0; $a < count($tableStructure); $a++) {
        if ($tableStructure[$a]['COLUMN_NAME'] != 'id' && $tableStructure[$a]['COLUMN_NAME'] != 'created_at' && $tableStructure[$a]['COLUMN_NAME'] != 'updated_at' && $tableStructure[$a]['COLUMN_NAME'] != 'img' && $tableStructure[$a]['COLUMN_NAME'] != 'codigo' && $tableStructure[$a]['COLUMN_NAME'] != $fieldEmpresa) {
            $campos1 .= $tableStructure[$a]['COLUMN_NAME'] . ",";
            $editor = '';
            switch ($tableStructure[$a]['DATA_TYPE']) {
                case 'int':
                    $tableR = strtolower(substr($tableStructure[$a]['COLUMN_NAME'], 2, 1)) . substr($tableStructure[$a]['COLUMN_NAME'], 3, strlen($tableStructure[$a]['COLUMN_NAME']));
                    $parametros = "";
                    if ($_SESSION['idEmpresa'] != '') {
                        if ($tableR != 'tipoCostos' && $tableR != 'paises' && $tableR != 'departamentos' && $tableR != 'municipios' && $tableR != 'tipoClientes' && $tableR != 'clasificacionClientes' && $tableR != 'listasPrecios' && $tableR != 'tipoProductos' && $tableR != 'fabricaProducto' && $tableR != 'statusProducto' && $tableR != 'roles' && $tableR != 'statusEmpleado') {
                            $parametros = 'where idEmpresas=' . $_SESSION['idEmpresa'];
                        }
                    }
                    $dataTable = $dynamic->dataTable($dbS, $tableR, $parametros);
                    $selectTable = $dynamic->tableStructure($dbC, $dbS, $tableR);
                    $editor = "<select class='form-control' id='" . $tableStructure[$a]['COLUMN_NAME'] . "'>";
                    $editor.="<option value='0'>[seleccione...]</option>";
                    for ($b = 0; $b < count($dataTable); $b++) {
                        if ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == $dataTable[$b][$selectTable[0]['COLUMN_NAME']]) {
                            $editor.="<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . " selected=''>" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . "</option>";
                        } else {
                            $editor.="<option value=" . $dataTable[$b][$selectTable[0]['COLUMN_NAME']] . ">" . $dataTable[$b][$selectTable[1]['COLUMN_NAME']] . "</option>";
                        }
                    }
                    $editor .="</select>";
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
                    }
                    if ($tableStructure[$a]['COLUMN_NAME'] == 'pwd') {
                        $editor = "<input class='form-control " . $transformText . "' type='password' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='30' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                    } else {
                        $editor = "<input class='form-control " . $transformText . "' type='text' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='60' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                    }
                    break;
                case 'text':
                    $editor = "<textarea class='form-control' id='" . $tableStructure[$a]['COLUMN_NAME'] . "'>" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "</textarea>";
                    break;
                case 'date':
                    $val = ($getRecord[$tableStructure[$a]['COLUMN_NAME']] == '') ? $dynamic->date : $getRecord[$tableStructure[$a]['COLUMN_NAME']];
                    $editor = "<input class='form-control' type='text' class='date' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='30' value='" . $val . "'/>";
                    break;
                case 'time':
                    $editor = "<input class='form-control' type='text' class='time' id='" . $tableStructure[$a]['COLUMN_NAME'] . "' size='30' value='" . $getRecord[$tableStructure[$a]['COLUMN_NAME']] . "'/>";
                    break;
            }
            ?>
            <tr>
                <td><?= $tableStructure[$a]['COLUMN_COMMENT']; ?>&nbsp;</td>
                <td><?= $editor; ?></td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
        <td colspan="2">
            <button type="button" class="btn btn-primary" id="cerrar1" onclick="saveRecordBusqueda('<?= substr($campos1, 0, strlen($campos1) - 1); ?>', '<?= $table; ?>','<?= $_REQUEST['title']; ?>');">
                <span class="glyphicon glyphicon-floppy-disk"></span> Guardar
            </button>
        </td>
    </tr>
</table>
<!--END FORM-->
