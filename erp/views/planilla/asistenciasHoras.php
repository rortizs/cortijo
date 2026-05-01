<?php
/**
 *
 */
session_start();
require_once ("../../models/login.php");
require_once ("../../models/planilla.php");
$login = new Login();
$planilla = new Planilla();
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];
$idDepartamentos = $_POST['idDepartamentos'];
$getDepartamentos = $planilla->getHrmDepartamentos($_SESSION['idEmpresa']);

//
function lastDayMonth($mes) {
    $month = $mes;
    $year = date('Y');
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('d', mktime(0, 0, 0, $month, $day, $year));
}

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

function nombreDia($dia) {
    switch ($dia) {
        case 'Monday':
            return 'Lunes';
            break;
        case 'Tuesday':
            return 'Martes';
            break;
        case 'Wednesday':
            return 'Miercoles';
            break;
        case 'Thursday':
            return 'Jueves';
            break;
        case 'Friday':
            return 'Viernes';
            break;
        case 'Saturday':
            return 'Sabado';
            break;
        case 'Sunday':
            return 'Domingo';
            break;
    }
}
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Parametros
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label>Fecha Inicio</label>
                        <div class='input-group date' id='recorddate1'>
                            <input type='text' id="fechaInicio" class="form-control input-sm" value="<?= $_REQUEST['fechaInicio'] ?: date('d-m-Y'); ?>"/>
                            <span class="input-group-addon btn-primary"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha Fin</label>
                        <div class='input-group date' id='recorddate2'>
                            <input type='text' id="fechaFin" class="form-control input-sm" value="<?= $_REQUEST['fechaFin'] ?: date('d-m-Y'); ?>"/>
                            <span class="input-group-addon btn-primary"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>Departamento</label>
                        <select class="form-control input-sm" id="idDepartamentos" onchange="loadEmpleados();">
                            <option value="" selected="">[Seleccione...]</option>
                            <?php
                            if ($idDepartamentos == '*') {
                                ?>
                                <option value="*" selected="">Todos los Departamentos</option>
                                <?php
                            } else {
                                ?>
                                <option value="*">Todos los Departamentos</option>
                                <?php
                            }
                            ?>
                            <?php
                            foreach ($getDepartamentos as $key => $value) {
                                if ($value['id'] == $idDepartamentos) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= strtoupper($value['descripcion']); ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= strtoupper($value['descripcion']); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Empleados:</label>
                        <select class="form-control input-sm" id="idEmpleados">
                            <option value="*">Todos los empleados</option>
                        </select>
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-primary btn-sm" onclick="generarReporteAsistenciasHoras();">
                            <span class="fa fa-list"></span> Generar Reporte
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="generarReporteAsistenciasHorasPDF();">
                            <span class="fa fa-print"></span> Imprimir Reporte
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <center>
        <?php
        $lastDayMonth = lastDayMonth($_REQUEST['month']);
        if ($_REQUEST['fechaInicio'] != '' && $_REQUEST['fechaFin'] != '') {
            $params['idHrmDepartamentos'] = $_REQUEST['idDepartamentos'];
            $params['idEmpleados'] = $_REQUEST['idEmpleados'];
            $getEmpleados = $planilla->getHrmEmpleados($params, $_SESSION['idEmpresa']);
            ?>
            <h4>
                Reporte Detalle de Horas Extras<br/>
                <small>
                    De <?= $_REQUEST['fechaInicio']; ?> al <?= $_REQUEST['fechaFin']; ?><br/>
                    Departamento(s): <?= $_REQUEST['txtDepartamentos']; ?> | Empleado(s): <?= $_REQUEST['txtEmpleados']; ?>
                </small>
            </h4>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-fixed" style="width: 80% !important;">
                        <thead>
                            <tr class="info">
                                <td colspan="2"></td>
                                <td class="text-center" colspan="<?= $lastDayMonth + 1; ?>">
                                    De <?= $_REQUEST['fechaInicio']; ?> al <?= $_REQUEST['fechaFin']; ?>
                                </td>
                            </tr>
                            <tr class="info">
                                <td>Empleado</td>
                                <td>Departamento</td>
                                <?php
                                //for ($i = 1; $i <= $lastDayMonth; $i++) {
                                for ($i = date("Y-m-d", strtotime($fechaInicio)); $i <= date("Y-m-d", strtotime($fechaFin)); $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
                                    ?>
                                    <td style="width: 50px !important; min-width: 50px !important;" align="center">
                                        <?= date("d-m-Y", strtotime($i)); ?><br/>
                                        <?= nombreDia(date("l", strtotime($i))); ?>
                                    </td>
                                    <?php
                                }
                                ?>
                                <td align="center">Total<br/>Horas</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($a = 0; $a < count($getEmpleados); $a++) {
                                $totalDiurnas = 0;
                                $totalNocturnas = 0;
                                ?>
                                <tr>
                                    <td><?= $getEmpleados[$a]['codigoEmpleado']; ?> - <?= strtoupper($getEmpleados[$a]['nombreEmpleado']); ?></td>
                                    <td><?= strtoupper($getEmpleados[$a]['departamento']); ?></td>
                                    <?php
                                    //for ($i = 1; $i <= $lastDayMonth; $i++) {
                                    for ($i = date("Y-m-d", strtotime($fechaInicio)); $i <= date("Y-m-d", strtotime($fechaFin)); $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
                                        //$fecha = date($_REQUEST['year'] . '-' . $_REQUEST['month'] . '-' . addZero($i));
                                        $marcaje = $planilla->horaMarcaje($getEmpleados[$a]['codigoEmpleado'], $i, $_SESSION['idEmpresa']);
                                        $hrsExtrasDiurnas = 0;
                                        $hrsExtrasNocturnas = 0;
                                        $horaEntrada = $marcaje['horaEntrada'] ?: '--';
                                        $horaSalida = $marcaje['horaSalida'] ?: '--';
                                        $style = "";
                                        $turno = "";
                                        if ($marcaje['turno'] != '') {
                                            if ($marcaje['horaEntrada'] != '' && $marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaSalida'] != '') {
                                                if ($marcaje['dia'] == 'Friday') {
                                                    $hrsExtrasNocturnas = ($planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']) - 2);
                                                } else {
                                                    $hrsExtrasNocturnas = ($planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']) - 1);
                                                }
                                            } else if ($marcaje['horaEntrada'] != '' && $marcaje['horaSalidaTurno'] != '18:00:00' && $marcaje['horaSalida'] > $marcaje['horaSalidaTurno']) {
                                                $hrsExtrasDiurnas = $planilla->restarTiempos($marcaje['horaSalidaTurno'], $marcaje['horaSalida']);
                                            }
                                            if ($marcaje['horaEntrada'] == $marcaje['horaSalida'] || $marcaje['horaEntrada'] == '' || $marcaje['horaSalida'] == '') {
                                                $style = "style='color:blue;'";
                                                $hrsExtrasDiurnas = 0;
                                                $turno = "MARCAJE INCOMPLETO";
                                            } else if ($marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaSalida'] > '08:00:00') {
                                                $style = "style='color:blue;'";
                                                $hrsExtrasNocturnas = 0;
                                                $turno = "MARCAJE INCOMPLETO";
                                            } else if ($marcaje['horaEntradaTurno'] >= '18:00:00' && $marcaje['horaEntrada'] < '17:00:00') {
                                                $style = "style='color:blue;'";
                                                $hrsExtrasNocturnas = 0;
                                                $turno = "MARCAJE INCOMPLETO";
                                            } else {
                                                $style = "style='color:green;'";
                                                $turno = $marcaje['turno'];
                                            }
                                        } else if ($marcaje['dia'] == 'Saturday' || $marcaje['dia'] == 'Sunday') {
                                            $style = "style='color:black;'";
                                            $turno = "";
                                            $horaEntrada = "";
                                            $horaSalida = "";
                                        } else if ($marcaje['turno'] == '' && $marcaje['horaEntrada'] != $marcaje['horaSalida']) {
                                            $style = "style='color:red;'";
                                            $turno = "SIN TURNO DEFINIDO";
                                        } else if ($marcaje['turno'] == '' && $marcaje['horaEntrada'] == $marcaje['horaSalida'] && $marcaje['horaEntrada'] != '' && $marcaje['horaSalida'] != '') {
                                            $style = "style='color:blue;'";
                                            $turno = "SIN TURNO DEFINIDO";
                                            $hrsExtrasDiurnas = 0;
                                        }
                                        $totalDiurnas += ($hrsExtrasDiurnas < 0 ? 0 : $hrsExtrasDiurnas);
                                        $totalNocturnas += ($hrsExtrasNocturnas < 0 ? 0 : $hrsExtrasNocturnas);
                                        ?>
                                        <td align="center">
                                            <?= $marcaje['horaEntrada'] ?: 0; ?><br/>
                                            <?= $marcaje['horaSalida'] ?: 0; ?>
                                            <hr/>
                                            <?= ($hrsExtrasDiurnas < 0 ? 0 : $hrsExtrasDiurnas) ?><br/>
                                            <?= ($hrsExtrasNocturnas < 0 ? 0 : $hrsExtrasNocturnas) ?>
                                        </td>
                                        <?php
                                    }
                                    ?>
                                    <td align="center"><?= $totalDiurnas; ?><br/><?= $totalNocturnas; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }
        ?>
    </center>
</div>