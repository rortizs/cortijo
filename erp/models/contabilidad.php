<?php

/**
 * POS /Modulo Inventarios - Class Inventarios
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
require_once ("dbCon.php");
require_once ("general.php");
require_once ("admin.php");

class Contabilidad extends General {

    /** METODO getTipoOperacionesPartidas
     * 
     */
    public function getTipoOperacionesPartidas() {
        $this->resultado = null;
        $sql = "select * from tipoOperacionPartida;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO saveFormato
     * 
     */
    public function saveFormato($params, $idUsuarios, $idEmpresas) {
        $response = "";
        $sql = "insert into formatos (descripcion,idUsuarios,idEmpresas,created_at)"
                . " values('" . strtoupper($params['descripcion']) . "'," . $idUsuarios . "," . $idEmpresas . ",'" . $this->timestamp . "')";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->saveFormatoDetalle(mysql_insert_id(), $params['detalle']);
            $response[] = array('message' => 'success', 'idFormato' => mysql_insert_id());
        } else {
            $response[] = array('message' => 'failed', 'Query' => $sql);
        }
        return $response;
    }

    /** METODO saveFormatoDetalle
     * 
     */
    public function saveFormatoDetalle($idFormato, $detalle) {
        $sql2 = "delete from formatosDetalle where idFormatos=" . $idFormato . "; ";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2 == true) {
            $numero = count($detalle);
            $i = 1;
            $sql = "insert into formatosDetalle values";
            foreach ($detalle as $key => $value) {
                if ($i !== $numero) {
                    $sql .= "(null," . $idFormato . "," . $value['idNomenclatura'] . ",'" . $value['debe'] . "','" . $value['haber'] . "'," . $value['idCentrosCosto'] . "),";
                } else {
                    $sql .= "(null," . $idFormato . "," . $value['idNomenclatura'] . ",'" . $value['debe'] . "','" . $value['haber'] . "'," . $value['idCentrosCosto'] . ")";
                }
                $i ++;
            }
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('insert true detalle formato # ' . $idFormato);
            } else {
                error_log('error sql: ' . $sql);
            }
        } else {
            error_log('error sql: ' . $sql);
        }
    }

    /** METODO savePartida
     * 
     */
    public function savePartida($params, $idUsuarios, $idEmpresas) {
        $response = "";
        $sql = "insert into partidas (numero,partida_at,descripcion,idTipoOperacionPartida,idUsuarios,idEmpresas,created_at)"
                . " values('" . $params['numero'] . "','" . date("Y-m-d", strtotime($params['partida_at'])) . "','" . $params['descripcion'] . "'," . $params['idTipoOperacionPartida'] . "," . $idUsuarios . "," . $idEmpresas . ",'" . $this->timestamp . "')";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->savePartidaDetalle(mysql_insert_id(), $params['detalle']);
            $admin = new Admin();
            $updateCorrelativo = $admin->updateCorrelativoPartidas($params['idDocumento'], $params['numero'], $idEmpresas);
            $response[] = array('message' => 'success', 'idPartida' => mysql_insert_id());
        } else {
            $response[] = array('message' => 'failed', 'Query' => $sql);
        }
        return $response;
    }

    /** METODO partidaDetalle
     * 
     */
    public function savePartidaDetalle($idPartida, $detalle) {
        $numero = count($detalle);
        $i = 1;
        $sql2 = "delete from partidasDetalle where idPartidas=" . $idPartida . "; ";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2 == true) {
            $sql = "insert into partidasDetalle values";
            foreach ($detalle as $key => $value) {
                if ($i !== $numero) {
                    $sql .= "(null," . $idPartida . "," . $value['idNomenclatura'] . ",'" . $value['debe'] . "','" . $value['haber'] . "'," . $value['idCentrosCosto'] . "),";
                } else {
                    $sql .= "(null," . $idPartida . "," . $value['idNomenclatura'] . ",'" . $value['debe'] . "','" . $value['haber'] . "'," . $value['idCentrosCosto'] . ");";
                }
                $i ++;
            }
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('insert true detalle partida # ' . $idPartida);
            } else {
                error_log('error sql: ' . $sql);
            }
        } else {
            error_log('error sql: ' . $sql);
        }
    }

    /** METODO getLibroDiario
     * 
     */
    public function getLibroDiario($params, $idEmpresas, $tipo) {
        $this->resultado = null;
        $sql = "";
        $filter = "";
        if (isset($params['yearInicial']) && isset($params['yearFinal'])) {
            $filter .= " and year(partida_at) between '" . $params['yearInicial'] . "' and '" . $params['yearFinal'] . "'";
        }
        if (isset($params['mesInicial']) && isset($params['mesFinal'])) {
            $filter .= " and month(partida_at) between '" . $params['mesInicial'] . "' and '" . $params['mesFinal'] . "'";
        }
//        if (isset($params['partidaInicial']) && isset($params['partidaFinal']) && $params['partidaInicial'] !== '0' && $params['partidaFinal'] !== '0') {
//            $filter .= " and numero between '" . $params['partidaInicial'] . "' and '" . $params['partidaFinal'] . "'";
//        }
        if ($tipo == 'detallado') {
            $sql = "select 
                    * 
                from 
                    partidas
                where 
                    idEmpresas=" . $idEmpresas . " " . $filter . ";";
        } else {
            $sql = "SELECT
                        a.idTipoOperacionPartida,d.descripcion
                    FROM
                        partidas as a inner join partidasDetalle as b on(a.id=b.idPartidas)
                        inner join nomenclatura as c on(b.idNomenclatura=c.id)
                        inner join tipoOperacionPartida as d on(a.idTipoOperacionPartida=d.id)
                    WHERE
                        a.idEmpresas=" . $idEmpresas . " " . $filter . "
                    GROUP BY
                        a.idTipoOperacionPartida
                    ORDER BY
                        a.idTipoOperacionPartida asc";
        }
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getPartida
     * 
     */
    public function getPartida($idPartida) {
        $sql = "SELECT 
                    id,
                    numero,
                    date_format(partida_at,'%d-%m-%Y') as partida_at,
                    descripcion,
                    idTipoOperacionPartida,
                    idUsuarios,
                    idEmpresas,
                    created_at,
                    updated_at
                FROM
                    partidas
                WHERE
                    id = " . $idPartida . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO getLibroDiarioDetalle
     * 
     */
    public function getLibroDiarioDetalle($idPartida, $tipo, $params) {
        $this->resultado = null;
        $sql = "";
        if ($tipo == 'detallado') {
            $sql = "SELECT
                    b.id as idCuenta,
                    b.cuenta as cuenta, 
                    b.descripcion as nombreCuenta,
                    concat(b.cuenta,' - ',b.descripcion) as cuentaContable,
                    ifnull(c.id,0) as idCentroCosto,
                    ifnull(concat(c.cuenta,' - ',c.descripcion),'') as centroCosto,
                    a.debe,
                    a.haber
                FROM
                    partidasDetalle AS a
                        INNER JOIN
                    nomenclatura AS b ON (a.idNomenclatura = b.id)
                        LEFT JOIN
                    centrosCosto AS c ON (a.idCentrosCosto = c.id)
                where 
                    a.idPartidas=" . $idPartida . ";";
        } else {
            $sql = "SELECT
                        a.idTipoOperacionPartida,
                        c.cuenta,
                        c.descripcion as nombreCuenta,
                        sum(debe) as debe,
                        sum(haber) as haber
                    FROM
                        partidas as a inner join partidasDetalle as b on(a.id=b.idPartidas)
                        inner join nomenclatura as c on(b.idNomenclatura=c.id)
                    WHERE
                        a.idEmpresas = " . $params['idEmpresas'] . " and a.idTipoOperacionPartida=" . $idPartida . "
                            AND YEAR(a.partida_at) BETWEEN '" . $params['yearInicial'] . "' AND '" . $params['yearFinal'] . "'
                            AND MONTH(a.partida_at) BETWEEN '" . $params['mesInicial'] . "' AND '" . $params['mesFinal'] . "'
                    GROUP BY
                            b.idNomenclatura
                    ORDER BY
                            a.idTipoOperacionPartida asc";
        }
        //echo $sql.'<br/>';
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getFormato
     * 
     */
    public function getFormato($idFormato) {
        $sql = "select * from formatos where id=" . $idFormato . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO getFormatoDetalle
     * 
     */
    public function getFormatoDetalle($idFormato) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.id,
                    a.idNomenclatura,
                    concat(b.cuenta,' - ',b.descripcion) as cuentaContable,
                    if(debe='[Seleccione...]','0',debe) as debe,
                    if(haber='[Seleccione...]','0',haber) as haber,
                    a.idCentrosCosto,
                    ifnull(concat(c.cuenta,' - ',c.descripcion),'-') as centroCosto
                FROM
                    formatosDetalle AS a
                        LEFT JOIN
                    nomenclatura AS b ON (a.idNomenclatura = b.id)
                        LEFT JOIN
                    centrosCosto AS c ON (a.idCentrosCosto = c.id)
                WHERE
                    a.idFormatos =" . $idFormato . ";";
        //echo $sql;
        //exit();
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getIDP
     * 
     */
    public function getIDP($params) {
        $this->resultado = null;
        $sql = "select * from vw_idp where idEmpresas='" . $params['empresa'] . "';";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO updatePartida
     * 
     */
    public function updatePartida($params, $idEmpresas) {
        $response = "";
        $sql = "update partidas set partida_at='" . date("Y-m-d", strtotime($params['partida_at'])) . "',descripcion='" . $params['descripcion'] . "',idTipoOperacionPartida='" . $params['idTipoOperacionPartida'] . "'"
                . "where id=" . $params['idPartida'] . " and idEmpresas=" . $idEmpresas . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->savePartidaDetalle($params['idPartida'], $params['detalle']);
            $response[] = array('message' => 'success', 'idPartida' => mysql_insert_id());
        } else {
            $response[] = array('message' => 'failed', 'Query' => $sql);
        }
        return $response;
    }

    /** METODO updateFormato
     * 
     */
    public function updateFormato($params, $idEmpresas) {
        $response = "";
        $sql = "update formatos set descripcion='" . $params['descripcion'] . "'"
                . "where id=" . $params['idFormato'] . " and idEmpresas=" . $idEmpresas . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->saveFormatoDetalle($params['idFormato'], $params['detalle']);
            $response[] = array('message' => 'success', 'idFormato' => mysql_insert_id());
        } else {
            $response[] = array('message' => 'failed', 'Query' => $sql);
        }
        return $response;
    }

    /** METODO getLibroCompras
     * 
     */
    public function getLibroCompras($params, $idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.serieFactura,
                    a.noFactura,
                    DATE_FORMAT(a.fechaFactura, '%d-%m-%Y') AS fechaFactura,
                    'COMPRAS' as tipoDocumento,
                    b.nitP as nit,
                    b.descripcion as proveedores,
                    if(b.idPequenoContribuyente=1,'Si','No') as pequenoContribuyente,
                    idTipoOperacion,
                    if(idTipoOperacion=4,subtotal,0) as combustibles,
                    if(idTipoOperacion=1,subtotal,0) as compras,
                    case idTipoOperacion
                        when 2 then subtotal
                        when 3 then subtotal
                    end as servicios,
                    0 as inguat,
                    exento,
                    iva,
                    total,
                    subtotal
                FROM
                    compras AS a
                        LEFT JOIN
                    proveedores AS b ON (a.idProveedores = b.id)
                WHERE    
                    year(a.fechaContabilizacion)='" . $params['yearInicial'] . "' and
                    month(a.fechaContabilizacion)='" . $params['mesInicial'] . "' and
                    a.idSucursales=" . $params['idSucursales'] . " and    
                    a.idEmpresas=" . $idEmpresas . "";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getLibroVentas
     * 
     */
    public function getLibroVentas($params, $idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    concat('FACT ',a.serie,'-',a.correlativo) as documento,
                    DATE_FORMAT(a.fechaFactura, '%d-%m-%Y') AS fechaVenta,
                    a.nit,
                    a.nombre AS comprador,
                    ifnull(case a.idTipoOperacion
                                when 4 then a.subtotal
                    end,0) as exportaciones,
                    ifnull(case a.idTipoOperacion
                                when 1 then a.subtotal
                    end,0) as bienes,
                    ifnull(case a.idTipoOperacion
                                when 2 then a.subtotal
                    end,0) as servicios,
                    a.iva,
                    a.total,
                    a.idEmpresas,
                    'FACTURAS DE VENTA' as tipoDocumento
                FROM
                    ventas AS a
                WHERE    
                    year(a.fechaFactura)='" . $params['yearInicial'] . "' and
                    month(a.fechaFactura)='" . $params['mesInicial'] . "' and
                    a.idSucursales=" . $params['idSucursales'] . " and    
                    a.idEmpresas=" . $idEmpresas . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getLibroVentasResumenDocumentos
     * 
     */
    public function getLibroVentasResumenDocumentos($params, $idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    CONCAT(b.prefijo, ' ', b.serie) AS tipoDocumento,
                    sum(a.subtotal) as subtotal,
                    sum(a.iva) as iva,
                    sum(a.total) as total,
                    count(*) as cantidad
                FROM
                    ventas AS a
                        LEFT JOIN
                    documentosCorrelativos AS b ON (SUBSTRING_INDEX(a.documento, '-', 1) = b.serie && a.idEmpresas = b.idEmpresas)
                WHERE    
                    year(a.created_at)='" . $params['yearInicial'] . "' and
                    month(a.created_at)='" . $params['mesInicial'] . "' and
                    a.idSucursales=" . $params['idSucursales'] . " and
                    a.idEmpresas=" . $idEmpresas . " GROUP by CONCAT(b.prefijo, ' ', b.serie)";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getLibroMayor
     * 
     */
    public function getLibroMayor($params, $idEmpresas, $tipo) {
        $this->resultado = null;
        $sql = "";
        $filter = "";
        if (isset($params['yearInicial']) && isset($params['yearFinal'])) {
            $filter .= " and year(partida_at) between '" . $params['yearInicial'] . "' and '" . $params['yearFinal'] . "'";
        }
        if (isset($params['mesInicial']) && isset($params['mesFinal'])) {
            $filter .= " and month(partida_at) between '" . $params['mesInicial'] . "' and '" . $params['mesFinal'] . "'";
        }
        if ($tipo == 'detallado') {
            $sql = "";
        } else {
            $sql = "SELECT
                        c.cuenta,
                        c.descripcion as cuentaContable,
                        b.idNomenclatura,
                        c.idTipoCuentaContable,
                        d.descripcion
                    FROM
                        partidas as a inner join partidasDetalle as b on(a.id=b.idPartidas)
                        inner join nomenclatura as c on(b.idNomenclatura=c.id)
                        inner join tipoCuentaContable as d on(c.idTipoCuentaContable=d.id)
                    WHERE
                        a.idEmpresas=" . $idEmpresas . " " . $filter . "
                    GROUP BY
                        b.idNomenclatura ORDER BY a.partida_at asc;";
        }
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getLibroMayorDetalle
     * 
     */
    public function getLibroMayorDetalle($idCuentaContable, $tipo, $params) {
        $this->resultado = null;
        $sql = "";
        if ($tipo == 'detallado') {
            $sql = "SELECT
                        if(d.idDocumentosCorrelativos is null,concat('Partida ',a.numero),concat(d.serieFactura,' ',d.noFactura)) as documento,
                        a.descripcion as concepto,
                        a.partida_at,
                        if(d.idDocumentosCorrelativos is null,a.descripcion,f.descripcion) as nombre,
                        a.numero,
                        b.debe,
                        b.haber
                    FROM
                        partidas as a inner join partidasDetalle as b on(a.id=b.idPartidas)
                        inner join nomenclatura as c on(b.idNomenclatura=c.id)
                        left join compras as d on(a.descripcion=d.conceptoCompra)
                        left join proveedores as f on(d.idProveedores=f.id)
                    WHERE
                        a.idEmpresas = " . $params['idEmpresas'] . " and b.idNomenclatura=" . $idCuentaContable . "
                        AND YEAR(a.partida_at) BETWEEN '" . $params['yearInicial'] . "' AND '" . $params['yearFinal'] . "'
                        AND MONTH(a.partida_at) BETWEEN '" . $params['mesInicial'] . "' AND '" . $params['mesFinal'] . "'
                    ";
        } else {
            $sql = "SELECT
                        a.descripcion,
                        date_format(a.partida_at,'%d-%m-%Y') as partida_at,
                        a.numero,
                        b.debe,
                        b.haber
                    FROM
                        partidas as a inner join partidasDetalle as b on(a.id=b.idPartidas)
                        inner join nomenclatura as c on(b.idNomenclatura=c.id)
                    WHERE
                        a.idEmpresas = " . $params['idEmpresas'] . " and b.idNomenclatura=" . $idCuentaContable . "
                            AND YEAR(a.partida_at) BETWEEN '" . $params['yearInicial'] . "' AND '" . $params['yearFinal'] . "'
                            AND MONTH(a.partida_at) BETWEEN '" . $params['mesInicial'] . "' AND '" . $params['mesFinal'] . "'
                    ORDER BY partida_at asc;";
        }
        //echo $sql.'<br/>';
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getBalanceSaldos
     * 
     */
    public function getBalanceSaldos($params, $idEmpresas) {
        $this->resultado = null;
        $filter = "";
        if ($params['tipoReporte'] == '2') {
            $filter = " AND YEAR(a.partida_at) between '" . $params['yearInicial'] . "' and '" . $params['yearFinal'] . "'"
                    . " AND MONTH(a.partida_at) between '" . $params['monthInicial'] . "' and '" . $params['monthFinal'] . "'";
        } else {
            $filter = " AND YEAR(a.partida_at) = '" . $params['yearInicial'] . "' AND MONTH(a.partida_at) = '" . $params['monthInicial'] . "'";
        }
        $sql = "SELECT 
                    c.cuenta,
                    c.descripcion AS cuentaContable,
                    ifnull(d.inicial,0) as inicial,
                    SUM(bb.debe) AS debitos,
                    SUM(bb.haber) AS creditos,
                    CASE c.idTipoCuentaContable
                        WHEN 1 THEN SUM(bb.debe) - SUM(bb.haber)
                        WHEN 2 THEN SUM(bb.debe) + SUM(bb.haber)
                    END AS saldo,
                    c.idTipoCuentaContable
                FROM
                    partidas AS a
                        INNER JOIN
                    partidasDetalle AS bb ON (a.id = bb.idPartidas)
                        INNER JOIN
                    nomenclatura AS c ON (bb.idNomenclatura = c.id)
                                LEFT JOIN
                    cierresContables as d ON(d.idNomenclatura=bb.idNomenclatura)    
                WHERE
                    a.idEmpresas = " . $idEmpresas . " " . $filter . "
                GROUP BY c.cuenta;";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getBalanceGeneralEstadoResultados
     * 
     */
    public function getBalanceGeneralEstadoResultados($params, $idEmpresas) {
        $this->resultado = null;
        $filtro = "";
        if ($params['tipo'] == 1) {
            $filtro = " and substring(c.cuenta,1,1)>3";
        } else {
            $filtro = " and substring(c.cuenta,1,1)<=3";
        }
        $filtro .= " AND YEAR(a.partida_at) = '" . $params['periodo'] . "' AND MONTH(a.partida_at) = '" . $params['mes'] . "'";
        $sql = "SELECT 
                    c.cuenta,
                    c.descripcion as cuentaContable,
                    CASE c.idTipoCuentaContable
                        WHEN 1 THEN SUM(b.debe) - SUM(b.haber)
                        WHEN 2 THEN SUM(b.debe) + SUM(b.haber)
                    END as saldo,
                    c.nivel,
                    c.padre
                FROM
                    partidas AS a
                        INNER JOIN
                    partidasDetalle AS b ON (a.id = b.idPartidas)
                                INNER JOIN
                    nomenclatura as c ON(b.idNomenclatura=c.id) 
                WHERE
                    a.idEmpresas = $idEmpresas " . $filtro . "
                GROUP by
                    c.cuenta
                ORDER BY
                    c.cuenta ASC;";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getNomenclatura
     * 
     */
    public function getNomenclatura($empresa) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    vw_nomenclatura
                WHERE    
                    idEmpresas='" . $empresa . "';";
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
    public function getCuenta($cuenta, $idEmpresa) {
        $sql = "SELECT 
                    cuenta,
                    descripcion as cuentaContable,
                    padre,
                    nivel
                FROM
                    nomenclatura
                WHERE
                    cuenta = '" . $cuenta . "' and idEmpresas=" . $idEmpresa . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO INSERT BALANCE GENERAL
     * 
     */
    public function insertBalanceGeneral($detalle, $periodo, $mes, $idEmpresas, $mesTxt) {
        $response = "";
        $numero = count($detalle);
        $i = 1;
        $sql2 = "delete from contBalanceGeneral where periodo='" . $periodo . "' and mes='" . $mes . "' and idEmpresas=$idEmpresas;";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2 == true) {
            $sql = "insert into contBalanceGeneral values";
            foreach ($detalle as $key => $value) {
                if ($i !== $numero) {
                    $sql .= "(null,'" . $periodo . "','" . $mes . "','" . $value['cuenta'] . "','" . $value['cuentaContable'] . "','" . $value['saldo4'] . "','" . $value['saldo3'] . "','" . $value['saldo2'] . "','" . $value['saldo1'] . "'," . $idEmpresas . "),";
                } else {
                    $sql .= "(null,'" . $periodo . "','" . $mes . "','" . $value['cuenta'] . "','" . $value['cuentaContable'] . "','" . $value['saldo4'] . "','" . $value['saldo3'] . "','" . $value['saldo2'] . "','" . $value['saldo1'] . "'," . $idEmpresas . ");";
                }
                $i ++;
            }
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                $response[] = array('message' => 'Cierre ' . $mesTxt . '-' . $periodo . ' para Balance General generado exitosamente');
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'No se generaron movimiento en ' . $mesTxt . '-' . $periodo . ' en las cuentas para generar Balance General');
            }
        } else {
            $response[] = array('message' => 'failed step 1');
        }
        return $response;
    }

    /** METODO savePartidaBovedas
     * 
     */
    public function savePartidaBovedas($params, $idUsuarios, $idEmpresas) {
        $response = "";
        $idTipoOperacionPartida = 1;
        // obtener el numero partida
        $admin = new Admin();
        $documento = $admin->getCorrelativoPartidas($idEmpresas);
        $sql = "insert into partidas (numero,partida_at,descripcion,idTipoOperacionPartida,idUsuarios,idEmpresas,created_at)"
                . " values('" . $documento['numero'] . "','" . date("Y-m-d", strtotime($params['arqueo_at'])) . "','" . $params['descripcionPartida'] . "'," . $idTipoOperacionPartida . "," . $idUsuarios . "," . $idEmpresas . ",'" . $this->timestamp . "')";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //$this->savePartidaDetalle(mysql_insert_id(), $params['detalle']);
            $admin = new Admin();
            $updateCorrelativo = $admin->updateCorrelativoPartidas($documento['idDocumento'], $documento['numero'], $idEmpresas);
            $response[] = array('message' => 'success', 'idPartida' => mysql_insert_id());
        } else {
            $response[] = array('message' => 'failed', 'Query' => $sql);
        }
        return $response;
    }

    /** METODO INSERT ESTADO DE RESULTADOS
     * 
     */
    public function insertEstadoResultados($detalle, $periodo, $mes, $idEmpresas, $mesTxt) {
        $response = "";
        $numero = count($detalle);
        $i = 1;
        $sql2 = "delete from contEstadoResultados where periodo='" . $periodo . "' and mes='" . $mes . "' and idEmpresas=$idEmpresas;";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2 == true) {
            $sql = "insert into contEstadoResultados values";
            foreach ($detalle as $key => $value) {
                if ($i !== $numero) {
                    $sql .= "(null,'" . $periodo . "','" . $mes . "','" . $value['cuenta'] . "','" . $value['cuentaContable'] . "','" . $value['saldo4'] . "','" . $value['saldo3'] . "','" . $value['saldo2'] . "','" . $value['saldo1'] . "'," . $idEmpresas . "),";
                } else {
                    $sql .= "(null,'" . $periodo . "','" . $mes . "','" . $value['cuenta'] . "','" . $value['cuentaContable'] . "','" . $value['saldo4'] . "','" . $value['saldo3'] . "','" . $value['saldo2'] . "','" . $value['saldo1'] . "'," . $idEmpresas . ");";
                }
                $i ++;
            }
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                $response[] = array('message' => 'Cierre ' . $mesTxt . '-' . $periodo . ' para Estado de Resultados generado exitosamente');
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'No se encontraron movimientos en ' . $mesTxt . '-' . $periodo . ' en las cuentas para generar Estado de Resultados');
            }
        } else {
            $response[] = array('message' => 'failed step 1');
        }
        return $response;
    }

    public function getCentrosCosto($idEmpresas) {
        $this->resultado = null;
        $sql = "select * from centrosCosto where idEmpresas=" . $idEmpresas . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getMonedas
     * 
     */
    public function getMonedas($idEmpresas) {
        $this->resultado = null;
        $sql = "select * from monedas where idEmpresas=" . $idEmpresas . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

}
