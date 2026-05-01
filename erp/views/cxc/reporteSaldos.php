<?php
/* CXC - Reporte de Saldos
 * 
 */
session_start();
require_once("../../models/admin.php");
require_once("../../models/contabilidad.php");
$admin = new Admin();
$contabilidad = new Contabilidad();
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
$getCentrosCosto = $contabilidad->getCentrosCosto($_SESSION['idEmpresa']);
?>
<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
        <section class="panel">
            <header class="panel-heading">
                Filtros Reporte de Saldos
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label>Año</label>
                        <select id="year" class="form-control input-sm">
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
                    <div class="col-lg-12">
                        <label>Mes</label>
                        <select id="month" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getMeses as $key => $value) {
                                if ($value['number'] == $_POST['month']) {
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
                        <label>Tipo de Reporte</label>
                        <select id="tipoReporte" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <option value="1">Resumen</option>
                            <option value="2">Detallado</option>
                        </select>
                    </div>  
                    <div class="col-lg-12">
                        <label>Centro de Costo</label>
                        <select id="idCentrosCosto" class="form-control input-sm">
                            <option>[Seleccione...]</option>
                            <?php
                            foreach ($getCentrosCosto as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-success btn-sm" onclick="exportarReporteSaldos();">
                            <i class="fa fa-file-excel-o"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
