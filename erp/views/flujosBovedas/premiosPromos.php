<?php
session_start();
require_once("../../models/flujosBovedas.php");
$getBovedas = flujosBovedas::getBovedas($_SESSION['idEmpresa']);
$getAnos = flujosBovedas::getAnos();
$getMeses = flujosBovedas::getMeses();
$tipoReporte = array(
    array('id' => 1, 'descripcion' => 'Maquinas'),
    array('id' => 2, 'descripcion' => 'Mesas')
);
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Filtros Reporte
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label><i class="fa fa-filter"></i> Tipo Reporte</label>
                        <select class="form-control input-sm" id="idTipoReporte">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($tipoReporte as $key => $value) {
                                if ($value['id'] == $_POST['idTipoReporte']) {
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
                        <label><i class="fa fa-database"></i> Sala</label>
                        <select class="form-control input-sm" id="idSalas">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getBovedas as $key => $value) {
                                if ($value['id'] == $_POST['idSalas']) {
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
                    <div class="col-lg-2">
                        <label><i class="fa fa-calendar"></i> Año</label>
                        <select class="form-control input-sm" id="year">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getAnos as $key => $value) {
                                if ($value['descripcion'] == $_POST['year']) {
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
                    <div class="col-lg-2">
                        <label><i class="fa fa-calendar"></i> Mes</label>
                        <select class="form-control input-sm" id="month">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getMeses as $key => $value) {
                                if ($value['id'] == $_POST['month']) {
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
                    <div class="col-lg-4">
                        &nbsp;<br/>
                        <button class="btn btn-sm btn-primary" onclick="generarReportePremiosPromos();">
                            Generar Reporte
                        </button>
                        <!--<button class="btn btn-sm btn-success">Exportar a Excel</button>-->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
if (isset($_POST['idSalas']) && isset($_POST['year']) && isset($_POST['month'])) {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body">
                    <center>
                        <h4>
                            Reporte Premios y Promociones <?= $_POST['tipoReporte']; ?><br/>
                            <small>
                                SALA: <?= $_POST['sala']; ?><br/>
                                AÑO: <?= $_POST['year']; ?> MES: <?= $_POST['monthName']; ?>
                            </small>
                        </h4>
                    </center>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" width="70%">
                            <thead>
                                <tr class="info text-uppercase">
                                    <td>Fecha</td>
                                    <td align="right">Promociones Con Descuento</td>
                                    <td align="right">Promociones Sin Descuento</td>
                                    <td align="right">TOTAL PREMIOS Y PROMOCIONES</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $reporte = flujosBovedas::reportePremiosPromos($_POST['year'], $_POST['month'], $_POST['idSalas'], $_SESSION['idEmpresa']);
                                $total1 = 0;
                                $total2 = 0;
                                $total3 = 0;
                                foreach ($reporte as $key => $value) {
                                    $total1 += $value['pcd'];
                                    $total2 += $value['psd'];
                                    $total3 += $value['total'];
                                    ?>
                                    <tr>
                                        <td><?= $value['arqueo_at'] ?></td>
                                        <td align="right"><?= $value['pcd']; ?></td>
                                        <td align="right"><?= $value['psd']; ?></td>
                                        <td align="right"><?= $value['total']; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>  
                            </tbody>
                            <tr class="info headerReporte">
                                <td>Totales</td>
                                <td align="right"><?= number_format($total1, 2); ?></td>
                                <td align="right"><?= number_format($total2, 2); ?></td>
                                <td align="right"><?= number_format($total3, 2); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php
}
?>