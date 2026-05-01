<?php

/**
 * POS /Modulo Inventarios - Class Inventarios
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
require_once ("dbCon.php");
require_once ("general.php");

class Facturas extends General {

    /** GET INFO EMPRESA BY SUCURSAL
     * 
     */
    public function getEmpresa($idSucursal) {
        $sql = "SELECT 
                    empresas.*
                FROM
                    sucursales inner join empresas on(sucursales.idEmpresas=empresas.id)
                where
                    sucursales.id=" . $idSucursal . "
                group by empresas.id;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** GET INFO VENTA
     * 
     */
    public function getVenta($idVenta) {
        $sql = "SELECT 
                    *
                FROM
                    ventas
                WHERE id=" . $idVenta . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }
    
    /** GET INFO PEDIDO
     * 
     */
    public function getPedido($idPedido) {
        $sql = "SELECT 
                    pedidos.*,
                    usuarios.userName as realizadoPor,
                    date_format(pedidos.created_at,'%d-%m-%Y %H:%i:%s') as fechaPedido
                FROM
                    pedidos inner join usuarios on(pedidos.idUsuarios=usuarios.id)
                WHERE
                    pedidos.id =" . $idPedido . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** GET DETALLE VENTA
     * 
     */
    public function getDetalleVenta($idVenta) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.cantidad,
                    a.idProductos,
                    b.sku,
                    b.descLarga as descCorta,
                    precio as costoUnitario,
                    total
                FROM
                    ventasDetalle as a inner join productos as b on(a.idProductos=b.id)
                WHERE
                    a.idVentas = " . $idVenta . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }
    
    /** METODO getDetallePedido
     * 
     */
    public function getDetallePedido($idPedido) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.cantidad,
                    a.idProductos,
                    b.sku,
                    b.descLarga as descCorta,
                    precio as costoUnitario,
                    total
                FROM
                    pedidosDetalle as a inner join productos as b on(a.idProductos=b.id)
                WHERE
                    a.idPedidos = " . $idPedido . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** GET INFO VALE
     * 
     */
    public function getVale($idVale) {
        $sql = "SELECT 
                    valesCaja.*,
                    sucursales.descripcion as sucursal,
                    empresas.razonSocial as empresa,
                    empresas.nit,
                    empresas.direccion,
                    DATE_FORMAT(valesCaja.created_at,'%b %d %Y %h:%i %p') as fechaVale
                FROM
                    valesCaja inner join sucursales on(valesCaja.idSucursales=sucursales.id)
                    inner join empresas on(sucursales.idEmpresas=empresas.id)
                WHERE
                    valesCaja.id=" . $idVale . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** GET FONDO CORTE
     * 
     */
    public function getFondoCorte($idUsuario, $idSucursal) {
        $sql = "select format(ifnull(replace(monto,',',''),0),2) as fondo "
                . "from fondoCaja "
                . "where idUsuarios=" . $idUsuario . " and idSucursales=" . $idSucursal . " "
                . "and date(date_apertura)='" . $this->date . "' and date_cierre is null;";
        //echo $sql . '\n';
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        return $reg;
    }

    /** GET VALES CORTE
     * 
     */
    public function getValesCorte($idSucursal, $idUsuario) {
        $sql = "select format(ifnull(sum(replace(monto,',','')),0),2) as totalVales from valesCaja where idSucursales=" . $idSucursal . " and idUsuarios=" . $idUsuario . " and date(created_at)='" . $this->date . "' and statusCierre='0';";
        //echo $sql . '\n';
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        return $reg;
    }

    /**
     * 
     */
    public function getCierre($idCierre) {
        $sql = "SELECT 
                    a.*,
                    b.descripcion as sucursal,
                    c.nombreComercial as empresa,
                    d.userName as empleado
                FROM
                    cierresCaja as a inner join sucursales as b on(a.idSucursales=b.id)
                    inner join empresas as c on(b.idEmpresas=c.id)
                    inner join usuarios as d on(a.idUsuarios=d.id)
                where a.id=" . $idCierre . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        return $reg;
    }

    /**
     * 
     */
    public function getCorte($idCorte) {
        $sql = "SELECT 
                    a.*,
                    b.descripcion as sucursal,
                    c.nombreComercial as empresa,
                    d.userName as empleado
                FROM
                    corteCaja as a inner join sucursales as b on(a.idSucursales=b.id)
                    inner join empresas as c on(b.idEmpresas=c.id)
                    inner join usuarios as d on(a.idUsuarios=d.id)
                where a.id=" . $idCorte . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        return $reg;
    }

    /**
     * 
     */
    public function getCorteDetalle($idCorte) {
        $this->resultado = null;
        $sql = "select
                *
                from corteCajaDetalle where idCorteCaja=" . $idCorte . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

}
