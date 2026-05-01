<?php

session_start();
require_once("../../models/reportes.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$reportes = new Reportes();
$workbook = new Workbook("-");
$filaHoja = 2;
$workbook->HeaderingExcel('reporteExistencias.xls');
$worksheet1 = & $workbook->add_worksheet('Reporte Existencias');
$existencias = $reportes->existencias($_REQUEST);
//print_r($existencias);
//exit();
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
$worksheet1->write_string(0, 0, 'Reporte de Existencias', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, 8);
$worksheet1->write_string(1, 0, 'Codigo', $formato_encabezado);
$worksheet1->write_string(1, 1, 'Descripcion', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Existencia', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Marca', $formato_encabezado);
$worksheet1->write_string(1, 4, 'Familia Nivel 1', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Familia Nivel 2', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Familia Nivel 3', $formato_encabezado);
$worksheet1->write_string(1, 7, $_REQUEST['tipoValorTxt'], $formato_encabezado);
$worksheet1->write_string(1, 8, $_REQUEST['tipoValorTxt'].' Total', $formato_encabezado);
//CONTENIDO
$piezas = 0;
$costo = 0;
$costoTotal = 0;
foreach ($existencias as $key => $value) {
    $unidadesTotal += $value['saldo'];
    $costoTotal += ($value['saldo'] * $value[$_REQUEST['tipoValor']]);
    $worksheet1->write_string($filaHoja, 0, $value['sku'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['descLarga'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, number_format($value['saldo']), $formato_celda);
    $worksheet1->write_string($filaHoja, 3, $value['idMarcas'], $formato_celda);
    $worksheet1->write_string($filaHoja, 4, $value['idFamiliaNivel1'], $formato_celda);
    $worksheet1->write_string($filaHoja, 5, $value['idFamiliaNivel2'], $formato_celda);
    $worksheet1->write_string($filaHoja, 6, $value['idFamiliaNivel3'], $formato_celda);
    $worksheet1->write_string($filaHoja, 7, number_format($value[$_REQUEST['tipoValor']],4), $formato_celda);
    $worksheet1->write_string($filaHoja, 8, number_format(($value['saldo'] * $value[$_REQUEST['tipoValor']]),4), $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'Totales', $formato_encabezado);
$worksheet1->merge_cells($filaHoja, 0, $filaHoja, 1);
$worksheet1->write_string($filaHoja, 2, number_format($unidadesTotal), $formato_encabezado);
$worksheet1->write_string($filaHoja, 3, '', $formato_encabezado);
$worksheet1->merge_cells($filaHoja, 3, $filaHoja, 6);
$worksheet1->write_string($filaHoja, 7, '', $formato_encabezado);
$worksheet1->write_string($filaHoja, 8, number_format($costoTotal,2), $formato_encabezado);
$workbook->close();
?>