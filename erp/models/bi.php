<?php

/**
 * Description of reportes
 *
 * @author Richard Sasvin
 */
require_once("dbCon.php");
require_once("general.php");

class BI extends General {

    /** METODO VENTAS
     * 
     */
    public function ventas($params) {
        $this->resultado = null;
        $columns = "";
        $group = "";
        $order = "";
        if ($params['group1'] != "") {
            switch ($params['group1']) {
                case 'year':
                    $columns = "year(ventas.created_at) as agrupacion";
                    $group = " year(ventas.created_at)";
                    $order = " year(ventas.created_at) asc";
                    break;
                case 'month':
                    $columns = "month(ventas.created_at) as agrupacion";
                    $group = " month(ventas.created_at)";
                    $order = " month(ventas.created_at) asc";
                    break;
                case 'day':
                    $columns = "day(ventas.created_at) as agrupacion";
                    $group = " day(ventas.created_at)";
                    $order = " day(ventas.created_at) asc";
                    break;
                case 'hour':
                    $columns = "hour(ventas.created_at) as agrupacion";
                    $group = " hour(ventas.created_at)";
                    $order = " hour(ventas.created_at) asc";
                    break;
            }
        }
        if ($params['sucursales'] != "") {
            $columns .= ",sucursales.descripcion as sucursales";
            $group .= ",ventas.idSucursales";
        }
        $sql = "select 
                    " . $columns . ",sum(ventas.total) as total
                from
                    ventas
                        inner join
                    usuarios ON (ventas.idVendedores = usuarios.id)
                        inner join
                    sucursales ON (ventas.idSucursales = sucursales.id)
                group by 
                    " . $group . "
                order by 
                    " . $order . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

}
