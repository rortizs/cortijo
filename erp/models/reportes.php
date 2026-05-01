<?php

/**
 * Description of reportes
 *
 * @author Richard Sasvin
 */
require_once "dbCon.php";
require_once "general.php";

class Reportes extends General
{

	/** METODO KARDEX
	 *
	 */
	public function kardex($params, $idEmpresas)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['ingresoA'] != "" && $params['idPuntoIngreso'] != "") {
			$filtros .= " and a.ingresoA = " . $params['ingresoA'] . " and a.idPuntoIngreso = " . $params['idPuntoIngreso'] . "";
		}
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and date(a.created_at) between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['codigo'] != "") {
			$filtros .= " and b.sku='" . $params['codigo'] . "'";
		}
		if ($params['idTipoProductos'] != "") {
			$filtros .= " and b.idTipoProductos=" . $params['idTipoProductos'] . "";
		}
		$order = "order by a.id desc";
		if ($params['tipoOrdenamiento'] === 'producto') {
			$order = "order by b.descLarga asc,a.id desc";
		}
		//
		$sql = "SELECT
                    a.idProductos,
                    DATE_FORMAT(a.created_at, '%d-%m-%Y') AS created_at,
                    a.documento,
                    CONCAT(b.sku, ' - ', b.descLarga) AS sku,
                    a.ingreso,
                    a.salida,
                    a.saldo,
                    c.descripcion AS unidadMedida,
                    a.serie,
                    coalesce(d.descripcion,e.observaciones) as observaciones,
                    if(ingreso='0.00','warning','success') as class,
                    if(a.ingresoA=1,'BODEGA','SUCURSAL') as ingresoA,
                    if(a.ingresoA=1,bd.descripcion,sc.descripcion) as idPuntoIngreso
                FROM
                    inventarios AS a
                        INNER JOIN
                    productos AS b ON (a.idProductos = b.id)
                        LEFT JOIN
                    medidas AS c ON (b.idMedidas2 = c.id)
                        LEFT JOIN
                    ajustes AS d ON (a.documento = d.documento)
                        LEFT JOIN
                    traslados AS e ON (a.documento = e.documento)
			LEFT JOIN
                    bodegas AS bd ON (a.idPuntoIngreso = bd.id)
			LEFT JOIN
                    sucursales AS sc ON (a.idPuntoIngreso = sc.id)
                WHERE
                    a.idEmpresas=" . $idEmpresas . " " . $filtros . "
                group by a.id
                " . $order . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CONSULTA FACTURAS
	 *
	 */
	public function consultaFacturas($params)
	{
		$this->resultado = null;
		$filtros = "";
		$idVenta = "";
		$serie = "";
		$correlativo = "";
		$nit = "";
		$cajero = "";
		$vendedor = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and fechaFactura between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['serie'] != "") {
			$filtros .= " and a.serie='" . $params['serie'] . "'";
		}
		if ($params['correlativo'] != "") {
			$filtros .= " and a.correlativo='" . $params['correlativo'] . "'";
		}
		if ($params['nit'] != "") {
			$filtros .= " and a.nit='" . $params['nit'] . "'";
		}
		if ($params['idSucursales'] != "") {
			$filtros .= " and a.idSucursales=" . $params['idSucursales'] . "";
		}
		if ($params['idVendedor'] != "") {
			$vendedor = $params['idVendedor'];
			$filtros .= " and a.idVendedores=" . $vendedor . "";
		}
		if ($params['tipoVenta'] != "") {
			$filtros .= " and a.idTipoVenta=" . $params['tipoVenta'] . "";
		}
		if ($params['estatus'] != "") {
			$filtros .= " and a.anulacion=" . $params['estatus'] . "";
		}
		if ($params['cliente'] != "") {
			$cliente = str_replace(" ", "%", $params['cliente']);
			$filtros .= " and a.nombre like '%" . $cliente . "%'";
		}
		$sql = "SELECT
                    a.id as idVenta,
                    date_format(a.fechaFactura,'%d-%m-%Y') as fechaFactura,
                    if(a.idTipoVenta=1,'CONTADO','CREDITO') as tipoVenta,
                    a.serie,
                    a.correlativo,
                    a.nit,
                    a.nombre as nombreCliente,
                    b.descripcion as sucursal,
                    c.userName as vendedor,
                    if(a.anulacion,'ANULADA','ACTIVA') as estatus,
                    ifnull(date_format(a.anulacion_at,'%d-%m-%Y'),'-') as fechaAnulacion,
                    a.anticipo,
                    a.saldo,
                    (a.total-a.iva) as subtotal,
                    a.iva,
                    a.total,
                    a.totalDolares,
                    a.idSucursales,
                    cj.userName as cajero,
                    a.autorizacionFEL,
                    a.fechaEmisionFEL,
                    a.conceptoVenta,
                    if(autorizacionFEL is null,'RECIBO DE VENTA','FACTURA ELECTRONICA') as tipoTransaccion
                FROM
                    ventas as a
                    left join sucursales as b on(a.idSucursales=b.id)
                    left join usuarios as c on(if(a.idVendedores=0,a.idUsuarios,a.idVendedores)=c.id)
                    LEFT JOIN usuarios as cj on(a.idUsuarios=cj.id)
                where
                    a.idEmpresas=" . $params['idEmpresas'] . " " . $filtros . "
                group by
                    concat(a.serie,'-',a.correlativo)
                order by serie,cast(correlativo as unsigned) asc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** Metodo obtiene las facturas del mes pasado
	 *
	 */
	public function consultaFacturasMes($params)
	{
		$this->resultado = null;
		$filtros = "";
		$idVenta = "";
		$serie = "";
		$correlativo = "";
		$nit = "";
		$cajero = "";
		$vendedor = "";

		//obtener el primer dia y el ultimo dia del mes pasado del anio actual
		$fechaInicio = date('Y-m-01', strtotime('-1 month'));
		$fechaFin = date('Y-m-t', strtotime('-1 month'));


		//aplicar filtros
		if ($params['serie'] != "") {
			$filtros .= " and a.serie='" . $params['serie'] . "'";
		}
		if ($params['correlativo'] != "") {
			$filtros .= " and a.correlativo='" . $params['correlativo'] . "'";
		}
		if ($params['nit'] != "") {
			$filtros .= " and a.nit='" . $params['nit'] . "'";
		}
		if ($params['idSucursales'] != "") {
			$filtros .= " and a.idSucursales=" . $params['idSucursales'] . "";
		}
		if ($params['idVendedor'] != "") {
			$vendedor = $params['idVendedor'];
			$filtros .= " and a.idVendedores=" . $vendedor . "";
		}
		if ($params['tipoVenta'] != "") {
			$filtros .= " and a.idTipoVenta=" . $params['tipoVenta'] . "";
		}
		if ($params['estatus'] != "") {
			$filtros .= " and a.anulacion=" . $params['estatus'] . "";
		}
		if ($params['cliente'] != "") {
			$cliente = str_replace(" ", "%", $params['cliente']);
			$filtros .= " and a.nombre like '%" . $cliente . "%'";
		}

		$sql = "SELECT
                date_format(a.fechaFactura,'%d-%m-%Y') as fechaFactura,
                concat(a.serie,'-',a.correlativo) as documento,
                a.total as totalDocumento,
                if(d.sku='',e.sku,d.sku) as sku,
                coalesce(d.descLarga,e.descLarga) as descLarga,
                d.cantidad,
                d.precio,
                d.costo,
                if(a.anulacion=1,0,d.total) as total,
                d.totalCosto,
                a.nombre as cliente,
                c.userName as vendedor,
                (if(a.anulacion=1,0,d.total)-d.totalCosto) as utilidad,
                round((if(a.anulacion=1,0,d.total)-d.totalCosto)/if(a.anulacion=1,0,d.total)*100,2) as margen
            FROM
                ventas as a
                left join sucursales as b on(a.idSucursales=b.id)
                left join usuarios as c on(if(a.idVendedores=0,a.idUsuarios,a.idVendedores)=c.id)
                left join ventasDetalle as d on(a.id=d.idVentas)
                left join productos as e on(d.idProductos=e.id)
            WHERE
                a.idEmpresas=:idEmpresas " . $filtros . " AND a.anulacion!=1 AND d.cantidad!=0
            ORDER BY serie,cast(correlativo as unsigned) asc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CONSULTA FACTURAS DETALLADO
	 *
	 */
	public function consultarFacturasDetallado($params)
	{
		$this->resultado = null;
		$filtros = "";
		$idVenta = "";
		$serie = "";
		$correlativo = "";
		$nit = "";
		$cajero = "";
		$vendedor = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and fechaFactura between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['serie'] != "") {
			$filtros .= " and a.serie='" . $params['serie'] . "'";
		}
		if ($params['correlativo'] != "") {
			$filtros .= " and a.correlativo='" . $params['correlativo'] . "'";
		}
		if ($params['nit'] != "") {
			$filtros .= " and a.nit='" . $params['nit'] . "'";
		}
		if ($params['idSucursales'] != "") {
			$filtros .= " and a.idSucursales=" . $params['idSucursales'] . "";
		}
		if ($params['idVendedor'] != "") {
			$vendedor = $params['idVendedor'];
			$filtros .= " and a.idVendedores=" . $vendedor . "";
		}
		if ($params['tipoVenta'] != "") {
			$filtros .= " and a.idTipoVenta=" . $params['tipoVenta'] . "";
		}
		if ($params['estatus'] != "") {
			$filtros .= " and a.anulacion=" . $params['estatus'] . "";
		}
		if ($params['cliente'] != "") {
			$cliente = str_replace(" ", "%", $params['cliente']);
			$filtros .= " and a.nombre like '%" . $cliente . "%'";
		}
		$sql = "SELECT
                    date_format(a.fechaFactura,'%d-%m-%Y') as fechaFactura,
                    concat(a.serie,'-',a.correlativo) as documento,
                    a.total as totalDocumento,
                    if(d.sku='',e.sku,d.sku) as sku,
                    coalesce(d.descLarga,e.descLarga) as descLarga,
                    d.cantidad,
                    d.precio,
                    d.costo,
                    if(a.anulacion=1,0,d.total) as total,
                    d.totalCosto,
                    a.nombre as cliente,
                    c.userName as vendedor,
                    (if(a.anulacion=1,0,d.total)-d.totalCosto) as utilidad,
                    round((if(a.anulacion=1,0,d.total)-d.totalCosto)/if(a.anulacion=1,0,d.total)*100,2) as margen
                FROM
                    ventas as a
                    left join sucursales as b on(a.idSucursales=b.id)
                    left join usuarios as c on(if(a.idVendedores=0,a.idUsuarios,a.idVendedores)=c.id)
                    left join ventasDetalle as d on(a.id=d.idVentas)
                    left join productos as e on(d.idProductos=e.id)
                where
                    a.idEmpresas=" . $params['idEmpresas'] . " " . $filtros . " and a.anulacion!=1 and d.cantidad!=0
                order by serie,cast(correlativo as unsigned) asc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}


	//
	public function consultaFacturasLaxTravel($params)
	{
		$this->resultado = null;
		$filtros = "";
		$idVenta = "";
		$documento = "";
		$nit = "";
		$cajero = "";
		$vendedor = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " fechaFactura between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['tipoFacturacion'] != "") {
			$tipoFacturacion = $params['tipoFacturacion'];
			$filtros .= " and valTipoFacturacion=" . $tipoFacturacion . "";
		}
		if ($params['documento'] != "") {
			$documento = $params['documento'];
			$filtros .= "and documento='" . $documento . "'";
		}
		if ($params['pagare'] != "") {
			$pagare = $params['pagare'];
			$filtros .= " and pagare='" . $pagare . "'";
		}
		if ($params['vendedor'] != "") {
			$vendedor = $params['vendedor'];
			$filtros .= " and codVendedor=" . $vendedor . "";
		}
		if ($params['tipoVenta'] != "") {
			if ($params['tipoVenta'] == 1) {
				$tipoVenta = "'Contado'";
			} else {
				$tipoVenta = "'Credito'";
			}
			$filtros .= " and tipoVenta=" . $tipoVenta . "";
		}
		if ($params['cliente'] != "") {
			$cliente = $params['cliente'];
			$filtros .= " and codigoCliente= " . $cliente . "";
		}
		$sql = "SELECT * FROM
                   vw_facturacion
                where
                    " . $filtros . " and idEmpresas=" . $params['idEmpresas'] . ""
			. " order by idVenta;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	//
	public function consultarBoletos($params)
	{
		$this->resultado = null;
		$filtros = "";
		$idVenta = "";
		$documento = "";
		$nit = "";
		$cajero = "";
		$vendedor = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and fecha2 between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['noPagare'] != "") {
			$noPagare = $params['noPagare'];
			$filtros .= " and pagare like '%" . $noPagare . "%'";
		}
		if ($params['noBoleto'] != "") {
			$noBoleto = $params['noBoleto'];
			$filtros .= " and boleto like '%" . $noBoleto . "%'";
		}
		if ($params['reserva'] != "") {
			$reserva = $params['reserva'];
			$filtros .= " and reserva like '%" . $reserva . "'%";
		}
		if ($params['proveedor'] != "") {
			$proveedor = str_replace(" ", "%", $params['proveedor']);
			$filtros .= " and proveedor like '%" . $proveedor . "%'";
		}
		if ($params['vendedor'] != "") {
			$filtros .= " and codVendedor like'%" . $params['vendedor'] . "%'";
		}
		if ($params['cliente'] != "") {
			$cliente = $params['cliente'];
			$filtros .= " and codCliente like '%" . $cliente . "%'";
		}
		if ($params['pasajero'] != "") {
			$pasajero = str_replace(" ", "%", $params['pasajero']);
			$filtros .= " and pasajero like '%" . $pasajero . "%'";
		}
		if ($params['lineaAerea'] != "") {
			$lineaAerea = $params['lineaAerea'];
			$filtros .= " and codigoLineaAerea like '%" . $lineaAerea . "%'";
		}
		if ($params['tipoPago'] == 1) {
			$filtros .= " and tarjeta not in('');";
		}
		if ($params['tipoPago'] == 2) {
			$filtros .= " and tarjeta in('');";
		}
		$sql = "SELECT
                   * FROM vw_boletos
                where
                    codAgencia=21 " . $filtros . ""
			. ";";
		//and a.idEmpresas=" . $params['idEmpresas'] . "
		// echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO DETALLE PRODUCTOS FACTURA
	 *
	 */
	public function detalleProductosFactura($param)
	{
		$this->resultado = null;
		$sql = "SELECT
                    a.idProductos,
                    b.sku AS 'sku',
                    b.descLarga,
                    a.cantidad,
                    a.precio,
                    a.total
                FROM
                    ventasDetalle AS a left JOIN
                    productos AS b ON (a.idProductos = b.id)
                WHERE
                    a.idVentas =" . $param['idVenta'] . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO VENTAS POR PRODUCTO
	 *
	 */
	public function ventasPorProducto($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['idSucursales'] != "") {
			$filtros .= " and a.idSucursales='" . $params['idSucursales'] . "'";
		}
		if ($params['idFamiliaNivel1'] != "") {
			$filtros .= " and c.idFamiliaNivel1=" . $params['idFamiliaNivel1'] . "";
		}
		if ($params['idFamiliaNivel2'] != "") {
			$filtros .= " and c.idFamiliaNivel2=" . $params['idFamiliaNivel2'] . "";
		}
		if ($params['idFamiliaNivel3'] != "") {
			$filtros .= " and c.idFamiliaNivel3=" . $params['idFamiliaNivel3'] . "";
		}
		if ($params['codigo'] != "") {
			$filtros .= " and c.sku='" . $params['codigo'] . "'";
		}
		if ($params['idVendedores'] != "") {
			$filtros .= " and b.idVendedores=" . $params['idVendedores'] . "";
		}
		$sql = "select
                    c.sku as codigo,
                    c.descLarga as descripcion,
                    sum(a.cantidad) as cantidad,
                    sum(a.total) as totalVenta,
                    sum(a.totalCosto) as totalCosto,
                    sum(a.total)-sum(a.totalCosto) as utilidad,
                    round((1-(sum(a.totalCosto)/sum(a.total)))*100,0) as margen,
                    d.userName as vendedor
                from
                    ventasDetalle as a
                        inner join
                    ventas as b on(a.idVentas=b.id)
                        left join
                    productos as c on(a.idProductos=c.id)
                        left join
                    usuarios as d on(b.idVendedores=d.id)
                where
                    b.idEmpresas=" . $params['idEmpresas'] . " and
                    anulacion=0 and
                    date(b.fechaFactura) between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "' " . $filtros . "
                group by a.idProductos;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO VENTAS POR VENDEDOR
	 *
	 */
	public function ventasPorVendedor($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= "and DATE_FORMAT(v.created_at,'%Y-%m-%d') between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['idVendedor'] != "") {
			$idVendedor = $params['idVendedor'];
			$filtros .= "and v.idVendedores='" . $idVendedor . "'";
		}
		$sql = "select
                count(*) as cantidad,v.total,v.subtotal,u.userName from ventas as v inner join usuarios as u on u.id=v.idVendedores
                where
                    v.idEmpresas=" . $params['idEmpresas'] . " "
			. $filtros . " group by v.idVendedores";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO VENTAS POR FAMILIA
	 *
	 */
	public function ventasPorFamilia($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= "and v.created_at between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "' ";
		}
		if ($params['idFamilia'] != "") {
			$idFamilia = $params['idFamilia'];
			$filtros .= "and f.id='" . $idFamilia . "' ";
		}
		if ($params['idVendedor'] != "") {
			$idVendedor = $params['idVendedor'];
			$filtros .= "and v.idVendedores='" . $idVendedor . "' ";
		}
		$sql = "select f.id,v.total,f.descripcion,u.userName from ventas as v
		inner join usuarios as u on u.id=v.idVendedores
                inner join ventasDetalle as vd  on v.id=vd.idVentas
                inner join productos as p on vd.idProductos=p.id
                inner join familiaNivel" . $params['nivelFamilias'] . " as f on p.idFamiliaNivel" . $params['nivelFamilias'] . "=f.id
                where
                    v.idEmpresas=" . $params['idEmpresas'] . " "
			. $filtros . " group by f.id order by f.id";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO HISTORIAL COSTOS
	 *
	 */
	public function historialCostos($params)
	{
		$this->resultado = null;
		$sql = "select
                    a.*,b.sku,b.descLarga,date_format(a.fechaCompra,'%d-%m-%Y %H:%i:%s') as fechaCompra2
                from
                    productosCostos as a inner join productos as b on(a.idProductos=b.id)
                where b.sku='" . $params['codigo'] . "' and b.idEmpresas=" . $params['idEmpresas'] . "
                order by timestamp(fechaCompra) asc";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO VENTAS POR PRODUCTO
	 *
	 */
	public function consumoMateriales($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['codigo'] != "") {
			$filtros .= " and e.sku='" . $params['codigo'] . "'";
		}
		$sql = "select
                    e.sku,
                    e.descLarga,
                    f.descripcion as unidadMedida,
                    d.unidades * SUM(a.cantidad) AS unidadesUtilizadas,
                    round((d.total * sum(a.cantidad)), 2) as totalCosto
                from
                    ventasDetalle as a
                        inner join
                    ventas as b ON (a.idVentas = b.id)
                        inner join
                    productos as c ON (a.idProductos = c.id)
                        inner join
                    productosComponentes as d ON (a.idProductos = d.idProductoPrincipal)
                        inner join
                    productos as e ON (d.idProductos = e.id)
                        inner join
                    medidas as f on(e.idMedidas2=f.id)
                where
                    anulacion=0 and date(b.fechaFactura) between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and a.idSucursales=" . $params['idSucursales'] . " " . $filtros . "
                group by d.idProductos
                order by b.id asc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO GET ESTADO DE CUENTA
	 *
	 */
	public function estadoCuenta($nit)
	{
		$this->resultado = null;
		$sql = "SELECT
                    ventasCredito . *,
                    usuarios.userName,
                    sucursales.descripcion as sucursal,
                    ventas.documento
                FROM
                    ventasCredito inner join clientes on(ventasCredito.idClientes=clientes.id)
                    left join usuarios on(ventasCredito.idUsuarios=usuarios.id)
                    left join sucursales on(usuarios.idSucursales=sucursales.id)
                    left join ventas on(ventasCredito.idVentas=ventas.id)
                WHERE
                    clientes.nitC='" . $nit . "' order by timestamp(ventasCredito.created_at) desc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** REPORTE DE COBROS
	 *
	 */
	public function reporteCobros($dias)
	{
		$this->resultado = null;
		$sql = "select * from infoClientes
                where diasCredito='" . $dias . "' and diasMora >0 and saldo_actual >0;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO REPORTE EXISTENCIAS
	 *
	 */
	public function existencias($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['tipoReporte'] != "") {
			switch ($params['tipoReporte']) {
				case '2':
					$filtros .= " and saldo >'0.00'";
					break;
				case '3':
					$filtros .= " and saldo <='0.00'";
					break;
			}
		}
		//        if ($params['idMarcas'] != "[Seleccione...]") {
		//            if ($params['tipoReporte'] > 1) {
		//                $filtros .= " and idMarcas ='" . $params['idMarcas'] . "'";
		//            } else {
		//                $filtros .= " idMarcas ='" . $params['idMarcas'] . "'";
		//            }
		//        }
		//        if ($filtros != '') {
		//            $filtros = 'WHERE ' . $filtros;
		//        }
		//
		$sql = " select * from inventario
                 where ingresoA=" . $params['ingresoA'] . " and idPuntoIngreso=" . $params['idPuntoIngreso'] . " " . $filtros . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO REPORTE EXISTENCIAS
	 *
	 */
	public function existenciasKasual($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['tipoReporte'] != "") {
			switch ($params['tipoReporte']) {
				case '2':
					$filtros .= " existencia >'0.00'";
					break;
				case '3':
					$filtros .= " existencia <='0.00'";
					break;
			}
		}
		if ($params['idMarcas'] != "[Seleccione...]") {
			if ($params['tipoReporte'] > 1) {
				$filtros .= " and idMarcas ='" . $params['idMarcas'] . "'";
			} else {
				$filtros .= " idMarcas ='" . $params['idMarcas'] . "'";
			}
		}
		if ($filtros != '') {
			$filtros = 'WHERE ' . $filtros;
		}
		//
		$sql = " select
                    *,
                    (existencia*precioCosto) as costoTotal
                from(
                 SELECT
                    sku,
                    descLarga,
                    idMarcas,
                    item,
                    ifnull((select saldo from inventarios where ingresoA=" . $params['ingresoA'] . " and idPuntoIngreso=" . $params['idPuntoIngreso'] . " and idProductos=vw_productos.id and year(created_at)='" . $params['periodo'] . "' and month(created_at)='" . $params['mes'] . "' order by id desc limit 1),0) as existencia,
                    idFamiliaNivel1,
                    idFamiliaNivel2,
                    idFamiliaNivel3,
                    precioCosto,
                    precioPublico,
                    precioMayorista
                FROM
                    vw_productos) as t
                " . $filtros . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	public function consultarTomaMedidas($params)
	{
		$this->resultado = null;
		$filtros = "";

		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and fechaEntrega between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['noPedido'] != "") {
			$noPedido = $params['noPedido'];
			$filtros .= " and documento = '" . $noPedido . "'";
		}

		$sql = "SELECT * FROM vw_tomaMedidas WHERE  idEmpresas='" . $params['idEmpresas'] . "'" . $filtros . "";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO reporte de abonos
	 *
	 */
	public function reporteAbonos($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    a.fechaDeposito,
                    a.noBoleta,
                    a.nombreDeposito,
                    a.monto,
                    year(c.fechaFactura) as yearFactura,
                    month(c.fechaFactura) as monthFactura,
                    c.fechaFactura,
                    b.facLiquidadas,
                    b.debitos as abono,
                    c.total as totalFactura,
                    c.saldo
                FROM
                    depositos as a
                    LEFT JOIN cxc as b on(a.noBoleta=b.idDocumento)
                    LEFT JOIN ventas as c on(b.facLiquidadas=concat(c.serie,'-',c.correlativo))
                    LEFT JOIN clientes as d on(b.idClientes=d.id)
                where
                    -- b.idTipoDocumento=4
                    -- and
                    a.fechaDeposito between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and a.idEmpresas=" . $params['idEmpresas'] . "
                    and d.idCentrosCosto=" . $params['idCentrosCosto'] . "
                order by
                    a.fechaDeposito asc";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CONSULTA VALES
	 *
	 */
	public function consultarVales($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and a.fechaVale between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['estado'] != "") {
			$filtros .= " and a.estado=" . $params['estado'] . "";
		}
		if ($params['documento'] != "") {
			$filtros .= " and concat(a.serie,'-',a.documento)='" . $params['documento'] . "'";
		}
		if ($params['cliente'] != "") {
			$filtros .= " and a.nombre like '%" . str_replace(" ", "%", $params['cliente']) . "%'";
		}
		$sql = "SELECT
                    a.id,
                    concat(a.serie,'-',a.documento) as documento,
                    date_format(a.fechaVale,'%d-%m-%Y') as fechaVale,
                    a.nit,
                    a.nombre as solicitadoPor,
                    b.userName as realizadoPor,
                    case a.estado
                        when 1 then 'Abierto'
                        when 2 then 'Facturado'
                    end as estado,
                    a.valorVale,
                    vd.cantidad as galones,
                    vd.precio,
                    vd.costo,
                    vd.totalCosto
                 FROM
                    vales as a
                    left join usuarios as b on(a.idUsuarios=b.id)
                    left join sucursales as c on(a.idSucursales=c.id)
                    left join empresas as d on(a.idEmpresas=d.id)
                    left join ventas as e on(a.idVentas=e.id)
                    left join valesDetalle as vd on(a.id=vd.idVales)
                WHERE
                    a.idEmpresas=" . $params['idEmpresas'] . " $filtros;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO eliminarVale
	 *
	 */
	public function eliminarVale($params)
	{
		$response = "";
		//PASO 1. ELIMINAR REGISTROS DE CXC
		$sql1 = "delete from cxc where idDocumento='" . $params['documento'] . "' and idEmpresas=" . $params['idEmpresas'] . ";";
		$query1 = mysql_query($sql1, dbCon::conPrincipal());
		if ($query1) {
			//PASO 2. ELIMINAR DEPOSITO
			$sql2 = "delete from vales where id=" . $params['idVale'] . "  and idEmpresas=" . $params['idEmpresas'] . ";";
			$query2 = mysql_query($sql2, dbCon::conPrincipal());
			if ($query2) {
				//PASO 3. ELIMINAR REGISTRO INVENTARIO
				$sql3 = "delete from inventarios where documento='" . $params['documento'] . "'  and idEmpresas=" . $params['idEmpresas'] . ";";
				$query3 = mysql_query($sql3, dbCon::conPrincipal());
				if ($query3) {
					$response[] = array('message' => 'success');
				} else {
					$error = mysql_error();
					$response[] = array('message' => 'failed step3', 'error' => $error);
				}
			} else {
				$error = mysql_error();
				$response[] = array('message' => 'failed step6', 'error' => $error);
			}
		} else {
			$error = mysql_error();
			$response[] = array('message' => 'failed step5', 'error' => $error);
		}
		return $response;
	}

	/** METODO reporte de abonos
	 *
	 */
	public function reporteComisionesSuple($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    date(b.created_at) as fechaDeposito,
                    idDocumento as noBoleta,
                    d.nombreC as nombreDeposito,
                    b.debitos as monto,
                    YEAR(c.fechaFactura) AS yearFactura,
                    MONTH(c.fechaFactura) AS monthFactura,
                    c.fechaFactura,
                    c.id as idVentas,
                    b.facLiquidadas,
                    b.debitos AS abono,
                    c.total AS totalFactura,
                    c.saldo,
                    ifnull(f.cantidad,0) as galonesVenta,
                    d.idUsuarios as idVendedor,
                    e.userName as vendedor
                FROM
                    cxc as b
                        LEFT JOIN
                    ventas AS c ON (b.facLiquidadas = CONCAT(c.serie, '-', c.correlativo))
                        LEFT JOIN
                    clientes as d on(b.idClientes=d.id)
                        LEFT JOIN
                    usuarios as e on(d.idUsuarios=e.id)
                        LEFT JOIN
                    ventasDetalle as f on(c.id=f.idVentas)
                WHERE
                    b.idTipoDocumento = 4
                    AND date(b.created_at) BETWEEN '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'
                    AND d.idEmpresas = " . $_REQUEST['idEmpresas'] . " AND d.idUsuarios=" . $_REQUEST['idVendedores'] . "
                    AND d.idCentrosCosto=" . $params['idCentrosCosto'] . "
                group by
                    noBoleta,facLiquidadas
                ORDER BY date(b.created_at) ASC;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CONSULTA VALES
	 *
	 */
	public function consultarValesLiquidar($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    a.id,
                    concat(a.serie,'-',a.documento) as documento,
                    date_format(a.fechaVale,'%d-%m-%Y') as fechaVale,
                    a.nit,
                    a.nombre as solicitadoPor,
                    b.userName as realizadoPor,
                    case a.estado
                            when 1 then 'Abierto'
                            when 2 then 'Facturado'
                    end as estado,
                    a.valorVale,
                    vd.cantidad as galones,
                    vd.precio,
                    vd.costo,
                    vd.totalCosto
                 FROM
                    vales as a
                    left join usuarios as b on(a.idUsuarios=b.id)
                    left join sucursales as c on(a.idSucursales=c.id)
                    left join empresas as d on(a.idEmpresas=d.id)
                    left join ventas as e on(a.idVentas=e.id)
                    left join valesDetalle as vd on(a.id=vd.idVales)
                WHERE
                    a.idClientes=" . $params['idClientes'] . " and a.idEmpresas=" . $params['idEmpresas'] . " and a.estado=1;";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO reporte de saldos
	 *
	 */
	public function reporteSaldos($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['year'] != "") {
			$filtros .= " AND YEAR(fechaFactura) = '" . $params['year'] . "'";
		}
		if ($params['month'] != "") {
			$filtros .= " AND MONTH(fechaFactura) = '" . $params['month'] . "'";
		}
		$sql = "SELECT
                    YEAR(fechaFactura) AS year,
                    CASE MONTH(fechaFactura)
                        WHEN 1 THEN 'ENERO'
                        WHEN 2 THEN 'FEBRERO'
                        WHEN 3 THEN 'MARZO'
                        WHEN 4 THEN 'ABRIL'
                        WHEN 5 THEN 'MAYO'
                        WHEN 6 THEN 'JUNIO'
                        WHEN 7 THEN 'JULIO'
                        WHEN 8 THEN 'AGOSTO'
                        WHEN 9 THEN 'SEPTIEMBRE'
                        WHEN 10 THEN 'OCTUBRE'
                        WHEN 11 THEN 'NOVIEMBRE'
                        WHEN 12 THEN 'DICIEMBRE'
                    END AS month,
                    SUM(saldo) AS totalFacturas
                FROM
                    ventas AS a
                        INNER JOIN
                    clientes AS b ON (a.idClientes = b.id)
                WHERE
                    anulacion = 0
                    AND b.idCentrosCosto = " . $params['idCentrosCosto'] . "
                    AND a.idEmpresas = " . $params['idEmpresas'] . " " . $filtros . "
                GROUP BY
                    MONTH(fechaFactura);";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO reporte de saldos detallado
	 *
	 */
	public function reporteSaldosDetallado($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    saldo,
                    nombre as cliente,
                    CONCAT(serie, '-', correlativo) AS documento,
                    date_format(fechaFactura,'%d-%m-%Y') as fechaDocumento
                FROM
                    ventas as a inner join clientes as b on(a.idClientes=b.id)
                WHERE
                    anulacion = 0 and b.idCentrosCosto=" . $params['idCentrosCosto'] . "
                    AND saldo!=0
                    and year(fechaFactura)='" . $params['year'] . "'
                    and month(fechaFactura)='" . $params['month'] . "'
                    and a.idEmpresas=" . $params['idEmpresas'] . "";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CONSULTA RECIBOS
	 *
	 */
	public function consultarRecibos($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and a.created_at between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['recibo'] != "") {
			$filtros .= " and a.idDocumento='" . $params['recibo'] . "'";
		}
		if ($params['factura'] != "") {
			$filtros .= " and concat(serie,'-',correlativo)='" . $params['factura'] . "'";
		}
		if ($params['cliente'] != "") {
			$cliente = str_replace(" ", "%", $params['cliente']);
			$filtros .= " and b.nombreF like '%" . $cliente . "%'";
		}
		$sql = "SELECT
                    a.id,
                    a.idVentas,
                    date_format(a.created_at,'%d-%m-%Y') as fechaRecibo,
                    idDocumento as documento,
                    b.nombreF as nombreCliente,
                    b.nitC as nit,
                    concat(serie,'-',correlativo) as factura,
                    c.total as montoFactura,
                    debitos as monto,
                    c.saldo
                FROM
                    cxc AS a
                        LEFT JOIN
                    clientes AS b ON (a.idClientes = b.id)
                        LEFT JOIN
                    ventas AS c ON (a.idVentas = c.id)
                WHERE
                    a.idTipoDocumento = 2 AND a.idEmpresas=" . $params['idEmpresas'] . " " . $filtros . "
                ORDER BY
                    date(a.created_at) asc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CONSULTA FACTURAS SUPLE
	 *
	 */
	public function consultaFacturasSuple($params)
	{
		$this->resultado = null;
		$filtros = "";
		$idVenta = "";
		$serie = "";
		$correlativo = "";
		$nit = "";
		$cajero = "";
		$vendedor = "";
		if ($params['fechaInicio'] != "" && $params['fechaFin'] != "") {
			$filtros .= " and fechaFactura between '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'";
		}
		if ($params['serie'] != "") {
			$filtros .= " and a.serie='" . $params['serie'] . "'";
		}
		if ($params['correlativo'] != "") {
			$filtros .= " and a.correlativo='" . $params['correlativo'] . "'";
		}
		if ($params['nit'] != "") {
			$filtros .= " and a.nit='" . $params['nit'] . "'";
		}
		if ($params['idSucursales'] != "") {
			$filtros .= " and a.idSucursales=" . $params['idSucursales'] . "";
		}
		if ($params['idVendedor'] != "") {
			$vendedor = $params['idVendedor'];
			$filtros .= " and a.idVendedores=" . $vendedor . "";
		}
		if ($params['tipoVenta'] != "") {
			$filtros .= " and a.idTipoVenta=" . $params['tipoVenta'] . "";
		}
		if ($params['estatus'] != "") {
			$filtros .= " and a.anulacion=" . $params['estatus'] . "";
		}
		if ($params['cliente'] != "") {
			$cliente = str_replace(" ", "%", $params['cliente']);
			$filtros .= " and a.nombre like '%" . $cliente . "%'";
		}
		$sql = "SELECT
                    a.id as idVenta,
                    date_format(a.fechaFactura,'%d-%m-%Y') as fechaFactura,
                    if(a.idTipoVenta=1,'CONTADO','CREDITO') as tipoVenta,
                    concat(a.serie,'-',a.correlativo) as documento,
                    a.nit,
                    a.nombre as nombreCliente,
                    b.descripcion as sucursal,
                    c.userName as vendedor,
                    if(a.anulacion,'ANULADA','ACTIVA') as estatus,
                    ifnull(date_format(a.anulacion_at,'%d-%m-%Y'),'-') as fechaAnulacion,
                    ifnull(d.cantidad,0) as cantidad,
                    a.anticipo,
                    a.saldo,
                    (a.total-a.iva) as subtotal,
                    a.iva,
                    a.total
                FROM
                    ventas as a
                    inner join sucursales as b on(a.idSucursales=b.id)
                    inner join usuarios as c on(if(a.idVendedores=0,a.idUsuarios,a.idVendedores)=c.id)
                    left join ventasDetalle as d on(a.id=d.idVentas)
                where
                    a.idEmpresas=" . $params['idEmpresas'] . " " . $filtros . "
                order by serie,cast(correlativo as unsigned) asc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CONSULTA FACTURAS GENERAL
	 *
	 */
	public function consultarFacturasGeneral($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    *
                FROM
                    vw_consultaGeneralFacturacion
                WHERE
                    fechaDocumento BETWEEN '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'
                ORDER BY fechaDocumento ASC";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO consultarDespachos
	 *
	 */
	public function consultarDespachos($params)
	{
		$bombas = "";
		foreach ($params['bomba'] as $key => $value) {
			if ($key === count($bombas)) {
				$bombas .= "'" . $value . "'";
			} else {
				$bombas .= "'" . $value . "',";
			}
		}
		$this->resultado = null;
		$filtros = "";
		if ($params['estado'] != "") {
			$filtros .= " and estado='" . $params['estado'] . "'";
		}
		if ($params['producto'] != "") {
			$filtros .= " and producto='" . $params['producto'] . "'";
		}
		if ($params['mangera'] != "") {
			$filtros .= " and mangera='" . $params['mangera'] . "'";
		}
		if ($params['bomba'] != "") {
			$filtros .= " and bomba in (" . $bombas . ")";
		}
		$sql = "SELECT
                    *,
                    CASE estado
                            WHEN 0 THEN 'SIN PROCESAR'
                            WHEN 1 THEN 'FACTURADO'
                            WHEN 2 THEN 'VALE EMITIDO'
                    END as estado,
                    CASE estado
                            WHEN 0 THEN 'N/A'
                            WHEN 1 THEN (select autorizacionFEL from ventas where id=despachos.idVentas)
                            WHEN 2 THEN (select concat(serie,'-',documento) from vales where id=despachos.idVentas)
                    END as documento,
                    precioVenta,
                    (SELECT
                        precioCompra
                    FROM
                        comprasDetalle
                            INNER JOIN
                        compras ON (comprasDetalle.idCompras = compras.id)
                                    INNER JOIN
                         productos on(comprasDetalle.idProductos=productos.id)
                    WHERE
                        productos.upc =despachos.producto and date_format(fechaFactura,'%d/%m/%Y')<=substring(fechaHora,1,10) order by compras.id desc limit 1) as costo
                FROM
                    despachos
                ORDER BY id DESC;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO getBombas
	 *
	 */
	public function getBombas($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    *
                FROM
                    bodegas
                WHERE idEmpresas=" . $params['idEmpresas'] . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO consultarFacturasKreativos
	 *
	 */
	public function consultarFacturasKreativos($params)
	{
		$this->resultado = null;
		$filtros = "";
		$idVenta = "";
		$serie = "";
		$correlativo = "";
		$nit = "";
		$cajero = "";
		$vendedor = "";
		if ($params['serie'] != "") {
			$filtros .= " and ventas.serie='" . $params['serie'] . "'";
		}
		if ($params['correlativo'] != "") {
			$filtros .= " and ventas.correlativo='" . $params['correlativo'] . "'";
		}
		if ($params['idSucursales'] != "") {
			$filtros .= " and ventas.idSucursales=" . $params['idSucursales'] . "";
		}
		if ($params['idVendedor'] != "") {
			$vendedor = $params['idVendedor'];
			$filtros .= " and ventas.idVendedores=" . $vendedor . "";
		}
		if ($params['estatus'] != "") {
			$filtros .= " and ventas.anulacion=" . $params['estatus'] . "";
		}
		if ($params['cliente'] != "") {
			$cliente = str_replace(" ", "%", $params['cliente']);
			$filtros .= " and concat(ventas.nombre,' ',ventas.nit) like '%" . $cliente . "%'";
		}
		if ($params['monedaFacturacion'] != "") {
			$filtros .= " and ventas.moneda='" . $params['monedaFacturacion'] . "'";
		}
		if ($params['formaPago'] != "") {
			$filtros .= " and ventas.idFormasPago=" . $params['formaPago'] . "";
		}
		$sql = "SELECT
                    ventas.id as idVenta,
                    serie,
                    correlativo,
                    autorizacionFEL,
                    fechaEmisionFEL,
                    nit,
                    nombre,
                    direccion,
                    upper(usuarios.userName) as vendedor,
                    proveedores.descripcion as lineaAerea,
                    if(idFormasPago=1,'EFECTIVO','TARJETA') as formaPago,
                    moneda,
                    valorFactura,
                    if(anulacion=0,'ACTIVA','ANULADA') as estado,
                    upper(u.userName) as usuario,
                    cantidad,
                    sku,
                    descLarga,
                    precio as precioUnitario,
                    ventasDetalle.total,
                    ventas.fechaFactura,
                    ventas.idSucursales,
                    concat(ventas.serie,'-',ventas.correlativo) as documento
                FROM
                    ventas
                    inner join ventasDetalle on(ventas.id=ventasDetalle.idVentas)
                    left join usuarios on(ventas.idVendedores=usuarios.id)
                    left join proveedores on(ventas.idLineasAereas=proveedores.id)
                    left join usuarios as u on(ventas.idUsuarios=u.id)
                where
                    ventas.fechaFactura between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "' " . $filtros . " order by ventas.id desc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO consultarDespachosP1
	 *
	 */
	public function consultarDespachosP1($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['producto'] != "") {
			$filtros .= " and ventasDetalle.descLarga='" . $params['producto'] . "'";
		}
		if ($params['bomba'] != "") {
			$filtros .= " and idBombas=" . $params['bomba'] . "";
		}
		$sql = "SELECT
                    bodegas.descripcion as bomba,
                    ventasDetalle.descLarga as producto,
                    sum(ventasDetalle.cantidad) as galones,
                    sum(ventasDetalle.total) as totalVenta,
                    sum(ventasDetalle.totalCosto) as totalCosto,
                    (sum(ventasDetalle.total)-sum(ventasDetalle.totalCosto)) as utilidad,
                    round(((sum(ventasDetalle.total)-sum(ventasDetalle.totalCosto))/sum(ventasDetalle.total)*100),2) as margen,
                    ifnull((select lectura from lecturas where idBodegas=ventas.idBombas and idProductos=ventasDetalle.idProductos and date_format(fechaHora,'%d/%m/%Y %H:%i:%s')='" . $params['fechaInicio'] . "'),0) as lecturaInicial,
                    ifnull((select lectura from lecturas where idBodegas=ventas.idBombas and idProductos=ventasDetalle.idProductos and date_format(fechaHora,'%d/%m/%Y %H:%i:%s')='" . $params['fechaFin'] . "'),0) as lecturaFinal,
                    ifnull((select precioVenta from lecturas where idBodegas=ventas.idBombas and idProductos=ventasDetalle.idProductos and date_format(fechaHora,'%d/%m/%Y %H:%i:%s')='" . $params['fechaInicio'] . "'),0) as precioLectura
                FROM
                    ventas
                    inner join ventasDetalle on(ventas.id=ventasDetalle.idVentas)
                    inner join bodegas on(ventas.idBombas=bodegas.id)
                WHERE
                    ventas.idEmpresas = " . $params['idEmpresas'] . " and ventas.anulacion=0
                    and date_format(ventas.created_at,'%d/%m/%Y %H:%i:%s') between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "' " . $filtros . "
                group by
                    idBombas,sku";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO consultarDespachosP1
	 *
	 */
	public function consultarCompras($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['tipoCompra'] != "") {
			$filtros .= " and compras.idTipoCompra=" . $params['tipoCompra'] . "";
		}
		if ($params['proveedor'] != "") {
			$proveedor = str_replace(" ", "%", $params['proveedor']);
			$filtros .= " and concat(proveedores.descripcion,' ',proveedores.nitP) like '%" . $proveedor . "%'";
		}
		if ($params['serie'] != "") {
			$filtros .= " and compras.serieFactura='" . $params['serie'] . "'";
		}
		if ($params['correlativo'] != "") {
			$filtros .= " and compras.noFactura='" . $params['correlativo'] . "'";
		}
		if ($params['idCentrosCosto'] != "") {
			$filtros .= " and compras.idCentrosCosto=" . $params['idCentrosCosto'] . "";
		}
		$sql = "SELECT
                    compras.id,
                    fechaFactura,
                    if(idTipoCompra=1,'CONTADO','CREDITO') as tipoCompra,
                    serieFactura,
                    noFactura,
                    ifnull(proveedores.nitP,'SIN NIT') as nit,
                    ifnull(proveedores.descripcion,'SIN PROVEEDOR') as proveedor,
                    total,
                    subtotal,
                    iva,
                    compras.saldo,
                    compras.conceptoCompra,
                    centrosCosto.descripcion as centrosCosto,
                    ifnull(vw_hrmEmpleados.idHrmDepartamentos,'N/A') as depto,
                    ifnull(vw_hrmEmpleados.idHrmPuestos,'N/A') as puesto,
                    ifnull(bodegas.descripcion,'N/A') as lugarIngreso
                FROM
                    compras
                    left join proveedores on(compras.idProveedores=proveedores.id)
                    left join centrosCosto on(compras.idCentrosCosto=centrosCosto.id)
                    left join vw_hrmEmpleados on(proveedores.nitP=vw_hrmEmpleados.codigoEmpleado)
                    left join bodegas on(compras.idPuntoIngreso=bodegas.id)
                WHERE
                    compras.idEmpresas = " . $params['idEmpresas'] . "
                    and compras.fechaFactura between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "' " . $filtros . " ORDER BY fechaFactura ASC";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/**
	 * LIBRO DE COMPRAS
	 */
	public function libroCompras($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    *
                FROM
                    libroCompras
                WHERE
                    YEAR(fechaDocumento) = " . $params['yearInicial'] . "
                    AND MONTH(fechaDocumento) = " . $params['mesInicial'] . "
                    AND idSucursales = " . $params['idSucursales'] . "
                    AND idEmpresas = " . $params['idEmpresas'] . "
                    AND generaIva = 1
                ORDER BY fechaDocumento ASC";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/**
	 * LIBRO DE VENTAS
	 */
	public function libroVentas($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    *
                FROM
                    libroVentas
                WHERE
                    year(fechaDocumento)=" . $params['yearInicial'] . "
                    and month(fechaDocumento)=" . $params['mesInicial'] . "
                    and idEmpresas=" . $params['idEmpresas'] . "
                    and idSucursales=" . $params['idSucursales'] . " order by fechaDocumento desc;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO COMPRAS POR PRODUCTO
	 *
	 */
	public function comprasPorProducto($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['idFamiliaNivel1'] != "") {
			$filtros .= " and c.idFamiliaNivel1=" . $params['idFamiliaNivel1'] . "";
		}
		if ($params['idFamiliaNivel2'] != "") {
			$filtros .= " and c.idFamiliaNivel2=" . $params['idFamiliaNivel2'] . "";
		}
		if ($params['idFamiliaNivel3'] != "") {
			$filtros .= " and c.idFamiliaNivel3=" . $params['idFamiliaNivel3'] . "";
		}
		if ($params['codigo'] != "") {
			$filtros .= " and concat(c.sku,' ',ifnull(c.upc,'')) like '%" . $params['codigo'] . "%'";
		}
		if ($params['proveedor'] != "") {
			$proveedor = str_replace(" ", "%", $params['proveedor']);
			$filtros .= " and concat(p.descripcion,' ',p.nitP) like '%" . $proveedor . "%'";
		}
		$sql = "select
                    c.sku as codigo,
                    c.descLarga as descripcion,
                    sum(a.cantidad) as cantidad,
                    sum(a.total) as totalCosto,
                    p.descripcion as proveedor,
                    precioCompra
                from
                    comprasDetalle as a
                        inner join
                    compras as b on(a.idCompras=b.id)
                        left join
                    productos as c on(a.idProductos=c.id)
			inner join
                    proveedores p on(b.idProveedores=p.id)
                where
                    b.idEmpresas=" . $params['idEmpresas'] . "
                    and date(b.fechaFactura) between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "' " . $filtros . "
                group by a.idProductos,b.idProveedores order by codigo,idProveedores;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO REPORTE EXISTENCIAS HISTORICO
	 *
	 */
	public function existenciasHistorico($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['idMarcas'] != "") {
			$filtros .= " and idMarcas =" . $params['idMarcas'] . "";
		}
		if ($params['idFamilias'] != "") {
			$filtros .= " and idGrupos =" . $params['idGrupos'] . "";
		}
		if ($params['idFamiliaNivel1'] != "") {
			$filtros .= " and idFamiliaNivel1 =" . $params['idFamiliaNivel1'] . "";
		}
		if ($params['idFamiliaNivel2'] != "") {
			$filtros .= " and idFamiliaNivel2 =" . $params['idFamiliaNivel2'] . "";
		}
		if ($params['idFamiliaNivel3'] != "") {
			$filtros .= " and idFamiliaNivel3 =" . $params['idFamiliaNivel3'] . "";
		}
		if ($params['sku'] != "") {
			$filtros .= " and sku='" . $params['sku'] . "'";
			//$filtros .= " and concat(sku,' ',ifnull(upc,'')) like '%" . $params['sku'] . "%'";
		}
		//
		$sql = "SELECT
                    sku,descLarga,precioCosto,precioPublico,
                    ifnull((SELECT
                        saldo
                     FROM
                        inventarios
                     WHERE
                        idEmpresas = productos.idEmpresas
                        AND idProductos = productos.id and date(created_at)!='0000-00-00'
                        and timestamp(created_at)<='" . $params['fecha'] . " " . $params['hora'] . "' and ingresoA=2
                        order by id desc limit 1),0) as saldo,
                        marcas.descripcion as marca,
                        grupos.descripcion as proveedor,
                        familiaNivel1.descripcion as departamento,
                        familiaNivel2.descripcion as categoria,
                        familiaNivel3.descripcion as subCategoria
                FROM
                    productos
                    left join marcas on(productos.idMarcas=marcas.id)
                    left join grupos on(productos.idGrupos=grupos.id)
                    left join familiaNivel1 on(productos.idFamiliaNivel1=familiaNivel1.id)
                    left join familiaNivel2 on(productos.idFamiliaNivel2=familiaNivel2.id)
                    left join familiaNivel3 on(productos.idFamiliaNivel3=familiaNivel3.id)
                WHERE
                    productos.idEmpresas = " . $params['idEmpresas'] . " " . $filtros . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO consultarFacturasResumen
	 *
	 */
	public function consultarFacturasResumen($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    empresas.nombreComercial AS empresa,
                    sucursales.descripcion AS agencia,
                    FORMAT(SUM(total), 2) AS total,
                    count(ventas.id) as numeroTransacciones,
                    round(SUM(total)/count(ventas.id),2) as ticketPromedio,
                    FORMAT(SUM(total)/1.12, 2) AS subtotal,
                    FORMAT((SUM(total)/1.12)*0.12, 2) AS iva
                FROM
                    ventas
                        INNER JOIN
                    sucursales ON (ventas.idSucursales = sucursales.id)
                        INNER JOIN
                    empresas ON (sucursales.idEmpresas = empresas.id)
                WHERE
                    ventas.fechaFactura between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
                    and empresas.nit='" . $params['nitEmpresa'] . "'
                GROUP BY idSucursales;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO consultarCortesCaja
	 *
	 */
	public function consultarCortesCaja($params)
	{
		$this->resultado = null;
		$sql = "select
                    corteCaja.*,
                    usuarios.userName as cajero
                from
                    corteCaja
                    inner join usuarios on(corteCaja.idUsuarios=usuarios.id)
                where
                    corteCaja.fechaCorte between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "' and corteCaja.idSucursales=" . $params['idSucursales'] . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO antiguedadSaldos
	 *
	 */
	public function antiguedadSaldos($params)
	{
		$this->resultado = null;
		$filter = "";
		if ($params['tipoConsulta'] !== '') {
			if ($params['tipoConsulta'] === '2') {
				$filter .= " and saldo>0";
			}
		}
		if ($params['nit'] !== '') {
			$filter .= " and nitC='" . $params['nit'] . "'";
		}
		$sql = "SELECT
                    *
                FROM
                    vw_antiguedadSaldos
                where
                    fechaReporte between '" . date("Y-m-d", strtotime($_REQUEST['fechaInicio'])) . "' and '" . date("Y-m-d", strtotime($_REQUEST['fechaFin'])) . "'
                    and idEmpresas=" . $params['idEmpresas'] . " " . $filter . "
                order by nitC,fechaReporte";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO CORTE GASOLINERAS
	 *
	 */
	public function getCorteGasolinera($params)
	{
		$this->resultado = null;
		$filtros = "";
		if ($params['idVendedores'] != "") {
			$filtros .= " and idUsuarios=" . $params['idVendedores'] . "";
		}
		//
		$sql = "SELECT
					ventas.idUsuarios,
					usuarios.userName as despachador,
				    SUM(ventas.subtotal) AS subtotal,
				    SUM(ventas.iva) AS iva,
				    (SUM(ventas.total)-(SUM(ventas.subtotal)+SUM(ventas.iva))) as idp,
				    SUM(ventas.total) AS total_facturacion,
				    ifnull((SELECT
						SUM(cantidad)
					FROM
						ventasDetalle
						inner join ventas as v on(ventasDetalle.idVentas=v.id)
						WHERE
						v.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and v.idEmpresas=" . $params['idEmpresas'] . " and ventasDetalle.idUsuarios=ventas.idUsuarios and idProductos=9),0) as galones_fac_super,
				    ifnull((SELECT
						round(SUM((cantidad*precio)),2)
					FROM
						ventasDetalle
						inner join ventas as v on(ventasDetalle.idVentas=v.id)
						WHERE
						v.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and v.idEmpresas=" . $params['idEmpresas'] . " and ventasDetalle.idUsuarios=ventas.idUsuarios and idProductos=9),0) as total_fac_super,
					ifnull((SELECT
						SUM(cantidad)
					FROM
						ventasDetalle
						inner join ventas as v on(ventasDetalle.idVentas=v.id)
						WHERE
						v.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and v.idEmpresas=" . $params['idEmpresas'] . " and ventasDetalle.idUsuarios=ventas.idUsuarios and idProductos=8),0) as galones_fac_regular,
				    ifnull((SELECT
						round(SUM((cantidad*precio)),2)
					FROM
						ventasDetalle
						inner join ventas as v on(ventasDetalle.idVentas=v.id)
						WHERE
						v.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and v.idEmpresas=" . $params['idEmpresas'] . " and ventasDetalle.idUsuarios=ventas.idUsuarios and idProductos=8),0) as total_fac_regular,
					ifnull((SELECT
						SUM(cantidad)
					FROM
						ventasDetalle
						inner join ventas as v on(ventasDetalle.idVentas=v.id)
						WHERE
						v.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and v.idEmpresas=" . $params['idEmpresas'] . " and ventasDetalle.idUsuarios=ventas.idUsuarios and idProductos=7),0) as galones_fac_diesel,
				    ifnull((SELECT
						round(SUM((cantidad*precio)),2)
					FROM
						ventasDetalle
						inner join ventas as v on(ventasDetalle.idVentas=v.id)
						WHERE
						v.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and v.idEmpresas=" . $params['idEmpresas'] . " and ventasDetalle.idUsuarios=ventas.idUsuarios and idProductos=7),0) as total_fac_diesel,
				     ifnull((SELECT
				 		SUM(ventasDetalle.total)
				 	FROM
				 		ventasDetalle
				 		inner join ventas as v on(ventasDetalle.idVentas=v.id)
				 		WHERE
				 		v.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
				 	and v.idEmpresas=" . $params['idEmpresas'] . " and ventasDetalle.idUsuarios=ventas.idUsuarios and idProductos not in(7,8,9)),0) as fac_lubricantes,
				    ifnull((SELECT
						SUM(cantidad)
					FROM
						valesDetalle
						inner join vales on(valesDetalle.idVales=vales.id)
						WHERE
						vales.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and vales.idEmpresas=" . $params['idEmpresas'] . "
					and vales.idUsuarios=ventas.idUsuarios
					and idProductos=9),0) as galones_vales_super,
					ifnull((SELECT
						SUM(total)
					FROM
						valesDetalle
						inner join vales on(valesDetalle.idVales=vales.id)
						WHERE
						vales.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and vales.idEmpresas=" . $params['idEmpresas'] . "
					and vales.idUsuarios=ventas.idUsuarios
					and idProductos=9),0) as total_vales_super,
				    ifnull((SELECT
						SUM(cantidad)
					FROM
						valesDetalle
						inner join vales on(valesDetalle.idVales=vales.id)
						WHERE
						vales.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and vales.idEmpresas=" . $params['idEmpresas'] . "
					and vales.idUsuarios=ventas.idUsuarios
					and idProductos=8),0) as galones_vales_regular,
					ifnull((SELECT
						SUM(total)
					FROM
						valesDetalle
						inner join vales on(valesDetalle.idVales=vales.id)
						WHERE
						vales.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and vales.idEmpresas=" . $params['idEmpresas'] . "
					and vales.idUsuarios=ventas.idUsuarios
					and idProductos=8),0) as total_vales_regular,
				    ifnull((SELECT
						SUM(cantidad)
					FROM
						valesDetalle
						inner join vales on(valesDetalle.idVales=vales.id)
						WHERE
						vales.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and vales.idEmpresas=" . $params['idEmpresas'] . "
					and vales.idUsuarios=ventas.idUsuarios
					and idProductos=7),0) as galones_vales_diesel,
					ifnull((SELECT
						SUM(total)
					FROM
						valesDetalle
						inner join vales on(valesDetalle.idVales=vales.id)
						WHERE
						vales.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
					and vales.idEmpresas=" . $params['idEmpresas'] . "
					and vales.idUsuarios=ventas.idUsuarios
					and idProductos=7),0) as total_vales_diesel,
				    ifnull((SELECT
					   sum(valorVale)
					FROM
						vales
					WHERE
						created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
							and idEmpresas=" . $params['idEmpresas'] . " and idUsuarios=ventas.idUsuarios
					GROUP BY idUsuarios),0) as total_vales,
				    ifnull((SELECT
						sum(debitos) as totalNC
					FROM
						cxc
						inner join ventas as v on(cxc.idVentas=v.id)
					WHERE
						idTipoDocumento = 6 and cxc.idEmpresas=" . $params['idEmpresas'] . " and cxc.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
						and v.idUsuarios=ventas.idUsuarios),0) as total_nc,
					ifnull((SELECT
					    sum(totalGalones) as totalLecturas
					FROM
					    lecturas
					where
						idProductos=9 and idUsuarios=ventas.idUsuarios and fechaHoraInicioTurno='" . $params['fechaInicio'] . "' and fechaHoraFinTurno='" . $params['fechaFin'] . "'),0) as galones_lectura_super,
					ifnull((SELECT
					    sum(totalMoneda) as totalVenta
					FROM
					    lecturas
					where
						idProductos=9 and idUsuarios=ventas.idUsuarios and fechaHoraInicioTurno='" . $params['fechaInicio'] . "' and fechaHoraFinTurno='" . $params['fechaFin'] . "'),0) as total_lectura_super,
					ifnull((SELECT
					    sum(totalGalones) as totalLecturas
					FROM
					    lecturas
					where
						idProductos=8 and idUsuarios=ventas.idUsuarios and fechaHoraInicioTurno='" . $params['fechaInicio'] . "' and fechaHoraFinTurno='" . $params['fechaFin'] . "'),0) as galones_lectura_regular,
					ifnull((SELECT
					    sum(totalMoneda) as totalVenta
					FROM
					    lecturas
					where
						idProductos=8 and idUsuarios=ventas.idUsuarios and fechaHoraInicioTurno='" . $params['fechaInicio'] . "' and fechaHoraFinTurno='" . $params['fechaFin'] . "'),0) as total_lectura_regular,
					ifnull((SELECT
					    sum(totalGalones) as totalLecturas
					FROM
					    lecturas
					where
						idProductos=7 and idUsuarios=ventas.idUsuarios and fechaHoraInicioTurno='" . $params['fechaInicio'] . "' and fechaHoraFinTurno='" . $params['fechaFin'] . "'),0) as galones_lectura_diesel,
					ifnull((SELECT
					    sum(totalMoneda) as totalVenta
					FROM
					    lecturas
					where
						idProductos=7 and idUsuarios=ventas.idUsuarios and fechaHoraInicioTurno='" . $params['fechaInicio'] . "' and fechaHoraFinTurno='" . $params['fechaFin'] . "'),0) as total_lectura_diesel,
					ifnull((SELECT
					    sum(totalMoneda) as totalVenta
					FROM
					    lecturas
					where
						idUsuarios=ventas.idUsuarios and fechaHoraInicioTurno='" . $params['fechaInicio'] . "' and fechaHoraFinTurno='" . $params['fechaFin'] . "'),0) as total_lecturas
				FROM
				    ventas
				    inner join usuarios on(ventas.idUsuarios=usuarios.id)
				WHERE
					ventas.created_at between '" . $params['fechaInicio'] . "' and '" . $params['fechaFin'] . "'
				    and ventas.idEmpresas=" . $params['idEmpresas'] . " " . $filtros . "
				GROUP BY idUsuarios";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO ventasHoy
	 *
	 */
	public function ventasHoy($params)
	{
		$this->resultado = null;
		$filter = "";
		switch ($params['option']) {
			case 'Hoy':
				$filter = "and DATE(fechaFactura) = CURDATE()";
				break;
			case 'Ayer':
				$filter = "and DATE(fechaFactura) = DATE_ADD(CURDATE(), INTERVAL -1 DAY)";
				break;
			case 'MesHoy':
				$filter = "and fechaFactura between concat(year(curdate()),'-',month(curdate()),'-01') and concat(year(curdate()),'-',month(curdate()),'-',day(curdate()))";
				break;
			case 'MesAyer':
				$filter = "and fechaFactura between concat(year(curdate()),'-',month(curdate())-1,'-01') and concat(year(curdate()),'-',month(curdate())-1,'-',day(curdate()))";
				break;
			case 'UltimaTransaccion':
				$filter = "and DATE(fechaFactura) = CURDATE() order by id desc limit 1";
				break;
		}
		$sql = "SELECT
            ifnull(SUM(total),0) AS total,
            count(id) as noTransacciones,
            round(SUM(total)/count(id),2) as ventaPromedio,
            time(created_at) as horaUltimaTransaccion,
            total as ultimaTransaccion
        FROM
            ventas
        WHERE
            idEmpresas = " . $params['idEmpresas'] . " " . $filter . ";";
		echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO ultimaTransaccion
	 *
	 */
	public function ultimaTransaccion($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    total,
                    time(created_at) as hora,
                    (select sum(total) from ventas where idEmpresas = 8 and fechaFactura=curdate() and hour(created_at)=hour(curtime())) as montoUltimaHora,
                    (select count(id) from ventas where idEmpresas = 8 and fechaFactura=curdate() and hour(created_at)=hour(curtime())) as transaccionesUltimaHora,
                    (select ifnull(round(sum(total)/count(id),2),0) from ventas where idEmpresas = 8 and fechaFactura=curdate() and hour(created_at)=hour(curtime())) as promUltimaHora
                FROM
                    ventas
                WHERE
                    idEmpresas = " . $params['idEmpresas'] . " and fechaFactura=curdate() order by id desc limit 1;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO top10hoy
	 *
	 */
	public function top10hoy($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    sku,
                    SUBSTRING(descLarga, 1, 20) AS descLarga,
                    SUM(ventasDetalle.total) AS total,
                    round(sum(ventasDetalle.cantidad),3) as cantidad
                FROM
                    ventasDetalle
                    inner join ventas on(ventasDetalle.idVentas=ventas.id)
                where ventas.idEmpresas=" . $params['idEmpresas'] . " and fechaFactura=curdate()
                group by sku
                order by sum(ventasDetalle.total)  desc limit 10;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/*
	         * MEOTODO TOTAL DTES
					 * ESTE METODO AHORA ACOMULADO EL SALDO DE LOS DTES CADA MES
					 * Y CUANDO SE REALIZA UNA NUEVA COMPRA DE DTES SE ACTUALIZA EL SALDO
					 * LA TABLA PARA ESTE METODO ES control_dte
					 * HECHO POR: RICHARD ORTIZ DIC. 2024
*/
	public function totalDtes($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                e.nombreComercial AS sucursal,
								YEAR(v.fechaFactura) AS periodo,
								MONTH(v.fechaFactura) AS mes,
								(
									SELECT SUM(c.dteComprados)
									FROM control_dte c
									WHERE c.idEmpresas = v.idEmpresas
										AND (c.periodo < YEAR(v.fechaFactura)
											OR (C.periodo = YEAR(v.fechaFactura)
												AND c.mes <= MONTH(v.fechaFactura)))
								) AS dteCompradosAcomulados,
								COUNT(*) AS totalUtilizados,
								(
									SELECT SUM(c.dteComprados)
									FROM control_dte c
									WHERE c.idEmpresas = v.idEmpresas
										AND (c.periodo < YEAR(v.fechaFactura)
											OR (c.periodo = YEAR(v.fechaFactura)
												AND c.mes <= MONTH(v.fechaFactura)))
								) - COUNT(*) AS saldoDTE,
								COUNT(DISTINCT DAY(v.fechaFactura)) AS diasFacturados,
								CEIL(COUNT(*) / COUNT(DISTINCT DAY(v.fechaFactura))) AS facturacionPorDia
            FROM
                ventas v
            INNER JOIN
                empresas e ON v.idEmpresas = e.id
            WHERE
                v.idEmpresas =" . $params['idEmpresas'] . " 
						AND v.fechaFactura >= '2022-07-01'
						AND v.fechaFactura <= CURDATE()
						AND v.autorizacionDTE IS NOT NULL
						GROUP BY
							v.idEmpresas, YEAR(v.fechaFactura), MONTH(v.fechaFactura)
							ORDER BY
								YEAR(v.fechaFactura), MONTH(v.fechaFactura)";
		$query = mysql_query($sql, dbCon::conPrincipal());
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO top10mes
	 *
	 */
	public function top10mes($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    sku,
                    SUBSTRING(descLarga, 1, 20) AS descLarga,
                    SUM(ventasDetalle.total) AS total,
                    round(sum(ventasDetalle.cantidad),3) as cantidad
                FROM
                    ventasDetalle
                    inner join ventas on(ventasDetalle.idVentas=ventas.id)
                where ventas.idEmpresas=" . $params['idEmpresas'] . " and year(fechaFactura)=year(curdate()) and month(fechaFactura)=month(curdate())
                group by sku
                order by sum(ventasDetalle.total)  desc limit 10;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO ultimas10Transacciones
	 *
	 */
	public function ultimas10Transacciones($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    total, TIME(created_at) AS hora
                FROM
                    ventas
                WHERE
                    ventas.idEmpresas = " . $params['idEmpresas'] . "
                        AND fechaFactura = CURDATE()
                ORDER BY id desc
                LIMIT 10;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO ultimos7dias
	 *
	 */
	public function ultimos7dias($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    date_format(fechaFactura,'%d-%m-%Y') as fecha,
                    sum(total) as total
                FROM
                    ventas
                WHERE
                    ventas.idEmpresas = " . $params['idEmpresas'] . "
                group by
                        fechaFactura
                ORDER BY id desc
                LIMIT 7;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO currentYear
	 *
	 */
	public function currentYear($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    case MONTH(fechaFactura)
                      when 1 then 'ENERO'
                      when 2 then 'FEBRERO'
                      when 3 then 'MARZO'
                      when 4 then 'ABRIL'
                      when 5 then 'MAYO'
                      when 6 then 'JUNIO'
                      when 7 then 'JULIO'
                      when 8 then 'AGOSTO'
                      when 9 then 'SEPTIEMBRE'
                      when 10 then 'OCTUBRE'
                      when 11 then 'NOVIEMBRE'
                      when 12 then 'DICIEMBRE'
                    end as mes,
                    SUM(total) AS total
                FROM
                    ventas
                WHERE
                    ventas.idEmpresas = " . $params['idEmpresas'] . "
                        AND YEAR(fechaFactura) = YEAR(CURDATE())
                GROUP BY MONTH(fechaFactura)
                ORDER BY MONTH(fechaFactura) DESC;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO consultarResumenCompras
	 *
	 */
	public function consultarResumenCompras($params)
	{
		$this->resultado = null;
		$sql = "SELECT
                    compras.idCentrosCosto,
                    centrosCosto.descripcion AS centroCosto,
                    centrosCosto.presupuestoMensual as presupuestado,
                    ifnull(presupuestos." . $params['campo'] . ",0) as presupuestado,
                    sum(total) as ejecutado,
                    (ifnull(presupuestos." . $params['campo'] . ",0)-sum(total)) as diferencia,
                    (select sum(total) from compras where compras.idEmpresas=" . $params['idEmpresas'] . " and year(fechaFactura)='" . $params['periodo'] . "' and month(fechaFactura)='" . $params['mes'] . "') as total,
                    round((sum(total)/(select sum(total) from compras where compras.idEmpresas=" . $params['idEmpresas'] . " and year(fechaFactura)='" . $params['periodo'] . "' and month(fechaFactura)='" . $params['mes'] . "'))*100,2) as 'porcentaje_sobre_total',
                    count(compras.id) as noGastos,
                    round(sum(total)/count(compras.id),2) as gastoPromedio
                FROM
                    compras
                        INNER JOIN
                    centrosCosto ON (compras.idCentrosCosto = centrosCosto.id)
                        LEFT JOIN
                    presupuestos on(presupuestos.idCentrosCosto=centrosCosto.id)
                WHERE
                    compras.idEmpresas=" . $params['idEmpresas'] . "
                    and year(fechaFactura)='" . $params['periodo'] . "' and month(fechaFactura)='" . $params['mes'] . "'
                GROUP BY compras.idCentrosCosto;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO ventasHoy
	 *
	 */
	public function comprasHoy($params)
	{
		$this->resultado = null;
		$filter = "";
		switch ($params['option']) {
			case 'Hoy':
				$filter = "and fechaFactura = CURDATE()";
				break;
			case 'Ayer':
				$filter = "and fechaFactura=DATE_ADD(CURDATE(), INTERVAL -1 DAY)";
				break;
			case 'MesHoy':
				$filter = "and fechaFactura between concat(year(curdate()),'-',month(curdate()),'-01') and concat(year(curdate()),'-',month(curdate()),'-',day(curdate()))";
				break;
			case 'MesAyer':
				$filter = "and fechaFactura between concat(year(curdate()),'-',month(curdate())-1,'-01') and concat(year(curdate()),'-',month(curdate())-1,'-',day(curdate()))";
				break;
			case 'UltimaTransaccion':
				$filter = "and fechaFactura = CURDATE() order by id desc limit 1";
				break;
		}
		$sql = "SELECT
                ifnull(SUM(total),0) AS total
            FROM
                compras
            WHERE
                idEmpresas = " . $params['idEmpresas'] . " " . $filter . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO utilidad
	 *
	 */
	public function utilidad($params)
	{
		$this->resultado = null;
		$filter = "";
		switch ($params['option']) {
			case 'MesHoy':
				$filter = "and fecha between concat(year(curdate()),'-',month(curdate()),'-01') and concat(year(curdate()),'-',month(curdate()),'-',day(curdate()))";
				break;
			case 'MesAyer':
				$filter = "and fecha between concat(year(curdate()),'-',month(curdate())-1,'-01') and concat(year(curdate()),'-',month(curdate())-1,'-',day(curdate()))";
				break;
		}
		$sql = "SELECT
                    year(fecha) as periodo,month(fecha) as mes,format(sum(ingreso),2) as ingresos,format(sum(salida),2) as salida,
                    (sum(ingreso)-sum(salida)) as utilidad,
                    ifnull(round((1-(sum(salida)/sum(ingreso)))*100,0),0) as margen
                FROM
                    (SELECT
                        'VENTA' AS transaccion,
                            fechaFactura AS fecha,
                            total AS ingreso,
                            0 AS salida,
                            nit,
                            nombre,
                            idEmpresas
                    FROM
                        ventas
                    WHERE
                        anulacion = 0 UNION ALL SELECT
                        'COMPRA' AS transaccion,
                            fechaFactura AS fecha,
                            0 AS ingreso,
                            total AS salida,
                            nitP,
                            descripcion,
                            compras.idEmpresas
                    FROM
                        compras
                    INNER JOIN proveedores ON (compras.idProveedores = proveedores.id)) AS t
                WHERE
                    idEmpresas = " . $params['idEmpresas'] . " " . $filter . ";";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}

	/** METODO ventasHoy
	 *
	 */
	public function resumenInventario($params)
	{
		$this->resultado = null;
		$sql = "select
                    sum(costo) as costo,
                    sum(venta) as venta
                from (SELECT
                    ifnull((select saldo from inventarios where ingresoA=2 and idProductos=productos.id order by id desc limit 1),0) as existencia,
                    (ifnull((select saldo from inventarios where ingresoA=2 and idProductos=productos.id order by id desc limit 1),0)*precioCosto) as costo,
                    (ifnull((select saldo from inventarios where ingresoA=2 and idProductos=productos.id order by id desc limit 1),0)*precioPublico) as venta,
                    idEmpresas
                FROM
                    productos) as t
                WHERE
                    idEmpresas = " . $params['idEmpresas'] . " and existencia>0;";
		//echo $sql . "<br/>";
		$query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
		while ($reg = mysql_fetch_assoc($query)) {
			$this->resultado[] = $reg;
		}
		return $this->resultado;
	}
}
