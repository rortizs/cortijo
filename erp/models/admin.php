<?php

/**
 * Description of admin
 *
 * @author Richard Sasvin
 */
require_once "dbCon.php";
require_once "general.php";
require_once "email_.php";
require_once "agenciasViajes.php";

class Admin extends General {

	public function getConfPaisEmpresa($idEmpresas) {
		$this->resultado = null;
		$sql = "SELECT
                    b.*
                FROM
                    empresas AS a
                        LEFT JOIN
                    paises AS b ON (a.idPaises = b.id)
                WHERE
                    a.id = " . $idEmpresas . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	public function getTiposVenta() {
		$this->resultado = null;
		$sql = "select * from tipoVenta;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	public function getAnos() {
		$this->resultado = null;
		$sql = "select * from anos order by descripcion asc;";
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
	public function getEmpresas($params) {
		$this->resultado = null;
		$filter = "";
		if ($params['idEmpresas'] !== '') {
			$filter = " where id=" . $params['idEmpresas'] . "";
		}
		$sql = "select * from empresas " . $filter . ";";
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
		$sql = "select id,nitP,descripcion,direccionP,diasCredito,idPequenoContribuyente "
			. "from proveedores "
			. "where id=" . $params['idProveedor'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET PROVEEDORES
	 *
	 */
	public function getProveedoresByNit($params) {
		$this->resultado = null;
		$sql = "select * from proveedores where nitP='" . $params['nit'] . "' limit 1;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET PROVEEDORES
	 *
	 */
	public function getProveedoresByName($nombreProveedor) {
		$sql = "select * from proveedores where descripcion='" . $nombreProveedor . "';";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			return $reg;
		}
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
		$filtro = "";
		if ($param['idRoles'] !== '1' && $param['idRoles'] !== '2') {
			$filtro = "and a.idUsuarios=" . $param['idUsuarios'] . " ";
		}
		$sql = "select
                    a.id,a.prefijo,a.serie,a.resolucion
                from
                    documentosCorrelativos as a inner join documentos as b on(a.idDocumentos=b.id)
                where b.descripcion='" . $param['tipo'] . "'
                    " . $filtro . "
                    and a.idEmpresas=" . $param['idEmpresas'] . ";";
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
                    correlativo,
                    serie,
                    IF(idAvailablePrint = 1, 'Si', 'No') AS availablePrint,
                    numeroItems,
                    idFormatos
                from
                    documentosCorrelativos
                where
                    id=" . $param['idDocumento'] . ";";
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
				error_log('error al actualizar correlativo');
			}
		}
	}

	/** METODO GET CLIENTES
	 *
	 */
	public function getClientes($params) {
		$this->resultado = null;
//        $filter = "";
		//        if (strtolower($params['nit']) === 'cf' || strtolower($params['nit']) === 'c/f') {
		//            $filter = "id=" . $params['codCliente'] . "";
		//        } else {
		//            $filter = " and id=" . $params['codCliente'] . "";
		//        }
		//$sql = "select * from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
		$sql = "SELECT
                    *
                FROM
                    clientes
                WHERE
                    (nitC='" . $params['nit'] . "') or cast(codigoC as unsigned)='" . $params['nit'] . "'
                    AND idEmpresas = " . $params['idEmpresas'] . ";";
//        echo $sql . "<br/>";
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
		$filter = "";
		if (isset($params['modulo'])) {
			$filter = " and idRoles in(3,12)";
		}
		if (isset($params['idVendedor'])) {
			$filter = " and id=" . $params['idVendedor'] . "";
		}
		$sql = "select * from usuarios where idEmpresas=" . $params['idEmpresas'] . " " . $filter . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	public function getVendedoresLax($params) {
		$this->resultado = null;
		$filter = "";
		if ($params['modulo'] == '') {
			$filter = " and idSucursales=" . $params['idSucursales'] . " ";
		}
		$sql = "select * from usuarios where idRoles in(4) and idEmpresas=" . $params['idEmpresas'] . " " . $filter . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	public function getProveedoresLax($params) {
		$this->resultado = null;
		$sql = "select * from proveedores where idEmpresas=" . $params['idEmpresas'] . "";
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
		$filter = "";
		if ($params['modulo'] == '') {
			$filter = " and idSucursales=" . $params['idSucursales'] . "";
		}
		$this->resultado = null;
		$sql = "select * from usuarios "
			. "where idRoles=3 and idEmpresas=" . $params['idEmpresas'] . " " . $filter . ";";
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
                    user='" . $params['user'] . "' and pwd='" . md5($params['pwd']) . "' and idRoles in(1,2,13,14);";
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
                    a.*,ifnull(concat(b.cuenta,' - ',b.descripcion),'sin cuenta definida') as nomenclatura
                FROM
                    formArqueoBoveda as a left join nomenclatura as b on(a.idNomenclatura=b.id)
                WHERE
                    a.parent='" . $params['parent'] . "';";
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
                    a.*,ifnull(concat(b.cuenta,' - ',b.descripcion),'sin cuenta definida') as nomenclatura
                FROM
                    formArqueoBovedaMesas as a left join nomenclatura as b on(a.idNomenclatura=b.id)
                WHERE
                    a.parent='" . $params['parent'] . "';";
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
		$sql = "insert into formArqueoBoveda (parent,descripcion,idNomenclatura,operacion) values('" . $params['parent'] . "','" . $params['descripcion'] . "'," . $params['idNomenclatura'] . ",'" . $params['operacion'] . "');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$sql2 = "SELECT
                        a.*,ifnull(concat(b.cuenta,' - ',b.descripcion),'sin cuenta definida') as nomenclatura
                    FROM
                        formArqueoBoveda as a left join nomenclatura as b on(a.idNomenclatura=b.id)
                    WHERE
                        a.parent='" . $params['parent'] . "' and a.id=" . mysql_insert_id() . ";";
			//$sql2 = "select * from formArqueoBoveda where id=" . mysql_insert_id() . ";";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			$reg = mysql_fetch_assoc($query2);
			$response[] = array('message' => 'success', 'id' => $reg['id'], 'parent' => $reg['parent'], 'descripcion' => $reg['descripcion'], 'idNomenclatura' => $reg['idNomenclatura'], 'operacion' => $reg['operacion'], 'nomenclatura' => $reg['nomenclatura']);
		} else {
			$response[] = array('message' => 'failed');
		}
		return $response;
	}

	/** METODO saveRowFormArqueoBovedaMesas
	 *
	 */
	public function saveRowFormArqueoBovedaMesas($params) {
		$response = "";
		$sql = "insert into formArqueoBovedaMesas (parent,descripcion,idNomenclatura,operacion) values('" . $params['parent'] . "','" . $params['descripcion'] . "'," . $params['idNomenclatura'] . ",'" . $params['operacion'] . "');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$sql2 = "SELECT
                        a.*,ifnull(concat(b.cuenta,' - ',b.descripcion),'sin cuenta definida') as nomenclatura
                    FROM
                        formArqueoBovedaMesas as a left join nomenclatura as b on(a.idNomenclatura=b.id)
                    WHERE
                        a.parent='" . $params['parent'] . "' and a.id=" . mysql_insert_id() . ";";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			$reg = mysql_fetch_assoc($query2);
			$response[] = array('message' => 'success', 'id' => $reg['id'], 'parent' => $reg['parent'], 'descripcion' => $reg['descripcion'], 'idNomenclatura' => $reg['idNomenclatura'], 'operacion' => $reg['operacion'], 'nomenclatura' => $reg['nomenclatura']);
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
		$sql = "update formArqueoBoveda set descripcion='" . $params['descripcion'] . "',idNomenclatura=" . $params['idNomenclatura'] . ", operacion='" . $params['operacion'] . "'"
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
		$sql = "update formArqueoBovedaMesas set descripcion='" . $params['descripcion'] . "',idNomenclatura=" . $params['idNomenclatura'] . ", operacion='" . $params['operacion'] . "'"
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
                    idFormArqueoBoveda,valor
                FROM
                    arqueoBovedaDetalle
                WHERE
                    idFormArqueoBoveda in(28,29,30) and idArqueoBoveda=(select id from arqueoBoveda where idSucursales=" . $params['idSalas'] . " and idEmpresas=" . $params['idEmpresas'] . " order by date(arqueo_at) desc limit 1);";
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
                    idFormArqueoBoveda,valor
                FROM
                    arqueoBovedaMesasDetalle
                WHERE
                    idFormArqueoBoveda in(21,22,23) and idArqueoBoveda=(select id from arqueoBovedaMesas where idSucursales=" . $params['idSalas'] . " and idEmpresas=" . $params['idEmpresas'] . " order by date(arqueo_at) desc limit 1);";
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

	/** METODO getFormulas
	 *
	 */
	public function getFormulas($idEmpresas) {
		$this->resultado = null;
		$sql = "SELECT
                    *
                FROM
                    formulas
                where
                    idEmpresas=" . $idEmpresas . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO Notificaciones
	 *
	 */
	public function notificaciones($modulo, $flag, $item, $idEmpresas) {
		$subject = "";
		$text_message = "";
		switch ($modulo) {
		case 'ORDEN DE COMPRAS':
			$subject = "Nueva Orden de Compra";
			$text_message = "FYI, se envia link de visualización de nueva orden de compra<br><br>";
			$text_message .= "Link: http://dev.kairossoft.com.gt/views/jasper/ordenCompra_pdf.php?idOrdenCompra=" . $item . "&idEmpresas=" . $idEmpresas . "&print=0";
			break;
		case 'REQUISICION DE COMPRAS':
			$subject = "Nueva Requisicion de Compra";
			$text_message = "FYI, se envia link de visualización de nueva requisición de compra<br><br>";
			$text_message .= "Link: http://dev.kairossoft.com.gt/views/jasper/requisicionCompra_pdf.php?idRequisicion=" . $item . "&idEmpresas=" . $idEmpresas . "&print=0";
			break;
		case 'COMPRAS':
			$subject = "Nueva Compra Ingresada";
			$text_message = "FYI, se envia link de visualización de nueva compra ingresada al sistema<br><br>";
			$text_message .= "Link: http://dev.kairossoft.com.gt/views/jasper/compras_pdf.php?idCompra=" . $item . "&idEmpresas=" . $idEmpresas . "&print=0";
			break;
		}
		//
		$email_message = new email_message_class;
		$from_address = "notifier@digicom.com.gt";
		$from_name = "notifier, digicom.com.gt";
		$cuenta = 'notifier@digicom.com.gt';
		$reply_name = $from_name;
		$reply_address = $from_address;
		$reply_address = $from_address;
		$error_delivery_name = $from_name;
		$error_delivery_address = $from_address;
		$to_name = "notifier, digicom.com.gt";
		$to_address = $cuenta;
		$email_message->SetMultipleEncodedEmailHeader('To', array('digicom.ortiz@gmail.com' => 'Richard Ortiz'));
		//
		$email_message->SetEncodedEmailHeader("From", $from_address, $from_name);
		$email_message->SetEncodedEmailHeader("Reply-To", $reply_address, $reply_name);
		$email_message->SetHeader("Sender", $from_address);
		//
		if (defined("PHP_OS") && strcmp(substr(PHP_OS, 0, 3), "WIN")) {
			$email_message->SetHeader("Return-Path", $error_delivery_address);
		}

		$email_message->SetEncodedHeader("Subject", $subject);
		//
		$email_message->AddHTMLPart($email_message->WrapText($text_message));
		//
		$error = $email_message->Send();
		if (strcmp($error, "")) {
			error_log('error en envio. ' . $error);
		} else {
			error_log('mensaje enviado.');
		}
	}

	/** METODO getProductos
	 *
	 */
	public function getProductos($busqueda, $idEmpresas) {
		$this->resultado = null;
		$sql = "SELECT
                    *
                FROM
                    productos
                WHERE
                    CONCAT(sku, ' ', descLarga) LIKE '%" . $busqueda . "%' AND idEmpresas = " . $idEmpresas . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO consultaExistencias
	 *
	 */
	public function consultaExistencias($ingresoA, $idPuntoIngreso, $idProductos, $idEmpresas) {
		$sql = "SELECT
                    saldo
                FROM
                    inventarios
                WHERE
                    ingresoA=" . $ingresoA . " and idPuntoIngreso=" . $idPuntoIngreso . " and idProductos=" . $idProductos . " and idEmpresas=" . $idEmpresas . " order by id desc limit 1;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			return $reg['saldo'];
		}
	}

	/** METODO getFormulas
	 *
	 */
	public function getEmpleados($idDepto) {
		$this->resultado = null;
		$sql = "SELECT
                    codigoEmpleado as idUsuarios,
                    concat(primerNombre,' ',segundoNombre,' ',primerApellido,' ',segundoApellido) as nombreCompleto
                FROM
                    hrmEmpleados
                WHERE
                    idHrmDepartamentos = " . $idDepto . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	//
	public function getFamilias($params) {
		$this->resultado = null;
		$nivel = $params['nivelFamilia'];
		$sql = "SELECT
                    id,descripcion
                FROM
                    familiaNivel" . $nivel . " ";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO resumenDocumentosOperados
	 *
	 */
	public function resumenDocumentosOperados($idEmpresa) {
		$this->resultado = null;
		$sql = "select 'cotizaciones' as documento ,count(*) as documentosOperados from ventas where DATE(fechaFactura)=CURDATE() and tipoTransaccion = 1 and autorizacionFEL IS NOT NULL and anulacion = 0 and idEmpresas=" . $idEmpresa . "
                union all
                select 'pedidos' as documento ,count(*) as documentosOperados from pedidos where date(created_at)=curdate() and idEmpresas=" . $idEmpresa . "
                union all
                SELECT 'facturacion' as documento, COUNT(*) as documentosOperados
                    FROM ventas
                    WHERE YEAR(fechaFactura) = YEAR(CURDATE())
                    AND tipoTransaccion = 1
                    AND idEmpresas = " . $idEmpresa . "
                    AND autorizacionFEL IS NOT NULL
                union all
                select 'compras' as documento ,count(*) as documentosOperados from compras where DATE(fechaFactura)=CURDATE() and idEmpresas=" . $idEmpresa . "
                union all
                select 'totalCotizado' as documento ,IFNULL(SUM(total), 0) as documentosOperados from ventas where DATE(fechaFactura)=CURDATE() and tipoTransaccion = 1 and autorizacionFEL IS NOT NULL and anulacion = 0 and idEmpresas=" . $idEmpresa . "
                union all
                select 'totalPedidos' as documento ,IFNULL(SUM(total), 0) as documentosOperados from pedidos where date(created_at)=curdate() and idEmpresas=" . $idEmpresa . "
                union all
                SELECT 'totalFacturado' as documento, IFNULL(SUM(total), 0) as documentosOperados FROM ventas WHERE YEAR(fechaFactura) = YEAR(CURDATE()) AND tipoTransaccion = 1 AND autorizacionFEL IS NOT NULL AND idEmpresas=" . $idEmpresa . "
                union all
                SELECT 'facturaciones' as documento, COUNT(id) as documentosOperados FROM ventas WHERE YEAR(fechaFactura) = YEAR(CURDATE()) AND tipoTransaccion = 1 AND autorizacionFEL IS NOT NULL AND idEmpresas=" . $idEmpresa . "
                union all
                select 'totalCompras' as documento ,IFNULL(SUM(total), 0) as documentosOperados from compras where DATE(fechaFactura)=CURDATE() and idEmpresas=" . $idEmpresa . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/**
	 * Ventas por mes para un año dado (para gráfica de barras del dashboard)
	 */
	public function ventasPorMes($idEmpresa, $anio) {
		$anio = (int) $anio;
		$this->resultado = null;
		$sql = "SELECT
                    MONTH(fechaFactura) AS mes,
                    COUNT(*) AS cantidadVentas,
                    IFNULL(SUM(total), 0) AS totalVentas
                FROM ventas
                WHERE YEAR(fechaFactura) = " . $anio . "
                    AND tipoTransaccion = 1
                    AND autorizacionFEL IS NOT NULL
                    AND anulacion = 0
                    AND idEmpresas = " . (int) $idEmpresa . "
                GROUP BY MONTH(fechaFactura)
                ORDER BY MONTH(fechaFactura)";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/*
	     * MEOTODO TOTAL DTES
    */
	public function totalDtes($idEmpresas) {
		$this->resultado = null;

		$sqlPeriodo = "SELECT CONCAT(periodo, '-', LPAD(mes, 2, '0'), '-01') as fechaInicio
                       FROM control_dte WHERE idEmpresas = " . $idEmpresas . "
                       ORDER BY periodo DESC, mes DESC LIMIT 1";
		$queryPeriodo = mysql_query($sqlPeriodo, dbCon::conPrincipal());
		$regPeriodo = mysql_fetch_assoc($queryPeriodo);
		$fechaInicio = $regPeriodo ? $regPeriodo['fechaInicio'] : '2022-07-01';

		$sql = "SELECT
                nombreComercial as sucursal,
                YEAR(fechaFactura) AS periodo,
                MONTH(fechaFactura) AS mes,
                COUNT(*) AS total,
                COUNT(DISTINCT (DAY(fechaFactura))) AS diasFacturados,
                CEIL((COUNT(*) / COUNT(DISTINCT (DAY(fechaFactura))))) AS facturacionPorDia
            FROM
                ventas
            INNER JOIN
                empresas ON (ventas.idEmpresas = empresas.id)
            WHERE
                idEmpresas = " . $idEmpresas . " AND fechaFactura >= '" . $fechaInicio . "' AND fechaFactura <= CURDATE() AND autorizacionFEL is not null
                GROUP BY ventas.idEmpresas , YEAR(fechaFactura) , MONTH(fechaFactura)
                ORDER BY YEAR(fechaFactura) , MONTH(fechaFactura) ASC";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/*
	 * CRUD COMPRAS DTE
	 */
	public function getComprasDte($idEmpresa) {
		$this->resultado = null;
		$sql = "SELECT
                    c.id,
                    c.periodo,
                    c.mes,
                    c.dteComprados,
                    c.fechaRegistro,
                    (
                        SELECT COUNT(*) FROM ventas v
                        WHERE v.idEmpresas = c.idEmpresas
                          AND v.autorizacionFEL IS NOT NULL
                          AND v.anulacion = 0
                          AND v.fechaFactura >= CONCAT(c.periodo,'-',LPAD(c.mes,2,'0'),'-01')
                          AND v.fechaFactura < IFNULL(
                              (SELECT CONCAT(n.periodo,'-',LPAD(n.mes,2,'0'),'-01')
                               FROM control_dte n
                               WHERE n.idEmpresas = c.idEmpresas
                                 AND (n.periodo > c.periodo OR (n.periodo = c.periodo AND n.mes > c.mes))
                               ORDER BY n.periodo ASC, n.mes ASC LIMIT 1),
                              DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                          )
                    ) AS dteEmitidos,
                    c.dteComprados - (
                        SELECT COUNT(*) FROM ventas v
                        WHERE v.idEmpresas = c.idEmpresas
                          AND v.autorizacionFEL IS NOT NULL
                          AND v.anulacion = 0
                          AND v.fechaFactura >= CONCAT(c.periodo,'-',LPAD(c.mes,2,'0'),'-01')
                          AND v.fechaFactura < IFNULL(
                              (SELECT CONCAT(n.periodo,'-',LPAD(n.mes,2,'0'),'-01')
                               FROM control_dte n
                               WHERE n.idEmpresas = c.idEmpresas
                                 AND (n.periodo > c.periodo OR (n.periodo = c.periodo AND n.mes > c.mes))
                               ORDER BY n.periodo ASC, n.mes ASC LIMIT 1),
                              DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                          )
                    ) AS saldo
                FROM control_dte c
                WHERE c.idEmpresas = " . $idEmpresa . "
                ORDER BY c.periodo DESC, c.mes DESC";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	public function guardarComprasDte($params) {
		$idEmpresa   = (int) $_SESSION['idEmpresa'];
		$periodo     = (int) $params['periodo'];
		$mes         = (int) $params['mes'];
		$dteComprados = (int) $params['dteComprados'];

		$sql = "INSERT INTO control_dte (idEmpresas, periodo, mes, dteComprados)
                VALUES ($idEmpresa, $periodo, $mes, $dteComprados)";
		$query = mysql_query($sql, dbCon::conPrincipal());
		return ['success' => (bool) $query];
	}

	public function eliminarComprasDte($id) {
		$id  = (int) $id;
		$sql = "DELETE FROM control_dte WHERE id = $id";
		$query = mysql_query($sql, dbCon::conPrincipal());
		return ['success' => (bool) $query];
	}

	/*
	 * METODO GET DTE SALDO - retorna saldo real del período activo
	 */
	public function getDteSaldo($idEmpresa) {
		$sqlPeriodo = "SELECT periodo, mes, dteComprados,
                       CONCAT(periodo, '-', LPAD(mes, 2, '0'), '-01') as fechaInicio
                       FROM control_dte WHERE idEmpresas = " . $idEmpresa . "
                       ORDER BY periodo DESC, mes DESC LIMIT 1";
		$queryPeriodo = mysql_query($sqlPeriodo, dbCon::conPrincipal());
		$periodo = mysql_fetch_assoc($queryPeriodo);

		if (!$periodo) return null;

		$fechaInicio  = $periodo['fechaInicio'];
		$dteComprados = (int) $periodo['dteComprados'];

		$sqlEmitidos = "SELECT COUNT(*) as emitidos FROM ventas
                        WHERE idEmpresas = " . $idEmpresa . "
                        AND autorizacionFEL IS NOT NULL
                        AND anulacion = 0
                        AND fechaFactura >= '" . $fechaInicio . "'
                        AND fechaFactura <= CURDATE()";
		$queryEmitidos = mysql_query($sqlEmitidos, dbCon::conPrincipal());
		$rowEmitidos   = mysql_fetch_assoc($queryEmitidos);
		$dteEmitidos   = (int) $rowEmitidos['emitidos'];

		$dteRestantes = $dteComprados - $dteEmitidos;
		$porcentaje   = ($dteComprados > 0) ? round(($dteEmitidos / $dteComprados) * 100, 1) : 0;

		if ($dteRestantes < 0) {
			$alerta = 'danger';
		} elseif ($porcentaje >= 80) {
			$alerta = 'warning';
		} else {
			$alerta = 'ok';
		}

		return [
			'dteComprados' => $dteComprados,
			'dteEmitidos'  => $dteEmitidos,
			'dteRestantes' => $dteRestantes,
			'porcentaje'   => $porcentaje,
			'fechaInicio'  => $fechaInicio,
			'alerta'       => $alerta,
			'periodo'      => $periodo['periodo'],
			'mes'          => $periodo['mes'],
		];
	}

	public function getDtesPorMes($idEmpresa, $anio) {
		$idEmpresa = (int) $idEmpresa;
		$anio      = (int) $anio;
		$this->resultado = null;
		$sql = "SELECT
                    m.mes,
                    IFNULL((SELECT SUM(c2.dteComprados) FROM control_dte c2
                             WHERE c2.idEmpresas = $idEmpresa
                               AND c2.periodo = $anio
                               AND c2.mes = m.mes), 0) AS dteComprados,
                    (SELECT COUNT(*) FROM ventas v2
                     WHERE v2.idEmpresas = $idEmpresa
                       AND v2.autorizacionFEL IS NOT NULL
                       AND v2.anulacion = 0
                       AND YEAR(v2.fechaFactura) = $anio
                       AND MONTH(v2.fechaFactura) = m.mes) AS dteEmitidos
                FROM (
                    SELECT 1 AS mes UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                    UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8
                    UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
                ) m
                ORDER BY m.mes ASC";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO top10Productos
	 *
	 */
	public function top10Productos($idEmpresa) {
		$this->resultado = null;
		$sql = "SELECT
                    concat(b.sku,' - ',b.descLarga) as producto,
                    sum(a.cantidad) as totalUnidades,
                    sum(a.total) as totalVenta
                FROM
                    ventasDetalle as a inner join productos as b on(a.idProductos=b.id)
                    inner join ventas as c on(a.idVentas=c.id)
                where
                    c.fechaFactura between '2018-06-18' and '2018-06-22' and c.idEmpresas=" . $idEmpresa . "
                group by
                    a.idProductos
                order by totalUnidades desc limit 10";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/**
	 * metodo getCodigosClientes
	 */
	public function getCodigosClientes() {
		$this->resultado = null;
		$sql = "SELECT
                    nombre,nit
                FROM
                    ventas as a inner join clientes as b on(a.nit=b.nitC)
                WHERE
                    YEAR(a.created_at) = '2018'
                        AND MONTH(a.created_at) between '06' and '07'
                GROUP BY a.nit;";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET AÑOS
	 *
	 */
	public function getMarcas() {
		$this->resultado = null;
		$sql = "select * from marcas order by descripcion asc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO resumenDepositos
	 *
	 */
	public function resumenDepositos($params) {
		$this->resultado = null;
		$sql = "select
                    numeroCuenta,
                    idBancos as banco
                    ,date_format(curdate(),'%d-%m-%Y') as fecha
                    ,ifnull((
                        SELECT
                            sum(monto)
                        FROM
                            depositos
                            inner join clientes on(depositos.nombreDeposito=clientes.nombreC)
                        WHERE
                            fechaDeposito between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idCuentasBancarias=vw_cuentasBancarias.id and idCentrosCosto=" . $params['idCentrosCosto'] . "
                        group by
                            idCuentasBancarias
                    ),0) as montoDepositado
                    ,ifnull((
                        SELECT
                            count(depositos.id)
                        FROM
                            depositos
                            inner join clientes on(depositos.nombreDeposito=clientes.nombreC)
                        WHERE
                            fechaDeposito between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idCuentasBancarias=vw_cuentasBancarias.id and idCentrosCosto=" . $params['idCentrosCosto'] . "
                        group by
                            idCuentasBancarias
                    ),0) as numeroDepositos
                from vw_cuentasBancarias;";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

}
