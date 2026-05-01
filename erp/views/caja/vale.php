<div class="row">
    <div class="col-lg-12">
        <label>Solicitado Por</label>
        <input type='text' class='form-control input-sm' id='solicitado'/>
    </div>
    <div class="col-lg-12">
        <label>Monto</label>
        <input type='text' class='form-control input-sm' id='monto'/>
    </div>
    <div class="col-lg-12">
        <label>Motivo</label>
        <textarea rows='4' cols='50' class='form-control input-sm' id='motivoVale'></textarea>
    </div>
    <div class="col-lg-12">
        &nbsp;<br/>
        <button class='btn btn-success btn-sm' onclick='saveVal();'>
            <i class='fa fa-print'></i> Imprimir Vale
        </button>
        <button class='btn btn-danger btn-sm' onclick='cancelarModal();'>
            <i class='fa fa-times'></i> Cancelar
        </button>
    </div>
</div>
