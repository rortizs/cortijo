<?php
/*
 * PLANILLA - GENERACION DE PLA
 */
session_start();
require_once("../../models/planilla.php");
require_once("../../models/admin.php");
$planilla = new Planilla();
$admin = new Admin();
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
                        <label>Año</label>
                        <select class="form-control input-sm" id="year">
                            <?php
                            $getYear = $admin->getAnos();
                            foreach ($getYear as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Mes</label>
                        <select class="form-control input-sm" id="month">
                            <?php
                            $getMonth = $admin->getMeses();
                            foreach ($getMonth as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-8">
                        &nbsp;<br/>
                        <button class="btn btn-primary btn-sm" onclick="generarPlanillaSeguroSocial();">
                            <i class="fa fa-table"></i> Generar Planilla
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="generarPlanilla();">
                            <i class="fa fa-print"></i> Imprimir Planilla
                        </button>
                    </div>
                </div>
        </section>
    </div>
    <?php
    if (isset($_REQUEST['year']) && isset($_REQUEST['month'])) {
        $getHrmDepartamentos = $planilla->getHrmDepartamentos($_SESSION['idEmpresa']);
        ?>
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body">
                    <center>
                        <h4 style="text-transform: uppercase !important;">
                            <small>
                                <?php
                                $tipoPlanilla = "Pre-generación Planilla Seguro Social";
                                $periodo = "Periodo " . $_REQUEST['monthTXT'] . " - " . $_REQUEST['yearTXT'] . "";
                                $_REQUEST['fechaInicio'] = date('01-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                                $_REQUEST['fechaFin'] = date('30-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                                ?>
                                <?= $tipoPlanilla; ?><br/>
                                <?= $periodo; ?><br/>
                            </small>
                        </h4>
                    </center>
                    <div class="clearfix">&nbsp;</div>
                    <div class="table-responsive">
                        <?php
                        foreach ($getHrmDepartamentos as $key => $value) {
                            ?>
                            <table class="table table-striped table-bordered table-hover" cellspacing="0">
                                <thead>
                                    <tr class="info text-center">
                                        <td>Número o identificación<br/>de la liquidación.</td>
                                        <td>Numero<br/>de Afiliado</td>
                                        <td>Primer Nombre<br/>de afiliado</td>
                                        <td>Segundo Nombre<br/>de afiliado</td>
                                        <td>Primer Apellido<br/>de Afiliado</td>
                                        <td>Segundo Apellido<br/>de Afiliado</td>
                                        <td>Apellido de Casada<br/>de Afiliado</td>
                                        <td>Sueldo devengado<br/>en el periodo</td>
                                        <td>Fecha de alta</td>
                                        <td>Fecha de baja</td>
                                        <td>Código de Centro<br/>de trabajo asignado</td>
                                        <td>Nit </td>
                                        <td>Código<br/>Ocupación</td>
                                        <td>Condición<br/>Laboral</td>
                                        <td>Deducciones</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $generacionPlanilla = $planilla->generacionPlanilla($_REQUEST, $value['descripcion'], $_SESSION['idEmpresa']);
                                    foreach ($generacionPlanilla as $key2 => $value2) {
                                        $salarioExtOrd = round(($value2['horasExtSimples'] + $value2['horasExtDobles'] + $value2['horasExtMixtas']), 2);
                                        $sueldoDevengado = $value2['salario'] + $value2['bonifDecreto'] + $salarioExtOrd;
                                        $fechaIngreso = "";
                                        $fechaEgreso = "";
                                        if ($value2['mesIngreso'] == $_REQUEST['month'] && $value2['anoIngreso'] == $_REQUEST['yearTXT']) {
                                            $fechaIngreso = $value2['fechaIngreso'];
                                        }
                                        if ($value2['mesEgreso'] == $_REQUEST['month'] && $value2['mesEgreso'] == $_REQUEST['yearTXT']) {
                                            $fechaEgreso = $value2['fechaEgreso'];
                                        }
                                        ?>
                                        <tr>
                                            <td><?= ($key2 + 1); ?></td>
                                            <td><?= $value2['noSeguroSocial']; ?></td>
                                            <td><?= $value2['primerNombre']; ?></td>
                                            <td><?= $value2['segundoNombre']; ?></td>
                                            <td><?= $value2['primerApellido']; ?></td>
                                            <td><?= $value2['segundoApellido']; ?></td>
                                            <td><?= $value2['apellidoCasada']; ?></td>
                                            <td><?= number_format($sueldoDevengado, 2); ?></td>
                                            <td><?= $fechaIngreso; ?></td>
                                            <td><?= $fechaEgreso; ?></td>
                                            <td><?= $value2['centroTrabajoSeguroSocial']; ?></td>
                                            <td><?= $value2['noTributario']; ?></td>
                                            <td><?= $value2['profesion']; ?></td>
                                            <td><?= $value2['condicionLaboral']; ?></td>
                                            <td>--</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </section>
        </div>
        <?php
    }
    ?>
</div>