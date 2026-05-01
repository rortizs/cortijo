<?php
/* CONTABILIDAD - LIBRO DE VENTAS
 * 
 */
session_start();
require_once("../../models/admin.php");
$admin = new Admin();
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
?>
<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
        <section class="panel">
            <header class="panel-heading">
                Impresión Libro de Ventas
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
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
                    <div class="col-lg-12">
                        <label>Mes Inicial</label>
                        <select id="mesInicial" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getMeses as $key => $value) {
                                if ($value['number'] == date('m')) {
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
                        <label>Establecimiento</label>
                        <select id="idSucursales" class="form-control input-sm">
                        </select>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-warning btn-sm" onclick="imprimirLibroVentas();">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
