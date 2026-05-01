<?php
/* CXC - Reporte de Abonos
 * 
 */
session_start();
require_once("../../models/admin.php");
require_once("../../models/planilla.php");
$admin = new Admin();
$planilla = new Planilla();
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
$getHrmPlanillas = $planilla->getHrmPlanillas($_SESSION['idEmpresa']);
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Filtros para Generacion de Nomina
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label>Planilla</label>
                        <select id="idHrmPlanillas" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getHrmPlanillas as $key => $value) {
                                if ($value['id'] == $_POST['idHrmPlanillas']) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label>Año</label>
                        <select id="periodo" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getAnos as $key => $value) {
                                if ($value['descripcion'] == ($_POST['year'] ?: date('Y'))) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label>Mes</label>
                        <select id="mes" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getMeses as $key => $value) {
                                if ($value['number'] == ($_POST['month'] ?: date('m'))) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-info btn-sm" onclick="loadConstructorSabana();">
                            <i class="fa fa-table"></i> (1) Generar Nomina
                        </button>
                        <button class="btn btn-info btn-sm" onclick="saveSabanaPlanilla();">
                            <i class="fa fa-floppy-o"></i> (2) Guardar Nomina
                        </button>
                        <button class="btn btn-info btn-sm" onclick="loadConstructorSabana();">
                            <i class="fa fa-table"></i> (3) Consultar Nomina
                        </button>
                        <button class="btn btn-info btn-sm" onclick="authSabanaPlanilla();">
                            <i class="fa fa-search"></i> (4) Autorizar Nomina
                        </button>
                        <button class="btn btn-info btn-sm" onclick="imprimirConstructorSabana();">
                            <i class="fa fa-print"></i> (5) Imprimir Planilla
                        </button>
                        <button class="btn btn-info btn-sm" onclick="imprimirConstructorSabana();">
                            <i class="fa fa-print"></i> (6) Cerrar Planilla
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<input type="hidden" id="validate" value="0"/>
<div id="constructorSabana">
</div>