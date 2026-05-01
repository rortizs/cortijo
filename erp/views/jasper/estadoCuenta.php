<?php
session_start();
require_once ("../../clases/cobros.php");
$cobros = new Cobros();
$nit = $_REQUEST['nit'];
$message="";
//
if($_REQUEST['flag']==1){
	$ingresarAbono=$cobros->ingresarAbono($_REQUEST['idClientes'],$_REQUEST['saldo_anterior'],$_REQUEST['abonos'],$_REQUEST['idTipoTransacciones'],$_SESSION['idUsuarios']);
	$message=$ingresarAbono;
}
$estadoCuenta = $cobros -> estadoCuenta($nit);
?>
<?=$message;?>
<fieldset class="scheduler-border">
	<legend class="scheduler-border">
		Estado de Cuenta
	</legend>
	<button class="btn btn-primary" onclick="loadCobros();">
		Regresar a listado de clientes
	</button>
	<button class="btn btn-primary" onclick="exportarEstadoCuenta();">
		Exportar estado cuenta
	</button>
	<div class="clear">&nbsp;</div>
	<table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="80%">
		<thead>
			<tr class="info text-center">
				<td>fecha</td>
				<td>saldo anterior</td>
				<td>compra</td>
				<td>abonos</td>
				<td>saldo actual</td>
				<td>no boleta</td>
				<td>observ. sistema anterior</td>
				<td>usuario receptor</td>
				<td>sucursal</td>
			</tr>
		</thead>
		<tbody>
			<?php
				for ($i=0; $i < count($estadoCuenta) ; $i++) {
					$transaccion="";
					$abono="-";
					$compra="-";
					$noDoc="";
					switch ($estadoCuenta[$i]['idTipoTransacciones']) {
						case '1':
							$transaccion="Credito";
							$compra=$estadoCuenta[$i]['saldo_actual']-$estadoCuenta[$i]['saldo_anterior'];
							$noDoc=$estadoCuenta[$i]['factura'];
							break;
						case '2':
							$transaccion="Abono";
							$abono=$estadoCuenta[$i]['abonos'];
							$noDoc=$estadoCuenta[$i]['noBoleta'];
							break;	
					} 
					?>
					<tr class="text-center">
						<td><?=date_format(date_create($estadoCuenta[$i]['created_at']),'d/m/Y H:i:s'); ?></td>
						<td><?=$estadoCuenta[$i]['saldo_anterior']; ?></td>
						<td><?=$compra; ?></td>
						<td><?=$abono; ?></td>
						<td><?=$estadoCuenta[$i]['saldo_actual']; ?></td>
						<td><?=$noDoc; ?></td>
						<td><?=$estadoCuenta[$i]['observaciones']; ?></td>
						<td><?=$estadoCuenta[$i]['userName']?:'--'; ?></td>
						<td><?=$estadoCuenta[$i]['nombreSucursal']?:'--'; ?></td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
</fieldset>