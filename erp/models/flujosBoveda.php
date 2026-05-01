<?php

/**
 * Description of admin
 *
 * @author Richard Sasvin
 */
require_once ("dbCon.php");
require_once ("general.php");

class FlujosBoveda extends General {

    public function getAnos() {
        $this->resultado = null;
        $sql = "select * from anos;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET AÑOS
     * 
     */
    public function getMeses() {
        $this->resultado = null;
        $sql = "select * from meses;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET EMPRESAS
     *
     */
    public function getEmpresas() {
        $this->resultado = null;
        $sql = "select * from empresas;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET PROVEEDORES
     *
     */
    public function getProveedores($params) {
        $this->resultado = null;
        $sql = "select * from proveedores where id=" . $params['idProveedor'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET BODEGAS
     *
     */
    public function getBodegas($idEmpresa) {
        $this->resultado = null;
        $sql = "select * from bodegas where idEmpresas=" . $idEmpresa . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET SUCURSALES
     *
     */
    public function getSucursales($idEmpresa, $params) {
        $this->resultado = null;
        $filtros = "idEmpresas=" . $idEmpresa . "";
        if ($params['idEmpresaIngreso'] != "") {
            $filtros = "idEmpresas=" . $params['idEmpresaIngreso'] . "";
        }
        $sql = "select * from sucursales where " . $filtros . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET DOCUMENTOS
     * 
     */
    public function getDocumentos($param) {
        $this->resultado = null;
        $sql = "select 
                    a.id,a.prefijo,a.serie,a.resolucion
                from
                    documentosCorrelativos as a inner join documentos as b on(a.idDocumentos=b.id)
                where b.descripcion='" . $param['tipo'] . "' and a.idEmpresas=" . $param['idEmpresas'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET DOCUMENTOS
     * 
     */
    public function getDocumentosCorrelativo($param) {
        $this->resultado = null;
        $sql = "select 
                    correlativo,serie
                from
                    documentosCorrelativos where id=" . $param['idDocumento'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO updateCorrelativoFactura
     *
     */
    public function updateCorrelativoDocumento($prefijo, $correlativo, $idEmpresas) {
        if ($correlativo >= $correlativo) {
            $correlativo = $correlativo;
            $new = $correlativo + 1;
            $correlativo = str_pad($new, strlen($correlativo), '0', STR_PAD_LEFT);
            //
            $sql = "update documentosCorrelativos set correlativo='" . $correlativo . "' "
                    . "where prefijo='" . $prefijo . "' and idEmpresas=" . $idEmpresas . ";";
            //echo $sql;
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('correlativo actualizado exitosamente');
            } else {
                error_log('error al actualizar correlativo factura');
            }
        }
    }

    /** METODO GET CLIENTES
     *
     */
    public function getClientes($params) {
        $this->resultado = null;
        $sql = "select * from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET VENDEDORES
     *
     */
    public function getVendedores($params) {
        $this->resultado = null;
        $sql = "select * from usuarios where idRoles=12 and idSucursales=" . $params['idSucursales'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET EMISORES
     *
     */
    public function getEmisores() {
        $this->resultado = null;
        $sql = "select * from emisores;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET CAJEROS
     *
     */
    public function getCajeros($params) {
        $this->resultado = null;
        $sql = "select * from usuarios "
                . "where idRoles=3 and idSucursales=" . $params['idSucursales'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO ADMIN LOGIN
     * 
     */
    public function loginAdmin($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    usuarios
                WHERE 
                    user='" . $params['user'] . "' and pwd='" . md5($params['pwd']) . "' and idRoles in(1,2,13);";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET NOMENCLATURA
     *
     */
    public function getNomenclatura($params) {
        $this->resultado = null;
        $filter = "";
        if ($params['idNomenclatura'] !== '0') {
            $filter = " and id=" . $params['idNomenclatura'] . "";
        }
        $sql = "select * from nomenclatura where idEmpresas=" . $params['idEmpresas'] . " " . $filter . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getFormArqueoBoveda
     *
     */
    public function getFormArqueoBoveda($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.*,
                    ifnull(concat(b.cuenta,' - ',b.descripcion),'sin cuenta definida') as nomenclatura,
                    ifnull(concat(c.cuenta,' - ',c.descripcion),'sin centro definido') as centrosCosto
                FROM 
                    formArqueoBoveda as a 
                        left join 
                    nomenclatura as b on(a.idNomenclatura=b.id)
                        left join 
                    centrosCosto as c on(a.idCentrosCosto=c.id)
                WHERE
                    a.parent='" . $params['parent'] . "' and a.idSucursales=" . $params['idSucursales'] . " and a.idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET NOMENCLATURA
     *
     */
    public function getFormArqueoBovedaMesas($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.*,
                    ifnull(concat(b.cuenta,' - ',b.descripcion),'sin cuenta definida') as nomenclatura,
                    ifnull(concat(c.cuenta,' - ',c.descripcion),'sin centro definido') as centrosCosto
                FROM 
                    formArqueoBovedaMesas as a 
                        left join 
                    nomenclatura as b on(a.idNomenclatura=b.id)
                        left join 
                    centrosCosto as c on(a.idCentrosCosto=c.id)
                WHERE 
                    a.parent='" . $params['parent'] . "' and a.idSucursales=" . $params['idSucursales'] . " and a.idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO saveRowFormArqueoBoveda
     *
     */
    public function saveRowFormArqueoBoveda($params) {
        $response = "";
        $sql = "insert into formArqueoBoveda (parent,descripcion,idNomenclatura,idCentrosCosto,operacion,idSucursales,idEmpresas)
                values('" . $params['parent'] . "','" . $params['descripcion'] . "'," . $params['idNomenclatura'] . "," . $params['idCentrosCosto'] . ",'" . $params['operacion'] . "'," . $params['idSucursales'] . "," . $params['idEmpresas'] . ");";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $sql2 = "SELECT 
                        a.*,
                        ifnull(concat(b.cuenta,' - ',b.descripcion),'sin cuenta definida') as nomenclatura,
                        ifnull(concat(c.cuenta,' - ',c.descripcion),'sin centro definido') as centrosCosto
                    FROM 
                        formArqueoBoveda as a 
                            left join 
                        nomenclatura as b on(a.idNomenclatura=b.id)
                            left join 
                        centrosCosto as c on(a.idCentrosCosto=c.id)
                    WHERE 
                        a.parent='" . $params['parent'] . "' and a.id=" . mysql_insert_id() . " and a.idSucursales=" . $params['idSucursales'] . " and a.idEmpresas=" . $params['idEmpresas'] . ";";
            //$sql2 = "select * from formArqueoBoveda where id=" . mysql_insert_id() . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            $reg = mysql_fetch_assoc($query2);
            $response[] = array('message' => 'success', 'id' => $reg['id'], 'parent' => $reg['parent'], 'descripcion' => $reg['descripcion'], 'idNomenclatura' => $reg['idNomenclatura'], 'operacion' => $reg['operacion'], 'nomenclatura' => $reg['nomenclatura'], 'idCentrosCosto' => $reg['idCentrosCosto'], 'centrosCosto' => $reg['centrosCosto']);
        } else {
            $response[] = array('message' => 'failed', "error" => $sql);
        }
        return $response;
    }

    /** METODO saveRowFormArqueoBovedaMesas
     *
     */
    public function saveRowFormArqueoBovedaMesas($params) {
        $response = "";
        $sql = "insert into formArqueoBovedaMesas (parent,descripcion,idNomenclatura,idCentrosCosto,operacion,idSucursales,idEmpresas)
                values('" . $params['parent'] . "','" . $params['descripcion'] . "'," . $params['idNomenclatura'] . "," . $params['idCentrosCosto'] . ",'" . $params['operacion'] . "',".$params['idSucursales'].",".$params['idEmpresas'].");";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $sql2 = "SELECT 
                        a.*,
                        ifnull(concat(b.cuenta,' - ',b.descripcion),'sin cuenta definida') as nomenclatura,
                        ifnull(concat(c.cuenta,' - ',c.descripcion),'sin centro definido') as centrosCosto
                    FROM 
                        formArqueoBovedaMesas as a 
                            left join 
                        nomenclatura as b on(a.idNomenclatura=b.id)
                            left join 
                        centrosCosto as c on(a.idCentrosCosto=c.id)
                    WHERE 
                        a.parent='" . $params['parent'] . "' and a.id=" . mysql_insert_id() . " and a.idSucursales=" . $params['idSucursales'] . " and a.idEmpresas=" . $params['idEmpresas'] . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            $reg = mysql_fetch_assoc($query2);
            $response[] = array('message' => 'success', 'id' => $reg['id'], 'parent' => $reg['parent'], 'descripcion' => $reg['descripcion'], 'idNomenclatura' => $reg['idNomenclatura'], 'operacion' => $reg['operacion'], 'nomenclatura' => $reg['nomenclatura'], 'idCentrosCosto' => $reg['idCentrosCosto'], 'centrosCosto' => $reg['centrosCosto']);
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO updateRowFormArqueoBoveda
     *
     */
    public function updateRowFormArqueoBoveda($params) {
        $response = "";
        $sql = "update formArqueoBoveda set descripcion='" . $params['descripcion'] . "',idNomenclatura=" . $params['idNomenclatura'] . ",idCentrosCosto=" . $params['idCentrosCosto'] . ",operacion='" . $params['operacion'] . "'"
                . " where id=" . $params['id'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO updateRowFormArqueoBoveda
     *
     */
    public function updateRowFormArqueoBovedaMesas($params) {
        $response = "";
        $sql = "update formArqueoBovedaMesas set descripcion='" . $params['descripcion'] . "',idNomenclatura=" . $params['idNomenclatura'] . ",idCentrosCosto=" . $params['idCentrosCosto'] . ", operacion='" . $params['operacion'] . "'"
                . " where id=" . $params['id'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO deleteRowFormArqueoBoveda
     *
     */
    public function deleteRowFormArqueoBoveda($params) {
        $response = "";
        $sql = "delete from formArqueoBoveda where id=" . $params['id'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', mysqli_error(dbCon::conPrincipal()));
        }
        return $response;
    }

    /** METODO deleteRowFormArqueoBovedaMesas
     *
     */
    public function deleteRowFormArqueoBovedaMesas($params) {
        $response = "";
        $sql = "delete from formArqueoBovedaMesas where id=" . $params['id'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', mysqli_error(dbCon::conPrincipal()));
        }
        return $response;
    }

    /** METODO guardarArqueoMaquinas
     *
     */
    public function guardarArqueoMaquinas($params) {
        $response = "";
        //validar si ya existe un arqueo en el dia seleccionado
        $sql1 = "select * from arqueoBoveda where idSucursales=" . $params['idSalas'] . " and arqueo_at='" . date("Y-m-d", strtotime($params['fechaArqueo'])) . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        if ($reg1['id'] !== null) {
            $response[] = array('message' => 'exists', 'idArqueo' => $reg1['id']);
        } else {
            $sql = "insert into arqueoBoveda values(null," . $params['idSalas'] . ",'" . date("Y-m-d", strtotime($params['fechaArqueo'])) . "','" . $params['totalIngreso1'] . "','" . $params['totalIngreso2'] . "','" . $params['totalIngreso3'] . "','" . $params['totalIngreso4'] . "','" . $params['totalIngreso'] . "'," . $params['idEmpresas'] . ",'" . $this->timestamp . "')";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                $this->guardarArqueoMaquinasDetalle(mysql_insert_id(), $params['detalle'], '0');
                $response[] = array('message' => 'success');
            } else {
                $response[] = array('message' => 'failed');
            }
        }
        return $response;
    }

    /** METODO guardarArqueoMesas
     *
     */
    public function guardarArqueoMesas($params) {
        $response = "";
        //validar si ya existe un arqueo en el dia seleccionado
        $sql1 = "select * from arqueoBovedaMesas where idSucursales=" . $params['idSalas'] . " and arqueo_at='" . date("Y-m-d", strtotime($params['fechaArqueo'])) . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        if ($reg1['id'] !== null) {
            $response[] = array('message' => 'exists', 'idArqueo' => $reg1['id']);
        } else {
            $sql = "insert into arqueoBovedaMesas values(null," . $params['idSalas'] . ",'" . date("Y-m-d", strtotime($params['fechaArqueo'])) . "','" . $params['totalIngreso1'] . "','" . $params['totalIngreso2'] . "','" . $params['totalIngreso3'] . "','" . $params['totalIngreso4'] . "','" . $params['totalIngreso'] . "'," . $params['idEmpresas'] . ",'" . $this->timestamp . "')";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                $this->guardarArqueoMesasDetalle(mysql_insert_id(), $params['detalle'], '0');
                $response[] = array('message' => 'success');
            } else {
                $response[] = array('message' => 'failed');
            }
        }
        return $response;
    }

    /** METODO guardarArqueoMaquinasDetalle
     * 
     */
    public function guardarArqueoMaquinasDetalle($idArqueoBoveda, $detalle, $flag) {
        if ($flag == '1') {
            $sql2 = "delete from arqueoBovedaDetalle where idArqueoBoveda=" . $idArqueoBoveda . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                error_log('se borro el detalle anterior id: ' . $idArqueoBoveda);
            } else {
                error_log('error al borrar detalle anterior sql: ' . $sql2);
            }
        }
        foreach ($detalle as $key => $value) {
            $sql = "insert into arqueoBovedaDetalle values(null," . $idArqueoBoveda . "," . $value['idFormArqueoBoveda'] . ",'" . $value['valor'] . "');";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('success arqueoBovedaDetalle');
            } else {
                error_log('error sql: ' . $sql);
            }
        }
    }

    /** METODO guardarArqueoMesasDetalle
     * 
     */
    public function guardarArqueoMesasDetalle($idArqueoBoveda, $detalle, $flag) {
        if ($flag == '1') {
            $sql2 = "delete from arqueoBovedaMesasDetalle where idArqueoBoveda=" . $idArqueoBoveda . ";";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2 == true) {
                error_log('se borro el detalle anterior id: ' . $idArqueoBoveda);
            } else {
                error_log('error al borrar detalle anterior sql: ' . $sql2);
            }
        }
        foreach ($detalle as $key => $value) {
            $sql = "insert into arqueoBovedaMesasDetalle values(null," . $idArqueoBoveda . "," . $value['idFormArqueoBoveda'] . ",'" . $value['valor'] . "');";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('success arqueoBovedaMesasDetalle');
            } else {
                error_log('error sql: ' . $sql);
            }
        }
    }

    /** METODO saldosInicialesBovedas
     * 
     */
    public function saldosInicialesBovedas($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    idFormArqueoBoveda,
                    valor,
                    DATE_FORMAT(DATE_ADD(arqueo_at,INTERVAL 1 DAY),'%d-%m-%Y') as arqueo_at
                FROM
                    arqueoBovedaDetalle inner join arqueoBoveda on(arqueoBovedaDetalle.idArqueoBoveda=arqueoBoveda.id)
                WHERE
                    idFormArqueoBoveda IN (28 , 29, 30)
                AND idArqueoBoveda = (
                    SELECT 
                        id
                    FROM
                        arqueoBoveda
                    WHERE
                        idSucursales = " . $params['idSalas'] . "
                            AND idEmpresas = " . $params['idEmpresas'] . "
                    ORDER BY DATE(arqueo_at) DESC
                    LIMIT 1);";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO saldosInicialesBovedasMesas
     * 
     */
    public function saldosInicialesBovedasMesas($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    idFormArqueoBoveda, 
                    valor,
                    DATE_FORMAT(DATE_ADD(arqueo_at,INTERVAL 1 DAY),'%d-%m-%Y') as arqueo_at
                FROM
                    arqueoBovedaMesasDetalle inner join arqueoBovedaMesas on(arqueoBovedaMesasDetalle.idArqueoBoveda=arqueoBovedaMesas.id)
                WHERE
                    idFormArqueoBoveda IN (21 , 22, 23)
                    AND idArqueoBoveda = (
                        SELECT 
                            id
                        FROM
                            arqueoBovedaMesas
                        WHERE
                            idSucursales = " . $params['idSalas'] . "
                                AND idEmpresas = " . $params['idEmpresas'] . "
                        ORDER BY DATE(arqueo_at) DESC
                        LIMIT 1);";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO consultarCierre
     * 
     */
    public function consultarCierre($params) {
        $this->resultado = null;
        //GET id del arqueo segun dia y empresa
        $sql = "select * from arqueoBoveda where idSucursales=" . $params['idSalas'] . " and arqueo_at='" . date("Y-m-d", strtotime($params['fechaArqueo'])) . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        //GET detalle de arqueo
        $sql2 = "select 
                    a.*,b.parent,b.operacion
                from
                    arqueoBovedaDetalle as a inner join formArqueoBoveda as b on(a.idFormArqueoBoveda=b.id)
                where
                    idArqueoBoveda =" . $reg['id'] . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        while ($reg2 = mysql_fetch_assoc($query2)) {
            $this->resultado[] = $reg2;
        }
        return $this->resultado;
    }

    /** METODO consultarCierreMesas
     * 
     */
    public function consultarCierreMesas($params) {
        $this->resultado = null;
        //GET id del arqueo segun dia y empresa
        $sql = "select * from arqueoBovedaMesas where idSucursales=" . $params['idSalas'] . " and arqueo_at='" . date("Y-m-d", strtotime($params['fechaArqueo'])) . "' and idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        //GET detalle de arqueo
        $sql2 = "select 
                    a.*,b.parent,b.operacion
                from
                    arqueoBovedaMesasDetalle as a inner join formArqueoBovedaMesas as b on(a.idFormArqueoBoveda=b.id)
                where
                    idArqueoBoveda =" . $reg['id'] . ";";
        //echo $sql2;
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        while ($reg2 = mysql_fetch_assoc($query2)) {
            $this->resultado[] = $reg2;
        }
        return $this->resultado;
    }

    /** METODO updateArqueoMaquinas
     *
     */
    public function updateArqueoMaquinas($params) {
        $response = "";
        $sql = "update arqueoBoveda set totalIngreso1='" . $params['totalIngreso1'] . "',totalIngreso2='" . $params['totalIngreso2'] . "',totalIngreso3='" . $params['totalIngreso3'] . "',totalIngreso4='" . $params['totalIngreso4'] . "',totalIngreso='" . $params['totalIngreso'] . "'"
                . " where id=" . $params['idArqueoBoveda'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->guardarArqueoMaquinasDetalle($params['idArqueoBoveda'], $params['detalle'], '1');
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO updateArqueoMaquinas
     *
     */
    public function updateArqueoMesas($params) {
        $response = "";
        $sql = "update arqueoBovedaMesas set totalIngreso1='" . $params['totalIngreso1'] . "',totalIngreso2='" . $params['totalIngreso2'] . "',totalIngreso3='" . $params['totalIngreso3'] . "',totalIngreso4='" . $params['totalIngreso4'] . "',totalIngreso='" . $params['totalIngreso'] . "'"
                . " where id=" . $params['idArqueoBoveda'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $this->guardarArqueoMesasDetalle($params['idArqueoBoveda'], $params['detalle'], '1');
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO getCorrelativoPartidas
     * 
     */
    public function getCorrelativoPartidas($idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    correlativo,idDocumentos
                FROM
                    documentosCorrelativos as a inner join documentos as b on(a.idDocumentos=b.id)
                where 
                    modulo='PARTIDAS' and a.idEmpresas=" . $idEmpresas . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO updateCorrelativoFactura
     *
     */
    public function updateCorrelativoPartidas($idDocumento, $correlativo, $idEmpresas) {
        if ($correlativo >= $correlativo) {
            $correlativo = $correlativo;
            $new = $correlativo + 1;
            $correlativo = str_pad($new, strlen($correlativo), '0', STR_PAD_LEFT);
            //
            $sql = "update documentosCorrelativos set correlativo='" . $correlativo . "' "
                    . "where idEmpresas=" . $idEmpresas . " and idDocumentos=" . $idDocumento . ";";
            //echo $sql;
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                error_log('correlativo actualizado exitosamente');
            } else {
                error_log('error al actualizar correlativo factura');
            }
        }
    }

    /** METODO eliminarCierre
     *
     */
    public function eliminarCierre($params) {
        $sql = "";
        if ($params['flag'] == '1') {
            $sql = "delete from arqueoBoveda where id=" . $params['idArqueoBoveda'] . ";";
        } else {
            $sql = "delete from arqueoBovedaMesas where id=" . $params['idArqueoBoveda'] . ";";
        }
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed');
        }
        return $response;
    }

    /** METODO getAllCierresBovedas
     * 
     */
    public function getAllCierresBovedas($idEmpresas) {
        $this->resultado = null;
        $sql = "SELECT 
                    correlativo,idDocumentos
                FROM
                    documentosCorrelativos as a inner join documentos as b on(a.idDocumentos=b.id)
                where 
                    modulo='PARTIDAS' and a.idEmpresas=" . $idEmpresas . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

}
