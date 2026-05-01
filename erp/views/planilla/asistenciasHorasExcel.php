<?php

require_once ("../../clases/login.php");
require_once ("../../clases/inventarios.php");
require_once("../../clases/librerias/excel/Workbook.php");
require_once("../../clases/librerias/excel/Worksheet.php");
$login = new Login();
$inventarios = new Inventarios();
$workbook = new Workbook("-");

//
function lastDayMonth($mes) {
    $month = $mes;
    $year = date('Y');
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('d', mktime(0, 0, 0, $month, $day, $year));
}

;

//
function addZero($number) {
    $numero = "";
    if (strlen((string) $number) == 1) {
        $numero = "0" . $number;
    } else {
        $numero = $number;
    }
    return $numero;
}

function monthName($number) {
    $name = "";
    switch ($number) {
        case 1 :
            $name = 'Enero';
            break;
        case 2 :
            $name = 'Febrero';
            break;
        case 3 :
            $name = 'Marzo';
            break;
        case 4 :
            $name = 'Abril';
            break;
        case 5 :
            $name = 'Mayo';
            break;
        case 6 :
            $name = 'Junio';
            break;
        case 7 :
            $name = 'Julio';
            break;
        case 8 :
            $name = 'Agosto';
            break;
        case 9 :
            $name = 'Septiembre';
            break;
        case 10 :
            $name = 'Octube';
            break;
        case 11 :
            $name = 'Noviembre';
            break;
        case 12 :
            $name = 'Diciembre';
            break;
    }
    return $name;
}

$lastDayMonth = lastDayMonth($_REQUEST['month']);
$getEmpleados = $inventarios->getEmpleados($_REQUEST['idSucursales'], $_REQUEST['idEmpleados']);
//
$filaHoja = 2;
$workbook->HeaderingExcel('Detalle_Asistencia_Horas_Extras.xls');
$worksheet1 = & $workbook->add_worksheet('Horas_Extras');
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
$worksheet1->write_string(0, 0, 'Reporte Detalle Asistencia Horas Extras', $formato_encabezado);
$worksheet1->merge_cells(0, 0, 0, $lastDayMonth + 3);
$worksheet1->write_string(1, 0, 'Empleado.', $formato_encabezado);
$worksheet1->write_string(1, 1, 'Sucursal', $formato_encabezado);
$worksheet1->write_string(1, 2, 'Puesto', $formato_encabezado);
for ($i = 1; $i <= $lastDayMonth; $i++) {
    $worksheet1->write_string(1, (2 + $i), $i, $formato_encabezado);
}
$worksheet1->write_string(1, $lastDayMonth + 3, 'Total Horas', $formato_encabezado);
//CONTENIDO
for ($a = 0; $a < count($getEmpleados); $a++) {
    $worksheet1->write_string($filaHoja, 0, $getEmpleados[$a]['userName'], $formato_celda);
    $worksheet1->write_string($filaHoja, 1, $getEmpleados[$a]['sucursal'], $formato_celda);
    $worksheet1->write_string($filaHoja, 2, $getEmpleados[$a]['puesto'], $formato_celda);
    $times = array();
    for ($i = 1; $i <= $lastDayMonth; $i++) {
        $getHorasTrabajadas = $inventarios->getHorasTrabajadas($getEmpleados[$a]['id'], date('Y-' . $_REQUEST['month'] . '-' . addZero($i)));
        $horasExtras = "--";
        if ($getHorasTrabajadas > $getEmpleados[$a]['horarioEnHoras']) {
            $horasExtras = $inventarios->restarTiempos($getEmpleados[$a]['horarioEnHoras'], $getHorasTrabajadas);
            array_push($times, $horasExtras);
        }
        $worksheet1->write_string($filaHoja, (2 + $i), $horasExtras, $formato_celda);
    }
    $worksheet1->write_string($filaHoja, $lastDayMonth + 3, $inventarios->AddPlayTime($times), $formato_celda);
    $filaHoja++;
}
$workbook->close();
?>