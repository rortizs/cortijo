<?php

/**
 * Description of caja
 *
 * @author Richard Sasvin
 */
require_once "dbCon.php";
require_once "general.php";
require_once "admin.php";
require_once "contabilidad.php";

class Caja extends General
{

	/** CLIENTES
	 *
	 */
	public function clientes($params, $idEmpresas)
	{
		//CREAR O ACTUALIZAR QUE NO SEA CF
		if ($params['nit'] != 'CF') {
			$sql2 = "";
			//Validar NIT
			$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
			$query = mysql_query($sql, dbCon::conPrincipal());
			$reg = mysql_fetch_assoc($query);
			if ($reg['id'] == '') {
				// CREA CLIENTE NUEVO
				//echo 'crear cliente nuevo';
				$sql2 = "insert into clientes (nombreC,nombreF,nitC,direccionC,telefonoC,idEmpresas,created_at) "
					. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "'," . $idEmpresas . ",'" . $this->timestamp . "');";
			} else {
				// EDITA DATOS TEL, DIRECCION
				//echo 'actualizar cliente';
				$sql2 = "update clientes set direccionC='" . $params['direccion'] . "',telefonoC='" . $params['telefono'] . "',updated_at='" . $this->timestamp . "'"
					. "where id=" . $reg['id'] . " and idEmpresas=" . $idEmpresas . ";";
			}
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			if ($query2 == TRUE) {
				error_log('cliente creado exitosamente\n');
				return true;
			} else {
				$error = mysql_error();
				error_log('error al crear cliente ' . $error);
				return false;
			}
		} else {
			return true;
		}
	}

	/** GET FONDO CORTE
	 *
	 */
	public function getFondoCorte($idUsuario, $idSucursal)
	{
		$this->resultado = null;
		$sql = "select *
                from fondoCaja
                where idUsuarios=" . $idUsuario . " and idSucursales=" . $idSucursal . "
                and date(date_apertura)='" . $this->date . "' and date_cierre is null;";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO save FONDO CAJA
	 *
	 */
	public function saveFondoCaja($params)
	{
		$response = "";
		$sql = "insert into fondoCaja (monto,tasaCambio,idUsuarios,idSucursales,date_apertura)
                values('" . $params['monto'] . "','" . $params['tasaCambio'] . "'," . $params['idUsuarios'] . "," . $params['idSucursales'] . ",'" . $this->timestamp . "');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success', 'idFondo' => mysql_insert_id());
		} else {
			$response[] = array('message' => 'failed');
		}
		return $response;
	}

	/** GET INFO FONDO CAJA
	 *
	 */
	public function getFondoCaja($idFondo)
	{
		$sql = "SELECT
                    b.userName as empleado,
                    a.monto,
                    c.descripcion as sucursal,
                    DATE_FORMAT(a.date_apertura,'%b %d %Y %h:%i %p') as fecha,
                    d.*
                FROM
                    fondoCaja as a inner join usuarios as b on(a.idUsuarios=b.id)
                    inner join sucursales as c on(a.idSucursales=c.id)
                    inner join empresas as d on(c.idEmpresas=d.id)
                where a.id=" . $idFondo . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			return $reg;
		}
	}

	/** METODO AGREGAR PRODUCTO A VENTA
	 *
	 */
	public function agregarProductoVenta($params)
	{
		//echo $params['tipoProducto'] . ' ' . $params['idProducto'];
		//Consulta los componentes y existencia en inventario
		//Obtener la sucursal de facturacion de la tabla facturacionConf
		$sql1 = "SELECT
                    ingresoA, idPuntoIngreso,agruparItems
                FROM
                    facturacionConf
                WHERE
                    opcion = 'facturacion'
                        AND idEmpresas = " . $params['idEmpresas'] . " AND idSucursales=" . $params['idSucursales'] . ";";
		$query1 = mysql_query($sql1, dbCon::conPrincipal());
		$reg1 = mysql_fetch_assoc($query1);
		$sucursal = $reg1['idPuntoIngreso'];
		$response = "";
		$flag = false;
		if ($params['modulo'] === 'facAdmin') {
			$flag = false;
		} else {
			$sql = "SELECT
	                    concat(a.sku,' - ',a.descLarga) as producto,
	                    b.unidades,
	                    ifnull(c.saldo,'0.00') as saldo,
	                    if(ifnull(c.saldo,'0.00') < (b.unidades*" . $params['cantidad'] . "),'false','true') as action
	                FROM
	                    productos as a
	                    left join productosComponentes as b on(a.id=b.idProductoPrincipal)
	                    inner join inventarios as c on(coalesce(b.idProductos,a.id)=c.idProductos)
	                WHERE
	                    c.ingresoA = " . $reg1['ingresoA'] . "
	                    AND c.idPuntoIngreso = " . $reg1['idPuntoIngreso'] . "
	                    and a.id = " . $params['idProducto'] . "
	                order by c.id desc limit 1;";
			$query = mysql_query($sql, dbCon::conPrincipal());
			while ($reg = mysql_fetch_assoc($query)) {
				if ($params['/* The above code appears to be a comment block in PHP. It starts with /* and ends
				with */, indicating a multi-line comment. The text "valExistencias" is written
				within the comment block. Comments in PHP are used to provide explanations or notes
				within the code that are not executed by the PHP interpreter. */
				valExistencias'] === '1') {
					if ($reg['action'] == 'false') {
						$flag = true;
						$response[] = array(
							'producto' => $reg['producto'],
							'action' => $reg['action'],
							'existencia' => $reg['saldo'],
							'cantidad' => ($reg['unidades'] * $params['cantidad'])
						);
					}
				}
			}
		}
		if ($flag == true) {
			return $response;
		} else {
			if ($reg1['agruparItems'] === '1' && strtoupper($params['codigo']) !== 'SC') {
				//Paso 1: Consultar si producto ya esta en la lista de productos
				$sql = "select * from ventasDetalle "
					. "where idProductos=" . $params['idProducto'] . " and idVentas=0 and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $sucursal . ";";
				$query = mysql_query($sql, dbCon::conPrincipal());
				$reg = mysql_fetch_assoc($query);
				//Paso 2: Consulta costo del producto
				$sql1 = "select precioCosto as costo from productos where id=" . $params['idProducto'] . ";";
				$query1 = mysql_query($sql1, dbCon::conPrincipal());
				$reg1 = mysql_fetch_assoc($query1);
				//
				if (mysql_num_rows($query) == 0) {
					//Si no esta inserta
					$totalCosto = $reg1['costo'] * $params['cantidad'];
					$sql = "insert into ventasDetalle(tipoProducto,idProductos,sku,descLarga,cantidad,precio,precioDolares,costo,total,totalDolares,totalCosto,idUsuarios,idSucursales,idEmpresas)" .
						" values('" . $params['tipoProducto'] . "'," . $params['idProducto'] . ",'" . strtoupper($params['codigo']) . "','" . strtoupper($params['descProducto']) . "','" . $params['cantidad'] . "','" . $params['precio'] . "','" . $params['precioDolares'] . "','" . $reg1['costo'] . "','" . $params['total'] . "','" . $params['totalDolares'] . "','" . $totalCosto . "'," . $params['idUsuarios'] . "," . $params['idSucursales'] . "," . $params['idEmpresas'] . ");";
					//echo $sql;
					$query = mysql_query($sql, dbCon::conPrincipal());
					if ($query == true) {
						$response[] = array('message' => 'success');
					} else {
						$error = mysql_error();
						$response[] = array('message' => 'failed insert', 'Error' => $error, 'Query' => $sql);
					}
				} else {
					//Si esta actualiza cantidad, precioVenta, costo y totales
					$newCantidad = ($reg['cantidad'] + $params['cantidad']);
					$newTotal = ($reg['precio'] * $newCantidad);
					$newTotalDolares = ($reg['precioDolares'] * $newCantidad);
					$newTotalC = ($reg1['costo'] * $newCantidad);
					//
					$sql = "update ventasDetalle set cantidad='" . $newCantidad . "',total='" . $newTotal . "',totalDolares='" . $newTotalDolares . "',totalCosto='" . $newTotalC . "' "
						. "where idProductos=" . $params['idProducto'] . " and idVentas=0 and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . ";";
					$query = mysql_query($sql, dbCon::conPrincipal());
					if ($query == true) {
						$response[] = array('message' => 'success');
					} else {
						$response[] = array('message' => 'failed update', 'Query' => $sql);
					}
				}
			} else {
				//Paso 2: Consulta costo del producto
				$sqlC = "select precioCosto as costo from productos where id=" . $params['idProducto'] . ";";
				$queryC = mysql_query($sqlC, dbCon::conPrincipal());
				$regC = mysql_fetch_assoc($queryC);
				//Si no esta inserta
				$totalCosto = $regC['costo'] * $params['cantidad'];
				$sql = "insert into ventasDetalle(tipoProducto,idProductos,sku,descLarga,cantidad,precio,precioDolares,costo,total,totalDolares,totalCosto,idUsuarios,idSucursales,idEmpresas)" .
					" values('" . $params['tipoProducto'] . "'," . $params['idProducto'] . ",'" . strtoupper($params['codigo']) . "','" . strtoupper($params['descProducto']) . "','" . $params['cantidad'] . "','" . $params['precio'] . "','" . $params['precioDolares'] . "','" . $regC['costo'] . "','" . $params['total'] . "','" . $params['totalDolares'] . "','" . $totalCosto . "'," . $params['idUsuarios'] . "," . $params['idSucursales'] . "," . $params['idEmpresas'] . ");";
				//echo $sql;
				$query = mysql_query($sql, dbCon::conPrincipal());
				if ($query) {
					$response[] = array('message' => 'success');
				} else {
					$error = mysql_error();
					$response[] = array('message' => 'failed insert', 'Error' => $error, 'Query' => $sql);
				}
			}
			return $response;
		}
	}

	/** METODO agregarProductoPedido
	 *
	 */
	public function agregarProductoPedido($params)
	{
		$response = "";
		$flag = false;

		$sql1 = "SELECT
                ingresoA, idPuntoIngreso, agruparItems
            FROM
                facturacionConf
            WHERE
                opcion = 'pedidos' AND idEmpresas = " . $params['idEmpresas'] . " and idSucursales=" . $params['idSucursales'] . ";";
		$query1 = mysql_query($sql1, dbCon::conPrincipal());
		$reg1 = mysql_fetch_assoc($query1);

		$sql = "SELECT
                concat(a.sku,' - ',a.descLarga) as producto,
                b.unidades,
                ifnull(c.saldo,'0.00') as saldo,
                if(ifnull(c.saldo,'0.00') < (b.unidades*1),'false','true') as action
            FROM
                productos as a
                left join productosComponentes as b on(a.id=b.idProductoPrincipal)
                inner join inventarios as c on(coalesce(b.idProductos,a.id)=c.idProductos)
            WHERE
                c.ingresoA = " . $reg1['ingresoA'] . "
                AND c.idPuntoIngreso = " . $reg1['idPuntoIngreso'] . "
                and a.id = " . $params['idProducto'] . "
            order by c.id desc limit 1;";
		$query = mysql_query($sql, dbCon::conPrincipal());

		if ($params['valExistencias'] === '1') {
			while ($reg = mysql_fetch_assoc($query)) {
				if ($reg['action'] == 'false') {
					$flag = true;
					$response[] = array(
						'producto' => $reg['producto'],
						'action' => $reg['action'],
						'existencia' => $reg['saldo'],
						'cantidad' => ($reg['unidades'] * $params['cantidad'])
					);
				}
			}
		}

		if ($flag == true) {
			return $response;
		} else {
			if ($reg1['agruparItems'] === '1') {
				//Paso 1: Consultar si producto ya esta en la lista de productos del pedido
				$sql = "select * from pedidosDetalle "
					. "where idProductos=" . $params['idProducto'] . " and idPedidos is null and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . ";";
				$query = mysql_query($sql, dbCon::conPrincipal());
				$reg = mysql_fetch_assoc($query);
				//Paso 2: Consulta costo del producto
				$sql1 = "select precioCosto as costo from productos where id=" . $params['idProducto'] . ";";
				$query1 = mysql_query($sql1, dbCon::conPrincipal());
				$reg1 = mysql_fetch_assoc($query1);
				//
				if (mysql_num_rows($query) == 0 || (strtoupper($params['codigo']) === 'SC' && strtoupper($params['descProducto']) !== strtoupper($reg['descLarga']))) {
					//Si no esta inserta o si es SC con descripción diferente
					$totalCosto = $reg1['costo'] * $params['cantidad'];
					$filter = "values(null,";
					if ($params['idPedido'] != '') {
						$filter = "values(" . $params['idPedido'] . ",";
					}
					$sql = "insert into pedidosDetalle(idPedidos,tipoProducto,idProductos,sku,descLarga,cantidad,precio,costo,total,totalCosto,idUsuarios,idSucursales,idEmpresas)
                    " . $filter . "'" . $params['tipoProducto'] . "'," . $params['idProducto'] . ",'" . strtoupper($params['codigo']) . "','" . strtoupper($params['descProducto']) . "','" . $params['cantidad'] . "','" . $params['precio'] . "','" . $reg1['costo'] . "','" . $params['total'] . "','" . $totalCosto . "'," . $params['idUsuarios'] . "," . $params['idSucursales'] . "," . $params['idEmpresas'] . ");";
					$query = mysql_query($sql, dbCon::conPrincipal());
					if ($query == true) {
						$response[] = array('message' => 'success');
					} else {
						$response[] = array('message' => 'failed insert', 'Query' => $sql);
					}
				} else {
					//Si esta actualiza cantidad, precioVenta, costo y totales
					$newCantidad = ($reg['cantidad'] + $params['cantidad']);
					$newTotal = ($reg['precio'] * $newCantidad);
					$newTotalC = ($reg1['costo'] * $newCantidad);
					//
					$filter = "idPedidos is null";
					if ($params['idPedido'] != '') {
						$filter = "idPedidos=" . $params['idPedido'] . "";
					}
					$sql = "update pedidosDetalle set cantidad='" . $newCantidad . "',total='" . $newTotal . "',totalCosto='" . $newTotalC . "' "
						. "where idProductos=" . $params['idProducto'] . " and " . $filter . " and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . ";";
					$query = mysql_query($sql, dbCon::conPrincipal());
					if ($query == true) {
						$response[] = array('message' => 'success');
					} else {
						$response[] = array('message' => 'failed update', 'Query' => $sql);
					}
				}
			} else {
				//Paso 2: Consulta costo del producto
				$sql1 = "select precioCosto as costo from productos where id=" . $params['idProducto'] . ";";
				$query1 = mysql_query($sql1, dbCon::conPrincipal());
				$reg1 = mysql_fetch_assoc($query1);
				$totalCosto = $reg1['costo'] * $params['cantidad'];
				$filter = "values(null,";
				if ($params['idPedido'] != '') {
					$filter = "values(" . $params['idPedido'] . ",";
				}
				$sql = "insert into pedidosDetalle(idPedidos,tipoProducto,idProductos,sku,descLarga,cantidad,precio,costo,total,totalCosto,idUsuarios,idSucursales,idEmpresas)
                    " . $filter . "'" . $params['tipoProducto'] . "'," . $params['idProducto'] . ",'" . strtoupper($params['codigo']) . "','" . strtoupper($params['descProducto']) . "','" . $params['cantidad'] . "','" . $params['precio'] . "','" . $reg1['costo'] . "','" . $params['total'] . "','" . $totalCosto . "'," . $params['idUsuarios'] . "," . $params['idSucursales'] . "," . $params['idEmpresas'] . ");";
				$query = mysql_query($sql, dbCon::conPrincipal());
				if ($query == true) {
					$response[] = array('message' => 'success');
				} else {
					$response[] = array('message' => 'failed insert', 'Query' => $sql);
				}
			}
			return $response;
		}
	}

	/** METODO ELIMINAR PRODUCTO DE VENTA
	 *
	 */
	public function eliminarProductoVenta($params)
	{
		$response = "";
		$sql = "delete from ventasDetalle "
			. "where id=" . $params['item'] . ";";
		//echo $sql."<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO eliminarProductoPedido
	 *
	 */
	public function eliminarProductoPedido($params)
	{
		$response = "";
		$sql = "delete from pedidosDetalle where id=" . $params['item'] . ";";
		//echo $sql."<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO getProductosPedido
	 *
	 */
	//
	public function getProductosPedido2($params, $idProducto)
	{
		$this->resultado = null;

		if ($idProducto != '') {
			$filter = " and a.id= " . $idProducto . "";
		} else {
			$filter = " and a.idPedidos is null and idUsuarios=" . $params['idUsuarios'] . " and idSucursales =" . $params['idSucursales'] . "";
		}
		$sql = "SELECT
                    b.sku AS 'sku', b.descLarga, a.*,c.saldo as existencia
                FROM
                    pedidosDetalle AS a
                        LEFT JOIN
                    productos AS b ON (a.idProductos = b.id)
                        LEFT JOIN
                    inventario as c on(a.idProductos=c.idProductos)
                where
                    c.ingresoA=" . $params['ingresoA'] . " and idPuntoIngreso=" . $params['idPuntoIngreso'] . " " . $filter . ";";
		//echo $sql . "\n";
		//echo $params['idPedido'];
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado = $reg;
		}
		return $this->resultado;
	}

	//
	public function getProductosPedido1($params, $idProducto)
	{
		$this->resultado = null;
		$filter = " and a.idPedidos is null and idUsuarios=" . $params['idUsuarios'] . " and idSucursales =" . $params['idSucursales'] . "";
		if ($idProducto != '') {
			$filter = " and a.id= " . $idProducto . "";
		}
		$sql = "SELECT
                    b.sku AS 'sku', b.descLarga, a.*,c.saldo as existencia
                FROM
                    pedidosDetalle AS a
                        LEFT JOIN
                    productos AS b ON (a.idProductos = b.id)
                        LEFT JOIN
                    inventario as c on(a.idProductos=c.idProductos)
                where
                    c.ingresoA=1 and idPuntoIngreso=1 " . $filter . ";";
		//echo $sql . "\n";
		//echo $params['idPedido'];
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado = $reg;
		}
		return $this->resultado;
	}

	//
	public function getProductosPedido($params)
	{
		$this->resultado = null;
		if ($params['idPedido'] != '') {
			$filter = " a.idPedidos= " . $params['idPedido'] . "";
		} else {
			$filter = " a.idPedidos is null and idUsuarios=" . $params['idUsuarios'] . " and idSucursales =" . $params['idSucursales'] . "";
		}
		$sql = "SELECT
                    a.*,
                    b.id as idProducto,
                    b.sku AS 'sku',
                    a.descLarga,
                    b.idFabricaProducto,
                    (select saldo from inventario where ingresoA=2 and idProductos=a.idProductos and idPuntoIngreso=a.idSucursales) as existencia
                FROM
                    pedidosDetalle AS a
                    INNER JOIN productos AS b ON (a.idProductos = b.id)
                    LEFT JOIN marcas as m on(b.idMarcas=m.id)
                where " . $filter . "";
		//echo $sql . "\n";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
			/*
	              if ($reg['idFabricaProducto'] == '1') {
	              $this->resultado[] = $this->getProductosPedido1($params, $reg['id']);
	              } else if ($reg['idFabricaProducto'] == '2') {
	              $this->resultado[] = $this->getProductosPedido2($params, $reg['id']);
	              }
	             *
*/
		}
		return $this->resultado;
	}

	/** METODO cerrarVenta
	 *
	 */
	public function cerrarVenta($params, $idUsuarios, $idSucursales, $idEmpresas)
	{
		if ($params['tipoTransaccion'] === '1') {
			//EMITE FACTURA
			//CONSULTA DATOS EMPRESA
			$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $idEmpresas . ";";
			$queryE = mysql_query($sqlE, dbCon::conPrincipal());
			$regE = mysql_fetch_assoc($queryE);
			//
			//URL PRODUCTIVO
			$URL = "https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml";
			$UsuarioAPI = $regE['usuarioAPI'];
			$LlaveAPI = $regE['llaveAPI'];
			$UsuarioFirma = $regE['usuarioFirma'];
			$LlaveFirma = $regE['llaveFirma'];
			date_default_timezone_set("America/Guatemala");
			//GENERACION DE XML
			$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
			$direccion = $this->consultaNIT($params);
			$direccionComprador = $direccion[0]['direccion'];
			//
			$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
                    <dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.2.0">
                      <dte:SAT ClaseDocumento="dte">
                        <dte:DTE ID="DatosCertificados">
                          <dte:DatosEmision ID="DatosEmision">
                            <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $this->date . 'T' . $this->time . '-06:00" Tipo="FACT"></dte:DatosGenerales>
                            <dte:Emisor AfiliacionIVA="' . $regE['tipoAfiliacion'] . '" CodigoEstablecimiento="' . $regE['codigoEstablecimiento'] . '" CorreoEmisor="marriolaj@gmail.com" NITEmisor="' . $regE['nit'] . '" NombreComercial="' . $regE['nombreComercial'] . '" NombreEmisor="' . $regE['razonSocial'] . '">
                              <dte:DireccionEmisor>
                                <dte:Direccion>' . $regE['direccion'] . '</dte:Direccion>
                                <dte:CodigoPostal>01009</dte:CodigoPostal>
                                <dte:Municipio>' . $regE['municipio'] . '</dte:Municipio>
                                <dte:Departamento>' . $regE['departamento'] . '</dte:Departamento>
                                <dte:Pais>GT</dte:Pais>
                              </dte:DireccionEmisor>
                            </dte:Emisor>
                            <dte:Receptor CorreoReceptor="" IDReceptor="' . str_replace('-', '', $params['nit']) . '" NombreReceptor="' . $nombreComprador . '">
                              <dte:DireccionReceptor>
                                <dte:Direccion>' . ($direccionComprador ?: 'CIUDAD') . '</dte:Direccion>
                                <dte:CodigoPostal>0101</dte:CodigoPostal>
                                <dte:Municipio>GUATEMALA</dte:Municipio>
                                <dte:Departamento>GUATEMALA</dte:Departamento>
                                <dte:Pais>GT</dte:Pais>
                              </dte:DireccionReceptor>
                            </dte:Receptor>
                            <dte:Frases>
                              <dte:Frase CodigoEscenario="' . $regE['codigoEscenario'] . '" TipoFrase="' . $regE['tipoFrase'] . '"></dte:Frase>';
			if ($regE['agenteRetenedor'] === '1') {
				$xml_data .= '<dte:Frase TipoFrase="2" CodigoEscenario="1"/>';
			}
			$xml_data .= '</dte:Frases>
                            <dte:Items>';
			$params['idUsuarios'] = $idUsuarios;
			$totalVenta = 0;
			$totalIVA = 0;
			foreach ($this->getProductosVenta($params) as $key => $value) {
				$totalVenta += $value['total'];
				$totalIVA += $value['iva'];
				//
				$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="1">
                        <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                        <dte:UnidadMedida>UND</dte:UnidadMedida>
                        <dte:Descripcion>' . $value['descLarga'] . '</dte:Descripcion>
                        <dte:PrecioUnitario>' . round($value['precio'], 3) . '</dte:PrecioUnitario>
                        <dte:Precio>' . ($value['precio'] * $value['cantidad']) . '</dte:Precio>
                        <dte:Descuento>0.00</dte:Descuento>
                        <dte:Impuestos>
                          <dte:Impuesto>
                            <dte:NombreCorto>IVA</dte:NombreCorto>
                            <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                            <dte:MontoGravable>' . $value['subtotal'] . '</dte:MontoGravable>
                            <dte:MontoImpuesto>' . $value['iva'] . '</dte:MontoImpuesto>
                          </dte:Impuesto>
                        </dte:Impuestos>
                        <dte:Total>' . round($value['total'], 2) . '</dte:Total>
                      </dte:Item>';
			}
			$xml_data .= '</dte:Items>
                            <dte:Totales>
                              <dte:TotalImpuestos>
                                <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . $totalIVA . '"/>
                              </dte:TotalImpuestos>
                              <dte:GranTotal>' . round($totalVenta, 2) . '</dte:GranTotal>
                            </dte:Totales>
                           </dte:DTE>
                      </dte:SAT>
                    </dte:GTDocumento>';
			// echo $xml_data;
			// exit();
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_MUTE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('UsuarioAPI: ' . $UsuarioAPI . '', 'LlaveAPI: ' . $LlaveAPI . '', 'UsuarioFirma: ' . $UsuarioFirma . '', 'LlaveFirma: ' . $LlaveFirma . '', 'Identificador: ' . date('YmdHis') . ''));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$response = json_decode($output, true);
			if ($response['uuid']) {
				//$responseF[] = array('message' => 'success', 'autorizacion' => $response['Autorizacion'], 'serie' => $response['Serie'], 'numero' => $response['NUMERO'], 'fechaEmision' => $response['Fecha_DTE']);
				$saldo = 0.00;
				if ($params['tipoVenta'] === '2') {
					$saldo = $params['saldo'] ?: $params['total'];
				}
				//CREAR CLIENTE SI ID ES VACIO Y QUE NO SEA CF
				$idClientes = "0";
				if ($params['nombreF'] != 'Consumidor Final') {
					//Validar NIT
					$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
					$query = mysql_query($sql, dbCon::conPrincipal());
					$reg = mysql_fetch_assoc($query);
					if ($reg['id'] == '') {
						$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,telefonoC,idTipoClientes,idEmpresas,created_at,mail) "
							. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "',1," . $idEmpresas . ",'" . $this->timestamp . "','" . $params['mail'] . "');";
						$queryC = mysql_query($sqlC, dbCon::conPrincipal());
						if ($queryC == TRUE) {
							$idClientes = mysql_insert_id();
							error_log('cliente creado exitosamente\n');
						} else {
							error_log('error al crear cliente ' . $sqlC);
						}
					} else {
						$sqlU = "update clientes set nombreC='" . $params['nombre'] . "',nombreF='" . $params['nombre'] . "', direccionC='" . $params['direccion'] . "',telefonoC='" . $params['telefono'] . "',mail='" . $params['mail'] . "'"
							. " where id=" . $reg['id'] . ";";
						$queryU = mysql_query($sqlU, dbCon::conPrincipal());
						$idClientes = $reg['id'];
					}
				}
				// GUARDA VENTA
				$sql = "insert into ventas
                        (id,
                        serie,
                        correlativo,
                        fechaFactura,
                        valorFactura,
                        subtotal,
                        descuento,
                        descuentoP,
                        total,
                        anticipo,
                        saldo,
                        iva,
                        tipoCambio,
                        totalDolares,
                        totalEnLetras,
                        totalEnLetrasDolares,
                        nit,
                        nombre,
                        direccion,
                        idTipoOperacion,
                        idTipoVenta,
                        conceptoVenta,
                        idUsuarios,
                        idSucursales,
                        idEmpresas,
                        created_at,
                        updated_at,
                        idFormatos,
                        idPartidas,
                        statusCierre,
                        anulacion,
                        idAdminUser,
                        motivoAnulacion,
                        anulacion_at,
                        idClientes,
                        idVendedores,
                        autorizacionFEL,
                        fechaEmisionFEL,
                        tipoTransaccion,xml)
                    values(
                        null,
                        '" . $response['serie'] . "',
                        '" . $response['numero'] . "',
                        '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                        '" . $params['total'] . "',
                        '" . $params['subtotal'] . "',
                        '" . $params['descuentoM'] . "',
                        '" . $params['descuentoP'] . "',
                        '" . $params['total'] . "',
                        '" . $params['anticipo'] . "',
                        '" . $saldo . "',
                        '" . $params['iva'] . "',
                        '" . $params['tasaCambio'] . "',
                        '" . $params['totalDolares'] . "',
                        '" . $params['totalEnLetras'] . "',
                        '" . $params['totalEnLetrasDolares'] . "',
                        '" . $params['nit'] . "',
                        '" . $nombreComprador . "',
                        '" . $params['direccion'] . "',
                        '1',
                        '" . $params['tipoVenta'] . "',
                        '" . $params['observaciones'] . "',
                        '" . $idUsuarios . "',
                        '" . $idSucursales . "',
                        '" . $idEmpresas . "',
                        '" . $this->timestamp . "',
                        null,
                        " . $params['idFormato'] . ",
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        '" . $idClientes . "',
                        '" . $params['vendedores'] . "','" . $response['uuid'] . "','" . $response['fecha'] . "'," . $params['tipoTransaccion'] . ",'" . $xml_data . "');";
				$query = mysql_query($sql, dbCon::conPrincipal());
				//echo $query;
				if ($query) {
					//OBTIENE ID VENTA
					$idVenta = mysql_insert_id();
					$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
						. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
					$query2 = mysql_query($sql2, dbCon::conPrincipal());
					if ($query2) {
						if (isset($params['idPedido'])) {
							$this->updateEstadoPedido($params['idPedido'], $idVenta, '3');
						}
						//INGRESO DE DETALLE LIQUIDACION VENTA AL CONTADO
						if ($params['tipoVenta'] === '1') {
							$this->liquidacionVenta($params['detallePago'], $idVenta);
						}
						//INGRESO DE VENTA AL CREDITO
						if ($params['tipoVenta'] === '2') {
							$this->ventasCredito($params['nit'], $idVenta, $params['correlativo'], $params['total'], $params['anticipo'], $saldo, date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
							//$this->liquidacionVenta($params['detallePago'], $idVenta);
						}
						//GUARDAR PARTIDA AUTOMATICA
						if ($params['idFormato'] !== '0') {
							$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
							$params['idTipoOperacionPartida'] = 2;
							$params['conceptoCompra'] = 'REF. FEL. ' . $response['Autorizacion'];
							$params['idEmpresas'] = $idEmpresas;
							$params['idUsuarios'] = $idUsuarios;
							$params['idVenta'] = $idVenta;
							$this->savePartidaAutomatica($params);
						}
						//DESCUENTA PRODUCTO DE INVENTARIO
						$this->movimientoInventario($idVenta, 'FEL - ' . $response['uuid'], 'salida', $this->timestamp);
						//
						//ENVIO DE MAIL
						if ($params['mail'] !== '') {
							$this->envioMailFEL($params['mail'], $params['nombre'], $response['Autorizacion'], $nombreComercial, $nitEmisor, $idVenta);
						}
						//
						$responseF[] = array('message' => 'success', 'idVenta' => $idVenta, 'idSucursales' => $idSucursales, 'autorizacion' => $response['Autorizacion']);
					} else {
						$responseF[] = array('message' => 'failed step 2', 'error' => $sql2);
					}
				} else {
					$error = mysql_error();
					$responseF[] = array('message' => $error, 'Query' => $sql, 'Error' => $error);
				}
			} else {
				//echo $xml_data;
				$responseF[] = array('message' => $response['descripcion_errores'][0]['mensaje_error']);
			}
			return $responseF;
		} else {
			//EMITE RECIBO
			$response = "";
			$saldo = 0.00;
			if ($params['tipoVenta'] === '2') {
				$saldo = $params['saldo'] ?: $params['total'];
			}
			//CREAR CLIENTE SI ID ES VACIO Y QUE NO SEA CF
			$idClientes = "0";
			if ($params['nombreF'] != 'Consumidor Final') {
				//Validar NIT
				$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
				$query = mysql_query($sql, dbCon::conPrincipal());
				$reg = mysql_fetch_assoc($query);
				if ($reg['id'] == '') {
					$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,telefonoC,idTipoClientes,idEmpresas,created_at) "
						. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "',1," . $idEmpresas . ",'" . $this->timestamp . "');";
					$queryC = mysql_query($sqlC, dbCon::conPrincipal());
					if ($queryC == TRUE) {
						$idClientes = mysql_insert_id();
						error_log('cliente creado exitosamente\n');
					} else {
						error_log('error al crear cliente ' . $sqlC);
					}
				} else {
					$idClientes = $reg['id'];
				}
			}
			// GUARDA VENTA
			$doc = explode('-', $params['correlativo']);
			$sql = "insert into ventas
                    (id,
                    serie,
                    correlativo,
                    fechaFactura,
                    valorFactura,
                    subtotal,
                    descuento,
                    descuentoP,
                    total,
                    anticipo,
                    saldo,
                    iva,
                    tipoCambio,
                    totalDolares,
                    totalEnLetras,
                    totalEnLetrasDolares,
                    nit,
                    nombre,
                    direccion,
                    idTipoOperacion,
                    idTipoVenta,
                    conceptoVenta,
                    idUsuarios,
                    idSucursales,
                    idEmpresas,
                    created_at,
                    updated_at,
                    idFormatos,
                    idPartidas,
                    statusCierre,
                    anulacion,
                    idAdminUser,
                    motivoAnulacion,
                    anulacion_at,
                    idClientes,
                    idVendedores,
                    autorizacionFEL,
                    fechaEmisionFEL,
                    tipoTransaccion)
                values(
                    null,
                    '" . $doc[0] . "',
                    '" . $doc[1] . "',
                    '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                    '" . $params['total'] . "',
                    '" . $params['subtotal'] . "',
                    '" . $params['descuentoM'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $params['total'] . "',
                    '" . $params['anticipo'] . "',
                    '" . $saldo . "',
                    '" . $params['iva'] . "',
                    '" . $params['tasaCambio'] . "',
                    '" . $params['totalDolares'] . "',
                    '" . $params['totalEnLetras'] . "',
                    '" . $params['totalEnLetrasDolares'] . "',
                    '" . $params['nit'] . "',
                    '" . $params['nombre'] . "',
                    '" . $params['direccion'] . "',
                    '1',
                    '" . $params['tipoVenta'] . "',
                    '" . $params['observaciones'] . "',
                    '" . $idUsuarios . "',
                    '" . $idSucursales . "',
                    '" . $idEmpresas . "',
                    '" . $this->timestamp . "',
                    null,
                    " . $params['idFormato'] . ",
                    0,
                    0,
                    0,
                    null,
                    null,
                    null,
                    '" . $idClientes . "',
                '" . $params['vendedores'] . "',null,null," . $params['tipoTransaccion'] . ");";
			$query = mysql_query($sql, dbCon::conPrincipal());
			//echo $query;
			if ($query) {
				//OBTIENE ID VENTA
				$idVenta = mysql_insert_id();
				$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
					. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//INGRESO DE DETALLE LIQUIDACION VENTA AL CONTADO
					if ($params['tipoVenta'] === '1') {
						$this->liquidacionVenta($params['detallePago'], $idVenta);
					}
					//INGRESO DE VENTA AL CREDITO
					if ($params['tipoVenta'] === '2') {
						$this->ventasCredito($params['idClientes'], $idVenta, $params['correlativo'], $params['total'], $params['anticipo'], $saldo, date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
						//$this->liquidacionVenta($params['detallePago'], $idVenta);
					}
					//GUARDAR PARTIDA AUTOMATICA
					if ($params['idFormato'] !== '0') {
						$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
						$params['idTipoOperacionPartida'] = 2;
						$params['conceptoCompra'] = 'REF. RECIBO. ' . $params['correlativo'];
						$params['idEmpresas'] = $idEmpresas;
						$params['idUsuarios'] = $idUsuarios;
						$params['idVenta'] = $idVenta;
						$this->savePartidaAutomatica($params);
					}
					//DESCUENTA PRODUCTO DE INVENTARIO
					$this->movimientoInventario($idVenta, 'RECIBO - ' . $params['correlativo'], 'salida', $params['fechaFactura']);
					//ACTUALIZAR CORRELATIVO
					$admin = new Admin();
					$correlativo = explode('-', $params['correlativo']);
					$updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $correlativo[1], $idEmpresas);
					//
					if (isset($params['idPedido'])) {
						$this->updateEstadoPedido($params['idPedido'], $idVenta, '3');
					}
					//
					$response[] = array('message' => 'success', 'idVenta' => $idVenta, 'idSucursales' => $idSucursales);
				} else {
					$response[] = array('message' => 'failed step 2', 'Query' => $sql2);
				}
			} else {
				$error = mysql_error();
				$response[] = array('message' => 'failed step 1', 'Query' => $sql, 'Error' => $error);
			}
			return $response;
		}
	}

	/** METODO cerrarVenta
	 *
	 */
	public function cerrarVentaAgenciaViajes($params, $idUsuarios, $idSucursales, $idEmpresas)
	{
		$response = "";
		$saldo = 0.00;
		if ($params['tipoVenta'] == 2) {
			$saldo = $params['total'];
		}
		//CREAR CLIENTE SI ID ES VACIO Y QUE NO SEA CF
		if ($params['nombreF'] != 'Consumidor Final') {
			//Validar NIT
			$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
			$query = mysql_query($sql, dbCon::conPrincipal());
			$reg = mysql_fetch_assoc($query);
			if ($reg['id'] == '') {
				$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,telefonoC,idTipoClientes,idEmpresas,created_at) "
					. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "',1," . $idEmpresas . ",'" . $this->timestamp . "');";
				$queryC = mysql_query($sqlC, dbCon::conPrincipal());
				if ($queryC == TRUE) {
					error_log('cliente creado exitosamente\n');
				} else {
					error_log('error al crear cliente ' . $sqlC);
				}
			}
		}

		// GUARDA VENTA
		if ($params['tipoImpresion'] == '1') {
			$doc2 = explode('-', $params['correlativoPagare']);
			$params['correlativoFactura'] = "";
		} else if ($params['tipoImpresion'] == '2') {
			$doc = explode('-', $params['correlativoFactura']);
			$params['correlativoPagare'] = "";
		} else if ($params['tipoImpresion'] == '3') {
			$doc2 = explode('-', $params['correlativoPagare']);
			$doc = explode('-', $params['correlativoFactura']);
		}
		$sql = "insert into ventas
                values(
                    null,
                    '" . $doc[0] . "',
                    '" . $doc[1] . "',
                    '" . $doc2[0] . "',
                    '" . $doc2[1] . "',
                    '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                    '" . $params['total'] . "',
                    '" . $params['subTotal'] . "',
                    '" . ($params['totalImpuestos'] - $params['iva']) . "',
                    '" . $params['descuentoM'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $params['total'] . "',
                    '" . $saldo . "',
                    '" . round(($params['iva']), 2) . "',
                    '" . $params['tasaCambio'] . "',
                    '" . $params['totalDolares'] . "',
                    '" . $params['totalEnLetras'] . "',
                    '" . $params['fee'] . "',
                    '" . $params['otrosCargos'] . "',
                    '" . $params['nit'] . "',
                    '" . $params['nombre'] . "',
                    '" . $params['direccion'] . "',
                    '1',
                    '" . $params['tipoVenta'] . "',
                    '" . $params['motivo'] . "',
                    '" . $idUsuarios . "',
                    '" . $idSucursales . "',
                    '" . $idEmpresas . "',
                    '" . $this->timestamp . "',
                    null,
                    0,
                    0,
                    0,
                    0,
                    null,
                    null,
                    null,
                    '" . $params['idClientes'] . "',
                    '" . $params['vendedores'] . "',
                 '" . $params['tipoFacturacion'] . "');";
		//echo $query;
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query) {
			//OBTIENE ID VENTA
			$idVenta = mysql_insert_id();
			//echo var_dump($params['boletos']);
			$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
				. "where idVentas=0 and idUsuarios=" . $idUsuarios . " and idSucursales=" . $idSucursales . ";";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			$caja = new Caja();
			$boletos = $caja->updateAgenciaViajes($idVenta);
			if ($query2) {
				$params['idTipoDocumento'] = 3;
				if ($params['correlativoFactura'] != '' && $params['correlativoPagare'] != '') {
					$docs = $params['correlativoFactura'] . ', ' . $params['correlativoPagare'];
				} else if ($params['correlativoFactura'] != '') {
					$docs = $params['correlativoFactura'];
				} else if ($params['correlativoPagare'] != '') {
					$docs = $params['correlativoPagare'];
				}
				$caja->CrearCXPAgenciaViajes($params, $idVenta, $docs, $saldo, '0.00', 3, $idUsuarios, $idEmpresas, $boletos);

				//INGRESO DE DETALLE LIQUIDACION VENTA AL CONTADO
				if ($params['tipoVenta'] === '1') {
					$this->liquidacionVenta($params['detallePago'], $idVenta);
				}
				//INGRESO DE VENTA AL CREDITO
				if ($params['tipoVenta'] === '2') {
					//$caja->ventasCreditoAgenciaViajes($params, $idVenta, $docs, $saldo, '0.00', 3, $idUsuarios, $idEmpresas);                 //$this->ventasCredito($idVenta, $params['idClientes'], $params['total'], $idUsuarios, $params['correlativo']);
					$this->ventasCredito($params['idClientes'], $idVenta, $docs, $params['total'], $params['anticipo'], $saldo, date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);

					//$this->liquidacionVenta($params['detallePago'], $idVenta);
				}
				//GUARDAR PARTIDA AUTOMATICA
				if ($params['idFormato'] != '') {
					$params['partida_at'] = $this->date;
					$params['idTipoOperacionPartida'] = 1;
					$params['conceptoCompra'] = 'Venta ' . $params['correlativo'];
					$params['idEmpresas'] = $idEmpresas;
					$params['idUsuarios'] = $idUsuarios;
					$this->savePartidaAutomatica($params);
				}
				//DESCUENTA PRODUCTO DE INVENTARIO
				$this->movimientoInventario($idVenta, $params['correlativo'], 'salida');
				//ACTUALIZAR CORRELATIVO
				$admin = new Admin();
				if ($params['tipoImpresion'] == '1' || $params['tipoImpresion'] == '3') {
					$correlativo = explode('-', $params['correlativoPagare']);
					$updateCD = $admin->updateCorrelativoDocumento('PAGARE', $correlativo[1], $idEmpresas);
				}
				if ($params['tipoImpresion'] == '2' || $params['tipoImpresion'] == '3') {
					$correlativo = explode('-', $params['correlativoFactura']);
					$updateCD = $admin->updateCorrelativoDocumento('FACTURA', $correlativo[1], $idEmpresas);
				}
				//
				if (isset($params['idPedido'])) {
					$this->updateEstadoPedido($params['idPedido'], $idVenta, '3');
				}
				//
				$response[] = array('message' => 'success', 'idVenta' => $idVenta);
			} else {
				$response[] = array('message' => 'failed step 2', 'Query' => $sql2);
			}
		} else {
			$response[] = array('message' => 'failed step 1', 'Query' => $sql);
		}
		return $response;
	}

	public function updateAgenciaViajes($idVenta)
	{
		$response = "";
		$sql = "select idProducto FROM ventasDetalle where idVentas=" . $idVenta . "";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$sqlUpdate = "update agenciasViajes set idVentas='" . $idVenta . "' WHERE id='" . $reg['idProducto'] . "'";
			$queryUpdate = mysql_query($sqlUpdate, dbCon::conPrincipal());
			$response[] = array('idBoleto' => $reg['idProducto']);
		}

		return $response;
	}

	/** METODO generarPedido
	 *
	 */
	public function generarPedido($params, $idUsuarios, $idSucursales, $idEmpresas)
	{
		$response = "";
		$clientes = $this->clientes($params, $idEmpresas);
		if ($clientes) {
			// GUARDA PEDIDO
			$sql = "insert into pedidos"
				. " values(null
                        ,'" . $params['correlativo'] . "'
                        ,'" . $params['nit'] . "'
                        ,'" . $params['nombre'] . "'
                        ,'" . $params['telefono'] . "'
                        ,'" . $params['direccion'] . "'
                        ,'" . $params['total'] . "'
                        ,'0.00'
                        ,'" . $params['total'] . "'
                        ,'1'
                        ,'" . $params['observaciones'] . "'
                        ,'" . $params['tipoVenta'] . "'
                        ,'" . $params['idVendedores'] . "'
                        ,'" . $idSucursales . "'
                        ,'" . $idEmpresas . "'
                        ,'" . $this->timestamp . "'
                        ,null
                        ,'" . $params['idClientes'] . "','" . ($params['fechaEntrega'] ?: '0000-00-00') . "','" . ($params['horaEntrega'] ?: '00:00:00') . "',1)";
			//echo $sql;
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query) {
				//OBTIENE ID PEDIDO PARA ACTUALIZAR DETALLE DE PEDIDO
				$idPedido = mysql_insert_id();
				$sql2 = "update pedidosDetalle set idPedidos=" . $idPedido . " "
					. "where idPedidos is null and idUsuarios=" . $idUsuarios . " and idSucursales=" . $idSucursales . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//ACTUALIZAR CORRELATIVO DE DOCUMENTO
					$admin = new Admin();
					$correlativo = explode('-', $params['correlativo']);
					$updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $correlativo[1], $idEmpresas);
					//ACTUALIZA STATUS DE COTIZACION A PROCESADA SI PEDIDO ES GENERADO DESDE COTIZACION
					if (isset($params['idCotizaciones'])) {
						$this->updateEstadoCotizacion($params['idCotizaciones'], $idPedido);
					}
					$response[] = array('message' => 'success', 'idPedido' => $idPedido);
				} else {
					$error = mysql_error();
					$response[] = array('message' => 'failed step 2', 'error' => $error, 'query' => $sql2);
				}
			} else {
				$error = mysql_error();
				$response[] = array('message' => 'failed step 1', 'error' => $error, 'query' => $sql);
			}
		} else {
			$response[] = array('message' => 'failed step: clientes');
		}
		return $response;
	}

	/** DETALLE LIQUIDACION VENTA
	 *
	 */
	public function liquidacionVenta($detalle, $idVenta)
	{
		foreach ($detalle as $key => $value) {
			$sql = "insert into ventasLiquidacion "
				. "values(null," . $idVenta . ",'" . $value['formaPago'] . "','" . $value['valor'] . "','" . $value['emisores'] . "','" . $value['auth'] . "');";
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query == true) {
				error_log('success liquidacionVenta');
			} else {
				error_log('error sql: ' . $sql);
			}
		}
	}

	/**
	 *
	 */
	public function movimientoInventario($idVenta, $documento, $operacion, $fechaMovimiento)
	{
		//Obtener los productos de la venta
		$sql = "SELECT
                    tipoProducto,
                    idProductos,
                    cantidad,
                    idUsuarios,
                    idSucursales,
                    idEmpresas
                FROM
                    ventasDetalle
                WHERE
                    idVentas =" . $idVenta . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			switch ($reg['tipoProducto']) {
				case 'Producto Fabricado':
					//Consulta los componentes y existencia en inventario
					$sql2 = "select
                                a.idProductos,
                                concat(c.sku,' - ',c.descLarga) as producto,
                                a.unidades,
                                ifnull((SELECT
                                            ifnull(saldo,0)
                                        FROM
                                            inventarios
                                        WHERE
                                            idProductos = a.idProductos and ingresoA=2 and idPuntoIngreso=" . $reg['idSucursales'] . "
                                        order by id desc limit 1),'0.00') as saldo,
                                if(ifnull((SELECT
                                            ifnull(saldo,0)
                                        FROM
                                            inventarios
                                        WHERE
                                            idProductos = a.idProductos and ingresoA=2 and idPuntoIngreso=" . $reg['idSucursales'] . "
                                        order by id desc limit 1),'0.00') < (a.unidades*" . $reg['cantidad'] . "),'false','true') as action
                            from
                                productosComponentes as a
                                    left join
                                inventario as b ON (a.idProductos = b.idProductos)
                                    inner join
                                productos as c ON (a.idProductos = c.id)
                            where
                                a.idProductoPrincipal = " . $reg['idProductos'] . " group by c.sku;";
					$query2 = mysql_query($sql2, dbCon::conPrincipal());
					while ($reg2 = mysql_fetch_assoc($query2)) {
						$saldo = $reg2['saldo'] - ($reg2['unidades'] * $reg['cantidad']);
						//Procesar movimiento
						$sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos," . $operacion . ",saldo,idUsuarios,idEmpresas,created_at)"
							. "values('2'," . $reg['idSucursales'] . ",'" . $documento . "'," . $reg2['idProductos'] . ",'" . ($reg2['unidades'] * $reg['cantidad']) . "','" . $saldo . "'," . $reg['idUsuarios'] . "," . $reg['idEmpresas'] . ",'" . date("Y-m-d", strtotime($fechaMovimiento)) . "');";
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
					$sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idProductos," . $operacion . ",saldo,idUsuarios,idEmpresas,created_at)"
						. "values('2'," . $reg['idSucursales'] . ",'" . $documento . "'," . $reg['idProductos'] . ",'" . $reg['cantidad'] . "','" . $saldo . "'," . $reg['idUsuarios'] . "," . $reg['idEmpresas'] . ",'" . date("Y-m-d", strtotime($fechaMovimiento)) . "');";
					$query3 = mysql_query($sql3, dbCon::conPrincipal());
					if (!$query3) {
						error_log(mysql_error());
					}
					break;
			}
		}
	}

	/** METODO GET TIPO DE CAMBIO
	 *
	 */
	public function getTipoCambio($idUsuarios, $idSucursal)
	{
		$this->resultado = null;
		$sql = "select tasaCambio "
			. "from fondoCaja "
			. "where idUsuarios=" . $idUsuarios . " and idSucursales=" . $idSucursal . " "
			. "order by id desc limit 1;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO ANULAR FACTURA FEL INFILE
	 *
	 */
	public function anulacionFacturaINFILE($params, $idEmpresas, $dbProject)
	{
		$responseF = "";
		//OBTENER DATOS DE EMPRESA SEGUN EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $idEmpresas . ";";
		$resE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_array($resE);
		//
		$sqlF = "SELECT
                    *,(select tipoFacturacion from empresas where id=ventas.idEmpresas) as tipoFacturacion,
                    concat(fechaFactura,'T',time(created_at),'-06:00') as fechaEmision
                FROM
                    ventas
                where
                    id=" . $params['idFactura'] . ";";
		$resF = mysql_query($sqlF, dbCon::conPrincipal());
		$regF = mysql_fetch_array($resF);
		if ($regF['tipoTransaccion'] === '1') {
			$URL = "https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml";
			$UsuarioAPI = $regE['usuarioAPI'];
			$LlaveAPI = $regE['llaveAPI'];
			$UsuarioFirma = $regE['usuarioFirma'];
			$LlaveFirma = $regE['llaveFirma'];
			//
			$xml_data = '<dte:GTAnulacionDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.1.0" xmlns:n1="http://www.altova.com/samplexml/other-namespace" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.1.0 C:\Users\User\Desktop\FEL\Esquemas\GT_AnulacionDocumento-0.1.0.xsd">
                    <dte:SAT>
                      <dte:AnulacionDTE ID="DatosCertificados">
                        <dte:DatosGenerales FechaEmisionDocumentoAnular="' . $regF['fechaEmision'] . '" FechaHoraAnulacion="' . $this->date . 'T' . $this->time . '-06:00" ID="DatosAnulacion" IDReceptor="' . strtoupper(str_replace('-', '', $regF['nit'])) . '" MotivoAnulacion="' . $params['motivoAnulacion'] . '" NITEmisor="' . $regE['nit'] . '" NumeroDocumentoAAnular="' . $regF['autorizacionFEL'] . '"></dte:DatosGenerales>
                      </dte:AnulacionDTE>
                    </dte:SAT>
                  </dte:GTAnulacionDocumento>';
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_MUTE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('UsuarioAPI: ' . $UsuarioAPI . '', 'LlaveAPI: ' . $LlaveAPI . '', 'UsuarioFirma: ' . $UsuarioFirma . '', 'LlaveFirma: ' . $LlaveFirma . '', 'Identificador: ' . md5(date('YmdHis')) . '_test_infile_2'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$response = json_decode($output, true);
			if ($response['uuid']) {
				//paso 1 anular factura
				$sql1 = "update ventas "
					. "set anulacion=1, "
					. "valorFactura='0.00',"
					. "subtotal='0.00',"
					. "descuento='0.00',"
					. "descuentoP='0.00',"
					. "total='0.00',"
					. "iva='0.00',"
					. "totalDolares='0.00',"
					. "totalEnLetras='CERO- ANULADA',"
					. "idAdminUser=" . $params['idAdminUser'] . ", "
					. "motivoAnulacion='" . $params['motivoAnulacion'] . "', "
					. "anulacion_at='" . $this->timestamp . "'"
					. "where id=" . $params['idFactura'] . ";";
				$query1 = mysql_query($sql1, dbCon::conPrincipal());
				if ($query1 == true) {
					//
					$sql2 = "update ventasDetalle set precio='0.00', costo='0.00', total='0.00', totalCosto='0.00'
                             where idVentas=" . $params['idFactura'] . ";";
					$query2 = mysql_query($sql2, dbCon::conPrincipal());
					if ($query2 == true) {
						//paso 2 ingresar los productos al inventario
						$this->movimientoInventario($params['idFactura'], 'INGRESO POR ANULACION DE FAC ' . $regF['serie'] . '-' . $regF['correlativo'], 'ingreso', $this->timestamp);
						//paso 3 habilitar pedido gestionado
						$this->updateEstadoPedido('', $params['idFactura'], '1');
						$this->eliminarFacCXC($params['idFactura']);
						$this->liberarVales($params['idFactura']);
					} else {
						$responseF[] = array('message' => 'failed step 2', 'Query' => $sql2);
					}
					//
					$responseF[] = array('message' => 'success');
				} else {
					$responseF[] = array('message' => 'failed step 1', 'Query' => $sql1);
				}
			} else {
				$responseF[] = array('message' => 'failed step fel', 'error' => $response['descripcion_errores'][0]['mensaje_error'], 'xml' => $xml_data);
			}
		} else {
			//paso 1 anular factura
			$sql1 = "update ventas "
				. "set anulacion=1, "
				. "valorFactura='0.00',"
				. "subtotal='0.00',"
				. "descuento='0.00',"
				. "descuentoP='0.00',"
				. "total='0.00',"
				. "iva='0.00',"
				. "totalDolares='0.00',"
				. "totalEnLetras='CERO- ANULADA',"
				. "idAdminUser=" . $params['idAdminUser'] . ", "
				. "motivoAnulacion='" . $params['motivoAnulacion'] . "', "
				. "anulacion_at='" . $this->timestamp . "'"
				. "where id=" . $params['idFactura'] . ";";
			$query1 = mysql_query($sql1, dbCon::conPrincipal());
			if ($query1 == true) {
				$sql2 = "update ventasDetalle set precio='0.00', costo='0.00', total='0.00', totalCosto='0.00'
                         where idVentas=" . $params['idFactura'] . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2 == true) {
					//paso 2 ingresar los productos al inventario
					$this->movimientoInventario($params['idFactura'], 'INGRESO POR ANULACION DE FAC ' . $regF['serie'] . '-' . $regF['correlativo'], 'ingreso', $this->timestamp);
					//paso 3 habilitar pedido gestionado
					$this->updateEstadoPedido('', $params['idFactura'], '1');
					$this->eliminarFacCXC($params['idFactura']);
					$this->liberarVales($params['idFactura']);
					//
					$responseF[] = array('message' => 'success');
				} else {
					$responseF[] = array('message' => 'failed step 2', 'Query' => $sql2);
				}
			} else {
				$responseF[] = array('message' => 'failed step 1', 'Query' => $sql1);
			}
		}
		return $responseF;
	}

	public function anulacionFactura($params, $idEmpresas, $dbProject)
	{
		$responseF = "";
		//OBTENER DATOS DE EMPRESA SEGUN EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $idEmpresas . ";";
		$resE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_array($resE);
		//
		$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkdULjAwMDA0MTExNjU2OS5URVNUVVNFUiIsIm5iZiI6MTYzNTI4ODU2MCwiZXhwIjoxNjY2MzkyNTYwLCJpYXQiOjE2MzUyODg1NjAsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6NDkyMjAiLCJhdWQiOiJodHRwOi8vbG9jYWxob3N0OjQ5MjIwIn0.Fwg484JGFy9lmPtDBlqq3IyUJLOIcZhtD121LYtgGKQ";
		$URL = "https://felgtaws.digifact.com.gt/gt.com.fel.api.v2/api/FELRequest?NIT=" . $regE['nitWS'] . "&TIPO=ANULAR_FEL_TOSIGN&FORMAT=XML";
		//----------
		//PROCESO DE ANULACION DE NC O FACTURA
		if ($params['opcionAnular'] === 'nc') {
			$sqlF = "SELECT
                        cxc.fechaEmisionFEL as FechaEmisionDocumentoAnular,
                        ventas.nit,
                        cxc.autorizacionFEL as NumeroDocumentoAAnular,
                        cxc.idVentas,
                        cxc.debitos
                    FROM
                        cxc
                        inner join ventas on(cxc.idVentas=ventas.id)
                    WHERE
                        cxc.id=" . $params['idFactura'] . ";";
			$resF = mysql_query($sqlF, dbCon::conPrincipal());
			$regF = mysql_fetch_array($resF);
			//
			$xml_data = '<?xml version="1.0" encoding="utf-8"?>
                        <dte:GTAnulacionDocumento xmlns:dte="http://www.sat.gob.gt/dte/fel/0.1.0"
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            Version="0.1">
                            <dte:SAT>
                              <dte:AnulacionDTE ID="DatosCertificados">
                                <dte:DatosGenerales FechaEmisionDocumentoAnular="' . $regF['FechaEmisionDocumentoAnular'] . '" FechaHoraAnulacion="' . $this->date . 'T' . $this->time . '" ID="DatosAnulacion" IDReceptor="' . strtoupper(str_replace('-', '', $regF['nit'])) . '" MotivoAnulacion="' . $params['motivoAnulacion'] . '" NITEmisor="' . $regE['nit'] . '" NumeroDocumentoAAnular="' . $regF['NumeroDocumentoAAnular'] . '">
                                </dte:DatosGenerales>
                              </dte:AnulacionDTE>
                            </dte:SAT>
                      </dte:GTAnulacionDocumento>';
			//-----------
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_MUTE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Authorization: ' . $token . ''));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$response = json_decode($output, true);
			if ($response['Codigo'] === 1) {
				$sql1 = "update cxc set debitos='0' where id=" . $params['idFactura'] . ";";
				$query1 = mysql_query($sql1, dbCon::conPrincipal());
				if ($query1 == true) {
					$sql2 = "update ventas set total=(total+" . $regF['debitos'] . ") where id=" . $regF['idVentas'] . ";";
					$query2 = mysql_query($sql2, dbCon::conPrincipal());
					if ($query2 == true) {
						$responseF[] = array('message' => 'success');
					} else {
						$responseF[] = array('message' => 'failed step 2', 'Query' => $sql2);
					}
				} else {
					$responseF[] = array('message' => 'failed step 1', 'Query' => $sql1);
				}
			} else {
				//echo $xml_data;
				$responseF[] = array('message' => 'failed fel nc', 'error' => $response['ResponseDATA1'], 'xml' => $xml_data);
			}
		} else {
			$sqlF = "SELECT
                        *,(select tipoFacturacion from empresas where id=ventas.idEmpresas) as tipoFacturacion
                    FROM
                        ventas
                    where
                        id=" . $params['idFactura'] . ";";
			$resF = mysql_query($sqlF, dbCon::conPrincipal());
			$regF = mysql_fetch_array($resF);
			//CONSULTAR TIPO DE DOCUMENTO RECIBO O FACTURA
			if ($regF['tipoTransaccion'] === '1' && $regF['tipoFacturacion'] === '1') {
				$xml_data = '<?xml version="1.0" encoding="utf-8"?>
                        <dte:GTAnulacionDocumento xmlns:dte="http://www.sat.gob.gt/dte/fel/0.1.0"
                            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            Version="0.1">
                            <dte:SAT>
                              <dte:AnulacionDTE ID="DatosCertificados">
                                <dte:DatosGenerales FechaEmisionDocumentoAnular="' . $regF['fechaEmisionFEL'] . '" FechaHoraAnulacion="' . $this->date . 'T' . $this->time . '" ID="DatosAnulacion" IDReceptor="' . strtoupper(str_replace('-', '', $regF['nit'])) . '" MotivoAnulacion="' . $params['motivoAnulacion'] . '" NITEmisor="' . $regE['nit'] . '" NumeroDocumentoAAnular="' . $regF['autorizacionFEL'] . '">
                                </dte:DatosGenerales>
                              </dte:AnulacionDTE>
                            </dte:SAT>
                      </dte:GTAnulacionDocumento>';
				//-----------
				$ch = curl_init($URL);
				curl_setopt($ch, CURLOPT_MUTE, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Authorization: ' . $token . ''));
				curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
				$response = json_decode($output, true);
				if ($response['Codigo'] === 1) {
					//paso 1 anular factura
					$sql1 = "update ventas "
						. "set anulacion=1, "
						. "valorFactura='0.00',"
						. "saldo='0.00',"
						. "subtotal='0.00',"
						. "descuento='0.00',"
						. "descuentoP='0.00',"
						. "total='0.00',"
						. "iva='0.00',"
						. "totalDolares='0.00',"
						. "totalEnLetras='CERO- ANULADA',"
						. "idAdminUser=" . $params['idAdminUser'] . ", "
						. "motivoAnulacion='" . $params['motivoAnulacion'] . "', "
						. "anulacion_at='" . $this->timestamp . "'"
						. "where id=" . $params['idFactura'] . ";";
					$query1 = mysql_query($sql1, dbCon::conPrincipal());
					if ($query1 == true) {
						//paso 2 ingresar los productos al inventario
						$this->movimientoInventario($params['idFactura'], 'INGRESO POR ANULACION DE FAC ' . $regF['serie'] . '-' . $regF['correlativo'], 'ingreso', $this->timestamp);
						//paso 3 habilitar pedido gestionado
						$this->updateEstadoPedido('', $params['idFactura'], '1');
						$this->eliminarFacCXC($params['idFactura']);
						$this->liberarVales($params['idFactura']);
						//
						$responseF[] = array('message' => 'success');
					} else {
						$responseF[] = array('message' => 'failed step 1', 'Query' => $sql1);
					}
				} else {
					//print_r($response);
					$responseF[] = array('message' => 'failed step FEL', 'error' => $response['ResponseDATA1'], 'xml' => $xml_data);
				}
			} else {
				//paso 1 anular factura
				$sql1 = "update ventas "
					. "set anulacion=1, "
					. "valorFactura='0.00',"
					. "subtotal='0.00',"
					. "descuento='0.00',"
					. "descuentoP='0.00',"
					. "total='0.00',"
					. "iva='0.00',"
					. "totalDolares='0.00',"
					. "totalEnLetras='CERO- ANULADA',"
					. "idAdminUser=" . $params['idAdminUser'] . ", "
					. "motivoAnulacion='" . $params['motivoAnulacion'] . "', "
					. "anulacion_at='" . $this->timestamp . "'"
					. "where id=" . $params['idFactura'] . ";";
				$query1 = mysql_query($sql1, dbCon::conPrincipal());
				if ($query1 == true) {
					//paso 2 ingresar los productos al inventario
					$this->movimientoInventario($params['idFactura'], 'INGRESO POR ANULACION DE FAC ' . $regF['serie'] . '-' . $regF['correlativo'], 'ingreso', $this->timestamp);
					//paso 3 habilitar pedido gestionado
					$this->updateEstadoPedido('', $params['idFactura'], '1');
					$this->eliminarFacCXC($params['idFactura']);
					$this->liberarVales($params['idFactura']);
					//
					$responseF[] = array('message' => 'success');
				} else {
					$responseF[] = array('message' => 'failed step 1', 'Query' => $sql1);
				}
			}
		}
		return $responseF;
	}

	/** METODO SAVE VALE
	 *
	 */
	public function saveVale($params)
	{
		$response = "";
		$sql = "insert into valesCaja "
			. "values(null,'" . $params['solicitadoPor'] . "','" . $params['monto'] . "','" . $params['observaciones'] . "'," . $params['idUsuarios'] . "," . $params['idSucursales'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "','0');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success', 'idVale' => mysql_insert_id());
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** GET VALES CORTE
	 *
	 */
	public function getTotalVales($params)
	{
		$this->resultado = null;
		$sql = "select format(ifnull(sum(replace(monto,',','')),0),2) as totalVales "
			. "from valesCaja "
			. "where idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . " and date(created_at)='" . date("Y-m-d", strtotime($params['fechaCorte'])) . "' and statusCierre='0';";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CORTE DE CAJA
	 *
	 */
	public function corteCaja($params, $idUsuarios, $idSucursales, $idEmpresas, $fechaCorte)
	{
		$response = "";
		$sql = "insert into corteCaja "
			. "values(null,'" . date("Y-m-d", strtotime($fechaCorte)) . "',"
			. "'" . $params['fondoCaja'] . "',"
			. "'" . $params['totalVales'] . "',"
			. "'" . $params['totalEfectivo'] . "',"
			. "'" . $params['totalEfectivoDolares'] . "',"
			. "'" . $params['totalExenciones'] . "',"
			. "'" . $params['totalCheques'] . "',"
			. "'" . $params['totalVouchers'] . "',"
			. "'" . $params['totalCorte'] . "',"
			. "'" . $params['totalVentasContado'] . "',"
			. "'" . $params['totalVentasCredito'] . "',"
			. "'" . $params['diferencia'] . "',"
			. "" . $idUsuarios . ","
			. "" . $idSucursales . ","
			. "" . $idEmpresas . ","
			. "'" . $this->timestamp . "',"
			. "'0','" . $params['totalRecibos'] . "');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$idCorte = mysql_insert_id();
			$this->corteCajaDetalle($idCorte, $params['detalle']);
			$response[] = array('message' => 'success', 'idCorte' => $idCorte);
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO DETALLE CORTE DE CAJA
	 *
	 */
	public function corteCajaDetalle($idCorte, $detalle)
	{
		foreach ($detalle as $key => $value) {
			$sql = "insert into corteCajaDetalle values(null," . $idCorte . ",'" . $value['cantidad'] . "','" . $value['descripcion'] . "','" . $value['total'] . "');";
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query == true) {
				error_log('success corteCajaDetalle');
			} else {
				error_log('error sql: ' . $sql);
			}
		}
	}

	/** METODO GET CORTE
	 *
	 */
	public function getCorte($idCorte)
	{
		$sql = "select *, date_format(created_at,'%d-%m-%Y %H:%i:%s') as created_at"
			. " from corteCaja "
			. "where id=" . $idCorte . ";";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			return $reg;
		}
	}

	/** METODO GET CIERRE CAJA
	 *
	 */
	/**
	 * Resumen de ventas pendientes de cierre para un cajero/sucursal
	 */
	public function getCierre($idSucursales, $idUsuarios)
	{
		$sql = "SELECT
                    IFNULL(SUM(total), 0) as ventas,
                    IFNULL(SUM(CASE WHEN idFormasPago = 1 THEN total ELSE 0 END), 0) as efectivo,
                    IFNULL(SUM(CASE WHEN idFormasPago = 2 THEN total ELSE 0 END), 0) as cambios,
                    IFNULL(SUM(CASE WHEN idFormasPago = 5 THEN total ELSE 0 END), 0) as tarjetas
                FROM ventas
                WHERE idSucursales = " . $idSucursales . "
                  AND idUsuarios = " . $idUsuarios . "
                  AND statusCierre = '0'
                  AND anulacion = '0';";
		$query = mysql_query($sql, dbCon::conPrincipal());
		$response = array();
		if ($query) {
			$row = mysql_fetch_assoc($query);
			if ($row) {
				$response[] = $row;
			}
		}
		return $response;
	}

	public function getTotalVentas($idSucursales, $idUsuarios, $fechaCorte)
	{
		$this->resultado = null;
		$sql = "select
                    idTipoVenta, SUM(total) AS efectivo
                from
                    ventas
                where
                    fechaFactura = '" . date("Y-m-d", strtotime($fechaCorte)) . "'
                    and idSucursales = " . $idSucursales . "
                    and statusCierre = 0
                    and anulacion = 0
                    and idUsuarios = " . $idUsuarios . "
                GROUP BY idTipoVenta;";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET CIERRE CAJA
	 *
	 */
	public function getTotalVentasTJ($idSucursales, $idUsuarios, $fechaCorte)
	{
		$this->resultado = null;
		$sql = "select
                    a.idEmisores, sum(a.valor) as total
                from
                    ventasLiquidacion as a
                        inner join
                    ventas as b ON (a.idVentas = b.id)
                where
                    a.idFormasPago = 5
                    and date(created_at) = '" . date("Y-m-d", strtotime($fechaCorte)) . "'
                    and idSucursales = " . $idSucursales . "
                    and statusCierre = 0
                    and anulacion = 0
                    and idUsuarios = " . $idUsuarios . " group by idEmisores;";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET CIERRE CAJA
	 *
	 */
	public function getTotalVentaExencion($idSucursales, $idUsuarios, $fechaCorte)
	{
		$this->resultado = null;
		$sql = "select
                    sum(a.valor) as total
                from
                    ventasLiquidacion as a
                        inner join
                    ventas as b ON (a.idVentas = b.id)
                where
                    a.idFormasPago = 3
                    and date(created_at) = '" . date("Y-m-d", strtotime($fechaCorte)) . "'
                    and idSucursales = " . $idSucursales . "
                    and statusCierre = 0
                    and anulacion = 0
                    and idUsuarios = " . $idUsuarios . ";";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET CIERRE CAJA
	 *
	 */
	public function getTotalVentaCheques($idSucursales, $idUsuarios, $fechaCorte)
	{
		$this->resultado = null;
		$sql = "select
                    sum(a.valor) as total
                from
                    ventasLiquidacion as a
                        inner join
                    ventas as b ON (a.idVentas = b.id)
                where
                    a.idFormasPago = 4
                    and date(created_at) = '" . date("Y-m-d", strtotime($fechaCorte)) . "'
                    and idSucursales = " . $idSucursales . "
                    and statusCierre = 0
                    and anulacion = 0
                    and idUsuarios = " . $idUsuarios . ";";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO getTotalRecibos
	 *
	 */
	public function getTotalRecibos($idUsuarios, $fechaCorte, $idEmpresas)
	{
		$this->resultado = null;
		$sql = "select sum(valor) as total
                from(
                SELECT
                    a.valor as valor
                FROM
                    cxc AS a
                        LEFT JOIN
                    clientes AS b ON (a.idClientes = b.nitC)
                        LEFT JOIN
                    ventas AS c ON (a.idVentas = c.id)
                WHERE
                    a.idTipoDocumento = 2
                        AND a.idEmpresas = " . $idEmpresas . "
                        and a.idUsuarios=" . $idUsuarios . "
                        AND DATE(a.created_at) = '" . date("Y-m-d", strtotime($fechaCorte)) . "'
                GROUP BY idDocumento) as t;";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO PROCESAR CIERRE
	 *
	 */
	public function procesarCierre($idUsuarios, $idSucursales)
	{
		$this->cerrarCaja($idUsuarios, $idSucursales);
	}

	/**
	 *
	 */
	public function cerrarCaja($idUsuarios, $idSucursales, $fechaCierre)
	{
		$response = "";
		$sql = "update fondoCaja set date_cierre='" . $this->timestamp . "' where idUsuarios=" . $idUsuarios . " and idSucursales=" . $idSucursales . " and date(date_apertura)='" . $fechaCierre . "';";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$sql2 = "update ventas set statusCierre='1' where idUsuarios=" . $idUsuarios . " and idSucursales=" . $idSucursales . " and date(created_at)='" . $fechaCierre . "' and statusCierre='0';";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			if ($query2 == true) {
				$sql3 = "update corteCaja set statusCierre='1' where idUsuarios=" . $idUsuarios . " and idSucursales=" . $idSucursales . " and date(created_at)='" . $fechaCierre . "' and statusCierre='0';";
				$query3 = mysql_query($sql3, dbCon::conPrincipal());
				if ($query3 == true) {
					$sql4 = "update valesCaja set statusCierre='1' where idUsuarios=" . $idUsuarios . " and idSucursales=" . $idSucursales . " and date(created_at)='" . $fechaCierre . "' and statusCierre='0';";
					$query4 = mysql_query($sql4, dbCon::conPrincipal());
					$response[] = array('message' => 'success');
				} else {
					error_log('error sql step 3: ' . $sql3);
				}
			} else {
				error_log('error sql step 2: ' . $sql2);
			}
		} else {
			error_log('error sql step 1: ' . $sql);
		}
		return $response;
	}

	/** METODO CANCELAR VENTA
	 *
	 */
	public function cancelarVenta($idSucursales, $idUsuarios)
	{
		$response = "";
		$sql = "delete from ventasDetalle where idVentas=0  and idUsuarios=" . $idUsuarios . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO cancelarPedido
	 *
	 */
	public function cancelarPedido($params)
	{
		$response = "";
		$sql = "";
		if ($params['idPedido'] != "") {
			$sql = "update pedidos set estado='2' where id=" . $params['idPedido'] . ";";
		} else {
			$sql = "delete from pedidosDetalle where idPedidos is null  and idUsuarios=" . $params['idUsuarios'] . ";";
		}
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			if ($params['estado'] === 'Confirmado') {
				//PASO 1 CONSULTAR LOS PARAMETROS DE SUCURSAL DE SALIDA
				$sql1 = "SELECT
                            ingresoA, idPuntoIngreso
                        FROM
                            facturacionConf
                        WHERE
                            opcion = 'facturacion' AND idEmpresas = " . $params['idEmpresas'] . ";";
				$query1 = mysql_query($sql1, dbCon::conPrincipal());
				$reg1 = mysql_fetch_assoc($query1);
				//PASO 2 OBTENER LOS PRODUCTOS DEL PEDIDO Y LAS EXISTENCIAS ACTUALES EN SUCURSAL
				$sql2 = "SELECT
                            h.documento,
                            if(d.sku is not null,d.id,b.id) as idProductos,
                            if(d.sku is not null,d.sku,b.sku) as codigo,
                            upper(if(d.sku is not null,d.descLarga,b.descLarga)) as producto,
                            if(c.unidades is null,a.cantidad,(a.cantidad*c.unidades))  as cantidad,
                            (select saldo from inventarios where idEmpresas = a.idEmpresas and ingresoA=" . $reg1['ingresoA'] . " and idPuntoIngreso=" . $reg1['idPuntoIngreso'] . " and idProductos=if(d.sku is not null,d.id,b.id) order by id desc limit 1) as existencia
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
				//echo $sql2."\n";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());

				//PASO 3 REALIZAR LA SALIDA DE LOS PRODUCTOS
				$sql3 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idTipoProductos,idProductos,salida,saldo,idUsuarios,idEmpresas) values";
				$numero = mysql_num_rows($query2);
				$i = 1;
				while ($reg2 = mysql_fetch_assoc($query2)) {
					if ($i !== $numero) {
						$sql3 .= "(" . $reg1['ingresoA'] . "," . $reg1['idPuntoIngreso'] . ",'SALIDA CANCELACION PEDIDO " . $reg2['documento'] . "','1'," . $reg2['idProductos'] . ",'" . $reg2['cantidad'] . "','" . ($reg2['existencia'] - $reg2['cantidad']) . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . "),";
					} else {
						$sql3 .= "(" . $reg1['ingresoA'] . "," . $reg1['idPuntoIngreso'] . ",'SALIDA CANCELACION PEDIDO " . $reg2['documento'] . "','1'," . $reg2['idProductos'] . ",'" . $reg2['cantidad'] . "','" . ($reg2['existencia'] - $reg2['cantidad']) . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ");";
					}
					$i++;
				}
				$query3 = mysql_query($sql3, dbCon::conPrincipal());
				if ($query3 == true) {
					//PASO 4 CONSULTAR LOS PARAMETROS DE BODEGA DE ENTRADA
					$sql4 = "SELECT
                                ingresoA, idPuntoIngreso
                            FROM
                                facturacionConf
                            WHERE
                                opcion = 'pedidos' AND idEmpresas = " . $params['idEmpresas'] . ";";
					$query4 = mysql_query($sql4, dbCon::conPrincipal());
					$reg4 = mysql_fetch_assoc($query4);
					//PASO 5 OBTENER LOS PRODUCTOS DEL PEDIDO Y LAS EXISTENCIAS ACTUALES EN BODEGA
					$sql5 = "SELECT
                                h.documento,
                                if(d.sku is not null,d.id,b.id) as idProductos,
                                if(d.sku is not null,d.sku,b.sku) as codigo,
                                upper(if(d.sku is not null,d.descLarga,b.descLarga)) as producto,
                                sum(a.cantidad) as cantidad,
                                (select saldo from inventarios where idEmpresas = a.idEmpresas and ingresoA=" . $reg4['ingresoA'] . " and idPuntoIngreso=" . $reg4['idPuntoIngreso'] . " and idProductos=if(d.sku is not null,d.id,b.id) order by id desc limit 1) as existencia
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
					$query5 = mysql_query($sql5, dbCon::conPrincipal());
					//PASO 6 REALIZAR LA SALIDA DE LOS PRODUCTOS
					$sql6 = "insert into inventarios(ingresoA,idPuntoIngreso,documento,idTipoProductos,idProductos,ingreso,saldo,idUsuarios,idEmpresas) values";
					$numero2 = mysql_num_rows($query5);
					$i2 = 1;
					while ($reg5 = mysql_fetch_assoc($query5)) {
						if ($i2 !== $numero2) {
							$sql6 .= "(" . $reg4['ingresoA'] . "," . $reg4['idPuntoIngreso'] . ",'INGRESO CANCELACION PEDIDO " . $reg5['documento'] . "','1'," . $reg5['idProductos'] . ",'" . $reg5['cantidad'] . "','" . ($reg5['existencia'] + $reg5['cantidad']) . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . "),";
						} else {
							$sql6 .= "(" . $reg4['ingresoA'] . "," . $reg4['idPuntoIngreso'] . ",'INGRESO CANCELACION PEDIDO " . $reg5['documento'] . "','1'," . $reg5['idProductos'] . ",'" . $reg5['cantidad'] . "','" . ($reg5['existencia'] + $reg5['cantidad']) . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ");";
						}
						$i2++;
					}
					$query6 = mysql_query($sql6, dbCon::conPrincipal());
					if ($query6 == true) {
						$this->updateEstadoCotizacion('', $params['idPedido'], '1');
						$response[] = array('message' => 'success');
					} else {
						$error = mysql_error();
						$response[] = array('message' => 'failed 3', 'error' => $error, 'query' => $sql6);
					}
				} else {
					$error = mysql_error();
					$response[] = array('message' => 'failed 2', 'error' => $error, 'query' => $sql3);
				}
			} else {
				$this->updateEstadoCotizacion('', $params['idPedido'], '1');
				$response[] = array('message' => 'success');
			}
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** VALIDAR AUTH VOUCHER
	 *
	 */
	public function validarAuthVoucher($idEmpresas, $idEmisores, $noAuth)
	{
		$this->resultado = null;
		$sql = "select
                    noAutorizacion
                from
                    ventas
                where
                    idEmpresas = " . $idEmpresas . " and idEmisores=" . $idEmisores . " and noAutorizacion='" . $noAuth . "';";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/**
	 *
	 */
	public function getVales($idUsuarios, $idSucursales, $fechaCorte)
	{
		$this->resultado = null;
		$sql = "select
                    *
                from
                    valesCaja
                where
                    idSucursales=" . $idSucursales . " and idUsuarios=" . $idUsuarios . " and date(created_at) = '" . $fechaCorte . "';";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET CIERRE CAJA
	 *
	 */
	public function getTotalVentasTJDetalle($idSucursales, $idUsuarios, $fechaCorte)
	{
		$this->resultado = null;
		$sql = "select
                    c.descripcion as emisor, valor, auth
                from
                    ventasLiquidacion as a
                        LEFT join
                    ventas as b ON (a.idVentas = b.id)
                        LEFT join
                    emisores as c on(a.idEmisores=c.id)
                where
                    a.idFormasPago = 5
                    and b.fechaFactura = '" . $fechaCorte . "'
                    and idSucursales = " . $idSucursales . "
                    and anulacion = 0
                    and idUsuarios = " . $idUsuarios . " and a.idEmisores != 0
                order by
                    c.descripcion asc;";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO getTotalVentasCheques
	 *
	 */
	public function getTotalVentasChequesDetalle($idSucursales, $idUsuarios, $fechaCorte)
	{
		$this->resultado = null;
		$sql = "select
                    c.descripcion as emisor, valor, auth
                from
                    ventasLiquidacion as a
                        LEFT join
                    ventas as b ON (a.idVentas = b.id)
                        LEFT join
                    emisores as c on(a.idEmisores=c.id)
                where
                    a.idFormasPago = 4
                    and b.fechaFactura = '" . $fechaCorte . "'
                    and idSucursales = " . $idSucursales . "
                    and anulacion = 0
                    and idUsuarios = " . $idUsuarios . "
                order by
                    c.descripcion asc;";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET CIERRE CAJA
	 *
	 */
	public function getTotalVentaExencionDetalle($idSucursales, $idUsuarios, $fechaCorte)
	{
		$this->resultado = null;
		$sql = "select
                    a.valor,auth
                from
                    ventasLiquidacion as a
                        inner join
                    ventas as b ON (a.idVentas = b.id)
                where
                    a.idFormasPago = 3
                    and b.fechaFactura = '" . $fechaCorte . "'
                    and idSucursales = " . $idSucursales . "
                    and anulacion = 0
                    and idUsuarios = " . $idUsuarios . " and a.valor!='0.00';";
		//echo $sql . '\n';
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO ventasCredito
	 *
	 */
	public function ventasCredito($idClientes, $idVentas, $documento, $total, $anticipo, $saldo, $fecha, $idUsuarios, $idEmpresas)
	{
		$sql = " insert into cxc(idClientes,idVentas,idTipoDocumento,idDocumento,facLiquidadas,creditos,debitos,saldo,created_at,idUsuarios,idEmpresas)"
			. "values('" . $idClientes . "'," . $idVentas . ",1,'" . $documento . "',null,'" . $total . "','" . $anticipo . "','" . $saldo . "','" . $fecha . "'," . $idUsuarios . "," . $idEmpresas . ");";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			error_log('venta credito ingresado exitosamente idCliente:' . $idClientes);
		} else {
			error_log('error al ingresar venta credito idCliente: ' . $sql);
		}
	}

	//
	public function ventasCreditoAgenciaViajes($params, $idVenta, $doc, $credito, $debito, $tipoDocumento, $idUsuarios, $idEmpresas)
	{
		$response = "";
		$sql = "";
		$saldoTotal = 0;

		//INSERTA EN CXC PARA GUARDAR REGISTRO EN ESTADO DE CUENTA
		$sql = "insert into cxc(idClientes,idVentas,idTipoDocumento,idDocumento,creditos,debitos,saldo,idUsuarios,idEmpresas)"
			. "values(" . $params['idClientes'] . "," . $idVenta . "," . $params['idTipoDocumento'] . ",'" . $doc . "'," . $credito . "," . $debito . "," . ($credito - $debito) . "," . $idUsuarios . "," . $idEmpresas . ");";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query) {
			error_log('cxc actualizada');
		} else {
			$response[] = array('message' => mysql_error(), 'query' => $sql3);
			return $response;
		}
	}

	public function CrearCXPAgenciaViajes($params, $idVenta, $doc, $credito, $debito, $tipoDocumento, $idUsuarios, $idEmpresas, $boletos)
	{
		$response = "";
		$sql = "";
		$aPagarTotal = 0;
		//INSERTA EN CXC PARA GUARDAR REGISTRO EN ESTADO DE CUENTA
		foreach ($boletos as $key => $value) {
			$sql = "SELECT  * FROM vw_boletos where idBoleto='" . $value['idBoleto'] . "' ";
			//echo $sql;
			$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
			while ($reg = mysql_fetch_assoc($query)) {
				$aPagarTotal += $reg['aPagar'];
			}
		}
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query) {
			error_log('cxc actualizada');
		}
		//INSERTA EN CXC PARA GUARDAR REGISTRO EN ESTADO DE CUENTA
		$sql = "insert into cxp(idProveedores,idCompras,idTipoDocumento,idDocumento,creditos,debitos,saldo,idUsuarios,idEmpresas)"
			. "values(" . $params['idProveedor'] . "," . $idVenta . "," . $params['idTipoDocumento'] . ",'" . $doc . "'," . $aPagarTotal . "," . $debito . "," . ($aPagarTotal - $debito) . "," . $idUsuarios . "," . $idEmpresas . ");";
		//  echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query) {
			error_log('cxc actualizada');
		} else {
			$response[] = array('message' => mysql_error(), 'query' => $sql3);
			return $response;
		}
	}

	/** METODO getPedido
	 *
	 */
	public function getPedido($params)
	{
		$this->resultado = null;
		$filter = "";
		$filter2 = "id=" . $params['idPedido'] . "";
		if ($params['action'] != 'edit') {
			if ($params['idEmpresas'] == 6) {
				//$filter = "estado=4";
				$filter = "estado=1";
			} else {
				$filter = "estado=1";
			}
			$filter2 = " and id=" . $params['idPedido'] . "";
		}
		$sql = "SELECT
                    *, SUBSTRING_INDEX(TRIM(documento), '-', - 1) AS noPedido,
                    date_format(created_at,'%d-%m-%Y') as fechaPedido
                FROM
                    pedidos
                WHERE
                    " . $filter . " " . $filter2 . " and idEmpresas=" . $params['idEmpresas'] . ";";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO cancelarPedido
	 *
	 */
	public function updateEstadoPedido($idPedido, $idVenta, $estado)
	{
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

	/** METODO consultarPedidos
	 *
	 */
	public function consultarPedidos($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " date(a.created_at) between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['estadoPedido'] != "") {
			$filtros .= " and `a`.`estado`='" . $params['estadoPedido'] . "'";
		}
		if ($params['cliente'] != "") {
			$cliente = str_replace(" ", "%", $params['cliente']);
			$filtros .= " and a.nombre like '%" . $cliente . "%'";
		}
		if ($params['noPedido'] != "") {
			$filtros .= " and a.documento='" . $params['noPedido'] . "'";
		}
		if ($params['idVendedores'] != "") {
			$filtros .= " and a.idUsuarios='" . $params['idVendedores'] . "'";
		}
		if ($params['idSucursales'] != "") {
			$filtros .= " and a.idSucursales=" . $params['idSucursales'] . "";
		}
		$sql = "SELECT
                    a.id as idPedido,
                    date_format(a.created_at, '%d-%m-%Y') as fecha,
                    a.documento,
                    a.nit,
                    a.nombre,
                    a.direccion,
                    b.userName as vendedor,
                    (CASE `a`.`estado`
                        WHEN 1 THEN 'Abierto'
                        WHEN 2 THEN 'Cancelado'
                        WHEN 3 THEN 'Facturado'
                        WHEN 4 THEN 'Confirmado'
                    END) AS `estado`,
                    c.descripcion as idSucursales,
                    a.total,
                    d.nombreComercial as idEmpresas,
                    a.observaciones,
                    ifnull(upper((concat(e.serie,'-',e.correlativo))),'N/A') as noVentas,
                    if(estadoOrden='1','NO ENTREGADO','ENTREGADO') as estadoOrden
                FROM
                    pedidos AS a
                        LEFT JOIN
                    usuarios AS b ON (a.idUsuarios = b.id)
                        LEFT JOIN
                    sucursales AS c ON (a.idSucursales = c.id)
                        LEFT JOIN
                    empresas AS d ON (a.idEmpresas = d.id)
                            LEFT JOIN
                    ventas as e on(a.idVentas=e.id)
                where
                    " . $filtros . " and a.idEmpresas=" . $params['idEmpresas'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO savePartidaAutomatica
	 *
	 */
	public function savePartidaAutomatica($params)
	{
		$response = "";
		$admin = new Admin();
		$doc = $admin->getCorrelativoPartidas($params['idEmpresas']);
		//
		$sql = "insert into partidas (numero,partida_at,descripcion,idTipoOperacionPartida,idUsuarios,idEmpresas,created_at)"
			. " values('" . $doc[0]['correlativo'] . "','" . $params['partida_at'] . "','" . $params['conceptoCompra'] . "'," . $params['idTipoOperacionPartida'] . "," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$idPartida = mysql_insert_id();
			$this->savePartidaDetalleAutomatica(mysql_insert_id(), $params);
			//
			$updateCorrelativo = $admin->updateCorrelativoPartidas($doc[0]['idDocumentos'], $doc[0]['correlativo'], $params['idEmpresas']);
			//
			$sql3 = "update ventas set idFormatos=" . $params['idFormato'] . ",idPartidas=" . $idPartida . " where id=" . $params['idVenta'] . ";";
			$query3 = mysql_query($sql3, dbCon::conPrincipal());
			if ($query3 == true) {
				error_log('update ventas idVenta= ' . $params['idVenta'] . '');
				error_log(array('message' => 'success', 'idPartida' => $idPartida));
			} else {
				error_log('error update ventas idVenta= ' . $params['idVenta'] . ' Query: ' . $sql3);
			}
		} else {
			error_log(array('message' => 'failed', 'Query' => $sql));
		}
	}

	/** METODO partidaDetalle
	 *
	 */
	public function savePartidaDetalleAutomatica($idPartida, $params)
	{
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
			$i++;
		}
		//echo $sql;
		//exit();
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			error_log('insert true detalle partida # ' . $idPartida);
		} else {
			error_log('error sql: ' . $sql);
		}
	}

	/** METODO examenesPedidos
	 *
	 */
	public function examenesPedido($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    idPedidos,idFamiliaNivel1
                FROM
                    pedidosDetalle as a inner join productos as b on(a.idProductos=b.id)
                WHERE
                    idPedidos=" . $params['idPedido'] . "
                group by
                    idFamiliaNivel1;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO generarCotizacion
	 *
	 */
	public function generarCotizacion($params, $idUsuarios, $idSucursales, $idEmpresas, $dbProject)
	{
		$response = "";
		$clientes = $this->clientes($params, $idEmpresas);
		if ($clientes) {
			// GUARDA COTIZACION
			$sql = "insert into cotizaciones"
				. " values(null
                        ,'" . $params['correlativo'] . "'
                        ,'" . $params['nit'] . "'
                        ,'" . $params['nombre'] . "'
                        ,'" . $params['telefono'] . "'
                        ,'" . $params['direccion'] . "'
                        ,'" . $params['total'] . "'
                        ,'1'
                        ,'" . $params['observaciones'] . "'
                        ,'" . $params['tipoVenta'] . "'
                        ,'" . $params['idVendedores'] . "'
                        ,'" . $idSucursales . "'
                        ,'" . $idEmpresas . "'
                        ,'" . date("Y-m-d", strtotime($params['fechaCotizacion'])) . "'
                     ,null,"
				. $params['idClientes'] . ");";
			//echo $query;
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query) {
				//OBTIENE ID COTIZACION PARA ACTUALIZAR DETALLE DE COTIZACION
				$idCotizacion = mysql_insert_id();
				$sql2 = "update cotizacionesDetalle set idCotizaciones=" . $idCotizacion . " "
					. "where idCotizaciones is null and idUsuarios=" . $idUsuarios . " and idSucursales=" . $idSucursales . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//ACTUALIZAR CORRELATIVO DE DOCUMENTO
					$admin = new Admin();
					$correlativo = explode('-', $params['correlativo']);
					$updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $correlativo[1], $idEmpresas);
					//ENVIO DE CORREO
					if ($dbProject === 'erp_suple') {
						$this->envioMailCotizacion($idCotizacion);
					}
					//
					$response[] = array('message' => 'success', 'idCotizacion' => $idCotizacion);
				} else {
					$error = mysql_error();
					$response[] = array('message' => 'failed step 2', 'error' => $error, 'query' => $sql2);
				}
			} else {
				$error = mysql_error();
				$response[] = array('message' => 'failed step 1', 'error' => $error, 'query' => $sql);
			}
		} else {
			$response[] = array('message' => 'failed step: clientes');
		}
		return $response;
	}

	/** METODO getProductosCotizacion
	 *
	 */
	public function getProductosCotizacion($params)
	{
		$this->resultado = null;
		//
		$sql1 = "SELECT
                    ingresoA, idPuntoIngreso,agruparItems
                FROM
                    facturacionConf
                WHERE
                    opcion = 'pedidos' AND idEmpresas = " . $params['idEmpresas'] . ";";
		$query1 = mysql_query($sql1, dbCon::conPrincipal());
		$reg1 = mysql_fetch_assoc($query1);
		//
		$filter = " a.idCotizaciones is null and a.idUsuarios=" . $params['idUsuarios'] . " and a.idSucursales =" . $params['idSucursales'] . "";
		if ($params['idCotizacion'] != '') {
			$filter = " a.idCotizaciones= " . $params['idCotizacion'] . "";
		}
		$sql = "SELECT
                    b.sku AS 'sku', b.descLarga, a.*,
                    (select saldo from inventarios where ingresoA=" . $reg1['ingresoA'] . " and idPuntoIngreso=" . $reg1['idPuntoIngreso'] . " and idProductos=b.id order by id desc limit 1) as existencia
                FROM
                    cotizacionesDetalle AS a
                        LEFT JOIN
                    productos AS b ON (a.idProductos = b.id)
                WHERE
                    " . $filter . ";";
		//echo $sql . "\n";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO agregarProductoCotizacion
	 *
	 */
	public function agregarProductoCotizacion($params)
	{
		$response = [];

		// Paso 1: Consultar si el producto ya está en la lista
		$filter = "idCotizaciones IS NULL";
		if (!empty($params['idCotizacion'])) {
			$filter = "idCotizaciones = " . intval($params['idCotizacion']);
		}

		$sql = "SELECT * FROM cotizacionesDetalle
            WHERE idProductos=" . intval($params['idProducto']) . " 
            AND " . $filter . " 
            AND idUsuarios=" . intval($params['idUsuarios']) . " 
            AND idSucursales=" . intval($params['idSucursales']) . "
            AND (UPPER(descLarga) = UPPER('" . mysql_real_escape_string($params['descProducto']) . "')
                 OR UPPER('" . mysql_real_escape_string($params['codigo']) . "') = 'SC');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		$reg = mysql_fetch_assoc($query);

		if (
			mysql_num_rows($query) == 0
			|| (strtoupper(trim($params['codigo'])) === 'SC'
				&& strtoupper(trim($params['descProducto'])) !== strtoupper(trim($reg['descLarga'])))
		) {
			// Si no está o si es SC con descripción diferente, inserta un nuevo registro
			$sql = "INSERT INTO cotizacionesDetalle
                (idCotizaciones, tipoProducto, idProductos, descLarga, cantidad, precio, total, idUsuarios, idSucursales, idEmpresas)
                VALUES (
                    " . (!empty($params['idCotizacion']) ? intval($params['idCotizacion']) : "NULL") . ",
                    '" . mysql_real_escape_string($params['tipoProducto']) . "',
                    " . intval($params['idProducto']) . ",
                    '" . mysql_real_escape_string(trim($params['descProducto'])) . "',
                    " . floatval($params['cantidad']) . ",
                    " . floatval($params['precio']) . ",
                    " . floatval($params['total']) . ",
                    " . intval($params['idUsuarios']) . ",
                    " . intval($params['idSucursales']) . ",
                    " . intval($params['idEmpresas']) . ");";
			$query = mysql_query($sql, dbCon::conPrincipal());
			$response[] = $query ? ['message' => 'success'] : ['message' => 'failed insert', 'Query' => $sql];
		} else {
			// Si está, actualiza cantidad, precioVenta, costo y totales
			$newCantidad = $reg['cantidad'] + $params['cantidad'];
			$newTotal = $reg['precio'] * $newCantidad;

			$sql = "UPDATE cotizacionesDetalle 
                SET cantidad='" . floatval($newCantidad) . "', total='" . floatval($newTotal) . "' 
                WHERE idProductos=" . intval($params['idProducto']) . " 
                AND descLarga='" . mysql_real_escape_string(trim($reg['descLarga'])) . "' 
                AND " . $filter . " 
                AND idUsuarios=" . intval($params['idUsuarios']) . " 
                AND idSucursales=" . intval($params['idSucursales']) . ";";
			$query = mysql_query($sql, dbCon::conPrincipal());
			$response[] = $query ? ['message' => 'success'] : ['message' => 'failed update', 'Query' => $sql];
		}

		return $response;
	}

	/** METODO cancelarCotizacion
	 *
	 */
	public function cancelarCotizacion($params)
	{
		$response = "";
		$sql = "";
		if ($params['idCotizacion'] != '') {
			$sql = "update cotizaciones set estado='2' where id=" . $params['idCotizacion'] . ";";
		} else {
			$sql = "delete from cotizacionesDetalle where idCotizaciones is null  and idUsuarios=" . $params['idUsuarios'] . ";";
		}
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO eliminarProductoCotizacion
	 *
	 */
	public function eliminarProductoCotizacion($params)
	{
		$response = "";
		$sql = "delete from cotizacionesDetalle where id=" . $params['item'] . ";";
		//echo $sql."<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO consultarCotizaciones
	 *
	 */
	public function consultarCotizaciones($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and date(a.created_at) between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['estadoCotizacion'] != "") {
			$filtros .= " and `a`.`estado`='" . $params['estadoCotizacion'] . "'";
		}
		if ($params['cliente'] != "") {
			$cliente = str_replace(" ", "%", $params['cliente']);
			$filtros .= " and a.nombre like '%" . $cliente . "%'";
		}
		if ($params['noCotizacion'] != "") {
			$filtros .= " and a.documento='" . $params['noCotizacion'] . "'";
		}
		if ($params['idVendedores'] != "") {
			$filtros .= " and a.idUsuarios='" . $params['idVendedores'] . "'";
		}
		$sql = "SELECT
                    a.id AS idCotizacion,
                    DATE_FORMAT(a.created_at, '%d-%m-%Y') AS fecha,
                    a.documento,
                    a.nit,
                    a.nombre,
                    a.direccion,
                    b.userName AS vendedor,
                    (CASE `a`.`estado`
                        WHEN 1 THEN 'Abierto'
                        WHEN 2 THEN 'Cancelado'
                        WHEN 3 THEN 'Procesada'
                    END) AS `estado`,
                    c.descripcion AS idSucursales,
                    a.total,
                    d.nombreComercial AS idEmpresas,
                    a.observaciones,
                    ifnull(e.documento,'N/A') as noPedido
                FROM
                    cotizaciones AS a
                        LEFT JOIN
                    usuarios AS b ON (a.idUsuarios = b.id)
                        LEFT JOIN
                    sucursales AS c ON (a.idSucursales = c.id)
                        LEFT JOIN
                    empresas AS d ON (a.idEmpresas = d.id)
                        LEFT JOIN
                    pedidos as e ON(a.idPedidos=e.id)
                where
                    a.idEmpresas=" . $params['idEmpresas'] . " " . $filtros . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO getCotizacion
	 *
	 */
	public function getCotizacion($params, $idSucursales, $idEmpresas)
	{
		$this->resultado = null;
		$filter = "";
		$filter2 = "id=" . $params['idCotizacion'] . "";
		if ($params['action'] != 'edit') {
			$filter = "estado=1";
			$filter2 = " and documento='" . $params['idCotizacion'] . "'";
		}
		$sql = "SELECT
                    *, SUBSTRING_INDEX(TRIM(documento), '-', - 1) AS noCotizacion,
                    date_format(created_at,'%d-%m-%Y') as fechaCotizacion
                FROM
                    cotizaciones
                WHERE
                    " . $filter . " " . $filter2 . ";";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO updateEstadoCotizacion
	 *
	 */
	public function updateEstadoCotizacion($idCotizaciones, $idPedido, $estado)
	{
		switch ($estado) {
			case '1':
				$sql = "update cotizaciones set estado='1',idPedidos=NULL where idPedidos=" . $idPedido . ";";
				$query = mysql_query($sql, dbCon::conPrincipal());
				if ($query == true) {
					error_log(array('message' => 'success'));
				} else {
					error_log(array('message' => 'failed', 'Query' => $sql));
				}
				break;
			default:
				foreach ($idCotizaciones as $key => $value) {
					$sql = "update cotizaciones set estado='3',idPedidos=" . $idPedido . " where documento='" . $value['cotizacion'] . "';";
					$query = mysql_query($sql, dbCon::conPrincipal());
					if ($query == true) {
						error_log(array('message' => 'success'));
					} else {
						error_log(array('message' => 'failed', 'Query' => $sql));
					}
				}
				break;
		}
	}

	/** METODO consultarVales
	 *
	 */
	public function consultarVales($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " date(a.created_at) between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		$sql = "SELECT
                    a.id as idVale,
                    DATE_FORMAT(a.created_at, '%d-%m-%Y') AS fecha,
                    a.solicitado,
                    a.monto,
                    a.observaciones,
                    b.userName as pagador,
                    c.descripcion as sucursal
                FROM
                    valesCaja AS a
                        INNER JOIN
                    usuarios AS b ON (a.idUsuarios = b.id)
                        INNER JOIN
                    sucursales AS c ON (a.idSucursales = c.id)
                where
                    " . $filtros . " AND a.idEmpresas=" . $params['idEmpresas'] . ";";
		//echo $sql . "\n";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO actualizarCotizacion
	 *
	 */
	public function actualizarCotizacion($params)
	{
		$response = "";
		// GUARDA COTIZACION
		$sql = "update cotizaciones set nit='" . $params['nit'] . "',nombre='" . $params['nombre'] . "',telefono='" . $params['telefono'] . "',direccion='" . $params['direccion'] . "',total='" . $params['total'] . "',observaciones='" . $params['observaciones'] . "',idTipoVenta='" . $params['tipoVenta'] . "',created_at='" . date("Y-m-d", strtotime($params['fechaCotizacion'])) . "'
                where id=" . $params['idCotizacion'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query) {
			$response[] = array('message' => 'success', 'idCotizacion' => $params['idCotizacion']);
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO actualizarCotizacion
	 *
	 */
	public function actualizarPedido($params)
	{
		$response = "";
		// GUARDA COTIZACION
		$sql = "update pedidos set nit='" . $params['nit'] . "',nombre='" . $params['nombre'] . "',telefono='" . $params['telefono'] . "',direccion='" . $params['direccion'] . "',total='" . $params['total'] . "',saldo='" . $params['total'] . "',observaciones='" . $params['observaciones'] . "',idTipoVenta='" . $params['tipoVenta'] . "',created_at='" . date("Y-m-d", strtotime($params['fechaPedido'])) . "'
                where id=" . $params['idPedido'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query) {
			$response[] = array('message' => 'success', 'idPedido' => $params['idPedido']);
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO ELIMINAR FACTURA
	 *
	 */
	public function eliminarFactura($params, $idEmpresas, $dbProject)
	{
		$response = "";
		if ($_SESSION['dbProject'] == 'erp_laxTravelTopacio') {
			//paso 1 anular factura
			$sql1 = "delete from ventas where id=" . $params['idFactura'] . ";";
			$query1 = mysql_query($sql1, dbCon::conPrincipal());
			if ($query1 == true) {
				//paso 2: Obtiene correlativo de movimiento
				$agencia = new agenciasViajes();
				$update = $agencia->updateAgenciasViajes($params['idFactura'], $idEmpresas);
				$response[] = array('message' => 'success');
			} else {
				$response[] = array('message' => 'failed step 1', 'Query' => $sql1);
			}
		} else {
			//paso 1 anular factura
			$sql1 = "delete from ventas where id=" . $params['idFactura'] . ";";
			$query1 = mysql_query($sql1, dbCon::conPrincipal());
			if ($query1 == true) {
				//paso 2 ingresar los productos al inventario
				$this->movimientoInventario($params['idFactura'], 'INGRESO POR ELIMINACION DE FAC ' . $params['documento'], 'ingreso', date('Y-m-d'));
				//paso 3 habilitar pedido gestionado
				if ($dbProject === 'erp_gsp') {
					$this->updateEstadoPedido('', $params['idFactura'], '4');
				} else {
					$this->updateEstadoPedido('', $params['idFactura'], '1');
				}
				$this->eliminarFacCXC($params['idFactura']);
				$this->liberarVales($params['idFactura']);
				//
				$response[] = array('message' => 'success');
			} else {
				$response[] = array('message' => 'failed step 1', 'Query' => $sql1);
			}
		}
		return $response;
	}

	/** METODO GET PRODUCTOS VENTA
	 *
	 */
	public function getProductosVenta($params)
	{
		$this->resultado = null;
		$filtro = "";
		if ($params['idVenta'] != '') {
			$filtro = "idVentas = " . $params['idVenta'] . "";
		} else {
			$filtro = "idVentas = 0 and idUsuarios=" . $params['idUsuarios'] . "";
		}
		$sql = "SELECT
                    a.id,
                    a.idProductos,
                    a.sku AS 'codigo',
                    a.descLarga,
                    a.cantidad,
                    a.precio,
                    a.total,
                    a.costo,
                    a.totalCosto,
                    round((total/1.12)*0.12,2) as iva,
                    round((total/1.12),2) as subtotal,
                    (a.total*(" . ($params['descuentoP'] ?: 0) . "/100)) as descuento,
                    (a.total-(a.total*(" . ($params['descuentoP'] ?: 0) . "/100))) as totalDescuento,
                    ((a.total-(a.total*(" . ($params['descuentoP'] ?: 0) . "/100)))/1.12)* 0.12 as ivaDescuento,
                    (a.total-(a.total*(" . ($params['descuentoP'] ?: 0) . "/100)))/1.12 as subtotalDescuento,
                    (a.cantidad* CASE a.sku
                        WHEN 7 THEN (a.precio+1.30)
                        WHEN 8 THEN (a.precio+4.60)
                        WHEN 9 THEN (a.precio+4.70)
                    END) as totalG,
                    case idProductos
                          when 9 then (cantidad*4.70)
                          when 8 then (cantidad*4.60)
                          when 7 then (cantidad*1.30)
                          else 0
					end as exento
                FROM
                    ventasDetalle AS a
                WHERE
                    " . $filtro . ";";
		//echo $sql . "\n";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO AGREGAR BOLETO AL DETALLE
	 *
	 */
	public function agregarBoletoDetalle($params)
	{

		$sql = "INSERT INTO ventasDetalle(idventas,idBoleto,precio,total,idUsuarios,idSucursales,idEmpresas)"
			. " values('0','" . $params['noBoleto'] . "','" . $params['montoTotal'] . "','" . $params['montoTotal']
			. "', '" . $params['idUsuarios'] . "', '" . $params['idSucursales'] . "', '" . $params['idEmpresas'] . "') ";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed insert', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO ELIMINAR BOLETO AL DETALLE
	 *
	 */
	public function eliminarBoletoDetalle($params)
	{
		$response = "";
		$sql = "delete from ventasDetalle "
			. "where idVentas=0 and idProductos=" . $params['idProducto'] . " and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
		//echo $sql."<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO updateItemVenta
	 *
	 */
	public function updateItemVenta($params)
	{
		$response = "";
		$sql = "update ventasDetalle set cantidad='" . $params['cantidad'] . "',precio='" . $params['precio'] . "',total='" . $params['total'] . "'"
			. "where id=" . $params['item'] . " and idProductos=" . $params['idProductos'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed');
		}
		return $response;
	}

	/** METODO envioMailCotizacion
	 *
	 */
	public function envioMailCotizacion($idCotizacion)
	{
		$email_message = new email_message_class;
		$from_address = "notifier@digicom.com.gt";
		$from_name = "Confirmacion de Cotizaciones";
		$reply_name = $from_name;
		$reply_address = $from_address;
		$reply_address = $from_address;
		$error_delivery_name = $from_name;
		$error_delivery_address = $from_address;
		$to_name = "Confirmacion de Cotizaciones";
		$to_address = $cuentareporte;
		$subject = "Confirmacion de Cotizaciones";
		$email_message->SetMultipleEncodedEmailHeader('To', array('digicom.ortiz@gmail.com' => 'Richard Ortiz', 'digicom.ortiz@hotmail.com' => 'richard Ortiz', 'digicom.ortiz@gmail.com' => 'Richard Ortiz'));
		$email_message->SetEncodedEmailHeader("From", $from_address, $from_name);
		$email_message->SetEncodedEmailHeader("Reply-To", $reply_address, $reply_name);
		$email_message->SetHeader("Sender", $from_address);
		//
		if (defined("PHP_OS") && strcmp(substr(PHP_OS, 0, 3), "WIN")) {
			$email_message->SetHeader("Return-Path", $error_delivery_address);
		}

		$email_message->SetEncodedHeader("Subject", $subject);
		//
		$text_message = "Estimados Sres: <br><br>
                     Adjunto encontrar&aacute; link de visualizacion de cotizacion<br><br>
                     http://suple.kairossoft.com.gt/views/jasper/cotizaciones.php?idCotizacion=" . $idCotizacion . "&dbProject=erp_suple
                     <br><br>
                     Atte.<br><br>
                     Sistema de Notificaciones Digicom<br>
                     <i>La informacion contenida en este Mensaje de correo electronico es confidencial, tan solo para el uso de la persona o entidad mencionada anteriormente. Si el lector de este e-mail no es el destinatario, o la persona, empleado o agente responsable de entregarlo al destinatario, se le notifica que cualquier revision, difusion, distribucion o copia de esta comunicacion esta estrictamente prohibida. Si usted recibio este e-mail por error, por favor borrelo.</i>";
		$email_message->AddHTMLPart($email_message->WrapText($text_message));
		//
		$error = $email_message->Send();
		if (strcmp($error, "")) {
			error_log('Error en envio de mail cotizaciones: ' . $error);
		} else {
			error_log('Mensaje envio exitosamente idCotizacion: ' . $idCotizacion);
		}
	}

	public function agregarTomaMedidasDetalle($params)
	{
		$response = "";
		$sql = "insert into tomaMedidasDetalle (idTomaMedidas,apellido,nombre,hombro,manga,altura,cabeza,noRecibo,observaciones,
            idUsuarios,idSucursales,idEmpresas)
            values('" . $params['idTomaMedidas'] . "','" . $params['apellido'] . "','" . $params['nombre'] . "','" . $params['hombro'] . "','" . $params['manga'] . "',"
			. "'" . $params['altura'] . "','" . $params['cabeza'] . "','" . $params['recibo'] . "','" . $params['observaciones'] . "'"
			. "," . $params['idUsuarios'] . ",'" . $params['idSucursales'] . "','" . $params['idEmpresas'] . "');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		//echo $sql;
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed');
		}
		return $response;
	}

	public function getTomaMedidasDetalle($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                  *
                FROM
                    tomaMedidasDetalle
                WHERE
                idTomaMedidas =" . $params['idTomaMedidas'] . " and
                idUsuarios=" . $params['idUsuarios'] . " and idEmpresas =" . $params['idEmpresas'] . ";";
		// echo $sql."\n";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	public function eliminarTomaMedidasDetalle($params)
	{
		$this->resultado = null;
		$sql = "DELETE FROM tomaMedidasDetalle WHERE  id =" . $params['id'] . "";

		//echo $sql."\n";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed');
		}
		return $response;
	}

	public function generarTomaMedidas($params)
	{
		$response = "";
		$sql = "insert into tomaMedidas(idPedidos,fechaEntrega,fechaGraduacion,fechaRecoger,observaciones,idUsuarios,idSucursales,idEmpresas)
            values('" . $params['idPedido'] . "','" . date("Y-m-d", strtotime($params['fechaEntrega'])) . "','"
			. date("Y-m-d", strtotime($params['fechaGraduacion'])) . "','" . date("Y-m-d", strtotime($params['fechaRecoger'])) . "','"
			. $params['observaciones'] . "','" . $params['idUsuarios'] . "','" . $params['idSucursales'] . "','" . $params['idEmpresas'] . "');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		$idTomaMedidas = mysql_insert_id();
		//echo $sql;
		if ($query == true) {
			$sql2 = "update tomaMedidasDetalle set idTomaMedidas=" . $idTomaMedidas . " where idTomaMedidas=0 and idUsuarios='" . $params['idUsuarios'] . "' and idEmpresas='" . $params['idEmpresas'] . "' ";
			//echo $sql2;
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			if ($query2 == true) {
				$response[] = array('message' => 'success', 'idTomaMedidas' => $idTomaMedidas);
			} else {
				$response[] = array('message' => 'failed');
			}
		} else {
			$response[] = array('message' => 'failed');
		}
		return $response;
	}

	public function eliminarTomaMedidas($params)
	{
		$response = "";

		$sql = "delete from tomaMedidas "
			. "where  id=" . $params['idTomaMedida'] . " and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
		//echo $sql."<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	public function getTomaMedidas($params)
	{
		$this->resultado = null;

		$sql = "SELECT t.*,p.nombre as nombreP FROM vw_tomaMedidas as t "
			. "inner join pedidos as p on p.id=t.idPedidos "
			. "WHERE  t.idEmpresas='" . $params['idEmpresas'] . "'  and t.id= '" . $params['idTomaMedidas'] . "'";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	public function actualizarTomaMedidas($params)
	{
		$response = "";
		$sql = "UPDATE tomaMedidas SET idPedidos= '" . $params['idPedido'] . "',observaciones= '" . $params['observaciones'] . "',fechaEntrega='" . date("Y-m-d", strtotime($params['fechaEntrega'])) . "',
           fechaGraduacion='" . date("Y-m-d", strtotime($params['fechaGraduacion'])) . "',fechaRecoger='" . date("Y-m-d", strtotime($params['fechaRecoger'])) . "'"
			. "WHERE id='" . $params['idTomaMedidas'] . "'";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed');
		}
		return $response;
	}

	public function cancelarTomaMedidas($idSucursales, $idUsuarios)
	{
		$response = "";
		$sql = "delete from tomaMedidasDetalle where idTomaMedidas=0 and idUsuarios=" . $idUsuarios . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO updateItemCotizacion
	 *
	 */
	public function updateItemCotizacion($params)
	{
		$response = "";
		$sql = "update cotizacionesDetalle set cantidad='" . $params['cantidad'] . "',precio='" . $params['precio'] . "',total='" . $params['total'] . "'"
			. "where id=" . $params['item'] . " and idProductos=" . $params['idProductos'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed');
		}
		return $response;
	}

	/** METODO liquidarVales
	 *
	 */
	public function liquidarVales($params)
	{
		$response = "";
		//EMITE FACTURA
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $params['idEmpresas'] . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//
		//EMITE FEL
		$responseF = "";
		//SCRIPT EMISION DE FACTURA
		$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkdULjAwMDEwODIwMDMxMC4xMDgyMDAzMTAiLCJuYmYiOjE2MzEyOTY1MDgsImV4cCI6MTY2MjQwMDUwOCwiaWF0IjoxNjMxMjk2NTA4LCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQ5MjIwIiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdDo0OTIyMCJ9.7e67_Vt28gf5C36vQ9zE-q3ekq7ZPzZWEJOm3GQQP9I";
		//URL DESARROLLO
		//$URL = "https://felgttestaws.digifact.com.gt/felapi/api/FelRequest?NIT=000107795620&TIPO=CERTIFICATE_DTE_XML_TOSIGN&FORMAT=XML";
		//URL PRODUCTIVO
		$URL = "https://felgtaws.digifact.com.gt/gt.com.fel.api.v2/api/FelRequest?NIT=" . $regE['nitWS'] . "&TIPO=CERTIFICATE_DTE_XML_TOSIGN&FORMAT=XML";
		date_default_timezone_set("America/Guatemala");
		$tipoDocumento = "FACT";
		$fechaEmision = $params['fechaFactura'];
		$horaEmision = $this->time;
		$codigoMoneda = "GTQ";
		//DATOS DEL VENDEDOR
		$nitEmisor = $regE['nit'];
		$nombreEmisor = $regE['razonSocial'];
		$codEstablecimiento = $regE['codigoEstablecimiento'];
		$nombreComercial = $regE['nombreComercial'];
		$afiliacionIVA = $regE['tipoAfiliacion'];
		$direccionEmisor = $regE['direccion'];
		$codigoPostal = "0100";
		$municipio = "GUATEMALA";
		$depto = "GUATEMALA";
		$pais = "GT";
		//DATOS DEL COMPRADOR
		$nombreComprador = $params['nombre'];
		$nitComprador = $params['nit'];
		$direccion = $this->consultaNIT($params);
		$direccionComprador = ($direccion[0]['direccion'] ?: 'CIUDAD');
		$munComprador = "GUATEMALA";
		$deptoComprador = "GUATEMALA";
		$codigoPostalComprador = "01001";
		$paisComprador = "GT";
		//GENERACION DE XML
		$xml_data = '<?xml version="1.0" encoding="UTF-8"?><dte:GTDocumento xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" Version="0.1">
                    <dte:SAT ClaseDocumento="dte">
                        <dte:DTE ID="DatosCertificados">
                            <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales Tipo="' . $tipoDocumento . '" FechaHoraEmision="' . $fechaEmision . 'T' . $horaEmision . '" CodigoMoneda="' . $codigoMoneda . '" NumeroAcceso="' . $regE['numeroAcceso'] . '"/>
                                <dte:Emisor NITEmisor="' . $nitEmisor . '" NombreEmisor="' . $nombreEmisor . '" CodigoEstablecimiento="' . $codEstablecimiento . '" NombreComercial="' . $nombreComercial . '" AfiliacionIVA="' . $afiliacionIVA . '">
                                    <dte:DireccionEmisor>
                                        <dte:Direccion>' . $direccionEmisor . '</dte:Direccion>
                                        <dte:CodigoPostal>' . $codigoPostal . '</dte:CodigoPostal>
                                        <dte:Municipio>' . $municipio . '</dte:Municipio>
                                        <dte:Departamento>' . $depto . '</dte:Departamento>
                                        <dte:Pais>' . $pais . '</dte:Pais>
                                    </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor NombreReceptor="' . $nombreComprador . '" IDReceptor="' . $nitComprador . '">
                                    <dte:DireccionReceptor>
                                        <dte:Direccion>' . $direccionComprador . '</dte:Direccion>
                                        <dte:CodigoPostal>' . $codigoPostalComprador . '</dte:CodigoPostal>
                                        <dte:Municipio>' . $munComprador . '</dte:Municipio>
                                        <dte:Departamento>' . $deptoComprador . '</dte:Departamento>
                                        <dte:Pais>' . $paisComprador . '</dte:Pais>
                                    </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                    <dte:Frase TipoFrase="' . $regE['tipoFrase'] . '" CodigoEscenario="' . $regE['codigoEscenario'] . '" />';
		if ($regE['agenteRetenedor'] === '1') {
			$xml_data .= '<dte:Frase TipoFrase="2" CodigoEscenario="1"/>';
		}
		$xml_data .= '</dte:Frases>
        								<dte:Items>';
		$idp = 0;
		$iva = 0;
		$subtotal = 0;
		$total = 0;
		foreach ($params['valesLiquidar'] as $key => $value) {
			$idp += $value['idp'];
			$iva += $value['iva'];
			$subtotal += $value['subtotal'];
			$total += $value['total'];
			//DETALLE DE LA FACTURA
			$xml_data .= '<dte:Item NumeroLinea="' . ($key + 1) . '" BienOServicio="B">
                        <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                        <dte:UnidadMedida>GAL</dte:UnidadMedida>
                        <dte:Descripcion>' . $value['tipo'] . '</dte:Descripcion>
                        <dte:PrecioUnitario>' . round($value['precioUnitario'], 6) . '</dte:PrecioUnitario>
                        <dte:Precio>' . round($value['precio'], 6) . '</dte:Precio>
                        <dte:Descuento>0</dte:Descuento>';
			//GENERA IMPUESTOS SI LA AFILIACION NO ES PEQUENO CONTRIBUYENTE
			$xml_data .= '<dte:Impuestos>
                                <dte:Impuesto>
                                    <dte:NombreCorto>IVA</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                    <dte:MontoGravable>' . round($value['subtotal'], 6) . '</dte:MontoGravable>
                                    <dte:MontoImpuesto>' . round($value['iva'], 6) . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                                <dte:Impuesto>
                                    <dte:NombreCorto>PETROLEO</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>' . $value['codCombustible'] . '</dte:CodigoUnidadGravable>
                                    <dte:CantidadUnidadesGravables>' . $value['cantidad'] . '</dte:CantidadUnidadesGravables>
                                    <dte:MontoImpuesto>' . round($value['idp'], 6) . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                            </dte:Impuestos>';
			$xml_data .= '<dte:Total>' . round($value['total'], 3) . '</dte:Total>
                          </dte:Item>';
		}
		//TERMINA DETALLE DE LA FACTURA
		$xml_data .= '</dte:Items>';
		$xml_data .= '<dte:Totales>';
		//GENERA IMPUESTOS SI LA AFILIACION NO ES PEQUENO CONTRIBUYENTE
		$xml_data .= '<dte:TotalImpuestos>
                                        <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . round($iva, 6) . '" />
                                        <dte:TotalImpuesto NombreCorto="PETROLEO" TotalMontoImpuesto="' . round($idp, 6) . '"/>
                                    </dte:TotalImpuestos>';
		$xml_data .= '<dte:GranTotal>' . round($total, 3) . '</dte:GranTotal>
                                </dte:Totales>';
		$xml_data .= '</dte:DatosEmision>
                        </dte:DTE>
                        <dte:Adenda>
                            <dtecomm:Informacion_COMERCIAL xsi:schemaLocation="https://www.digifact.com.gt/dtecomm"
                                xmlns:dtecomm="https://www.digifact.com.gt/dtecomm">
                                <dtecomm:InformacionAdicional Version="V7.20012020">
                                    <dtecomm:REFERENCIA_INTERNA>' . $regE['numeroAcceso'] . '</dtecomm:REFERENCIA_INTERNA>
                                    <dtecomm:FECHA_REFERENCIA>' . $fechaEmision . 'T' . $horaEmision . '</dtecomm:FECHA_REFERENCIA>
                                    <dtecomm:VALIDAR_REFERENCIA_INTERNA>VALIDAR</dtecomm:VALIDAR_REFERENCIA_INTERNA>
                                </dtecomm:InformacionAdicional>
                            </dtecomm:Informacion_COMERCIAL>
                        </dte:Adenda>
                    </dte:SAT>
                </dte:GTDocumento>';
		//            echo $xml_data;
		//            exit();
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Authorization: ' . $token . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['Codigo'] === 1) {
			// GUARDA VENTA
			$sql = "insert into ventas
                values(
                    null,
                    '" . $response['Serie'] . "',
                    '" . $response['NUMERO'] . "',
                    '" . $params['fechaFactura'] . "',
                    '" . $total . "',
                    '" . $subtotal . "',
                    '" . $params['descuentoM'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $total . "',
                    '" . $params['anticipo'] . "',
                    '" . $params['saldo'] . "',
                    '" . $iva . "',
                    '" . $params['tasaCambio'] . "',
                    '" . $params['totalDolares'] . "',
                    '" . $params['totalEnLetras'] . "',
                    null,
                    '" . $params['nit'] . "',
                    '" . $params['nombre'] . "',
                    '" . $direccion[0]['direccion'] . "',
                    '2',
                    '2',
                    '" . $params['observaciones'] . "',
                    '" . $params['idUsuarios'] . "',
                    '" . $params['idSucursales'] . "',
                    '" . $params['idEmpresas'] . "',
                    '" . $this->timestamp . "',
                    null,
                    0,
                    0,
                    0,
                    0,
                    null,
                    null,
                    null,
                    '" . $params['idClientes'] . "',
                    '" . $params['idUsuarios'] . "','" . $response['Autorizacion'] . "','" . $response['Fecha_DTE'] . "',1,0,'" . $xml_data . "','" . $regE['numeroAcceso'] . "','FACT');";
			$query = mysql_query($sql, dbCon::conPrincipal());
			//echo $query;
			if ($query) {
				//OBTIENE ID VENTA
				$idVenta = mysql_insert_id();
				//INGRESAR DETALLE DE VENTAS
				foreach ($params['valesLiquidar'] as $key => $value) {
					$sqlD = "insert into ventasDetalle values(null," . $idVenta . ",'Producto'," . $value['idProductos'] . ",'" . $value['idProductos'] . "','" . $value['tipo'] . "','" . $value['cantidad'] . "','" . $value['precioUnitario2'] . "','0','" . $value['costo'] . "','" . $value['total'] . "','0','" . $value['totalCosto'] . "','" . $params['idUsuarios'] . "','" . $params['idSucursales'] . "','" . $params['idEmpresas'] . "');";
					$queryD = mysql_query($sqlD, dbCon::conPrincipal());
				}
				//INGRESO DE VENTA AL CREDITO
				$documento = $response['Serie'] . '-' . $response['NUMERO'];
				$this->ventasCredito($params['nit'], $idVenta, $documento, $params['total'], $params['anticipo'], $params['saldo'], date("Y-m-d", strtotime($params['fechaFactura'])), $params['idUsuarios'], $params['idEmpresas']);
				//ELIMINAR DE LA TABLA CXC EL REGISTRO DE VALES
				$vales = explode(";", $params['vales']);
				foreach ($vales as $key => $value) {
					$sql1 = "delete from cxc where idDocumento='" . $value . "' and idEmpresas=" . $params['idEmpresas'] . ";";
					$query1 = mysql_query($sql1, dbCon::conPrincipal());
					if ($query1) {
						$sql2 = "update vales set estado=2, idVentas=" . $idVenta . " where concat(serie,'-',documento)='" . $value . "';";
						$query2 = mysql_query($sql2, dbCon::conPrincipal());
					}
				}
				//UPDATE NUMERO DE ACCESO
				$sql7 = "update empresas set numeroAcceso=(" . $regE['numeroAcceso'] . ")+1 where id=" . $params['idEmpresas'] . ";";
				$query7 = mysql_query($sql7, dbCon::conPrincipal());
				//
				$responseF[] = array('message' => 'success', 'idVenta' => $idVenta);
			} else {
				$error = mysql_error();
				$responseF[] = array('message' => 'failed step 1', 'Query' => $sql, 'error' => $error);
			}
		} else {
			//echo $xml_data;
			$responseF[] = array('message' => 'failed step FEL', 'error' => $response['ResponseDATA1']);
		}
		return $responseF;
	}

	/** METODO saveFactRecurrente
	 *
	 */
	public function saveFactRecurrente($params)
	{
		$response = "";
		$sql = "insert into facturacionRecurrente (id,idClientes,idProductos,monto,idTipoCuota,idPeriodoPago,fechaInicio,fechaFin,noCuotas,status,idEmpresas,created_at) "
			. " values(null," . $params['idClientes'] . "," . $params['idProductos'] . ",'" . $params['monto'] . "'," . $params['idTipoCuota'] . "," . $params['idPeriodoPago'] . ",'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "','" . date("Y-m-d", strtotime(($params['fechaFin'] ?: '00-00-0000 00:00:00'))) . "','" . $params['noCuotas'] . "',1," . $params['idEmpresas'] . ",'" . $this->timestamp . "');";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'query' => $sql);
		}
		return $response;
	}

	/**
	 * getFactRecurrente
	 */
	public function getFactRecurrente($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    a.id,
                    idClientes,
                    concat(b.sku,'-',b.descLarga) as idProductos,
                    monto,
                    case idTipoCuota
                        when 1 then 'Indefinida'
                        when 2 then 'Periodo'
                    end as idTipoCuota,
                    case idPeriodoPago
                        when 1 then 'Mensual'
                        when 2 then 'Bimensual'
                        when 3 then 'Trimestral'
                        when 4 then 'Semestral'
                        when 5 then 'Anual'
                    end as idPeriodoPago,
                    date_format(fechaInicio,'%d-%m-%Y') as fechaInicio,
                    date_format(fechaFin,'%d-%m-%Y') as fechaFin,
                    noCuotas,
                    cuotasPagadas,
                    cuotasPorPagar,
                    status,
                    a.idEmpresas,
                    a.created_at
                FROM
                    facturacionRecurrente as a
                    inner join productos as b on(a.idProductos=b.id)
                WHERE
                    idClientes=" . $params['idClientes'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO eliminarFactRecurrente
	 *
	 */
	public function eliminarFactRecurrente($params)
	{
		$response = "";
		$sql = "delete from facturacionRecurrente where id=" . $params['item'] . " and idClientes= " . $params['idClientes'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'query' => $sql);
		}
		return $response;
	}

	public function eliminarFacCXC($idFactura)
	{
		$sql = "delete from cxc where idVentas=" . $idFactura . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
	}

	public function liberarVales($idFactura)
	{
		$sql = "update vales set estado=1,idVentas=null where idVentas=" . $idFactura . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
	}

	public function eliminarRecibo($params)
	{
		$response = "";
		$sql = "SELECT * FROM cxc WHERE id = " . $params['idRecibo'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		$reg = mysql_fetch_assoc($query);
		$factura = $reg['idVentas'];
		$monto = $reg['debitos'];
		//elimina el recibo
		$sql1 = "delete from cxc where id=" . $params['idRecibo'] . ";";
		$query1 = mysql_query($sql1, dbCon::conPrincipal());
		if ($query1) {
			//revierte el saldo
			$sql2 = "update ventas set saldo=(saldo+" . $monto . ") where id=" . $factura . ";";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			if ($query2) {
				$response[] = array('message' => 'success');
			} else {
				$response[] = array('message' => 'failed step 2', 'query' => $sql2);
			}
		} else {
			$response[] = array('message' => 'failed step 1', 'query' => $sql1);
		}
		return $response;
	}

	public function consultaNit($params)
	{
		$responseF = [];
		$URL = "https://www.ingface.net/ServiciosIngface/ingfaceWsServices";
		$xml_data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://services.ws.ingface.com/">
                    <soapenv:Header/>
                    <soapenv:Body>
                       <ser:nitContribuyentes>
                          <!--Optional:-->
                          <usuario>CONSUMO_NIT</usuario>
                          <!--Optional:-->
                          <clave>58B45D8740C791420C53A49FFC924A1B58B45D8740C791420C53A49FFC924A1B</clave>
                          <nit>' . $params['nit'] . '</nit>
                       </ser:nitContribuyentes>
                    </soapenv:Body>';
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);

		//valido si no es XML (por ejemplo error 502)
		if (strpos(trim($output), '<') !== 0) {
			error_log("consultaNit() - Respuesta no valida del servidor: " . var_export($output, true));
			return [['message' => 'error', 'msj' => 'Respuesta no valida de la SAT', 'direccion' => 'CIUDAD']];
		}
		//echo $output;
		//echo'\n';
		//CONTINUA PARSEANDO XML SI ES VALIDO
		try {
			$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $output);
			$xml = new SimpleXMLElement($response);
			$body = $xml->xpath('//SBody')[0];
			$array = json_decode(json_encode((array) $body), TRUE);

			if ($array['ns2nitContribuyentesResponse']['return']['nombre'] === 'Nit no valido') {
				$responseF[] = array('message' => 'error', 'msj' => $array['ns2nitContribuyentesResponse']['return']['nombre']);
			} else {
				$responseF[] = array('message' => 'success', 'nombre' => $array['ns2nitContribuyentesResponse']['return']['nombre'], 'direccion' => $array['ns2nitContribuyentesResponse']['return']['direccion_completa']);
			}
		} catch (Exception $e) {
			error_log('consultaNit() - Error al procesar la respuesta: ' . $e->getMessage());
			$responseF[] = array('message' => 'error', 'msj' => 'Error al procesar la respuesta de la SAT');
		}
		return $responseF;
	}

	/**
	 * getVenta
	 */
	public function getVenta($params)
	{
		$this->resultado = null;
		$sql = "select *,(select sum(cantidad) from ventasDetalle where idVentas=" . $params['idVentas'] . ") as galones
        			from ventas where id=" . $params['idVentas'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/**
	 *
	 */
	public function generarNCFEL($params)
	{
		$responseF = "";
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $params['idEmpresas'] . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		$responseF = "";
		$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkdULjAwMDEwODIwMDMxMC4xMDgyMDAzMTAiLCJuYmYiOjE2MzEyOTY1MDgsImV4cCI6MTY2MjQwMDUwOCwiaWF0IjoxNjMxMjk2NTA4LCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQ5MjIwIiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdDo0OTIyMCJ9.7e67_Vt28gf5C36vQ9zE-q3ekq7ZPzZWEJOm3GQQP9I";
		$URL = "https://felgtaws.digifact.com.gt/gt.com.fel.api.v2/api/FelRequest?NIT=" . $regE['nitWS'] . "&TIPO=CERTIFICATE_DTE_XML_TOSIGN&FORMAT=XML";
		date_default_timezone_set("America/Guatemala");
		//XML
		//GENERACION DE XML
		$FechaHoraEmision = $params['fechaNC'] . "T" . $this->time;
		//DATOS DEL VENDEDOR
		$nitEmisor = $regE['nit'];
		$nombreEmisor = str_replace('"', '&quot;', str_replace('&', '&amp;', $regE['razonSocial']));
		$codEstablecimiento = $regE['codigoEstablecimiento'];
		$nombreComercial = $regE['nombreComercial'];
		$afiliacionIVA = $regE['tipoAfiliacion'];
		$direccionEmisor = $regE['direccion'];
		$codigoPostal = "0100";
		$municipio = $regE['municipio'];
		$depto = $regE['departamento'];
		$pais = "GT";
		//DATOS DEL COMPRADOR
		$nombreComprador = str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombreComprador']));
		$nitComprador = $params['nitComprador'];
		$direccionComprador = $params['direccionComprador'];
		$muniComprador = "GUATEMALA";
		$deptoComprador = "GUATEMALA";
		$codigoPostalComprador = "01001";
		$paisComprador = "GT";
		//DATOS DEL DOCUMENTO A APLICAR NOTA DE CREDITO
		$NumeroAutorizacionDocumentoOrigen = $params['autorizacionFEL'];
		$FechaEmisionDocumentoOrigen = $params['fechaFactura'];
		$MotivoAjuste = $params['motivoNC'];
		$NumeroDocumentoOrigen = $params['correlativo'];
		$SerieDocumentoOrigen = $params['serie'];
		//
		$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
                        <dte:GTDocumento xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" Version="0.1">
                            <dte:SAT ClaseDocumento="dte">
                                <dte:DTE ID="DatosCertificados">
                                    <dte:DatosEmision ID="DatosEmision">
                                        <dte:DatosGenerales Tipo="NCRE" FechaHoraEmision="' . $FechaHoraEmision . '"
                                            CodigoMoneda="GTQ" NumeroAcceso="100000000"/>
                                        <dte:Emisor NITEmisor="' . $nitEmisor . '" NombreEmisor="' . $nombreEmisor . '" CodigoEstablecimiento="' . $codEstablecimiento . '"
                                            NombreComercial="' . $nombreComercial . '" AfiliacionIVA="' . $afiliacionIVA . '">
                                            <dte:DireccionEmisor>
                                                <dte:Direccion>' . $direccionEmisor . '</dte:Direccion>
                                                <dte:CodigoPostal>' . $codigoPostal . '</dte:CodigoPostal>
                                                <dte:Municipio>' . $municipio . '</dte:Municipio>
                                                <dte:Departamento>' . $depto . '</dte:Departamento>
                                                <dte:Pais>' . $pais . '</dte:Pais>
                                            </dte:DireccionEmisor>
                                        </dte:Emisor>
                                        <dte:Receptor NombreReceptor="' . $nombreComprador . '" IDReceptor="' . $nitComprador . '">
                                            <dte:DireccionReceptor>
                                                <dte:Direccion>' . $direccionComprador . '</dte:Direccion>
                                                <dte:CodigoPostal>' . $codigoPostalComprador . '</dte:CodigoPostal>
                                                <dte:Municipio>' . $muniComprador . '</dte:Municipio>
                                                <dte:Departamento>' . $deptoComprador . '</dte:Departamento>
                                                <dte:Pais>' . $paisComprador . '</dte:Pais>
                                            </dte:DireccionReceptor>
                                        </dte:Receptor>
                                        <dte:Items>';
		$totalIDP = 0;
		$totalIVA = 0;
		$totalVenta = 0;
		if ($params['tipoNC'] === '2' && $params['dbProject'] === 'erp_inversionesyproyectos' || $params['dbProject'] === 'erp_suplegt' || $params['dbProject'] === 'erp_corporacionsancarlos') {
			if ($params['iva'] > $params['ivaDoc']) {
				foreach ($this->getProductosVenta($params) as $key => $value) {
					//CALCULO IDP
					$precioUnitario = 0;
					$precio = 0;
					$subtotal = 0;
					$iva = 0;
					$idp = 0;
					$total = 0;
					$combustible = "";
					$codCombustible = 0;
					switch ($value['codigo']) {
						case '9':
							$codCombustible = 1;
							$combustible = "SUPER";
							if ($params['descuentoP'] === '0') {
								$precioUnitario = round(($value['precio'] - 4.70), 5);
							} else {
								$precioUnitario = round(((($value['totalDescuento']) / $value['cantidad']) - 4.70), 5);
							}
							$precio = ($value['cantidad'] * $precioUnitario);
							$subtotal = round(($precio / 1.12), 2);
							$iva = round(($subtotal * 0.12), 6);
							$idp = round(($value['cantidad'] * 4.70), 4);
							$total = ($precio + $idp);
							break;
						case '8':
							$codCombustible = 2;
							$combustible = "REGULAR";
							if ($params['descuentoP'] === '0') {
								$precioUnitario = round(($value['precio'] - 4.60), 5);
							} else {
								$precioUnitario = round(((($value['totalDescuento']) / $value['cantidad']) - 4.60), 5);
							}
							$precio = ($value['cantidad'] * $precioUnitario);
							$subtotal = round(($precio / 1.12), 2);
							$iva = round(($subtotal * 0.12), 6);
							$idp = round(($value['cantidad'] * 4.60), 4);
							$total = ($precio + $idp);
							break;
						case '7':
							$codCombustible = 4;
							$combustible = "DIESEL";
							if ($params['descuentoP'] === '0') {
								$precioUnitario = round(($value['precio'] - 1.30), 5);
							} else {
								$precioUnitario = round(((($value['totalDescuento']) / $value['cantidad']) - 1.30), 5);
							}
							$precio = ($value['cantidad'] * $precioUnitario);
							$subtotal = round(($precio / 1.12), 2);
							$iva = round(($subtotal * 0.12), 6);
							$idp = round(($value['cantidad'] * 1.30), 4);
							$total = ($precio + $idp);
							break;
					}
					$totalIDP += $idp;
					$totalIVA += $iva;
					$totalVenta += $total;
					//
					$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="' . ($key + 1) . '">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>GLS</dte:UnidadMedida>
                            <dte:Descripcion>' . $combustible . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $precioUnitario . '</dte:PrecioUnitario>
                            <dte:Precio>' . $precio . '</dte:Precio>
                            <dte:Descuento>0.00</dte:Descuento>
                            <dte:Impuestos>
                              <dte:Impuesto>
                                <dte:NombreCorto>IVA</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                <dte:MontoGravable>' . $subtotal . '</dte:MontoGravable>
                                <dte:MontoImpuesto>' . $iva . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                              <dte:Impuesto>
                                <dte:NombreCorto>PETROLEO</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>' . $codCombustible . '</dte:CodigoUnidadGravable>
                                <dte:CantidadUnidadesGravables>' . $value['cantidad'] . '</dte:CantidadUnidadesGravables>
                                <dte:MontoImpuesto>' . round($idp, 4) . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . $total . '</dte:Total>
                          </dte:Item>';
				}
			} else {
				$xml_data .= '<dte:Item NumeroLinea="1" BienOServicio="B">
                            <dte:Cantidad>1.0000</dte:Cantidad>
                            <dte:UnidadMedida>UND</dte:UnidadMedida>
                            <dte:Descripcion>' . $MotivoAjuste . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $params['montoNC'] . '</dte:PrecioUnitario>
                            <dte:Precio>' . $params['montoNC'] . '</dte:Precio>
                            <dte:Descuento>0</dte:Descuento>
                            <dte:Impuestos>
                                <dte:Impuesto>
                                    <dte:NombreCorto>IVA</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                    <dte:MontoGravable>' . round($params['subtotal'], 2) . '</dte:MontoGravable>
                                    <dte:MontoImpuesto>' . round($params['iva'], 2) . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . $params['montoNC'] . '</dte:Total>
                        </dte:Item>';
			}
		} else {
			$xml_data .= '<dte:Item NumeroLinea="1" BienOServicio="B">
                            <dte:Cantidad>1.0000</dte:Cantidad>
                            <dte:UnidadMedida>CA</dte:UnidadMedida>
                            <dte:Descripcion>' . $MotivoAjuste . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $params['montoNC'] . '</dte:PrecioUnitario>
                            <dte:Precio>' . $params['montoNC'] . '</dte:Precio>
                            <dte:Descuento>0</dte:Descuento>
                            <dte:Impuestos>
                                <dte:Impuesto>
                                    <dte:NombreCorto>IVA</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                    <dte:MontoGravable>' . round($params['subtotal'], 2) . '</dte:MontoGravable>
                                    <dte:MontoImpuesto>' . round($params['iva'], 6) . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . $params['montoNC'] . '</dte:Total>
                        </dte:Item>';
		}
		$xml_data .= '</dte:Items>
                        <dte:Totales>
                        <dte:TotalImpuestos>';
		if ($params['dbProject'] === 'erp_inversionesyproyectos' || $params['dbProject'] === 'erp_suplegt') {
			if ($params['iva'] > $params['ivaDoc']) {
				$xml_data .= '<dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . $params['ivaDoc'] . '"/>';
				$xml_data .= '<dte:TotalImpuesto NombreCorto="PETROLEO" TotalMontoImpuesto="' . round($totalIDP, 3) . '"/>';
				$xml_data .= '</dte:TotalImpuestos><dte:GranTotal>' . $params['montoNC'] . '</dte:GranTotal></dte:Totales>';
			} else {
				$xml_data .= '<dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . round($params['iva'], 6) . '"/>';
				$xml_data .= '</dte:TotalImpuestos>
                        <dte:GranTotal>' . round($params['montoNC'], 2) . '</dte:GranTotal>
                      </dte:Totales>';
			}
		} else {
			$xml_data .= '<dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . round($params['iva'], 6) . '"/>';
			$xml_data .= '</dte:TotalImpuestos>
                        <dte:GranTotal>' . round($params['montoNC'], 2) . '</dte:GranTotal>
                      </dte:Totales>';
		}
		$xml_data .= '<dte:Complementos>
                                            <dte:Complemento URIComplemento="dteref" NombreComplemento="NCRE"
                                                xmlns:cno="http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0"
                                                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                                xsi:schemaLocation="http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0 GT_Complemento_Referencia_Nota-0.1.0.xsd">
                                                <cno:ReferenciasNota Version="0.1" NumeroAutorizacionDocumentoOrigen="' . $NumeroAutorizacionDocumentoOrigen . '"
                                                    FechaEmisionDocumentoOrigen="' . $FechaEmisionDocumentoOrigen . '" MotivoAjuste="' . $MotivoAjuste . '"
                                                    NumeroDocumentoOrigen="' . $NumeroDocumentoOrigen . '" SerieDocumentoOrigen="' . $SerieDocumentoOrigen . '"/>
                                            </dte:Complemento>
                                        </dte:Complementos>
                                    </dte:DatosEmision>
                                </dte:DTE>
                            </dte:SAT>
                        </dte:GTDocumento>';
		//END XML
		// echo $xml_data;
		// exit();
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Authorization: ' . $token . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['Codigo'] === 1) {
			//ACTUALIZACION DE SALDO DE FACTURAS
			$sql2 = "update ventas set total=(total-" . $params['montoNC'] . "), updated_at='" . $this->timestamp . "'"
				. "where id='" . $params['idVenta'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			if ($query2 == true) {
				//INSERTA EN CXC PARA GUARDAR REGISTRO EN ESTADO DE CUENTA
				$sql3 = "insert into cxc(idClientes,idVentas,idTipoDocumento,idDocumento,facLiquidadas,creditos,debitos,saldo,created_at,idUsuarios,idEmpresas,observaciones,autorizacionFEL,fechaEmisionFEL)"
					. "values('" . $params['nitComprador'] . "'," . $params['idVenta'] . ",'6','" . $response['Serie'] . '-' . $response['NUMERO'] . "','" . $params['serie'] . '-' . $params['correlativo'] . "','0.00'," . $params['montoNC'] . "," . ($params['saldoFactura'] - $params['montoNC']) . ",'" . $this->timestamp . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $params['motivoNC'] . "','" . $response['Autorizacion'] . "','" . $response['Fecha_DTE'] . "');";
				$query3 = mysql_query($sql3, dbCon::conPrincipal());
				$idNC = mysql_insert_id();
				if ($query3) {
					$responseF[] = array('message' => 'success', 'idNC' => $idNC, 'autorizacion' => $response['Autorizacion'], 'serie' => $response['Serie'], 'numero' => $response['NUMERO'], 'fechaEmision' => $response['Fecha_DTE']);
				} else {
					$responseF[] = array('message' => 'error actualizar cxc', 'query' => $sql3, 'error' => mysql_error());
				}
			} else {
				$responseF[] = array('message' => 'error actualizar ventas', 'query' => $sql2, 'error' => mysql_error());
			}
		} else {
			$responseF[] = array('message' => 'failed step FEL', 'error' => $response['ResponseDATA1'], 'xml' => $xml_data);
			//echo $xml_data;
		}
		return $responseF;
	}

	/**
	 * getVenta
	 */
	public function consultaNCFEL($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['serieNC'] != "") {
			$filtros .= " and cxc.idDocumento like '%" . $params['serieNC'] . "%' ";
		}
		if ($params['correlativoNC'] != "") {
			$filtros .= " and cxc.idDocumento like '%" . $params['correlativoNC'] . "%' ";
		}
		if ($params['cliente'] != "") {
			$filtros .= " and concat(ventas.nit,' ',ventas.nombre) like '%" . $params['cliente'] . "%' ";
		}
		$sql = "SELECT
                    cxc.id as idNC,
                    cxc.idVentas as idFactura,
                    ventas.nombre,
                    ventas.nit,
                    concat(ventas.serie,'-',ventas.correlativo) as factura,
                    ventas.fechaFactura,
                    ventas.total,
                    ventas.valorFactura as saldo,
                    idDocumento as NC,
                    cxc.debitos as montoNC,
                    cxc.created_at as fechaNC,
                    cxc.observaciones,
                    cxc.idEmpresas,
                    if(cxc.debitos>0,'ACTIVA','ANULADA') as estado
                FROM
                    cxc
                    inner join ventas on(cxc.idVentas=ventas.id)
                WHERE
                    idTipoDocumento = 6 and cxc.idEmpresas=" . $params['idEmpresas'] . " and date(cxc.created_at) between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "' " . $filtros . ";";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO emitirFactura
	 *
	 */
	public function emitirFactura($params, $idUsuarios, $idEmpresas)
	{
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $idEmpresas . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//EMITE FEL
		$responseF = "";
		//SCRIPT EMISION DE FACTURA
		$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkdULjAwMDEwODIwMDMxMC4xMDgyMDAzMTAiLCJuYmYiOjE2MzEyOTY1MDgsImV4cCI6MTY2MjQwMDUwOCwiaWF0IjoxNjMxMjk2NTA4LCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQ5MjIwIiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdDo0OTIyMCJ9.7e67_Vt28gf5C36vQ9zE-q3ekq7ZPzZWEJOm3GQQP9I";
		//URL PRODUCTIVO
		$URL = "https://felgtaws.digifact.com.gt/gt.com.fel.api.v2/api/FelRequest?NIT=" . $regE['nitWS'] . "&TIPO=CERTIFICATE_DTE_XML_TOSIGN&FORMAT=XML";
		date_default_timezone_set("America/Guatemala");
		$tipoDocumento = "FACT";
		if ($regE['tipoAfiliacion'] === 'PEQ') {
			$tipoDocumento = "FPEQ";
		}
		$fechaEmision = $this->date;
		$horaEmision = $this->time;
		$codigoMoneda = "GTQ";
		//DATOS DEL VENDEDOR
		$nitEmisor = $regE['nit'];
		$nombreEmisor = $regE['razonSocial'];
		$codEstablecimiento = $regE['codigoEstablecimiento'];
		$nombreComercial = $regE['nombreComercial'];
		$afiliacionIVA = $regE['tipoAfiliacion'];
		$direccionEmisor = $regE['direccion'];
		$codigoPostal = "0100";
		$municipio = "GUATEMALA";
		$depto = "GUATEMALA";
		$pais = "GT";
		//DATOS DEL COMPRADOR
		$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
		$nitComprador = trim(str_replace('-', '', $params['nit']));
		$direccionComprador = $params['direccion'];
		$munComprador = "GUATEMALA";
		$deptoComprador = "GUATEMALA";
		$codigoPostalComprador = "01001";
		$paisComprador = "GT";
		//GENERACION DE XML
		$xml_data = '<?xml version="1.0" encoding="UTF-8"?><dte:GTDocumento xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" Version="0.1">
                    <dte:SAT ClaseDocumento="dte">
                        <dte:DTE ID="DatosCertificados">
                            <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales Tipo="' . $tipoDocumento . '" FechaHoraEmision="' . $params['fechaFactura'] . 'T' . $horaEmision . '" CodigoMoneda="' . $codigoMoneda . '" />
                                <dte:Emisor NITEmisor="' . $nitEmisor . '" NombreEmisor="' . $nombreEmisor . '" CodigoEstablecimiento="' . $codEstablecimiento . '" NombreComercial="' . $nombreComercial . '" AfiliacionIVA="' . $afiliacionIVA . '">
                                    <dte:DireccionEmisor>
                                        <dte:Direccion>' . $direccionEmisor . '</dte:Direccion>
                                        <dte:CodigoPostal>' . $codigoPostal . '</dte:CodigoPostal>
                                        <dte:Municipio>' . $municipio . '</dte:Municipio>
                                        <dte:Departamento>' . $depto . '</dte:Departamento>
                                        <dte:Pais>' . $pais . '</dte:Pais>
                                    </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor NombreReceptor="' . $nombreComprador . '" IDReceptor="' . $nitComprador . '">
                                    <dte:DireccionReceptor>
                                        <dte:Direccion>' . $direccionComprador . '</dte:Direccion>
                                        <dte:CodigoPostal>' . $codigoPostalComprador . '</dte:CodigoPostal>
                                        <dte:Municipio>' . $munComprador . '</dte:Municipio>
                                        <dte:Departamento>' . $deptoComprador . '</dte:Departamento>
                                        <dte:Pais>' . $paisComprador . '</dte:Pais>
                                    </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                    <dte:Frase TipoFrase="' . $regE['tipoFrase'] . '" CodigoEscenario="' . $regE['codigoEscenario'] . '" />';
		if ($regE['agenteRetenedor'] === '1') {
			$xml_data .= '<dte:Frase TipoFrase="2" CodigoEscenario="1"/>';
		}
		$xml_data .= '</dte:Frases>
                                <dte:Items>';
		$params['idUsuarios'] = $idUsuarios;
		foreach ($this->getProductosVenta($params) as $key => $value) {
			if ($params['descuentoP'] > 0) {
				//DETALLE DE LA FACTURA
				$xml_data .= '<dte:Item NumeroLinea="1" BienOServicio="B">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>UND</dte:UnidadMedida>
                            <dte:Descripcion>' . $value['descLarga'] . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $value['precio'] . '</dte:PrecioUnitario>
                            <dte:Precio>' . $value['total'] . '</dte:Precio>
                            <dte:Descuento>' . $value['descuento'] . '</dte:Descuento>';
				//GENERA IMPUESTOS SI LA AFILIACION NO ES PEQUENO CONTRIBUYENTE
				if ($afiliacionIVA !== 'PEQ') {
					$xml_data .= '<dte:Impuestos>
                                    <dte:Impuesto>
                                        <dte:NombreCorto>IVA</dte:NombreCorto>
                                        <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                        <dte:MontoGravable>' . round($value['subtotalDescuento'], 2) . '</dte:MontoGravable>
                                        <dte:MontoImpuesto>' . $value['ivaDescuento'] . '</dte:MontoImpuesto>
                                    </dte:Impuesto>
                                </dte:Impuestos>';
				}
				$xml_data .= '<dte:Total>' . $value['totalDescuento'] . '</dte:Total></dte:Item>';
			} else {
				//DETALLE DE LA FACTURA
				$xml_data .= '<dte:Item NumeroLinea="1" BienOServicio="B">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>UND</dte:UnidadMedida>
                            <dte:Descripcion>' . $value['descLarga'] . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $value['precio'] . '</dte:PrecioUnitario>
                            <dte:Precio>' . $value['total'] . '</dte:Precio>
                            <dte:Descuento>' . $params['descuentoM'] . '</dte:Descuento>';
				//GENERA IMPUESTOS SI LA AFILIACION NO ES PEQUENO CONTRIBUYENTE
				if ($afiliacionIVA !== 'PEQ') {
					$xml_data .= '<dte:Impuestos>
                                    <dte:Impuesto>
                                        <dte:NombreCorto>IVA</dte:NombreCorto>
                                        <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                        <dte:MontoGravable>' . round($value['subtotal'], 2) . '</dte:MontoGravable>
                                        <dte:MontoImpuesto>' . $value['iva'] . '</dte:MontoImpuesto>
                                    </dte:Impuesto>
                                </dte:Impuestos>';
				}
				$xml_data .= '<dte:Total>' . $value['total'] . '</dte:Total></dte:Item>';
			}
		}
		$xml_data .= '</dte:Items>';
		//TERMINA DETALLE DE LA FACTURA
		$xml_data .= '<dte:Totales>';
		//GENERA IMPUESTOS SI LA AFILIACION NO ES PEQUENO CONTRIBUYENTE
		if ($afiliacionIVA !== 'PEQ') {
			$xml_data .= '<dte:TotalImpuestos>
                                        <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . round($params['iva'], 2) . '" />
                                    </dte:TotalImpuestos>';
		}
		$xml_data .= '<dte:GranTotal>' . round($params['total'], 2) . '</dte:GranTotal>
                                </dte:Totales>';
		$xml_data .= '</dte:DatosEmision>
                        </dte:DTE>
                    </dte:SAT>
                </dte:GTDocumento>';
		//echo $xml_data;
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Authorization: ' . $token . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['Codigo'] === 1) {
			//$responseF[] = array('message' => 'success', 'autorizacion' => $response['Autorizacion'], 'serie' => $response['Serie'], 'numero' => $response['NUMERO'], 'fechaEmision' => $response['Fecha_DTE']);
			$saldo = 0.00;
			if ($params['tipoVenta'] === '2') {
				$saldo = $params['saldo'] ?: $params['total'];
			}
			//CREAR CLIENTE SI ID ES VACIO Y QUE NO SEA CF
			$idClientes = "0";
			if ($params['nombreF'] != 'Consumidor Final') {
				//Validar NIT
				$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
				$query = mysql_query($sql, dbCon::conPrincipal());
				$reg = mysql_fetch_assoc($query);
				if ($reg['id'] == '') {
					$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,telefonoC,idTipoClientes,idEmpresas,created_at) "
						. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "',1," . $idEmpresas . ",'" . $this->timestamp . "');";
					$queryC = mysql_query($sqlC, dbCon::conPrincipal());
					if ($queryC == TRUE) {
						$idClientes = mysql_insert_id();
						error_log('cliente creado exitosamente\n');
					} else {
						error_log('error al crear cliente ' . $sqlC);
					}
				} else {
					$idClientes = $reg['id'];
				}
			}
			// GUARDA VENTA
			$doc = explode('-', $params['correlativo']);
			$sql = "insert into ventas
                            (id,
                            serie,
                            correlativo,
                            fechaFactura,
                            valorFactura,
                            subtotal,
                            descuento,
                            descuentoP,
                            total,
                            anticipo,
                            saldo,
                            iva,
                            tipoCambio,
                            totalDolares,
                            totalEnLetras,
                            totalEnLetrasDolares,
                            nit,
                            nombre,
                            direccion,
                            idTipoOperacion,
                            idTipoVenta,
                            conceptoVenta,
                            idUsuarios,
                            idSucursales,
                            idEmpresas,
                            created_at,
                            updated_at,
                            idFormatos,
                            idPartidas,
                            statusCierre,
                            anulacion,
                            idAdminUser,
                            motivoAnulacion,
                            anulacion_at,
                            idClientes,
                            idVendedores,
                            autorizacionFEL,
                            fechaEmisionFEL,
                            tipoTransaccion)
                        values(
                            null,
                            '" . $response['Serie'] . "',
                            '" . $response['NUMERO'] . "',
                            '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                            '" . $params['total'] . "',
                            '" . $params['subtotal'] . "',
                            '" . $params['descuentoM'] . "',
                            '" . $params['descuentoP'] . "',
                            '" . $params['total'] . "',
                            '" . $params['anticipo'] . "',
                            '" . $params['total'] . "',
                            '" . $params['iva'] . "',
                            '" . $params['tasaCambio'] . "',
                            '" . $params['totalDolares'] . "',
                            '" . $params['totalEnLetras'] . "',
                            '" . $params['totalEnLetrasDolares'] . "',
                            '" . $params['nit'] . "',
                            '" . $nombreComprador . "',
                            '" . $params['direccion'] . "',
                            '1',
                            '" . $params['tipoVenta'] . "',
                            '" . $params['observaciones'] . "',
                            '" . $idUsuarios . "',
                            '" . $params['idSucursales'] . "',
                            '" . $idEmpresas . "',
                            '" . $this->timestamp . "',
                            null,
                            " . $params['idFormato'] . ",
                            0,
                            0,
                            0,
                            null,
                            null,
                            null,
                            '" . $idClientes . "',
                            '" . $params['vendedores'] . "','" . $response['Autorizacion'] . "','" . $response['Fecha_DTE'] . "',1);";
			$query = mysql_query($sql, dbCon::conPrincipal());
			//echo $query;
			if ($query) {
				//OBTIENE ID VENTA
				$idVenta = mysql_insert_id();
				$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
					. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//INGRESO DE VENTA AL CREDITO
					if ($params['tipoVenta'] === '2') {
						$this->ventasCredito($params['nit'], $idVenta, ($response['Serie'] . '-' . $response['NUMERO']), $params['total'], $params['anticipo'], $saldo, date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
						//$this->liquidacionVenta($params['detallePago'], $idVenta);
					}
					//GUARDAR PARTIDA AUTOMATICA
					if ($params['idFormato'] !== '0') {
						$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
						$params['idTipoOperacionPartida'] = 2;
						$params['conceptoCompra'] = 'REF. FEL. ' . $response['Autorizacion'];
						$params['idEmpresas'] = $idEmpresas;
						$params['idUsuarios'] = $idUsuarios;
						$params['idVenta'] = $idVenta;
						$this->savePartidaAutomatica($params);
					}
					//
					$responseF[] = array('message' => 'success', 'idVenta' => $idVenta, 'idSucursales' => $params['idSucursales'], 'autorizacion' => $response['Autorizacion']);
				} else {
					$responseF[] = array('message' => 'failed step 2', 'error' => $sql2);
				}
			} else {
				$error = mysql_error();
				$responseF[] = array('message' => 'failed step 1', 'Query' => $sql, 'Error' => $error);
			}
		} else {
			//echo $xml_data;
			$responseF[] = array('message' => 'failed step FEL', 'error' => $response['ResponseDATA1']);
		}
		return $responseF;
	}

	/** METODO
	 * emitirFactura INFILE
	 *
	 */
	public function emitirFacturaINFILE($params, $idUsuarios, $idEmpresas)
	{
		$responseF = "";
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS
                from
                    empresas
                where
                    id=" . $idEmpresas . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//SCRIPT EMISION DE FACTURA
		//URL PRODUCTIVO
		$URL = "https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml";
		$UsuarioAPI = $regE['usuarioAPI'];
		$LlaveAPI = $regE['llaveAPI'];
		$UsuarioFirma = $regE['usuarioFirma'];
		$LlaveFirma = $regE['llaveFirma'];
		date_default_timezone_set("America/Guatemala");
		//GENERACION DE XML
		$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
		$direccion = $this->consultaNIT($params);
		$direccionComprador = $params['direccion'];
		//
		$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
                        <dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.2.0">
                          <dte:SAT ClaseDocumento="dte">
                            <dte:DTE ID="DatosCertificados">
                              <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $params['fechaFactura'] . 'T' . $this->time . '-06:00" Tipo="FACT"></dte:DatosGenerales>
                                <dte:Emisor AfiliacionIVA="' . $regE['tipoAfiliacion'] . '" CodigoEstablecimiento="' . $regE['codigoEstablecimiento'] . '" CorreoEmisor="marriolaj@gmail.com" NITEmisor="' . $regE['nit'] . '" NombreComercial="' . $regE['nombreComercial'] . '" NombreEmisor="' . $regE['razonSocial'] . '">
                                  <dte:DireccionEmisor>
                                    <dte:Direccion>' . $regE['direccion'] . '</dte:Direccion>
                                    <dte:CodigoPostal>01009</dte:CodigoPostal>
                                    <dte:Municipio>' . $regE['municipio'] . '</dte:Municipio>
                                    <dte:Departamento>' . $regE['departamento'] . '</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor CorreoReceptor="" IDReceptor="' . trim(str_replace('-', '', $params['nit'])) . '" NombreReceptor="' . $nombreComprador . '">
                                  <dte:DireccionReceptor>
                                    <dte:Direccion>' . ($direccionComprador ?: 'CIUDAD') . '</dte:Direccion>
                                    <dte:CodigoPostal>0101</dte:CodigoPostal>
                                    <dte:Municipio>GUATEMALA</dte:Municipio>
                                    <dte:Departamento>GUATEMALA</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                  <dte:Frase CodigoEscenario="' . $regE['codigoEscenario'] . '" TipoFrase="' . $regE['tipoFrase'] . '"></dte:Frase>';
		if ($regE['agenteRetenedor'] === '1') {
			$xml_data .= '<dte:Frase TipoFrase="2" CodigoEscenario="1"/>';
		}
		//valida si se aplica subsidio
		$params['idUsuarios'] = $idUsuarios;
		$flag = false;
		foreach ($this->getProductosVenta($params) as $key => $value) {
			if ($value['codigo'] === '8' || $value['codigo'] === '7') {
				$flag = true;
			}
		}
		if ($flag === true && $regE['subsidio'] === '1') {
			$xml_data .= '<dte:Frase TipoFrase="9" CodigoEscenario="2"/>';
		}
		//end valida si se aplica subsidio
		$xml_data .= '</dte:Frases>
                              <dte:Items>';
		$totalIDP = 0;
		$totalIVA = 0;
		$totalVenta = 0;
		$totalSubtotal = 0;
		foreach ($this->getProductosVenta($params) as $key => $value) {
			//CALCULO IDP
			$precioUnitario = 0;
			$precio = 0;
			$subtotal = 0;
			$iva = 0;
			$idp = 0;
			$total = 0;
			$combustible = "";
			$codCombustible = 0;
			switch ($value['codigo']) {
				case '9':
					$codCombustible = 1;
					$combustible = "SUPER";
					$precioUnitario = ($value['precio'] - 4.70);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 4.70), 2);
					$total = ($precio + $idp);
					break;
				case '8':
					$codCombustible = 2;
					$combustible = "REGULAR";
					$precioUnitario = ($value['precio'] - 4.60);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 4.60), 2);
					$total = ($precio + $idp);
					break;
				case '7':
					$codCombustible = 4;
					$combustible = "DIESEL";
					$precioUnitario = ($value['precio'] - 1.30);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 1.30), 2);
					$total = ($precio + $idp);
					break;
				case '6':
					$codCombustible = 4;
					$combustible = "ION DIESEL";
					$precioUnitario = ($value['precio'] - 1.30);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 1.30), 2);
					$total = ($precio + $idp);
					break;
			}
			$totalIDP += $idp;
			$totalIVA += $iva;
			$totalVenta += $total;
			$totalSubtotal += $subtotal;
			//
			$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="1">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>GLS</dte:UnidadMedida>
                            <dte:Descripcion>' . $combustible . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $precioUnitario . '</dte:PrecioUnitario>
                            <dte:Precio>' . $precio . '</dte:Precio>
                            <dte:Descuento>0.00</dte:Descuento>
                            <dte:Impuestos>
                              <dte:Impuesto>
                                <dte:NombreCorto>IVA</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                <dte:MontoGravable>' . $subtotal . '</dte:MontoGravable>
                                <dte:MontoImpuesto>' . $iva . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                              <dte:Impuesto>
                                <dte:NombreCorto>PETROLEO</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>' . $codCombustible . '</dte:CodigoUnidadGravable>
                                <dte:CantidadUnidadesGravables>' . $value['cantidad'] . '</dte:CantidadUnidadesGravables>
                                <dte:MontoImpuesto>' . $idp . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . round($total, 2) . '</dte:Total>
                          </dte:Item>';
		}
		$xml_data .= '</dte:Items>
                                <dte:Totales>
                                  <dte:TotalImpuestos>
                                    <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . $totalIVA . '"/>
                                    <dte:TotalImpuesto NombreCorto="PETROLEO" TotalMontoImpuesto="' . $totalIDP . '"/>
                                  </dte:TotalImpuestos>
                                  <dte:GranTotal>' . round($totalVenta, 2) . '</dte:GranTotal>
                                </dte:Totales>
                               </dte:DTE>
                          </dte:SAT>
                        </dte:GTDocumento>';
		// echo $xml_data;
		// exit();
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('UsuarioAPI: ' . $UsuarioAPI . '', 'LlaveAPI: ' . $LlaveAPI . '', 'UsuarioFirma: ' . $UsuarioFirma . '', 'LlaveFirma: ' . $LlaveFirma . '', 'Identificador: ' . date('YmdHis') . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['uuid']) {
			//Validar NIT
			$sqlCV = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
			$queryCV = mysql_query($sqlCV, dbCon::conPrincipal());
			$regCV = mysql_fetch_assoc($queryCV);
			if ($regCV['id'] == '') {
				$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,idTipoClientes,idEmpresas,created_at) "
					. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','CIUDAD',1," . $idEmpresas . ",'" . $this->timestamp . "');";
				$queryC = mysql_query($sqlC, dbCon::conPrincipal());
				if ($queryC == TRUE) {
					$idClientes = mysql_insert_id();
					error_log('cliente creado exitosamente\n');
				} else {
					error_log('error al crear cliente ' . $sqlC);
				}
			} else {
				$sqlU = "update clientes set nombreC='" . $params['nombre'] . "',nombreF='" . $params['nombre'] . "', direccionC='CIUDAD'"
					. " where id=" . $reg['id'] . ";";
				$queryU = mysql_query($sqlU, dbCon::conPrincipal());
				$idClientes = $reg['id'];
			}
			//ventas
			$sql = "insert into ventas
                    (`id`,
                    `serie`,
                    `correlativo`,
                    `fechaFactura`,
                    `valorFactura`,
                    `subtotal`,
                    `descuento`,
                    `descuentoP`,
                    `total`,
                    `anticipo`,
                    `saldo`,
                    `iva`,
                    `tipoCambio`,
                    `totalDolares`,
                    `totalEnLetras`,
                    `totalEnLetrasDolares`,
                    `nit`,
                    `nombre`,
                    `direccion`,
                    `idTipoOperacion`,
                    `idTipoVenta`,
                    `conceptoVenta`,
                    `idUsuarios`,
                    `idSucursales`,
                    `idEmpresas`,
                    `created_at`,
                    `updated_at`,
                    `idFormatos`,
                    `idPartidas`,
                    `statusCierre`,
                    `anulacion`,
                    `idAdminUser`,
                    `motivoAnulacion`,
                    `anulacion_at`,
                    `idClientes`,
                    `idVendedores`,
                    `autorizacionFEL`,
                    `fechaEmisionFEL`,
                    `tipoTransaccion`,
                    `idBombas`,
                    `xml`,
                    `numeroAcceso`)
                    values(
                        null,
                        '" . $response['serie'] . "',
                        '" . $response['numero'] . "',
                        '" . $params['fechaFactura'] . "',
                        '" . $params['total'] . "',
                        '" . $totalSubtotal . "',
                        '" . $totalIDP . "',
                        '" . $params['descuentoP'] . "',
                        '" . $params['total'] . "',
                        '" . $params['anticipo'] . "',
                        '" . $params['total'] . "',
                        '" . $totalIVA . "',
                        '0.00',
                        '0.00',
                        '" . $params['totalEnLetras'] . "',
                        '-',
                        '" . $params['nit'] . "',
                        '" . $nombreComprador . "',
                        '" . ($direccionComprador ?: 'CIUDAD') . "',
                        '" . $params['tipoOperacion'] . "',
                        '" . $params['tipoVenta'] . "',
                        '" . $params['observaciones'] . "',
                        '" . $idUsuarios . "',
                        '" . $params['idSucursales'] . "',
                        '" . $idEmpresas . "',
                        '" . $this->timestamp . "',
                        null,
                        0,
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        '" . $idUsuarios . "','" . $response['uuid'] . "','" . $response['fecha'] . "',1,0,'" . $xml_data . "',null);";
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query) {
				//ventas detalle
				$idVenta = mysql_insert_id();
				$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
					. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//INGRESO DE VENTA AL CREDITO
					if ($params['tipoVenta'] === '2') {
						$this->ventasCredito($params['nit'], $idVenta, ($response['serie'] . '-' . $response['numero']), $params['total'], $params['anticipo'], $params['total'], date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
					}
					//GUARDAR PARTIDA AUTOMATICA
					if ($params['idFormato'] !== '0') {
						$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
						$params['idTipoOperacionPartida'] = 2;
						$params['conceptoCompra'] = 'REF. FEL. ' . $response['serie'] . '-' . $response['numero'] . '-';
						$params['idEmpresas'] = $idEmpresas;
						$params['idUsuarios'] = $idUsuarios;
						$params['idVenta'] = $idVenta;
						$this->savePartidaAutomatica($params);
					}
					//RESPONSE
					$responseF[] = array('message' => 'success', 'idVenta' => $idVenta);
				} else {
					$responseF[] = array('message' => 'failed step 2', 'query' => $sql2);
					return $responseF;
				}
			} else {
				$responseF[] = array('message' => 'failed step 1', 'query' => $sql);
				return $responseF;
			}
		} else {
			$responseF[] = array('message' => $response['descripcion_errores'][0]['mensaje_error']);
			echo $xml_data;
		}
		return $responseF;
	}

	// POS SAN ANTONIO

	public function emitirFacturaINFILE_SANANTONIO($params, $idUsuarios, $idEmpresas)
	{
		$responseF = "";
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS
                from
                    empresas
                where
                    id=" . $idEmpresas . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//SCRIPT EMISION DE FACTURA
		//URL PRODUCTIVO
		$URL = "https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml";
		$UsuarioAPI = 'TRANSTASOL';
		$LlaveAPI = '210246CE0A39CBB45B45A02F73605552';
		$UsuarioFirma = 'TRANSTASOL';
		$LlaveFirma = 'aa1af57d3472afb2127959576f855836';
		date_default_timezone_set("America/Guatemala");
		//GENERACION DE XML
		$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
		$direccion = $this->consultaNIT($params);
		$direccionComprador = $direccion[0]['direccion'];
		//
		$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
                        <dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.2.0">
                          <dte:SAT ClaseDocumento="dte">
                            <dte:DTE ID="DatosCertificados">
                              <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $params['fechaFactura'] . 'T' . $this->time . '-06:00" Tipo="FACT"></dte:DatosGenerales>
                                <dte:Emisor AfiliacionIVA="' . $regE['tipoAfiliacion'] . '" CodigoEstablecimiento="' . $regE['codigoEstablecimiento'] . '" CorreoEmisor="marriolaj@gmail.com" NITEmisor="' . $regE['nit'] . '" NombreComercial="' . $regE['nombreComercial'] . '" NombreEmisor="' . $regE['razonSocial'] . '">
                                  <dte:DireccionEmisor>
                                    <dte:Direccion>' . $regE['direccion'] . '</dte:Direccion>
                                    <dte:CodigoPostal>01009</dte:CodigoPostal>
                                    <dte:Municipio>' . $regE['municipio'] . '</dte:Municipio>
                                    <dte:Departamento>' . $regE['departamento'] . '</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor CorreoReceptor="" IDReceptor="' . str_replace('-', '', $params['nit']) . '" NombreReceptor="' . $nombreComprador . '">
                                  <dte:DireccionReceptor>
                                    <dte:Direccion>' . ($direccionComprador ?: 'CIUDAD') . '</dte:Direccion>
                                    <dte:CodigoPostal>0101</dte:CodigoPostal>
                                    <dte:Municipio>GUATEMALA</dte:Municipio>
                                    <dte:Departamento>GUATEMALA</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                  <dte:Frase CodigoEscenario="' . $regE['codigoEscenario'] . '" TipoFrase="' . $regE['tipoFrase'] . '"></dte:Frase>
                                </dte:Frases>
                                <dte:Items>';
		$params['idUsuarios'] = $idUsuarios;
		$totalIDP = 0;
		$totalIVA = 0;
		$totalVenta = 0;
		foreach ($this->getProductosVenta($params) as $key => $value) {
			//CALCULO IDP
			$precioUnitario = 0;
			$precio = 0;
			$subtotal = 0;
			$iva = 0;
			$idp = 0;
			$total = 0;
			$combustible = "";
			$codCombustible = 0;
			switch ($value['codigo']) {
				case '9':
					$codCombustible = 1;
					$combustible = "SUPER";
					$precioUnitario = ($value['precio'] - 4.70);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 4.70), 2);
					$total = ($precio + $idp);
					break;
				case '8':
					$codCombustible = 2;
					$combustible = "REGULAR";
					$precioUnitario = ($value['precio'] - 4.60);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 4.60), 2);
					$total = ($precio + $idp);
					break;
				case '7':
					$codCombustible = 4;
					$combustible = "DIESEL";
					$precioUnitario = ($value['precio'] - 1.30);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 1.30), 2);
					$total = ($precio + $idp);
					break;
			}
			$totalIDP += $idp;
			$totalIVA += $iva;
			$totalVenta += $total;
			//
			$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="1">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>GLS</dte:UnidadMedida>
                            <dte:Descripcion>' . ($combustible ?: $value['descLarga']) . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . ($precioUnitario ?: $value['precio']) . '</dte:PrecioUnitario>
                            <dte:Precio>' . ($precio ?: $value['total']) . '</dte:Precio>
                            <dte:Descuento>0.00</dte:Descuento>
                            <dte:Impuestos>
                              <dte:Impuesto>
                                <dte:NombreCorto>IVA</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                <dte:MontoGravable>' . ($subtotal ?: $params['subtotal']) . '</dte:MontoGravable>
                                <dte:MontoImpuesto>' . ($iva ?: $params['iva']) . '</dte:MontoImpuesto>
                              </dte:Impuesto>';
			if ($idEmpresas === '1') {
				$xml_data .= '<dte:Impuesto>
                                <dte:NombreCorto>PETROLEO</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>' . $codCombustible . '</dte:CodigoUnidadGravable>
                                <dte:CantidadUnidadesGravables>' . $value['cantidad'] . '</dte:CantidadUnidadesGravables>
                                <dte:MontoImpuesto>' . $idp . '</dte:MontoImpuesto>
                              </dte:Impuesto>';
			}
			$xml_data .= '</dte:Impuestos>
                            <dte:Total>' . round(($total ?: $params['total']), 2) . '</dte:Total>
                          </dte:Item>';
		}
		$xml_data .= '</dte:Items>
                                <dte:Totales>
                                  <dte:TotalImpuestos>
                                    <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . ($totalIVA ?: $params['iva']) . '"/>';
		if ($idEmpresas === '1') {
			$xml_data .= '<dte:TotalImpuesto NombreCorto="PETROLEO" TotalMontoImpuesto="' . $totalIDP . '"/>';
		}

		$xml_data .= '</dte:TotalImpuestos>
                                  <dte:GranTotal>' . round(($totalVenta ?: $params['total']), 2) . '</dte:GranTotal>
                                </dte:Totales>
                               </dte:DTE>
                          </dte:SAT>
                        </dte:GTDocumento>';
		// echo $xml_data;
		// exit();
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('UsuarioAPI: ' . $UsuarioAPI . '', 'LlaveAPI: ' . $LlaveAPI . '', 'UsuarioFirma: ' . $UsuarioFirma . '', 'LlaveFirma: ' . $LlaveFirma . '', 'Identificador: ' . date('YmdHis') . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['uuid']) {
			//Validar NIT
			$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
			$query = mysql_query($sql, dbCon::conPrincipal());
			$reg = mysql_fetch_assoc($query);
			if ($reg['id'] == '') {
				$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,idTipoClientes,idEmpresas,created_at) "
					. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . ($direccionComprador ?: 'CIUDAD') . "',1," . $params['idEmpresas'] . ",'" . $this->timestamp . "');";
				$queryC = mysql_query($sqlC, dbCon::conPrincipal());
				if ($queryC == TRUE) {
					$idClientes = mysql_insert_id();
					error_log('cliente creado exitosamente\n');
				} else {
					error_log('error al crear cliente ' . $sqlC);
				}
			} else {
				$sqlU = "update clientes set nombreC='" . $params['nombre'] . "',nombreF='" . $params['nombre'] . "', direccionC='" . ($direccionComprador ?: 'CIUDAD') . "'"
					. " where id=" . $reg['id'] . ";";
				$queryU = mysql_query($sqlU, dbCon::conPrincipal());
				$idClientes = $reg['id'];
			}
			//ventas
			$sql = "insert into ventas
                    values(
                        null,
                        '" . $response['serie'] . "',
                        '" . $response['numero'] . "',
                        '" . $this->date . "',
                        '" . $params['valorFactura'] . "',
                        '" . $params['subtotal'] . "',
                        '" . $params['descuento'] . "',
                        '" . $params['descuentoP'] . "',
                        '" . $params['total'] . "',
                        '" . $params['anticipo'] . "',
                        '" . $params['total'] . "',
                        '" . $params['iva'] . "',
                        '0.00',
                        '0.00',
                        '" . $params['totalEnLetras'] . "',
                        '-',
                        '" . $params['nit'] . "',
                        '" . $nombreComprador . "',
                        '" . ($direccionComprador ?: 'CIUDAD') . "',
                        '" . $params['idTipoOperacion'] . "',
                        '" . $params['idTipoVenta'] . "',
                        '" . $params['observaciones'] . "',
                        '" . $idUsuarios . "',
                        '" . $params['idSucursales'] . "',
                        '" . $idEmpresas . "',
                        '" . $this->timestamp . "',
                        null,
                        0,
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        '" . $idUsuarios . "','" . $response['uuid'] . "','" . $response['fecha'] . "',1,0,'" . $xml_data . "',null);";
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query) {
				//ventas detalle
				$idVenta = mysql_insert_id();
				$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
					. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//INGRESO DE VENTA AL CREDITO
					if ($params['tipoVenta'] === '2') {
						$this->ventasCredito($params['nit'], $idVenta, ($response['Serie'] . '-' . $response['NUMERO']), $params['total'], $params['anticipo'], $params['total'], date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
					}
					//GUARDAR PARTIDA AUTOMATICA
					if ($params['idFormato'] !== '0') {
						$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
						$params['idTipoOperacionPartida'] = 2;
						$params['conceptoCompra'] = 'REF. FEL. ' . $response['Autorizacion'];
						$params['idEmpresas'] = $idEmpresas;
						$params['idUsuarios'] = $idUsuarios;
						$params['idVenta'] = $idVenta;
						$this->savePartidaAutomatica($params);
					}
					//RESPONSE
					$responseF[] = array('message' => 'success', 'idVenta' => $idVenta);
				} else {
					$responseF[] = array('message' => 'failed step 2', 'query' => $sql2);
					return $responseF;
				}
			} else {
				$responseF[] = array('message' => 'failed step 1', 'query' => $sql);
				return $responseF;
			}
		} else {
			$responseF[] = array('message' => $response['descripcion_errores'][0]['mensaje_error']);
			echo $xml_data;
		}
		return $responseF;
	}

	/** generarNCCompras
	 *
	 */
	public function generarNCCompras($params)
	{
		$responseF = "";
		//ACTUALIZACION DE SALDO DE FACTURAS
		$sql2 = "update compras set total=(total-" . $params['montoNC'] . "), updated_at='" . $this->timestamp . "'"
			. "where id='" . $params['idCompras'] . "';";
		$query2 = mysql_query($sql2, dbCon::conPrincipal());
		if ($query2 == true) {
			//INSERTA EN CXC PARA GUARDAR REGISTRO EN ESTADO DE CUENTA
			$sql3 = "insert into cxp(idProveedores,idCompras,idTipoDocumento,idDocumento,facLiquidadas,creditos,debitos,saldo,created_at,idUsuarios,idEmpresas,observaciones)"
				. "values(" . $params['idProveedores'] . "," . $params['idCompras'] . ",'6','" . $params['serieNC'] . '-' . $params['correlativoNC'] . "','" . $params['serie'] . '-' . $params['correlativo'] . "','0.00'," . $params['montoNC'] . "," . ($params['saldoFactura'] - $params['montoNC']) . ",'" . $this->timestamp . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $params['motivoNC'] . "');";
			$query3 = mysql_query($sql3, dbCon::conPrincipal());
			$idNC = mysql_insert_id();
			if ($query3) {
				$responseF[] = array('message' => 'success', 'idNC' => $idNC);
			} else {
				$responseF[] = array('message' => 'error actualizar cxc', 'query' => $sql3, 'error' => mysql_error());
			}
		} else {
			$responseF[] = array('message' => 'error actualizar ventas', 'query' => $sql2, 'error' => mysql_error());
		}
		//
		return $responseF;
	}

	public function envioMailFEL($mail, $cliente, $autorizacionFEL, $vendedor, $nitVendedor, $idVenta)
	{
		$email_message = new email_message_class;
		$from_address = "fel@digicom.com.gt";
		$from_name = "fel@digicom.com.gt - ENVIO FACTURA ELECTRONICA";
		$reply_name = $from_name;
		$reply_address = $from_address;
		$reply_address = $from_address;
		$error_delivery_name = $from_name;
		$error_delivery_address = $from_address;
		$to_name = "fel@digicom.com.gt";
		$subject = "fel@digicom.com.gt - ENVIO FACTURA ELECTRONICA - " . $autorizacionFEL . "";
		$email_message->SetMultipleEncodedEmailHeader('To', array($mail => $cliente));
		//
		$email_message->SetEncodedEmailHeader("From", $from_address, $from_name);
		$email_message->SetEncodedEmailHeader("Reply-To", $reply_address, $reply_name);
		$email_message->SetHeader("Sender", $from_address);
		//
		if (defined("PHP_OS") && strcmp(substr(PHP_OS, 0, 3), "WIN")) {
			$email_message->SetHeader("Return-Path", $error_delivery_address);
		}

		$email_message->SetEncodedHeader("Subject", $subject);
		$url_factura = "http://erp.digicom.com.gt/fel.php?idVenta=" . $idVenta . "&nit=" . $nitVendedor . "";
		//
		$text_message = "Estimado (a) " . strtoupper($cliente);
		$text_message .= "<br>";
		$text_message .= "<p>S&iacute;rvase encontrar el link de descarga su documento tributario electr&oacute;nico n&uacute;mero " . $autorizacionFEL . " certificada por la Superintendencia de Administraci&oacute;n Tributaria (SAT), Dicho documento fue generado desde el vendedor: <b>" . $vendedor . "</b> - NIT: <b>" . $nitVendedor . "</b></p>";
		$text_message .= "<p>Este env&iacute;o adem&aacute;s de facilitarle su comprobante fiscal de un modo r&aacute;pido, c&oacute;modo y seguro, constituye en promover la adecuada utilizaci&oacute;n de los recursos naturales para un futuro de nuestro planeta.</p>";
		$text_message .= "<p>Lo invitamos a unirse a nuestros esfuerzos no imprimiendo este correo, si no es estrictamente necesario.</p>";
		$text_message .= "<p>LINK DE DESCARGA <a href='" . trim($url_factura) . "'>" . trim($url_factura) . "</a></p>";
		$text_message .= "<i>La informacion contenida en este Mensaje de correo electronico es confidencial, tan solo para el uso de la persona o entidad mencionada anteriormente. Si el lector de este e-mail no es el destinatario, o la persona, empleado o agente responsable de entregarlo al destinatario, se le notifica que cualquier revision, difusion, distribucion o copia de esta comunicacion esta estrictamente prohibida. Si usted recibio este e-mail por error, por favor borrelo.</i>";
		$email_message->AddHTMLPart($email_message->WrapText($text_message));
		//
		$error = $email_message->Send();
		if (strcmp($error, "")) {
			error_log("Error: $error");
		} else {
			error_log("Message sent");
		}
	}

	/** METODO emitirFacturaCombustibles
	 *
	 */
	public function emitirFacturaCombustibles($params, $idUsuarios, $idEmpresas)
	{
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $idEmpresas . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//EMITE FEL
		$responseF = "";
		//SCRIPT EMISION DE FACTURA
		$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkdULjAwMDEwODIwMDMxMC4xMDgyMDAzMTAiLCJuYmYiOjE2MzEyOTY1MDgsImV4cCI6MTY2MjQwMDUwOCwiaWF0IjoxNjMxMjk2NTA4LCJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQ5MjIwIiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdDo0OTIyMCJ9.7e67_Vt28gf5C36vQ9zE-q3ekq7ZPzZWEJOm3GQQP9I";
		//URL PRODUCTIVO
		$URL = "https://felgtaws.digifact.com.gt/gt.com.fel.api.v2/api/FelRequest?NIT=" . $regE['nitWS'] . "&TIPO=CERTIFICATE_DTE_XML_TOSIGN&FORMAT=XML";
		date_default_timezone_set("America/Guatemala");
		$tipoDocumento = "FACT";
		$horaEmision = $this->time;
		$codigoMoneda = "GTQ";
		//DATOS DEL VENDEDOR
		$nitEmisor = $regE['nit'];
		$nombreEmisor = $regE['razonSocial'];
		$codEstablecimiento = $regE['codigoEstablecimiento'];
		$nombreComercial = $regE['nombreComercial'];
		$afiliacionIVA = $regE['tipoAfiliacion'];
		$direccionEmisor = $regE['direccion'];
		$codigoPostal = "0100";
		$municipio = "GUATEMALA";
		$depto = "GUATEMALA";
		$pais = "GT";
		//DATOS DEL COMPRADOR
		$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
		$nitComprador = trim(str_replace('-', '', $params['nit']));
		$direccionComprador = $params['direccion'];
		$munComprador = "GUATEMALA";
		$deptoComprador = "GUATEMALA";
		$codigoPostalComprador = "01001";
		$paisComprador = "GT";
		//GENERACION DE XML
		$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
                    <dte:GTDocumento xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" Version="0.1">
                    <dte:SAT ClaseDocumento="dte">
                        <dte:DTE ID="DatosCertificados">
                            <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales Tipo="' . $tipoDocumento . '" FechaHoraEmision="' . $params['fechaFactura'] . 'T' . $horaEmision . '" CodigoMoneda="' . $codigoMoneda . '" />
                                <dte:Emisor NITEmisor="' . $nitEmisor . '" NombreEmisor="' . $nombreEmisor . '" CodigoEstablecimiento="' . $codEstablecimiento . '" NombreComercial="' . $nombreComercial . '" AfiliacionIVA="' . $afiliacionIVA . '">
                                    <dte:DireccionEmisor>
                                        <dte:Direccion>' . $direccionEmisor . '</dte:Direccion>
                                        <dte:CodigoPostal>' . $codigoPostal . '</dte:CodigoPostal>
                                        <dte:Municipio>' . $municipio . '</dte:Municipio>
                                        <dte:Departamento>' . $depto . '</dte:Departamento>
                                        <dte:Pais>' . $pais . '</dte:Pais>
                                    </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor NombreReceptor="' . $nombreComprador . '" IDReceptor="' . $nitComprador . '">
                                    <dte:DireccionReceptor>
                                        <dte:Direccion>' . $direccionComprador . '</dte:Direccion>
                                        <dte:CodigoPostal>' . $codigoPostalComprador . '</dte:CodigoPostal>
                                        <dte:Municipio>' . $munComprador . '</dte:Municipio>
                                        <dte:Departamento>' . $deptoComprador . '</dte:Departamento>
                                        <dte:Pais>' . $paisComprador . '</dte:Pais>
                                    </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                    <dte:Frase TipoFrase="' . $regE['tipoFrase'] . '" CodigoEscenario="' . $regE['codigoEscenario'] . '" />';
		if ($regE['agenteRetenedor'] === '1') {
			$xml_data .= '<dte:Frase TipoFrase="2" CodigoEscenario="1"/>';
		}
		$xml_data .= '</dte:Frases>
        <dte:Items>';
		//
		$params['idUsuarios'] = $idUsuarios;
		$totalIDP = 0;
		$totalIVA = 0;
		$totalVenta = 0;
		foreach ($this->getProductosVenta($params) as $key => $value) {
			//CALCULO IDP
			$precioUnitario = 0;
			$precio = 0;
			$subtotal = 0;
			$iva = 0;
			$idp = 0;
			$total = 0;
			$combustible = "";
			$codCombustible = 0;
			switch ($value['codigo']) {
				case '9':
					$codCombustible = 1;
					$combustible = "SUPER";
					$precioUnitario = ($value['precio'] - 4.70);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 6);
					$idp = round(($value['cantidad'] * 4.70), 6);
					$total = ($precio + $idp);
					break;
				case '8':
					$codCombustible = 2;
					$combustible = "REGULAR";
					$precioUnitario = ($value['precio'] - 4.60);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 6);
					$idp = round(($value['cantidad'] * 4.60), 6);
					$total = ($precio + $idp);
					break;
				case '7':
					$codCombustible = 4;
					$combustible = "DIESEL";
					$precioUnitario = ($value['precio'] - 1.30);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 6);
					$idp = round(($value['cantidad'] * 1.30), 6);
					$total = ($precio + $idp);
					break;
			}
			$totalIDP += $idp;
			$totalIVA += $iva;
			$totalVenta += $total;
			//
			$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="1">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>GLS</dte:UnidadMedida>
                            <dte:Descripcion>' . $combustible . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $precioUnitario . '</dte:PrecioUnitario>
                            <dte:Precio>' . $precio . '</dte:Precio>
                            <dte:Descuento>0.00</dte:Descuento>
                            <dte:Impuestos>
                              <dte:Impuesto>
                                <dte:NombreCorto>IVA</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                <dte:MontoGravable>' . $subtotal . '</dte:MontoGravable>
                                <dte:MontoImpuesto>' . $iva . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                              <dte:Impuesto>
                                <dte:NombreCorto>PETROLEO</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>' . $codCombustible . '</dte:CodigoUnidadGravable>
                                <dte:CantidadUnidadesGravables>' . $value['cantidad'] . '</dte:CantidadUnidadesGravables>
                                <dte:MontoImpuesto>' . $idp . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . round($total, 2) . '</dte:Total>
                          </dte:Item>';
		}
		$xml_data .= '</dte:Items>
                                <dte:Totales>
                                  <dte:TotalImpuestos>
                                    <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . $totalIVA . '"/>
                                    <dte:TotalImpuesto NombreCorto="PETROLEO" TotalMontoImpuesto="' . $totalIDP . '"/>
                                  </dte:TotalImpuestos>
                                  <dte:GranTotal>' . round($totalVenta, 2) . '</dte:GranTotal>
                                </dte:Totales>
                                </dte:DatosEmision>
                               </dte:DTE>
                          </dte:SAT>
                        </dte:GTDocumento>';
		// echo $xml_data;
		// exit();
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Authorization: ' . $token . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['Codigo'] === 1) {
			//Validar NIT
			$sql_c = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
			$query_c = mysql_query($sql_c, dbCon::conPrincipal());
			$reg_c = mysql_fetch_assoc($query_c);
			if ($reg_c['id'] == '') {
				$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,idTipoClientes,idEmpresas,created_at) "
					. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . ($direccionComprador ?: 'CIUDAD') . "',1," . $idEmpresas . ",'" . $this->timestamp . "');";
				$queryC = mysql_query($sqlC, dbCon::conPrincipal());
				if ($queryC == TRUE) {
					$idClientes = mysql_insert_id();
					error_log('cliente creado exitosamente\n');
				} else {
					error_log('error al crear cliente ' . $sqlC);
				}
			} else {
				$sqlU = "update clientes set nombreC='" . $params['nombre'] . "',nombreF='" . $params['nombre'] . "', direccionC='" . ($direccionComprador ?: 'CIUDAD') . "'"
					. " where id=" . $reg_c['id'] . ";";
				$queryU = mysql_query($sqlU, dbCon::conPrincipal());
				$idClientes = $reg_c['id'];
			}
			//ventas
			$sql = "insert into ventas
                    (id,
                    serie,
                    correlativo,
                    fechaFactura,
                    valorFactura,
                    subtotal,
                    descuento,
                    descuentoP,
                    total,
                    anticipo,
                    saldo,
                    iva,
                    tipoCambio,
                    totalDolares,
                    totalEnLetras,
                    totalEnLetrasDolares,
                    nit,
                    nombre,
                    direccion,
                    idTipoOperacion,
                    idTipoVenta,
                    conceptoVenta,
                    idUsuarios,
                    idSucursales,
                    idEmpresas,
                    created_at,
                    updated_at,
                    idFormatos,
                    idPartidas,
                    statusCierre,
                    anulacion,
                    idAdminUser,
                    motivoAnulacion,
                    anulacion_at,
                    idClientes,
                    idVendedores,
                    autorizacionFEL,
                    fechaEmisionFEL,
                    tipoTransaccion,
                    idBombas,
                    xml,
                    numeroAcceso)
                    values(
                        null,
                        '" . $response['Serie'] . "',
                        '" . $response['NUMERO'] . "',
                        '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                        '" . $params['total'] . "',
                        '" . $params['subtotal'] . "',
                        '" . $params['descuento'] . "',
                        '" . $params['descuentoP'] . "',
                        '" . $params['total'] . "',
                        '" . $params['anticipo'] . "',
                        '" . $params['total'] . "',
                        '" . $params['iva'] . "',
                        '0.00',
                        '0.00',
                        '" . $params['totalEnLetras'] . "',
                        '-',
                        '" . $params['nit'] . "',
                        '" . $nombreComprador . "',
                        '" . ($direccionComprador ?: 'CIUDAD') . "',
                        '" . $params['idTipoOperacion'] . "',
                        '" . $params['idTipoVenta'] . "',
                        '" . $params['observaciones'] . "',
                        '" . $idUsuarios . "',
                        '" . $params['idSucursales'] . "',
                        '" . $idEmpresas . "',
                        '" . $this->timestamp . "',
                        null,
                        0,
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        '" . $idUsuarios . "','" . $response['Autorizacion'] . "','" . $response['Fecha_DTE'] . "',1,0,'" . $xml_data . "',null);";
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query) {
				//ventas detalle
				$idVenta = mysql_insert_id();
				$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
					. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//INGRESO DE VENTA AL CREDITO
					if ($params['tipoVenta'] === '2') {
						$this->ventasCredito($params['nit'], $idVenta, ($response['Serie'] . '-' . $response['NUMERO']), $params['total'], $params['anticipo'], $params['total'], date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
					}
					//GUARDAR PARTIDA AUTOMATICA
					if ($params['idFormato'] !== '0') {
						$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
						$params['idTipoOperacionPartida'] = 2;
						$params['conceptoCompra'] = 'REF. FEL. ' . $response['Autorizacion'];
						$params['idEmpresas'] = $idEmpresas;
						$params['idUsuarios'] = $idUsuarios;
						$params['idVenta'] = $idVenta;
						$this->savePartidaAutomatica($params);
					}
					//RESPONSE
					$responseF[] = array('message' => 'success', 'idVenta' => $idVenta);
				} else {
					$responseF[] = array('message' => 'failed step 2', 'query' => $sql2);
					return $responseF;
				}
			} else {
				$responseF[] = array('message' => 'failed step 1', 'query' => $sql);
				return $responseF;
			}
		} else {
			$responseF[] = array('message' => 'failed step FEL', 'error' => $response['ResponseDATA1']);
			//echo $xml_data;
		}
		return $responseF;
	}

	/** cambiarEstadoOrden
	 *
	 */
	public function cambiarEstadoOrden($params)
	{
		$response = "";
		//ACTUALIZACION DE SALDO DE FACTURAS
		$sql = "update pedidos set estadoOrden='" . $params['estado'] . "' where id=" . $params['idPedido'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed');
		}
		//
		return $response;
	}

	/** METODO eliminarValeCaja
	 *
	 */
	public function eliminarValeCaja($params)
	{
		$response = "";
		$sql = "delete from valesCaja where id=" . $params['idVale'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
			$response[] = array('message' => 'success');
		} else {
			$response[] = array('message' => 'failed', 'Query' => $sql);
		}
		return $response;
	}

	/** METODO emitirFactura INFILE
	 *
	 */
	public function emitirFacturaINFILE_normal($params, $idUsuarios, $idEmpresas)
	{
		$responseF = "";
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS
                from
                    empresas
                where
                    id=" . $idEmpresas . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//SCRIPT EMISION DE FACTURA
		//URL PRODUCTIVO
		$URL = "https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml";
		$UsuarioAPI = $regE['usuarioAPI'];
		$LlaveAPI = $regE['llaveAPI'];
		$UsuarioFirma = $regE['usuarioFirma'];
		$LlaveFirma = $regE['llaveFirma'];
		date_default_timezone_set("America/Guatemala");
		//GENERACION DE XML
		//$direccion = $this->consultaNIT($params);
		$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
		$direccionComprador = $params['direccion'];
		$codigoEstablecimiento = $regE['codigoEstablecimiento'];
		if ($regE['nit'] === '36963011') {
			if ($params['idSucursales'] === '1') {
				$codigoEstablecimiento = 2;
			} else {
				$codigoEstablecimiento = 3;
			}
		}
		$tipoDocumento = "FACT";
		if ($regE['tipoAfiliacion'] === 'PEQ') {
			$tipoDocumento = "FPEQ";
		}
		//
		$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
                        <dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.2.0">
                          <dte:SAT ClaseDocumento="dte">
                            <dte:DTE ID="DatosCertificados">
                              <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $params['fechaFactura'] . 'T' . $this->time . '-06:00" Tipo="' . $tipoDocumento . '"></dte:DatosGenerales>
                                <dte:Emisor AfiliacionIVA="' . $regE['tipoAfiliacion'] . '" CodigoEstablecimiento="' . $codigoEstablecimiento . '" CorreoEmisor="marriolaj@gmail.com" NITEmisor="' . $regE['nit'] . '" NombreComercial="' . $regE['nombreComercial'] . '" NombreEmisor="' . $regE['razonSocial'] . '">
                                  <dte:DireccionEmisor>
                                    <dte:Direccion>' . $regE['direccion'] . '</dte:Direccion>
                                    <dte:CodigoPostal>01009</dte:CodigoPostal>
                                    <dte:Municipio>' . $regE['municipio'] . '</dte:Municipio>
                                    <dte:Departamento>' . $regE['departamento'] . '</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor CorreoReceptor="" IDReceptor="' . str_replace('-', '', $params['nit']) . '" NombreReceptor="' . $nombreComprador . '">
                                  <dte:DireccionReceptor>
                                    <dte:Direccion>' . ($direccionComprador ?: 'CIUDAD') . '</dte:Direccion>
                                    <dte:CodigoPostal>0101</dte:CodigoPostal>
                                    <dte:Municipio>GUATEMALA</dte:Municipio>
                                    <dte:Departamento>GUATEMALA</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                  <dte:Frase CodigoEscenario="' . $regE['codigoEscenario'] . '" TipoFrase="' . $regE['tipoFrase'] . '"></dte:Frase>';
		if ($regE['agenteRetenedor'] === '1') {
			$xml_data .= '<dte:Frase TipoFrase="2" CodigoEscenario="1"/>';
		}
		$xml_data .= '</dte:Frases>
                                <dte:Items>';
		$params['idUsuarios'] = $idUsuarios;
		$totalVenta = 0;
		$totalIVA = 0;
		foreach ($this->getProductosVenta($params) as $key => $value) {
			$totalVenta += $value['total'];
			$totalIVA += $value['iva'];
			//
			$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="1">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>UND</dte:UnidadMedida>
                            <dte:Descripcion>' . $value['descLarga'] . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . round($value['precio'], 3) . '</dte:PrecioUnitario>
                            <dte:Precio>' . ($value['precio'] * $value['cantidad']) . '</dte:Precio>
                            <dte:Descuento>0.00</dte:Descuento>';
			if ($regE['tipoAfiliacion'] !== 'PEQ') {
				$xml_data .= '<dte:Impuestos>
                              <dte:Impuesto>
                                <dte:NombreCorto>IVA</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                <dte:MontoGravable>' . $value['subtotal'] . '</dte:MontoGravable>
                                <dte:MontoImpuesto>' . $value['iva'] . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                            </dte:Impuestos>';
			}
			$xml_data .= '<dte:Total>' . round($value['total'], 2) . '</dte:Total>
                          </dte:Item>';
		}
		$xml_data .= '</dte:Items>
                                <dte:Totales>';
		if ($regE['tipoAfiliacion'] !== 'PEQ') {
			$xml_data .= '<dte:TotalImpuestos>
                                    <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . $totalIVA . '"/>
                                  </dte:TotalImpuestos>';
		}
		$xml_data .= '<dte:GranTotal>' . round($totalVenta, 2) . '</dte:GranTotal>
                                </dte:Totales>
                               </dte:DTE>
                          </dte:SAT>
                        </dte:GTDocumento>';
		// echo $xml_data;
		// exit();
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('UsuarioAPI: ' . $UsuarioAPI . '', 'LlaveAPI: ' . $LlaveAPI . '', 'UsuarioFirma: ' . $UsuarioFirma . '', 'LlaveFirma: ' . $LlaveFirma . '', 'Identificador: ' . date('YmdHis') . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['uuid']) {
			//Validar NIT
			$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
			$query = mysql_query($sql, dbCon::conPrincipal());
			$reg = mysql_fetch_assoc($query);
			if ($reg['id'] == '') {
				$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,idTipoClientes,idEmpresas,created_at) "
					. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . ($direccionComprador ?: 'CIUDAD') . "',1," . $params['idEmpresas'] . ",'" . $this->timestamp . "');";
				$queryC = mysql_query($sqlC, dbCon::conPrincipal());
				if ($queryC == TRUE) {
					$idClientes = mysql_insert_id();
					error_log('cliente creado exitosamente\n');
				} else {
					error_log('error al crear cliente ' . $sqlC);
				}
			} else {
				$sqlU = "update clientes set nombreC='" . $params['nombre'] . "',nombreF='" . $params['nombre'] . "', direccionC='" . ($direccionComprador ?: 'CIUDAD') . "'"
					. " where id=" . $reg['id'] . ";";
				$queryU = mysql_query($sqlU, dbCon::conPrincipal());
				$idClientes = $reg['id'];
			}
			//ventas
			$sql = "insert into ventas
                    (`id`,
                    `serie`,
                    `correlativo`,
                    `fechaFactura`,
                    `valorFactura`,
                    `subtotal`,
                    `descuento`,
                    `descuentoP`,
                    `total`,
                    `anticipo`,
                    `saldo`,
                    `iva`,
                    `tipoCambio`,
                    `totalDolares`,
                    `totalEnLetras`,
                    `totalEnLetrasDolares`,
                    `nit`,
                    `nombre`,
                    `direccion`,
                    `idTipoOperacion`,
                    `idTipoVenta`,
                    `conceptoVenta`,
                    `idUsuarios`,
                    `idSucursales`,
                    `idEmpresas`,
                    `created_at`,
                    `updated_at`,
                    `idFormatos`,
                    `idPartidas`,
                    `statusCierre`,
                    `anulacion`,
                    `idAdminUser`,
                    `motivoAnulacion`,
                    `anulacion_at`,
                    `idClientes`,
                    `idVendedores`,
                    `autorizacionFEL`,
                    `fechaEmisionFEL`,
                    `tipoTransaccion`,
                    `xml`)
                    values(
                        null,
                        '" . $response['serie'] . "',
                        '" . $response['numero'] . "',
                        '" . $params['fechaFactura'] . "',
                        '" . $params['valorFactura'] . "',
                        '" . $totalSubtotal . "',
                        '" . $totalIDP . "',
                        '" . $params['descuentoP'] . "',
                        '" . $params['total'] . "',
                        '" . $params['anticipo'] . "',
                        '" . $params['total'] . "',
                        '" . $totalIVA . "',
                        '0.00',
                        '0.00',
                        '" . $params['totalEnLetras'] . "',
                        '-',
                        '" . $params['nit'] . "',
                        '" . $params['nombre'] . "',
                        '" . ($direccionComprador ?: 'CIUDAD') . "',
                        '" . $params['idTipoOperacion'] . "',
                        '" . $params['idTipoVenta'] . "',
                        '" . $params['observaciones'] . "',
                        '" . $idUsuarios . "',
                        '" . $params['idSucursales'] . "',
                        '" . $idEmpresas . "',
                        '" . $this->timestamp . "',
                        null,
                        0,
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        '" . $idUsuarios . "','" . $response['uuid'] . "','" . $response['fecha'] . "',1,'" . $xml_data . "');";
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query) {
				//ventas detalle
				$idVenta = mysql_insert_id();
				$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
					. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//INGRESO DE VENTA AL CREDITO
					if ($params['tipoVenta'] === '2') {
						$this->ventasCredito($params['nit'], $idVenta, ($response['serie'] . '-' . $response['numero']), $params['total'], $params['anticipo'], $params['total'], date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
					}
					//GUARDAR PARTIDA AUTOMATICA
					if ($params['idFormato'] !== '0') {
						$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
						$params['idTipoOperacionPartida'] = 2;
						$params['conceptoCompra'] = 'REF. FEL. ' . $response['Autorizacion'];
						$params['idEmpresas'] = $idEmpresas;
						$params['idUsuarios'] = $idUsuarios;
						$params['idVenta'] = $idVenta;
						$this->savePartidaAutomatica($params);
					}
					//RESPONSE
					$responseF[] = array('message' => 'success', 'idVenta' => $idVenta);
				} else {
					$responseF[] = array('message' => 'failed step 2', 'query' => $sql2);
					return $responseF;
				}
			} else {
				$responseF[] = array('message' => 'failed step 1', 'query' => $sql);
				return $responseF;
			}
		} else {
			$responseF[] = array('message' => $response['descripcion_errores'][0]['mensaje_error']);
			//echo $xml_data;
		}
		return $responseF;
	}

	/**
	 * getPreciosProducto
	 */
	public function getPreciosProducto($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    precioPublico,
                    precio1,
                    precio2,
                    precio3,
                    precio4,
                    precio5
                FROM
                    productos
                WHERE
                    id = " . $params['idProducto'] . ";";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO cerrarVentaINFILE
	 *
	 */
	public function cerrarVentaINFILE($params, $idUsuarios, $idSucursales, $idEmpresas)
	{
		if ($params['tipoTransaccion'] === '1') {
			//EMITE FACTURA
			//CONSULTA DATOS EMPRESA
			$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $idEmpresas . ";";
			$queryE = mysql_query($sqlE, dbCon::conPrincipal());
			$regE = mysql_fetch_assoc($queryE);
			//
			if ($regE['tipoFacturacion'] === '1') {
				//EMITE FEL
				//URL PRODUCTIVO
				$URL = "https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml";
				$UsuarioAPI = $regE['usuarioAPI'];
				$LlaveAPI = $regE['llaveAPI'];
				$UsuarioFirma = $regE['usuarioFirma'];
				$LlaveFirma = $regE['llaveFirma'];
				date_default_timezone_set("America/Guatemala");
				//GENERACION DE XML
				$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
				$direccion = $this->consultaNIT($params);
				$direccionComprador = $direccion[0]['direccion'];
				//
				$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
                        <dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.2.0">
                          <dte:SAT ClaseDocumento="dte">
                            <dte:DTE ID="DatosCertificados">
                              <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $this->date . 'T' . $this->time . '-06:00" Tipo="FACT"></dte:DatosGenerales>
                                <dte:Emisor AfiliacionIVA="' . $regE['tipoAfiliacion'] . '" CodigoEstablecimiento="' . $regE['codigoEstablecimiento'] . '" CorreoEmisor="marriolaj@gmail.com" NITEmisor="' . $regE['nit'] . '" NombreComercial="' . $regE['nombreComercial'] . '" NombreEmisor="' . $regE['razonSocial'] . '">
                                  <dte:DireccionEmisor>
                                    <dte:Direccion>' . $regE['direccion'] . '</dte:Direccion>
                                    <dte:CodigoPostal>01009</dte:CodigoPostal>
                                    <dte:Municipio>' . $regE['municipio'] . '</dte:Municipio>
                                    <dte:Departamento>' . $regE['departamento'] . '</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor CorreoReceptor="" IDReceptor="' . str_replace('-', '', $params['nit']) . '" NombreReceptor="' . $nombreComprador . '">
                                  <dte:DireccionReceptor>
                                    <dte:Direccion>' . ($direccionComprador ?: 'CIUDAD') . '</dte:Direccion>
                                    <dte:CodigoPostal>0101</dte:CodigoPostal>
                                    <dte:Municipio>GUATEMALA</dte:Municipio>
                                    <dte:Departamento>GUATEMALA</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                  <dte:Frase CodigoEscenario="' . $regE['codigoEscenario'] . '" TipoFrase="' . $regE['tipoFrase'] . '"></dte:Frase>';
				if ($regE['agenteRetenedor'] === '1') {
					$xml_data .= '<dte:Frase TipoFrase="2" CodigoEscenario="1"/>';
				}
				$xml_data .= '</dte:Frases>
                                <dte:Items>';
				$params['idUsuarios'] = $idUsuarios;
				$totalVenta = 0;
				$totalIVA = 0;
				foreach ($this->getProductosVenta($params) as $key => $value) {
					$totalVenta += $value['total'];
					$totalIVA += $value['iva'];
					//
					$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="1">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>UND</dte:UnidadMedida>
                            <dte:Descripcion>' . $value['descLarga'] . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . round($value['precio'], 3) . '</dte:PrecioUnitario>
                            <dte:Precio>' . ($value['precio'] * $value['cantidad']) . '</dte:Precio>
                            <dte:Descuento>0.00</dte:Descuento>
                            <dte:Impuestos>
                              <dte:Impuesto>
                                <dte:NombreCorto>IVA</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                <dte:MontoGravable>' . $value['subtotal'] . '</dte:MontoGravable>
                                <dte:MontoImpuesto>' . $value['iva'] . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . round($value['total'], 2) . '</dte:Total>
                          </dte:Item>';
				}
				$xml_data .= '</dte:Items>
                                <dte:Totales>
                                  <dte:TotalImpuestos>
                                    <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . $totalIVA . '"/>
                                  </dte:TotalImpuestos>
                                  <dte:GranTotal>' . round($totalVenta, 2) . '</dte:GranTotal>
                                </dte:Totales>
                               </dte:DTE>
                          </dte:SAT>
                        </dte:GTDocumento>';
				// echo $xml_data;
				// exit();
				$ch = curl_init($URL);
				curl_setopt($ch, CURLOPT_MUTE, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('UsuarioAPI: ' . $UsuarioAPI . '', 'LlaveAPI: ' . $LlaveAPI . '', 'UsuarioFirma: ' . $UsuarioFirma . '', 'LlaveFirma: ' . $LlaveFirma . '', 'Identificador: ' . date('YmdHis') . ''));
				curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
				$response = json_decode($output, true);
				if ($response['uuid']) {
					//$responseF[] = array('message' => 'success', 'autorizacion' => $response['Autorizacion'], 'serie' => $response['Serie'], 'numero' => $response['NUMERO'], 'fechaEmision' => $response['Fecha_DTE']);
					$saldo = 0.00;
					if ($params['tipoVenta'] === '2') {
						$saldo = $params['saldo'] ?: $params['total'];
					}
					//CREAR CLIENTE SI ID ES VACIO Y QUE NO SEA CF
					$idClientes = "0";
					if ($params['nombreF'] != 'Consumidor Final') {
						//Validar NIT
						$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
						$query = mysql_query($sql, dbCon::conPrincipal());
						$reg = mysql_fetch_assoc($query);
						if ($reg['id'] == '') {
							$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,telefonoC,idTipoClientes,idEmpresas,created_at,mail) "
								. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "',1," . $idEmpresas . ",'" . $this->timestamp . "','" . $params['mail'] . "');";
							$queryC = mysql_query($sqlC, dbCon::conPrincipal());
							if ($queryC == TRUE) {
								$idClientes = mysql_insert_id();
								error_log('cliente creado exitosamente\n');
							} else {
								error_log('error al crear cliente ' . $sqlC);
							}
						} else {
							$sqlU = "update clientes set nombreC='" . $params['nombre'] . "',nombreF='" . $params['nombre'] . "', direccionC='" . $params['direccion'] . "',telefonoC='" . $params['telefono'] . "',mail='" . $params['mail'] . "'"
								. " where id=" . $reg['id'] . ";";
							$queryU = mysql_query($sqlU, dbCon::conPrincipal());
							$idClientes = $reg['id'];
						}
					}
					// GUARDA VENTA
					$doc = explode('-', $params['correlativo']);
					$sql = "insert into ventas
                            (id,
                            serie,
                            correlativo,
                            fechaFactura,
                            valorFactura,
                            subtotal,
                            descuento,
                            descuentoP,
                            total,
                            anticipo,
                            saldo,
                            iva,
                            tipoCambio,
                            totalDolares,
                            totalEnLetras,
                            totalEnLetrasDolares,
                            nit,
                            nombre,
                            direccion,
                            idTipoOperacion,
                            idTipoVenta,
                            conceptoVenta,
                            idUsuarios,
                            idSucursales,
                            idEmpresas,
                            created_at,
                            updated_at,
                            idFormatos,
                            idPartidas,
                            statusCierre,
                            anulacion,
                            idAdminUser,
                            motivoAnulacion,
                            anulacion_at,
                            idClientes,
                            idVendedores,
                            autorizacionFEL,
                            fechaEmisionFEL,
                            tipoTransaccion,xml,tipoFEL)
                        values(
                            null,
                            '" . $response['serie'] . "',
                            '" . $response['numero'] . "',
                            '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                            '" . $params['total'] . "',
                            '" . $params['subtotal'] . "',
                            '" . $params['descuentoM'] . "',
                            '" . $params['descuentoP'] . "',
                            '" . $params['total'] . "',
                            '" . $params['anticipo'] . "',
                            '" . $saldo . "',
                            '" . $params['iva'] . "',
                            '" . $params['tasaCambio'] . "',
                            '" . $params['totalDolares'] . "',
                            '" . $params['totalEnLetras'] . "',
                            '" . $params['totalEnLetrasDolares'] . "',
                            '" . $params['nit'] . "',
                            '" . $nombreComprador . "',
                            '" . $params['direccion'] . "',
                            '1',
                            '" . $params['tipoVenta'] . "',
                            '" . $params['observaciones'] . "',
                            '" . $idUsuarios . "',
                            '" . $idSucursales . "',
                            '" . $idEmpresas . "',
                            '" . $this->timestamp . "',
                            null,
                            " . $params['idFormato'] . ",
                            0,
                            0,
                            0,
                            null,
                            null,
                            null,
                            '" . $idClientes . "',
                            '" . $params['vendedores'] . "','" . $response['uuid'] . "','" . $response['fecha'] . "'," . $params['tipoTransaccion'] . ",'" . $xml_data . "','" . $tipoDocumento . "');";
					$query = mysql_query($sql, dbCon::conPrincipal());
					//echo $query;
					if ($query) {
						//OBTIENE ID VENTA
						$idVenta = mysql_insert_id();
						$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
							. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
						$query2 = mysql_query($sql2, dbCon::conPrincipal());
						if ($query2) {
							if (isset($params['idPedido'])) {
								$this->updateEstadoPedido($params['idPedido'], $idVenta, '3');
							}
							//INGRESO DE DETALLE LIQUIDACION VENTA AL CONTADO
							if ($params['tipoVenta'] === '1') {
								$this->liquidacionVenta($params['detallePago'], $idVenta);
							}
							//INGRESO DE VENTA AL CREDITO
							if ($params['tipoVenta'] === '2') {
								$this->ventasCredito($params['nit'], $idVenta, $params['correlativo'], $params['total'], $params['anticipo'], $saldo, date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
								//$this->liquidacionVenta($params['detallePago'], $idVenta);
							}
							//GUARDAR PARTIDA AUTOMATICA
							if ($params['idFormato'] !== '0') {
								$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
								$params['idTipoOperacionPartida'] = 2;
								$params['conceptoCompra'] = 'REF. FEL. ' . $response['Autorizacion'];
								$params['idEmpresas'] = $idEmpresas;
								$params['idUsuarios'] = $idUsuarios;
								$params['idVenta'] = $idVenta;
								$this->savePartidaAutomatica($params);
							}
							//DESCUENTA PRODUCTO DE INVENTARIO
							$this->movimientoInventario($idVenta, 'FEL ' . ($response['Serie'] . '-' . $response['NUMERO']), 'salida', $params['fechaFactura']);
							//
							//ENVIO DE MAIL
							if ($params['mail'] !== '') {
								$this->envioMailFEL($params['mail'], $params['nombre'], $response['Autorizacion'], $nombreComercial, $nitEmisor, $idVenta);
							}
							//
							$responseF[] = array('message' => 'success', 'idVenta' => $idVenta, 'idSucursales' => $idSucursales, 'autorizacion' => $response['Autorizacion']);
						} else {
							$responseF[] = array('message' => 'failed step 2', 'error' => $sql2);
						}
					} else {
						$error = mysql_error();
						$responseF[] = array('message' => $error, 'Query' => $sql, 'Error' => $error);
					}
				} else {
					//echo $xml_data;
					$responseF[] = array('message' => $response['descripcion_errores'][0]['mensaje_error']);
				}
				return $responseF;
			} else {
				//FACTURA NORMAL
				$response = "";
				$saldo = 0.00;
				if ($params['tipoVenta'] === '2') {
					$saldo = $params['saldo'] ?: $params['total'];
				}
				//CREAR CLIENTE SI ID ES VACIO Y QUE NO SEA CF
				$idClientes = "0";
				if ($params['nombreF'] != 'Consumidor Final') {
					//Validar NIT
					$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
					$query = mysql_query($sql, dbCon::conPrincipal());
					$reg = mysql_fetch_assoc($query);
					if ($reg['id'] == '') {
						$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,telefonoC,idTipoClientes,idEmpresas,created_at) "
							. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "',1," . $idEmpresas . ",'" . $this->timestamp . "');";
						$queryC = mysql_query($sqlC, dbCon::conPrincipal());
						if ($queryC == TRUE) {
							$idClientes = mysql_insert_id();
							error_log('cliente creado exitosamente\n');
						} else {
							error_log('error al crear cliente ' . $sqlC);
						}
					} else {
						$sqlU = "update clientes set nombreC='" . $params['nombre'] . "',nombreF='" . $params['nombre'] . "', direccionC='" . $params['direccion'] . "',telefonoC='" . $params['telefono'] . "',mail='" . $params['mail'] . "'"
							. " where id=" . $reg['id'] . ";";
						$queryU = mysql_query($sqlU, dbCon::conPrincipal());
						$idClientes = $reg['id'];
					}
				}
				// GUARDA VENTA
				$doc = explode('-', $params['correlativo']);
				$sql = "insert into ventas
                        (id,
                        serie,
                        correlativo,
                        fechaFactura,
                        valorFactura,
                        subtotal,
                        descuento,
                        descuentoP,
                        total,
                        anticipo,
                        saldo,
                        iva,
                        tipoCambio,
                        totalDolares,
                        totalEnLetras,
                        totalEnLetrasDolares,
                        nit,
                        nombre,
                        direccion,
                        idTipoOperacion,
                        idTipoVenta,
                        conceptoVenta,
                        idUsuarios,
                        idSucursales,
                        idEmpresas,
                        created_at,
                        updated_at,
                        idFormatos,
                        idPartidas,
                        statusCierre,
                        anulacion,
                        idAdminUser,
                        motivoAnulacion,
                        anulacion_at,
                        idClientes,
                        idVendedores,
                        autorizacionFEL,
                        fechaEmisionFEL,
                        tipoTransaccion)
                    values(
                        null,
                        '" . $doc[0] . "',
                        '" . $doc[1] . "',
                        '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                        '" . $params['total'] . "',
                        '" . $params['subtotal'] . "',
                        '" . $params['descuentoM'] . "',
                        '" . $params['descuentoP'] . "',
                        '" . $params['total'] . "',
                        '" . $params['anticipo'] . "',
                        '" . $saldo . "',
                        '" . $params['iva'] . "',
                        '" . $params['tasaCambio'] . "',
                        '" . $params['totalDolares'] . "',
                        '" . $params['totalEnLetras'] . "',
                        '" . $params['totalEnLetrasDolares'] . "',
                        '" . $params['nit'] . "',
                        '" . $params['nombre'] . "',
                        '" . $params['direccion'] . "',
                        '1',
                        '" . $params['tipoVenta'] . "',
                        '" . $params['observaciones'] . "',
                        '" . $idUsuarios . "',
                        '" . $idSucursales . "',
                        '" . $idEmpresas . "',
                        '" . $this->timestamp . "',
                        null,
                        " . $params['idFormato'] . ",
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        '" . $idClientes . "',
                    '" . $params['vendedores'] . "',null,null," . $params['tipoTransaccion'] . ");";
				$query = mysql_query($sql, dbCon::conPrincipal());
				//echo $query;
				if ($query) {
					//OBTIENE ID VENTA
					$idVenta = mysql_insert_id();
					$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
						. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
					$query2 = mysql_query($sql2, dbCon::conPrincipal());
					if ($query2) {
						//INGRESO DE DETALLE LIQUIDACION VENTA AL CONTADO
						if ($params['tipoVenta'] === '1') {
							$this->liquidacionVenta($params['detallePago'], $idVenta);
						}
						//INGRESO DE VENTA AL CREDITO
						if ($params['tipoVenta'] === '2') {
							$this->ventasCredito($params['idClientes'], $idVenta, $params['correlativo'], $params['total'], $params['anticipo'], $saldo, date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
							//$this->liquidacionVenta($params['detallePago'], $idVenta);
						}
						//GUARDAR PARTIDA AUTOMATICA
						if ($params['idFormato'] !== '0') {
							$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
							$params['idTipoOperacionPartida'] = 2;
							$params['conceptoCompra'] = 'REF. FACT. ' . $params['correlativo'];
							$params['idEmpresas'] = $idEmpresas;
							$params['idUsuarios'] = $idUsuarios;
							$params['idVenta'] = $idVenta;
							$this->savePartidaAutomatica($params);
						}
						//DESCUENTA PRODUCTO DE INVENTARIO
						$this->movimientoInventario($idVenta, 'FAC - ' . $params['correlativo'], 'salida', $params['fechaFactura']);
						//ACTUALIZAR CORRELATIVO
						$admin = new Admin();
						$correlativo = explode('-', $params['correlativo']);
						$updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $correlativo[1], $idEmpresas);
						//
						if (isset($params['idPedido'])) {
							$this->updateEstadoPedido($params['idPedido'], $idVenta, '3');
						}
						//
						$response[] = array('message' => 'success', 'idVenta' => $idVenta, 'idSucursales' => $idSucursales, 'serie' => $doc[0]);
					} else {
						$response[] = array('message' => 'failed step 2', 'Query' => $sql2);
					}
				} else {
					$error = mysql_error();
					$response[] = array('message' => 'failed step 1', 'Query' => $sql, 'Error' => $error);
				}
				return $response;
			}
		} else {
			//EMITE RECIBO
			$response = "";
			$saldo = 0.00;
			if ($params['tipoVenta'] === '2') {
				$saldo = $params['saldo'] ?: $params['total'];
			}
			//CREAR CLIENTE SI ID ES VACIO Y QUE NO SEA CF
			$idClientes = "0";
			if ($params['nombreF'] != 'Consumidor Final') {
				//Validar NIT
				$sql = "select id from clientes where nitC='" . $params['nit'] . "' and idEmpresas=" . $idEmpresas . ";";
				$query = mysql_query($sql, dbCon::conPrincipal());
				$reg = mysql_fetch_assoc($query);
				if ($reg['id'] == '') {
					$sqlC = "insert into clientes (nombreC,nombreF,nitC,direccionC,telefonoC,idTipoClientes,idEmpresas,created_at) "
						. "values('" . $params['nombre'] . "','" . $params['nombre'] . "','" . $params['nit'] . "','" . $params['direccion'] . "','" . $params['telefono'] . "',1," . $idEmpresas . ",'" . $this->timestamp . "');";
					$queryC = mysql_query($sqlC, dbCon::conPrincipal());
					if ($queryC == TRUE) {
						$idClientes = mysql_insert_id();
						error_log('cliente creado exitosamente\n');
					} else {
						error_log('error al crear cliente ' . $sqlC);
					}
				} else {
					$idClientes = $reg['id'];
				}
			}
			// GUARDA VENTA
			$doc = explode('-', $params['correlativo']);
			$sql = "insert into ventas
                    (id,
                    serie,
                    correlativo,
                    fechaFactura,
                    valorFactura,
                    subtotal,
                    descuento,
                    descuentoP,
                    total,
                    anticipo,
                    saldo,
                    iva,
                    tipoCambio,
                    totalDolares,
                    totalEnLetras,
                    totalEnLetrasDolares,
                    nit,
                    nombre,
                    direccion,
                    idTipoOperacion,
                    idTipoVenta,
                    conceptoVenta,
                    idUsuarios,
                    idSucursales,
                    idEmpresas,
                    created_at,
                    updated_at,
                    idFormatos,
                    idPartidas,
                    statusCierre,
                    anulacion,
                    idAdminUser,
                    motivoAnulacion,
                    anulacion_at,
                    idClientes,
                    idVendedores,
                    autorizacionFEL,
                    fechaEmisionFEL,
                    tipoTransaccion)
                values(
                    null,
                    '" . $doc[0] . "',
                    '" . $doc[1] . "',
                    '" . date("Y-m-d", strtotime($params['fechaFactura'])) . "',
                    '" . $params['total'] . "',
                    '" . $params['subtotal'] . "',
                    '" . $params['descuentoM'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $params['total'] . "',
                    '" . $params['anticipo'] . "',
                    '" . $saldo . "',
                    '" . $params['iva'] . "',
                    '" . $params['tasaCambio'] . "',
                    '" . $params['totalDolares'] . "',
                    '" . $params['totalEnLetras'] . "',
                    '" . $params['totalEnLetrasDolares'] . "',
                    '" . $params['nit'] . "',
                    '" . $params['nombre'] . "',
                    '" . $params['direccion'] . "',
                    '1',
                    '" . $params['tipoVenta'] . "',
                    '" . $params['observaciones'] . "',
                    '" . $idUsuarios . "',
                    '" . $idSucursales . "',
                    '" . $idEmpresas . "',
                    '" . $this->timestamp . "',
                    null,
                    " . $params['idFormato'] . ",
                    0,
                    0,
                    0,
                    null,
                    null,
                    null,
                    '" . $idClientes . "',
                '" . $params['vendedores'] . "',null,null," . $params['tipoTransaccion'] . ");";
			$query = mysql_query($sql, dbCon::conPrincipal());
			//echo $query;
			if ($query) {
				//OBTIENE ID VENTA
				$idVenta = mysql_insert_id();
				$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
					. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//INGRESO DE DETALLE LIQUIDACION VENTA AL CONTADO
					if ($params['tipoVenta'] === '1') {
						$this->liquidacionVenta($params['detallePago'], $idVenta);
					}
					//INGRESO DE VENTA AL CREDITO
					if ($params['tipoVenta'] === '2') {
						$this->ventasCredito($params['idClientes'], $idVenta, $params['correlativo'], $params['total'], $params['anticipo'], $saldo, date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
						//$this->liquidacionVenta($params['detallePago'], $idVenta);
					}
					//GUARDAR PARTIDA AUTOMATICA
					if ($params['idFormato'] !== '0') {
						$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
						$params['idTipoOperacionPartida'] = 2;
						$params['conceptoCompra'] = 'REF. RECIBO. ' . $params['correlativo'];
						$params['idEmpresas'] = $idEmpresas;
						$params['idUsuarios'] = $idUsuarios;
						$params['idVenta'] = $idVenta;
						$this->savePartidaAutomatica($params);
					}
					//DESCUENTA PRODUCTO DE INVENTARIO
					$this->movimientoInventario($idVenta, 'RECIBO - ' . $params['correlativo'], 'salida', $params['fechaFactura']);
					//ACTUALIZAR CORRELATIVO
					$admin = new Admin();
					$correlativo = explode('-', $params['correlativo']);
					$updateCD = $admin->updateCorrelativoDocumento($params['tipoDocumento'], $correlativo[1], $idEmpresas);
					//
					if (isset($params['idPedido'])) {
						$this->updateEstadoPedido($params['idPedido'], $idVenta, '3');
					}
					//
					$response[] = array('message' => 'success', 'idVenta' => $idVenta, 'idSucursales' => $idSucursales);
				} else {
					$response[] = array('message' => 'failed step 2', 'Query' => $sql2);
				}
			} else {
				$error = mysql_error();
				$response[] = array('message' => 'failed step 1', 'Query' => $sql, 'Error' => $error);
			}
			return $response;
		}
	}

	/**
	 * procesarPagare
	 */
	public function procesarPagare($params)
	{
		$response = "";
		//CONSULTAR SI NUMERO DE PAGARE YA FUE INGRESADO
		$sql = "select id from pagares where numero='" . $params['numeroPagare'] . "';";
		$query = mysql_query($sql, dbCon::conPrincipal());
		$reg = mysql_fetch_assoc($query);
		if ($reg['id'] === null) {
			$sql2 = "insert into pagares values(null," . $params['idVenta'] . ",'" . $params['fechaPagare'] . "','" . $params['numeroPagare'] . "','" . $params['valorPagare'] . "','" . $params['galonesPagare'] . "','" . $params['placa'] . "','" . $params['piloto'] . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "');";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			if ($query2) {
				$response[] = array('message' => 'success');
				$this->ventasCredito($params['nitPagare'], $params['idVenta'], $params['correlativo'], $params['valorPagare'], 0, $params['valorPagare'], $params['fechaPagare'], $params['idUsuarios'], $params['idEmpresas']);
			} else {
				$error = mysql_error();
				$response[] = array('message' => $error);
			}
		} else {
			$response[] = array('message' => 'docExists');
		}
		return $response;
	}

	/**
	 * consultaFacPagare
	 */
	public function consultaFacPagare($params)
	{
		$this->resultado = null;
		$sql = "SELECT
				    pagares.id,
				    concat(ventas.serie,'-',ventas.correlativo) as factura,
				    date_format(pagares.fecha,'%d-%m-%Y') as fecha,
				    pagares.numero,
				    pagares.monto,
				    pagares.galones,
				    pagares.placa,
				    pagares.piloto,
				    usuarios.userName as usuario,
				    date_format(pagares.created_at,'%d-%m-%Y %H:%i:%s') as created_at
				FROM
				    pagares
				    inner join ventas on(pagares.idVentas=ventas.id)
				    inner join usuarios on(pagares.idUsuarios=usuarios.id)
				WHERE idVentas=" . $params['idVentas'] . ";";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/**
	 * consultaPagares
	 */
	public function consultaPagares($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['numeroPagare'] != "") {
			$filtros .= " and pagares.numero= '" . $params['numeroPagare'] . "' ";
		}
		if ($params['serieFactura'] != "") {
			$filtros .= " and ventas.serie='" . $params['serieFactura'] . "'";
		}
		if ($params['correlativoFactura'] != "") {
			$filtros .= " and ventas.correlativo='" . $params['correlativoFactura'] . "'";
		}
		if ($params['cliente'] != "") {
			$filtros .= " and concat(ventas.nit,' ',ventas.nombre) like '%" . $params['cliente'] . "%' ";
		}
		$sql = "SELECT
				    pagares.id,
				    concat(ventas.serie,'-',ventas.correlativo) as factura,
				    date_format(pagares.fecha,'%d-%m-%Y') as fecha,
				    pagares.numero,
				    pagares.monto,
				    pagares.galones,
				    pagares.placa,
				    pagares.piloto,
				    usuarios.userName as usuario,
				    empresas.nombreComercial as sucursal,
				    date_format(pagares.created_at,'%d-%m-%Y %H:%i:%s') as created_at,
				    ventas.nit,
				    ventas.nombre,
				    pagares.idVentas
				FROM
				    pagares
				    inner join ventas on(pagares.idVentas=ventas.id)
				    inner join usuarios on(pagares.idUsuarios=usuarios.id)
				    inner join empresas on(pagares.idEmpresas=empresas.id)
				WHERE
					date(pagares.fecha) between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "' " . $filtros . " ORDER BY pagares.fecha DESC;";
		//echo $sql;
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/**
	 * eliminarPagare
	 */
	public function eliminarPagare($params)
	{
		$response = "";
		$sql2 = "delete from pagares where id=" . $params['idPagare'] . ";";
		$query2 = mysql_query($sql2, dbCon::conPrincipal());
		if ($query2) {
			$sql3 = "delete from cxc where idVentas=" . $params['idVentas'] . ";";
			$query3 = mysql_query($sql3, dbCon::conPrincipal());
			if ($query3) {
				$response[] = array('message' => 'success');
			} else {
				$error = mysql_error();
				$response[] = array('message' => $error);
			}
		} else {
			$error = mysql_error();
			$response[] = array('message' => $error);
		}
		return $response;
	}

	/** METODO emitirFacturaCamara_normal
	 *
	 */
	public function emitirFacturaCamara_normal($params, $idUsuarios, $idEmpresas)
	{
		$responseF = "";
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *
                from
                    empresas
                where
                    id=" . $idEmpresas . ";";
		//echo $sqlE;
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//SCRIPT EMISION DE FACTURA
		//URL PRODUCTIVO
		$URL = "https://ws.ccgfel.gt/Api/CertificarDTE";
		$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
		$direccion = $this->consultaNIT($params);
		$direccionComprador = $direccion[0]['direccion'];
		$tipoFact = "FACT";
		if ($params['tipoVenta'] === '2') {
			$tipoFact = "FCAM";
		}
		//
		$xml_data = '<dte:GTDocumento xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" Version="0.1">
                          <dte:SAT ClaseDocumento="dte">
                            <dte:DTE ID="DatosCertificados">
                              <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $params['fechaFactura'] . 'T' . $this->time . '.000-06:00" Tipo="' . $tipoFact . '" />
                                <dte:Emisor AfiliacionIVA="' . $regE['tipoAfiliacion'] . '" CodigoEstablecimiento="' . $regE['codigoEstablecimiento'] . '" CorreoEmisor="jonathan.juarez@grupocubix.com" NITEmisor="' . $regE['nit'] . '" NombreComercial="' . $regE['nombreComercial'] . '" NombreEmisor="' . $regE['razonSocial'] . '">
                                  <dte:DireccionEmisor>
                                    <dte:Direccion>' . $regE['direccion'] . '</dte:Direccion>
                                    <dte:CodigoPostal>01010</dte:CodigoPostal>
                                    <dte:Municipio>' . $regE['municipio'] . '</dte:Municipio>
                                    <dte:Departamento>' . $regE['departamento'] . '</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor CorreoReceptor="" IDReceptor="' . str_replace('-', '', $params['nit']) . '" NombreReceptor="' . $nombreComprador . '">
                                  <dte:DireccionReceptor>
                                    <dte:Direccion>' . ($direccionComprador ?: 'CIUDAD') . '</dte:Direccion>
                                    <dte:CodigoPostal>01010</dte:CodigoPostal>
                                    <dte:Municipio>Guatemala</dte:Municipio>
                                    <dte:Departamento>Guatemala</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                  <dte:Frase CodigoEscenario="' . $regE['codigoEscenario'] . '" TipoFrase="' . $regE['tipoFrase'] . '" />
                                </dte:Frases>
                                <dte:Items>';
		$params['idUsuarios'] = $idUsuarios;
		foreach ($this->getProductosVenta($params) as $key => $value) {
			$xml_data .= '<dte:Item NumeroLinea="' . ($key + 1) . '" BienOServicio="B">
                        <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                        <dte:UnidadMedida>UND</dte:UnidadMedida>
                        <dte:Descripcion>' . $value['descLarga'] . '</dte:Descripcion>
                        <dte:PrecioUnitario>' . $value['precio'] . '</dte:PrecioUnitario>
                        <dte:Precio>' . $value['total'] . '</dte:Precio>
                        <dte:Descuento>' . $value['descuento'] . '</dte:Descuento>';
			$xml_data .= '<dte:Impuestos>
                                <dte:Impuesto>
                                    <dte:NombreCorto>IVA</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                    <dte:MontoGravable>' . round($value['subtotal'], 2) . '</dte:MontoGravable>
                                    <dte:MontoImpuesto>' . $value['iva'] . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                            </dte:Impuestos>';
			$xml_data .= '<dte:Total>' . round($value['total'], 2) . '</dte:Total></dte:Item>';
		}
		$xml_data .= '</dte:Items>
                                <dte:Totales>
                                  <dte:TotalImpuestos>
                                    <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . round($params['iva'], 2) . '"/>
                                  </dte:TotalImpuestos>
                                  <dte:GranTotal>' . $params['total'] . '</dte:GranTotal>
                                </dte:Totales>';
		if ($params['tipoVenta'] === '2') {
			$i = 1;
			$days = 30;
			$fechaCuota = date('Y-m-d', strtotime($params['fechaFactura'] . ($days * $i) . ' days'));
			$stringFCAM = 'ABONO NO ' . $i . ' MONTO: Q.' . number_format($params['total'], 2) . ' FECHA DE VENCIMIENTO: ' . $fechaCuota . ' | ';
			//
			$xml_data .= '<dte:Complementos>
                            <dte:Complemento IDComplemento="1" NombreComplemento="abono" URIComplemento="http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0">
                              <cfc:AbonosFacturaCambiaria xmlns:cfc="http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0" Version="1">
                                <cfc:Abono>
                                  <cfc:NumeroAbono>' . $i . '</cfc:NumeroAbono>
                                  <cfc:FechaVencimiento>' . $fechaCuota . '</cfc:FechaVencimiento>
                                  <cfc:MontoAbono>' . round($params['total'], 2) . '</cfc:MontoAbono>
                                </cfc:Abono>
                              </cfc:AbonosFacturaCambiaria>
                            </dte:Complemento>
                          </dte:Complementos>';
		}
		$xml_data .= '</dte:DatosEmision>
                               </dte:DTE>
                          </dte:SAT>
                        </dte:GTDocumento>';
		header('Content-Type: application/json');
		$post = array('xmlDte' => base64_encode($xml_data), 'Referencia' => $this->timestamp);
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $regE['token']));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['UUID']) {
			//ventas
			$sql = "insert into ventas
                    values(
                        null,
                        '" . $response['Serie'] . "',
                        '" . $response['Numero'] . "',
                        '" . $params['fechaFactura'] . "',
                        '" . $params['total'] . "',
                        '" . $params['subtotal'] . "',
                        '" . $params['descuento'] . "',
                        '" . $params['descuentoP'] . "',
                        '" . $params['total'] . "',
                        '" . $params['anticipo'] . "',
                        '" . $params['saldo'] . "',
                        '" . $params['iva'] . "',
                        '0.00',
                        '0.00',
                        '" . $params['totalEnLetras'] . "',
                        '-',
                        '" . $params['nit'] . "',
                        '" . $nombreComprador . "',
                        '" . ($direccionComprador ?: 'CIUDAD') . "',
                        '" . $params['tipoOperacion'] . "',
                        '" . $params['tipoVenta'] . "',
                        '" . $params['observaciones'] . " - " . $stringFCAM . "',
                        '" . $params['idUsuarios'] . "',
                        '" . $params['idSucursales'] . "',
                        '" . $idEmpresas . "',
                        '" . $this->timestamp . "',
                        null,
                        0,
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        '" . $params['idUsuarios'] . "','" . $response['UUID'] . "','" . $response['FechaHoraCertificacion'] . "',1,0,'" . $xml_data . "','" . $regE['numeroAcceso'] . "','FACT');";
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query) {
				if ($query) {
					//OBTIENE ID VENTA
					$idVenta = mysql_insert_id();
					$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
						. "where idVentas=0 and idUsuarios=" . $params['idUsuarios'] . ";";
					$query2 = mysql_query($sql2, dbCon::conPrincipal());
					if ($query2) {
						$responseF[] = array('message' => 'success', 'idVenta' => $idVenta, 'idSucursales' => $params['idSucursales'], 'autorizacion' => $response['Autorizacion']);
					} else {
						$responseF[] = array('message' => 'failed step 2', 'error' => $sql2);
					}
				} else {
					$error = mysql_error();
					$responseF[] = array('message' => 'failed step 1', 'Query' => $sql, 'Error' => $error);
				}
			} else {
				$responseF[] = array('message' => 'failed step 1', 'query' => $sql);
				return $responseF;
			}
		} else {
			$responseF[] = array('message' => $response);
			//echo $xml_data;
			//print_r($response);
		}
		return $responseF;
	}

	/** METODO emitirFacturaCamara
	 *
	 */
	public function emitirFacturaCamara($params, $idUsuarios, $idEmpresas)
	{
		$responseF = "";
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *
                from
                    empresas
                where
                    id=" . $idEmpresas . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//SCRIPT EMISION DE FACTURA
		//URL PRODUCTIVO
		$URL = "https://ws.ccgfel.gt/Api/CertificarDTE";
		date_default_timezone_set("America/Guatemala");
		//GENERACION DE XML
		$nombreComprador = str_replace("'", '&apos;', str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])));
		$direccion = $this->consultaNIT($params);
		$direccionComprador = $direccion[0]['direccion'];
		$tipoFact = "FACT";
		if ($params['tipoVenta'] === '2') {
			$tipoFact = "FCAM";
		}
		//
		$xml_data = '<dte:GTDocumento xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" Version="0.1">
                          <dte:SAT ClaseDocumento="dte">
                            <dte:DTE ID="DatosCertificados">
                              <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $params['fechaFactura'] . 'T' . $this->time . '.000-06:00" Tipo="' . $tipoFact . '" />
                                <dte:Emisor AfiliacionIVA="' . $regE['tipoAfiliacion'] . '" CodigoEstablecimiento="' . $regE['codigoEstablecimiento'] . '" CorreoEmisor="jonathan.juarez@grupocubix.com" NITEmisor="' . $regE['nit'] . '" NombreComercial="' . $regE['nombreComercial'] . '" NombreEmisor="' . $regE['razonSocial'] . '">
                                  <dte:DireccionEmisor>
                                    <dte:Direccion>' . $regE['direccion'] . '</dte:Direccion>
                                    <dte:CodigoPostal>01010</dte:CodigoPostal>
                                    <dte:Municipio>' . $regE['municipio'] . '</dte:Municipio>
                                    <dte:Departamento>' . $regE['departamento'] . '</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor CorreoReceptor="" IDReceptor="' . str_replace('-', '', $params['nit']) . '" NombreReceptor="' . $nombreComprador . '">
                                  <dte:DireccionReceptor>
                                    <dte:Direccion>' . ($direccionComprador ?: 'CIUDAD') . '</dte:Direccion>
                                    <dte:CodigoPostal>01010</dte:CodigoPostal>
                                    <dte:Municipio>Guatemala</dte:Municipio>
                                    <dte:Departamento>Guatemala</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                  <dte:Frase CodigoEscenario="' . $regE['codigoEscenario'] . '" TipoFrase="' . $regE['tipoFrase'] . '" />
                                </dte:Frases>
                                <dte:Items>';
		$params['idUsuarios'] = $idUsuarios;
		$totalIDP = 0;
		$totalIVA = 0;
		$totalVenta = 0;
		$totalSubtotal = 0;
		foreach ($this->getProductosVenta($params) as $key => $value) {
			//CALCULO IDP
			$precioUnitario = 0;
			$precio = 0;
			$subtotal = 0;
			$iva = 0;
			$idp = 0;
			$total = 0;
			$combustible = "";
			$codCombustible = 0;
			switch ($value['codigo']) {
				case '9':
					$codCombustible = 1;
					$combustible = "SUPER";
					$precioUnitario = ($value['precio'] - 4.70);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 4.70), 2);
					$total = ($precio + $idp);
					break;
				case '8':
					$codCombustible = 2;
					$combustible = "REGULAR";
					$precioUnitario = ($value['precio'] - 4.60);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 4.60), 2);
					$total = ($precio + $idp);
					break;
				case '7':
					$codCombustible = 4;
					$combustible = "DIESEL";
					$precioUnitario = ($value['precio'] - 1.30);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 1.30), 2);
					$total = ($precio + $idp);
					break;
				case '6':
					$codCombustible = 4;
					$combustible = "ION DIESEL";
					$precioUnitario = ($value['precio'] - 1.30);
					$precio = ($value['cantidad'] * $precioUnitario);
					$subtotal = round(($precio / 1.12), 2);
					$iva = round(($subtotal * 0.12), 2);
					$idp = round(($value['cantidad'] * 1.30), 2);
					$total = ($precio + $idp);
					break;
			}
			$totalIDP += $idp;
			$totalIVA += $iva;
			$totalVenta += $total;
			$totalSubtotal += $subtotal;
			//
			$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="' . ($key + 1) . '">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>GLS</dte:UnidadMedida>
                            <dte:Descripcion>' . $combustible . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $precioUnitario . '</dte:PrecioUnitario>
                            <dte:Precio>' . $precio . '</dte:Precio>
                            <dte:Descuento>0.00</dte:Descuento>
                            <dte:Impuestos>
                              <dte:Impuesto>
                                <dte:NombreCorto>IVA</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                <dte:MontoGravable>' . $subtotal . '</dte:MontoGravable>
                                <dte:MontoImpuesto>' . $iva . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                              <dte:Impuesto>
                                <dte:NombreCorto>PETROLEO</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>' . $codCombustible . '</dte:CodigoUnidadGravable>
                                <dte:CantidadUnidadesGravables>' . $value['cantidad'] . '</dte:CantidadUnidadesGravables>
                                <dte:MontoImpuesto>' . $idp . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . round($total, 2) . '</dte:Total>
                          </dte:Item>';
		}
		$xml_data .= '</dte:Items>
                                <dte:Totales>
                                  <dte:TotalImpuestos>
                                    <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . $totalIVA . '" />
                                    <dte:TotalImpuesto NombreCorto="PETROLEO" TotalMontoImpuesto="' . $totalIDP . '" />
                                  </dte:TotalImpuestos>
                                  <dte:GranTotal>' . round($totalVenta, 2) . '</dte:GranTotal>
                                </dte:Totales>';
		if ($params['tipoVenta'] === '2') {
			$i = 1;
			$days = 30;
			$fechaCuota = date('Y-m-d', strtotime($params['fechaFactura'] . ($days * $i) . ' days'));
			$stringFCAM = 'ABONO NO ' . $i . ' MONTO: Q.' . number_format($totalVenta, 2) . ' FECHA DE VENCIMIENTO: ' . $fechaCuota . ' | ';
			//
			$xml_data .= '<dte:Complementos>
                            <dte:Complemento IDComplemento="1" NombreComplemento="abono" URIComplemento="http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0">
                              <cfc:AbonosFacturaCambiaria xmlns:cfc="http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0" Version="1">
                                <cfc:Abono>
                                  <cfc:NumeroAbono>' . $i . '</cfc:NumeroAbono>
                                  <cfc:FechaVencimiento>' . $fechaCuota . '</cfc:FechaVencimiento>
                                  <cfc:MontoAbono>' . round($totalVenta, 2) . '</cfc:MontoAbono>
                                </cfc:Abono>
                              </cfc:AbonosFacturaCambiaria>
                            </dte:Complemento>
                          </dte:Complementos>';
		}
		$xml_data .= '</dte:DatosEmision>
                            </dte:DTE>
                          </dte:SAT>
                        </dte:GTDocumento>';
		header('Content-Type: application/json');
		$post = array('xmlDte' => base64_encode($xml_data), 'Referencia' => $this->timestamp);
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $regE['token']));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['UUID']) {
			//ventas
			$sql = "insert into ventas
                    values(
                        null,
                        '" . $response['Serie'] . "',
                        '" . $response['Numero'] . "',
                        '" . $params['fechaFactura'] . "',
                        '" . $params['valorFactura'] . "',
                        '" . $params['subtotal'] . "',
                        '" . $params['descuento'] . "',
                        '" . $params['descuentoP'] . "',
                        '" . $params['total'] . "',
                        '" . $params['anticipo'] . "',
                        '" . $params['saldo'] . "',
                        '" . $params['iva'] . "',
                        '0.00',
                        '0.00',
                        '" . $params['totalEnLetras'] . "',
                        '-',
                        '" . $params['nit'] . "',
                        '" . $nombreComprador . "',
                        '" . ($direccionComprador ?: 'CIUDAD') . "',
                        '" . $params['tipoOperacion'] . "',
                        '" . $params['tipoVenta'] . "',
                        '" . $params['observaciones'] . " - " . $stringFCAM . "',
                        '" . $idUsuarios . "',
                        '" . $params['idSucursales'] . "',
                        '" . $idEmpresas . "',
                        '" . $this->timestamp . "',
                        null,
                        0,
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        null,
                        '" . $params['idUsuarios'] . "','" . $response['UUID'] . "','" . $response['FechaHoraCertificacion'] . "',1,0,'" . md5($xml_data) . "',null,null);";
			$query = mysql_query($sql, dbCon::conPrincipal());
			if ($query) {
				//ventas detalle
				$idVenta = mysql_insert_id();
				$sql2 = "update ventasDetalle set idVentas=" . $idVenta . " "
					. "where idVentas=0 and idUsuarios=" . $idUsuarios . ";";
				$query2 = mysql_query($sql2, dbCon::conPrincipal());
				if ($query2) {
					//INGRESO DE VENTA AL CREDITO
					if ($params['tipoVenta'] === '2') {
						$this->ventasCredito($params['nit'], $idVenta, ($response['serie'] . '-' . $response['numero']), $params['total'], $params['anticipo'], $params['total'], date("Y-m-d", strtotime($params['fechaFactura'])), $idUsuarios, $idEmpresas);
					}
					//GUARDAR PARTIDA AUTOMATICA
					if ($params['idFormato'] !== '0') {
						$params['partida_at'] = date("Y-m-d", strtotime($params['fechaFactura']));
						$params['idTipoOperacionPartida'] = 2;
						$params['conceptoCompra'] = 'REF. FEL. ' . $response['Serie'] . '-' . $response['Numero'];
						$params['idEmpresas'] = $idEmpresas;
						$params['idUsuarios'] = $idUsuarios;
						$params['idVenta'] = $idVenta;
						$this->savePartidaAutomatica($params);
					}
					//RESPONSE
					$responseF[] = array('message' => 'success', 'idVenta' => $idVenta);
				} else {
					$responseF[] = array('message' => 'failed step 2', 'query' => $sql2);
					return $responseF;
				}
			} else {
				//echo mysql_error();
				//echo $sql;
				$responseF[] = array('message' => 'failed step 1', 'query' => $sql);
				//return $responseF;
			}
		} else {
			$responseF[] = array('message' => $response);
			//echo $xml_data;
		}
		return $responseF;
	}

	/** METODO ANULAR FACTURA FEL CAMARA
	 *
	 */
	public function anulacionFacturaCamara($params, $idEmpresas, $dbProject)
	{
		$responseF = "";
		//OBTENER DATOS DE EMPRESA SEGUN EMPRESA
		$sqlE = "select * from empresas where id=" . $idEmpresas . ";";
		$resE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_array($resE);
		//
		$sqlF = "SELECT
                    *,(select tipoFacturacion from empresas where id=ventas.idEmpresas) as tipoFacturacion,
                    concat(fechaFactura,'T',time(created_at),'-06:00') as fechaEmision
                FROM
                    ventas
                where
                    id=" . $params['idFactura'] . ";";
		$resF = mysql_query($sqlF, dbCon::conPrincipal());
		$regF = mysql_fetch_array($resF);
		if ($regF['tipoTransaccion'] === '1') {
			$URL = "https://ws.ccgfel.gt/Api/AnularDte";
			//
			$xml_data = '<?xml version="1.0" encoding="utf-8"?>
                        <dte:GTAnulacionDocumento xmlns:dte="http://www.sat.gob.gt/dte/fel/0.1.0" Version="0.1">
                          <dte:SAT>
                            <dte:AnulacionDTE ID="DatosCertificados">
                              <dte:DatosGenerales ID="DatosAnulacion" NumeroDocumentoAAnular="' . $regF['autorizacionFEL'] . '" NITEmisor="' . $regE['nit'] . '" IDReceptor="' . strtoupper(str_replace('-', '', $regF['nit'])) . '" FechaEmisionDocumentoAnular="' . $regF['fechaEmision'] . '" FechaHoraAnulacion="' . $this->date . 'T' . $this->time . '-06:00" MotivoAnulacion="' . $params['motivoAnulacion'] . '" />
                            </dte:AnulacionDTE>
                          </dte:SAT>
                        </dte:GTAnulacionDocumento>';
			header('Content-Type: application/json');
			$post = array('xmlDte' => base64_encode($xml_data), 'Referencia' => $this->timestamp);
			$ch = curl_init($URL);
			curl_setopt($ch, CURLOPT_MUTE, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $regE['token']));
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$response = json_decode($output, true);
			if ($response['UUID']) {
				//paso 1 anular factura
				$sql1 = "update ventas "
					. "set anulacion=1, "
					. "valorFactura='0.00',"
					. "subtotal='0.00',"
					. "descuento='0.00',"
					. "descuentoP='0.00',"
					. "total='0.00',"
					. "iva='0.00',"
					. "totalDolares='0.00',"
					. "totalEnLetras='CERO- ANULADA',"
					. "idAdminUser=" . $params['idAdminUser'] . ", "
					. "motivoAnulacion='" . $params['motivoAnulacion'] . "', "
					. "anulacion_at='" . $this->timestamp . "'"
					. " where id=" . $params['idFactura'] . ";";
				$query1 = mysql_query($sql1, dbCon::conPrincipal());
				if ($query1 == true) {
					$sql2 = "update ventasDetalle set precio='0.00', costo='0.00', total='0.00', totalCosto='0.00'
                             where idVentas=" . $params['idFactura'] . ";";
					$query2 = mysql_query($sql1, dbCon::conPrincipal());
					if ($query2 == true) {
						//paso 2 ingresar los productos al inventario
						$this->movimientoInventario($params['idFactura'], 'INGRESO POR ANULACION DE FAC ' . $regF['serie'] . '-' . $regF['correlativo'], 'ingreso', $this->timestamp);
						//paso 3 habilitar pedido gestionado
						$this->updateEstadoPedido('', $params['idFactura'], '1');
						$this->eliminarFacCXC($params['idFactura']);
						$this->liberarVales($params['idFactura']);
					} else {
						$responseF[] = array('message' => 'failed step 2', 'Query' => $sql2);
					}
					//
					$responseF[] = array('message' => 'success');
				} else {
					print_r($response);
					$responseF[] = array('message' => 'failed step 1', 'Query' => $sql1);
				}
			} else {
				$responseF[] = array('message' => 'failed step fel', 'xml' => $xml_data);
			}
		} else {
			//paso 1 anular factura
			$sql1 = "update ventas "
				. "set anulacion=1, "
				. "valorFactura='0.00',"
				. "subtotal='0.00',"
				. "descuento='0.00',"
				. "descuentoP='0.00',"
				. "total='0.00',"
				. "iva='0.00',"
				. "totalDolares='0.00',"
				. "totalEnLetras='CERO- ANULADA',"
				. "idAdminUser=" . $params['idAdminUser'] . ", "
				. "motivoAnulacion='" . $params['motivoAnulacion'] . "', "
				. "anulacion_at='" . $this->timestamp . "'"
				. "where id=" . $params['idFactura'] . ";";
			$query1 = mysql_query($sql1, dbCon::conPrincipal());
			if ($query1 == true) {
				$sql2 = "update ventasDetalle set precio='0.00', costo='0.00', total='0.00', totalCosto='0.00'
                         where idVentas=" . $params['idFactura'] . ";";
				$query2 = mysql_query($sql1, dbCon::conPrincipal());
				if ($query2 == true) {
					//paso 2 ingresar los productos al inventario
					$this->movimientoInventario($params['idFactura'], 'INGRESO POR ANULACION DE FAC ' . $regF['serie'] . '-' . $regF['correlativo'], 'ingreso', $this->timestamp);
					//paso 3 habilitar pedido gestionado
					$this->updateEstadoPedido('', $params['idFactura'], '1');
					$this->eliminarFacCXC($params['idFactura']);
					$this->liberarVales($params['idFactura']);
					//
					$responseF[] = array('message' => 'success');
				} else {
					$responseF[] = array('message' => 'failed step 2', 'Query' => $sql2);
				}
			} else {
				$responseF[] = array('message' => 'failed step 1', 'Query' => $sql1);
			}
		}
		return $responseF;
	}

	/** METODO liquidarVales
	 *
	 */
	public function liquidarValesInfile($params)
	{
		$response = "";
		//EMITE FACTURA
		//CONSULTA DATOS EMPRESA
		$sqlE = "select *,lpad(nit,12,0) as nitWS from empresas where id=" . $params['idEmpresas'] . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		//
		//EMITE FEL
		$responseF = "";
		//SCRIPT EMISION DE FACTURA
		//URL DESARROLLO
		$URL = "https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml";
		$UsuarioAPI = $regE['usuarioAPI'];
		$LlaveAPI = $regE['llaveAPI'];
		$UsuarioFirma = $regE['usuarioFirma'];
		$LlaveFirma = $regE['llaveFirma'];
		date_default_timezone_set("America/Guatemala");
		//
		$tipoDocumento = "FACT";
		$fechaEmision = $params['fechaFactura'];
		$horaEmision = $this->time;
		$codigoMoneda = "GTQ";
		//DATOS DEL VENDEDOR
		$nitEmisor = $regE['nit'];
		$nombreEmisor = $regE['razonSocial'];
		$codEstablecimiento = $regE['codigoEstablecimiento'];
		$nombreComercial = $regE['nombreComercial'];
		$afiliacionIVA = $regE['tipoAfiliacion'];
		$direccionEmisor = $regE['direccion'];
		$codigoPostal = "0100";
		$municipio = "GUATEMALA";
		$depto = "GUATEMALA";
		$pais = "GT";
		//DATOS DEL COMPRADOR
		$nombreComprador = $params['nombre'];
		$nitComprador = $params['nit'];
		$direccion = $this->consultaNIT($params);
		$direccionComprador = ($direccion[0]['direccion'] ?: 'CIUDAD');
		$munComprador = "GUATEMALA";
		$deptoComprador = "GUATEMALA";
		$codigoPostalComprador = "01001";
		$paisComprador = "GT";
		//GENERACION DE XML
		$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
                        <dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.2.0">
                          <dte:SAT ClaseDocumento="dte">
                            <dte:DTE ID="DatosCertificados">
                              <dte:DatosEmision ID="DatosEmision">
                                <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $this->date . 'T' . $this->time . '-06:00" Tipo="FACT"></dte:DatosGenerales>
                                <dte:Emisor AfiliacionIVA="' . $regE['tipoAfiliacion'] . '" CodigoEstablecimiento="' . $regE['codigoEstablecimiento'] . '" CorreoEmisor="marriolaj@gmail.com" NITEmisor="' . $regE['nit'] . '" NombreComercial="' . $regE['nombreComercial'] . '" NombreEmisor="' . $regE['razonSocial'] . '">
                                  <dte:DireccionEmisor>
                                    <dte:Direccion>' . $regE['direccion'] . '</dte:Direccion>
                                    <dte:CodigoPostal>01009</dte:CodigoPostal>
                                    <dte:Municipio>' . $regE['municipio'] . '</dte:Municipio>
                                    <dte:Departamento>' . $regE['departamento'] . '</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionEmisor>
                                </dte:Emisor>
                                <dte:Receptor CorreoReceptor="" IDReceptor="' . str_replace('-', '', $params['nit']) . '" NombreReceptor="' . str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombre'])) . '">
                                  <dte:DireccionReceptor>
                                    <dte:Direccion>' . ($direccionComprador ?: 'CIUDAD') . '</dte:Direccion>
                                    <dte:CodigoPostal>0101</dte:CodigoPostal>
                                    <dte:Municipio>GUATEMALA</dte:Municipio>
                                    <dte:Departamento>GUATEMALA</dte:Departamento>
                                    <dte:Pais>GT</dte:Pais>
                                  </dte:DireccionReceptor>
                                </dte:Receptor>
                                <dte:Frases>
                                  <dte:Frase CodigoEscenario="' . $regE['codigoEscenario'] . '" TipoFrase="' . $regE['tipoFrase'] . '"></dte:Frase>';
		//valida si se aplica subsidio
		$flag = false;
		foreach ($params['valesLiquidar'] as $key => $value) {
			if ($value['idProductos'] === '8' || $value['idProductos'] === '7') {
				$flag = true;
			}
		}
		if ($flag === true && $regE['subsidio'] === '1') {
			$xml_data .= '<dte:Frase TipoFrase="9" CodigoEscenario="2"/>';
		}
		//end valida si se aplica subsidio
		$xml_data .= '</dte:Frases>
                                <dte:Items>';
		//DETALLE DE FACTURA
		$idp = 0;
		$iva = 0;
		$subtotal = 0;
		$total = 0;
		foreach ($params['valesLiquidar'] as $key => $value) {
			$idp += $value['idp'];
			$iva += $value['iva'];
			$subtotal += $value['subtotal'];
			$total += $value['total'];
			//DETALLE DE LA FACTURA
			$xml_data .= '<dte:Item NumeroLinea="' . ($key + 1) . '" BienOServicio="B">
                        <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                        <dte:UnidadMedida>GAL</dte:UnidadMedida>
                        <dte:Descripcion>' . $value['tipo'] . '</dte:Descripcion>
                        <dte:PrecioUnitario>' . round($value['precioUnitario'], 6) . '</dte:PrecioUnitario>
                        <dte:Precio>' . round($value['precio'], 6) . '</dte:Precio>
                        <dte:Descuento>0</dte:Descuento>';
			$xml_data .= '<dte:Impuestos>
                                <dte:Impuesto>
                                    <dte:NombreCorto>IVA</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                    <dte:MontoGravable>' . round($value['subtotal'], 6) . '</dte:MontoGravable>
                                    <dte:MontoImpuesto>' . round($value['iva'], 6) . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                                <dte:Impuesto>
                                    <dte:NombreCorto>PETROLEO</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>' . $value['codCombustible'] . '</dte:CodigoUnidadGravable>
                                    <dte:CantidadUnidadesGravables>' . $value['cantidad'] . '</dte:CantidadUnidadesGravables>
                                    <dte:MontoImpuesto>' . round($value['idp'], 6) . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                            </dte:Impuestos>';
			$xml_data .= '<dte:Total>' . round($value['total'], 3) . '</dte:Total>
                          </dte:Item>';
		}
		//TERMINA DETALLE DE LA FACTURA
		$xml_data .= '</dte:Items>';
		$xml_data .= '<dte:Totales>';
		$xml_data .= '<dte:TotalImpuestos>
                                        <dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . round($iva, 6) . '" />
                                        <dte:TotalImpuesto NombreCorto="PETROLEO" TotalMontoImpuesto="' . round($idp, 6) . '"/>
                                    </dte:TotalImpuestos>';
		$xml_data .= '<dte:GranTotal>' . round($total, 3) . '</dte:GranTotal>
                                </dte:Totales>
                               </dte:DTE>
                          </dte:SAT>
                        </dte:GTDocumento>';
		//            echo $xml_data;
		//            exit();
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('UsuarioAPI: ' . $UsuarioAPI . '', 'LlaveAPI: ' . $LlaveAPI . '', 'UsuarioFirma: ' . $UsuarioFirma . '', 'LlaveFirma: ' . $LlaveFirma . '', 'Identificador: ' . date('YmdHis') . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['uuid']) {
			// GUARDA VENTA
			$sql = "insert into ventas
            	(`id`,
				`serie`,
				`correlativo`,
				`fechaFactura`,
				`valorFactura`,
				`subtotal`,
				`descuento`,
				`descuentoP`,
				`total`,
				`anticipo`,
				`saldo`,
				`iva`,
				`tipoCambio`,
				`totalDolares`,
				`totalEnLetras`,
				`totalEnLetrasDolares`,
				`nit`,
				`nombre`,
				`direccion`,
				`idTipoOperacion`,
				`idTipoVenta`,
				`conceptoVenta`,
				`idUsuarios`,
				`idSucursales`,
				`idEmpresas`,
				`created_at`,
				`updated_at`,
				`idFormatos`,
				`idPartidas`,
				`statusCierre`,
				`anulacion`,
				`idAdminUser`,
				`motivoAnulacion`,
				`anulacion_at`,
				`idClientes`,
				`idVendedores`,
				`autorizacionFEL`,
				`fechaEmisionFEL`,
				`tipoTransaccion`,
				`idBombas`,
				`xml`,
				`numeroAcceso`,
				`tipoFel`)
                values(
                    null,
                    '" . $response['serie'] . "',
                    '" . $response['numero'] . "',
                    '" . $params['fechaFactura'] . "',
                    '" . $total . "',
                    '" . $subtotal . "',
                    '" . $params['descuentoM'] . "',
                    '" . $params['descuentoP'] . "',
                    '" . $total . "',
                    '" . $params['anticipo'] . "',
                    '" . $params['saldo'] . "',
                    '" . $iva . "',
                    '" . $params['tasaCambio'] . "',
                    '" . $params['totalDolares'] . "',
                    '" . $params['totalEnLetras'] . "',
                    null,
                    '" . $params['nit'] . "',
                    '" . $params['nombre'] . "',
                    '" . $direccion[0]['direccion'] . "',
                    '2',
                    '2',
                    '" . $params['observaciones'] . "',
                    '" . $params['idUsuarios'] . "',
                    '" . $params['idSucursales'] . "',
                    '" . $params['idEmpresas'] . "',
                    '" . $this->timestamp . "',
                    null,
                    0,
                    0,
                    0,
                    0,
                    null,
                    null,
                    null,
                    '" . $params['idClientes'] . "',
                    '" . $params['idUsuarios'] . "',
                    '" . $response['uuid'] . "',
                    '" . $response['fecha'] . "',
                    1,
                    0,
                    '" . $xml_data . "',
                    null,
                    'FACT');";
			$query = mysql_query($sql, dbCon::conPrincipal());
			//echo $query;
			if ($query) {
				//OBTIENE ID VENTA
				$idVenta = mysql_insert_id();
				//INGRESAR DETALLE DE VENTAS
				foreach ($params['valesLiquidar'] as $key => $value) {
					$sqlD = "insert into ventasDetalle values(null," . $idVenta . ",'Producto'," . $value['idProductos'] . ",'" . $value['idProductos'] . "','" . $value['tipo'] . "','" . $value['cantidad'] . "','" . $value['precioUnitario2'] . "','0','" . $value['costo'] . "','" . $value['total'] . "','0','" . $value['totalCosto'] . "','" . $params['idUsuarios'] . "','" . $params['idSucursales'] . "','" . $params['idEmpresas'] . "');";
					$queryD = mysql_query($sqlD, dbCon::conPrincipal());
				}
				//INGRESO DE VENTA AL CREDITO
				$documento = $response['Serie'] . '-' . $response['NUMERO'];
				$this->ventasCredito($params['nit'], $idVenta, $documento, $params['total'], $params['anticipo'], $params['saldo'], date("Y-m-d", strtotime($params['fechaFactura'])), $params['idUsuarios'], $params['idEmpresas']);
				//ELIMINAR DE LA TABLA CXC EL REGISTRO DE VALES
				$vales = explode(";", $params['vales']);
				foreach ($vales as $key => $value) {
					$sql1 = "delete from cxc where idDocumento='" . $value . "' and idEmpresas=" . $params['idEmpresas'] . ";";
					$query1 = mysql_query($sql1, dbCon::conPrincipal());
					if ($query1) {
						$sql2 = "update vales set estado=2, idVentas=" . $idVenta . " where concat(serie,'-',documento)='" . $value . "';";
						$query2 = mysql_query($sql2, dbCon::conPrincipal());
					}
				}
				//UPDATE NUMERO DE ACCESO
				//$sql7 = "update empresas set numeroAcceso=(" . $regE['numeroAcceso'] . ")+1 where id=" . $params['idEmpresas'] . ";";
				//$query7 = mysql_query($sql7, dbCon::conPrincipal());
				//
				$responseF[] = array('message' => 'success', 'idVenta' => $idVenta);
			} else {
				$error = mysql_error();
				$responseF[] = array('message' => 'failed step 1', 'Query' => $sql, 'error' => $error);
			}
		} else {
			//echo $xml_data;
			$responseF[] = array('message' => $response['descripcion_errores'][0]['mensaje_error']);
		}
		return $responseF;
	}

	/**
	 *
	 */
	public function generarNCFEL_INFILE($params)
	{
		$responseF = "";
		//CONSULTA DATOS EMPRESA
		$sqlE = "select * from empresas where id=" . $params['idEmpresas'] . ";";
		$queryE = mysql_query($sqlE, dbCon::conPrincipal());
		$regE = mysql_fetch_assoc($queryE);
		$responseF = "";
		$URL = "https://certificador.feel.com.gt/fel/procesounificado/transaccion/v2/xml";
		$UsuarioAPI = $regE['usuarioAPI'];
		$LlaveAPI = $regE['llaveAPI'];
		$UsuarioFirma = $regE['usuarioFirma'];
		$LlaveFirma = $regE['llaveFirma'];
		date_default_timezone_set("America/Guatemala");
		//XML
		//GENERACION DE XML
		$FechaHoraEmision = $params['fechaNC'] . "T" . $this->time;
		//DATOS DEL VENDEDOR
		$nitEmisor = $regE['nit'];
		$nombreEmisor = str_replace('"', '&quot;', str_replace('&', '&amp;', $regE['razonSocial']));
		$codEstablecimiento = $regE['codigoEstablecimiento'];
		$nombreComercial = $regE['nombreComercial'];
		$afiliacionIVA = $regE['tipoAfiliacion'];
		$direccionEmisor = $regE['direccion'];
		$codigoPostal = "0100";
		$municipio = $regE['municipio'];
		$depto = $regE['departamento'];
		$pais = "GT";
		//DATOS DEL COMPRADOR
		$nombreComprador = str_replace('"', '&quot;', str_replace('&', '&amp;', $params['nombreComprador']));
		$nitComprador = $params['nitComprador'];
		$direccionComprador = $params['direccionComprador'];
		$muniComprador = "GUATEMALA";
		$deptoComprador = "GUATEMALA";
		$codigoPostalComprador = "01001";
		$paisComprador = "GT";
		//DATOS DEL DOCUMENTO A APLICAR NOTA DE CREDITO
		$NumeroAutorizacionDocumentoOrigen = $params['autorizacionFEL'];
		$FechaEmisionDocumentoOrigen = $params['fechaFactura'];
		$MotivoAjuste = $params['motivoNC'];
		$NumeroDocumentoOrigen = $params['correlativo'];
		$SerieDocumentoOrigen = $params['serie'];
		//
		$xml_data = '<dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.2.0">
					  <dte:SAT ClaseDocumento="dte">
					    <dte:DTE ID="DatosCertificados">
					      <dte:DatosEmision ID="DatosEmision">
					        <dte:DatosGenerales CodigoMoneda="GTQ" FechaHoraEmision="' . $FechaHoraEmision . '" Tipo="NCRE"></dte:DatosGenerales>
					        <dte:Emisor AfiliacionIVA="' . $afiliacionIVA . '" CodigoEstablecimiento="' . $codEstablecimiento . '" CorreoEmisor="jonathan.juarez@grupocubix.com" NITEmisor="' . $nitEmisor . '" NombreComercial="' . $nombreComercial . '" NombreEmisor="' . $nombreEmisor . '">
					          <dte:DireccionEmisor>
	                                <dte:Direccion>' . $direccionEmisor . '</dte:Direccion>
	                                <dte:CodigoPostal>' . $codigoPostal . '</dte:CodigoPostal>
	                                <dte:Municipio>' . $municipio . '</dte:Municipio>
	                                <dte:Departamento>' . $depto . '</dte:Departamento>
	                                <dte:Pais>' . $pais . '</dte:Pais>
	                            </dte:DireccionEmisor>
					        </dte:Emisor>
					        <dte:Receptor NombreReceptor="' . $nombreComprador . '" IDReceptor="' . $nitComprador . '">
                                <dte:DireccionReceptor>
                                    <dte:Direccion>' . $direccionComprador . '</dte:Direccion>
                                    <dte:CodigoPostal>' . $codigoPostalComprador . '</dte:CodigoPostal>
                                    <dte:Municipio>' . $muniComprador . '</dte:Municipio>
                                    <dte:Departamento>' . $deptoComprador . '</dte:Departamento>
                                    <dte:Pais>' . $paisComprador . '</dte:Pais>
                                </dte:DireccionReceptor>
                            </dte:Receptor>
					        <dte:Items>';
		$totalIDP = 0;
		$totalIVA = 0;
		$totalVenta = 0;
		if ($params['tipoNC'] === '2' && $params['dbProject'] === 'erp_petromas') {
			if ($params['iva'] > $params['ivaDoc']) {
				foreach ($this->getProductosVenta($params) as $key => $value) {
					//CALCULO IDP
					$precioUnitario = 0;
					$precio = 0;
					$subtotal = 0;
					$iva = 0;
					$idp = 0;
					$total = 0;
					$combustible = "";
					$codCombustible = 0;
					switch ($value['codigo']) {
						case '9':
							$codCombustible = 1;
							$combustible = "SUPER";
							if ($params['descuentoP'] === '0') {
								$precioUnitario = round(($value['precio'] - 4.70), 5);
							} else {
								$precioUnitario = round(((($value['totalDescuento']) / $value['cantidad']) - 4.70), 5);
							}
							$precio = ($value['cantidad'] * $precioUnitario);
							$subtotal = round(($precio / 1.12), 2);
							$iva = round(($subtotal * 0.12), 6);
							$idp = round(($value['cantidad'] * 4.70), 4);
							$total = ($precio + $idp);
							break;
						case '8':
							$codCombustible = 2;
							$combustible = "REGULAR";
							if ($params['descuentoP'] === '0') {
								$precioUnitario = round(($value['precio'] - 4.60), 5);
							} else {
								$precioUnitario = round(((($value['totalDescuento']) / $value['cantidad']) - 4.60), 5);
							}
							$precio = ($value['cantidad'] * $precioUnitario);
							$subtotal = round(($precio / 1.12), 2);
							$iva = round(($subtotal * 0.12), 6);
							$idp = round(($value['cantidad'] * 4.60), 4);
							$total = ($precio + $idp);
							break;
						case '7':
							$codCombustible = 4;
							$combustible = "DIESEL";
							if ($params['descuentoP'] === '0') {
								$precioUnitario = round(($value['precio'] - 1.30), 5);
							} else {
								$precioUnitario = round(((($value['totalDescuento']) / $value['cantidad']) - 1.30), 5);
							}
							$precio = ($value['cantidad'] * $precioUnitario);
							$subtotal = round(($precio / 1.12), 2);
							$iva = round(($subtotal * 0.12), 6);
							$idp = round(($value['cantidad'] * 1.30), 4);
							$total = ($precio + $idp);
							break;
					}
					$totalIDP += $idp;
					$totalIVA += $iva;
					$totalVenta += $total;
					//
					$xml_data .= '<dte:Item BienOServicio="B" NumeroLinea="' . ($key + 1) . '">
                            <dte:Cantidad>' . $value['cantidad'] . '</dte:Cantidad>
                            <dte:UnidadMedida>GLS</dte:UnidadMedida>
                            <dte:Descripcion>' . $combustible . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $precioUnitario . '</dte:PrecioUnitario>
                            <dte:Precio>' . $precio . '</dte:Precio>
                            <dte:Descuento>0.00</dte:Descuento>
                            <dte:Impuestos>
                              <dte:Impuesto>
                                <dte:NombreCorto>IVA</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                <dte:MontoGravable>' . $subtotal . '</dte:MontoGravable>
                                <dte:MontoImpuesto>' . $iva . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                              <dte:Impuesto>
                                <dte:NombreCorto>PETROLEO</dte:NombreCorto>
                                <dte:CodigoUnidadGravable>' . $codCombustible . '</dte:CodigoUnidadGravable>
                                <dte:CantidadUnidadesGravables>' . $value['cantidad'] . '</dte:CantidadUnidadesGravables>
                                <dte:MontoImpuesto>' . round($idp, 4) . '</dte:MontoImpuesto>
                              </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . $total . '</dte:Total>
                          </dte:Item>';
				}
			} else {
				$xml_data .= '<dte:Item NumeroLinea="1" BienOServicio="B">
                            <dte:Cantidad>1.0000</dte:Cantidad>
                            <dte:UnidadMedida>UND</dte:UnidadMedida>
                            <dte:Descripcion>' . $MotivoAjuste . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $params['montoNC'] . '</dte:PrecioUnitario>
                            <dte:Precio>' . $params['montoNC'] . '</dte:Precio>
                            <dte:Descuento>0</dte:Descuento>
                            <dte:Impuestos>
                                <dte:Impuesto>
                                    <dte:NombreCorto>IVA</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                    <dte:MontoGravable>' . round($params['subtotal'], 2) . '</dte:MontoGravable>
                                    <dte:MontoImpuesto>' . round($params['iva'], 2) . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . $params['montoNC'] . '</dte:Total>
                        </dte:Item>';
			}
		} else {
			$xml_data .= '<dte:Item NumeroLinea="1" BienOServicio="B">
                            <dte:Cantidad>1.0000</dte:Cantidad>
                            <dte:UnidadMedida>CA</dte:UnidadMedida>
                            <dte:Descripcion>' . $MotivoAjuste . '</dte:Descripcion>
                            <dte:PrecioUnitario>' . $params['montoNC'] . '</dte:PrecioUnitario>
                            <dte:Precio>' . $params['montoNC'] . '</dte:Precio>
                            <dte:Descuento>0</dte:Descuento>
                            <dte:Impuestos>
                                <dte:Impuesto>
                                    <dte:NombreCorto>IVA</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                    <dte:MontoGravable>' . round($params['subtotal'], 2) . '</dte:MontoGravable>
                                    <dte:MontoImpuesto>' . round($params['iva'], 6) . '</dte:MontoImpuesto>
                                </dte:Impuesto>
                            </dte:Impuestos>
                            <dte:Total>' . $params['montoNC'] . '</dte:Total>
                        </dte:Item>';
		}
		$xml_data .= '</dte:Items>
                        <dte:Totales>
                        <dte:TotalImpuestos>';
		if ($params['dbProject'] === 'erp_petromas') {
			if ($params['iva'] > $params['ivaDoc']) {
				$xml_data .= '<dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . $params['ivaDoc'] . '"/>';
				$xml_data .= '<dte:TotalImpuesto NombreCorto="PETROLEO" TotalMontoImpuesto="' . round($totalIDP, 3) . '"/>';
				$xml_data .= '</dte:TotalImpuestos><dte:GranTotal>' . $params['montoNC'] . '</dte:GranTotal></dte:Totales>';
			} else {
				$xml_data .= '<dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . round($params['iva'], 6) . '"/>';
				$xml_data .= '</dte:TotalImpuestos>
                        <dte:GranTotal>' . round($params['montoNC'], 2) . '</dte:GranTotal>
                      </dte:Totales>';
			}
		} else {
			$xml_data .= '<dte:TotalImpuesto NombreCorto="IVA" TotalMontoImpuesto="' . round($params['iva'], 6) . '"/>';
			$xml_data .= '</dte:TotalImpuestos>
                        <dte:GranTotal>' . round($params['montoNC'], 2) . '</dte:GranTotal>
                      </dte:Totales>';
		}
		$xml_data .= '<dte:Complementos>
					          <dte:Complemento IDComplemento="Notas" NombreComplemento="Notas" URIComplemento="http://www.sat.gob.gt/fel/notas.xsd">
					            <cno:ReferenciasNota xmlns:cno="http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0" FechaEmisionDocumentoOrigen="' . $FechaEmisionDocumentoOrigen . '" MotivoAjuste="' . $MotivoAjuste . '"
					            NumeroAutorizacionDocumentoOrigen="' . $NumeroAutorizacionDocumentoOrigen . '" NumeroDocumentoOrigen="' . $NumeroDocumentoOrigen . '" SerieDocumentoOrigen="' . $SerieDocumentoOrigen . '"
					            Version="0.0" xsi:schemaLocation="http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0 C:\Users\User\Desktop\FEL\Esquemas\GT_Complemento_Referencia_Nota-0.1.0.xsd"></cno:ReferenciasNota>
					          </dte:Complemento>
					        </dte:Complementos>
					      </dte:DatosEmision>
					    </dte:DTE>
					  </dte:SAT>
					</dte:GTDocumento>';
		//END XML
		// echo $xml_data;
		// exit();
		$ch = curl_init($URL);
		curl_setopt($ch, CURLOPT_MUTE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('UsuarioAPI: ' . $UsuarioAPI . '', 'LlaveAPI: ' . $LlaveAPI . '', 'UsuarioFirma: ' . $UsuarioFirma . '', 'LlaveFirma: ' . $LlaveFirma . '', 'Identificador: ' . date('YmdHis') . ''));
		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($output, true);
		if ($response['uuid']) {
			//ACTUALIZACION DE SALDO DE FACTURAS
			$sql2 = "update ventas set total=(total-" . $params['montoNC'] . "), updated_at='" . $this->timestamp . "'"
				. "where id='" . $params['idVenta'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			if ($query2 == true) {
				//INSERTA EN CXC PARA GUARDAR REGISTRO EN ESTADO DE CUENTA
				$sql3 = "insert into cxc(idClientes,idVentas,idTipoDocumento,idDocumento,facLiquidadas,creditos,debitos,saldo,created_at,idUsuarios,idEmpresas,observaciones,autorizacionFEL,fechaEmisionFEL)"
					. "values('" . $params['nitComprador'] . "'," . $params['idVenta'] . ",'6','" . $response['serie'] . '-' . $response['numero'] . "','" . $params['serie'] . '-' . $params['correlativo'] . "','0.00'," . $params['montoNC'] . "," . ($params['saldoFactura'] - $params['montoNC']) . ",'" . $this->timestamp . "'," . $params['idUsuarios'] . "," . $params['idEmpresas'] . ",'" . $params['motivoNC'] . "','" . $response['uuid'] . "','" . $response['fecha'] . "');";
				$query3 = mysql_query($sql3, dbCon::conPrincipal());
				$idNC = mysql_insert_id();
				if ($query3) {
					$responseF[] = array('message' => 'success', 'idNC' => $idNC, 'autorizacion' => $response['uuid'], 'serie' => $response['serie'], 'numero' => $response['numero'], 'fechaEmision' => $response['fecha']);
				} else {
					$responseF[] = array('message' => 'error actualizar cxc', 'query' => $sql3, 'error' => mysql_error());
				}
			} else {
				$responseF[] = array('message' => 'error actualizar ventas', 'query' => $sql2, 'error' => mysql_error());
			}
		} else {
			$responseF[] = array('message' => 'failed step FEL', 'error' => $response['descripcion_errores'][0]['mensaje_error'], 'xml' => $xml_data);
			//echo $xml_data;
		}
		return $responseF;
	}
}
