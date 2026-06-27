<?php

/**
 * Description of reportes
 *
 * @author jjuarez
 */
require_once("dbCon.php");
require_once("general.php");

class flujosBovedas extends General {

    /** METODO getAnos
     * 
     */
    public static function formArqueoBoveda() {
        $resultado = null;
        $sql = "SELECT 
                    *,if(operacion=1,'+','-') as operaciones,operacion
                FROM
                    formArqueoBoveda
                WHERE
                    parent in('formArqueo2','formArqueo3')
                ORDER BY
                    parent ASC;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** METODO getAnos
     * 
     */
    public static function arqueoDetalle($idFormArqueoBoveda, $fecha) {
        $sql = "SELECT 
                    b.valor
                FROM
                    arqueoBoveda AS a
                        INNER JOIN
                    arqueoBovedaDetalle AS b ON (a.id = b.idArqueoBoveda)
                WHERE
                    idFormArqueoBoveda = " . $idFormArqueoBoveda . "
                        AND idEmpresas = 1
                        AND a.arqueo_at='" . $fecha . "';";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO getAnos
     * 
     */
    public static function getAnos() {
        $resultado = null;
        $sql = "select * from anos order by descripcion desc;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** METODO getMeses
     * 
     */
    public static function getMeses() {
        $resultado = null;
        $sql = "select * from meses;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** METODO getBovedas
     * 
     */
    public static function getBovedas($idEmpresas) {
        $resultado = null;
        $sql = "select * from sucursales where idEmpresas=" . $idEmpresas . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** METODO saldoInicialMes
     * 
     */
    public static function saldoInicialMes($mes, $idSalas, $tipoReporte) {
        $filter = "";
        switch ($tipoReporte) {
            case '1':
                $filter = "arqueoBoveda";
                break;
            case '2':
                $filter = "arqueoBovedaMesas";
                break;
        }
        $sql = "select 
                    replace(totalIngreso1,',','') as saldoInicial
                from
                    " . $filter . " 
                where 
                    month(arqueo_at)='" . $mes . "' and idSucursales=" . $idSalas . "
                order by id asc limit 1;";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg['saldoInicial'];
        }
    }

    /** METODO reporteControlBovedas
     * 
     */
    public static function reporteControlBovedas($year, $month, $idSala, $idEmpresas, $tipoReporte) {
        $resultado = null;
        $filter = "";
        switch ($tipoReporte) {
            case '1':
                $filter = "arqueoBoveda";
                break;
            case '2':
                $filter = "arqueoBovedaMesas";
                break;
        }
        $sql = "select 
                    date_format(arqueo_at, '%d-%m-%Y') as dia, 
                    totalIngreso1 as saldoInicial,
                    if(totalIngreso3>0,totalIngreso3,0) as aumentos,
                    if(totalIngreso3<0,totalIngreso3,0) as disminuciones,
                    totalIngreso4 as saldosFinal,
                    totalIngreso as diferenciaCorte
                from
                    " . $filter . "
                where
                    year(arqueo_at)='" . $year . "' and month(arqueo_at)='" . $month . "' and idSucursales=" . $idSala . " and idEmpresas='" . $idEmpresas . "'";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** METODO resumenIngresosMaquinas
     * 
     */
    public static function resumenIngresosMaquinas($year, $month, $idSala, $idEmpresas) {
        $resultado = null;
        $sql = "SELECT 
                    arqueo_at,
                    idSucursales,
                    idEmpresas,
                    replace(replace(b,',',''),' ','') as b,
                    replace(replace(c,',',''),' ','') as c,
                    replace(replace(d,',',''),' ','') as d,
                    replace(replace(e,',',''),' ','') as e,
                    replace(replace(f,',',''),' ','') as f,
                    replace(replace(g,',',''),' ','') as g,
                    replace(replace(h,',',''),' ','') as h,
                    replace(replace(i,',',''),' ','') as i,
                    replace(replace(j,',',''),' ','') as j,
                    replace(replace(k,',',''),' ','') as k,
                    replace(replace(l,',',''),' ','') as l,
                    replace(replace(m,',',''),' ','') as m,
                    replace(replace(n,',',''),' ','') as n,
                    replace(replace(o,',',''),' ','') as o,
                    replace(replace(p,',',''),' ','') as p,
                    replace(replace(q,',',''),' ','') as q,
                    replace(replace(r,',',''),' ','') as r,
                    REPLACE(REPLACE(s, ',', ''), ' ', '') AS s,
                    ifnull(
                    (SELECT 
                        ROUND((valor / DAY(LAST_DAY('" . $year . "-" . $month . "-01'))), 2) AS valorLicencia
                    FROM
                        casinos_licencias AS a
                            INNER JOIN
                        anos AS b ON (a.idAnos = b.id)
                            INNER JOIN
                        meses AS c ON (a.idMeses = c.id)
                    WHERE
                        a.idSucursales = " . $idSala . " AND a.idEmpresas = " . $idEmpresas . "
                            AND b.descripcion = '" . $year . "'
                            AND c.id = '" . $month . "'),0) as licencia
                FROM
                    vw_resumenIngresosMaquinas
                WHERE
                    idSucursales = " . $idSala . " AND idEmpresas = " . $idEmpresas . " and year(arqueo_at)='" . $year . "' and month(arqueo_at)='" . $month . "';";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** METODO resumenIngresosMesas
     * 
     */
    public static function resumenIngresosMesas($year, $month, $idSala, $idEmpresas) {
        $resultado = null;
        $sql = "SELECT 
                    arqueo_at,
                    idSucursales,
                    idEmpresas,
                    REPLACE(REPLACE(b, ',', ''), ' ', '') AS b,
                    REPLACE(REPLACE(c, ',', ''), ' ', '') AS c,
                    REPLACE(REPLACE(d, ',', ''), ' ', '') AS d,
                    REPLACE(REPLACE(e, ',', ''), ' ', '') AS e,
                    REPLACE(REPLACE(f, ',', ''), ' ', '') AS f,
                    REPLACE(REPLACE(g, ',', ''), ' ', '') AS g,
                    REPLACE(REPLACE(h, ',', ''), ' ', '') AS h,
                    REPLACE(REPLACE(i, ',', ''), ' ', '') AS i,
                    REPLACE(REPLACE(j, ',', ''), ' ', '') AS j,
                    REPLACE(REPLACE(k, ',', ''), ' ', '') AS k,
                    REPLACE(REPLACE(l, ',', ''), ' ', '') AS l,
                    REPLACE(REPLACE(m, ',', ''), ' ', '') AS m,
                    REPLACE(REPLACE(n, ',', ''), ' ', '') AS n,
                    REPLACE(REPLACE(o, ',', ''), ' ', '') AS o,
                    REPLACE(REPLACE(p, ',', ''), ' ', '') AS p,
                    REPLACE(REPLACE(r, ',', ''), ' ', '') AS r,
                    REPLACE(REPLACE(s, ',', ''), ' ', '') AS s,
                    REPLACE(REPLACE(t, ',', ''), ' ', '') AS t,
                    REPLACE(REPLACE(u, ',', ''), ' ', '') AS u,
                    ifnull(
                    (SELECT 
                        ROUND((valor / DAY(LAST_DAY('" . $year . "-" . $month . "-01'))), 2) AS valorLicencia
                    FROM
                        casinos_licencias AS a
                            INNER JOIN
                        anos AS b ON (a.idAnos = b.id)
                            INNER JOIN
                        meses AS c ON (a.idMeses = c.id)
                    WHERE
                        a.idSucursales = " . $idSala . " AND a.idEmpresas = " . $idEmpresas . "
                            AND b.descripcion = '" . $year . "'
                            AND c.id = '" . $month . "'),0) as licencia
                FROM
                    vw_resumenIngresosMesas
                WHERE
                    idSucursales = " . $idSala . " AND idEmpresas = " . $idEmpresas . " and year(arqueo_at)='" . $year . "' and month(arqueo_at)='" . $month . "';";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** METODO reporteVisanet
     * 
     */
    public static function reporteVisanet($year, $month, $idSala, $idEmpresas) {
        $resultado = null;
        $sql = "SELECT 
                    arqueo_at, 
                    replace(c,',','') as v1, 
                    replace(d,',','') as v2,  
                    SUM(replace(c,',','') + replace(d,',','')) AS monto,
                    round((SUM(replace(c,',','') + replace(d,',',''))/1.12)*0.12,2) AS montoIva,
                    SUM(replace(c,',','') + replace(d,',',''))-round((SUM(replace(c,',','') + replace(d,',',''))/1.12)*0.12,2) as montoSinIva,
                    round((SUM(replace(c,',','') + replace(d,',',''))-round((SUM(replace(c,',','') + replace(d,',',''))/1.12)*0.12,2))*0.08,2) as comision,
                    round((SUM(replace(c,',','') + replace(d,',',''))-round((SUM(replace(c,',','') + replace(d,',',''))/1.12)*0.12,2))*0.08,2)*0.12 as comsionIva,
                    round((SUM(replace(c,',','') + replace(d,',',''))/1.12)*0.12,2)*0.15 as retIva,
                    round(SUM(replace(c,',','') + replace(d,',',''))-(round((SUM(replace(c,',','') + replace(d,',',''))-round((SUM(replace(c,',','') + replace(d,',',''))/1.12)*0.12,2))*0.08,2)+(round((SUM(replace(c,',','') + replace(d,',',''))-round((SUM(replace(c,',','') + replace(d,',',''))/1.12)*0.12,2))*0.08,2)*0.12)+(round((SUM(replace(c,',','') + replace(d,',',''))/1.12)*0.12,2)*0.15)),2) as montoDepositado
                FROM
                    vw_resumenIngresosMaquinas
                WHERE
                    idSucursales = " . $idSala . " AND idEmpresas = " . $idEmpresas . "
                        AND YEAR(arqueo_at) = '" . $year . "'
                        AND MONTH(arqueo_at) = '" . $month . "'
                GROUP BY arqueo_at;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** METODO reportePremiosPromos
     * 
     */
    public static function reportePremiosPromos($year, $month, $idSala, $idEmpresas) {
        $resultado = null;
        $sql = "SELECT
                    arqueo_at,
                    sum(replace(m,',','')) AS pcd,
                    sum(replace(n,',','')) AS psd,
                    round(sum(replace(m,',','')+replace(n,',','')),2) as total
                 FROM
                     vw_resumenIngresosMaquinas
                 WHERE
                     idSucursales = " . $idSala . " AND idEmpresas = " . $idEmpresas . "
                         AND YEAR(arqueo_at) = '" . $year . "'
                         AND MONTH(arqueo_at) = '" . $month . "'
                 GROUP BY arqueo_at;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** ENCABEZADOS REPORTE DE INGRESOS
     * 
     */
    public static function encabezadoReporteIngresos() {
        $resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    formArqueoBovedaMesas
                WHERE
                    parent not in('formArqueo1','formArqueo4')
                ORDER BY parent ASC";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

    /** VALOR POR ENCABEZADO
     *  
     */
    public static function valorReporteIngresos($fecha, $idFormArqueoBoveda, $idEmpresas) {
        $sql = "SELECT 
                    replace(b.valor,',','') as valor
                FROM
                    arqueoBovedaMesas as a inner join arqueoBovedaMesasDetalle as b on(a.id=b.idArqueoBoveda)
                where a.arqueo_at='" . $fecha . "' and a.idEmpresas=" . $idEmpresas . " and idFormArqueoBoveda=" . $idFormArqueoBoveda . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** VALOR DIARIO LICENCIA
     *  
     */
    public static function valorDiarioLicencia($year, $month, $idEmpresas) {
        $sql = "SELECT 
                    ROUND((valor / DAY(LAST_DAY('" . $year . "-" . $month . "-01'))), 2) AS valorLicencia
                FROM
                    casinos_licencias
                where idMeses=" . $month . " and idSucursales=" . $idEmpresas . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** VALOR CONSOLIDADO POR ENCABEZADO
     *  
     */
    public static function valorConsolidadoReporteIngresos($month, $idFormArqueoBoveda, $idEmpresas) {
        $sql = "SELECT 
                    replace(b.valor,',','') as valor
                FROM
                    arqueoBovedaMesas as a inner join arqueoBovedaMesasDetalle as b on(a.id=b.idArqueoBoveda)
                where a.arqueo_at='" . $fecha . "' and a.idEmpresas=" . $idEmpresas . " and idFormArqueoBoveda=" . $idFormArqueoBoveda . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO partidaBovedas
     * 
     */
    public static function partidaBovedas($params) {
        $resultado = null;
        $table1 = "arqueoBoveda";
        $table2 = "arqueoBovedaDetalle";
        $table3 = "formArqueoBoveda";
        if ($params['idTipoReporte'] == '2') {
            $table1 = "arqueoBovedaMesas";
            $table2 = "arqueoBovedaMesasDetalle";
            $table3 = "formArqueoBovedaMesas";
        }
        $sql = "SELECT 
                    a.idSucursales,
                    a.arqueo_at,
                    if(d.cuenta = '1010103',
                    case g.cuenta  
                              when '2001' then '1010103'
                              when '2002' then '1010104'
                              when '2101' then '1010105'
                              when '2201' then '1010107'
                              when '2202' then '1010104'
                              when '2301' then '1010109'
                              when '2401' then '1010113'	
                            end,
                    d.cuenta) as cuenta,
                    upper(if(d.cuenta = '1010103',
                    case g.cuenta  
                              when '2001' then 'Caja General Fantastic Proceres I'
                              when '2002' then 'Caja General Fantastic Proceres II'
                              when '2101' then 'Caja General Fantastic Xela I'
                              when '2201' then 'Caja General Fantastic Majadas I'
                              when '2202' then 'Caja General Fantastic Majadas II'
                              when '2301' then 'Caja General Pistomania I'
                              when '2401' then 'Caja General Metronorte I'	
                            end,
                    d.descripcion)) as cuentaContable,
                    e.descripcion as tipoCuentaContable,
                    g.cuenta as cc,
                    g.descripcion as centroCosto,
                    abs(round(case idTipoCuentaContable  
                              when 1 then if(sum(if(c.operacion=1,+replace(valor,',',''),-replace(valor,',','')))>0,sum(if(c.operacion=1,+replace(valor,',',''),-replace(valor,',',''))),0)
                              when 2 then if(sum(if(c.operacion=1,+replace(valor,',',''),-replace(valor,',','')))<0,sum(if(c.operacion=1,+replace(valor,',',''),-replace(valor,',',''))),0) 
                    end,2)) as debe,
                    abs(round(case idTipoCuentaContable  
                              when 1 then if(sum(if(c.operacion=1,+replace(valor,',',''),-replace(valor,',','')))<0,sum(if(c.operacion=1,+replace(valor,',',''),-replace(valor,',',''))),0)
                              when 2 then if(sum(if(c.operacion=1,+replace(valor,',',''),-replace(valor,',','')))>0,sum(if(c.operacion=1,+replace(valor,',',''),-replace(valor,',',''))),0) 
                    end,2)) as haber,
                    upper(concat('Registro contable al ',arqueo_at,' Sala ',g.descripcion)) as descripcionPartida
                FROM
                    ".$table1." AS a
                            INNER JOIN
                    ".$table2." AS b ON (a.id = b.idArqueoBoveda)
                            INNER JOIN
                    ".$table3." AS c ON (b.idFormArqueoBoveda = c.id)
                            INNER JOIN
                    vw_nomenclarutaBovedas AS d ON (c.idNomenclatura = d.id)
                            INNER JOIN
                    tipoCuentaContable as e on(d.idTipoCuentaContable=e.id)
                            INNER JOIN
                    sucursales as f on(a.idSucursales=f.id)
                            INNER JOIN
                    centrosCosto as g on(f.idCentrosCosto=g.id)    
                WHERE
                    a.idEmpresas =" . $params['idEmpresas'] . "  AND a.idSucursales=" . $params['idSalas'] . " and date(a.arqueo_at)='" . date("Y-m-d", strtotime($params['fechaCierre'])) . "'
                    AND c.idNomenclatura != 0
                    AND valor != 0
                group by
                    c.idNomenclatura;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $resultado[] = $reg;
        }
        return $resultado;
    }

}
