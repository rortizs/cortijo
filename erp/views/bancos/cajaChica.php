<?php
/* BANCOS - CREAR CAJA CHICA
 * 
 */
session_start();
$tipoLiquidacion = array(
    "Caja Chica", "Gastos"
);
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-4">
                        <label>Documento</label>
                        <input type="hidden" id="idDocumentosCorrelativos" value=""/>
                        <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Correlativo</label>
                        <input type="text" class="form-control input-sm" id="correlativo" readonly="" value=""/>
                    </div>
                    <div class="col-lg-4">
                        <label>Tipo</label>
                        <select class="form-control input-sm" id="tipoLiquidacion">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($tipoLiquidacion as $key => $value) {
                                ?>
                                <option value="<?= ($key + 1); ?>"><?= $value; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Entregado A</label>
                        <input type="text" class="form-control input-sm" id="entregadoA"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Monto</label>
                        <input type="number" class="form-control input-sm" id="monto"/>
                    </div>
                    <div class="col-lg-12">
                        <label>Descripcion</label>
                        <textarea class="form-control input-sm" id="descripcion"></textarea>
                    </div>
                    <div class="col-lg-12 right">
                        <div class="clear">&nbsp;</div>
                        <button class="btn btn-primary btn-sm" onclick="guardarCajaChica();">
                            <i class="fa fa-list"></i> Guardar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="cancelarModal();">
                            <i class="fa fa-trash"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
