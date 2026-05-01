<?php
require_once("../../models/inventarios.php");
$inventarios = new Inventarios();
$getTrasladoDetalle = $inventarios->getTrasladoDetalle($_REQUEST['idTraslado']);
?>
<input type="hidden" id="estado" value="<?= $getTrasladoDetalle[0]['estado']; ?>"/>
<div class="table-responsive">
    <table class="table table-striped table-bordered" cellspacing="0" width="100%">    
        <thead>
            <tr class="info">
                <td>Codigo</td>
                <td>Descripcion</td>
                <td>Cantidad Enviada</td>
                <td>Cantidad Contada</td>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($getTrasladoDetalle as $key => $value) {
                ?>
                <tr>
                    <td><?= $value['sku']; ?></td>
                    <td><?= $value['descLarga']; ?></td>
                    <td><?= $value['cantidad']; ?></td>
                    <td>
                        <input type="text" title="<?= $value['cantidad']; ?>" name="<?= $value['idProductos']; ?>" class="cantidadContada form-control input-sm"/>
                    </td>
                </tr>
                <?php
            }
            ?>   
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-lg-12">
        <label>Observaciones</label>
        <textarea rows="4" class="form-control input-sm text-uppercase" id="observaciones"><?= $getTrasladoDetalle[0]['observaciones']; ?></textarea>
        <input type="hidden" id="idTraslado"/>
    </div>
    <div class="col-lg-6">
        <label>&nbsp;</label><br/>
        <button class="btn btn-primary btn-sm" onclick="ingresarTraslado();">
            <i class="fa fa-check"></i> Ingresar
        </button>
        <button class="btn btn-danger btn-sm" onclick="cancelarModal();">
            <i class="fa fa-trash"></i> Cancelar
        </button>
    </div>
</div> 
