<?php
/*
 * 
 */
session_start();
require_once("../../models/admin.php");
$admin = new Admin();
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
$getMarcas = $admin->getMarcas();
?>
<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
        <section class="panel">
            <header class="panel-heading">
                Filtros Reporte
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label><i class="fa fa-filter"></i> Inventario de</label>
                        <select class="form-control input-sm" id="ingresoA" onchange="ingresoA2();">
                            <option value="">[Seleccione...]</option>
                            <option value="1">BODEGA</option>
                            <option value="2">SUCURSAL</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label><i class="fa fa-map-marker"></i> Lugar</label>
                        <select class="form-control input-sm" id="idPuntoIngreso">
                        </select>
                    </div>
                    <!--                    <div class="col-lg-12">
                                            <label><i class="fa fa-map-marker"></i> Periodo</label>
                                            <select id="periodo" class="form-control input-sm">
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
                                            <label><i class="fa fa-map-marker"></i> Mes</label>
                                            <select id="mes" class="form-control input-sm">
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
                                        </div>-->
                    <div class="col-lg-12">
                        <label><i class="fa fa-map-marker"></i> Tipo de Reporte</label>
                        <select class="form-control input-sm" id="tipoReporte">
                            <option value="">[Seleccione...]</option>
                            <option value="1">Completo</option>
                            <option value="2">Con Existencias</option>
                            <option value="3">Sin Existencias</option>
                        </select>
                    </div>

                    <!--                    <div class="col-lg-12">
                                            <label><i class="fa fa-map-marker"></i> Marca</label>
                                            <select class="form-control input-sm selectpicker" id="idMarcas" data-live-search='true'>
                                                <option value="">[Seleccione...]</option>
                    <?php
                    foreach ($getMarcas as $key => $value) {
                        ?>
                                                        <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                        <?php
                    }
                    ?>
                                            </select>
                                        </div>-->
                    <div class="col-lg-12">
                        <label>Tipo de Valor</label>
                        <select class="form-control input-sm" id="tipoValor">
                            <option value="">[Seleccione...]</option>
                            <option value="precioCosto">Costo</option>
                            <option value="precioPublico">Precio</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label>Producto</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('inventario', 'Inventario de Productos', 'kardex');" style="z-index: 999;">Buscar <i class="fa fa-search"></i></button>
                            </span>
                            <input type="text" class="form-control input-sm" id="codigo" placeholder="Ingrese código del Producto">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button type="button" class="btn btn-success btn-sm" onclick="exportarReporteExistencias();">
                            <span class="fa fa-file-excel-o"></span> Exportar a Excel
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

