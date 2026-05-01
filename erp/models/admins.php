<?php

/**
 * Description of admin
 *
 * @author Richard Sasvin
 */
require_once ("dbCon.php");
require_once ("general.php");
require_once ("email_.php");
require_once ("agenciasViajes.php");

class Admins extends General {

//* NUEVO METODO PARA total de factura DTE By Richard
    public function getTotalFacturacion($param){
        $this->resultado = null;
        $sql = SELECT 'facturacion' as documento, COUNT(*) as documentosOperados FROM ventas WHERE (fechaFactura BETWEEN '2022-01-01 00:00:00' AND '2022-12-31 23:59:59') AND tipoTransaccion = 1 and idEmpresas=" . $idEmpresa . ";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)){
            $this->resultado[] = $reg;
        }
        return $this->resulto;
    }

    //** METODO para total de ventas para el dashboard By Sasvin
    public function getTotalVentas($param){
        $this->resultado = null;
        $sql = "SELECT SUM(valorFactura) as total FROM ventas where idEmpresas=" . $idEmpresas . ";";
        $query = mysql_query($sql, dboCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

}