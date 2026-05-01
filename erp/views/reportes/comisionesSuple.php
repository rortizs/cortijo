<?php
/* CXC - Reporte de Abonos
 * 
 */
session_start();
require_once("../../models/admin.php");
require_once("../../models/contabilidad.php");
$admin = new Admin();
$contabilidad = new Contabilidad();
$params['idEmpresas'] = $_SESSION['idEmpresa'];
$vendedores = $admin->getVendedores($params);
$getCentrosCosto = $contabilidad->getCentrosCosto($_SESSION['idEmpresa']);
?>
<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
        <section class="panel">
            <header class="panel-heading">
                Filtros Reporte de Comisiones
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label>Fecha Inicio</label>
                        <div class='input-group date' id="fechaInicio">
                            <input type='text' class="form-control input-sm"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <label>Fecha Fin</label>
                        <div class='input-group date' id="fechaFin">
                            <input type='text' class="form-control input-sm"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
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
                        <label>Vendedor</label>
                        <select class="form-control input-sm" id="idVendedores">
                            <option value="">[Seleccione...]</option>
                            <?php
                            foreach ($vendedores as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['userName']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-success btn-sm" onclick="exportarReporteComisionesSuple();">
                            <i class="fa fa-file-excel-o"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
