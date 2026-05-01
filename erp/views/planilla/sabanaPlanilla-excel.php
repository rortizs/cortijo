<?php

session_start();
require_once("../../models/admin.php");
require_once("../../models/planilla.php");
require_once("../../models/librerias/excel/Workbook.php");
require_once("../../models/librerias/excel/Worksheet.php");
$admin = new Admin();
$planilla = new Planilla();
$workbook = new Workbook("-");
$filaHoja = 3;
$workbook->HeaderingExcel('planilla.xls');
$getMeses = $admin->getMeses();
$mes = "";
foreach ($getMeses as $value) {
    if ($value['id'] === $_REQUEST['mes']) {
        $mes = $value['descripcion'];
    }
}
$worksheet1 = & $workbook->add_worksheet('Planilla ' . $_REQUEST['periodo'] . '-' . $mes . '');
$columnsName = $planilla->getHrmConstructorPlanilla($_REQUEST['idHrmPlanillas'], $_SESSION['idEmpresa']);
$dataPlanilla = $planilla->getHrmPlanillasGeneradas($_REQUEST['periodo'], $_REQUEST['mes'], $_REQUEST['idHrmPlanillas'], $_SESSION['idEmpresa']);
//
//FORMATOS CELDAS
$formato_encabezado3 = & $workbook->add_format();
$formato_encabezado3->set_size(10);
$formato_encabezado3->set_align('left');
$formato_encabezado3->set_color('black');
$formato_encabezado3->set_pattern();
$formato_encabezado3->set_bold();
$formato_encabezado3->set_fg_color('white');
$formato_encabezado3->set_border(0);
$formato_encabezado3->set_border_color('black');
//
$formato_celda = & $workbook->add_format();
$formato_celda->set_size(8);
$formato_celda->set_align('right');
$formato_celda->set_color('black');
$formato_celda->set_pattern();
$formato_celda->set_fg_color('white');
$formato_celda->set_border(1);
$formato_celda->set_border_color('gray');
//ENCABEZADOS
$worksheet1->write_string(0, 0, 'Planilla de ' . $mes . ' / ' . $_REQUEST['periodo'] . '', $formato_encabezado3);
$worksheet1->merge_cells(0, 0, 0, (count($columnsName) - 1));
$worksheet1->write_string(1, 0, 'Empresa: ' . $_SESSION['nombreEmpresa'] . '', $formato_encabezado3);
$worksheet1->merge_cells(1, 0, 1, (count($columnsName) - 1));
foreach ($columnsName as $key => $value) {
    $worksheet1->write_string(2, $key, strtoupper($value['nombreCampo']), $formato_encabezado3);
}
//CONTENIDO
foreach ($dataPlanilla as $key2 => $value2) {
    foreach ($columnsName as $key => $value) {
        $worksheet1->write_string($filaHoja, $key, $value2['campo' . ($key + 1)], $formato_celda);
    }
    $filaHoja++;
}
foreach ($columnsName as $key => $value) {
    if ($value['idTipoCampo'] !== '1') {
        $worksheet1->write_string($filaHoja, $key, '0.00', $formato_celda);
    } else {
        $worksheet1->write_string($filaHoja, $key, '--', $formato_celda);
    }
}
//
$filaHoja2 = ($filaHoja + 5);
$worksheet1->write_string($filaHoja2, 0, 'Realizado Gestion de Personal', $formato_encabezado3);
$worksheet1->merge_cells($filaHoja2, 0, $filaHoja2, 2);
$worksheet1->write_string($filaHoja2, 4, 'Gerencia Administrativa', $formato_encabezado3);
$worksheet1->merge_cells($filaHoja2, 4, $filaHoja2, 2);
$worksheet1->write_string($filaHoja2, 8, 'Gerencia General', $formato_encabezado3);
$worksheet1->merge_cells($filaHoja2, 8, $filaHoja2, 2);
//
$workbook->close();
?>

