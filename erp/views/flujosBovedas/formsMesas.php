<?php
/*
 * Cierre de Boveda Mesas
 */
session_start();
?>
<input type="hidden" id="idEmpresaIngreso" value="<?=$_SESSION['idEmpresa'];?>"/>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <label>Sala</label>
                    <select id="idSalas" class="form-control input-sm" onchange="getFormSalaMesas();">
                    </select>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <label>Fecha de Arqueo</label>
                    <div class='input-group date' id='fechaArqueo'>
                        <input type='text' class="form-control input-sm"/>
                        <span class="input-group-addon btn-sm btn-primary">
                            <span class="fa fa-calendar"></span>
                        </span>
                    </div>
                    <input type='hidden' id="idArqueoBoveda"/>
                </div>
                <div class="col-lg-6">
                    <label>&nbsp;</label><br/>
                    <button type="button" class="btn btn-info btn-sm" onclick="consultarCierreMesas();" title="Consultar Cierre">
                        <span class="fa fa-search"></span>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="guardarArqueoMesas();" id="save" title="Guardar Cierre">
                        <span class="fa fa-floppy-o"></span>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="updateArqueoMesas();" id="update" title="Actualizar Cierre">
                        <span class="fa fa-refresh"></span>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="cancelUpdateArqueo('2');" id="cancel" title="Cancelar Cierre">
                        <span class="fa fa-trash"></span>
                    </button>
                </div>
            </div>
        </section>
    </div>
</div>
<table id="formArqueo1" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <td colspan="6">Apertura</td>
        </tr>
        <tr>
            <td style="width: 35% !important;">Descripcion</td>
            <td style="width: 20% !important;">Cuenta Contable</td>
            <td style="width: 20% !important;">Centro Costo</td>
            <td style="width: 10% !important;">Operacion</td>
            <td style="width: 10% !important;">Valor</td>
            <td style="width: 5% !important;">
                <button class="btn btn-primary btn-sm" onclick="addRowMesas('formArqueo1');">
                    <i class="fa fa-plus"></i>
                </button>
            </td>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr class="warning">
            <td colspan="4" style="width: 70% !important;">Total Apertura</td>
            <td colspan="2">
                <input type="text" id="totalIngreso1" class="form-control input-sm" readonly=""/>
            </td>
        </tr>
    </tfoot>
</table>
<table id="formArqueo2" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <td colspan="6">Win General del Casino</td>
        </tr>
        <tr>
            <td style="width: 35% !important;">Descripcion</td>
            <td style="width: 20% !important;">Cuenta Contable</td>
            <td style="width: 20% !important;">Centro Costo</td>
            <td style="width: 10% !important;">Operacion</td>
            <td style="width: 10% !important;">Valor</td>
            <td style="width: 5% !important;">
                <button class="btn btn-primary btn-sm" onclick="addRowMesas('formArqueo2');">
                    <i class="fa fa-plus"></i>
                </button>
            </td>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr class="warning">
            <td colspan="4" style="width: 70% !important;">Total Win General del Casino</td>
            <td colspan="2">
                <input type="text" id="totalIngreso2" class="form-control input-sm" readonly=""/>
            </td>
        </tr>
    </tfoot>
</table>
<table id="formArqueo3" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <td colspan="6">Incremento / Disminución Bóveda</td>
        </tr>
        <tr>
            <td style="width: 35% !important;">Descripcion</td>
            <td style="width: 20% !important;">Cuenta Contable</td>
            <td style="width: 20% !important;">Centro Costo</td>
            <td style="width: 10% !important;">Operacion</td>
            <td style="width: 10% !important;">Valor</td>
            <td style="width: 5% !important;">
                <button class="btn btn-primary btn-sm" onclick="addRowMesas('formArqueo3');">
                    <i class="fa fa-plus"></i>
                </button>
            </td>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr class="warning">
            <td colspan="4" style="width: 70% !important;">Total Incremento / Disminución Bóveda</td>
            <td colspan="2">
                <input type="text" id="totalIngreso3" class="form-control input-sm" readonly=""/>
            </td>
        </tr>
    </tfoot>
</table>
<table id="formArqueo4" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <thead>
        <tr>
            <td colspan="6">Cuadre</td>
        </tr>
        <tr>
            <td style="width: 35% !important;">Descripcion</td>
            <td style="width: 20% !important;">Cuenta Contable</td>
            <td style="width: 20% !important;">Centro Costo</td>
            <td style="width: 10% !important;">Operacion</td>
            <td style="width: 10% !important;">Valor</td>
            <td style="width: 5% !important;">
                <button class="btn btn-primary btn-sm" onclick="addRowMesas('formArqueo4');">
                    <i class="fa fa-plus"></i>
                </button>
            </td>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr class="warning">
            <td colspan="4" style="width: 70% !important;">Total Cuadre</td>
            <td colspan="2">
                <input type="text" id="totalIngreso4" class="form-control input-sm" readonly=""/>
            </td>
        </tr>
    </tfoot>
</table>
<table id="resultArqueo" class="table table-striped table-bordered" cellspacing="0" width="100%">
    <tr class="warning">
        <td colspan="4" style="width: 81.5% !important;">Cuadre Cierre</td>
        <td>
            <input type="text" id="totalIngreso" class="form-control input-sm" readonly=""/>
        </td>
    </tr>
</table>
