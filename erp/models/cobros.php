	<?php

/**
 * POS /Modulo Inventarios - Class Inventarios
 * @author Jonathan Juarez
 * @version 1.0 20140909
 */
require_once("dbCon.php");
require_once("general.php");

class Cobros extends General {
	/** METODO GET CLIENTES
	 * 
	 */
	public function getClientes(){
		$this->resultado = null;
		$sql="select * from infoCliente where tipoCliente=1;";
		$query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
	}
	
	
    /** METODO GET ESTADO DE CUENTA
     * 
     */
    public function estadoCuenta($nit) {
        $this->resultado = null;
        $sql = "SELECT 
					ventasCredito.*,
					usuarios.userName,
					sucursales.nombreSucursal,
					concat(ventas.serieFactura,'-',ventas.correlativo) as factura
				FROM
					ventasCredito inner join clientes on(ventasCredito.idClientes=clientes.id)
					left join usuarios on(ventasCredito.idUsuarios=usuarios.id)
					left join sucursales on(usuarios.idSucursales=sucursales.id)
					left join ventas on(ventasCredito.idVentas=ventas.id)
				WHERE
					clientes.nitC='".$nit."' order by timestamp(ventasCredito.created_at) desc;";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }
	
	/** METODO INGRESAR ABONO
	 * 
	 */
	public function ingresarAbono($idClientes,$saldoAnterior,$abono,$idTipoTransacciones,$idUsuarios) {
		$noBoleta=$this->correlativoBoleta();
		$sql = "insert into ventasCredito(idClientes,saldo_anterior,abonos,saldo_actual,idTipoTransacciones,noBoleta,idUsuarios,created_at)
				values(".$idClientes.",'".$saldoAnterior."','".$abono."','".($saldoAnterior-$abono)."',".$idTipoTransacciones.",'".$noBoleta."',".$idUsuarios.",'".date('Y-m-d H:i:s')."');";				
        $query = mysql_query($sql, dbCon::conPrincipal());
		if ($query == true) {
            $message = "<div class='alert alert-success' id='message' role='alert'>Abono ingresado exitosamente</div>";
            return $message;
        } else {
            $message = "<div class='alert alert-danger' id='message' role='alert'>Error al ingresar el abono, por favor intentelo mas tarde...</div>";
            return $message;
        }
	}
	
	/** METODO GENERAR BOLETA
	 *  
	 */	 
	public function generarBoleta($idClientes){
		$sql="SELECT 
				    clientes.nombreC,
				    ventasCredito.*,
				    Date_format(ventasCredito.created_at,'%d-%m-%Y %h:%i:%s %p') as fechaAbono
				FROM
				    ventasCredito inner join clientes on(ventasCredito.idClientes=clientes.id)
				WHERE
				    idClientes = ".$idClientes." and idTipoTransacciones=2
				ORDER BY TIMESTAMP(created_at) DESC
				LIMIT 1;";
		$query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }	
	}
	
	/** CORRELATIVO ORDEN
     * 
     */
    public function correlativoBoleta() {
        $sql = "select noBoleta from correlativos;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_array($query);
        $noOrden = '';
        $preOrden = $reg['noBoleta'] + 1;
        if (strlen($preOrden) == 1) {
            $noOrden = '000' . $preOrden;
        } else if (strlen($preOrden) == 2) {
            $noOrden = '00' . $preOrden;
        } else if (strlen($preOrden) == 3) {
            $noOrden = '0' . $preOrden;
        } else if (strlen($preOrden) == 4) {
            $noOrden = $preOrden;
        }
        $sql2 = "update correlativos set noBoleta='" . $noOrden . "';";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
        $sql3 = "select noBoleta from correlativos;";
        $query3 = mysql_query($sql3, dbCon::conPrincipal());
        $reg3 = mysql_fetch_array($query3);
        return $reg3['noBoleta'];
    }
}
