<?php
/* CONTABILIDAD - DIARIO
 * 
 */
session_start();
require_once("../../models/admin.php");
require_once("../../models/bancos.php");
$admin = new Admin();
$bancos = new Bancos();
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
$getCuentasBancarias = $bancos->getCuentasBancarias($_SESSION['idEmpresa']);
?>
<div class="row">
    <div class="col-lg-6 col-lg-offset-3">
        <section class="panel">
            <header class="panel-heading">
                Filtros del reporte
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label>Cuentas Bancarias</label>
                        <select id="idCuentaBancaria" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getCuentasBancarias as $key => $value) {
                                ?>
                                <option value="<?= $value['id']; ?>"><?= $value['cuenta']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label>Tipo Documento</label>
                        <select id="tipoDocumento" class="form-control input-sm">
                            <option value="TODOS">TODOS LOS DOCUMENTOS</option>
                            <option value="CHEQUE">CHEQUE</option>
                            <option value="DEPOSITO">DEPOSITO</option>
                            <option value="NOTA CREDITO">NOTA CREDITO</option>
                            <option value="NOTA DEBITO">NOTA DEBITO</option>
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label>Fecha Inicio</label>
                        <input type='text' class="form-control input-sm" id="fechaInicio"/>
                    </div>
                    <div class="col-lg-6">
                        <label>Fecha Fin</label>
                        <input type='text' class="form-control input-sm" id="fechaFin"/>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-warning btn-sm" onclick="imprimirDocumentosBancarios();">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
