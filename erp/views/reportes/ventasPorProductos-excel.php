<?php

session_start();
require_once("../../models/reportes.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$reportes = new reportes();
$workbook = new Workbook("-");
$filaHoja = 2;
$workbook->HeaderingExcel('ventasPorProducto.xls');
$worksheet1 = & $workbook->add_worksheet('REPORTE VENTAS POR PRODUCTO');
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$reporte = $reportes->ventasPorProducto($_REQUEST);
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
$worksheet1->write_string(0, 0, 'REPORTE VENTAS POR PRODUCTO', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, 7);
$worksheet1->write_string(1, 0, 'CODIGO', $formato_encabezado);
$worksheet1->write_string(1, 1, 'DESCRIPCION', $formato_encabezado);
$worksheet1->write_string(1, 2, 'CANTIDAD', $formato_encabezado);
$worksheet1->write_string(1, 3, 'TOTAL VENTA', $formato_encabezado);
$worksheet1->write_string(1, 4, 'TOTAL COSTO', $formato_encabezado);
$worksheet1->write_string(1, 5, 'UTILIDAD', $formato_encabezado);
$worksheet1->write_string(1, 6, 'MARGEN', $formato_encabezado);
$worksheet1->write_string(1, 7, 'VENDEDOR', $formato_encabezado);
$total1 = 0;
$total2 = 0;
$total3 = 0;
$total4 = 0;
//CONTENIDO
foreach ($reporte as $key => $value) {
    $total1 += $value['cantidad'];
    $total2 += $value['totalVenta'];
    $total3 += $value['totalCosto'];
    $total4 += $value['utilidad'];
    //
    $worksheet1->write_string($filaHoja, 0, $value['codigo'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['descripcion'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, number_format($value['cantidad'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 3, number_format($value['totalVenta'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 4, number_format($value['totalCosto'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 5, number_format($value['utilidad'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 6, number_format($value['margen'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 7, $value['vendedor'], 2, $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'TOTALES', $formato_encabezado);
$worksheet1->merge_cells($filaHoja, 0, $filaHoja, 1);
$worksheet1->write_string($filaHoja, 2, number_format($total1, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 3, number_format($total2, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 4, number_format($total3, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 5, number_format($total4, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 6, '', $formato_encabezado);
$worksheet1->write_string($filaHoja, 7, '', $formato_encabezado);
$workbook->close();
?>

