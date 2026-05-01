<?php

session_start();
require_once("../../models/reportes.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$reportes = new Reportes();
$workbook = new Workbook("-");
$filaHoja = 2;
$fechaInicio = $_REQUEST['fechaInicio'];
$fechaFin = $_REQUEST['fechaFin'];
$inventario = $_REQUEST['idPuntoIngresoTxt'];
$workbook->HeaderingExcel('kardex-' . $inventario . '-' . $fechaInicio . '-' . $fechaFin . '.xls');
$worksheet1 = & $workbook->add_worksheet('Kardex');
$kardex = $reportes->kardex($_REQUEST, $_SESSION['idEmpresa']);
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
$worksheet1->write_string(0, 0, 'Kardex ' . $_REQUEST['ingresoATxt'] . '-' . $_REQUEST['idPuntoIngresoTxt'] . '', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, 10);
$worksheet1->write_string(1, 0, 'Fecha', $formato_encabezado);
$worksheet1->write_string(1, 1, 'Documento', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Inventario De', $formato_encabezado);
$worksheet1->write_string(1, 3, 'Lugar', $formato_encabezado);
$worksheet1->write_string(1, 4, 'Serie', $formato_encabezado);
$worksheet1->write_string(1, 5, 'Ingresos', $formato_encabezado);
$worksheet1->write_string(1, 6, 'Salidas', $formato_encabezado);
$worksheet1->write_string(1, 7, 'Saldo', $formato_encabezado);
$worksheet1->write_string(1, 8, 'Costo', $formato_encabezado);
$worksheet1->write_string(1, 9, 'Unidad De Medida', $formato_encabezado);
$worksheet1->write_string(1, 10, 'Observaciones', $formato_encabezado);
//CONTENIDO
$arr = "";
for ($a = 0; $a < count($kardex); $a++) {
    if ($kardex[$a]['idProductos'] !== $kardex[$a + 1]['idProductos']) {
        $arr[] = array('name' => $kardex[$a]['sku']);
    }
}
foreach ($arr as $value) {
    $worksheet1->write_string($filaHoja, 0, $value['name'], $formato_encabezado);
    $worksheet1->merge_cells($filaHoja, 0, $filaHoja, 10);
    $filaHoja++;
    for ($b = 0; $b < count($kardex); $b++) {
        if ($kardex[$b]['sku'] == $value['name']) {
            $worksheet1->write_string($filaHoja, 0, $kardex[$b]['created_at'], $formato_celda);
            $worksheet1->write_string($filaHoja, 1, $kardex[$b]['documento'], $formato_celda);
            $worksheet1->write_string($filaHoja, 2, $kardex[$b]['ingresoA'], $formato_celda);
            $worksheet1->write_string($filaHoja, 3, $kardex[$b]['idPuntoIngreso'], $formato_celda);
            $worksheet1->write_string($filaHoja, 4, $kardex[$b]['serie'], $formato_celda);
            $worksheet1->write_string($filaHoja, 5, $kardex[$b]['ingreso'], $formato_celda);
            $worksheet1->write_string($filaHoja, 6, $kardex[$b]['salida'], $formato_celda);
            $worksheet1->write_string($filaHoja, 7, $kardex[$b]['saldo'], $formato_celda);
            $worksheet1->write_string($filaHoja, 8, $kardex[$b]['costo'], $formato_celda);
            $worksheet1->write_string($filaHoja, 9, $kardex[$b]['unidadMedida'], $formato_celda);
            $worksheet1->write_string($filaHoja, 10, $kardex[$b]['observaciones'], $formato_celda);
            $filaHoja++;
        }
    }
}
//
$workbook->close();
?>