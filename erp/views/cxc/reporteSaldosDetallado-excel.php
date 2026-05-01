<?php

session_start();
require_once("../../models/reportes.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$reportes = new reportes();
$workbook = new Workbook("-");
$filaHoja = 2;
$workbook->HeaderingExcel('reporteSaldosDetallado.xls');
$worksheet1 = & $workbook->add_worksheet('Reporte de Saldos Detallado');
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$reporte = $reportes->reporteSaldosDetallado($_REQUEST);
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
$worksheet1->write_string(0, 0, 'Reporte de Saldos Detallado', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, 3);
$worksheet1->write_string(1, 0, 'Documento', $formato_encabezado);
$worksheet1->write_string(1, 1, 'Cliente', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Fecha', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Saldo', $formato_encabezado);
$totalSaldo = 0;
//CONTENIDO
foreach ($reporte as $key => $value) {
    $totalSaldo += $value['saldo'];
    $worksheet1->write_string($filaHoja, 0, $value['documento'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['cliente'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['fechaDocumento'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, number_format($value['saldo'], 2), $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'Totales', $formato_encabezado);
$worksheet1->merge_cells($filaHoja, 0, $filaHoja, 2);
$worksheet1->write_string($filaHoja, 3, number_format($totalSaldo, 2), $formato_encabezado);
$workbook->close();
?>

