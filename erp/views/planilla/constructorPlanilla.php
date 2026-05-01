<?php
/**
 * PLANILLA /Constructor de Planilla
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
require_once ("../../models/planilla.php");
$planilla = new Planilla();
$data = $planilla->getHrmConstructorPlanilla($_REQUEST['idHrmPlanillas'], $_SESSION['idEmpresa']);
$tipoCampo = array(
    "Texto", "Numerico", "Formula", "Consolidado Pagos", "Consolidado Descuentos", "Consolidado General"
);
$tipoOperacion = array(
    "Pago", "Descuento"
);
$tipoValor = array(
    "General", "Individual"
);
?>
<input type="hidden" id="idHrmPlanilla" value="<?= $_REQUEST['idHrmPlanillas']; ?>"/>
<div class="row">
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Variables Globales
            </header>
            <div class="panel-body">
                <select class="form-control input-sm variables" id="variables" multiple="" style="height: 225px !important;">
                    <option>codigo_empleado</option>
                    <option>nombre_empleado</option>
                    <option>departamento</option>
                    <option>puesto</option>
                    <option>fecha_ingreso</option>
                    <option>salario_ordinario</option>
                    <option>bonificacion</option>
                    <option>comisiones</option>
                    <option>prestamos</option>
                    <option>hora_extra_diurna</option>
                    <option>hora_extra_noctura</option>
                    <option>hora_extra_mixta</option>
                </select>
            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Crear Nuevo Campo
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label>Nombre Campo</label>
                        <input type="hidden" id="idHrmConstructorPlanilla" class="form-control input-sm"/>
                        <input type="hidden" id="nombreCampoOld" class="form-control input-sm"/>
                        <input id="nombreCampo" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-12">
                        <label>Valor o Formula</label>
                        <textarea class="form-control input-sm" id="valor"></textarea>
                    </div>
                    <div class="col-lg-6">
                        <label>Tipo de Valor</label>
                        <select class="form-control input-sm" id="idTipoValor">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoValor as $key => $value) {
                                ?>
                                <option value="<?= ($key + 1); ?>"><?= $value; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Orden</label>
                        <input type="number" id="orden" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Tipo Campo</label>
                        <select class="form-control input-sm" id="idTipoCampo">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoCampo as $key => $value) {
                                ?>
                                <option value="<?= ($key + 1); ?>"><?= $value; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Tipo Operacion</label>
                        <select class="form-control input-sm" id="idTipoOperacion">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoOperacion as $key => $value) {
                                ?>
                                <option value="<?= ($key + 1); ?>"><?= $value; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Valor Máximo</label>
                        <input type="number" id="valorMaximo" class="form-control input-sm"/>
                    </div>
                    <div class="col-lg-12">
                        <div class="clearfix">&nbsp;</div>
                        <button class="btn btn-success btn-sm" id="add" onclick="saveCampoConstructorPlanilla();">
                            <i class="fa fa-floppy-o"></i> Guardar
                        </button>
                        <button class="btn btn-warning btn-sm" id="update" onclick="updateCampoConstructorPlanilla();">
                            <i class="fa fa-refresh"></i> Actualizar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="constructorPlanilla('<?= $_REQUEST['idHrmPlanillas']; ?>');">
                            <i class="fa fa-trash"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Listado de Campos
            </header>
            <div class="panel-body table-responsive">
                <table class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr class="info text-uppercase" style="font-weight: bold;">
                            <td></td>
                            <td>Nombre Campo</td>
                            <td>Valor / Formula</td>
                            <td>Tipo Valor</td>
                            <td>Orden</td>
                            <td>Tipo Campo</td>
                            <td>Tipo Operacion</td>
                            <td>Valor Máximo</td>
                            <td style="width: 100px !important;">Fecha de Creacion</td>
                            <td style="width: 100px !important;">Fecha de Actualizacion</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($data) === 0) {
                            ?>
                            <tr>
                                <td colspan="6" class="warning text-warning text-center"><strong>0 registros encontrados</strong></td>
                            </tr>
                            <?php
                        } else {
                            foreach ($data as $key => $value) {
                                ?>
                                <tr>
                                    <td>
                                        <button class="btn btn-warning btn-xs" onclick="getCampoConstructorPlanilla('<?= $value['id']; ?>');" <?= $status; ?>>
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        &nbsp;
                                        <button class="btn btn-danger btn-xs" onclick="deleteCampoConstructorPlanilla('<?= $value['id']; ?>');">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                    <td><?= $value['nombreCampo']; ?></td>
                                    <td><?= $value['valor']; ?></td>
                                    <td>
                                        <?php
                                        foreach ($tipoValor as $key => $value1) {
                                            if (($key + 1) == $value['idTipoValor']) {
                                                echo $value1;
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td><?= $value['orden']; ?></td>
                                    <td>
                                        <?php
                                        foreach ($tipoCampo as $key => $value1) {
                                            if (($key + 1) == $value['idTipoCampo']) {
                                                echo $value1;
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        foreach ($tipoOperacion as $key => $value1) {
                                            if (($key + 1) == $value['idTipoOperacion']) {
                                                echo $value1;
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td><?= $value['valorMaximo']; ?></td>
                                    <td><?= $value['created_at']; ?></td>
                                    <td><?= $value['updated_at']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
