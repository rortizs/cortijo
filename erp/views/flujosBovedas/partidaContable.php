<?php
session_start();
require_once("../../models/flujosBovedas.php");
$getBovedas = flujosBovedas::getBovedas($_SESSION['idEmpresa']);
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
                    <div class="col-lg-3">
                        <label>Fecha Cierre</label>
                        <div class='input-group date' id='fechaCierre'>
                            <input type='text' class="form-control input-sm" value="<?= $_POST['fechaCierre']; ?>"/>
                            <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        &nbsp;<br/>
                        <button class="btn btn-sm btn-primary" onclick="consultarPartidasBovedas();">
                            Consultar Partida
                        </button>
                        <!--<button class="btn btn-sm btn-success">Exportar a Excel</button>-->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<center>
    <?php
    if (isset($_POST['idSalas']) && isset($_POST['fechaCierre'])) {
        $_POST['idEmpresas'] = $_SESSION['idEmpresa'];
        $partidaBovedas = flujosBovedas::partidaBovedas($_POST);
        ?>
        <div class="text-center"> 
            <h4>Partida Contable</h4>
            <p>Tipo Reporte: <?= $_POST['tipoReporte']; ?><br/>
                Sucursal: <?= $_POST['sala']; ?><br/>
                Registro contable del: <?= $_POST['fechaCierre']; ?></p>
        </div>
        <table class="table table-bordered table-striped" style="width: 60% !important;">
            <thead>
                <tr class="info text-uppercase">
                    <td>Cuenta</td>
                    <td>Concepto</td>
                    <td>C.C</td>
                    <td>Debe</td>
                    <td>Haber</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $debe = 0;
                $haber = 0;
                $iva = 0;
                $cc = '';
                foreach ($partidaBovedas as $key => $value) {
                    $cc = $value['cc'];
                    $cuenta=$value['cuenta'];
                    $cuentaContable=$value['cuentaContable'];
                    $valorHaber = $value['haber'];
                    switch ($value['cuenta']) {
                        case '4010401':
                            $valorHaber = ($value['haber'] / 1.12);
                            $iva += ($value['haber'] - $valorHaber);
                            break;
                        case '4010402':
                            $valorHaber = ($value['haber'] / 1.12);
                            $iva += ($value['haber'] - $valorHaber);
                            break;
                        case '4010301':
                            if ($_SESSION['idEmpresa'] == '2') {
                                $valorHaber = ($value['haber'] / 1.12);
                                $iva += ($value['haber'] - $valorHaber);
                            }else{
                                $cuenta='2010109';
                                $cuentaContable='CUENTAS POR PAGAR TABLES,SA';
                            }
                            break;
                    }
                    $debe += $value['debe'];
                    $haber += $valorHaber;
                    ?>
                    <tr>
                        <td><?= $cuenta; ?></td>	
                        <td><?= $cuentaContable; ?></td>
                        <td><?= $value['cc']; ?></td>
                        <td align="right"><?= number_format($value['debe'], 2); ?></td>
                        <td align="right"><?= number_format($valorHaber, 2); ?></td>
                    </tr>
                    <?php
                }
                ?>
                <?php
                if ($iva !== 0) {
                    ?>
                    <tr>
                        <td>2010201</td>	
                        <td>IVA - DEBITO FISCAL IVA</td>
                        <td><?= $cc; ?></td>
                        <td align="right">0.00</td>
                        <td align="right"><?= number_format($iva, 2); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
            <thead>
                <tr>
                    <td colspan="3">Cuadre</td>
                    <td align="right"><?= number_format($debe, 2); ?></td>
                    <td align="right"><?= number_format($haber + $iva, 2); ?></td>
                </tr>
            </thead>
        </table>
        <?php
    }
    ?>
</center>