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
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$existencias = $reportes->consultaFacturas($_REQUEST);
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
$worksheet1->write_string(1, 1, 'Tipo de Venta', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Tipo Transaccion', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Documento', $formato_encabezado);
$worksheet1->write_string(1, 4, 'Nit', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Nombre Cliente', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Sucursal', $formato_encabezado);
$worksheet1->write_string(1, 7, 'Vendedor', $formato_encabezado);
$worksheet1->write_string(1, 8, 'Estatus', $formato_encabezado);
$worksheet1->write_string(1, 9, 'Fecha Anulacion', $formato_encabezado);
$worksheet1->write_string(1, 10, 'Anticipo', $formato_encabezado);
$worksheet1->write_string(1, 11, 'Saldo', $formato_encabezado);
$worksheet1->write_string(1, 12, 'Subtotal', $formato_encabezado);
$worksheet1->write_string(1, 13, 'Iva', $formato_encabezado);
$worksheet1->write_string(1, 14, 'Total', $formato_encabezado);
$total1 = 0;
$total2 = 0;
$total3 = 0;
$total4 = 0;
$total5 = 0;
//CONTENIDO
foreach ($existencias as $key => $value) {
    $total1 += $value['anticipo'];
    $total2 += $value['saldo'];
    $total3 += $value['subtotal'];
    $total4 += $value['iva'];
    $total5 += $value['total'];
    //
    $worksheet1->write_string($filaHoja, 0, $value['fechaFactura'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['tipoVenta'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['tipoTransaccion'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, $value['serie'].'-'.$value['correlativo'], $formato_celda);
    $worksheet1->write_string($filaHoja, 4, $value['nit'], $formato_celda);
    $worksheet1->write_string($filaHoja, 5, $value['nombreCliente'], $formato_celda);
    $worksheet1->write_string($filaHoja, 6, $value['sucursal'], $formato_celda);
    $worksheet1->write_string($filaHoja, 7, $value['vendedor'], $formato_celda);
    $worksheet1->write_string($filaHoja, 8, $value['estatus'], $formato_celda);
    $worksheet1->write_string($filaHoja, 9, $value['fechaAnulacion'], $formato_celda);
    $worksheet1->write_string($filaHoja, 10, number_format($value['anticipo'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 11, number_format($value['saldo'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 12, number_format($value['subtotal'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 13, number_format($value['iva'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 14, number_format($value['total'], 2), $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'Total Facturacion', $formato_encabezado);
$worksheet1->merge_cells($filaHoja, 0, $filaHoja, 9);
$worksheet1->write_string($filaHoja, 10, number_format($total1, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 11, number_format($total2, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 12, number_format($total3, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 13, number_format($total4, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 14, number_format($total5, 2), $formato_encabezado);
$workbook->close();
?>

