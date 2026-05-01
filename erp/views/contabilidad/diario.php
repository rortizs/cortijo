<?php
/* CONTABILIDAD - DIARIO
 * 
 */
session_start();
require_once("../../models/admin.php");
require_once("../../models/contabilidad.php");
$admin = new Admin();
$contabilidad = new Contabilidad();
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
$centrosCosto = $contabilidad->getCentrosCosto($_SESSION['idEmpresa']);
?>
<div class="row">
    <div class="col-lg-6 col-lg-offset-3">
        <section class="panel">
            <header class="panel-heading">
                Impresión del Libro de Diario
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label>Tipo de Reporte</label>
                        <select class="form-control input-sm" id="tipoReporte">
                            <option value="1">Detallado</option>
                            <option value="2">Resumen</option>
                            <option value="3">Partidas No Cuadradas</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Período Inicial</label>
                        <select id="yearInicial" class="form-control input-sm">
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
                    <div class="col-lg-6">
                        <label>Mes Inicial</label>
                        <select id="mesInicial" class="form-control input-sm">
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
                    <div class="col-lg-6">
                        <label>Período Final</label>
                        <select id="yearFinal" class="form-control input-sm">
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
                    <div class="col-lg-6">
                        <label>Mes Final</label>
                        <select id="mesFinal" class="form-control input-sm">
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
                    <!--
                    <div class="col-lg-6">
                        <label># Partida Inicial</label>
                        <input type="text" class="form-control input-sm" id="partidaInicial" value="0"/>
                    </div>
                    <div class="col-lg-6">
                        <label># Partida Final</label>
                        <input type="text" class="form-control input-sm" id="partidaFinal" value="0"/>
                    </div>
                    
                    <div class="col-lg-6">
                        <label>Centro de Costo</label>
                        <select id="idCentrosCosto" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($centrosCosto as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    -->
                    <div class="col-lg-6">
                        <label>Folio de Inicio</label>
                        <input type="text" class="form-control input-sm" id="folioInicio" value="0"/>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-warning btn-sm" onclick="imprimirDiario();">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
