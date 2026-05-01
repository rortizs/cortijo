<?php
/**
 * Reporte de Asistencia Diario
 */
session_start();
require_once ("../../models/planilla.php");
$planilla = new Planilla();
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];
$idDepartamentos = $_POST['idDepartamentos'];
$idEmpleados = $_POST['idEmpleados'];
$getDepartamentos = $planilla->getDepartamentos($_SESSION['idEmpresa']);

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
                        <div class='input-group date' id='fechaInicio'>
                            <input type='text' class="form-control input-sm" value="<?= $_REQUEST['fechaInicio'] ?: date('d-m-Y'); ?>"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>Fecha Fin</label>
                        <div class='input-group date' id='fechaFin'>
                            <input type='text' class="form-control input-sm" value="<?= $_REQUEST['fechaFin'] ?: date('d-m-Y'); ?>"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label>Departamento</label>
                        <select class="form-control input-sm" id="idDepartamentos" onchange="loadEmpleados();">
                            <option value="" selected="">[Seleccione...]</option>
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
                    <div class="col-lg-3">
                        <label>Empleados</label>
                        <select class="form-control input-sm" id="idEmpleados">
                            <option value="*">Todos los empleados</option>
                        </select>
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-primary btn-sm" onclick="generarReporteAsistencias();">
                            <span class="fa fa-list"></span> Generar Reporte
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="generarReporteAsistenciasPDF();">
                            <span class="fa fa-print"></span> Imprimir Reporte
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <center>
        <?php
        if ($_POST['flag'] == '1') {
            ?>
            <h4>
                Reporte de Marcaje por Empleado
            </h4>
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <table class="table table-bordered table-striped table-fixed">
                        <thead>
                            <tr>
                                <td colspan="7">Empleado: <?= $_REQUEST['nombreEmpleado']; ?></td>
                                <td colspan="2">Periodo: <?= $fechaInicio; ?> al <?= $fechaFin; ?></td>
                            </tr>
                            <tr>
                                <td>Fecha</td>
                                <td>Dia</td>
                                <td>Turno</td>
                                <td>Hora Entrada</td>
                                <td>Hora Salida</td>
                                <td>Hrs. Ext. Diurnas</td>
                                <td>Hrs. Ext. Nocturnas</td>
                                <td>Observaciones</td>
                            </tr>
                        </thead>
                        <?php
                        $totalDiurnas = 0;
                        $totalNocturnas = 0;
                        for ($i = date("Y-m-d", strtotime($fechaInicio)); $i <= date("Y-m-d", strtotime($fechaFin)); $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
                            $marcaje = $planilla->horaMarcaje($idEmpleados, $i, $_SESSION['idEmpresa']);
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
                            $totalDiurnas += $hrsExtrasDiurnas;
                            $totalNocturnas += $hrsExtrasNocturnas;
                            ?>
                            <tr <?= $style; ?>>
                                <td><?= date("d-m-Y", strtotime($i)); ?></td>
                                <td><?= nombreDia($marcaje['dia']); ?></td>
                                <td><?= $turno; ?></td>
                                <td><?= $horaEntrada; ?></td>
                                <td><?= $horaSalida ?></td>
                                <td align="center"><?= ($hrsExtrasDiurnas * 1) ?></td>
                                <td align="center"><?= ($hrsExtrasNocturnas * 1) ?></td>
                                <td></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <thead>
                            <tr>
                                <td colspan="5" align="right">Total de Horas</td>
                                <td align="center"><?= $totalDiurnas; ?></td>
                                <td align="center"><?= $totalNocturnas; ?></td>
                                <td></td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <?php
        }
        ?>
    </center>
</div>