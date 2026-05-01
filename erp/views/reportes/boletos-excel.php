<?php

session_start();
require_once("../../models/reportes.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$reportes = new reportes();
$workbook = new Workbook("-");
$filaHoja = 2;
$workbook->HeaderingExcel('Consulta de Boletos' . $_REQUEST['fechaInicio'] . '-' . $_REQUEST['fechaFin'] . '.xls');
$worksheet1 = & $workbook->add_worksheet('Consulta de Boletos');
$_REQUEST['idEmpresas'] = $_SESSION['idEmpresa'];
$boletos = $reportes->consultarBoletos($_REQUEST);
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
$worksheet1->merge_cells(0, 0, 0, 15);
$worksheet1->write_string(1, 0, 'Prov', $formato_encabezado);
$worksheet1->write_string(1, 1, 'Cod Cliente', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Pagare', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Boleto', $formato_encabezado);
$worksheet1->write_string(1, 4, 'Fecha', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Pasajero', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Tarifa', $formato_encabezado);
$worksheet1->write_string(1, 7, 'Imp.Nac', $formato_encabezado);
$worksheet1->write_string(1, 8, 'Imp.Ext', $formato_encabezado);
$worksheet1->write_string(1, 9, 'Total', $formato_encabezado);
$worksheet1->write_string(1, 10, 'Tarjeta', $formato_encabezado);
$worksheet1->write_string(1, 11, 'Comision', $formato_encabezado);
$worksheet1->write_string(1, 12, 'Iva', $formato_encabezado);
$worksheet1->write_string(1, 13, 'A Pagar', $formato_encabezado);
$worksheet1->write_string(1, 14, 'Tasa', $formato_encabezado);
$worksheet1->write_string(1, 15, 'Vendedor', $formato_encabezado);
//CONTENIDO

$totalTarifa = 0;
$totalImpuestoNacional = 0;
$totalImpuestoExtranjero = 0;
$totalTotal = 0;
$totalTarjeta = 0;
$totalComision = 0;
$totalIva = 0;
$totalPagar = 0;
foreach ($boletos as $key => $value) {

    $totalTarifa += $value['tarifa'];
    $totalImpuestoNacional += $value['impNacional'];
    $totalImpuestoExtranjero += $value['impExtranjero'];
    $totalTotal += $value['total'];
    $totalTarjeta += $value['tarjeta'];
    $totalComision += $value['comision'];
    $totalIva += $value['iva'];
    $totalPagar += $value['aPagar'];
    $worksheet1->write_string($filaHoja, 0, $value['codigoLineaAerea'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['codCliente'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['pagare'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, $value['boleto'], $formato_celda);
    $worksheet1->write_string($filaHoja, 4, $value['fecha'], $formato_celda);
    $worksheet1->write_string($filaHoja, 5, $value['pasajero'], $formato_celda);
    $worksheet1->write_string($filaHoja, 6, number_format($value['tarifa'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 7, number_format($value['impNacional'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 8, number_format($value['impExtranjero'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 9, number_format($value['total'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 10, number_format($value['tarjeta'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 11, number_format($value['comision'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 12, number_format($value['iva'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 13, number_format($value['aPagar'], 2), $formato_celda);
    $worksheet1->write_string($filaHoja, 14, $value['tasa'], $formato_celda);
    $worksheet1->write_string($filaHoja, 15, $value['codVendedor'], $formato_celda);
    $filaHoja++;
}
$worksheet1->write_string($filaHoja, 0, 'TOTALES', $formato_encabezado);
$worksheet1->write_string($filaHoja, 1, '-', $formato_encabezado);
$worksheet1->write_string($filaHoja, 2, '-', $formato_encabezado);
$worksheet1->write_string($filaHoja, 3, '-', $formato_encabezado);
$worksheet1->write_string($filaHoja, 4, '-', $formato_encabezado);
$worksheet1->write_string($filaHoja, 5, '-', $formato_encabezado);
$worksheet1->write_string($filaHoja, 6, number_format($totalTarifa, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 7, number_format($totalImpuestoNacional, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 8, number_format($totalImpuestoExtranjero, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 9, number_format($totalTotal, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 10, number_format($totalTarjeta, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 11, number_format($totalComision, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 12, number_format($totalIva, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 13, number_format($totalPagar, 2), $formato_encabezado);
$worksheet1->write_string($filaHoja, 14, '-', $formato_encabezado);
$worksheet1->write_string($filaHoja, 15, '-', $formato_encabezado);
$workbook->close();
?>