<style>
    tr.group,
    tr.group:hover {
        background-color: #ddd !important;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Filtros Kardex
            </header>
            <div class="panel-body">
                <div class="col-lg-2">
                    <label><i class="fa fa-filter"></i> Inventario de</label>
                    <select class="form-control input-sm" id="ingresoA" onchange="ingresoA2();">
                        <option value="">[Seleccione...]</option>
                        <option value="1">BODEGA</option>
                        <option value="2">SUCURSAL</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label><i class="fa fa-map-marker"></i> Lugar</label>
                    <select class="form-control input-sm" id="idPuntoIngreso">
                    </select>
                </div>
                <div class="col-lg-2">
                    <label>Fecha Inicio:</label>
                    <div class='input-group date' id="fechaInicio">
                        <input type='text' class="form-control input-sm"/>
                        <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-lg-2">
                    <label>Fecha Fin:</label>
                    <div class='input-group date' id="fechaFin">
                        <input type='text' class="form-control input-sm"/>
                        <span class="input-group-addon btn-sm btn-primary"><span class="fa fa-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <label><i class="fa fa-filter"></i> Tipo de Ordenamiento:</label>
                    <select class="form-control input-sm" id="tipoOrdenamiento">
                        <option value="fecha">Por Fecha</option>
                        <option value="producto">Por Producto</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label><i class="fa fa-filter"></i> Tipo de Producto:</label>
                    <select class="form-control input-sm" id="idTipoProductos">
                        <option value="">Todos</option>
                        <option value="1">Producto</option>
                        <option value="2">Servicio</option>
                        <option value="3">Producto Fabricado</option>
                        <option value="4">Materia Prima</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>Producto</label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-sm" type="button" onclick="busqueda('inventario', 'Inventario de Productos','kardex');" style="z-index: 999;">Buscar <i class="fa fa-search"></i></button>
                        </span>
                        <input type="text" class="form-control input-sm" id="codigo" placeholder="Ingrese código del Producto">
                    </div>
                </div>
                <div class="col-lg-12">
                    <label>&nbsp;</label><br/>
                    <button type="button" class="btn btn-primary btn-sm" onclick="generarKardex();">
                        <span class="fa fa-search"></span> Consultar
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportarKardex();">
                        <span class="fa fa-file-excel-o"></span> Exportar a Excel
                    </button>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="row" id="divReporte">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body" id="reporte-container">

            </div>
        </section>
    </div>
</div>
