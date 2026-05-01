<?php
require_once("../../clases/librerias/dompdf060/dompdf_config.inc.php");
require_once("../../clases/cobros.php");
$dompdf = new Dompdf();
$cobros = new Cobros();
$idClientes=$_REQUEST['idClientes'];
$generarBoleta=$cobros->generarBoleta($idClientes);
$html='
<html>
<head>
<title>Boleta de pago de credito</title>
<style>
body{
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>
<body>
<table width="100%">
	<tr>
		<td>
		REPRESENTACIONES MENORAH<br/>
		Direccion: <br/>
		Telefono:
		</td>
		<td align="right">
		<strong>No. Boleta: '.$generarBoleta['noBoleta'].'</strong>
		</td>
	</tr>
</table>
<center>
	<h2>Boleta de pago de credito</h2>
</center>
<table width="100%" border="0">
	<tr>
		<td style="width:20% !important;"><b>Recibimos de:</b></td>
		<td colspan="2">'.$generarBoleta['nombreC'].'</td>
	</tr>
	<tr>
		<td><b>La cantidad de:</b></td>
		<td colspan="2">'.$generarBoleta['abonos'].' Quetzales</td>
	</tr>
	<tr>
		<td><b>Por concepto de:</b></td>
		<td colspan="2">PAGO DE CREDITO</td>
	</tr>
	<tr>
		<td align="right" colspan="3"><strong>Saldo Actual: Q'.$generarBoleta['saldo_actual'].'</td>
	</tr>
	<tr>
		<td align="right" colspan="3">
			Fecha de pago: '.$generarBoleta['fechaAbono'].'
		</td>
	</tr>
</table>
</body>
</html>';
$dompdf->set_paper("Letter", "portrait");
$dompdf->load_html(utf8_decode($html));
$dompdf->render();
$dompdf->stream('boleta.pdf',array('Attachment'=>0));
?>