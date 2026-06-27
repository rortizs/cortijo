<?php

/**
 * dynamic.class
 * @author Jonathan Juarez
 * @version 1.0 20140122
 */
require_once("dbCon.php");
require_once("general.php");

class Dynamic extends General {

    /** METODO GET DB TABLE
     * 
     */
    public function dbTables($dbC, $dbS) {
        $this->resultado = null;
        $sql = "select table_name,table_comment from `tables` where table_schema='" . $dbS . "' order by table_comment asc;";
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO TABLE STRUCTURE
     * 
     */
    public function tableStructure($dbC, $dbS, $table) {
        $filters = "";
        if ($table == 'productos') {
            $filters = " AND COLUMN_NAME NOT IN('cantidad','precioUnitario','totalCompra')";
        } else if ($table == 'usuarios') {
            $filters = " AND COLUMN_NAME NOT IN('pwd')";
        }
        $this->resultado = null;
        $sql = "SELECT 
                    `COLUMN_NAME`, `ORDINAL_POSITION`,`DATA_TYPE`,`CHARACTER_MAXIMUM_LENGTH`,`COLUMN_COMMENT`,`IS_NULLABLE`
                FROM 
                    `COLUMNS` 
                WHERE 
                    `TABLE_SCHEMA` = '" . $dbS . "' AND `TABLE_NAME` = '" . $table . "' AND COLUMN_NAME!='idEmpresas' " . $filters . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO DATA TABLE
     *  
     */
    public function dataTable($dbC, $table, $parametros) {
        $this->resultado = null;
        $params = "";
        if ($parametros == '') {
            $params = "";
        } else {
            $params = $parametros;
        }
        $sql = "SELECT * FROM " . $table . " " . $params . " ;";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /**
     * 
     */
    public function dataTableView($dbC, $table, $key, $val) {
        $this->resultado = null;
        $filters = "";
        if ($key != '' && $val != '') {
            $filters = " where " . $key . "=" . $val . "";
        }
        $sql = "SELECT * FROM " . $table . " " . $filters . ";";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO SAVE DATA
     * 
     */
    public function saveData($dbC, $table, $campos, $valores) {
        $fields = '';
        $values = '';
        for ($a = 0; $a < count($campos); $a++) {
            $fields .= $campos[$a] . ",";
        }
        //
        for ($b = 0; $b < count($valores); $b++) {
            //$values.="'" . $valores[$b] . "',";
            $values .= '"' . $valores[$b] . '",';
        }
        //
        $sql = "insert into " . $table . " (id," . substr($fields, 0, strlen($fields) - 1) . ") values(null," . substr($values, 0, strlen($values) - 1) . ");";
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        $lastId = mysql_insert_id();
        if ($query == true) {
            if ($table == 'empresas') {
                $this->addSucursalYBodega($lastId);
            }
            if ($table == 'productos') {
                //$this->presentacionProductoDefault($lastId);
                $this->habilitarProductoVenta($lastId);
                $this->productosCodigosAlternos($lastId);
                //$this->medidasDescargaProductoDefault($lastId);
            }
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Datos Ingresados Exitosamente</div>";
            return $message;
        } else {
            //$error = mysql_errno();
            $error = mysql_error();
            $errorMessage = $this->errorMessage($table, $error);
            $message = "<div class='alert alert-danger' role='alert' id='message'' style='float:right;'>Error: " . $error . "</div>";
            return $message;
        }
    }

    /** METODO DELETE RECORD
     * 
     */
    public function deleteRecord($dbC, $table, $id) {
        $sql = "delete from " . $table . " where id=" . $id . ";";
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        if ($query == true) {
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Datos Eliminados Exitosamente</div>";
            return $message;
        } else {
            $message = "<div class='alert alert-danger' role='alert' id='message'' style='float:right;'>Error: Al eliminar datos</div>";
            return $message;
        }
    }

    /** METODO GET RECORD
     * 
     */
    public function getRecord($dbC, $table, $field, $value) {
        $sql = "select * from " . $table . " where " . $field . "=" . $value . ";";
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO UPDATE RECORD
     * 
     */
    public function updateRecord($dbC, $table, $fieldsValues, $field, $value) {
        $sql = "update " . $table . " set " . $fieldsValues . " where " . $field . "=" . $value . ";";
        //echo $sql."<br/>";
        //exit();
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        if ($query == true) {
            if ($table == 'productos') {
                $this->habilitarProductoVenta($value);
                //$this->medidasDescargaProductoDefault($lastId);
            }
            if ($table == 'vales') {
                $this->actualizarCXCVale($value);
            }
            $message = "<div class='alert alert-success' role='alert' id='message' style='float:right;'>Datos Actualizados Exitosamente</div>";
            return $message;
        } else {
            $error = mysql_error();
            $errorMessage = $this->errorMessage($table, $error);
            $message = "<div class='alert alert-danger' role='alert' id='message'' style='float:right;'>Error: " . $error . "</div>";
            return $message;
        }
    }

    /** METODO RELATIONSHIP TABLE RECORD
     * 
     */
    public function relationshipTableValue($columnName, $table, $field, $fieldValue, $dbC) {
        $sql = "select " . $columnName . " from " . $table . " where " . $field . "=" . $fieldValue . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO GET DATA BY ID
     * 
     */
    public function getDataById($dbC, $table, $field, $value) {
        $this->resultado = null;
        $sql = "select * from " . $table . " where " . $field . "=" . $value . ";";
        $query = mysql_query($sql, dbCon::conDynamic($dbC));
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO CODIGOS TABLAS
     * 
     */
    public function codigos($table, $idEmpresas) {
        $sql = "select correlativo from documentos where nombre='" . $table . "' and idEmpresas=" . $idEmpresas . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_array($query);
        $noOrden = '';
        $preOrden = $reg['correlativo'] + 1;
        if (strlen($preOrden) == 1) {
            $noOrden = '00000' . $preOrden;
        } else if (strlen($preOrden) == 2) {
            $noOrden = '0000' . $preOrden;
        } else if (strlen($preOrden) == 3) {
            $noOrden = '000' . $preOrden;
        } else if (strlen($preOrden) == 4) {
            $noOrden = '00' . $preOrden;
        } else if (strlen($preOrden) == 5) {
            $noOrden = '0' . $preOrden;
        } else if (strlen($preOrden) == 6) {
            $noOrden = $preOrden;
        }
        $sql2 = "update documentos set correlativo='" . $noOrden . "' where nombre='" . $table . "' and idEmpresas=" . $idEmpresas . ";";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        $sql3 = "select prefijo,correlativo from documentos where nombre='" . $table . "' and idEmpresas=" . $idEmpresas . ";";
        $query3 = mysql_query($sql3, dbCon::conPrincipal());
        $reg3 = mysql_fetch_array($query3);
        return $reg3['prefijo'] . '-' . $reg3['correlativo'];
    }

    /** METODO CREAR SUCURSAL Y BODEGA
     * 
     */
    public function addSucursalYBodega($idEmpresas) {
        //Sucursal
        $sql = "insert into sucursales (descripcion,idEmpresas,created_at) values('SUCURSAL 1'," . $idEmpresas . ",'" . $this->timestamp . "')";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query) {
            $sql2 = "insert into bodegas (descripcion,idEmpresas,created_at) values('BODEGA 1'," . $idEmpresas . ",'" . $this->timestamp . "')";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
            if ($query2) {
                error_log('success addSucursalYBodega idEmpresa: ' . $idEmpresas);
            } else {
                error_log('Error en creacion de bodga Query: ' . $sql2);
            }
        } else {
            error_log('Error en creacion de sucursal Query: ' . $sql);
        }
    }

    /** METODO PARA INSERTAR PRESENTACION DE VENTA SEGUN UNIDAD DE DESCARGA DE INVENTARIO
     * 
     */
    public function presentacionProductoDefault($idProducto) {
        //CONSULTA EL PRODUCTO
        $sql = "SELECT 
                p.*,
                ifnull(m.descripcion,'sin medida') as medida
            FROM
                productos as p left join medidas as m on(p.idMedidas2=m.id)
            WHERE
                p.id =" . $idProducto . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_array($query);
        //INSERTA EN TABLA productosPresentaciones
        if ($reg['medida'] != 'sin medida') {
            $sql = "insert into productosPresentaciones values(null,'" . $reg['medida'] . "'," . $idProducto . "," . $reg['idMedidas2'] . ",'" . $reg['equivalente'] . "','" . $reg['precioPublico'] . "');";
            $query = mysql_query($sql, dbCon::conPrincipal());
        }
    }

    /** METODO PARA INSERTAR PRESENTACION DE VENTA SEGUN UNIDAD DE DESCARGA DE INVENTARIO
     * 
     */
    public function medidasDescargaProductoDefault($idProducto) {
        error_log('medidas de descarga');
        //CONSULTA EL PRODUCTO
        $sql = "SELECT 
                p.*,
                ifnull(m.descripcion,'sin medida') as medida
            FROM
                productos as p left join medidas as m on(p.idMedidas2=m.id)
            WHERE
                p.id =" . $idProducto . ";";
        error_log($sql);
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_array($query);
        //INSERTA EN TABLA productosMedidasDescarga
        if ($reg['medida'] != 'sin medida') {
            $sql = "insert into productosMedidasDescarga values(null," . $idProducto . "," . $reg['idMedidas2'] . ",'" . $reg['equivalente'] . "','" . $reg['precioCosto'] . "');";
            error_log($sql);
            $query = mysql_query($sql, dbCon::conPrincipal());
        }
    }

    /** UPDATE FILE IMG FICHA PACIENTE
     * 
     */
    public function updateImage($id, $img) {
        $sql = "update hrmEmpleados set image='" . $img . "' where id=" . $id . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
    }

    /** METODO GET FAMILIA NIVEL 2
     * 
     */
    public function getFamiliaNivel2($id) {
        $sql = "select descripcion from familiaNivel1 where id=" . $id . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg['descripcion'];
        }
    }

    /** METODO GET FAMILIA NIVEL 3
     * 
     */
    public function getFamiliaNivel3($id) {
        $sql = "select descripcion from familiaNivel2 where id=" . $id . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg['descripcion'];
        }
    }

    /** METODO HABILITAR PRODUCTO VENTA
     * 
     */
    public function habilitarProductoVenta($idProducto) {
        //Consulta si el producto esta habilitado para la venta en la tabla productos y el tipo de producto
        $sql1 = "select * from productos where id=" . $idProducto . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        if ($reg1['idAvailableSale'] == '1') {
            $sql = "SELECT 
                        2 AS ingresoA, id AS idPuntoIngreso
                    FROM
                        sucursales 
                    WHERE
                        idEmpresas = " . $reg1['idEmpresas'] . "    
                    UNION ALL SELECT 
                        1 AS ingresoA, id AS idPuntoIngreso
                    FROM
                        bodegas
                    WHERE
                        idEmpresas = " . $reg1['idEmpresas'] . ";";
            $query = mysql_query($sql, dbCon::conPrincipal());
            while ($reg = mysql_fetch_assoc($query)) {
                //Consulta si el producto ya este ingresado en los inventarios de bodegas y sucursales
                $sql2 = "select * from inventarios where idProductos=" . $idProducto . " and ingresoA=" . $reg['ingresoA'] . " and idPuntoIngreso=" . $reg['idPuntoIngreso'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                $reg2 = mysql_fetch_assoc($query2);
                //Si fuera asi solo termina el proceso
                //De lo contrario inserta el producto en el inventario segun el tipo de producto
                if ($reg2['idProductos'] == '') {
                    $sql3 = "insert into inventarios (ingresoA,idPuntoIngreso,documento,idTipoProductos,idProductos,idEmpresas,created_at)
                             values(" . $reg['ingresoA'] . "," . $reg['idPuntoIngreso'] . ",'CREACION PRODUCTO'," . $reg1['idTipoProductos'] . "," . $idProducto . "," . $reg1['idEmpresas'] . ",'" . $this->timestamp . "');";
                    //echo $sql."<br/>";
                    $query3 = mysql_query($sql3, dbCon::conPrincipal());
                }
            }
        }
    }

    //
    public function errorMessage($table, $error) {
        $response = "";
        switch ($error) {
            case 1062:
                switch ($table) {
                    case 'usuarios':
                        $response = "Codigo de usuario duplicado";
                        break;
                    case 'productos':
                        $response = "Codigo de producto duplicado";
                        break;
                    case 'documentosCorrelativos':
                        $response = "Serie y Correlativos duplicados en documento";
                        break;
                }
                break;
        }
        return $response;
    }

    /** METODO HABILITAR PRODUCTO VENTA
     * 
     */
    public function productosCodigosAlternos($idProducto) {
        //Consulta si el producto esta habilitado para la venta en la tabla productos y el tipo de producto
        $sql1 = "select * from productos where id=" . $idProducto . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        //
        $sql2 = "insert into productosCodigosAlternos (idProductos,sku,idEmpresas,created_at)
                 values(" . $reg1['id'] . ",'" . $reg1['sku'] . "'," . $reg1['idEmpresas'] . ",'" . $this->timestamp . "');";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2 == true) {
            error_log('codigo alterno creado exitosamente producto ' . $reg1['sku']);
        } else {
            $error = mysql_error();
            error_log('error en crear codigo alterno producto ' . $reg1['sku'] . ' ' . $error);
        }
    }

    /** METODO HABILITAR PRODUCTO VENTA
     * 
     */
    public function actualizarCXCVale($idVales) {
        //Consulta si el producto esta habilitado para la venta en la tabla productos y el tipo de producto
        $sql1 = "select * from vales where id=" . $idVales . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg1 = mysql_fetch_assoc($query1);
        //
        $vale = $reg1['serie'] . '-' . $reg1['documento'];
        $sql2 = "update cxc set creditos='" . $reg1['valorVale'] . "',created_at='" . $reg1['fechaVale'] . "' where idDocumento='" . $vale . "';";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        if ($query2 == true) {
            error_log('vale modificado en cxc exitosamente ' . $vale);
        } else {
            $error = mysql_error();
            error_log('error al modificar vale en cxc ' . $vale . ' ' . $error);
        }
    }

}
