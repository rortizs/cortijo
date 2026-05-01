<div class="row">
    <div class="col-lg-6">
        <label>Fondo Caja</label>
        <input type='text' class='form-control input-sm' id='montoF'/>
    </div>
    <div class="col-lg-6">
        <label>Tasa Cambio $.</label>
        <input type='text' class='form-control input-sm' id='tasaCambioF'/>
    </div>
    <div class="col-lg-12">
        &nbsp;<br/>
        <button class='btn btn-success btn-sm' onclick='saveFondoCaja();'>
            <i class='fa fa-print'></i> Apertura Caja
        </button>
        <button class="btn btn-danger btn-sm" onclick="window.location = 'logout.php?action=logout'">
            Salir del Sistema&nbsp;<i class="fa fa-power-off"></i>
        </button>
    </div>
</div>