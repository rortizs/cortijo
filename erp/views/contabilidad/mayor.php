<?php
/* CONTABILIDAD - DIARIO
 * 
 */
session_start();
require_once("../../models/admin.php");
$admin = new Admin();
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
?>
<div class="row">
    <div class="col-lg-6 col-lg-offset-3">
        <section class="panel">
            <header class="panel-heading">
                Impresión del Libro Mayor
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <label>Año Inicial</label>
                        <select id="yearInicial" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getAnos as $key => $value) {
                                if ($value['descripcion'] == $_POST['year'] ?: date('Y')) {
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
                    <div class="col-lg-6">
                        <label>Año Final</label>
                        <select id="yearFinal" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getAnos as $key => $value) {
                                if ($value['descripcion'] == $_POST['year'] ?: date('Y')) {
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
                    <div class="col-lg-6">
                        <label>Tipo de Reporte</label>
                        <select class="form-control input-sm" id="tipoReporte">
                            <option value="1">Detallado</option>
                            <option value="2">Resumido</option>
                        </select>
                    </div>
                    <!--                    <div class="col-lg-6">
                                            <label>Cuenta Contable Inicial</label>
                                            <div>
                                                <button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda("vw_nomenclatura", "Cuentas Contables", "cuentasContables", 1);'>
                                                    <i class='fa fa-question'></i>
                                                </button>
                                                <input type='hidden' id='idNomenclaturaV-1'/>
                                                <input class='form-control input-sm' id='idNomenclatura-1' readonly='' style='width: 86%;'/>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Cuenta Contable Final</label>
                                            <div>
                                                <button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda("vw_nomenclatura", "Cuentas Contables", "cuentasContables", 2);'>
                                                    <i class='fa fa-question'></i>
                                                </button>
                                                <input type='hidden' id='idNomenclaturaV-2'/>
                                                <input class='form-control input-sm' id='idNomenclatura-2' readonly='' style='width: 86%;'/>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label>Centro de Costo</label>
                                            <div>
                                                <button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda("vw_centrosCosto", "Centros de Costo", "centrosCosto", 1);'>
                                                    <i class='fa fa-question'></i>
                                                </button>
                                                <input type='hidden' id='idCentrosCosto-1'/>
                                                <input class='form-control input-sm' id='centroCosto-1' readonly='' style='width: 86%;'/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <label>Todos Las Cuentas</label>
                                            <input type="checkbox" id="todasCuentas" class="form-control" value="1"/>
                                        </div>
                                        <div class="col-lg-3">
                                            <label>Todos Los Centros</label>
                                            <input type="checkbox" id="todosCentros" class="form-control" value="1"/>
                                        </div>-->
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-warning btn-sm" onclick="imprimirMayor();">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
