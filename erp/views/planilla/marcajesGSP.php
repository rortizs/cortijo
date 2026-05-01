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
                        <button type="button" class="btn btn-primary btn-sm" onclick="generarReporteMarcajes();">
                            <span class="fa fa-list"></span> Generar Reporte
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="imprimirReporteMarcajes();">
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
                    <table class="table table-bordered table-striped table-fixed" style="width: 80% !important;">
                        <thead>
                            <tr>
                                <td colspan="5">Empleado: <?= $_REQUEST['nombreEmpleado']; ?></td>
                                <td colspan="2">Periodo: <?= $fechaInicio; ?> al <?= $fechaFin; ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Fecha</label>
                                    <input type="text" id="fecha" class="form-control input-sm" placeholder="dd-mm-yyyy"/>
                                </td>
                                <td>
                                    <label>Dia</label>
                                    <input type="text" id="dia" class="form-control input-sm" readonly=""/>
                                </td>
                                <td>
                                    <label>Hora Entrada</label>
                                    <input type="text" id="horaEntrada" class="form-control input-sm" placeholder="hh:mm:ss"/>
                                </td>
                                <td>
                                    <label>Hora Salida</label>
                                    <input type="text" id="horaSalida" class="form-control input-sm" placeholder="hh:mm:ss"/>
                                </td>
                                <td>
                                    <label>Hrs. Ext. Diurnas</label>
                                    <input type="text" id="numeroHorasExtrasDiurnas" class="form-control input-sm" placeholder="0.00"/>
                                </td>
                                <td>
                                    <label>Hrs. Ext. Nocturnas</label>
                                    <input type="text" id="numeroHorasExtrasNocturas" class="form-control input-sm" placeholder="0.00"/>
                                </td>
                                <td>&nbsp;<br/>
                                    <button class="btn-xs btn-success" onclick="guardarMarcaje();">Guardar</button>
                                </td>
                            </tr>
                        </thead>
                        <?php
                        $getMarcajes = $planilla->getMarcajes($_POST);
                        $totalDiurnas = 0;
                        $totalNocturnas = 0;
                        foreach ($getMarcajes as $key => $value) {
                            $totalDiurnas += $value['numeroHorasExtrasDiurnas'];
                            $totalNocturnas += $value['numeroHorasExtrasNocturas'];
                            ?>
                            <tr>
                                <td><?= date("d-m-Y", strtotime($value['fecha'])); ?></td>
                                <td><?= $value['dia']; ?></td>
                                <td style="width: 10% !important;"><input type="text" class="form-control input-sm" id="horaEntrada-<?= $value['id']; ?>" value="<?= $value['horaEntrada']; ?>"/></td>
                                <td style="width: 10% !important;"><input type="text" class="form-control input-sm" id="horaSalida-<?= $value['id']; ?>" value="<?= $value['horaSalida']; ?>"/></td>
                                <td style="width: 10% !important;"><input type="text" class="form-control input-sm" id="numeroHorasExtrasDiurnas-<?= $value['id']; ?>" value="<?= $value['numeroHorasExtrasDiurnas']; ?>"/></td>
                                <td style="width: 10% !important;"><input type="text" class="form-control input-sm" id="numeroHorasExtrasNocturas-<?= $value['id']; ?>" value="<?= $value['numeroHorasExtrasNocturas']; ?>"/></td>
                                <td><button class="btn-xs btn-warning" onclick="updateMarcaje('<?= $value['id']; ?>');">ACTUALIZAR</button></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <thead>
                            <tr>
                                <td colspan="4" align="right">Total de Horas</td>
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