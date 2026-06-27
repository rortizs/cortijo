<?php

/**
 * general.class
 * @author Jonathan Juarez
 * @version 1.0 20140107
 * @version update 2.0 20150815
 */
require_once ("dbCon.php");

class CajaRapida {

    /** CONSTRUCTORES
     *
     */
    public $resultado;
    public $timeZone;
    public $timestamp;
    public $date;

    /** INICIALIZACION DE CONSTRUCTORES
     *
     */
    public function __construct() {
        $this->resultado = array();
        $this->timeZone = date_default_timezone_set("America/Guatemala");
        $this->timestamp = date('Y-m-d H:i:s');
        $this->date = date('Y-m-d');
    }
    
    /** METODO getCategorias
     *
     */
    public function getCategorias($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    id,descripcion
                FROM
                    familiaNivel2
                WHERE
                    idEmpresas = " . $params['idEmpresas'] . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getProductosCategorias
     *
     */
    public function getProductosCategorias($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    id,image,sku,descLarga,precioPublico,idTipoProductos,
                    ifnull((SELECT saldo FROM inventarios WHERE ingresoA=2 and idProductos=productos.id ORDER BY id DESC,DATE(created_at) DESC limit 1),0) as existencia
                FROM
                    productos
                WHERE
                    idFamiliaNivel2 = " . $params['idCategorias'] . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO agregarProductoVenta
     *
     */
    public function agregarProductoVenta($params) {
        //echo $params['tipoProducto'] . ' ' . $params['idProducto'];
        //Consulta los componentes y existencia en inventario
        //Obtener la sucursal de facturacion de la tabla facturacionConf
        $sql1 = "SELECT 
                    ingresoA, idPuntoIngreso,agruparItems
                FROM
                    facturacionConf
                WHERE
                    opcion = 'facturacion'
                        AND idEmpresas = " . $params['idEmpresas'] . " AND idSucursales=" . $params['idSucursales'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        $sucursal = $reg1['idPuntoIngreso'];
        $response = "";
        $flag = false;
        //
        $sql = "SELECT 
                    concat(a.sku,' - ',a.descLarga) as producto,
                    b.unidades,
                    ifnull(c.saldo,'0.00') as saldo,
                    if(ifnull(c.saldo,'0.00') < (b.unidades*1),'false','true') as action,
                    a.idTipoProductos,
                FROM
                    productos as a 
                    left join productosComponentes as b on(a.id=b.idProductoPrincipal) 
                    inner join inventarios as c on(coalesce(b.idProductos,a.id)=c.idProductos)
                WHERE
                    c.ingresoA = " . $reg1['ingresoA'] . "
                    AND c.idPuntoIngreso = " . $reg1['idPuntoIngreso'] . "
                    and a.id = " . $params['idProductos'] . "
                order by c.id desc limit 1;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        while ($reg) {
            if ($reg1['valExistencias'] === '1') {
                if ($reg['action'] == 'false') {
                    $flag = true;
                    $response[] = array('producto' => $reg['producto'],
                        'action' => $reg['action'],
                        'existencia' => $reg['saldo'],
                        'cantidad' => ($reg['unidades'] * $params['cantidad']));
                }
            }
        }
        if ($flag == true) {
            return $response;
        } else {
            if ($reg1['agruparItems'] === '1' && strtoupper($params['codigo']) !== 'SC') {
                //Paso 1: Consultar si producto ya esta en la lista de productos
                $sql = "select * from ventasDetalle "
                        . "where idProductos=" . $params['idProductos'] . " and idVentas=0 and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $sucursal . ";";
                $query = mysql_query($sql, dbCon::conPrincipal());
                $reg = mysql_fetch_assoc($query);
                //Paso 2: Consulta costo del producto
                $sql1 = "select * from productos where id=" . $params['idProductos'] . ";";
                $query1 = mysql_query($sql1, dbCon::conPrincipal());
                $reg1 = mysql_fetch_assoc($query1);
                //
                if (mysql_num_rows($query) == 0) {
                    //Si no esta inserta
                    $total = $reg1['precioPublico'] * $params['cantidad'];
                    $totalCosto = $reg1['precioCosto'] * $params['cantidad'];
                    $sql = "insert into ventasDetalle(tipoProducto,idProductos,sku,descLarga,cantidad,precio,precioDolares,costo,total,totalDolares,totalCosto,idUsuarios,idSucursales,idEmpresas)" .
                            " values('" . $reg1['idTipoProductos'] . "'," . $params['idProductos'] . ",'" . strtoupper($reg1['sku']) . "','" . strtoupper($reg1['descLarga']) . "','" . $params['cantidad'] . "','" . $reg1['precioPublico'] . "','0.00','" . $reg1['precioCosto'] . "','" . $total . "','0.00','" . $totalCosto . "'," . $params['idUsuarios'] . "," . $params['idSucursales'] . "," . $params['idEmpresas'] . ");";
                    // echo $sql;
                    $query = mysql_query($sql, dbCon::conPrincipal());
                    if ($query == true) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed insert', 'Error' => $error, 'Query' => $sql);
                    }
                } else {
                    //Si esta actualiza cantidad, precioVenta, costo y totales
                    $newCantidad = ($reg['cantidad'] + $params['cantidad']);
                    $newTotal = ($reg['precio'] * $newCantidad);
                    $newTotalDolares = 0.00;
                    $newTotalC = ($reg1['costo'] * $newCantidad);
                    //
                    $sql = "update ventasDetalle set cantidad='" . $newCantidad . "',total='" . $newTotal . "',totalDolares='" . $newTotalDolares . "',totalCosto='" . $newTotalC . "' "
                            . "where idProductos=" . $reg1['id'] . " and idVentas=0 and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . ";";
                    $query = mysql_query($sql, dbCon::conPrincipal());
                    if ($query == true) {
                        $response[] = array('message' => 'success');
                    } else {
                        $response[] = array('message' => 'failed update', 'Query' => $sql);
                    }
                }
            } else {
                //Paso 2: Consulta costo del producto
                $sqlC = "select precioCosto as costo from productos where id=" . $params['idProducto'] . ";";
                $queryC = mysql_query($sqlC, dbCon::conPrincipal());
                $regC = mysql_fetch_assoc($queryC);
                //Si no esta inserta
                $totalCosto = $regC['costo'] * $params['cantidad'];
                $sql = "insert into ventasDetalle(tipoProducto,idProductos,sku,descLarga,cantidad,precio,precioDolares,costo,total,totalDolares,totalCosto,idUsuarios,idSucursales,idEmpresas)" .
                        " values('" . $params['tipoProducto'] . "'," . $params['idProducto'] . ",'" . strtoupper($params['codigo']) . "','" . strtoupper($params['descProducto']) . "','" . $params['cantidad'] . "','" . $params['precio'] . "','" . $params['precioDolares'] . "','" . $regC['costo'] . "','" . $params['total'] . "','" . $params['totalDolares'] . "','" . $totalCosto . "'," . $params['idUsuarios'] . "," . $params['idSucursales'] . "," . $params['idEmpresas'] . ");";
                //echo $sql;
                $query = mysql_query($sql, dbCon::conPrincipal());
                if ($query) {
                    $response[] = array('message' => 'success');
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed insert', 'Error' => $error, 'Query' => $sql);
                }
            }
            return $response;
        }
    }

    /** METODO loadProductosVenta
     *
     */
    public function loadProductosVenta($params) {
        $this->resultado = null;
        $filtro = "";
        if ($params['idVenta'] != '') {
            $filtro = "a.idVentas = " . $params['idVenta'] . "";
        } else {
            $filtro = "a.idVentas = 0 and idUsuarios=" . $params['idUsuarios'] . "";
        }
        $sql = "SELECT
                    a.id,
                    a.idProductos,
                    a.sku AS 'codigo',
                    a.descLarga,
                    a.cantidad,
                    a.precio,
                    a.total,
                    a.costo,
                    a.totalCosto
                FROM
                    ventasDetalle AS a 
                WHERE
                    " . $filtro . ";";
//        echo $sql."\n";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    public function cancelarVenta($params) {
        $response = "";
        $sql = "delete from ventasDetalle where idVentas=0  and idUsuarios=" . $params['idUsuarios'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'Query' => $sql);
        }
        return $response;
    }

    /** METODO eliminarProductoVenta
     *
     */
    public function eliminarProductoVenta($params) {
        $response = "";
        $sql = "delete from ventasDetalle where id=" . $params['id'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'Query' => $sql);
        }
        return $response;
    }

    /** METODO finalizarVenta
     *
     */
    public function finalizarVenta($params) {
        $response = "";
        //CREAR CLIENTE SI ID ES VACIO Y QUE NO SEA CF
        $idClientes = "0";
        if (strtoupper($params['nombre']) != 'CONSUMIDOR FINAL') {
            //Validar NIT
            $sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query = mysql_query($sql, dbCon::conPrincipal());
            $reg = mysql_fetch_assoc($query);
            if ($reg['id'] == '') {
                $sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,idTipoClientes,idEmpresas,created_at) "
                        . "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "',1," . $params['idEmpresas'] . ",'" . $this->timestamp . "');";
                $queryC = mysql_query($sqlC, dbCon::conPrincipal());
                if ($queryC == TRUE) {
                    $idClientes = mysql_insert_id();
                    error_log('cliente creado exitosamente\n');
                } else {
                    error_log('error al crear cliente ' . $sqlC);
                }
            } else {
                $idClientes = $reg['id'];
            }
        }
        // GUARDA VENTA
        $sql = "insert into ventas 
                values(
                    null,
                    '" . $params['serie'] . "',
                    '" . $params['correlativo'] . "',
                    '" . $this->date . "',
                    '" . $params['total'] . "',
                    '" . $params['subtotal'] . "',
                    '" . $params['descuentoM'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $params['total'] . "',
                    '" . $params['anticipo'] . "',    
                    '" . $params['saldo'] . "',
                    '" . $params['iva'] . "',
                    '" . $params['tasaCambio'] . "',
                    '" . $params['totalDolares'] . "',
                    '" . $params['totalEnLetras'] . "',
                    '',
                    '" . $params['nit'] . "',
                    '" . $params['nombre'] . "',
                    '" . $params['direccion'] . "',
                    '1',
                    '" . $params['tipoVenta'] . "',
                    '',
                    '" . $params['idUsuarios'] . "',
                    '" . $params['idSucursales'] . "',    
                    '" . $params['idEmpresas'] . "',
                    '" . $this->timestamp . "',
                    null,
                    0,
                    0,
                    0,
                    0,
                    null,
                    null,
                    null,
                    '" . $idClientes . "',
                    null);";
        $query = mysql_query($sql, dbCon::conPrincipal());
        //echo $query;
        if ($query) {
            //OBTIENE ID VENTA
            $idVenta = mysql_insert_id();
            $sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
                    . "where idVentas=0 and idUsuarios=" . $params['idUsuarios'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2) {
                //DESCUENTA PRODUCTO DE INVENTARIO
                $this->movimientoInventario($idVenta, 'FAC - ' . ($params['serie'] . '-' . $params['correlativo']), 'salida', $this->timestamp, $params['dbName']);
                //ACTUALIZAR CORRELATIVO
                $this->updateCorrelativoDocumento($params['idDocumento'], $params['correlativo'],$params['dbName']);
                //
                $response[] = array('message' => 'success', 'idVenta' => $idVenta, 'idSucursales' => $params['idSucursales']);
            } else {
                $response[] = array('message' => 'failed step 2', 'Query' => $sql2);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step 1', 'Query' => $sql, 'Error' => $error);
        }
        return $response;
    }

    /** METODO updateCorrelativoFactura
     *
     */
    public function updateCorrelativoDocumento($idDocumento, $correlativo,$dbName) {
        if ($correlativo >= $correlativo) {
            $correlativo = $correlativo;
            $new = $correlativo + 1;
            $correlativo = str_pad($new, strlen($correlativo), '0', STR_PAD_LEFT);
            //
            $sql = "update documentosCorrelativos set correlativo='" . $correlativo . "' "
                    . "where id=" . $idDocumento . ";";
            //echo $sql;
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('correlativo actualizado exitosamente');
            } else {
                error_log('error al actualizar correlativo');
            }
        }
    }

    /** METODO getDocumentoFacturacion
     *
     */
    public function getDocumentoFacturacion($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    id,
                    serie,
                    correlativo
                FROM
                    documentosCorrelativos
                WHERE
                    idDocumentos = 6 and idEmpresas=" . $params['idEmpresas'] . " and idUsuarios=".$params['idUsuarios'].";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /**
     * 
     */
    public function movimientoInventario($idVenta, $documento, $operacion, $fechaMovimiento, $dbName) {
        //Obtener los productos de la venta
        $sql = "select tipoProducto,idProductos,cantidad,idUsuarios,idSucursales,idEmpresas "
                . "from ventasDetalle "
                . "where idVentas=" . $idVenta . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            switch ($reg['tipoProducto']) {
                case 3:
//                    echo 'estoy en combos';
                    //Consulta los componentes y existencia en inventario
                    $sql2 = "select 
                                a.idProductos,
                                concat(c.sku,' - ',c.descLarga) as producto,
                                a.unidades,
                                ifnull((SELECT 
                                            ifnull(saldo,0)
                                        FROM
                                            inventarios
                                        WHERE
                                            idProductos = a.idProductos and ingresoA=2 and idPuntoIngreso=" . $reg['idSucursales'] . "
                                        order by id desc limit 1),'0.00') as saldo,
                                if(ifnull((SELECT 
                                            ifnull(saldo,0)
                                        FROM
                                            inventarios
                                        WHERE
                                            idProductos = a.idProductos and ingresoA=2 and idPuntoIngreso=" . $reg['idSucursales'] . "
                                        order by id desc limit 1),'0.00') < (a.unidades*" . $reg['cantidad'] . "),'false','true') as action
                            from
                                productosComponentes as a
                                    left join
                                inventario as b ON (a.idProductos = b.idProductos)
                                    inner join
                                productos as c ON (a.idProductos = c.id)
                            where
                                a.idProductoPrincipal = " . $reg['idProductos'] . " group by c.sku;";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    while ($reg2 = mysql_fetch_assoc($query2)) {
                        $saldo = $reg2['saldo'] - ($reg2['unidades'] * $reg['cantidad']);
                        //Procesar movimiento
                        $sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos," . $operacion . ",saldo,idUsuarios,idEmpresas,created_at)"
                                . "values('2'," . $reg['idSucursales'] . ",'" . $documento . "'," . $reg2['idProductos'] . ",'" . ($reg2['unidades'] * $reg['cantidad']) . "','" . $saldo . "'," . $reg['idUsuarios'] . "," . $reg['idEmpresas'] . ",'" . $fechaMovimiento . "');";
                        $query3 = mysql_query($sql3, dbCon::conPrincipal());
                        if ($query3) {
                            error_log('insert true producto: ' . $reg2['producto']);
                        } else {
                            error_log('error insert producto: ' . $reg2['producto'] . ' ' . $sql3);
                        }
                    }
                    break;
                case 1:
                    //echo 'estoy en productos';
                    $sql2 = "select 
                                saldo
                            from
                                inventarios
                            where
                                ingresoA='2' and idPuntoIngreso=" . $reg['idSucursales'] . " and idProductos=" . $reg['idProductos'] . " and idEmpresas=" . $reg['idEmpresas'] . "
                            order by id desc limit 1;";
                    //echo $sql2;
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    $reg2 = mysql_fetch_assoc($query2);
                    $saldo = 0;
                    if ($operacion == 'ingreso') {
                        $saldo = $reg2['saldo'] + $reg['cantidad'];
                    } else {
                        $saldo = $reg2['saldo'] - $reg['cantidad'];
                    }
                    //Procesar movimiento
                    $sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos," . $operacion . ",saldo,idUsuarios,idEmpresas,created_at)"
                            . "values('2'," . $reg['idSucursales'] . ",'" . $documento . "'," . $reg['idProductos'] . ",'" . $reg['cantidad'] . "','" . $saldo . "'," . $reg['idUsuarios'] . "," . $reg['idEmpresas'] . ",'" . $fechaMovimiento . "');";
//                    echo $sql3;
                    $query3 = mysql_query($sql3, dbCon::conPrincipal());
                    if (!$query3) {
                        echo mysql_error();
                    }
                    break;
                default :
                    echo 'no estoy en ninguno';
                    break;
            }
        }
    }

}
