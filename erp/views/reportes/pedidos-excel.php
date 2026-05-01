<?php

session_start();
require_once("../../models/caja.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$caja = new Caja();
$workbook = new Workbook("-");
$filaHoja = 2;
$workbook->HeaderingExcel('Pedidos' . $_REQUEST['idPuntoIngresoTxt'] . '.xls');
$worksheet1 = & $workbook->add_worksheet('Reporte Pedidos');
$_REQUEST['idEmpresas']=$_SESSION['idEmpresa'];
$existencias = $caja->consultarPedidos($_REQUEST);
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
$worksheet1->write_string(0, 0, 'Reporte de Pedido' . $_REQUEST['idPuntoIngresoTxt'] . '', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, 10);
$worksheet1->write_string(1, 0, 'Fecha', $formato_encabezado);
$worksheet1->write_string(1, 1, 'No. Documento', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Nit', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Nombre Cliente', $formato_encabezado);
$worksheet1->write_string(1, 4, 'Direccion', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Observaciones', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Sucursal', $formato_encabezado);
$worksheet1->write_string(1, 7, 'Vendedor', $formato_encabezado);
$worksheet1->write_string(1, 8, 'Estatus', $formato_encabezado);  
$worksheet1->write_string(1, 9, 'No. Doc Venta', $formato_encabezado);
$worksheet1->write_string(1, 10, 'Total', $formato_encabezado);
//CONTENIDO
foreach ($existencias as $key => $value) {
    $worksheet1->write_string($filaHoja, 0, $value['fecha'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $value['documento'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $value['nit'], $formato_celda);
    $worksheet1->write_string($filaHoja, 3, $value['nombre'], $formato_celda);
    $worksheet1->write_string($filaHoja, 4, $value['direccion'], $formato_celda);
    $worksheet1->write_string($filaHoja, 5, $value['vendedor'], $formato_celda);
    $worksheet1->write_string($filaHoja, 6, $value['observaciones'], $formato_celda);   
    $worksheet1->write_string($filaHoja, 7, $value['idSucursales'], $formato_celda);
    $worksheet1->write_string($filaHoja, 8, $value['estado'], $formato_celda);
    $worksheet1->write_string($filaHoja, 9, $value['noVentas'], $formato_celda);
    $worksheet1->write_string($filaHoja, 10, $value['total'], $formato_celda);
    $filaHoja++;
}
$workbook->close();
?>

