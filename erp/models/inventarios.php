<?php

/**
 * POS /Modulo Inventarios - Class Inventarios
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
require_once ("dbCon.php");
require_once ("general.php");
require_once ("admin.php");
require_once ("contabilidad.php");
require_once ("bancos.php");

class Inventarios extends General {

    /** METODO facturacionConf
     * 
     */
    public function facturacionConf($opcion, $idEmpresas, $idSucursales) {
        $sql = "select 
                    *
                from 
                    facturacionConf
                where 
                    opcion='" . $opcion . "' and idEmpresas=" . $idEmpresas . " and idSucursales=" . $idSucursales . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO GET INFO PRODUCTO
     * 
     */
    public function getInfoProducto($idProducto) {
        $sql = "SELECT 
                    *
                FROM
                    vw_productosMedidas
                WHERE
                    id=" . $idProducto . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO AGREGAR COMPONENTE PRODUCTO
     * 
     */
    public function agregarComponenteProducto($parameters) {
        $total = $parameters['costo'] * $parameters['unidades'];
        if ($parameters['idMedidas'] == '') {
            $parameters['idMedidas'] = "0";
        }
        $sql = "insert into productosComponentes values(null," . $parameters['idProductoPrincipal'] . "," . $parameters['idProducto'] . "," . $parameters['idMedidas'] . ",'" . $parameters['costo'] . "','" . $parameters['unidades'] . "','" . $total . "');";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->costoPorComponentes($parameters['idProductoPrincipal']);
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Producto agregado exitosamente</div>";
            return $message;
        } else {
            $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Error: Al agregar producto QUERY " . $sql . "</div>";
            return $message;
        }
    }

    /** METODO ACTUALIZAR COMPONENTE PRODUCTO
     * 
     */
    public function actualizarComponenteProducto($parameters) {
        $total = $parameters['costo'] * $parameters['unidades'];
        $sql = "update productosComponentes set unidades='" . $parameters['unidades'] . "', total='" . $total . "'
                where idProductoPrincipal=" . $parameters['idProductoPrincipal'] . " and idProductos=" . $parameters['idProducto'] . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->costoPorComponentes($parameters['idProductoPrincipal']);
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Producto agregado exitosamente</div>";
            return $message;
        } else {
            $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Error: Al agregar producto QUERY " . $sql . "</div>";
            return $message;
        }
    }

    /** METODO ELIMINAR COMPONENTE PRODUCTO
     * 
     */
    public function eliminarComponenteProducto($parameters) {
        $sql = "delete from productosComponentes where id=" . $parameters['idComponente'] . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->costoPorComponentes($parameters['idProductoPrincipal']);
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Producto eliminado exitosamente</div>";
            return $message;
        } else {
            $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Error: Al eliminar producto QUERY " . $sql . "</div>";
            return $message;
        }
    }

    /** METODO LISTADO DE COMPONENTES DE PRODUCTOS
     * 
     */
    public function listadoProductosComponentes($idProducto) {
        $this->resultado = null;
        $sql = "SELECT 
                    productosComponentes.id as idComponente,
                    productos.id as idProducto,
                    productos.sku,
                    productos.descLarga,
                    medidas.descripcion as medida,
                    productosComponentes.costo,
                    productosComponentes.unidades,
                    productosComponentes.total,
                    productosComponentes.idMedidas
                FROM
                    productosComponentes inner join productos on(productosComponentes.idProductos=productos.id)
                    left join medidas on(productosComponentes.idMedidas=medidas.id)
                WHERE
                    idProductoPrincipal = " . $idProducto . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO COSTO PRODUCTO SEGUN LISTADO DE COMPONENTES
     * 
     */
    public function costoPorComponentes($idProducto) {
        $sql = "SELECT 
                    round(sum(total),2) as total
                FROM
                    productosComponentes inner join productos on(productosComponentes.idProductos=productos.id)
                    inner join medidas on(productosComponentes.idMedidas=medidas.id)
                WHERE
                    idProductoPrincipal = " . $idProducto . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        //
        $sql2 = "update productos set precioCosto='" . $reg['total'] . "' where id=" . $idProducto . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
    }

    /** METODO LISTADO DE COMPONENTES DE PRODUCTOS
     * 
     */
    public function listadoPresentaciones($idProducto) {
        $this->resultado = null;
        $sql = "SELECT 
                    productosPresentaciones.descripcion,
                    productosPresentaciones.id as idPresentacion,
                    medidas.descripcion as medida,
                    productosPresentaciones.unidades,
                    productosPresentaciones.precio
                FROM
                    productosPresentaciones 
                    inner join medidas on(productosPresentaciones.idMedidas=medidas.id)
                WHERE
                    productosPresentaciones.idProductos = " . $idProducto . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO AGREGAR PRESENTACION DE VENTA
     * 
     */
    public function agregarPresentacion($parameters) {
        $sql = "insert into productosPresentaciones values(null,'" . $parameters['descripcion'] . "'," . $parameters['idProductoPrincipal'] . "," . $parameters['idMedidas'] . ",'" . $parameters['unidades'] . "','" . $parameters['precioVenta'] . "')";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->costoPorComponentes($parameters['idProductoPrincipal']);
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Presentacion agregada exitosamente</div>";
            return $message;
        } else {
            $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Error: Al agregar presentacion QUERY " . $sql . "</div>";
            return $message;
        }
    }

    /** METODO ELIMINAR PRESENTACION
     * 
     */
    public function eliminarPresentacion($parameters) {
        $sql = "delete from productosPresentaciones where id=" . $parameters['idPresentacion'] . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Presentacion eliminada exitosamente</div>";
            return $message;
        } else {
            $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Error: Al eliminar presentacion QUERY " . $sql . "</div>";
            return $message;
        }
    }

    /** METODO GET STATUS COMPRA
     *
     */
    public function getStatusCompra() {
        $this->resultado = null;
        $sql = "select * from statusCompra order by id asc;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getCompraDetalle
     * 
     */
    public function getCompraDetalle($params) {
        $this->resultado = null;
        $filtros = "";
        //echo 'idCompra: '.$params['idCompra'];
        if ($params['idCompra'] !== 0) {
            $filtros .= "ocd.idCompras=" . $params['idCompra'] . "";
        } else {
            $filtros .= "ocd.idUsuarios=" . $params['idUsuarios'] . " and ocd.idCompras=0";
        }
        $sql = "select 
                    ocd.id,
                    p.sku,
                    p.descLarga,
                    ocd.cantidad,
                    ocd.precioCompra,
                    ocd.descuento,
                    ocd.total,
                    ifnull(oc.documento, 'n/a') as idComprasOrdenes,
                    p.idMedidas,
                    p.id as idProductos,
                    ocd.fechaVencimiento,
                    ocd.noLote
                from
                    comprasDetalle as ocd
                        left join
                    vw_productos as p ON (ocd.idProductos = p.id)
                        left join
                    comprasOrdenes as oc ON (ocd.idComprasOrdenes = oc.id)
                where " . $filtros . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO AGREGAR PRODUCTO A COMPRAS
     *
     */
    public function addItemCompra($params, $idUsuarios) {
        $response = "";
        $sql = "";
        if ($params['idCompra'] != "") {
            $sql = "insert into comprasDetalle(idCompras,idProductos,descuento,precioCompra,cantidad,total,idUsuarios,fechaVencimiento,noLote)"
                    . "values(" . $params['idCompra'] . "," . $params['idProductos'] . ",'" . $params['descuento'] . "','" . $params['precioCompra'] . "','" . $params['cantidad'] . "','" . $params['total'] . "'," . $idUsuarios . ",'".$params['fechaVencimiento']."','".$params['noLote']."');";
        } else {
            $sql = "insert into comprasDetalle(idProductos,descuento,precioCompra,cantidad,total,idUsuarios,fechaVencimiento,noLote)"
                    . "values(" . $params['idProductos'] . ",'" . $params['descuento'] . "','" . $params['precioCompra'] . "','" . $params['cantidad'] . "','" . $params['total'] . "'," . $idUsuarios . ",'".$params['fechaVencimiento']."','".$params['noLote']."');";
        }
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed', "error" => $error);
        }
        return $response;
    }

    /** METODO removeSerieProducto
     *
     */
    public function removeSerieProducto($params) {
        $response = "";
        $sql = "delete from inventarios where id=" . $params['item'] . " and idProductos=" . $params['idProductos'] . " and documento='';";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO GUARDAR COMPRA
     *
     */
    public function guardarCompra($params) {
        $response = "";
        //VALIDAR SERIE Y CORRELATIVO DE FACTURA SI DOCUMENTO GENERA IVA
        if ($params['generaIva'] == '1') {
            $sql = "select * from compras where serieFactura='" . $params['serieFactura'] . "' and noFactura='" . $params['noFactura'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query = mysql_query($sql, dbCon::conPrincipal());
            $reg = mysql_fetch_assoc($query);
            if ($reg['id'] === null) {
                $admin = new Admin();
                $documento = $params['tipoDocumento'] . "-" . $params['correlativo'];
                $saldo = 0;
                if ($params['idTipoCompra'] == 2) {
                    $saldo = $params['total'];
                }
                $sql = "insert into compras
                values(
                    null,
                    '" . $params['idTipoDocumento'] . "',
                    '" . $params['correlativo'] . "',
                    '" . $params['idProveedores'] . "',
                    " . $params['pequenoContribuyente'] . ",    
                    '" . $params['idTipoOperacion'] . "',
                    '" . $params['idTipoCompra'] . "',
                    '" . $params['conceptoCompra'] . "',
                    '" . $params['serieFactura'] . "',
                    '" . $params['noFactura'] . "',
                    '" . date("Y-m-d", strtotime($params['fechaContabilizacion'])) . "',
                    '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                    '" . date("Y-m-d", strtotime($params['fechaPago'])) . "',
                    '" . $params['valorFactura'] . "',
                    '" . $params['subtotal'] . "',
                    '" . $params['exento'] . "',
                    '" . $params['inguat'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $params['descuentoM'] . "',
                    '" . $params['total'] . "',
                    '" . $params['iva'] . "',
                    '" . $saldo . "',    
                    '" . $params['idUsuarios'] . "',
                    '" . $params['idSucursales'] . "',
                    '" . $params['idEmpresas'] . "',
                    '" . $this->timestamp . "',
                    null,    
                    '" . $params['idFormato'] . "',
                    0,
                    " . ($params['idCentrosCosto'] ?: 0) . ",
                    " . ($params['ingresoA'] ?: 0) . ",
                    " . ($params['idPuntoIngreso'] ?: 0) . ",
                    " . ($params['generaIva'] ?: 1) . ",
                    " . ($params['idCajaChica'] ?: 0) . "
                );";
                $query = mysql_query($sql, dbCon::conPrincipal());
                if ($query == true) {
                    //CARGA DE ITEMS DE COMPRA A INVENTARIOS
                    $idCompra = mysql_insert_id();
                    $sql2 = "update comprasDetalle set idCompras=" . $idCompra . " where idCompras=0 and idUsuarios=" . $params['idUsuarios'] . ";";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    if ($query2) {
                        if ($params['idTipoOperacion'] === '5') {
                            //$this->cargarComprasInventario($idCompra, $params['ingresoA'], $params['idPuntoIngreso']);
                            $this->cargarComprasInventario($idCompra, $params['ingresoA'], $params['idPuntoIngreso'], $documento, date("Y-m-d", strtotime($params['fechaFactura'])), $params['idEmpresas'], $params['idUsuarios']);
                        }
                    } else {
                        $response[] = array('message' => 'failed');
                    }
                    if ($params['idFormato'] !== '0') {
                        $params['idCompras'] = $idCompra;
                        $params['partida_at'] = $params['fechaContabilizacion'];
                        $params['idTipoOperacionPartida'] = 2;
                        $params['concepto'] = $params['conceptoCompra'];
                        $params['idTipoDocumento'] = 3;
                        $this->savePartidaAutomatica($params);
                    }
                    //ACTUALIZA CXP CUANDO LA COMPRA ES CREDITO
                    if ($params['idTipoCompra'] === '2') {
                        $this->comprasCredito($params['idProveedores'], $idCompra, ($params['serieFactura'] . "-" . $params['noFactura']), $params['total'], date("Y-m-d", strtotime($params['fechaFactura'])), $params['idUsuarios'], $params['idEmpresas']);
                    }
                    //ACTUALIZA CORRELATIVO COMPRA
                    $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
                    //ACTUALIZA ORDEN DE COMPRA
                    if ($params['idOrdenCompra']) {
                        $this->cerrarOrdenCompra($idCompra, $params['idOrdenCompra'], 'compras');
                    }
                    //ACTUALIZAR MONTOS A CAJA CHICA
                    if ($params['idCajaChica'] != "") {
                        $this->actualizarMontosCajaChica($params['idCajaChica'], $params['total']);
                    }
                    $response[] = array('message' => 'success', 'idCompra' => $idCompra);
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed', 'query' => $error);
                }
                return $response;
            } else {
                $response[] = array('message' => 'docExists');
            }
            return $response;
        } else {
            //NO VALIDAR SERIE Y CORRELATIVO DE FACTURA SI DOCUMENTO NO GENERA IVA
            $admin = new Admin();
            $documento = $params['tipoDocumento'] . "-" . $params['correlativo'];
            $saldo = 0;
            if ($params['idTipoCompra'] == 2) {
                $saldo = $params['total'];
            }
            $sql = "insert into compras
                values(
                    null,
                    '" . $params['idTipoDocumento'] . "',
                    '" . $params['correlativo'] . "',
                    '" . $params['idProveedores'] . "',
                    " . $params['pequenoContribuyente'] . ",    
                    '" . $params['idTipoOperacion'] . "',
                    '" . $params['idTipoCompra'] . "',
                    '" . $params['conceptoCompra'] . "',
                    '" . $params['serieFactura'] . "',
                    '" . $params['noFactura'] . "',
                    '" . date("Y-m-d", strtotime($params['fechaContabilizacion'])) . "',
                    '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                    '" . date("Y-m-d", strtotime($params['fechaPago'])) . "',
                    '" . $params['valorFactura'] . "',
                    '" . $params['subtotal'] . "',
                    '" . $params['exento'] . "',
                    '" . $params['inguat'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $params['descuentoM'] . "',
                    '" . $params['total'] . "',
                    '" . $params['iva'] . "',
                    '" . $saldo . "',    
                    '" . $params['idUsuarios'] . "',
                    '" . $params['idSucursales'] . "',
                    '" . $params['idEmpresas'] . "',
                    '" . $this->timestamp . "',
                    null,    
                    '" . $params['idFormato'] . "',
                    null,
                    " . ($params['idCentrosCosto'] ?: 0) . ",
                    " . ($params['ingresoA'] ?: 0) . ",
                    " . ($params['idPuntoIngreso'] ?: 0) . ",
                    " . ($params['generaIva'] ?: 1) . ",
                    " . ($params['idCajaChica'] ?: 0) . "
                );";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                //CARGA DE ITEMS DE COMPRA A INVENTARIOS
                $idCompra = mysql_insert_id();
                $sql2 = "update comprasDetalle set idCompras=" . $idCompra . " where idCompras=0 and idUsuarios=" . $params['idUsuarios'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if ($query2) {
                    if ($params['idTipoOperacion'] === '5') {
                        $this->cargarComprasInventario($idCompra, $params['ingresoA'], $params['idPuntoIngreso']);
                    }
                } else {
                    $response[] = array('message' => 'failed');
                }
                if ($params['idFormato'] !== '0') {
                    $params['idCompras'] = $idCompra;
                    $params['partida_at'] = $params['fechaContabilizacion'];
                    $params['idTipoOperacionPartida'] = 2;
                    $params['concepto'] = $params['conceptoCompra'];
                    $params['idTipoDocumento'] = 3;
                    $this->savePartidaAutomatica($params);
                }
                //ACTUALIZA CXP CUANDO LA COMPRA ES CREDITO
                if ($params['idTipoCompra'] === '2') {
                    $this->comprasCredito($params['idProveedores'], $idCompra, ($params['serieFactura'] . "-" . $params['noFactura']), $params['total'], date("Y-m-d", strtotime($params['fechaFactura'])), $params['idUsuarios'], $params['idEmpresas']);
                }
                //ACTUALIZA CORRELATIVO COMPRA
                $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
                //ACTUALIZAR MONTOS A CAJA CHICA
                if ($params['idCajaChica'] != "") {
                    $this->actualizarMontosCajaChica($params['idCajaChica'], $params['total']);
                }
                $response[] = array('message' => 'success', 'idCompra' => $idCompra);
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed', 'query' => $error);
            }
            return $response;
        }
    }

    /** METODO cargarComprasInventario
     *
     */
    public function cargarComprasInventario($idCompras, $ingresoA, $idPuntoIngreso, $documento, $fechaCompra, $idEmpresas, $idUsuarios) {
        //PROCESOS
        //Obtiene los productos de la compra
        $sql = "SELECT
                    a.idProductos,
                    (b.equivalente * a.cantidad) AS existCompra,
                    ROUND((a.precioCompra / b.equivalente), 2) AS costoUnitarioCompra,
                    ROUND(((b.equivalente * a.cantidad) * (a.precioCompra / b.equivalente)),2) AS costoCompra,
                    b.idTipoProductos,
                    b.idUtilizaSerie
                FROM
                    comprasDetalle as a
                    inner join productos as b on(a.idProductos=b.id)
                WHERE
                    a.idCompras =" . $idCompras . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            //Obtiene el saldo actual de cada producto
            $sql2 = "SELECT 
                        a.saldo,
                        b.precioCosto as costoUnitarioInv,
                        (a.saldo*b.precioCosto) as costoExistActual
                    FROM
                        inventarios as a inner join productos as b on(a.idProductos=b.id)
                    WHERE
                        a.idProductos = " . $reg['idProductos'] . " 
                        AND a.ingresoA = " . $ingresoA . "
                        AND a.idPuntoIngreso = " . $idPuntoIngreso . "
                    ORDER BY a.id DESC
                    LIMIT 1";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            $reg2 = mysql_fetch_assoc($query2);
            $saldo = ($reg2['saldo'] ?: 0);
            //Calcula costo promedio
            $costoPromedio = (($reg['costoCompra'] + $reg2['costoExistActual']) / ($saldo + $reg['existCompra']));
            //Actualiza nuevo costo en tabla productos
            $sql3 = "update productos set precioCosto='" . $costoPromedio . "' where id=" . $reg['idProductos'] . ";";
            $query3 = mysql_query($sql3, dbCon::conPrincipal());
            if ($query3) {
                //Insertar bitacora de costos
                $sql4 = "insert into productosCostos "
                        . "values(null,'" . $documento . "'," . $reg['idProductos'] . ",'" . $saldo . "','" . $reg2['costoUnitarioInv'] . "','" . $reg2['costoExistActual'] . "','" . $reg['existCompra'] . "','" . $reg['costoUnitarioCompra'] . "','" . $reg['costoCompra'] . "','" . $costoPromedio . "','" . $fechaCompra . "'," . $idPuntoIngreso . "," . $idEmpresas . ");";
                $query4 = mysql_query($sql4, dbCon::conPrincipal());
                if ($query4) {
                    //Insertar existencias a inventario
                    $sql5 = "";
                    if ($reg['idUtilizaSerie'] == '1') {
                        $sql5 = "update inventarios set ingresoA='" . $ingresoA . "', idPuntoIngreso=" . $idPuntoIngreso . ", documento='" . $documento . "', idTipoProductos=" . $reg['idTipoProductos'] . "
                                 where documento is null and idProductos=" . $reg['idProductos'] . " and idUsuarios=" . $idUsuarios . " and idEmpresas=" . $idEmpresas . ";";
                    } else {
                        $sql5 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos,ingreso,saldo,idUsuarios,idEmpresas)"
                                . "values('" . $ingresoA . "'," . $idPuntoIngreso . ",'COM " . $documento . "'," . $reg['idProductos'] . ",'" . $reg['existCompra'] . "','" . ($saldo + $reg['existCompra']) . "'," . $idUsuarios . "," . $idEmpresas . ");";
                    }
                    $query5 = mysql_query($sql5, dbCon::conPrincipal());
                    if ($query5) {
                        //Actualiza nuevo costo en productos fabricados que tengan como componente el producto
                        $sql6 = "update productosComponentes set costo='" . $costoPromedio . "' where idProductos=" . $reg['idProductos'] . ";";
                        $query6 = mysql_query($sql6, dbCon::conPrincipal());
                        //Ingreso de series
                        $sql7 = "update inventarios set ingresoA=" . $ingresoA . ", idPuntoIngreso=" . $idPuntoIngreso . ",documento='" . $documento . "'
                                 where documento is null and idEmpresas=" . $idEmpresas . ";";
                        $query7 = mysql_query($sql7, dbCon::conPrincipal());
                        //
                        error_log('success all steps');
                    } else {
                        error_log('failed step 5 ' . $sql5);
                    }
                } else {
                    error_log('failed step 4 ' . $sql4);
                }
            } else {
                error_log('failed step 3 ' . $sql3);
            }
        }
    }

    /** METODO CARGA DE INVENTARIO
     *
     */
    public function cargaInventario($params) {
        $response = "";
        //verificar si el producto existe en el catalogo de productos
        $sql1 = "select id as idProductos from productos where sku='" . $params['codigo'] . "';";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        if ($reg1['idProductos'] != '') {
            // inserta en la tabla de inventarios
            $sql = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos,ingreso,saldo,idUsuarios,idEmpresas)"
                    . "values('" . $params['ingresoA'] . "'," . $params['idPuntoIngreso'] . ",'" . $params['documento'] . "'," . $reg1['idProductos'] . ",'" . $params['cantidad'] . "','" . $params['cantidad'] . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ");";
            $query = mysql_query($sql, dbCon::conPrincipal());
        }
    }

    /** METODO AGREGAR PRODUCTO A TRASLADO
     *
     */
    public function addItemTraslado($params, $idUsuarios) {
        $response = "";
        $sql1 = "select id as idProductos from productos where sku='" . $params['codigo'] . "';";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        if ($reg1['idProductos'] != '') {
            $sql = "insert into trasladosDetalle(idProductos,cantidad,idUsuarios)"
                    . "values(" . $reg1['idProductos'] . ",'" . $params['cantidad'] . "'," . $idUsuarios . ")";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('success addItemTraslado ' . $params['codigo']);
                $response[] = array('message' => 'success');
            } else {
                error_log('failed addItemTraslado ' . $params['codigo']);
                $response[] = array('message' => 'failed');
            }
        }
        return $response;
    }

    /** METODO getTrasladoDetalleUsuario
     * 
     */
    public function getTrasladoDetalleUsuario($idUsuarios) {
        $this->resultado = null;
        $sql = "select 
                    a.id,
                    b.sku,
                    b.descLarga,
                    a.cantidad,
                    a.idProductos
                from
                    trasladosDetalle as a inner join productos as b on(a.idProductos=b.id)
                where
                    a.idUsuarios=" . $idUsuarios . " and idTraslados is null;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO AGREGAR ITEM COMPRA
     *
     */
    public function removeItemTraslado($params, $idUsuarios) {
        $response = "";
        $sql = "delete from trasladosDetalle where id=" . $params['item'] . " and idUsuarios=" . $idUsuarios . " and idTraslados is null;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO FINALIZAR TRASLADO
     * 
     */
    public function finalizarTraslado($params, $idUsuarios, $idEmpresas) {
        $response = "";
        $documento = $params['tipoDocumento'] . "-" . $params['correlativo'];
        $sql = "insert into traslados
                    values(null,'" . $documento . "'," . $params['salidaDe'] . "," . $params['idPuntoSalida'] . "," . $params['ingresoA'] . "," . $params['idPuntoIngreso'] . ",1,'" . $params['observaciones'] . "'," . $idUsuarios . "," . $idEmpresas . ",'" . date("Y-m-d", strtotime($params['fechaOperacion'] ?: $this->date)) . "');";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $idTraslado = mysql_insert_id();
            $sql2 = "update trasladosDetalle set idTraslados=" . $idTraslado . " where idTraslados is null and idUsuarios=" . $idUsuarios . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2) {
                $admin = new Admin();
                $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $idEmpresas);
                $this->descontarProductoTraslado($idTraslado);
                $response[] = array('message' => 'success', 'idTraslados' => $idTraslado);
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step2', 'error' => $error);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step1', 'error' => $error);
        }
        return $response;
    }

    /** METODO DESCONTAR PRODUCTO
     * 
     */
    public function descontarProductoTraslado($idTraslado) {
        $sql = "SELECT
                    a.salidaDe,
                    a.idPuntoSalida,
                    a.documento,
                    a.idUsuarios,
                    a.idEmpresas,
                    b.idProductos,
                    c.idTipoProductos,
                    c.idUtilizaSerie,
                    b.cantidad,
                    date_format(a.created_at,'%Y-%m-%d') as fechaOperacion
                FROM
                    traslados as a inner join trasladosDetalle as b on(a.id=b.idTraslados)
                    inner join productos as c on(b.idProductos=c.id)
                WHERE
                    a.id =" . $idTraslado . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            if ($reg['idUtilizaSerie'] == '1') {
                $sql2 = "update inventarios set ingresoA='" . $reg['salidaDe'] . "', idPuntoIngreso=" . $reg['idPuntoSalida'] . ", documento='" . $reg['documento'] . "', idTipoProductos=" . $reg['idTipoProductos'] . "
                         where  documento is null and idProductos=" . $reg['idProductos'] . " and idUsuarios=" . $reg['idUsuarios'] . " and idEmpresas=" . $reg['idEmpresas'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if (!$query2) {
                    $error = mysql_error();
                    error_log('Error en procesar ajuste: ' . $error);
                }
            } else {
                $saldo = 0;
                //Validar existencias si tipo de producto no sea Servicio
                if ($reg['idTipoProductos'] === '1') {
                    $cantidad = $reg['cantidad'];
                    $sql2 = "select 
                                saldo
                            from
                                inventarios
                            where
                                ingresoA='" . $reg['salidaDe'] . "' and idPuntoIngreso=" . $reg['idPuntoSalida'] . " and idProductos=" . $reg['idProductos'] . " and idEmpresas=" . $reg['idEmpresas'] . "
                            order by id desc limit 1;";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    $reg2 = mysql_fetch_assoc($query2);
                    $saldo = ($reg2['saldo'] ?: 0) - $reg['cantidad'];
                }
                //Procesar movimiento
                $sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idTipoProductos,idProductos,salida,saldo,idUsuarios,idEmpresas,created_at)"
                        . "values('" . $reg['salidaDe'] . "'," . $reg['idPuntoSalida'] . ",'" . $reg['documento'] . "'," . $reg['idTipoProductos'] . "," . $reg['idProductos'] . ",'" . $reg['cantidad'] . "','" . $saldo . "'," . $reg['idUsuarios'] . "," . $reg['idEmpresas'] . ",'" . $reg['fechaOperacion'] . "');";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if (!$query3) {
                    error_log('Error en procesar movimiento: ' . $sql3);
                }
            }
        }
    }

    /** METODO getTrasladoDetalleUsuario
     * 
     */
    public function getTrasladoDetalle($idTraslado) {
        $this->resultado = null;
        $sql = "select 
                    a.id, a.idProductos, b.sku, b.descLarga, a.cantidad,c.idStatusTraslado as estado,
                    c.observaciones
                from
                    trasladosDetalle as a
                        inner join
                    productos as b ON (a.idProductos = b.id)
                        inner join
                    vw_traslados as c on(a.idTraslados=c.id)
                where
                    idTraslados =" . $idTraslado . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO INGRESAR TRASLADO
     * 
     */
    public function ingresarTraslado($params, $idUsuarios, $idEmpresas, $dbProject) {
        $response = "";
        $sql = "";
        if ($dbProject == 'erp_raylink') {
            $sql = "SELECT 
                    b.*,
                    c.idTipoProductos,
                    c.idUtilizaSerie,
                    a.documento,
                    d.serie,
                    a.ingresoA,
                    a.idPuntoIngreso
                FROM
                    traslados as a 
                    inner join trasladosDetalle as b on(a.id=b.idTraslados)
                    inner join productos as c on(b.idProductos=c.id)
                    left join inventarios as d on(a.documento=d.documento)
                WHERE
                    b.idTraslados=" . $params['idTraslado'] . ";";
        } else {
            $sql = "SELECT 
                        b.*,
                        c.idTipoProductos,
                        c.idUtilizaSerie,
                        a.documento,
                        a.ingresoA,
                        a.idPuntoIngreso
                    FROM
                        traslados AS a
                            INNER JOIN
                        trasladosDetalle AS b ON (a.id = b.idTraslados)
                            INNER JOIN
                        productos AS c ON (b.idProductos = c.id)
                    WHERE
                        b.idTraslados = " . $params['idTraslado'] . ";";
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            if ($reg['idUtilizaSerie'] == '1') {
                $sql2 = "insert into inventarios (ingresoA,idPuntoIngreso,documento,idTipoProductos,idProductos,serie,ingreso,saldo,idUsuarios,idEmpresas)
                         values('" . $reg['ingresoA'] . "'," . $reg['idPuntoIngreso'] . ",'" . $reg['documento'] . "'," . $reg['idTipoProductos'] . "," . $reg['idProductos'] . ",'" . $reg['serie'] . "',1,1," . $idUsuarios . "," . $idEmpresas . ");";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if (!$query2) {
                    $error = mysql_error();
                    error_log('Error al ingresar traslado: ' . $error);
                }
            } else {
                $saldo = 0;
                $sql3 = "select 
                            saldo
                        from
                            inventarios
                        where
                            ingresoA='" . $reg['ingresoA'] . "' and idPuntoIngreso=" . $reg['idPuntoIngreso'] . " and idProductos=" . $reg['idProductos'] . " and idEmpresas=" . $idEmpresas . "
                        order by id desc limit 1;";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                $reg3 = mysql_fetch_assoc($query3);
                $saldo = $reg3['saldo'] + $reg['cantidad'];
                //
                $sql2 = "insert into inventarios (ingresoA,idPuntoIngreso,documento,idTipoProductos,idProductos,ingreso,saldo,idUsuarios,idEmpresas)
                         values('" . $reg['ingresoA'] . "'," . $reg['idPuntoIngreso'] . ",'" . $reg['documento'] . "'," . $reg['idTipoProductos'] . "," . $reg['idProductos'] . ",'" . $reg['cantidad'] . "','" . $saldo . "'," . $idUsuarios . "," . $idEmpresas . ");";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if (!$query2) {
                    $error = mysql_error();
                    error_log('Error al ingresar traslado: ' . $error);
                }
            }
        }
        //actualizar status traslado y observaciones
        $sql3 = "update traslados set idStatusTraslado=3, observaciones='" . $params['observaciones'] . "'
                 where id=" . $params['idTraslado'] . ";";
        $query3 = mysql_query($sql3, dbCon::conPrincipal());
        if ($query3 == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed step 3');
        }
        return $response;
    }

    /** procesarAjuste
     * 
     */
    public function procesarAjuste($params) {
        $response = "";
        //insertar en tabla de ajustes
        $factura = strtoupper($params['serieFactura'] . $params['noFactura']);
        $sql = "insert into ajustes values(null,'" . $params['ingresoA'] . "'," . $params['idPuntoIngreso'] . ",'" . $params['documento'] . "'," . $params['operacion'] . ",'" . $params['descripcion'] . "'," . $params['idUsuarios'] . ",'" . ($factura ?: 'N/A') . "'," . $params['idEmpresas'] . ",'" . date("Y-m-d", strtotime($params['fechaOperacion'])) . "');";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $idAjuste = mysql_insert_id();
            $sql2 = "update ajustesDetalle set idAjustes=" . $idAjuste . " where idAjustes is null and idUsuarios=" . $params['idUsuarios'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2) {
                $params['idAjuste'] = $idAjuste;
                $this->movimientoInventario($params);
                $admin = new Admin();
                $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
                $response[] = array('message' => 'success', 'idAjustes' => $idAjuste);
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step2', 'error' => $error);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step1', 'error' => $error, 'query' => $sql);
        }
        return $response;
    }

    /** METODO GET COMPRA
     * 
     */
    public function getCompra($idCompra) {
        $sql = "SELECT 
                    a.*,
                    b.descripcion AS nombreProveedor,
                    b.nitP,
                    b.direccionP,
                    b.diasCredito,
                    c.descripcion AS formato,
                    ifnull((SELECT 
                                idPartidas
                            FROM
                                (SELECT
                                        a.idPartidas, SUM(a.debe) AS total
                                FROM
                                        partidasDetalle as a
                                GROUP BY a.idPartidas) t
                            WHERE
                                total = a.total order by idPartidas desc limit 1),0) as idPartida,
                    a.idSucursales            
                FROM
                    compras AS a
                        INNER JOIN
                    proveedores AS b ON (a.idProveedores = b.id)
                        LEFT JOIN
                    formatos AS c ON (a.idFormatos = c.id)
                WHERE
                    a.id =" . $idCompra . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO GET loadProductoSKU
     * 
     */
    public function loadProductoSKU($param, $idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.*, b.descripcion AS tipoProducto,a.id as idProductos
                FROM
                    productos AS a
                    inner join tipoProductos AS b ON (a.idTipoProductos = b.id)
                    left join productosCodigosAlternos as c on(a.id=c.idProductos)
                where
                    coalesce(a.sku,a.upc) like '%" . $param['codigo'] . "%' and a.idEmpresas=" . $idEmpresas . " order by a.id desc limit 1;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET PRODUCTO BY CODIGO
     * 
     */
    public function loadProductoByCodigo($param, $idEmpresas) {
        $this->resultado = null;
        /*
          $sql = "select
          a.*
          from
          inventario as a inner join empresas as b on(a.idEmpresas=b.nombreComercial)
          where
          a.ingresoA = " . $param['ingresoA'] . " and a.idPuntoIngreso = " . $param['idPuntoIngreso'] . " and sku='" . $param['codigo'] . "' and b.id=" . $idEmpresas . ";";
          echo $sql . "<br/>";
         * 
         */
        $sql = "SELECT 
                    a.id,
                    a.ingresoA,
                    a.idPuntoIngreso,
                    a.idProductos,
                    g.sku,
                    b.descLarga,
                    c.descripcion as idTipoProductos,
                    f.descripcion as idAvailableSale,
                    ingreso,
                    salida,
                    saldo,
                    IFNULL((SELECT 
                            `ofertas`.`precio`
                        FROM
                            `ofertas`
                        WHERE
                            ((`ofertas`.`idProductos` = `b`.`id`)
                                AND (CAST(`ofertas`.`fechaHoraInicio` AS DATE) <= CURDATE())
                                AND (CAST(`ofertas`.`fechaHoraFin` AS DATE) >= CURDATE()))
                        ORDER BY `ofertas`.`id` DESC
                        LIMIT 1),
                    `b`.`precioCosto`) AS `costo`,
                    IFNULL((SELECT 
                            `ofertas`.`precio`
                        FROM
                            `ofertas`
                        WHERE
                            ((`ofertas`.`idProductos` = `b`.`id`)
                                AND (CAST(`ofertas`.`fechaHoraInicio` AS DATE) <= CURDATE())
                                AND (CAST(`ofertas`.`fechaHoraFin` AS DATE) >= CURDATE()))
                        ORDER BY `ofertas`.`id` DESC
                        LIMIT 1),
                    `b`.`precioPublico`) AS `precioPublico`,
                    d.nombreComercial as idEmpresas,
                    DATE_FORMAT(`a`.`created_at`, '%d-%m-%Y %H:%i:%s') AS `fechaIngreso`,
                    e.descripcion as idUtilizaSerie
                FROM
                    inventarios as a inner join productos as b on(a.idProductos=b.id)
                    inner join tipoProductos as c on(a.idTipoProductos=c.id)
                    inner join empresas d on(a.idEmpresas=d.id)
                    inner join utilizaSerie as e on(b.idUtilizaSerie=e.id)
                    inner join availableSale as f on(b.idAvailableSale=f.id)
                    left join productosCodigosAlternos as g on(a.idProductos=g.idProductos)
                WHERE
                    ingresoA = " . $param['ingresoA'] . " AND idPuntoIngreso = " . $param['idPuntoIngreso'] . " and b.sku ='" . $param['codigo'] . "' and b.idAvailableSale=1
                order by a.id desc limit 1;";
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
    public function movimientoInventario2($idVenta, $documento, $operacion) {
        //Obtener los productos de la venta
        $sql = "select tipoProducto,idProductos,cantidad,idUsuarios,idSucursales,idEmpresas "
                . "from ventasDetalle "
                . "where idVentas=" . $idVenta . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            switch ($reg['tipoProducto']) {
                case 'Producto Fabricado':
                    //Consulta los componentes y existencia en inventario
                    $sql2 = "select 
                                a.idProductos,
                                concat(c.sku,' - ',c.descLarga) as producto,
                                a.unidades,
                                ifnull(b.saldo,'0.00') as saldo,
                                if(ifnull(b.saldo,'0.00') < (a.unidades*" . $reg['cantidad'] . "),'false','true') as action
                            from
                                productosComponentes as a
                                    left join
                                inventario as b ON (a.idProductos = b.idProductos)
                                    inner join
                                productos as c ON (a.idProductos = c.id)
                            where
                                a.idProductoPrincipal = " . $reg['idProductos'] . ";";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    while ($reg2 = mysql_fetch_assoc($query2)) {
                        $saldo = $reg2['saldo'] - ($reg2['unidades'] * $reg['cantidad']);
                        //Procesar movimiento
                        $sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos," . $operacion . ",saldo,idUsuarios,idEmpresas)"
                                . "values('2'," . $reg['idSucursales'] . ",'" . $documento . "'," . $reg2['idProductos'] . ",'" . ($reg2['unidades'] * $reg['cantidad']) . "','" . $saldo . "'," . $reg['idUsuarios'] . "," . $reg['idEmpresas'] . ");";
                        $query3 = mysql_query($sql3, dbCon::conPrincipal());
                        if ($query3) {
                            error_log('insert true producto: ' . $reg2['producto']);
                        } else {
                            error_log('error insert producto: ' . $reg2['producto'] . ' ' . $sql3);
                        }
                    }
                    break;
                case 'Producto':
                    $sql2 = "select 
                                saldo
                            from
                                inventarios
                            where
                                ingresoA='2' and idPuntoIngreso=" . $reg['idSucursales'] . " and idProductos=" . $reg['idProductos'] . " and idEmpresas=" . $reg['idEmpresas'] . "
                            order by id desc limit 1;";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    $reg2 = mysql_fetch_assoc($query2);
                    $saldo = 0;
                    if ($operacion == 'ingreso') {
                        $saldo = $reg2['saldo'] + $reg['cantidad'];
                    } else {
                        $saldo = $reg2['saldo'] - $reg['cantidad'];
                    }
                    //Procesar movimiento
                    $sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos," . $operacion . ",saldo,idUsuarios,idEmpresas)"
                            . "values('2'," . $reg['idSucursales'] . ",'" . $documento . "'," . $reg['idProductos'] . ",'" . $reg['cantidad'] . "','" . $saldo . "'," . $reg['idUsuarios'] . "," . $reg['idEmpresas'] . ");";
                    $query3 = mysql_query($sql3, dbCon::conPrincipal());
                    error_log($query3);
                    break;
            }
        }
    }

    /** METODO MOVIMIENTO DE INVENTARIO
     * 
     */
    public function movimientoInventario($params) {
        $sql = "SELECT 
                    a.*,
                    b.idTipoProductos,
                    b.idUtilizaSerie
                FROM
                    ajustesDetalle AS a
                        LEFT JOIN
                    productos AS b ON (a.idProductos = b.id)
                WHERE
                    a.idAjustes=" . $params['idAjuste'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            if ($reg['idUtilizaSerie'] == '1') {
                $sql2 = "update inventarios set ingresoA='" . $params['ingresoA'] . "', idPuntoIngreso=" . $params['idPuntoIngreso'] . ", documento='" . $params['documento'] . "', idTipoProductos=" . $reg['idTipoProductos'] . "
                         where  documento='' and idProductos=" . $reg['idProductos'] . " and idUsuarios=" . $params['idUsuarios'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if (!$query2) {
                    $error = mysql_error();
                    echo ('Error en procesar ajuste: ' . $error);
                }
            } else {
                $cantidad = 0;
                $saldo = 0;
                $operacion = "";
                if ($params['operacion'] == '1') {
                    $operacion = 'ingreso';
                } else {
                    $operacion = 'salida';
                }
                //Validar existencias si tipo de producto no sea Servicio
                if ($reg['idTipoProductos'] !== '2') {
                    $cantidad = $reg['cantidad'];
                    $sql2 = "select 
                                saldo
                            from
                                inventarios
                            where
                                ingresoA='" . $params['ingresoA'] . "' and idPuntoIngreso=" . $params['idPuntoIngreso'] . " and idProductos=" . $reg['idProductos'] . " and idEmpresas=" . $params['idEmpresas'] . "
                            order by id desc limit 1;";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    $reg2 = mysql_fetch_assoc($query2);
                    if ($params['operacion'] == '1') {
                        $saldo = ($reg2['saldo'] ?: 0) + $reg['cantidad'];
                    } else {
                        $saldo = ($reg2['saldo'] ?: 0) - $reg['cantidad'];
                    }
                }
                //Procesar movimiento
                $sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idTipoProductos,idProductos," . $operacion . ",saldo,idUsuarios,idEmpresas,created_at)"
                        . "values('" . $params['ingresoA'] . "'," . $params['idPuntoIngreso'] . ",'" . $params['documento'] . "'," . $reg['idTipoProductos'] . "," . $reg['idProductos'] . ",'" . $reg['cantidad'] . "','" . $saldo . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . date("Y-m-d", strtotime($params['fechaOperacion'])) . "');";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if (!$query3) {
                    echo('Error en procesar movimiento: ' . $sql3);
                }
            }
        }
    }

    /** METODO getCompraDetalleUsuario
     * 
     */
    public function generarDescuentoDetalleCompra($params) {
        $response = "";
        $filtros = "";
        $filtros .= "ocd.idUsuarios=" . $params['idUsuarios'] . " and idCompras is null";
        $filtros2 = "idUsuarios=" . $params['idUsuarios'] . " and idCompras is null";
        $sql = "select 
                    ocd.id,
                    ocd.idProductos,
                    p.sku,
                    p.descLarga,
                    ocd.cantidad,
                    ocd.precioCompra,
                    ocd.descuentoM,
                    ocd.descuentoP,
                    ocd.total
                from
                    comprasDetalle as ocd left join productos as p on(ocd.idProductos=p.id)
                where " . $filtros . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $descuento = round(($reg['precioCompra'] * ($params['descuento'] / 100)), 2);
            $newPrecio = round($reg['precioCompra'] - ($descuento), 2);
            $newTotal = $reg['cantidad'] * $newPrecio;
            //echo ($params['descuento']/100) . ' ' . $newPrecio . ' ' . $descuento . ' ' . $newTotal;
            $sql1 = "update comprasDetalle set precioCompra='" . $newPrecio . "',descuentoM='" . $descuento . "',descuentoP='" . $params['descuento'] . "',total='" . $newTotal . "'
                       where idProductos=" . $reg['idProductos'] . " and " . $filtros2 . ";";
            //echo $sql1;
            $query1 = mysql_query($sql1, dbCon::conPrincipal());
            if ($query1 == true) {
                $response[] = array('message' => 'true');
            } else {
                $response[] = array('message' => 'failed');
            }
        }
        return $response;
    }

    /** METODO AGREGAR PRODUCTO A COMPRAS
     *
     */
    public function addItemRequisionCompra($params, $idUsuarios) {
        $response = "";
        $idProducto = $params['idProductos'] ?: 'null';
        $sql = "insert into comprasRequisicionDetalle(idProductos,codigo,descProducto,unidadMedida,cantidad,idUsuarios)"
                . "values(" . $idProducto . ",'" . $params['codigo'] . "','" . strtoupper($params['descProducto']) . "','" . strtoupper($params['unidadMedida']) . "','" . $params['cantidad'] . "'," . $idUsuarios . ");";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'result' => $sql);
        }
        return $response;
    }

    /** METODO getRequisicionDetalle
     * 
     */
    public function getRequisicionDetalle($params) {
        $this->resultado = null;
        $filtros = "";
        if ($params['idRequisicion'] != "") {
            $filtros .= "idComprasRequisicion=" . $params['idRequisicion'] . "";
        } else {
            $filtros .= "idUsuarios=" . $params['idUsuarios'] . " and idComprasRequisicion is null";
        }
        $sql = "select 
                    *
                from
                    comprasRequisicionDetalle
                where " . $filtros . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO AGREGAR ITEM COMPRA
     *
     */
    public function removeItemRequision($params, $idUsuarios) {
        $response = "";
        $filter = "and idComprasRequisicion is null";
        if (isset($params['idRequisicion'])) {
            $filter = "and idComprasRequisicion=" . $params['idRequisicion'] . "";
        }
        $sql = "delete from comprasRequisicionDetalle where id=" . $params['item'] . " and idUsuarios=" . $idUsuarios . " " . $filter . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO guardarRequisicion
     *
     */
    public function guardarRequisicion($params, $idUsuarios, $idEmpresas) {
        $response = "";
        $documento = $params['tipoDocumento'] . "-" . $params['correlativo'];
        $sql = "insert into comprasRequisicion
                values(null,'" . $documento . "','" . $params['solicitadoPor'] . "','" . $params['idHrmDepartamentos'] . "','" . $params['observaciones'] . "','1',null," . $idUsuarios . "," . $idEmpresas . ",'" . $this->timestamp . "')";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $idRequisicion = mysql_insert_id();
            $sql2 = "update comprasRequisicionDetalle set idComprasRequisicion=" . $idRequisicion . " where idComprasRequisicion is null and idUsuarios=" . $idUsuarios . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2) {
                $response[] = array('message' => 'success', 'idRequisicion' => $idRequisicion);
                $admin = new Admin();
                $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $idEmpresas);
                $notificaciones = $admin->notificaciones('REQUISICION DE COMPRAS', '1', $idRequisicion, $idEmpresas);
            } else {
                $response[] = array('message' => 'failed');
            }
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO GET COMPRA
     * 
     */
    public function getRequisicion($idRequisicion) {
        $sql = "select * from vw_comprasRequisicion where id=" . $idRequisicion . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO addItemOrdenCompra
     *
     */
    public function addItemOrdenCompra($params, $idUsuarios) {
        $response = "";
        if ($params['idOrdenCompra']) {
            $sql = "insert into comprasOrdenesDetalle(idComprasOrdenes,idProductos,cantidad,precio,total,idUsuarios)"
                    . "values(" . $params['idOrdenCompra'] . "," . $params['idProductos'] . ",'" . $params['cantidad'] . "','" . $params['precio'] . "','" . $params['total'] . "'," . $idUsuarios . ")";
        } else {
            $sql = "insert into comprasOrdenesDetalle(idProductos,cantidad,precio,total,idUsuarios)"
                    . "values(" . $params['idProductos'] . ",'" . $params['cantidad'] . "','" . $params['precio'] . "','" . $params['total'] . "'," . $idUsuarios . ")";
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'result' => $sql);
        }
        return $response;
    }

    /** METODO getRequisicionDetalle
     * 
     */
    public function getOrdenCompraDetalle($params) {
        $this->resultado = null;
        $filtros = "";
        if ($params['idOrdenCompra']) {
            $filtros .= "idComprasOrdenes=" . $params['idOrdenCompra'] . "";
        } else {
            $filtros .= "ocd.idUsuarios=" . $params['idUsuarios'] . " and ocd.idComprasOrdenes is null";
        }
        $sql = "select 
                    ocd.id,
                    p.id as idProductos,
                    p.sku,
                    p.descLarga,
                    p.idMedidas2,
                    ocd.cantidad,
                    ocd.precio,
                    ocd.total
                from
                    comprasOrdenesDetalle as ocd left join vw_productos as p on(ocd.idProductos=p.id)
                where " . $filtros . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO AGREGAR ITEM COMPRA
     *
     */
    public function removeItemOrdenCompra($params, $idUsuarios) {
        $response = "";
        $filter = "and idComprasOrdenes is null";
        if (isset($params['idComprasOrdenes'])) {
            $filter = "and idComprasOrdenes=" . $params['idRequisicion'] . "";
        }
        $sql = "delete from comprasOrdenesDetalle where id=" . $params['item'] . " and idUsuarios=" . $idUsuarios . " " . $filter . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO guardarRequisicion
     *
     */
    public function guardarOrdenCompra($params) {
        $response = "";
        $documento = $params['tipoDocumento'] . "-" . $params['correlativo'];
        $sql = "insert into comprasOrdenes
                values(null,'" . $documento . "','" . $params['solicitadoPor'] . "'," . $params['idHrmDepartamentos'] . ",'" . $params['observaciones'] . "'," . $params['idTipoOrdenCompra'] . ",'" . date("Y-m-d", strtotime($params['fechaArribo'])) . "','" . $params['tipoCambio'] . "'," . $params['idMonedas'] . ",'" . $params['monto'] . "','" . $params['montoEnLetras'] . "'," . $params['idProveedores'] . ",'1',null,null,'" . $this->timestamp . "',null,null," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",0,0);";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $idOrdenCompra = mysql_insert_id();
            $sql2 = "update comprasOrdenesDetalle set idComprasOrdenes=" . $idOrdenCompra . " where idComprasOrdenes is null and idUsuarios=" . $params['idUsuarios'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2) {
                $response[] = array('message' => 'success', 'idOrdenCompra' => $idOrdenCompra);
                $admin = new Admin();
                $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
                //$notificaciones = $admin->notificaciones('ORDEN DE COMPRAS', '1', $idOrdenCompra, $params['idEmpresas']);
            } else {
                $response[] = array('message' => 'failed 1');
            }
        } else {
            $response[] = array('message' => 'failed 2: ' . $sql);
        }
        return $response;
    }

    /** METODO GET COMPRA
     * 
     */
    public function getOrdenCompra($idOrdenCompra) {
        $sql = "select * from vw_comprasOrdenes where id=" . $idOrdenCompra . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO gestionarOC
     *
     */
    public function gestionarRC($params) {
        $response = "";
        $sql = "update comprasRequisicion set status='" . $params['statusRequisicion'] . "', observacionesStatus='" . $params['observaciones'] . "'"
                . " where id=" . $params['idRequisicion'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO gestionarOC
     *
     */
    public function gestionarOC($params) {
        $response = "";
        $sql = "update comprasOrdenes set status='" . $params['statusOrden'] . "', idAdminUser=" . $params['idAdminUser'] . ", observacionesAuth='" . $params['observaciones'] . "',auth_at='" . $this->timestamp . "'
                where id=" . $params['idOrdenCompra'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO importarDetalleRequisicionaOrden
     *
     */
    public function importarDetalleRequisicionaOrden($params, $idUsuarios) {
        $response = "";
        //PASO 1. Obtener el listado de productos de requisicion
        $sql1 = "select * from comprasRequisicionDetalle where idComprasRequisicion=" . $params['idRequisicion'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $number = mysql_num_rows($query1);
        $i = 1;
        //
        $sql = "insert into comprasOrdenesDetalle(idProductos,cantidad,idUsuarios) values";
        while ($reg = mysql_fetch_assoc($query1)) {
            if ($i !== $number) {
                $sql .= "(" . $reg['idProductos'] . ",'" . $reg['cantidad'] . "'," . $idUsuarios . "),";
            } else {
                $sql .= "(" . $reg['idProductos'] . ",'" . $reg['cantidad'] . "'," . $idUsuarios . ")";
            }
            $i ++;
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->finalizarImportacionRequisicion($params['idRequisicion']);
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO importarDetalleOCaCompras
     *
     */
    public function importarDetalleOCaCompras($params, $idUsuarios) {
        $response = "";
        //PASO 1. Obtener el listado de productos de la orden de compra
        $sql1 = "select * from comprasOrdenesDetalle where idComprasOrdenes=" . $params['idOrdenCompra'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $number = mysql_num_rows($query1);
        $i = 1;
        //
        $sql = "insert into comprasDetalle(idProductos,cantidad,precioCompra,total,idUsuarios,idComprasOrdenes) values";
        while ($reg = mysql_fetch_assoc($query1)) {
            if ($i !== $number) {
                $sql .= "(" . $reg['idProductos'] . ",'" . $reg['cantidad'] . "','" . $reg['precio'] . "','" . $reg['total'] . "'," . $idUsuarios . "," . $params['idOrdenCompra'] . "),";
            } else {
                $sql .= "(" . $reg['idProductos'] . ",'" . $reg['cantidad'] . "','" . $reg['precio'] . "','" . $reg['total'] . "'," . $idUsuarios . "," . $params['idOrdenCompra'] . ")";
            }
            $i ++;
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->finalizarImportacionOC($params['idOrdenCompra']);
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO finalizarImportacionOC
     *
     */
    public function finalizarImportacionRequisicion($idRequisicion) {
        $response = "";
        $sql = "update comprasRequisicion set status='4' where id=" . $idRequisicion . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            error_log('importacion finalizada: ' . $idRequisicion);
        } else {
            error_log('error en importacion oc: ' . $idRequisicion);
        }
        return $response;
    }

    /** METODO finalizarImportacionOC
     *
     */
    public function finalizarImportacionOC($idOrdenCompra) {
        $response = "";
        $sql = "update comprasOrdenes set status='4' where id=" . $idOrdenCompra . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            error_log('importacion finalizada: ' . $idOrdenCompra);
        } else {
            error_log('error en importacion oc: ' . $idOrdenCompra);
        }
        return $response;
    }

    /** METODO gestionarOC
     *
     */
    public function cancelarProcesoCompra($params, $idUsuarios) {
        $response = "";
        $sql = "";
        switch ($params['modulo']) {
            case 'requisicionCompra':
                $sql = "delete from comprasRequisicionDetalle where idComprasRequisicion is null and idUsuarios=" . $idUsuarios . ";";
                break;
            case 'ordenCompra':
                $sql = "delete from comprasOrdenesDetalle where idComprasOrdenes is null and idUsuarios=" . $idUsuarios . ";";
                break;
            case 'compra':
                $sql = "delete from comprasDetalle where idCompras=0 and idUsuarios=" . $idUsuarios . ";";
                break;
            case 'importaciones':
                $sql = "delete from importacionesDetalle where idImportaciones is null and idUsuarios=" . $idUsuarios . ";";
                break;
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO updateItemRequision
     *
     */
    public function updateItemRequision($params, $idUsuarios) {
        $response = "";
        $sql = "update comprasRequisicionDetalle "
                . "set codigo='" . $params['codigo'] . "',descProducto='" . strtoupper($params['descProducto']) . "',unidadMedida='" . strtoupper($params['unidadMedida']) . "',cantidad='" . $params['cantidad'] . "'"
                . " where id=" . $params['item'] . " and idUsuarios=" . $idUsuarios . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'result' => $sql);
        }
        return $response;
    }

    /** METODO updateItemOrdenCompra
     *
     */
    public function updateItemOrdenCompra($params, $idUsuarios) {
        $response = "";
        $filter = "";
        if ($params['idOrdenCompra']) {
            $filter = " and idOrdenCompra=" . $params['idOrdenCompra'] . "";
        }
        $sql = "update comprasOrdenesDetalle set cantidad='" . $params['cantidad'] . "',precio='" . $params['precio'] . "',total='" . $params['total'] . "' "
                . " where id=" . $params['item'] . " and idUsuarios=" . $idUsuarios . " " . $filter . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** METODO updateItemCompra
     *
     */
    public function updateItemCompra($params, $idUsuarios) {
        $response = "";
        $sql = "update comprasDetalle "
                . "set cantidad='" . $params['cantidad'] . "',precioCompra='" . $params['precioCompra'] . "',total='" . $params['total'] . "'"
                . " where id=" . $params['item'] . " and idUsuarios=" . $idUsuarios . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'result' => $sql);
        }
        return $response;
    }

    /** METODO savePartidaAutomatica
     * 
     */
    public function savePartidaAutomatica($params) {
        $admin = new Admin();
        $doc = $admin->getCorrelativoPartidas($params['idEmpresas']);
        $response = "";
        $sql = "insert into partidas (numero,partida_at,descripcion,idTipoOperacionPartida,idUsuarios,idEmpresas,created_at)"
                . " values('" . $doc[0]['correlativo'] . "','" . date("Y-m-d", strtotime($params['partida_at'])) . "','" . $params['concepto'] . "'," . $params['idTipoOperacionPartida'] . "," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "');";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $idPartida = mysql_insert_id();
            $this->savePartidaDetalleAutomatica($idPartida, $params);
            $updateCorrelativo = $admin->updateCorrelativoPartidas($doc[0]['idDocumentos'], $doc[0]['correlativo'], $params['idEmpresas']);
            //
            if ($params['idTipoOperacionPartida'] == 1) {
                $sql3 = "update ventas set idFormatos=" . $params['idFormato'] . ",idPartidas=" . $idPartida . " where id=" . $params['idVenta'] . ";";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if ($query3 == true) {
                    error_log('update ventas idVenta= ' . $params['idVenta'] . '');
                } else {
                    error_log('error update ventas idVenta= ' . $params['idVenta'] . ' Query: ' . $sql3);
                }
            } else {
                $sql3 = "update compras set idFormatos=" . $params['idFormato'] . ",idPartidas=" . $idPartida . " where id=" . $params['idCompras'] . ";";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if ($query3 == true) {
                    error_log('update compras idCompra= ' . $params['idCompras'] . '');
                } else {
                    error_log('error update compras idCompra= ' . $params['idCompras'] . ' Query: ' . $sql3);
                }
            }
            error_log('success create partida');
        } else {
            error_log('error create partida');
        }
    }

    /** METODO partidaDetalle
     * 
     */
    public function savePartidaDetalleAutomatica($idPartida, $params) {
        $conta = new Contabilidad();
        $getFormatoDetalle = $conta->getFormatoDetalle($params['idFormato']);
        $numero = count($getFormatoDetalle);
        $i = 1;
        $sql = "insert into partidasDetalle values";
        foreach ($getFormatoDetalle as $key => $value) {
            $debe = $params[strtolower($value['debe'])];
            $haber = $params[strtolower($value['haber'])];
            if ($i !== $numero) {
                $sql .= "(null," . $idPartida . "," . $value['idNomenclatura'] . ",'" . $debe . "','" . $haber . "'," . $value['idCentrosCosto'] . "),";
            } else {
                $sql .= "(null," . $idPartida . "," . $value['idNomenclatura'] . ",'" . $debe . "','" . $haber . "'," . $value['idCentrosCosto'] . ")";
            }
            $i ++;
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            error_log('insert true detalle partida # ' . $idPartida);
        } else {
            error_log('error sql: ' . $sql);
        }
    }

    /** METODO GUARDAR VENTA
     *
     */
    public function guardarVenta($params) {
        $response = "";
        //VALIDAR SERIE Y CORRELATIVO DE FACTURA
        $sql = "select * from ventas where autorizacion='" . $params['autorizacion'] . "' and serie='" . $params['serie'] . "' and correlativo='" . $params['correlativo'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        if ($reg['id'] === null) {
            $admin = new Admin();
            $saldo = 0.00;
            if ($params['idTipoVenta'] == 2) {
                $saldo = $params['total'];
            }
            $sql = "insert into ventas 
                values(
                    null,
                    '" . $params['autorizacion'] . "',
                    '" . $params['serie'] . "',
                    '" . $params['correlativo'] . "',
                    '" . date("Y-m-d", strtotime($params['fechaEmision'])) . "',
                    '" . date("H:i:s", strtotime($params['horaEmision'])) . "',
                    '" . $params['valorFactura'] . "',
                    '" . $params['subtotal'] . "',
                    '" . $params['descuento'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $params['total'] . "',
                    '0.00',    
                    '" . $saldo . "',
                    '" . $params['iva'] . "',
                    '" . $params['tipoCambio'] . "',
                    '" . $params['totalDolares'] . "',
                    '" . $params['totalEnLetras'] . "',
                    '" . $params['totalEnLetrasDolares'] . "',
                    '" . $params['nit'] . "',
                    '" . addslashes($params['nombre']) . "',
                    '" . addslashes($params['direccion']) . "',
                    '" . $params['idTipoOperacion'] . "',
                    '" . $params['idTipoVenta'] . "',
                    '" . addslashes($params['concepto']) . "',
                    '" . $params['idUsuarios'] . "',
                    '" . $params['idSucursales'] . "',    
                    '" . $params['idEmpresas'] . "',
                    '" . $this->timestamp . "',
                    '0000-00-00 00:00:00',
                    0,
                    0,
                    0,
                    0,
                    null,
                    null,
                    null,
                    '" . $params['idClientes'] . "',"
                    . "null);";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                $idVenta = mysql_insert_id();
                //ACTUALIZAR INVENTARIOS
                $sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
                        . "where idVentas=0 and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if ($query2) {
                    //
                    if (isset($params['idPedido'])) {
                        $this->updateEstadoPedido($params['idPedido'], $idVenta, '3');
                    }
                    //
                    $this->movimientoInventario2($idVenta, $params['correlativo'], 'salida');
                    //GUARDAR PARTIDA AUTOMATICA SI idFormato no se igual a null
                    $params['idVenta'] = $idVenta;
                    $params['partida_at'] = $params['fechaFactura'];
                    $params['idTipoOperacionPartida'] = 1;
                    $params['idTipoDocumento'] = 1;
                    $params['documento'] = ($params['serie'] . '-' . $params['correlativo']);
                    if ($params['idFormato'] !== '0') {
                        $this->savePartidaAutomatica($params);
                    }
                    //INGRESO DE CXC
                    if ($params['idTipoVenta'] == 2) {
                        $this->ventasCredito(($params['idClientes'] ?: 0), $idVenta, $params['documento'], $params['total'], date("Y-m-d", strtotime($params['fechaFactura'])), $params['idUsuarios'], $params['idEmpresas']);
                    }
                    $response[] = array('message' => 'success', 'idVenta' => $idVenta);
                } else {
                    $response[] = array('message' => 'failed step 2');
                }
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed', 'error' => $error, 'query' => $sql);
            }
            return $response;
        } else {
            $response[] = array('message' => 'docExists');
        }
        return $response;
    }

    /** METODO comprasCredito
     *
     */
    public function comprasCredito($idProveedores, $idCompras, $documento, $total, $fecha, $idUsuarios, $idEmpresas) {
        $sql = " insert into cxp(idProveedores,idCompras,idTipoDocumento,idDocumento,facLiquidadas,creditos,created_at,idUsuarios,idEmpresas)"
                . "values(" . $idProveedores . "," . $idCompras . ",1,'" . $documento . "',null,'" . $total . "','" . $fecha . "'," . $idUsuarios . "," . $idEmpresas . ");";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            error_log('compra credito ingresado exitosamente idProveedores:' . $idProveedores);
        } else {
            error_log('error al ingresar compra credito idProveedores: ' . $sql);
        }
    }

    /** METODO ACTUALIZAR COMPRA
     *
     */
    public function actualizarCompra($params) {
        $response = "";
        $sql = "update compras
                set idDocumentosCorrelativos='" . $params['idTipoDocumento'] . "',
                    correlativo='" . $params['correlativo'] . "',
                    idFormatos='" . $params['idFormato'] . "',
                    idProveedores='" . $params['idProveedores'] . "',
                    idTipoOperacion='" . $params['idTipoOperacion'] . "',
                    idTipoCompra='" . $params['idTipoCompra'] . "',
                    conceptoCompra='" . $params['conceptoCompra'] . "',
                    serieFactura='" . $params['serieFactura'] . "',
                    noFactura='" . $params['noFactura'] . "',
                    fechaContabilizacion='" . date("Y-m-d", strtotime($params['fechaContabilizacion'])) . "',
                    fechaFactura='" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                    fechaPago='" . date("Y-m-d", strtotime($params['fechaPago'])) . "',
                    valorFactura='" . $params['valorFactura'] . "',
                    subtotal='" . $params['subtotal'] . "',
                    exento='" . $params['exento'] . "',
                    inguat='" . $params['inguat'] . "',
                    descuentoP='" . $params['descuentoP'] . "',
                    descuentoM='" . $params['descuentoM'] . "',
                    total='" . $params['total'] . "',
                    iva='" . $params['iva'] . "',
                    idSucursales=" . $params['idSucursales'] . "   
                WHERE  
                    id=" . $params['idCompra'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->cargarComprasInventario($params['idCompra'], $params['idBodegas']);
            //ELIMINAR PARTIDA ACTUAL SI PARTIDA AUTOMATICA NO SEA IGUAL A 0
            if ($params['idFormato'] !== '0') {
                $sql2 = "delete from partidas where id=" . $params['idPartida'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if ($query2 == true) {
                    //GUARDAR PARTIDA AUTOMATICA
                    $params['partida_at'] = $params['fechaContabilizacion'];
                    $params['idTipoOperacionPartida'] = 2;
                    $params['concepto'] = $params['conceptoCompra'];
                    $this->savePartidaAutomatica($params);
                    $response[] = array('message' => 'success', 'idCompra' => $params['idCompra']);
                } else {
                    $response[] = array('message' => 'failed2');
                }
            } else {
                $response[] = array('message' => 'success', 'idCompra' => $params['idCompra']);
            }
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO ELIMINAR COMPRA
     *
     */
    public function eliminarCompra($params) {
        $response = "";
        $sql = "delete from compras where id=" . $params['idCompra'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //ELIMINAR PARTIDA ACTUAL
            if ($params['idPartida'] !== '0') {
                $sql2 = "delete from partidas where id=" . $params['idPartida'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
            }
            //ELIMINAR REGISTRO EN CXP SI LA COMPRA ES CREDITO
            if ($params['idTipoVenta'] !== '1') {
                $sql3 = "delete from cxp where idCompras=" . $params['idCompra'] . ";";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
            }
            //LIBERAR ORDENES DE COMPRA LIGADAS A LA COMPRA
            $this->liberarOrdenesCompra($params['idCompra'], 'compras');
            //
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO GET VENTA
     * 
     */
    public function getVenta($idVenta) {
        $sql = "SELECT 
                    a.id,
                    a.nit,
                    a.nombre,
                    a.direccion,
                    a.idTipoOperacion,
                    a.idTipoVenta,
                    a.conceptoVenta,
                    a.serie,
                    a.correlativo,
                    date_format(a.fechaFactura,'%d-%m-%Y') as fechaFactura,
                    if(tipoCambio!='1.0000',totalDolares,total) AS valorFactura,
                    a.subtotal,
                    a.descuento,
                    a.descuentoP,
                    a.total,
                    a.iva,
                    a.idFormatos,
                    b.descripcion AS formato,
                    a.idPartidas,
                    a.idSucursales,
                    a.tipoCambio
                FROM
                    ventas AS a
                        LEFT JOIN
                    formatos AS b ON (a.idFormatos = b.id)
                WHERE
                    a.id =" . $idVenta . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO ACTUALIZAR VENTA
     *
     */
    public function actualizarVenta($params) {
        $response = "";
        $sql = "update ventas
                set serie='" . $params['serie'] . "',
                    correlativo='" . $params['correlativo'] . "',
                    fechaFactura='" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                    valorFactura='" . $params['valorFactura'] . "',
                    subtotal='" . $params['subtotal'] . "',
                    descuento='" . $params['descuento'] . "',
                    descuentoP='" . $params['descuentoP'] . "',
                    total='" . $params['total'] . "',
                    iva='" . $params['iva'] . "',    
                    nit='" . $params['nit'] . "',
                    nombre='" . $params['nombre'] . "',    
                    direccion='" . $params['direccion'] . "',        
                    idTipoOperacion='" . $params['idTipoOperacion'] . "',
                    idTipoVenta='" . $params['idTipoVenta'] . "',        
                    conceptoVenta='" . $params['concepto'] . "',
                    idSucursales=" . $params['idSucursales'] . ",
                    totalDolares='" . $params['totalDolares'] . "',
                    tipoCambio='" . $params['tipoCambio'] . "'
                WHERE  
                    id=" . $params['idVenta'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //ELIMINAR PARTIDA ACTUAL SI PARTIDA AUTOMATICA NO SEA IGUAL A 0
            if ($params['idFormato'] !== '0') {
                $sql2 = "delete from partidas where id=" . $params['idPartida'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if ($query2 == true) {
                    //GUARDAR PARTIDA AUTOMATICA
                    $params['idVenta'] = $params['idVenta'];
                    $params['partida_at'] = $params['fechaFactura'];
                    $params['idTipoOperacionPartida'] = 1;
                    $params['concepto'] = $params['concepto'];
                    $this->savePartidaAutomatica($params);
                    $response[] = array('message' => 'success', 'idVenta' => $params['idVenta']);
                } else {
                    $response[] = array('message' => 'failed2', 'query' => $sql2);
                }
            } else {
                $response[] = array('message' => 'success', 'idVenta' => $params['idVenta']);
            }
        } else {
            $response[] = array('message' => 'failed', 'query' => $sql);
        }
        return $response;
    }

    /** METODO ELIMINAR VENTA
     *
     */
    public function eliminarVenta($params) {
        $response = "";
        $sql = "delete from ventas where id=" . $params['idVenta'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //ELIMINAR PARTIDA SI TIENE PARTIDA ASIGNADA
            if ($params['idPartida'] !== '0') {
                $sql2 = "delete from partidas where id=" . $params['idPartida'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
            }
            //ELIMINAR REGISTRO EN CXC SI LA VENTA ES CREDITO
            if ($params['idTipoVenta'] !== '1') {
                $sql3 = "delete from cxc where idVentas=" . $params['idVenta'] . ";";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
            }
            $this->movimientoInventario($params['idVenta'], $documento, 'ingreso');
            $response[] = array('message' => 'success', 'idVenta' => $params['idVenta']);
        } else {
            $response[] = array('message' => 'failed', 'query: ' . $sql);
        }
        return $response;
    }

    /** METODO getProductos
     * 
     */
    public function getProductos() {
        $this->resultado = null;
        $sql = "select id,concat(sku,' ',descLarga) as producto from vw_productos";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO INGRESO DE SERIE
     *
     */
    public function ingresoSeriesProducto($params) {
        $response = "";
        switch ($params['action']) {
            case 'facturacion':
                //VALIDAR SI SERIE EXISTE
                $sql = "select * from inventarios "
                        . "where idProductos=" . $params['idProducto'] . " and serie='" . $params['serie'] . "' and idEmpresas=" . $params['idEmpresas'] . " order by id desc;";
                $query = mysql_query($sql, dbCon::conPrincipal());
                $reg = mysql_fetch_assoc($query);
                if ($reg['id'] == '' || $reg['saldo'] === '0.0000') {
                    $sql2 = "insert into inventarios (idTipoProductos,idProductos,serie,salida,saldo,idUsuarios,idEmpresas,created_at)
                             values('" . $params['tipoProducto'] . "','" . $params['idProducto'] . "','" . $params['serie'] . "','1','0','" . $params['idUsuarios'] . "','" . $params['idEmpresas'] . "','" . $this->timestamp . "');";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    if ($query2 == true) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed', 'error' => $error);
                    }
                } else {
                    $response[] = array('message' => 'noExists');
                }
                break;
            case 'Negativo':
                //VALIDAR SI SERIE EXISTE
                $sql = "select * from inventarios "
                        . "where idProductos=" . $params['idProducto'] . " and serie='" . $params['serie'] . "' and idEmpresas=" . $params['idEmpresas'] . " order by id desc;";
                $query = mysql_query($sql, dbCon::conPrincipal());
                $reg = mysql_fetch_assoc($query);
                if (!$reg['id'] == '') {
                    $sql2 = "insert into inventarios (idTipoProductos,idProductos,serie,salida,saldo,idUsuarios,idEmpresas,created_at)
                             values('" . $params['tipoProducto'] . "','" . $params['idProducto'] . "','" . $params['serie'] . "','1','0','" . $params['idUsuarios'] . "','" . $params['idEmpresas'] . "','" . $this->timestamp . "');";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    if ($query2 == true) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed', 'error' => $error);
                    }
                } else {
                    $response[] = array('message' => 'noExists');
                }
                break;
            case 'traslados':
                //VALIDAR SI SERIE EXISTE
                $sql = "select * from inventarios "
                        . "where idProductos=" . $params['idProducto'] . " and serie='" . $params['serie'] . "' and idEmpresas=" . $params['idEmpresas'] . " order by id desc;";
                $query = mysql_query($sql, dbCon::conPrincipal());
                $reg = mysql_fetch_assoc($query);
                if ($reg['id'] == '' || $reg['saldo'] === '0.0000') {
                    $sql2 = "insert into inventarios (idTipoProductos,idProductos,serie,salida,saldo,idUsuarios,idEmpresas,created_at)
                             values('" . $params['tipoProducto'] . "','" . $params['idProducto'] . "','" . $params['serie'] . "','1','0','" . $params['idUsuarios'] . "','" . $params['idEmpresas'] . "','" . $this->timestamp . "');";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    if ($query2 == true) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed', 'error' => $error);
                    }
                } else {
                    $response[] = array('message' => 'noExists');
                }
                break;
            default :
                //VALIDAR SI SERIE YA FUE INGRESADA
                $sql = "select * from inventarios "
                        . "where idProductos=" . $params['idProducto'] . " and serie='" . $params['serie'] . "' and idEmpresas=" . $params['idEmpresas'] . " order by id desc;";
                $query = mysql_query($sql, dbCon::conPrincipal());
                $reg = mysql_fetch_assoc($query);
                if ($reg['id'] == '' || $reg['saldo'] === '0.0000') {
                    $sql2 = "insert into inventarios (idTipoProductos,idProductos,serie,ingreso,saldo,idUsuarios,idEmpresas,created_at)
                             values('" . $params['tipoProducto'] . "','" . $params['idProducto'] . "','" . $params['serie'] . "','1','1','" . $params['idUsuarios'] . "','" . $params['idEmpresas'] . "','" . $this->timestamp . "');";
                    $query2 = mysql_query($sql2, dbCon::conPrincipal());
                    if ($query2 == true) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed', 'error' => $error);
                    }
                } else {
                    $response[] = array('message' => 'exists', 'sql' => $sql);
                }
                break;
        }
        return $response;
    }

    /** METODO getInventarioSeries
     * 
     */
    public function getSeriesProducto($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.id,
                    b.sku,
                    b.descLarga,
                    a.serie
                FROM
                    inventarios as a inner join productos as b on(a.idProductos=b.id)
                WHERE
                    a.documento='' 
                    and idProductos=" . $params['idProductos'] . "
                    and a.idUsuarios=" . $params['idUsuarios'] . "
                    and a.idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO AGREGAR PRODUCTO A AJUSTE
     *
     */
    public function addItemAjuste($params, $idUsuarios) {
        $response = "";
        $sql1 = "select idProductos "
                . "from ajustesDetalle where idProductos='" . $params['idProductos'] . "' and idUsuarios=" . $idUsuarios . " and idAjustes is null;";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        $sql = "";
        if ($reg1['idProductos'] == '') {
            $sql = "insert into ajustesDetalle(idProductos,cantidad,idUsuarios)"
                    . "values(" . $params['idProductos'] . ",'" . $params['cantidad'] . "'," . $idUsuarios . ")";
        } else {
            $sql = "update ajustesDetalle set cantidad=(cantidad+" . $params['cantidad'] . ") where idProductos='" . $params['idProductos'] . "' and idUsuarios=" . $idUsuarios . " and idAjustes is null;";
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO getAjusteDetalle
     * 
     */
    public function getAjusteDetalle($params) {
        $this->resultado = null;
        $filtros = "";
        $fields = "";
        if ($params['dbProject'] === 'pos_kasualcosmeticos') {
            $fields = ",item,idMarcas";
        }
        if ($params['idAjuste'] != "") {
            $filtros .= "a.idAjustes=" . $params['idAjuste'] . "";
        } else {
            $filtros .= "a.idUsuarios=" . $params['idUsuarios'] . " and a.idAjustes is null";
        }

        $sql = "SELECT 
                    a.id,
                    p.id AS idProductos,
                    p.sku,
                    p.descLarga,
                    a.cantidad,
                    p.idTipoProductos as tipoProducto
                    " . $fields . "
                FROM
                    ajustesDetalle AS a
                        LEFT JOIN
                    vw_productos AS p ON (a.idProductos = p.id)
                where " . $filtros . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO removeItemAjuste
     *
     */
    public function removeItemAjuste($params, $idUsuarios) {
        $response = "";
        $sql = "delete from ajustesDetalle where id=" . $params['item'] . " and idUsuarios=" . $idUsuarios . " and idAjustes is null;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO AGREGAR ITEM COMPRA
     *
     */
    public function removeItemCompra($params) {
        $response = "";
        $sql = "delete from comprasDetalle where id=" . $params['item'] . " and idProductos=" . $params['idProductos'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            if ($params['idCompra'] != "") {
                $sql2 = "delete from inventarios where idProductos=" . $params['idProductos'] . " and documento='" . $params['documento'] . "';";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if ($query2 == true) {
                    $response[] = array('message' => 'success');
                } else {
                    $response[] = array('message' => 'failed');
                }
            } else {
                $response[] = array('message' => 'success');
            }
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /* METODO confirmarPedido
     * 
     */

    public function confirmarPedido($params) {
        //Obtener la bodega de pedidos de la tabla facturacionConf
        $sqlP = "SELECT 
                    ingresoA, idPuntoIngreso
                FROM
                    facturacionConf
                WHERE
                    opcion = 'pedidos'
                        AND idEmpresas = " . $params['idEmpresas'] . ";";
        $queryP = mysql_query($sqlP, dbCon::conPrincipal());
        $regP = mysql_fetch_assoc($queryP);
        //Obtener la sucursal de facturacion de la tabla facturacionConf
        $sql1 = "SELECT 
                    ingresoA, idPuntoIngreso
                FROM
                    facturacionConf
                WHERE
                    opcion = 'facturacion'
                        AND idEmpresas = " . $params['idEmpresas'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        //
        $response = "";
        $sql = "SELECT
                    h.documento,
                    if(d.sku is not null,d.id,b.id) as idProductos,
                    if(d.sku is not null,d.sku,b.sku) as codigo,
                    upper(if(d.sku is not null,d.descLarga,b.descLarga)) as producto,
                    if(c.unidades is null,a.cantidad,(a.cantidad*c.unidades))  as cantidad,
                    (select saldo from inventarios where idEmpresas = " . $params['idEmpresas'] . " and ingresoA=" . $regP['ingresoA'] . " and idPuntoIngreso=" . $regP['idPuntoIngreso'] . " and idProductos=if(d.sku is not null,d.id,b.id) order by id desc limit 1) as existencia
                FROM
                    pedidosDetalle as a left join productos as b on(a.idProductos=b.id)
                    left join productosComponentes as c on(a.idProductos=c.idProductoPrincipal)
                    left join productos as d on(c.idProductos=d.id)
                    left join familiaNivel1 as f1 on(if(d.sku is not null,d.idFamiliaNivel1=f1.id,b.idFamiliaNivel1=f1.id))
                    left join familiaNivel2 as f2 on(if(d.sku is not null,d.idFamiliaNivel2=f2.id,b.idFamiliaNivel2=f2.id))
                    left join familiaNivel3 as f3 on(if(d.sku is not null,d.idFamiliaNivel3=f3.id,b.idFamiliaNivel3=f3.id))
                    left join medidas as g on(if(d.sku is not null,d.idMedidas2=g.id,d.idMedidas2=g.id))
                    inner join pedidos as h on(a.idPedidos=h.id)
                    inner join tipoVenta as i on(h.idTipoVenta=i.id)
                    inner join usuarios as j on(h.idUsuarios=j.id)
                    inner join empresas as k on(h.idEmpresas=k.id)
                    left join clientes as l on(h.nit=l.nitC)
                WHERE
                    idPedidos = " . $params['idPedido'] . "
                group by codigo;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $flag = false;
        //VALIDAR EXISTENCIAS
        while ($reg = mysql_fetch_assoc($query)) {
            if (intval($reg['existencia']) < 1) {
                $flag = true;
            } else if (intval($reg['existencia']) < intval($reg['cantidad'])) {
                $flag = true;
            }
        }
        if ($flag == true) {
            $response[] = array('message' => 'sinExistencias');
        } else {
            $reg = mysql_fetch_assoc($query);
            $query22 = mysql_query($sql, dbCon::conPrincipal());
            while ($reg22 = mysql_fetch_assoc($query22)) {
                $this->addItemTraslado($reg22, $params['idUsuarios']);
            }
            $admin = new Admin();
            $param['idDocumento'] = 24;
            $correlativo = $admin->getDocumentosCorrelativo($param);
            $params['tipoDocumento'] = 'PEDIDO-TRASLADO';
            $params['correlativo'] = $correlativo[0]['correlativo'];
            $params['salidaDe'] = $regP['ingresoA'];
            $params['idPuntoSalida'] = $regP['idPuntoIngreso'];
            $params['ingresoA'] = $reg1['ingresoA'];
            $params['idPuntoIngreso'] = $reg1['idPuntoIngreso'];
            $this->finalizarTraslado($params, $params['idUsuarios'], $params['idEmpresas']);
            //
            $sql2 = "select max(id) as idTraslado from traslados where idUsuarios=" . $params['idUsuarios'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            $reg2 = mysql_fetch_assoc($query2);
            //
            $params2['idTraslado'] = $reg2['idTraslado'];
            $params2['observaciones'] = 'TRASLADO POR CONFIRMACION DE PEDIDO: ' . $reg['documento'] . '';
            $this->ingresarTraslado($params2, $params['idUsuarios'], $params['idEmpresas'], $params['dbProject']);
            //
            $sql4 = "update pedidos set estado='4' where id=" . $params['idPedido'] . ";";
            $query4 = mysql_query($sql4, dbCon::conPrincipal());
            if ($query4 == true) {
                $response[] = array('message' => 'success');
            } else {
                $response[] = array('message' => 'failed', 'Query' => $sql);
            }
            return $response;
        }
        return $response;
    }

    /** METODO AGREGAR PRODUCTO A COMPRAS
     *
     */
    public function addItemImportacion($params, $idUsuarios) {
        $response = "";
        $sql = "insert into importacionesDetalle(ingresoA,idPuntoIngreso,idProductos,cantidad,peso,arancel,precio,precioImportacion,total,totalImportacion,idUsuarios)"
                . "values('" . $params['ingresoA'] . "','" . $params['idPuntoIngreso'] . "'," . $params['idProductos'] . ",'" . $params['cantidad'] . "','" . $params['peso'] . "','" . $params['arancel'] . "','" . $params['precio'] . "','" . $params['precioImport'] . "','" . $params['total'] . "','" . $params['totalImport'] . "'," . $idUsuarios . ");";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed', "error" => $error);
        }
        return $response;
    }

    /** METODO getCompraDetalle
     * 
     */
    public function getImportacionesDetalle($params) {
        $this->resultado = null;
        $filtros = "";
        if (isset($params['idImportacion'])) {
            $filtros .= "idImportaciones=" . $params['idImportacion'] . "";
        } else {
            $filtros .= "a.idUsuarios=" . $params['idUsuarios'] . " and a.idImportaciones is null";
        }
        $sql = "select 
                    a.id,
                    b.sku,
                    b.descLarga,
                    a.cantidad,
                    a.arancel,
                    a.precio,
                    a.precioImportacion,
                    a.total,
                    a.totalImportacion,
                    a.idProductos,
                    a.peso,
                    a.ingresoA,
                    a.idPuntoIngreso
                from
                    importacionesDetalle as a
                        left join
                    vw_productos as b ON (a.idProductos = b.id)
                where " . $filtros . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** addGastoImportacion
     *
     */
    public function addGastoImportacion($params, $idUsuarios) {
        $response = "";
        $sql = "insert into importacionesGastos(idTipoDocumentoGasto,nit,proveedor,idTipoGasto,fechaFactura,serieFactura,noFactura,idMoneda,valor,subtotal,iva,total,idUsuarios)"
                . "values('" . $params['idTipoDocumentoGasto'] . "','" . $params['nit'] . "','" . $params['proveedor'] . "'," . $params['tipoGasto'] . ",'" . date("Y-m-d", strtotime($params['fechaFactura'])) . "','" . $params['serieFactura'] . "','" . $params['noFactura'] . "','" . $params['idMoneda'] . "','" . $params['valorFinal'] . "','" . $params['subtotal'] . "','" . $params['iva'] . "','" . $params['valorFinal'] . "','" . $idUsuarios . "');";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed', "error" => $error);
        }
        return $response;
    }

    /** METODO getImportacionesGastos
     * 
     */
    public function getImportacionesGastos($params) {
        $this->resultado = null;
        $filtros = "";
        if ($params['idImportacion'] != "") {
            $filtros .= "idImportaciones=" . $params['idImportacion'] . "";
        } else {
            $filtros .= "idUsuarios=" . $params['idUsuarios'] . " and idImportaciones is null";
        }
        $sql = "select 
                    *
                from
                    importacionesGastos
                where 
                    " . $filtros . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** removeGastoImportacion
     *
     */
    public function removeGastoImportacion($params, $idUsuarios) {
        $response = "";
        $sql = "delete from importacionesGastos where id=" . $params['item'] . " and idUsuarios=" . $idUsuarios . " and idImportaciones is null;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO guardarImportacion
     *
     */
    public function guardarImportacion($params) {
        $admin = new Admin();
        $response = "";
        $documento = $params['tipoDocumento'] . "-" . $params['correlativo'];
        $saldo = 0;
        if ($params['idTipoImportacion'] == 2) {
            $saldo = $params['total'];
        }
        $sql = "insert into importaciones
                values(
                    null,
                    '" . $params['idTipoDocumento'] . "',
                    '" . $params['correlativo'] . "',
                    '" . $params['idProveedores'] . "',
                    '" . $params['idTipoOperacion'] . "',
                    '" . $params['idTipoImportacion'] . "',
                    '" . $params['idTipoProrrateo'] . "',
                    '" . $params['conceptoImportacion'] . "',
                    '" . $params['serieFactura'] . "',
                    '" . $params['noFactura'] . "',
                    '" . date("Y-m-d", strtotime($params['fechaContabilizacion'])) . "',
                    '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                    '" . date("Y-m-d", strtotime($params['fechaPago'])) . "',
                    '" . $params['tipoCambio'] . "',
                    '" . $params['valorFacturaImportacion'] . "',
                    '" . $params['valorGastosImportacion'] . "',
                    '" . $params['valorTotalImportacion'] . "',
                    '" . $params['descuento'] . "',
                    '" . $params['valorFacturaTipoCambio'] . "',
                    '" . $params['subtotal'] . "',
                    '" . $params['iva'] . "',
                    '" . $params['total'] . "',
                    '" . $saldo . "',
                    '" . $params['cantidadItems'] . "',
                    '" . $params['fi'] . "',
                    '" . $params['fa'] . "',
                    '" . $params['fp'] . "',
                    '" . $params['idUsuarios'] . "',
                    '" . $params['idSucursales'] . "',
                    '" . $params['idEmpresas'] . "',
                    '" . $this->timestamp . "',
                    null,    
                    '" . $params['idFormato'] . "',
                    null    
                );";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //CARGA DE ITEMS DE COMPRA A INVENTARIOS
            $idImportacion = mysql_insert_id();
            $sql2 = "update importacionesDetalle set idImportaciones=" . $idImportacion . " where idImportaciones is null and idUsuarios=" . $params['idUsuarios'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2) {
                $sql3 = "update importacionesGastos set idImportaciones=" . $idImportacion . " where idImportaciones is null and idUsuarios=" . $params['idUsuarios'] . ";";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if ($query3) {
                    $factor = 0;
                    switch ($params['idTipoProrrateo']) {
                        case '1':
                            $factor = $params['fi'];
                            break;
                        case '2':
                            $factor = $params['fa'];
                            break;
                        case '3':
                            $factor = $params['fp'];
                            break;
                    }
                    //$this->cargarImportacionesInventario($idImportacion, $factor, $params['tipoCambio'], $documento, date("Y-m-d", strtotime($params['fechaFactura'])), $params['idEmpresas'], $params['idUsuarios']);
                    $this->cargarImportacionesInventario($idImportacion, $factor, $documento, date("Y-m-d", strtotime($params['fechaFactura'])), $params['idEmpresas'], $params['idUsuarios']);
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed3', 'error' => $error);
                }
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed2', 'error' => $error);
            }
            //GUARDAR PARTIDA AUTOMATICA
            if ($params['idFormato'] !== '') {
                $params['partida_at'] = $params['fechaContabilizacion'];
                $params['idTipoOperacionPartida'] = 2;
                $params['concepto'] = $params['conceptoCompra'];
                $this->savePartidaAutomatica($params);
            }
            //ACTUALIZA CORRELATIVO COMPRA
            $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
            //ACTUALIZA ORDEN DE COMPRA
            if ($params['idOrdenCompra']) {
                $this->cerrarOrdenCompra($idImportacion, $params['idOrdenCompra'], 'importaciones');
            }
            $response[] = array('message' => 'success', 'idImportacion' => $idImportacion);
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed1', 'error' => $error);
        }
        return $response;
    }

    /** METODO LISTADO COMPRAS
     *
     */
    public function cargarImportacionesInventario($idImportaciones, $factor, $documento, $fechaCompra, $idEmpresas, $idUsuarios) {
        //PROCESOS
        //Obtiene los productos de la compra
        $sql = "SELECT
                    a.idProductos,
                    (b.equivalente * a.cantidad) AS existCompra,
                    ROUND(((a.precio + " . $factor . ") / 1.12) / b.equivalente, 2) AS costoUnitarioCompra,
                    ROUND(((b.equivalente * a.cantidad) * ROUND(((a.precio+ " . $factor . ") / 1.12) / b.equivalente, 2)),2) AS costoCompra,
                    b.idTipoProductos,
                    b.idUtilizaSerie,
                    ingresoA,
                    idPuntoIngreso
                FROM
                    importacionesDetalle as a
                    inner join productos as b on(a.idProductos=b.id)
                WHERE
                    a.idImportaciones =" . $idImportaciones . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            //Obtiene el saldo actual de cada producto
            $sql2 = "SELECT 
                        a.saldo,
                        b.precioCosto as costoUnitarioInv,
                        (a.saldo*b.precioCosto) as costoExistActual
                    FROM
                        inventarios as a inner join productos as b on(a.idProductos=b.id)
                    WHERE
                        a.idProductos = " . $reg['idProductos'] . " 
                        AND a.ingresoA = " . $reg['ingresoA'] . "
                        AND a.idPuntoIngreso = " . $reg['idPuntoIngreso'] . "
                    ORDER BY a.id DESC
                    LIMIT 1";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            $reg2 = mysql_fetch_assoc($query2);
            $saldo = ($reg2['saldo'] ?: 0);
            //Calcula costo promedio
            $costoPromedio = (($saldo + $reg['existCompra']) / ($reg['costoCompra'] + $reg2['costoExistActual']));
            //Actualiza nuevo costo en tabla productos
            $sql3 = "update productos set precioCosto='" . $costoPromedio . "' where id=" . $reg['idProductos'] . ";";
            $query3 = mysql_query($sql3, dbCon::conPrincipal());
            if ($query3) {
                //Insertar bitacora de costos
                $sql4 = "insert into productosCostos "
                        . "values(null,'" . $documento . "'," . $reg['idProductos'] . ",'" . $saldo . "','" . $reg2['costoUnitarioInv'] . "','" . $reg2['costoExistActual'] . "','" . $reg['existCompra'] . "','" . $reg['costoUnitarioCompra'] . "','" . $reg['costoCompra'] . "','" . $costoPromedio . "','" . $fechaCompra . "'," . $reg['idPuntoIngreso'] . "," . $idEmpresas . ");";
                $query4 = mysql_query($sql4, dbCon::conPrincipal());
                if ($query4) {
                    //Insertar existencias a inventario
                    $sql5 = "";
                    if ($reg['idUtilizaSerie'] == '1') {
                        $sql5 = "update inventarios set ingresoA='" . $reg['ingresoA'] . "', idPuntoIngreso=" . $reg['idPuntoIngreso'] . ", documento='" . $documento . "', idTipoProductos=" . $reg['idTipoProductos'] . "
                                where documento is null and idProductos=" . $reg['idProductos'] . " and idUsuarios=" . $idUsuarios . " and idEmpresas=" . $idEmpresas . ";";
                    } else {
                        $sql5 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos,ingreso,saldo,idUsuarios,idEmpresas)"
                                . "values('" . $reg['ingresoA'] . "'," . $reg['idPuntoIngreso'] . ",'COM " . $documento . "'," . $reg['idProductos'] . ",'" . $reg['existCompra'] . "','" . ($saldo + $reg['existCompra']) . "'," . $idUsuarios . "," . $idEmpresas . ");";
                    }
                    $query5 = mysql_query($sql5, dbCon::conPrincipal());
                    if ($query5) {
                        //Actualiza nuevo costo en productos fabricados que tengan como componente el producto
                        $sql6 = "update productosComponentes set costo='" . $costoPromedio . "' where idProductos=" . $reg['idProductos'] . ";";
                        $query6 = mysql_query($sql6, dbCon::conPrincipal());
                        //Ingreso de series
                        $sql7 = "update inventarios set ingresoA=" . $reg['ingresoA'] . ", idPuntoIngreso=" . $reg['idPuntoIngreso'] . ",documento='" . $documento . "'
                                where documento is null and idEmpresas=" . $idEmpresas . ";";
                        $query7 = mysql_query($sql7, dbCon::conPrincipal());
                        //
                        error_log('success all steps');
                    } else {
                        error_log('failed step 5 ' . $sql5);
                    }
                } else {
                    error_log('failed step 4 ' . $sql4);
                }
            } else {
                error_log('failed step 3 ' . $sql3);
            }
        }
    }

    /** removeItemImportacion
     *
     */
    public function removeItemImportacion($params) {
        $response = "";
        $sql = "delete from importacionesDetalle where id=" . $params['item'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** updateItemImportacion
     *
     */
    public function updateItemImportacion($params) {
        $response = "";
        $sql = "update importacionesDetalle set cantidad='" . $params['cantidad'] . "',peso='" . $params['peso'] . "',arancel='" . $params['arancel'] . "',precio='" . $params['precio'] . "',total='" . $params['total'] . "'"
                . "where id=" . $params['item'] . " and idProductos=" . $params['idProductos'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO cancelarPedido
     *
     */
    public function updateEstadoPedido($idPedido, $idVenta, $estado) {
        $response = "";
        $sql = "";
        switch ($estado) {
            case '1':
                $sql = "update pedidos set estado='" . $estado . "',idVentas=NULL where idVentas=" . $idVenta . ";";
                break;
            case '3':
                $sql = "update pedidos set estado='" . $estado . "',idVentas=" . $idVenta . " where id=" . $idPedido . ";";
                break;
            case '4':
                $sql = "update pedidos set estado='" . $estado . "',idVentas=NULL where idVentas=" . $idVenta . ";";
                break;
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            error_log(array('message' => 'success'));
        } else {
            error_log(array('message' => 'failed', 'Query' => $sql));
        }
        return $response;
    }

    /** METODO ventasCredito
     *
     */
    public function ventasCredito($idClientes, $idVentas, $documento, $total, $fecha, $idUsuarios, $idEmpresas) {
        $saldo = 0;
        // OBTENER EL SALDO DE LA ULTIMA FACTURA
        $sql = "select saldo from cxc where idClientes=" . $idClientes . " order by id desc limit 1;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        if ($reg['saldo'] != '') {
            $saldo = $reg['saldo'];
        }
        $newSaldo = ($total + $saldo);
        $sql2 = "insert into cxc "
                . "values(null," . $idClientes . "," . $idVentas . ",'1','" . $documento . "','-','" . $total . "','0.00','" . $newSaldo . "','" . $fecha . "','" . $idUsuarios . "'," . $idEmpresas . ");";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2 == true) {
            error_log('venta credito ingresado exitosamente idCliente:' . $idClientes);
        } else {
            $error = mysql_error();
            error_log('error al ingresar venta credito idCliente: ' . $sql2);
        }
    }

    /** METODO ELIMINAR IMPORTACION
     *
     */
    public function eliminarImportacion($params) {
        //REVERTIR INVENTARIO INGRESADO DESDE LA IMPORTACION
        $sql1 = "delete from inventarios where id in(SELECT a.id FROM inventarios as a inner join importaciones as b on(a.documento=concat('IMPORTACIONES-',b.correlativo)) where b.id=5);";
        //REVERTIR COSTO DE PRODUCTOS
        //ELIMINAR REGISTROS DE PRODUCTOS COSTOS
        //UPDATE campo idImportaciones a null DE TABLE importacionesGastos
        //UPDATE campo idImportaciones a null DE TABLE importacionesDetalle
        //ELIMINAR REGISTRO DE TABLA importaciones
    }

    /** METODO ELIMINAR ORDEN DE COMPRA
     *
     */
    public function eliminarOrdenCompra($params) {
        $response = "";
        $sql = "DELETE FROM compras WHERE id=" . $params['idCompra'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //ELIMINAR PARTIDA ACTUAL
            if ($params['idPartida'] !== '0') {
                $sql2 = "delete from partidas where id=" . $params['idPartida'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if ($query2 == true) {
                    $response[] = array('message' => 'success', 'idCompra' => $params['idCompra']);
                } else {
                    $response[] = array('message' => 'failed2');
                }
            } else {
                $response[] = array('message' => 'success', 'idCompra' => $params['idCompra']);
            }
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO getProductosCodigosAlternos
     * 
     */
    public function getProductosCodigosAlternos($idProductos, $idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.id,
                    a.sku,
                    DATE_FORMAT(a.created_at, '%d-%m-%Y %H:%i:%s') AS created_at,
                    DATE_FORMAT(a.updated_at, '%d-%m-%Y %H:%i:%s') AS updated_at,
                    b.sku AS skuPrincipal,
                    b.descLarga
                FROM
                    productosCodigosAlternos AS a
                        INNER JOIN
                    productos AS b ON (a.idProductos = b.id)
                WHERE
                    a.idProductos = " . $idProductos . " AND a.idEmpresas = " . $idEmpresas . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO saveCodigoAlterno
     * 
     */
    public function saveCodigoAlterno($params) {
        //validar que codigo ya exista asociado al producto
        $sql1 = "select * from productosCodigosAlternos where sku='" . $params['skuNuevo'] . "' and idProductos=" . $params['idProductos'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        if ($reg1['id']) {
            $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Codigo ingresado ya esta asociado al producto</div>";
            return $message;
        } else {
            $sql = "insert into productosCodigosAlternos values(null," . $params['idProductos'] . ",'" . $params['skuNuevo'] . "'," . $params['idEmpresas'] . ",'" . $this->timestamp . "',null);";
            //echo $sql."<br/>";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Producto agregado exitosamente</div>";
                return $message;
            } else {
                $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Error: Al agregar producto QUERY " . $sql . "</div>";
                return $message;
            }
        }
    }

    /** METODO updateCodigoAlterno
     * 
     */
    public function updateCodigoAlterno($params) {
        //validar que codigo ya exista asociado al producto
        $sql1 = "select * from productosCodigosAlternos where sku='" . $params['sku'] . "' and idProductos=" . $params['idProductos'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        if ($reg1['id']) {
            $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Codigo ingresado ya esta asociado al producto</div>";
            return $message;
        } else {
            $sql = "update productosCodigosAlternos set sku='" . $params['sku'] . "',updated_at='" . $this->timestamp . "' where id=" . $params['item'] . ";";
            //echo $sql."<br/>";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Codigo actualizado exitosamente</div>";
                return $message;
            } else {
                $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Error: Al agregar producto QUERY " . $sql . "</div>";
                return $message;
            }
        }
    }

    /** METODO updateCodigoAlterno
     * 
     */
    public function deleteCodigoAlterno($params) {
        $sql = "delete from productosCodigosAlternos where id=" . $params['item'] . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Codigo eliminado exitosamente</div>";
            return $message;
        } else {
            $message = "<div class='alert alert-danger' role='alert' id='message' style='float:right;'>Error: Al agregar producto QUERY " . $sql . "</div>";
            return $message;
        }
    }

    /** METODO actualizarOrdenCompra
     *
     */
    public function actualizarOrdenCompra($params) {
        $response = "";
        $sql2 = "update comprasOrdenes set observaciones='" . $params['observaciones'] . "',arribo_at='" . date("Y-m-d", strtotime($params['fechaArribo'])) . "',tipoCambio='" . $params['tipoCambio'] . "',idMonedas=" . $params['idMonedas'] . ",monto='" . $params['monto'] . "',montoEnLetras='" . $params['montoEnLetras'] . "',updated_at='" . $this->timestamp . "'"
                . " where id=" . $params['idOrdenCompra'] . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2) {
            $response[] = array('message' => 'success', 'idOrdenCompra' => $params['idOrdenCompra']);
        } else {
            $response[] = array('message' => 'failed 1');
        }
        return $response;
    }

    /** METODO GET COMPRA
     * 
     */
    public function getOrdenCompraImport($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.documento,
                    b.id as idProveedores,
                    b.nitP,
                    b.descripcion as proveedor,
                    b.direccionP,
                    b.diasCredito,
                    a.observaciones,
                    b.idPequenoContribuyente,
                    a.tipoCambio,
                    a.idTipoOrdenCompra
                FROM
                    comprasOrdenes as a inner join proveedores as b on(a.idProveedores=b.id)
                WHERE
                    a.id =" . $params['idOrdenCompra'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO cerrarOrdenCompra
     *
     */
    public function cerrarOrdenCompra($idDocumento, $idOrdenCompra, $modulo) {
        foreach ($idOrdenCompra as $key => $value) {
            $sql = "";
            if ($modulo == 'importaciones') {
                $sql = "update comprasOrdenes set idImportaciones=" . $idDocumento . ",status='4',updated_at='" . $this->timestamp . "' where id=" . $value['ordenCompra'] . ";";
            } else {
                $sql = "update comprasOrdenes set idCompras=" . $idDocumento . ",status='4',updated_at='" . $this->timestamp . "' where id=" . $value['ordenCompra'] . ";";
            }
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('orden de compra cerrada exitosamente idOrdenCompra: ' . $value['ordenCompra']);
            } else {
                error_log('error al cerrar orden de compra query: ' . $sql);
            }
        }
    }

    /** METODO liberarOrdenesCompra
     *
     */
    public function liberarOrdenesCompra($idDocumento, $modulo) {
        $sql = "";
        if ($modulo == 'importaciones') {
            $sql = "update comprasOrdenes set idImportaciones=0,status='3',updated_at='" . $this->timestamp . "' where idImportaciones=" . $idDocumento . ";";
        } else {
            $sql = "update comprasOrdenes set idCompras=0 ,status='3',updated_at='" . $this->timestamp . "' where idCompras=" . $idDocumento . ";";
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            error_log('ordenes de compra ligadas a ' . $modulo . ' idDocumento: ' . $idDocumento . ' liberadas exitosamente');
        } else {
            error_log('error al liberar ordenes de compra ligadas a ' . $modulo . ' idDocumento: ' . $idDocumento . ' query: ' . $sql);
        }
    }

    /** METODO GET MEDIDAS
     * 
     */
    public function getMedidas($idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    medidas
                WHERE
                    idEmpresas=" . $idEmpresas . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getProductosEquivalencias
     * 
     */
    public function getProductosEquivalencias($idProductos, $idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.id,
                    a.idProductos,
                    b.sku,
                    a.idMedidas,
                    c.descripcion as medida,
                    a.equivalente,
                    costo,
                    a.idEmpresas,
                        date_format(a.created_at,'%d-%m-%Y %H:%m:%s') as created_at,
                    date_format(a.updated_at,'%d-%m-%Y %H:%m:%s') as updated_at
                FROM
                    productosEquivalencias as a
                    inner join productos as b on(a.idProductos=b.id)
                    inner join medidas as c on(a.idMedidas=c.id)
                    inner join empresas as d on(a.idEmpresas=d.id)
                WHERE
                    a.idEmpresas=" . $idEmpresas . " and a.idProductos=" . $idProductos . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /**
     * actualizarMontosCajaChica
     */
    public function actualizarMontosCajaChica($idCajaChica, $monto) {
        $response = "";
        $sql = "update cajaChica set montoLiquidado=(montoLiquidado+" . $monto . "),montoSinLiquidar=(montoSinLiquidar-" . $monto . "),updated_at='" . $this->timestamp . "' where id=" . $idCajaChica . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query) {
            error_log('montos actualizados exitosamente a idCajaChica: ' . $idCajaChica);
        } else {
            error_log('error al actualizar montos a idCajaChica: ' . $idCajaChica . ' query: ' . $sql);
        }
        return $response;
    }

    /** METODO getFamiliaNivel1
     * 
     */
    public function getFamiliaNivel1($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    familiaNivel1
                WHERE
                    idEmpresas = " . $params['idEmpresas'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getFamiliaNivel2
     * 
     */
    public function getFamiliaNivel2($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    familiaNivel2
                WHERE
                    idFamiliaNivel1=" . $params['idFamiliaNivel1'] . " and idEmpresas = " . $params['idEmpresas'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getFamiliaNivel3
     * 
     */
    public function getFamiliaNivel3($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    familiaNivel3
                WHERE
                    idFamiliaNivel2=" . $params['idFamiliaNivel1'] . " and idEmpresas = " . $params['idEmpresas'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** updateImportacionesDetalle
     *
     */
    public function updateImportacionesDetalle($params, $idUsuarios) {
        $response = "";
        $filtros = "";
        if (isset($params['idImportacion'])) {
            $filtros .= "idImportaciones=" . $params['idImportacion'] . "";
        } else {
            $filtros .= "idUsuarios=" . $idUsuarios . " and idImportaciones is null";
        }
        $sql = "update importacionesDetalle set precio=(precioImportacion*" . $params['tipoCambio'] . "),total=((precioImportacion*" . $params['tipoCambio'] . ")*cantidad) where " . $filtros . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed', "error" => $error);
        }
        return $response;
    }

    /** METODO GET IMPORTACION
     * 
     */
    public function getImportacion($idImportacion) {
        $sql = "SELECT 
                    b.nitP,
                    b.descripcion as nombreProveedor,
                    b.direccionP,
                    b.diasCredito,
                    a.idTipoOperacion,
                    a.idTipoImportacion,
                    a.idTipoProrrateo,
                    a.conceptoImportacion,
                    a.idSucursales,
                    a.idDocumentosCorrelativos,
                    a.correlativo,
                    a.serieFactura,
                    a.noFactura,
                    date_format(a.fechaContabilizacion,'%d-%m-%Y') as fechaContabilizacion,
                    date_format(a.fechaFactura,'%d-%m-%Y') as fechaFactura,
                    date_format(a.fechaPago,'%d-%m-%Y') as fechaPago,
                    a.tipoCambio,
                    a.valorFacturaImportacion,
                    a.valorGastosImportacion,
                    a.valorTotalImportacion,
                    a.descuento
                FROM
                    importaciones AS a
                        INNER JOIN
                    proveedores AS b ON (a.idProveedores = b.id)
                WHERE
                    a.id =" . $idImportacion . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO GET IMPORTACION
     * 
     */
    public function getInventario($idProductos) {
        $this->resultado = null;
        $sql = "SELECT
                    id,
                    idProductos,
                    ingreso,
                    salida
                FROM
                    inventarios
                WHERE
                    idProductos=" . $idProductos . " 
                order by 
                    date(created_at) asc;";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    public function updateSaldosInventario($sql) {
        $response = "";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success query ' . $sqls);
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed', "error" => $error);
        }
        return $response;
    }

    /** METODO getProductosMateriaPrima
     * 
     */
    public function getProductosMateriaPrima($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    sku, descLarga
                FROM
                    productos
                        INNER JOIN
                    inventarios ON (productos.id = inventarios.idProductos)
                WHERE
                    productos.idEmpresas = " . $params['idEmpresas'] . "
                        AND productos.idTipoProductos = 4
                        AND DATE(inventarios.created_at) BETWEEN '20200127' AND '20200129'
                GROUP BY inventarios.idProductos;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getCompraDetalle
     * 
     */
    public function getConsumoMateriaPrima($params) {
        $this->resultado = null;
        $sql = "SELECT
                    DATE(inventarios.created_at) as fecha,
                    if(ingreso!='0.00','INGRESO','SALIDA') as tipoMovimiento,
                    productos.descLarga,
                    if(ingreso!='0.00',ingreso,salida) as movimiento
                FROM
                    inventarios
                    inner join productos on(inventarios.idProductos=productos.id)
                WHERE
                    inventarios.idEmpresas = " . $params['idEmpresas'] . "
                    AND inventarios.idTipoProductos = 4
                    AND DATE(inventarios.created_at) BETWEEN '20200101' AND '20200129' and documento!='CREACION PRODUCTO'
                order by  DATE(inventarios.created_at) asc;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

}
