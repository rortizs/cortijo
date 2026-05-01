<?php

session_start();
require_once("../../models/reportes.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$reportes = new reportes();
$workbook = new Workbook("-");
$filaHoja = 2;
$workbook->HeaderingExcel('reporteAbonos.xls');
$worksheet1 = & $workbook->add_worksheet('Reporte de Abonos');
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$reporte = $reportes->reporteAbonos($_REQUEST);
//FORMATOS CELDAS
$formato_encabezado = & $workbook->add_format();
$formato_encabezado->set_size(8);
$formato_encabezado->set_align('center');
$formato_encabezado->set_color('white');
$formato_encabezado->set_pattern();
$formato_encabezado->set_bold();
$formato_encabezado->set_fg_color('gray');
$formato_encabezado->set_border(1);
$formato_encabezado->set_border_color('black');
//
$formato_celda = & $workbook->add_format();
$formato_celda->set_size(8);
$formato_celda->set_align('center');
$formato_celda->set_color('black');
$formato_celda->set_pattern();
$formato_celda->set_fg_color('white');
$formato_celda->set_border(1);
$formato_celda->set_border_color('gray');

//ENCABEZADOS
$worksheet1->write_string(0, 0, 'Consulta de Abonos Rango de Fechas ' . $_REQUEST['fechaInicio'] . ' al ' . $_REQUEST['fechaFin'] . '', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, 10);
$worksheet1->write_string(1, 0, 'Fecha Deposito', $formato_encabezado);
$worksheet1->write_string(1, 1, 'No. Boleto', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Nombre Cliente', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Monto', $formato_encabezado);
$worksheet1->write_string(1, 4, 'Ano Factura', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Mes Factura', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Fecha Factura', $formato_encabezado);
$worksheet1->write_string(1, 7, 'Fac. Liquidada', $formato_encabezado);
$worksheet1->write_string(1, 8, 'Monto Abonado', $formato_encabezado);
$worksheet1->write_string(1, 9, 'Total Factura', $formato_encabezado);
$worksheet1->write_string(1, 10, 'Saldo', $formato_encabezado);
$totalAnticipo = 0;
$totalSaldo = 0;
$totalVenta = 0;
//CONTENIDO
foreach ($reporte as $key => $value) {
    $worksheet1->write_string($filaHoja, 0, $value['fechaDeposito'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['noBoleta'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['nombreDeposito'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, number_format($value['monto'],2), $formato_celda);
    $worksheet1->write_string($filaHoja, 4, $value['yearFactura'], $formato_celda);
    $worksheet1->write_string($filaHoja, 5, $value['monthFactura'], $formato_celda);
    $worksheet1->write_string($filaHoja, 6, $value['fechaFactura'], $formato_celda);
    $worksheet1->write_string($filaHoja, 7, $value['facLiquidadas'], $formato_celda);
    $worksheet1->write_string($filaHoja, 8, number_format($value['abono'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 9, number_format($value['totalFactura'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 10, number_format($value['saldo'], 2), $formato_celda);
    $filaHoja++;
}
/*
$worksheet1->write_string($filaHoja, 0, 'Totales', $formato_encabezado);
$worksheet1->merge_cells($filaHoja, 0, $filaHoja, 7);
$worksheet1->write_string($filaHoja, 8, number_format($totalAnticipo, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 9, number_format($totalSaldo, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 10, number_format($totalVenta, 2), $formato_encabezado);
 *
 */
$workbook->close();
?>

