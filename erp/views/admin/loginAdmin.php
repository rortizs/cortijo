<div class="row">
    <div class="col-lg-6">
        <label>Usuario</label>
        <input type='text' class='form-control input-sm' id='userAdmin'/>
        <input type='hidden' class='form-control input-sm' id='action'/>
    </div>
    <div class="col-lg-6">
        <label>Contraseña</label>
        <input type='password' class='form-control input-sm' id='pwdAdmin'/>
    </div>
    <div class="col-lg-12">
        &nbsp;<br/>
        <button class='btn btn-success btn-sm' onclick='loginAdmin();'>
            <i class='fa fa-check'></i> Ingresar
        </button>
        <button class='btn btn-danger btn-sm' onclick='cancelarModal();'>
            <i class='fa fa-times'></i> Cancelar
        </button>
    </div>
</div>
