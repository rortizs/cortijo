<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="row">
    <div class="col-lg-12">
        <label>Estado de Orden</label>
        <select id="statusOrden" class="form-control input-sm">
            <option value="">[Seleccione...]</option>
            <option value="2">Aprobada</option>
            <option value="3">Rechazada</option>
        </select>
    </div>
    <div class="col-lg-12">
        <label>Observaciones</label>
        <textarea rows="5" class="form-control input-sm" id="observaciones"></textarea>
    </div>
    <div class="col-lg-12">
        &nbsp;<br/>
        <button class='btn btn-success btn-sm' onclick='procesarOC();'>
            <i class='fa fa-check'></i> Procesar
        </button>
        <button class='btn btn-danger btn-sm' onclick='cancelarModal();'>
            <i class='fa fa-times'></i> Cancelar
        </button>
    </div>
</div>