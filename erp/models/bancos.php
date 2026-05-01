<?php

/**
 * Description of bancos
 *
 * @author Richard Sasvin
 */
require_once ("dbCon.php");
require_once ("general.php");
require_once ("admin.php");
require_once ("contabilidad.php");

class Bancos extends General {

    public function getCheque($idCheque) {
        $sql = "select 
                    a . *,
                    b.descripcion as formato,
                    d.numeroCuenta,
                    d.nombreCuenta,
                    d.idBancos,
                    d.saldoLibros,
                    d.saldoBanco,
                    date_format(a.fechaCheque,'%d-%m-%Y') as fechaCheque2,
                    if(a.fechaCobro='0000-00-00','',date_format(a.fechaCobro,'%d-%m-%Y')) as fechaCobro2,
                    (case `a`.`status`
                        when 1 then 'Generado'
                        when 2 then 'Impreso'
                        when 3 then 'Conciliado'
                        when 4 then 'Anulado'
                    end) AS `statusDocumento`
                from
                    cheques as a
                        left join
                    formatos as b ON (a.idFormatos = b.id)
                        left join
                    partidas as c ON (a.idPartidas = c.id)
                        left join
                    vw_cuentasBancarias as d ON (a.idCuentasBancarias = d.id)
                where
                    a.id =" . $idCheque . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

//
    public function getDepositos($idDeposito) {
        $sql = "select 
                    a . *,
                    b.descripcion as formato,
                    d.numeroCuenta,
                    d.nombreCuenta,
                    d.idBancos,
                    d.saldoLibros,
                    d.saldoBanco,
                    date_format(a.fechaDeposito,'%d-%m-%Y') as fechadeposito2,
                    (case `a`.`status`
                        when 1 then 'Generado'
                        when 2 then 'Impreso'
                        when 3 then 'Conciliado'
                        when 4 then 'Anulado'
                    end) AS `statusDocumento`
                from
                    depositos as a
                        left join
                    formatos as b ON (a.idFormatos = b.id)
                        left join
                    partidas as c ON (a.idPartidas = c.id)
                        left join
                    vw_cuentasBancarias as d ON (a.idCuentasBancarias = d.id)
                where
                    a.id=" . $idDeposito . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    //
    public function getNDBancos($idNotaDebito) {
        $sql = "select 
                    a . *,
                    b.descripcion as formato,
                    d.numeroCuenta,
                    d.nombreCuenta,
                    d.idBancos,
                    d.saldoLibros,
                    d.saldoBanco,
                    date_format(a.fechaND,'%d-%m-%Y') as fechaND,
                    (case `a`.`status`
                        when 1 then 'Generado'
                        when 2 then 'Impreso'
                        when 3 then 'Conciliado'
                        when 4 then 'Anulado'
                    end) AS `statusDocumento`
                from
                    notasDebitoBancos as a
                        left join
                    formatos as b ON (a.idFormatos = b.id)
                        left join
                    partidas as c ON (a.idPartidas = c.id)
                        left join
                    vw_cuentasBancarias as d ON (a.idCuentasBancarias = d.id)
                where
                    a.id =" . $idNotaDebito . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    //
    public function getNCBancos($idNotaCredito) {
        $sql = "select 
                    a . *,
                    b.descripcion as formato,
                    d.numeroCuenta,
                    d.nombreCuenta,
                    d.idBancos,
                    d.saldoLibros,
                    d.saldoBanco,
                    date_format(a.fechaNC,'%d-%m-%Y') as fechaNC,
                    (case `a`.`status`
                        when 1 then 'Generado'
                        when 2 then 'Impreso'
                        when 3 then 'Conciliado'
                        when 4 then 'Anulado'
                    end) AS `statusDocumento`
                from
                    notasCreditoBancos as a
                        left join
                    formatos as b ON (a.idFormatos = b.id)
                        left join
                    partidas as c ON (a.idPartidas = c.id)
                        left join
                    vw_cuentasBancarias as d ON (a.idCuentasBancarias = d.id)
                where
                    a.id =" . $idNotaCredito . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    //
    public function getCuentasBancarias($idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.id,
                    concat(b.descripcion,'-',numeroCuenta,'-',nombreCuenta) as cuenta
                FROM
                    cuentasBancarias AS a
                        INNER JOIN
                    bancos AS b ON (a.idBancos = b.id)
                WHERE 
                    a.idEmpresas=" . $idEmpresas . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO guardarCheque
     *
     */
    public function guardarCheque($params) {
        $admin = new Admin();
        $response = "";
        //Validar que numero de cheque no este ingresado
        $sqlV = "SELECT id FROM cheques WHERE idCuentasBancarias=" . $params['idCuentaBancaria'] . " and noCheque='" . $params['noCheque'] . "';";
        $queryV = mysql_query($sqlV, dbCon::conPrincipal());
        $regV = mysql_fetch_assoc($queryV);
        if ($regV['id'] === null) {
            $sql = "insert into cheques values(null,'" . $params['idCuentaBancaria'] . "','" . $params['idTipoDocumentoBanco'] . "','" . $params['correlativo'] . "','" . $params['noCheque'] . "','" . date("Y-m-d", strtotime($params['fechaCheque'])) . "','" . $params['nombreCheque'] . "','" . $params['monto'] . "','" . $params['montoEnLetras'] . "','" . $params['motivo'] . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "','" . $params['noNegociable'] . "','0000-00-00','" . $params['idFormato'] . "', 0,1);";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                $idCheque = mysql_insert_id();
                $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros-" . $params['monto'] . ") "
                        . "where id='" . $params['idCuentaBancaria'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if ($query2 == true) {
                    //GUARDAR PARTIDA AUTOMATICA
                    $params['modulo'] = 'cheque';
                    $params['idDocumento'] = $idCheque;
                    $params['partida_at'] = $params['fechaCheque'];
                    $params['idTipoOperacionPartida'] = 2;
                    $params['concepto'] = $params['motivo'];
                    if ($params['idFormato'] != "") {
                        $this->savePartidaAutomatica($params);
                    }
                    //ACTUALIZAR CXP
                    if ($params['idProveedores'] != "") {
                        $this->actualizarCXP($params);
                    }
                    //AGREGARLE CHEQUE A CAJA CHICA
                    if ($params['idCajaChica'] != "") {
                        $this->addChequeCajaChica($idCheque, $params['idCajaChica'], $params['idTipoLiquidaciones'], $params['monto'], $params['moduloLiquidaciones']);
                    }
                    //ACTUALIZA CORRELATIVO COMPRA
                    $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
                    $response[] = array('message' => 'success', 'idCheque' => $idCheque);
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step 2', 'query' => $error);
                }
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step 1', 'query' => $error);
            }
        } else {
            $response[] = array('message' => 'docExists');
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
            //ACTUALIZACION DE DOCUMENTO
            switch ($params['modulo']) {
                case 'cheque':
                    $sql3 = "update cheques set idPartidas=" . $idPartida . " where id=" . $params['idDocumento'] . ";";
                    $query3 = mysql_query($sql3, dbCon::conPrincipal());
                    if ($query3 == true) {
                        error_log('update ' . $params['modulo'] . ' idDocumento= ' . $params['idDocumento'] . '');
                    } else {
                        error_log('error update ' . $params['modulo'] . ' idDocumento= ' . $params['idDocumento'] . ' Query: ' . $sql3);
                    }
                    break;
                case 'deposito':
                    $sql3 = "update depositos set idPartidas=" . $idPartida . " where id=" . $params['idDocumento'] . ";";
                    $query3 = mysql_query($sql3, dbCon::conPrincipal());
                    if ($query3 == true) {
                        error_log('update ' . $params['modulo'] . ' idDocumento= ' . $params['idDocumento'] . '');
                    } else {
                        error_log('error update ' . $params['modulo'] . ' idDocumento= ' . $params['idDocumento'] . ' Query: ' . $sql3);
                    }
                    break;
                case 'notaDebito':
                    $sql3 = "update notasDebitoBancos set idPartidas=" . $idPartida . " where id=" . $params['idDocumento'] . ";";
                    $query3 = mysql_query($sql3, dbCon::conPrincipal());
                    if ($query3 == true) {
                        error_log('update ' . $params['modulo'] . ' idDocumento= ' . $params['idDocumento'] . '');
                    } else {
                        error_log('error update ' . $params['modulo'] . ' idDocumento= ' . $params['idDocumento'] . ' Query: ' . $sql3);
                    }
                    break;
                case 'notaCredito':
                    $sql3 = "update notasCreditoBancos set idPartidas=" . $idPartida . " where id=" . $params['idDocumento'] . ";";
                    $query3 = mysql_query($sql3, dbCon::conPrincipal());
                    if ($query3 == true) {
                        error_log('update ' . $params['modulo'] . ' idDocumento= ' . $params['idDocumento'] . '');
                    } else {
                        error_log('error update ' . $params['modulo'] . ' idDocumento= ' . $params['idDocumento'] . ' Query: ' . $sql3);
                    }
                    break;
            }
            error_log('success create partida id: ' . $idPartida . '');
        } else {
            $error = mysql_error();
            error_log('error create partida error: ' . $error . '');
        }
    }

    /** METODO partidaDetalle
     * 
     */
    public function savePartidaDetalleAutomatica($idPartida, $params) {
        $conta = new Contabilidad();
        error_log('idFormato: ' . $params['idFormato']);
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
            $i++;
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            error_log('insert true detalle partida # ' . $idPartida);
        } else {
            error_log('error sql: ' . $sql);
        }
    }

    public function cxp($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    id as idCompras,
                    concat(serieFactura,'-',noFactura) as factura,
                    fechaFactura,
                    total as valorFactura,
                    saldo,
                    date_format(updated_at,'%d-%m-%Y') as fechaUltimoAbono
                FROM
                    compras
                WHERE
                    idProveedores=" . $params['idProveedores'] . " and idEmpresas=" . $params['idEmpresas'] . " and saldo > 0 ORDER BY fechaFactura;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    //cxc
    public function cxc($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    id as idVenta,
                    concat(serie,'-',correlativo) as factura,
                    fechaFactura,
                    total as valorFactura,
                    saldo,
                    ifnull(date_format(updated_at,'%d-%m-%Y'),'') as fechaUltimoAbono
                FROM
                    ventas
                WHERE
                    idClientes = " . $params['idClientes'] . "
                    and idEmpresas=" . $params['idEmpresas'] . "
                    and total > 0
                    and saldo > 0
                    and anulacion=0
                ORDER BY fechaFactura;";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO actualizarCXP
     * 
     */
    public function actualizarCXP($params) {
        $response = "";
        $sql = "";
        foreach ($params['facturas'] as $key => $value) {
            $saldoTotal = 0;
            //OBTIENE EL SALDO DE LAS FACTURAS POR NIT
            $sql = "select saldo from cxp where idProveedores=" . $params['idProveedores'] . " order by id desc limit 1;";
            $query = mysql_query($sql, dbCon::conPrincipal());
            $reg = mysql_fetch_assoc($query);
            if ($reg['saldo'] !== '') {
                $saldoTotal = $reg['saldo'];
            }
            //ACTUALIZACION DE SALDO DE FACTURAS
            $sql2 = "update compras set saldo=(saldo-" . $value['abono'] . "), updated_at='" . date("Y-m-d", strtotime($params['fechaCheque'])) . "'"
                    . "where id='" . $value['idCompras'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                //INSERTA EN CXC PARA GUARDAR REGISTRO EN ESTADO DE CUENTA
                $sql3 = "insert into cxp(idProveedores,idCompras,idTipoDocumento,idDocumento,facLiquidadas,creditos,debitos,saldo,created_at,idUsuarios,idEmpresas)"
                        . "values(" . $params['idProveedores'] . "," . $value['idCompras'] . ",'" . $params['idTipoDocumento'] . "','" . ($params['noCheque'] ?: $params['correlativo']) . "','" . $value['factura'] . "','0.00'," . $value['abono'] . "," . ($saldoTotal - $value['abono']) . ",'" . date("Y-m-d", strtotime($params['fechaCheque'])) . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ");";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if ($query3) {
                    error_log('cxp actualizada');
                } else {
                    $response[] = array('message' => mysql_error(), 'query' => $sql3);
                    return $response;
                }
            } else {
                error_log('error al actualizar cxp: ' . $sql2 . ' ' . mysql_error());
            }
        }
        $admin = new Admin();
        $correlativo = explode('-', $params['correlativo']);
        $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $correlativo[1], $params['idEmpresas']);
        $response[] = array('message' => 'success');
        return $response;
    }

    public function actualizarCXC($params) {
        $response = "";
        $sql = "";
        foreach ($params['facturas'] as $key => $value) {
            $saldoTotal = 0;
            //OBTIENE EL SALDO DE LAS FACTURAS POR NIT
            $sql = "select saldo from cxc where idClientes=" . $params['idClientes'] . " order by id desc limit 1;";
            $query = mysql_query($sql, dbCon::conPrincipal());
            $reg = mysql_fetch_assoc($query);
            if ($reg['saldo'] !== '') {
                $saldoTotal = $reg['saldo'];
            }
            //ACTUALIZACION DE SALDO DE FACTURAS
            $sql2 = "update ventas set saldo=(saldo-" . $value['abono'] . "), updated_at='" . date("Y-m-d H:i:s", strtotime($params['created_at'])) . "'"
                    . "where id='" . $value['idVentas'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                //INSERTA EN CXC PARA GUARDAR REGISTRO EN ESTADO DE CUENTA
                $sql3 = "insert into cxc(idClientes,idVentas,idTipoDocumento,idDocumento,facLiquidadas,creditos,debitos,saldo,created_at,idUsuarios,idEmpresas,chequeNo,nombreBanco,valor,fechaCobro,concepto,montoEnLetras)"
                        . "values(" . $params['idClientes'] . "," . $value['idVentas'] . ",'" . $params['idTipoDocumento'] . "','" . ($params['noBoleta'] ?: $params['correlativo']) . "','" . $value['factura'] . "','0.00'," . $value['abono'] . "," . ($saldoTotal - $value['abono']) . ",'" . date("Y-m-d", strtotime($params['created_at'])) . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $params['chequeNo'] . "','" . $params['nombreDelBanco'] . "','" . $params['valor'] . "','" . $params['fechaCobro'] . "','" . $params['motivo'] . "','" . $params['montoEnLetras'] . "');";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                $idRecibos = mysql_insert_id();
                if ($query3) {
                    error_log('cxc actualizada');
                } else {
                    
                    $response[] = array('message' => mysql_error(), 'query' => $sql3);
                    return $response;
                }
            } else {
                error_log('error al actualizar cxc: ' . $sql2 . ' ' . mysql_error());
            }
        }
        $admin = new Admin();
        $correlativo = explode('-', $params['correlativo']);
        $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $correlativo[1], $params['idEmpresas']);
        $response[] = array('message' => 'success', 'idRecibos' => $idRecibos);
        return $response;
    }

    /** METODO guardarDeposito
     *
     */
    public function guardarDeposito($params) {
        $admin = new Admin();
        $response = "";
        $sql = "insert into depositos values(null,'" . $params['idCuentaBancaria'] . "','" . $params['idTipoDocumentoD'] . "','" . $params['correlativo'] . "','" . $params['noBoleta'] . "','" . date("Y-m-d", strtotime($params['fechaDeposito'])) . "','" . $params['nombreDeposito'] . "','" . $params['monto'] . "','" . $params['montoEnLetras'] . "','" . $params['motivo'] . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "','" . $params['idFormato'] . "', 0,1);";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $idDeposito = mysql_insert_id();
            $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros+" . $params['monto'] . ")"
                    . "where id='" . $params['idCuentaBancaria'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                //GUARDAR PARTIDA AUTOMATICA
                $params['modulo'] = 'deposito';
                $params['idDocumento'] = $idDeposito;
                $params['partida_at'] = $params['fechaDeposito'];
                $params['idTipoOperacionPartida'] = 1;
                $params['idtipoDocumento'] = 1;
                $params['concepto'] = $params['motivo'];
                if ($params['idFormato'] !== "") {
                    $this->savePartidaAutomatica($params);
                }
                //ACTUALIZAR CXC
                if ($params['idClientes'] !== "") {
                    $this->actualizarCXC($params);
                }
                //ACTUALIZA CORRELATIVO DOCUMENTO
                $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
                $response[] = array('message' => 'success', 'idDeposito' => $idDeposito);
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step 2', 'error' => $error);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step 1', 'error' => $error);
        }
        return $response;
    }

    /** METODO guardarNDBancos
     *
     */
    public function guardarNDBancos($params) {
        $admin = new Admin();
        $response = "";
        $sql = "insert into notasDebitoBancos values(null,'" . $params['idCuentaBancaria'] . "','" . $params['idTipoDocumento'] . "','" . $params['correlativo'] . "','" . $params['noNotaDebito'] . "','" . date("Y-m-d", strtotime($params['fechaND'])) . "','" . $params['nombrePagoND'] . "','" . $params['monto'] . "','" . $params['montoEnLetras'] . "','" . $params['motivo'] . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "',null,'" . $params['idFormato'] . "', 0,1);";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $idNDBancos = mysql_insert_id();
            $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros-" . $params['monto'] . "),saldoBanco=(saldoBanco-" . $params['monto'] . ")"
                    . "where id='" . $params['idCuentaBancaria'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                //GUARDAR PARTIDA AUTOMATICA
                $params['modulo'] = 'notaDebito';
                $params['idDocumento'] = $idNDBancos;
                $params['partida_at'] = $params['fechaND'];
                $params['idTipoOperacionPartida'] = 2;
                $params['idTipoDocumento'] = 2;
                $params['concepto'] = $params['motivo'];
                $this->savePartidaAutomatica($params);
                //ACTUALIZAR CXP
                if ($params['idProveedores'] !== "") {
                    $this->actualizarCXP($params);
                }
                //ACTUALIZA CORRELATIVO DOCUMENTO
                $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
                $response[] = array('message' => 'success', 'idNDBancos' => $idNDBancos);
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step 2', 'error' => $error);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step 1', 'error' => $error);
        }
        return $response;
    }

    /** METODO guardarNCBancos
     *
     */
    public function guardarNCBancos($params) {
        $admin = new Admin();
        $response = "";
        $sql = "insert into notasCreditoBancos values(null,'" . $params['idCuentaBancaria'] . "','" . $params['idTipoDocumento'] . "','" . $params['correlativo'] . "','" . $params['noNotaCredito'] . "','" . date("Y-m-d", strtotime($params['fechaNC'])) . "','" . $params['nombrePagoNC'] . "','" . $params['monto'] . "','" . $params['montoEnLetras'] . "','" . $params['motivo'] . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "',null,'" . $params['idFormato'] . "', 0,1);";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $idNCBancos = mysql_insert_id();
            $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros+" . $params['monto'] . "),saldoBanco=(saldoBanco+" . $params['monto'] . ")"
                    . "where id='" . $params['idCuentaBancaria'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                //GUARDAR PARTIDA AUTOMATICA
                if ($params['idFormato'] != "") {
                    $params['modulo'] = 'notaCredito';
                    $params['idDocumento'] = $idNCBancos;
                    $params['partida_at'] = $params['fechaNC'];
                    $params['idTipoOperacionPartida'] = 1;
                    $params['idtipoDocumento'] = 2;
                    $params['concepto'] = $params['motivo'];
                    $this->savePartidaAutomatica($params);
                }
                // ACTUALIZAR CXC
                if ($params['idClientes'] != "") {
                    $this->actualizarCXC($params);
                }
                //ACTUALIZA CORRELATIVO DOCUMENTO
                $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $params['correlativo'], $params['idEmpresas']);
                $response[] = array('message' => 'success', 'idNCBancos' => $idNCBancos);
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step 2', 'error' => $error);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step 1', 'error' => $error);
        }
        return $response;
    }

    /** METODO impresionCheque
     *
     */
    public function impresionCheque($params) {
        $response = "";
        $sql = "update cheques set status='2' where id=" . $params['idCheque'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed', 'error' => $error);
        }
        return $response;
    }

    /** METODO anularCheque
     *
     */
    public function anularCheque($params) {
        $response = "";
        //PASO 1 CAMBIO DE ESTATUS DE CHEQUE
        $sql = "update cheques set status='4' where id=" . $params['idCheque'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //PASO 2 REGRESO DE MONTO DE CHEQUE A SALDO EN LIBROS
            $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros+" . $params['monto'] . ")"
                    . "where id='" . $params['idCuentaBancaria'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                //PASO 3 ELIMINAR PARTIDA CONTABLE
                $sql3 = "delete from partidas where id=" . $params['idPartida'] . ";";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if ($query3 == true) {
                    $response[] = array('message' => 'success');
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step3', 'error' => $error);
                }
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

    /** METODO conciliarCheque
     *
     */
    public function conciliarCheque($params) {
        $response = "";
        //PASO 1 CAMBIO DE ESTATUS DE CHEQUE
        $sql = "update cheques set fechaCobro='" . date("Y-m-d", strtotime($params['fechaCobro'])) . "',status='3' where id=" . $params['idCheque'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //PASO 2 INGRESAR DE MONTO DE CHEQUE A SALDO EN BANCO
            $sql2 = "update cuentasBancarias set saldoBanco=(saldoBanco+" . $params['monto'] . ")"
                    . "where id='" . $params['idCuentaBancaria'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                $response[] = array('message' => 'success');
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

    /** METODO eliminarCheque
     *
     */
    public function eliminarCheque($params) {
        $response = "";
        //PASO 1. OBTENER DATOS CHEQUE
        $sql1 = "select * from cheques where id=" . $params['idCheque'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        //PASO 2. REVERTIR SALDO EN LIBROS EN CUENTA BANCARIA
        $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros+" . $reg1['monto'] . ")"
                . "where id='" . $reg1['idCuentasBancarias'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2) {
            //PASO 3. CONSULTAR SALDO ABONADO EN FACTURAS CXP
            $sql3 = "select facLiquidadas,sum(debitos) as abono from cxp where idDocumento='" . $reg1['noCheque'] . "' group by facLiquidadas;";
            $query3 = mysql_query($sql3, dbCon::conPrincipal());
            while ($reg3 = mysql_fetch_assoc($query3)) {
                //PASO 4. REVERTIR ABONO EN COMPRAS
                $sql4 = "update compras set saldo=(saldo+" . $reg3['abono'] . "),updated_at='0000-00-00 00:00:00' where concat(serieFactura,'-',noFactura)='" . $reg3['facLiquidadas'] . "';";
                $query4 = mysql_query($sql4, dbCon::conPrincipal());
            }
            //PASO 5. ELIMINAR REGISTROS DE CXP
            $sql5 = "delete from cxp where idDocumento='" . $reg1['noCheque'] . "';";
            $query5 = mysql_query($sql5, dbCon::conPrincipal());
            if ($query5) {
                //PASO 6. ELIMINAR CHEQUE
                $sql6 = "delete from cheques where id=" . $params['idCheque'] . ";";
                $query6 = mysql_query($sql6, dbCon::conPrincipal());
                if ($query6) {
                    //PASO 7. ELIMINAR PARTIDA CONTABLE
                    $sql7 = "delete from partidas where id=" . $reg1['idPartidas'] . ";";
                    $query7 = mysql_query($sql7, dbCon::conPrincipal());
                    if ($query7) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed step7', 'error' => $error);
                    }
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step6', 'error' => $error);
                }
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step5', 'error' => $error);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step1', 'error' => $error);
        }
        return $response;
    }

    public function getFacturasCxp($params) {
        $this->resultado = null;
        $sql = "select c.noFactura as factura,
	               c.conceptoCompra as concepto,
                       c.fechaPago,
                       cxp.saldo,
                       cxp.abono
                       from cxp  inner join compras as c
                       on cxp.idCompras=c.id where idDocumento=" . $params['idDocumento'] . " and cxp.idEmpresas=" . $params['idEmpresas'] . "";
        echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    public function getFacturasCxc($params) {
        $this->resultado = null;
        $sql = "select CONCAT(v.serie,'-',v.correlativo) as factura,
	               v.conceptoVenta as concepto,
                       v.fechaFactura as fecha,
                       cxc.saldo,
                       cxc.abono
                       from cxc  inner join ventas as v
                       on cxc.idVentas=v.id where idDocumento=" . $params['idDocumento'] . " and cxc.idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    //
    public function getVentas() {
        $this->resultado = null;
        $sql = "SELECT 
                    nit,nombre,idClientes
                FROM
                    ventas
                WHERE
                    nit!=''
                group by 
                    idClientes;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    //
    public function saldosAcumuladosCXC($nit) {
        $response = "";
        $sql = "set @csum := 0;
                insert into cxc
                SELECT 
                    null,
                    clientes.id,
                    ventas.id,
                    3,
                    concat(serie,'-',correlativo) as idDocumentos,
                    null as facLiquidadas,
                    total,
                    0.00,
                    (@csum := @csum + total) as saldo,
                    fechaFactura,
                    1,
                    1
                FROM
                    ventas 
                    inner join 
                    clientes ON (ventas.idClientes = clientes.id)
                where
                    ventas.idClientes='" . $nit . "'
                order by fechaFactura;";
        echo $sql;
        echo '<br/>';
    }

    /** METODO eliminarDeposito
     *
     */
    public function eliminarDeposito($params) {
        $response = "";
        //PASO 1. OBTENER DATOS DEL DEPOSITO
        $sql1 = "select * from depositos where id=" . $params['idDeposito'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        //PASO 2. REVERTIR SALDO EN LIBROS EN CUENTA BANCARIA
        $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros-" . $reg1['monto'] . ")"
                . "where id='" . $reg1['idCuentasBancarias'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2) {
            //PASO 3. CONSULTAR SALDO ABONADO EN FACTURAS CXC
            $sql3 = "select facLiquidadas,sum(debitos) as abono from cxc where idDocumento='" . $reg1['noBoleta'] . "' group by facLiquidadas;";
            $query3 = mysql_query($sql3, dbCon::conPrincipal());
            while ($reg3 = mysql_fetch_assoc($query3)) {
                //PASO 4. REVERTIR ABONO EN VENTAS
                $sql4 = "update ventas set saldo=(saldo+" . $reg3['abono'] . "),updated_at='0000-00-00 00:00:00' where concat(serie,'-',correlativo)='" . $reg3['facLiquidadas'] . "';";
                $query4 = mysql_query($sql4, dbCon::conPrincipal());
            }
            //PASO 5. ELIMINAR REGISTROS DE CXC
            $sql5 = "delete from cxc where idDocumento='" . $reg1['noBoleta'] . "';";
            $query5 = mysql_query($sql5, dbCon::conPrincipal());
            if ($query5) {
                //PASO 6. ELIMINAR DEPOSITO
                $sql6 = "delete from depositos where id=" . $params['idDeposito'] . ";";
                $query6 = mysql_query($sql6, dbCon::conPrincipal());
                if ($query6) {
                    //PASO 7. ELIMINAR PARTIDA CONTABLE
                    $sql7 = "delete from partidas where id=" . $reg1['idPartidas'] . ";";
                    $query7 = mysql_query($sql7, dbCon::conPrincipal());
                    if ($query7) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed step7', 'error' => $error);
                    }
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step6', 'error' => $error);
                }
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step5', 'error' => $error);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step1', 'error' => $error);
        }
        return $response;
    }

    /** METODO eliminarNDBancos
     *
     */
    public function eliminarNDBancos($params) {
        $response = "";
        //PASO 1. OBTENER DATOS DEL DOCUMENTO
        $sql1 = "select * from notasDebitoBancos where id=" . $params['idNDBancos'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        //PASO 2. REVERTIR SALDO EN LIBROS EN CUENTA BANCARIA
        $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros+" . $reg1['monto'] . "),saldoBanco=(saldoBanco+" . $reg1['monto'] . ")"
                . "where id='" . $reg1['idCuentasBancarias'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2) {
            //PASO 3. ELIMINAR PARTIDA CONTABLE
            if ($reg1['idPartidas'] !== 0) {
                $sql3 = "delete from partidas where id=" . $reg1['idPartidas'] . ";";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if ($query3) {
                    //PASO 4. ELIMINAR DOCUMENTO
                    $sql4 = "delete from notasDebitoBancos where id=" . $params['idNDBancos'] . ";";
                    $query4 = mysql_query($sql4, dbCon::conPrincipal());
                    if ($query4) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed step4', 'error' => $error);
                    }
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step3', 'error' => $error);
                }
            } else {
                //PASO 4. ELIMINAR DOCUMENTO
                $sql4 = "delete from notasDebitoBancos where id=" . $params['idNDBancos'] . ";";
                $query4 = mysql_query($sql4, dbCon::conPrincipal());
                if ($query4) {
                    $response[] = array('message' => 'success');
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step4', 'error' => $error);
                }
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step2', 'error' => $error);
        }
        return $response;
    }

    /** METODO eliminarNCBancos
     *
     */
    public function eliminarNCBancos($params) {
        $response = "";
        //PASO 1. OBTENER DATOS DEL DOCUMENTO
        $sql1 = "select * from notasCreditoBancos where id=" . $params['idNCBancos'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        //PASO 2. REVERTIR SALDO EN LIBROS EN CUENTA BANCARIA
        $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros-" . $reg1['monto'] . "),saldoBanco=(saldoBanco-" . $reg1['monto'] . ")"
                . "where id='" . $reg1['idCuentasBancarias'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2) {
            //PASO 3. ELIMINAR PARTIDA CONTABLE
            if ($reg1['idPartidas'] !== 0) {
                $sql3 = "delete from partidas where id=" . $reg1['idPartidas'] . ";";
                $query3 = mysql_query($sql3, dbCon::conPrincipal());
                if ($query3) {
                    //PASO 4. ELIMINAR DOCUMENTO
                    $sql4 = "delete from notasCreditoBancos where id=" . $params['idNCBancos'] . ";";
                    $query4 = mysql_query($sql4, dbCon::conPrincipal());
                    if ($query4) {
                        $response[] = array('message' => 'success');
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed step4', 'error' => $error);
                    }
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step3', 'error' => $error);
                }
            } else {
                //PASO 4. ELIMINAR DOCUMENTO
                $sql4 = "delete from notasCreditoBancos where id=" . $params['idNCBancos'] . ";";
                $query4 = mysql_query($sql4, dbCon::conPrincipal());
                if ($query4) {
                    $response[] = array('message' => 'success');
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step4', 'error' => $error);
                }
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step2', 'error' => $error);
        }
        return $response;
    }

    /** METODO updateNCBancos
     *
     */
    public function updateNCBancos($params) {
        $response = "";
        //PASO 1. OBTENER DATOS DEL DOCUMENTO
        $sql1 = "select * from notasCreditoBancos where id=" . $params['idNCBancos'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        $idPartida = $reg1['idPartidas'];
        //PASO 2. REVERTIR SALDO EN CUENTA BANCARIA
        $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros-" . $reg1['monto'] . "),saldoBanco=(saldoBanco-" . $reg1['monto'] . ")"
                . "where id='" . $reg1['idCuentasBancarias'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2) {
            //PASO 3. ACTUALIZAR DOCUMENTO
            $sql3 = "update notasCreditoBancos "
                    . "set fechaNC='" . date("Y-m-d", strtotime($params['fechaNC'])) . "',nombrePagoNC='" . $params['nombrePagoNC'] . "',monto='" . $params['monto'] . "',montoEnLetras='" . $params['montoEnLetras'] . "',motivo='" . $params['motivo'] . "',updated_at='" . $this->timestamp . "',idFormatos=" . $params['idFormato'] . ""
                    . " where id=" . $params['idNCBancos'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
            $query3 = mysql_query($sql3, dbCon::conPrincipal());
            if ($query3) {
                //PASO 4. ACTUALIZAR SALDO EN CUENTA BANCARIA
                $sql4 = "update cuentasBancarias set saldoLibros=(saldoLibros+" . $params['monto'] . "),saldoBanco=(saldoBanco+" . $params['monto'] . ")"
                        . "where id='" . $reg1['idCuentasBancarias'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
                $query4 = mysql_query($sql4, dbCon::conPrincipal());
                if ($query4) {
                    //GUARDAR PARTIDA AUTOMATICA
                    if ($params['idFormato'] != "" && $idPartida === '0') {
                        $params['modulo'] = 'notaCredito';
                        $params['idDocumento'] = $params['idNCBancos'];
                        $params['partida_at'] = $params['fechaNC'];
                        $params['idTipoOperacionPartida'] = 1;
                        $params['idtipoDocumento'] = 2;
                        $params['concepto'] = $params['motivo'];
                        $this->savePartidaAutomatica($params);
                    } else if ($params['idFormato'] != "" && $idPartida !== '0') {
                        // ACTUALIZACION DE PARTIDA ACTUAL    
                        $params['modulo'] = 'notaCredito';
                        $params['idDocumento'] = $params['idNCBancos'];
                        $params['partida_at'] = $params['fechaNC'];
                        $params['idTipoOperacionPartida'] = 1;
                        $params['idtipoDocumento'] = 2;
                        $params['concepto'] = $params['motivo'];
                        $params['idPartidas'] = $idPartida;
                        $this->updatePartidaAutomatica($params);
                    }
                    $response[] = array('message' => 'success');
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step4', 'error' => $error);
                }
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step3', 'error' => $sql3);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step2', 'error' => $error);
        }
        return $response;
    }

    /** METODO updatePartidaAutomatica
     * 
     */
    public function updatePartidaAutomatica($params) {
        $sql = "delete from partidasDetalle where idPartidas=" . $params['idPartidas'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query) {
            $this->savePartidaDetalleAutomatica($params['idPartidas'], $params);
        }
    }

    /** METODO updateNDBancos
     *
     */
    public function updateNDBancos($params) {
        $response = "";
        //PASO 1. OBTENER DATOS DEL DOCUMENTO
        $sql1 = "select * from notasDebitoBancos where id=" . $params['idNDBancos'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        $idPartida = $reg1['idPartidas'];
        //PASO 2. REVERTIR SALDO EN CUENTA BANCARIA
        $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros+" . $reg1['monto'] . "),saldoBanco=(saldoBanco+" . $reg1['monto'] . ")"
                . "where id='" . $reg1['idCuentasBancarias'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2) {
            //PASO 3. ACTUALIZAR DOCUMENTO
            $sql3 = "update notasDebitoBancos "
                    . "set fechaND='" . date("Y-m-d", strtotime($params['fechaND'])) . "',nombrePagoND='" . $params['nombrePagoND'] . "',monto='" . $params['monto'] . "',montoEnLetras='" . $params['montoEnLetras'] . "',motivo='" . $params['motivo'] . "',updated_at='" . $this->timestamp . "',idFormatos=" . $params['idFormato'] . ""
                    . " where id=" . $params['idNDBancos'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
            $query3 = mysql_query($sql3, dbCon::conPrincipal());
            if ($query3) {
                //PASO 4. ACTUALIZAR SALDO EN CUENTA BANCARIA
                $sql4 = "update cuentasBancarias set saldoLibros=(saldoLibros-" . $params['monto'] . "),saldoBanco=(saldoBanco-" . $params['monto'] . ")"
                        . "where id='" . $reg1['idCuentasBancarias'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
                $query4 = mysql_query($sql4, dbCon::conPrincipal());
                if ($query4) {
                    //GUARDAR PARTIDA AUTOMATICA
                    if ($params['idFormato'] != "" && $idPartida === '0') {
                        $params['modulo'] = 'notaDebito';
                        $params['idDocumento'] = $params['idNDBancos'];
                        $params['partida_at'] = $params['fechaND'];
                        $params['idTipoOperacionPartida'] = 1;
                        $params['idtipoDocumento'] = 2;
                        $params['concepto'] = $params['motivo'];
                        $this->savePartidaAutomatica($params);
                    } else if ($params['idFormato'] != "" && $idPartida !== '0') {
                        // ACTUALIZACION DE PARTIDA ACTUAL    
                        $params['modulo'] = 'notaDebito';
                        $params['idDocumento'] = $params['idNDBancos'];
                        $params['partida_at'] = $params['fechaND'];
                        $params['idTipoOperacionPartida'] = 1;
                        $params['idtipoDocumento'] = 2;
                        $params['concepto'] = $params['motivo'];
                        $params['idPartidas'] = $idPartida;
                        $this->updatePartidaAutomatica($params);
                    }
                    $response[] = array('message' => 'success');
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step4', 'error' => $error);
                }
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step3', 'error' => $sql3);
            }
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step2', 'error' => $error);
        }
        return $response;
    }

    /**
     * guardarCajaChica
     */
    public function guardarCajaChica($params) {
        $response = "";
        $sql = "insert into cajaChica values(null,'" . $params['correlativo'] . "'," . $params['tipoLiquidacion'] . ",'" . $params['descripcion'] . "','" . $params['entregadoA'] . "','" . $params['monto'] . "','0.00','" . $params['monto'] . "','" . $this->timestamp . "',null,1," . $params['idEmpresas'] . ",0,0);";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query) {
            $admin = new Admin();
            $correlativo = explode('-', $params['correlativo']);
            $updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $correlativo[1], $params['idEmpresas']);
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step1', 'error' => $sql . ' - ' . $error);
        }
        return $response;
    }

    /**
     * addChequeCajaChica
     */
    public function addChequeCajaChica($idCheque, $idCajaChica, $idTipoLiquidacion, $monto, $modulo) {
        $response = "";
        $sql = "";
        if ($idTipoLiquidacion == 1) {
            if ($modulo == 'cajaChicaReintegros') {
                $sql = "update cajaChica set idCheques=" . $idCheque . ",montoLiquidado=(montoLiquidado-" . $monto . "),montoSinLiquidar=(montoSinLiquidar+" . $monto . "),idStatusCajaChica=3,updated_at='" . $this->timestamp . "' where id=" . $idCajaChica . ";";
            } else {
                $sql = "update cajaChica set idCheques=" . $idCheque . ",monto='" . $monto . "',montoSinLiquidar='" . $monto . "',idStatusCajaChica=3,updated_at='" . $this->timestamp . "' where id=" . $idCajaChica . ";";
            }
        } else {
            $sql = "update cajaChica set idCheques=" . $idCheque . ",idStatusCajaChica=3,updated_at='" . $this->timestamp . "' where id=" . $idCajaChica . ";";
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query) {
            error_log('cheque id: ' . $idCheque . ' agregado exitosamente a idCajaChica: ' . $idCajaChica);
        } else {
            error_log('error al agregar cheque id: ' . $idCheque . ' agregado exitosamente a idCajaChica: ' . $idCajaChica . ' query: ' . $sql);
        }
        return $response;
    }

    /**
     * cerrarCajaChica
     */
    public function cerrarCajaChica($params) {
        $response = "";
        $sql = "update cajaChica set idStatusCajaChica=2,updated_at='" . $this->timestamp . "' where id=" . $params['idCajaChica'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query) {
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step', 'error' => $sql . ' - ' . $error);
        }
        return $response;
    }

    /**
     * abrirCajaChica
     */
    public function abrirCajaChica($params) {
        $response = "";
        $sql = "update cajaChica set idStatusCajaChica=3,updated_at='" . $this->timestamp . "' where id=" . $params['idCajaChica'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query) {
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => 'failed step', 'error' => $sql . ' - ' . $error);
        }
        return $response;
    }

    /** METODO actualizarCheque
     *
     */
    public function actualizarCheque($params) {
        $response = "";
        //Obtener los datos del cheque 
        $sqlC = "select * from cheques where id=" . $params['idCheques'] . ";";
        $queryC = mysql_query($sqlC, dbCon::conPrincipal());
        $regC = mysql_fetch_assoc($queryC);
        //Validar que numero de cheque no este ingresado
        $sqlV = "SELECT id,noCheque FROM cheques WHERE idCuentasBancarias=" . $params['idCuentaBancaria'] . " and noCheque='" . $params['noCheque'] . "';";
        $queryV = mysql_query($sqlV, dbCon::conPrincipal());
        $regV = mysql_fetch_assoc($queryV);
        if ($regC['noCheque'] === $regV['noCheque'] || $regV['id'] === null) {
            //Actualizar los datos del cheque
            $sql = "update cheques set noCheque='" . $params['noCheque'] . "',fechaCheque='" . date("Y-m-d", strtotime($params['fechaCheque'])) . "',nombreCheque='" . $params['nombreCheque'] . "',monto='" . $params['monto'] . "',montoEnLetras='" . $params['montoEnLetras'] . "',motivo='" . $params['motivo'] . "',noNegociables='" . $params['noNegociable'] . "',idFormatos=" . $params['idFormato'] . ",created_at=curdate() where id=" . $params['idCheques'] . ";";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                //Revertir el saldo anterior en cuentaBancaria
                $sql2 = "update cuentasBancarias set saldoLibros=(saldoLibros+" . $regC['monto'] . ") "
                        . "where id='" . $regC['idCuentaBancaria'] . "' and idEmpresas=" . $regC['idEmpresas'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if ($query2 == true) {
                    $sql3 = "update cuentasBancarias set saldoLibros=(saldoLibros-" . $params['monto'] . ") "
                            . "where id='" . $params['idCuentaBancaria'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
                    $query3 = mysql_query($sql3, dbCon::conPrincipal());
                    if ($query3 == true) {
                        //ELIMINAR PARTIDA ACTUAL SI PARTIDA AUTOMATICA NO SEA IGUAL A 0
                        if ($params['idFormato'] !== '0') {
                            $sql4 = "delete from partidas where id=" . $regC['idPartidas'] . ";";
                            $query4 = mysql_query($sql4, dbCon::conPrincipal());
                            if ($query4 == true) {
                                //GUARDAR PARTIDA AUTOMATICA
                                $params['modulo'] = 'cheque';
                                $params['idDocumento'] = $params['idCheques'];
                                $params['partida_at'] = $params['fechaCheque'];
                                $params['idTipoOperacionPartida'] = 2;
                                $params['concepto'] = $params['motivo'];
                                $this->savePartidaAutomatica($params);
                            }
                        }
                        $response[] = array('message' => 'success', 'idCheque' => $params['idCheques']);
                    } else {
                        $error = mysql_error();
                        $response[] = array('message' => 'failed step 3', 'query' => $error);
                    }
                } else {
                    $error = mysql_error();
                    $response[] = array('message' => 'failed step 2', 'query' => $error);
                }
                //Actualizar el saldo nuevo en cuentaBancaria
            } else {
                $error = mysql_error();
                $response[] = array('message' => 'failed step 1', 'query' => $error);
            }
        } else {
            $response[] = array('message' => 'docExists');
        }
        return $response;
    }

}
