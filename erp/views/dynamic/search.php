<?php
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../../models/config.php");
require_once("../../models/dynamic.php");
$dynamic = new Dynamic();
$dbC = Config::$dbD;
$dbS = $_SESSION['dbProject'];
$table = $_REQUEST['table'];
$tableStructure = $dynamic->tableStructure($dbC, $dbS, $table);
//
// 1. SAVEDATA
if ($_REQUEST['flag'] == 1) {
    $campos = array();
    $campos[] = 'idEmpresas';
    $valores = array();
    $valores[] = $_SESSION['idEmpresa'];
    for ($a = 0; $a < count($tableStructure); $a++) {
        if ($tableStructure[$a]['COLUMN_NAME'] != 'id') {
            $campos[] = $tableStructure[$a]['COLUMN_NAME'];
            switch ($tableStructure[$a]['COLUMN_NAME']) {
                case 'pwd':
                    //$valores[] = md5($_REQUEST[$tableStructure[$a]['COLUMN_NAME']]);
                    $valores[] = md5($_REQUEST['pwd_real']);
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
                default:
                    $valores[] = $_REQUEST[$tableStructure[$a]['COLUMN_NAME']];
                    break;
            }
        }
    }
    $saveData = $dynamic->saveData($dbS, $table, $campos, $valores);
    $message = $saveData;
}
//
$columns = "";
switch ($table) {
    case 'proveedores':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Nombre</td>";
        $columns .= "<td>NIT</td>";
        break;
    case 'productos':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Código</td>";
        $columns .= "<td>Descripción</td>";
        $columns .= "<td>Costo Actual</td>";
        $columns .= "<td>Cantidad</td>";
        $columns .= "<td>Precio Unitario</td>";
        $columns .= "<td>Total</td>";
        break;
    case 'inventarioBodegas':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Código</td>";
        $columns .= "<td>Descripción</td>";
        $columns .= "<td>Existencias</td>";
        $columns .= "<td>Cantidad</td>";
        break;
    case 'inventario':
            $columns .= "<td>Item</td>";
            $columns .= "<td>Imagen</td>";
            $columns .= "<td>Código</td>";
            $columns .= "<td>UPC</td>";
            $columns .= "<td>Descripción</td>";
            $columns .= "<td>Existencias</td>";
            $columns .= "<td>Precio Publico</td>";
            $columns .= "<td>Precio Oferta</td>";
            $columns .= "<td>Tipo de Producto</td>";
            $columns .= "<td>Utiliza Serie</td>";
            $columns .= "<td>Familia</td>";
            
        break;
    case 'vw_clientes':
        $columns .= "<td>Item</td>";
        $columns .= "<td>CODIGO</td>";
        $columns .= "<td>NIT</td>";
        $columns .= "<td>Nombre</td>";
        $columns .= "<td>Nombre Facturación</td>";
        $columns .= "<td>Centro de Costo</td>";
        break;
    case 'vw_productosMedidas':
        $columns .= "<td>&nbsp;</td>";
        $columns .= "<td>Codigo</td>";
        $columns .= "<td>Descripcion</td>";
        $columns .= "<td>Costo</td>";
        break;
    case 'vw_productos':
        if ($_SESSION['dbProject'] === 'pos_homeoutlet') {
            $columns .= "<td>Item</td>";
            $columns .= "<td>SKU</td>";
            $columns .= "<td>UPC</td>";
            $columns .= "<td>Descripción</td>";
            $columns .= "<td>Precio Publico</td>";
            $columns .= "<td>Tipo de Producto</td>";
            $columns .= "<td>Utiliza Serie</td>";
            
        } else {
            $columns .= "<td>Item</td>";
            $columns .= "<td>Código</td>";
            $columns .= "<td>Descripción</td>";
            $columns .= "<td>Precio Publico</td>";
            $columns .= "<td>Tipo de Producto</td>";
            $columns .= "<td>Utiliza Serie</td>";
        }
        break;
    case 'vw_nomenclatura':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Cuenta</td>";
        $columns .= "<td>Nivel</td>";
        $columns .= "<td>Descripcion</td>";
        $columns .= "<td>Padre</td>";
        $columns .= "<td>Tipo Cuenta</td>";
        break;
    case 'vw_centrosCosto':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Cuenta</td>";
        $columns .= "<td>Descripcion</td>";
        break;
    case 'vw_formatos':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Descripcion</td>";
        break;
    case 'vw_cuentasBancarias':
        $columns .= "<td>Item</td>";
        $columns .= "<td>No. Cuenta</td>";
        $columns .= "<td>Nombre Cuenta</td>";
        $columns .= "<td>Banco</td>";
        $columns .= "<td>Saldo Libros</td>";
        $columns .= "<td>Saldo Banco</td>";
        break;
    case 'vw_pedidos':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Fecha</td>";
        $columns .= "<td>Documento</td>";
        $columns .= "<td>Nit</td>";
        $columns .= "<td>Nombre</td>";
        $columns .= "<td>Vendedor</td>";
        $columns .= "<td>Monto</td>";
        break;
    case 'vw_pedidosMedidas':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Fecha</td>";
        $columns .= "<td>Documento</td>";
        $columns .= "<td>Nit</td>";
        $columns .= "<td>Nombre</td>";
        $columns .= "<td>Vendedor</td>";
        break;
    case 'vw_agenciasViajes':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Fecha</td>";
        $columns .= "<td>Boleto</td>";
        $columns .= "<td>Cliente</td>";
        $columns .= "<td>Vendedor</td>";
        $columns .= "<td>Reserva</td>";
        $columns .= "<td>Linea Aerea</td>";
        break;
    case 'vw_cotizaciones':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Fecha</td>";
        $columns .= "<td>Documento</td>";
        $columns .= "<td>Nit</td>";
        $columns .= "<td>Nombre</td>";
        $columns .= "<td>Vendedor</td>";
        break;
    case 'usuarios':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Nombre</td>";
        break;
    case 'vw_comprasOrdenes':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Documento</td>";
        $columns .= "<td>Proveedor</td>";
        $columns .= "<td>Solicitado Por</td>";
        $columns .= "<td>Departamento</td>";
        $columns .= "<td>Observaciones</td>";
        break;
    case 'vw_cajaChica':
        $columns .= "<td>Item</td>";
        $columns .= "<td>Tipo</td>";
        $columns .= "<td>Documento</td>";
        $columns .= "<td>Descripcion</td>";
        $columns .= "<td>Responsable</td>";
        $columns .= "<td>Monto</td>";
        $columns .= "<td>Monto Liquidado</td>";
        $columns .= "<td>Monto Sin Liquidar</td>";
        break;
}
?>
<?php
if ($table == 'clientes') {
    ?>
    <button type="button" class="btn btn-primary btn-sm" onclick="AddRecordBusqueda('<?= $table; ?>', 'Clientes');">
        <span class="fa fa-plus"></span> Nuevo
    </button>
    <?php
}
?>
<?php
if ($table == 'proveedores') {
    ?>
    <button type="button" class="btn btn-primary btn-sm" onclick="AddRecordBusqueda('<?= $table; ?>', 'Proveedores');">
        <span class="fa fa-plus"></span> Nuevo
    </button>
    <?php
}
?>

<?php
if ($table != 'inventarioBodegas' && $table != 'vw_productosMedidas' && $table != 'productos' && $table != 'vw_agenciasViajes') {
    ?>
    <button type="button" class="btn btn-danger btn-sm" onclick="cancelarModal()">
        <span class="fa fa-times"></span> Cerrar
    </button>
    <?php
} else if ($table != 'vw_agenciasViajes') {
    ?>
    <button type="button" class="btn btn-primary btn-sm" onclick="cancelarModal()" style="right">
        <span class="fa fa-arrow-right"></span> Continuar
    </button>
    <?php
}
?>
<table id="busquedas" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
    <thead>
        <tr class="info text-uppercase" style="font-weight: bold;">
            <?= $columns; ?>
        </tr>
    </thead>	
</table>
<!-- CONTROLLER DIALOG-->
<div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" id="controllers">
            </div>
        </div>
    </div>
</div>
<?php
if ($table == 'vw_agenciasViajes') {
    ?>
    <div class="right">
        <button type="button" class="btn btn-success btn-sm" onclick="cancelarModal()" >
            <span class="fa fa-check"></span> Aceptar
        </button>
    </div>
<?php } ?>
<!-- /CONTROLLER DIALOG -->