<?php
session_start();
?>
<div class="row">
    <div class="col-lg-6 col-lg-offset-3">
        <section class="panel">
            <header class="panel-heading">
                Filtros de Busqueda
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <label>Rol</label>
                        <select class="form-control input-sm" id="idNivel">
                            <option value="">[Seleccione...]</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Modulo</label>
                        <select class="form-control input-sm" id="idModulos">
                            <option value="">[Seleccione...]</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-info btn-sm" onclick="getPermisos();">
                            <i class="fa fa-search"></i>Buscar
                        </button>
                        <button class="btn btn-success btn-sm" onclick="guardarPermisos();">
                            <i class="fa fa-floppy-o"></i>Guardar
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="clearInput();">
                            <i class="fa fa-trash"></i>Filtros
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="clear">&nbsp;</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body table-responsive">
                <table id="permisos" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td>OPCIONES</td>
                            <td class="text-center">Activar<br/><input type="checkbox" class="checkall checkall1" data-id="1"/></td>
                            <td class="text-center">Boton Nuevo<br/><input type="checkbox" class="checkall checkall2" data-id="2"/></td>
                            <td class="text-center">Boton Editar<br/><input type="checkbox" class="checkall checkall3" data-id="3"/></td>
                            <td class="text-center">Boton Eliminar<br/><input type="checkbox" class="checkall checkall4" data-id="4"/></td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>