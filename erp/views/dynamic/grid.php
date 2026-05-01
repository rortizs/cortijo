<?php

session_start();
require_once("../../models/config.php");
require_once("../../models/dynamic.php");
require_once("../../models/ssp.class.php");
$dynamic = new Dynamic();
$dbC = Config::$dbD;
$dbS = $_SESSION['dbProject'];
$table = $_REQUEST['table'];
$primaryKey = "";
$columns = "";
$columns = array();
$where = "";
$sql_details = array(
    'host' => Config::$host,
    'db' => $_SESSION['dbProject'],
    'user' => Config::$userDB,
    'pass' => Config::$pwdDB
);
$tableStructure = $dynamic->tableStructure($dbC, $dbS, $table);
//
if ($table == 'vw_empresas') {
    $where = 'id=' . $_SESSION['idEmpresa'];
} else if ($table == 'modulos' || $table == 'paginas' || $table == 'permisos' || $table == 'vw_roles' || $table == 'vw_hrmTipoEvento') {
    $where = "";
} else {
    $where = 'idEmpresas="' . $_SESSION['nombreEmpresa'] . '"';
}
//
// echo "select * from ".$table." where ".$where;
// exit();
switch ($_REQUEST['table']) {
    case 'inventarioBodegas':
        $primaryKey = 'bodega';
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'bodega', 'dt' => 1),
            array('db' => 'sku', 'dt' => 2),
            array('db' => 'descLarga', 'dt' => 3),
            array('db' => 'ingreso', 'dt' => 4),
            array('db' => 'salida', 'dt' => 5),
            array('db' => 'saldo', 'dt' => 6),
            array('db' => 'unidadMedida', 'dt' => 7),
            array('db' => 'fechaIngreso', 'dt' => 8),
        );
        break;
    case 'inventarioSucursales':
        $primaryKey = 'sucursal';
        $columns = array(
            array('db' => 'sucursal', 'dt' => 0),
            array('db' => 'sku', 'dt' => 1),
            array('db' => 'descLarga', 'dt' => 2),
            array('db' => 'idTipoProductos', 'dt' => 3),
            array('db' => 'ingreso', 'dt' => 4),
            array('db' => 'salida', 'dt' => 5),
            array('db' => 'saldo', 'dt' => 6),
            array('db' => 'unidadMedida', 'dt' => 7),
            array('db' => 'fechaIngreso', 'dt' => 8)
        );
        break;
    default :
        $primaryKey = 'id';
        for ($a = 0; $a < count($tableStructure); $a++) {
            switch ($tableStructure[$a]['COLUMN_NAME']) {
                case 'id':
                    $columnDetails = array(
                        'db' => 'id',
                        'dt' => 0,
                        'formatter' => function( $d, $row ) {
                            $status = "";
                            $action = "";
                            $type = "";
                            if ($_REQUEST['table'] == 'vw_comprasRequisicion') {
                                switch ($row[5]) {
                                    case 'Pendiente':
                                        $action = '1';
                                        break;
                                    case 'Aprobada':
                                        $action = '2';
                                        break;
                                    case 'Rechazada':
                                        $action = '3';
                                        break;
                                    case 'Importada':
                                        $action = '4';
                                        break;
                                }
                            }
                            if ($_REQUEST['table'] == 'vw_comprasOrdenes') {
                                switch ($row[12]) {
                                    case 'Pendiente':
                                        $action = '1';
                                        break;
                                    case 'Aprobada':
                                        $action = '2';
                                        break;
                                    case 'Rechazada':
                                        $action = '3';
                                        break;
                                    case 'Importada':
                                        $action = '4';
                                        break;
                                }
                                $type = $row[2];
                            }
                            if ($_REQUEST['table'] == 'vw_cajaChica') {
                                switch ($row[10]) {
                                    case 'ABIERTA':
                                        $action = '1';
                                        break;
                                    case 'CERRADA':
                                        $action = '2';
                                        break;
                                    case 'CHEQUE ASIGNADO':
                                        $action = '3';
                                        break;
                                }
                            }
                            return '<input type="checkbox" class="data" value="' . $d . '" ' . $status . ' data-value="' . $action . '" data-type="' . $type . '"/>';
                        }
                    );
                    break;
                case 'pwd_real':
                    $columnDetails = array(
                        'db' => $tableStructure[$a]['COLUMN_NAME'],
                        'dt' => $a,
                        'formatter' => function( $d, $row ) {
                            return '******';
                        }
                    );
                    break;
                case 'image':
                    switch ($table) {
                        case 'vw_clientes':
                            $columnDetails = array(
                                'db' => $tableStructure[$a]['COLUMN_NAME'],
                                'dt' => $a,
                                'formatter' => function( $d, $row ) {
                                    if (!$d) {
                                        return '<img src="assets/images/user.png" class="img-circle" style="width:50px !important;"/>';
                                    } else {
                                        return '<img src="assets/images/clientes/' . $d . '" class="img-circle" style="width:50px !important;"/>';
                                    }
                                }
                            );
                            break;
                        case 'vw_hrmEmpleados':
                            $columnDetails = array(
                                'db' => $tableStructure[$a]['COLUMN_NAME'],
                                'dt' => $a,
                                'formatter' => function( $d, $row ) {
                                    if (!$d) {
                                        return '<img src="assets/images/user.png" class="img-circle" style="width:50px !important;"/>';
                                    } else {
                                        return '<img src="assets/images/empleados/' . $d . '" class="img-circle" style="width:50px !important;"/>';
                                    }
                                }
                            );
                            break;
                        case 'vw_productos':
                            $columnDetails = array(
                                'db' => $tableStructure[$a]['COLUMN_NAME'],
                                'dt' => $a,
                                'formatter' => function( $d, $row ) {
                                    if (!$d) {
                                        return '<img src="assets/images/producto.png" class="img-circle" style="width:50px !important;"/>';
                                    } else {
                                        return '<img src="assets/images/productos/' . $d . '" class="img-circle" style="width:50px !important;"/>';
                                    }
                                }
                            );
                            break;
                    }
                    break;
                default:
                    $columnDetails = array('db' => $tableStructure[$a]['COLUMN_NAME'], 'dt' => $a);
                    break;
            }
            array_push($columns, $columnDetails);
        }
        break;
}
//
echo json_encode(
        SSP::complex($_POST, $sql_details, $table, $primaryKey, $columns, null, $where)
);


