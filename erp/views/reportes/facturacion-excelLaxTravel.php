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
$existencias = $reportes->consultaFacturasLaxTravel($_REQUEST);
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
$worksheet1->write_string(1, 0, 'Fecha', $formato_encabezado);
$worksheet1->write_string(1, 1, 'Tipo de Venta', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Tipo de Facturacion', $formato_encabezado);
$worksheet1->write_string(1, 3, 'No. Factura', $formato_encabezado);
$worksheet1->write_string(1, 4, 'No. Pagare', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Nit', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Codigo Cliente', $formato_encabezado);
$worksheet1->write_string(1, 7, 'Nombre Cliente', $formato_encabezado);
$worksheet1->write_string(1, 8, 'Vendedor', $formato_encabezado);
$worksheet1->write_string(1, 9, 'Estatus', $formato_encabezado);
$worksheet1->write_string(1, 10, 'Total Cargos Por Servicio', $formato_encabezado);
$worksheet1->write_string(1, 11, 'Total', $formato_encabezado);

//CONTENIDO
$total=0;
foreach ($existencias as $key => $value) {
    $total+=$value['total'];
    $totalCargos+=$value['totalCargos'];
    $worksheet1->write_string($filaHoja, 0, $value['fecha'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['tipoVenta'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['tipoFacturacion'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, $value['documento'], $formato_celda);
    $worksheet1->write_string($filaHoja, 4, $value['pagare'], $formato_celda);
    $worksheet1->write_string($filaHoja, 5, $value['nit'], $formato_celda);
    $worksheet1->write_string($filaHoja, 6, $value['codigoCliente'], $formato_celda);
    $worksheet1->write_string($filaHoja, 7, $value['nombre'], $formato_celda);
    $worksheet1->write_string($filaHoja, 8, $value['codVendedor'], $formato_celda);
    $worksheet1->write_string($filaHoja, 9, $value['statusFactura'], $formato_celda);
    $worksheet1->write_string($filaHoja, 10, $value['totalCargos'], $formato_celda);
    $worksheet1->write_string($filaHoja, 11, $value['total'], $formato_celda);
    $filaHoja++;
}

    $worksheet1->write_string($filaHoja, 0, 'TOTALES', $formato_encabezado);
    $worksheet1->write_string($filaHoja, 1, '', $formato_encabezado);
    $worksheet1->write_string($filaHoja, 2, '',$formato_encabezado);
    $worksheet1->write_string($filaHoja, 3, '',$formato_encabezado);
    $worksheet1->write_string($filaHoja, 4, '', $formato_encabezado);
    $worksheet1->write_string($filaHoja, 5, '', $formato_encabezado);
    $worksheet1->write_string($filaHoja, 6, '', $formato_encabezado);
    $worksheet1->write_string($filaHoja, 7, '',$formato_encabezado);
    $worksheet1->write_string($filaHoja, 8, '', $formato_encabezado);
    $worksheet1->write_string($filaHoja, 9, '', $formato_encabezado);
    $worksheet1->write_string($filaHoja, 10, $totalCargos, $formato_encabezado);
    $worksheet1->write_string($filaHoja, 11, $total, $formato_encabezado);
$workbook->close();
?>

