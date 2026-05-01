<?php
session_start();
?>
<div class="row">
    <div class="col-lg-8 col-lg-offset-2">
        <section class="panel">
            <header class="panel-heading">
                <i class="fa fa-file-text-o"></i> Registrar Compra de DTE
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-4">
                        <label>Año</label>
                        <select class="form-control input-sm" id="dteAnio">
                            <?php
                            $anioActual = (int) date('Y');
                            for ($a = $anioActual; $a >= 2022; $a--) {
                                $sel = ($a == $anioActual) ? 'selected' : '';
                                echo "<option value=\"$a\" $sel>$a</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>Mes de inicio</label>
                        <select class="form-control input-sm" id="dteMes">
                            <?php
                            $meses = [
                                1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',
                                5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',
                                9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
                            ];
                            $mesActual = (int) date('n');
                            foreach ($meses as $num => $nombre) {
                                $sel = ($num == $mesActual) ? 'selected' : '';
                                echo "<option value=\"$num\" $sel>$nombre</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label>DTEs comprados</label>
                        <input type="number" class="form-control input-sm" id="dteComprados"
                               placeholder="Ej: 1500" min="1" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-success btn-sm" onclick="guardarComprasDte();">
                            <i class="fa fa-floppy-o"></i> Guardar
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="getComprasDte();">
                            <i class="fa fa-refresh"></i> Refrescar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="clear">&nbsp;</div>

<div class="row">
    <div class="col-lg-8 col-lg-offset-2">
        <section class="panel">
            <header class="panel-heading">
                <i class="fa fa-list"></i> Historial de Compras DTE
            </header>
            <div class="panel-body table-responsive">
                <table id="tblComprasDte" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Año</th>
                            <th>Mes inicio</th>
                            <th class="text-right">DTEs comprados</th>
                            <th class="text-right">DTEs emitidos</th>
                            <th class="text-right">Saldo</th>
                            <th>Fecha registro</th>
                            <th class="text-center">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody id="tblComprasDteBody">
                        <tr><td colspan="6" class="text-center">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
    getComprasDte();
</script>
