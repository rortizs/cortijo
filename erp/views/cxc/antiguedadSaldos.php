<?php
/* CXC - Antiguedad de Saldos
 * 
 */
session_start();
require_once("../../models/contabilidad.php");
$contabilidad = new Contabilidad();
$centrosCosto = $contabilidad->getCentrosCosto($_SESSION['idEmpresa']);
?>
<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
        <section class="panel">
            <header class="panel-heading">
                Filtros Reporte de Antiguedad de Saldos
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
                        <label>Tipo de Reporte</label>
                        <select class="form-control input-sm" id="tipoReporte">
                            <option value="">Seleccione</option>
                            <option value="1">General</option>
                            <option value="2">Por Cliente</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label>Tipo de Consulta</label>
                        <select class="form-control input-sm" id="tipoConsulta">
                            <option value="">Seleccione</option>
                            <option value="1">Cuentas Generales</option>
                            <option value="2">Cuentas con Saldo</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label>Buscador de Clientes</label>
                        <div class="input-group m-bot15">
                            <span class="input-group-btn">
                                <button id="search" class="btn btn-primary btn-sm" type="button" onclick="busqueda('vw_clientes', 'Listado de Clientes', 'clientes');">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            <input type="hidden" id="idClientes">
                            <input class="form-control input-sm" type="text" id="nit" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                        loadClientesNit();" value="<?= $getVenta['nit']; ?>" placeholder="Oprima el boton para buscar cliente">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <label>Nombre Cliente</label>
                        <input type="text" class="form-control input-sm" id="nombre" value="<?= $getVenta['nombre']; ?>" readonly=""/>
                    </div>
                    <div class="col-lg-12">
                        <label>Centro Costo</label>
                        <select class="form-control input-sm" id="idCentrosCosto">
                            <option value="">Seleccione</option>
                            <?php
                            foreach ($centrosCosto as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-warning btn-sm" onclick="imprimirAntiguedadSaldos();">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
