<?php

session_start();
require_once("../../models/reportes.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$reportes = new reportes();
$workbook = new Workbook("-");
$filaHoja = 2;
$workbook->HeaderingExcel('Consulta de Facturacion Detallado' . $_REQUEST['idPuntoIngresoTxt'] . '.xls');
$worksheet1 = & $workbook->add_worksheet('Facturacion Detallado');
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$existencias = $reportes->consultarFacturasDetallado($_REQUEST);
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
$worksheet1->write_string(0, 0, 'Consulta de Facturacion' . $_REQUEST['idPuntoIngresoTxt'] . '', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, 13);
$worksheet1->write_string(1, 0, 'Fecha', $formato_encabezado);
$worksheet1->write_string(1, 1, 'Documento', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Cliente', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Vendedor', $formato_encabezado);
$worksheet1->write_string(1, 4, 'Total Documento', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Codigo Producto', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Desc. Producto', $formato_encabezado);
$worksheet1->write_string(1, 7, 'Cantidad', $formato_encabezado);
$worksheet1->write_string(1, 8, 'Costo', $formato_encabezado);
$worksheet1->write_string(1, 9, 'Precio Venta', $formato_encabezado);
$worksheet1->write_string(1, 10, 'Total Venta', $formato_encabezado);
$worksheet1->write_string(1, 11, 'Total Costo', $formato_encabezado);
$worksheet1->write_string(1, 12, 'Utilidad', $formato_encabezado);
$worksheet1->write_string(1, 13, 'Margen', $formato_encabezado);
$total1 = 0;
$total2 = 0;
$total3 = 0;
$total4 = 0;
//CONTENIDO
foreach ($existencias as $key => $value) {
    $total1 += $value['cantidad'];
    $total2 += $value['total'];
    $total3 += $value['totalCosto'];
    $total4 += $value['totalNeto'];
    //
    $worksheet1->write_string($filaHoja, 0, $value['fechaFactura'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['documento'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['cliente'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, $value['vendedor'], $formato_celda);
    $worksheet1->write_string($filaHoja, 4, $value['totalDocumento'], $formato_celda);
    $worksheet1->write_string($filaHoja, 5, $value['sku'], $formato_celda);
    $worksheet1->write_string($filaHoja, 6, $value['descLarga'], $formato_celda);
    $worksheet1->write_string($filaHoja, 7, number_format($value['cantidad'], 0), $formato_celda);
    $worksheet1->write_string($filaHoja, 8, $value['costo'], $formato_celda);
    $worksheet1->write_string($filaHoja, 9, number_format($value['precio'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 10, number_format($value['total'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 11, number_format($value['totalCosto'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 12, number_format($value['utilidad'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 13, number_format($value['margen'], 2), $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'Total Facturacion', $formato_encabezado);
$worksheet1->merge_cells($filaHoja, 0, $filaHoja, 6);
$worksheet1->write_string($filaHoja, 7, number_format($total1, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 8, '-', $formato_encabezado);
$worksheet1->write_string($filaHoja, 9, '-', $formato_encabezado);
$worksheet1->write_string($filaHoja, 10, number_format($total2, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 11, number_format($total3, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 12, number_format($total4, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 13, number_format((($total2 - $total3) / $total2) * 100, 2), $formato_encabezado);
$workbook->close();
?>

