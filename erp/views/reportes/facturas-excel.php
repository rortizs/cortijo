<?php

session_start();
require_once("../../models/reportes.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$reportes = new reportes();
$workbook = new Workbook("-");
$filaHoja = 2;
$workbook->HeaderingExcel('Consulta de Facturacion' . $_REQUEST['idPuntoIngresoTxt'] . '.xls');
$worksheet1 = & $workbook->add_worksheet('Consulta de Facturacion');
$_REQUEST['idEmpresas']=$_SESSION['idEmpresa'];
$existencias = $reportes->consultarFacturas($_REQUEST);
//FORMATOS CELDAS
$formato_encabezado = & $workbook->add_format();
$formato_encabezado->set_size(10);
$formato_encabezado->set_align('center');
$formato_encabezado->set_color('white');
$formato_encabezado->set_pattern();
$formato_encabezado->set_bold();
$formato_encabezado->set_fg_color('gray');
$formato_encabezado->set_border(1);
$formato_encabezado->set_border_color('black');
//
$formato_celda = & $workbook->add_format();
$formato_celda->set_size(10);
$formato_celda->set_align('center');
$formato_celda->set_color('black');
$formato_celda->set_pattern();
$formato_celda->set_fg_color('white');
$formato_celda->set_border(1);
$formato_celda->set_border_color('gray');

//ENCABEZADOS
$worksheet1->write_string(0, 0, 'Consulta de Facturacion' . $_REQUEST['idPuntoIngresoTxt'] . '', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, 11);
$worksheet1->write_string(1, 0, 'Tipo de Venta', $formato_encabezado);
$worksheet1->write_string(1, 1, 'fecha', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Documento', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Nit', $formato_encabezado);
$worksheet1->write_string(1, 4, 'Nombre', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Direccion', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Subtotal', $formato_encabezado);
$worksheet1->write_string(1, 7, 'Descuento', $formato_encabezado);
$worksheet1->write_string(1, 8, 'Descuento P', $formato_encabezado);
$worksheet1->write_string(1, 9, 'Costo Total', $formato_encabezado);
$worksheet1->write_string(1, 10, 'Utilidad', $formato_encabezado);
$worksheet1->write_string(1, 11, 'Margen', $formato_encabezado);
$worksheet1->write_string(1, 12, 'Cajero', $formato_encabezado);
$worksheet1->write_string(1, 13, 'Status Factura', $formato_encabezado);
//CONTENIDO
foreach ($existencias as $key => $value) {
    $worksheet1->write_string($filaHoja, 0, $value['tipoVenta'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['fecha'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['documento'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, $value['nit'], $formato_celda);
    $worksheet1->write_string($filaHoja, 4, $value['nombre'], $formato_celda);
    $worksheet1->write_string($filaHoja, 5, $value['direccion'], $formato_celda);
    $worksheet1->write_string($filaHoja, 6, $value['subTotal'], $formato_celda);
    $worksheet1->write_string($filaHoja, 7, $value['descuento'], $formato_celda);
    $worksheet1->write_string($filaHoja, 8, $value['descuentoP'], $formato_celda);
    $worksheet1->write_string($filaHoja, 9, $value['totalCosto'], $formato_celda);
    $worksheet1->write_string($filaHoja, 10, $value['utilidad'], $formato_celda);
    $worksheet1->write_string($filaHoja, 11, $value['margen'], $formato_celda);
    $worksheet1->write_string($filaHoja, 12, $value['cajero'], $formato_celda);
    $worksheet1->write_string($filaHoja, 13, $value['statusFactura'], $formato_celda);
    $filaHoja++;
}
$workbook->close();
?>

