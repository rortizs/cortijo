<?php

session_start();
require_once("../../models/config.php");
require( '../../models/ssp.class.php' );
$table = $_REQUEST['table'];
$columns = "";
$where = "";
$primaryKey = 'id';
$sql_details = array(
    'host' => Config::$host,
    'db' => $_SESSION['dbProject'],
    'user' => Config::$userDB,
    'pass' => Config::$pwdDB
);

if ($table == 'inventarioBodegas' || $table == 'vw_inventarioSucursales') {
    $primaryKey = 'sku';
}

switch ($_REQUEST['table']) {
    case 'proveedores':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    return '<input type="checkbox" onchange="loadProveedor(' . $d . ');"/>';
                }
            ),
            array('db' => 'descripcion', 'dt' => 1),
            array('db' => 'nitP', 'dt' => 2),
        );
        $where = "idEmpresas = " . $_SESSION['idEmpresa'] . "";
        break;
    case 'productos':
        $columns = array(
            array(
                'db' => 'idProductos',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $idProducto = "'" . $row[0] . "'";
                    $codigo = "'" . $row[1] . "'";
                    $desc = "'" . $row[2] . "'";
                    $existencia = "'" . $row[3] . "'";
                    $precioPublico = "'" . $row[5] . "'";
                    $tipoProducto = "'" . $row[7] . "'";
                    $modulo = "'" . $_REQUEST['modulo'] . "'";
                    return '<input type="checkbox" class="form-control input-sm" onchange="loadProducto(' . $idProducto . ',' . $codigo . ',' . $desc . ',' . $existencia . ',' . $precioPublico . ',' . $tipoProducto . ',' . $modulo . ');"/>';
                }
            ),
            array('db' => 'sku', 'dt' => 1),
            array('db' => 'descLarga', 'dt' => 2),
            array('db' => 'saldo', 'dt' => 3),
            array('db' => 'precioPublico', 'dt' => 4),
            array('db' => 'idTipoProductos', 'dt' => 5)
        );
        $ingresoA = $_REQUEST['ingresoA'] ?: 2;
        $idPuntoIngreso = $_REQUEST['idPuntoIngreso'] ?: $_SESSION['idSucursalesS'];
        if ($_REQUEST['modulo'] === 'Facturacion') {
            $where = "ingresoA = '" . $ingresoA . "' and idPuntoIngreso=" . $idPuntoIngreso . " and idTipoProductos!='Materia Prima' and idAvailableSale!='No'";
        } else {
            $where = "ingresoA = '" . $ingresoA . "' and idPuntoIngreso=" . $idPuntoIngreso . "";
        }
        break;
    case 'inventario':
        $columns = array(
            array(
                'db' => 'idProductos',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $idProducto = "'" . $row[0] . "'";
                    $codigo = "'" . $row[2] . "'";
                    $desc = "'" . $row[4] . "'";
                    $existencia = "'" . $row[5] . "'";
                    $precioPublico = "'" . $row[6] . "'";
                    if($row[6]!==$row[7]){
                        $precioPublico = "'" . $row[6] . "'";
                    }
                    $tipoProducto = "'" . $row[8] . "'";
                    $modulo = "'" . $_REQUEST['modulo'] . "'";
                    $utilizaSerie = "'" . $row[9] . "'";
                    return '<input type="checkbox" class="form-control input-sm" onchange="loadProducto(' . $idProducto . ',' . $codigo . ',' . $desc . ',' . $existencia . ',' . $precioPublico . ',' . $tipoProducto . ',' . $modulo . ',' . $utilizaSerie . ');"/>';
                }
            ),
            $columnDetails = array(
        'db' => 'image',
        'dt' => 1,
        'formatter' => function($d, $row) {
            if (!$d) {
                return '<img src="assets/images/producto.png" class="img-fluidcircle" alt="Responsive image" style="width:200px !important;"/>';
            } else {
                return '<img src="assets/images/productos/' . $d . '"class="img-fluidcircle" alt="Responsive image" style="width:200px !important;"/>';
            }
        }
            ),
            array('db' => 'sku', 'dt' => 2),
            array('db' => 'upc', 'dt' => 3),
            array('db' => 'descLarga', 'dt' => 4),
            array('db' => 'saldo', 'dt' => 5),
            array('db' => 'precioPublico', 'dt' => 6),
            array('db' => 'precioOferta', 'dt' => 7),
            array('db' => 'idTipoProductos', 'dt' => 8),
            array('db' => 'idUtilizaSerie', 'dt' => 9),
            array('db' => 'idGrupos', 'dt' => 10)
        );
        //    
        $ingresoA = $_REQUEST['ingresoA'];
        $idPuntoIngreso = $_REQUEST['idPuntoIngreso'];
        if ($_REQUEST['modulo'] === 'Facturacion') {
            $where = "ingresoA = '" . $ingresoA . "' and idPuntoIngreso=" . $idPuntoIngreso . " and idTipoProductos!='Materia Prima'";
        } else {
            $where = "ingresoA = '" . $ingresoA . "' and idPuntoIngreso=" . $idPuntoIngreso . "";
        }
        break;
    case 'vw_clientes':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $codCliente = "'" . $row[0] . "'";
                    $nit = "'" . $row[1] . "'";
                    return '<input type="checkbox" onchange="loadClientesNit(' . $nit . ',' . $codCliente . ');"/>';
                }
            ),
            array('db' => 'codigoC', 'dt' => 1),        
            array('db' => 'nitC', 'dt' => 2),
            array('db' => 'nombreC', 'dt' => 3),
            array('db' => 'nombreF', 'dt' => 4),
            array('db' => 'idCentrosCosto', 'dt' => 5)
        );
        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "'";
        break;
    case 'vw_productosMedidas':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    return '<input type="checkbox" onchange="loadProductoComponente(' . $d . ');"/>';
                    //return '<a onclick="loadProductoComponente(' . $d . ')">Seleccione</a>';
                }
            ),
            array('db' => 'sku', 'dt' => 1),
            array('db' => 'descLarga', 'dt' => 2),
            array('db' => 'precioCosto', 'dt' => 3)
        );
        $where = "idEmpresas = " . $_SESSION['idEmpresa'] . "";
        break;
    case 'vw_productos':
        if ($_SESSION['dbProject'] === 'pos_homeoutlet') {
            $column = "precioPublico";
            if ($_REQUEST['modulo'] == 'compras' || $_REQUEST['modulo'] == 'importaciones') {
                $column = "precioCosto";
            }
            $columns = array(
                array(
                    'db' => 'id',
                    'dt' => 0,
                    'formatter' => function($d, $row) {
                        $idProducto = "'" . $row[0] . "'";
                        $codigo = "'" . $row[1] . "'";
                        $desc = "'" . $row[2] . "'";
                        $existencia = "''";
                        $precioPublico = "'" . $row[5] . "'";
                        $tipoProducto = "'" . $row[7] . "'";
                        $utilizaSerie = "'" . $row[8] . "'";
                        $modulo = "'" . $_REQUEST['modulo'] . "'";
                        return '<input type="checkbox" onchange="loadProducto(' . $idProducto . ',' . $codigo . ',' . $desc . ',' . $existencia . ',' . $precioPublico . ',' . $tipoProducto . ',' . $modulo . ',' . $utilizaSerie . ');"/>';
                    }
                ),
                array('db' => 'sku', 'dt' => 1),
                array('db' => 'upc', 'dt' => 2),
                array('db' => 'descLarga', 'dt' => 3),
                array('db' => $column, 'dt' => 4),
                array('db' => 'idTipoProductos', 'dt' => 5),
                array('db' => 'idUtilizaSerie', 'dt' => 6)
            );
            $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "'";
        } else {
            $column = "precioPublico";
            if ($_REQUEST['modulo'] == 'compras' || $_REQUEST['modulo'] == 'importaciones') {
                $column = "precioCosto";
            }
            $columns = array(
                array(
                    'db' => 'id',
                    'dt' => 0,
                    'formatter' => function($d, $row) {
                        $idProducto = "'" . $row[0] . "'";
                        $codigo = "'" . $row[1] . "'";
                        $desc = "'" . $row[2] . "'";
                        $existencia = "''";
                        $precioPublico = "'" . $row[3] . "'";
                        $tipoProducto = "'" . $row[4] . "'";
                        $utilizaSerie = "'" . $row[5] . "'";
                        $modulo = "'" . $_REQUEST['modulo'] . "'";
                        return '<input type="checkbox" onchange="loadProducto(' . $idProducto . ',' . $codigo . ',' . $desc . ',' . $existencia . ',' . $precioPublico . ',' . $tipoProducto . ',' . $modulo . ',' . $utilizaSerie . ');"/>';
                    }
                ),
                array('db' => 'sku', 'dt' => 1),
                array('db' => 'descLarga', 'dt' => 2),
                array('db' => $column, 'dt' => 3),
                array('db' => 'idTipoProductos', 'dt' => 4),
                array('db' => 'idUtilizaSerie', 'dt' => 5)
            );
            $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "'";
        }
        break;
    case 'vw_nomenclatura':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $id = "'" . $row[0] . "'";
                    $cuenta = "'" . $row[1] . "'";
                    $descripcion = "'" . $row[3] . "'";
                    $item = "'" . $_REQUEST['item'] . "'";
                    return '<input type="checkbox" onchange="getCuentaContable(' . $id . ',' . $cuenta . ',' . $descripcion . ',' . $item . ');"/>';
                }
            ),
            array('db' => 'cuenta', 'dt' => 1),
            array('db' => 'nivel', 'dt' => 2),
            array('db' => 'descripcion', 'dt' => 3),
            array('db' => 'padre', 'dt' => 4),
            array('db' => 'tipoCuenta', 'dt' => 5)
        );
        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND idTipoOperacionContable='JORNALIZACIÓN'";
        break;
    case 'vw_centrosCosto':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $id = "'" . $row[0] . "'";
                    $cuenta = "'" . $row[1] . "'";
                    $descripcion = "'" . $row[2] . "'";
                    $item = "'" . $_REQUEST['item'] . "'";
                    return '<input type="checkbox" onchange="getCentrosCosto(' . $id . ',' . $cuenta . ',' . $descripcion . ',' . $item . ');"/>';
                }
            ),
            array('db' => 'cuenta', 'dt' => 1),
            array('db' => 'descripcion', 'dt' => 2)
        );
        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND idTipoOperacionContable='JORNALIZACIÓN'";
        break;
    case 'vw_formatos':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $id = "'" . $row[0] . "'";
                    $descripcion = "'" . $row[1] . "'";
                    return '<input type="checkbox" onchange="getFormato(' . $id . ',' . $descripcion . ');"/>';
                }
            ),
            array('db' => 'descripcion', 'dt' => 1)
        );
        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "'";
        break;
    case 'vw_cuentasBancarias':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $id = "'" . $row[0] . "'";
                    $numeroCuenta = "'" . $row[1] . "'";
                    $nombreCuenta = "'" . $row[2] . "'";
                    $banco = "'" . $row[3] . "'";
                    $saldoLibros = "'" . $row[4] . "'";
                    $saldoBanco = "'" . $row[5] . "'";
                    return '<input type="checkbox" onchange="getCuentaBancaria(' . $id . ',' . $numeroCuenta . ',' . $nombreCuenta . ',' . $banco . ',' . $saldoLibros . ',' . $saldoBanco . ');"/>';
                }
            ),
            array('db' => 'numeroCuenta', 'dt' => 1),
            array('db' => 'nombreCuenta', 'dt' => 2),
            array('db' => 'idBancos', 'dt' => 3),
            array('db' => 'saldoLibros', 'dt' => 4),
            array('db' => 'saldoBanco', 'dt' => 5),
        );
        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "'";
        break;
    case 'vw_pedidos':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $noPedido = "'" . $row[2] . "'";
                    $action = "'get'";
                    return '<input type="checkbox" onchange="getPedido(' . $d . ',' . $action . ');"/>';
                }
            ),
            array('db' => 'fecha', 'dt' => 1),
            array('db' => 'documento', 'dt' => 2),
            array('db' => 'nit', 'dt' => 3),
            array('db' => 'nombre', 'dt' => 4),
            array('db' => 'vendedor', 'dt' => 5),
            array('db' => 'total', 'dt' => 6)
        );
        switch ($_SESSION['dbProject']) {
            case 'erp_gsp':
                switch ($_SESSION['idEmpresa']) {
                    case '6':
                        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND estado='Confirmado'";
                        break;
                    default:
                        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND estado='Abierto'";
                        break;
                }
                break;
            default:
                $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND estado='Abierto'";
                break;
        }
        break;
    case 'vw_pedidosMedidas':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $noPedido = "'" . $row[2] . "'";
                    $action = "'get'";
                    return '<input type="checkbox" onchange="getPedidoMedida(' . $noPedido . ',' . $action . ');"/>';
                }
            ),
            array('db' => 'fecha', 'dt' => 1),
            array('db' => 'documento', 'dt' => 2),
            array('db' => 'nit', 'dt' => 3),
            array('db' => 'nombre', 'dt' => 4),
            array('db' => 'vendedor', 'dt' => 5)
        );
        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND estado='Abierto'";
        break;
    case 'vw_agenciasViajes':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $noReserva = "'" . $row[5] . "'";
                    return '<input type="checkbox" id=' . $noReserva . ' onchange="getBoleto(' . $noReserva . ');"/>';
                }
            ),
            array('db' => 'fecha', 'dt' => 1),
            array('db' => 'boleto', 'dt' => 2),
            array('db' => 'codCliente', 'dt' => 3),
            array('db' => 'codVendedor', 'dt' => 4),
            array('db' => 'reserva', 'dt' => 5),
            array('db' => 'lineaArea', 'dt' => 6)
        );
        $where = "idEmpresas = '" . $_SESSION['idEmpresas'] . "'   GROUP BY reserva";
        break;
    case 'vw_cotizaciones':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $noCotizacion = "'" . $row[2] . "'";
                    $nombreCotizacion = "'" . $row[4] . "'";
                    $action = "'get'";
                    return '<input type="checkbox" class="cotizaciones" onchange="getCotizacion(' . $noCotizacion . ',' . $action . ',' . $nombreCotizacion . ');"/>';
                }
            ),
            array('db' => 'fecha', 'dt' => 1),
            array('db' => 'documento', 'dt' => 2),
            array('db' => 'nit', 'dt' => 3),
            array('db' => 'nombre', 'dt' => 4),
            array('db' => 'vendedor', 'dt' => 5)
        );
        //$idEmpresas = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' and estado='Abierto'";
        break;
    case 'usuarios':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $id = "'" . $row[0] . "'";
                    return '<input type="checkbox"  onchange="getVendedoresReporte(' . $id . ');"/>';
                }
            ),
            array('db' => 'userName', 'dt' => 1),
        );
        $where = "idEmpresas = '" . $_SESSION['idEmpresas'] . "' and idRoles=12";
        break;
    case 'vw_comprasOrdenes':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $proveedor = "'" . $row[2] . "'";
                    return '<input type="checkbox" class="ordenesCompra" onchange="getOrdenCompra(' . $d . ',' . $proveedor . ');"/>';
                }
            ),
            array('db' => 'documento', 'dt' => 1),
            array('db' => 'idProveedores', 'dt' => 2),
            array('db' => 'solicitadoPor', 'dt' => 3),
            array('db' => 'idHrmDepartamentos', 'dt' => 4),
            array('db' => 'observaciones', 'dt' => 5)
        );
        $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND status='Aprobada'";
        break;
    case 'vw_cajaChica':
        $columns = array(
            array(
                'db' => 'id',
                'dt' => 0,
                'formatter' => function($d, $row) {
                    $documento = "'" . $row[1] . "'";
                    $tipoLiquidacion = "'" . $row[2] . "'";
                    $descripcion = "'" . $row[3] . "'";
                    $responsable = "'" . $row[4] . "'";
                    $monto = 0;
                    $modulo = "'" . $_REQUEST['modulo'] . "'";
                    switch ($_REQUEST['modulo']) {
                        case 'cajaChicaCompras':
                            $monto = "'" . $row[5] . "'";
                            break;
                        case 'cajaChicaReintegros':
                            $monto = "'" . $row[6] . "'";
                            break;
                        default:
                            $monto = "'" . $row[5] . "'";
                            break;
                    }
                    return '<input type="checkbox" onchange="getCajaChica(' . $d . ',' . $documento . ',' . $monto . ',' . $responsable . ',' . $descripcion . ',' . $tipoLiquidacion . ',' . $modulo . ');"/>';
                }
            ),
            array('db' => 'documento', 'dt' => 1),
            array('db' => 'idTipoLiquidaciones', 'dt' => 2),
            array('db' => 'descripcion', 'dt' => 3),
            array('db' => 'responsable', 'dt' => 4),
            array('db' => 'monto', 'dt' => 5),
            array('db' => 'montoLiquidado', 'dt' => 6),
            array('db' => 'montoSinLiquidar', 'dt' => 7)
        );
        $where = "";
        switch ($_REQUEST['modulo']) {
            case 'cajaChicaCompras':
                $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND idStatusCajaChica='CHEQUE ASIGNADO'";
                break;
            case 'cajaChicaReintegros':
                $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND idStatusCajaChica='CERRADA' AND montoSinLiquidar!=0";
                break;
            default:
                $where = "idEmpresas = '" . $_SESSION['nombreEmpresa'] . "' AND idStatusCajaChica='ABIERTA'";
                break;
        }
        break;
}
//
echo json_encode(
        SSP::complex($_POST, $sql_details, $table, $primaryKey, $columns, null, $where)
);


