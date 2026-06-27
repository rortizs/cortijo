<?php
session_start();
require_once ("../../models/inventarios.php");
require_once ("../../models/admin.php");
$inventarios = new Inventarios();
$admin = new Admin();
$getTiposVenta = $admin->getTiposVenta();
$facturacionConf = $inventarios->facturacionConf('pedidos', $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'], $_SESSION['idSucursalesS']);
?>
<html>
    <head></head>
    <body>
        <input type="hidden" id="idUsuariosPedidos" value="<?= $_SESSION['idUsuarios']; ?>"/>
        <input type='hidden' id="availablePrint" readonly=""/>
        <input type="hidden" id="idPedido"/>
        <input type="hidden" id="ingresoA" value="<?= $facturacionConf['ingresoA']; ?>"/>
        <input type="hidden" id="idPuntoIngreso" value="<?= $facturacionConf['idPuntoIngreso']; ?>"/>
        <input type="hidden" id="valExistencias" value="<?= $facturacionConf['valExistencias']; ?>"/>
        <input type="hidden" id="editarPrecio" value="<?= $facturacionConf['editarPrecio']; ?>">
        <div class="row">
            <!-- Documento y Info Cliente -->
            <div class="clear hidden-md hidden-lg">&nbsp;</div>
            <div class="clear hidden-md hidden-lg">&nbsp;</div>
            <div class="clear hidden-md hidden-lg">&nbsp;</div>
            <div class="clear hidden-md hidden-lg">&nbsp;</div>
            <div class="col-md-6 col-lg-6">
                <section class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                                <label>&nbsp;</label><br/>
                                <button id="btnCotizacion" class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_cotizaciones', 'Consulta de Cotizaciones', 'cotizaciones');">
                                    <i class="fa fa-search"></i> Buscar Cotizaciones
                                </button>
                            </div>
                            <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                                <label>Fecha Pedido</label>
                                <input type="text" class="form-control input-sm" id="fechaPedido" value="<?= $inventarios->dateViews; ?>"/>
                            </div>
                            <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                                <label>Documento</label>
                                <select class="form-control input-sm" id="tipoDocumento" onchange="loadDocumentosCorrelativo();">
                                </select>
                            </div>
                            <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                                <label>Correlativo</label>
                                <input type='text' class="form-control input-sm" id="correlativo" readonly=""/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <label>INGRESE NIT/CODIGO</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-sm" type="button" onclick="busqueda('vw_clientes', 'Busqueda Clientes', 'clientes');"><i class="fa fa-search"></i></button>
                                    </span>
                                    <input type='text' class="form-control input-sm facturacion" id="nit" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                loadClientesNit();" tabindex="2"/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <label>CODIGO</label>
                                <input type="text" class="form-control input-sm facturacion" value="" id="codigoC" tabindex="3"/>
                            </div>  
                            <div class="col-lg-6">
                                <label>Nombre Facturación</label>
                                <input type="hidden" id="idClientes" value="0"/>
                                <input type="hidden" id="availableCreditLimit"/>
                                <input type="text" class="form-control input-sm pedidos" value="" id="nombre" tabindex="2"/>
                            </div>
                            <div class="col-lg-6">
                                <label>Teléfono</label>
                                <input type="text" class="form-control input-sm pedidos" value="" id="telefono" tabindex="3"/>
                            </div>
                            <div class="col-lg-6">
                                <label>Direccion</label>
                                <input type="text" class="form-control input-sm pedidos" value="" id="direccion" tabindex="4"/>
                            </div>
                            <div class="col-lg-12">
                                <label>Observaciones</label>
                                <textarea class="form-control input-sm pedidos" id="observaciones" tabindex="5"></textarea>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- /Documento y Info Cliente -->
            <!-- Facturacion -->
            <div class="col-md-6 col-lg-6">
                <section class="panel">
                    <div class="panel-body">
                        <input type="hidden" id="numeroProductos"/>
                        <input type="hidden" id="subTotal" value="0"/>
                        <input type="hidden" id="descuentoM" value="0">
                        <input type="hidden" id="total" value="0"/>
                        <input type="hidden" id="cambio" value="0">
                        <input type="hidden" id="totalPagado" value="0" readonly="">
                        <div class="row">
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                                Subtotal
                            </div>
                            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtSubTotal">
                                0.00
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal" style="border-bottom: 2px solid #000;">
                                Descuento
                            </div>
                            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtDescuento" style="border-bottom: 2px solid #000;">
                                0.00
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 txtTotal">
                                Total
                            </div>
                            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 total text-right" id="txtTotal">
                                0.00
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <label>Vendedor</label>
                                <select class="form-control facturacion input-sm" id="vendedores">
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label>Tipo Venta</label>
                                <select class="form-control input-sm facturacion" id="tipoVenta" onchange="loadCamposHidden();">
                                    <?php
                                    foreach ($getTiposVenta as $key => $value) {
                                        $selected = "";
                                        if ($value['predefinido'] == '1') {
                                            $selected = "selected=''";
                                        }
                                        ?>
                                        <option value="<?= $value['id']; ?>" <?= $selected; ?>><?= $value['descripcion']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                &nbsp;<br/>
                                <button type="button" id="add" class="btn btn-success btn-sm" onclick="generarPedido();">
                                    <span class="fa fa-check"></span>&nbsp;Generar Pedido
                                </button>
                                <button type="button" id="edit" class="btn btn-warning btn-sm" onclick="actualizarPedido();">
                                    <span class="fa fa-refresh"></span>&nbsp;Actualizar Pedido
                                </button>
                                <button type="button" id="delete" class="btn btn-danger btn-sm" id="cancelarVenta" onclick="loadModuloPedidos();">
                                    <span class="fa fa-trash"></span>&nbsp;Cancelar Pedido
                                </button>
                                <button type="button" id="back" class="btn btn-info btn-sm" id="cancelarVenta" onclick="loadConsultaPedidos();">
                                    <span class="fa fa-undo"></span>&nbsp;Regresar
                                </button>   
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- END Facturacion -->
            <!-- Listado de Productos venta -->
            <div class="clear">&nbsp;</div>
            <div class="col-md-12 col-lg-12">
                <section class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12 hidden-xs hidden-sm">
                                <button class="btn btn-info btn-sm" onclick="templateDownload('pedidosDetalle');">
                                    <i class="fa fa-download"></i> Descargar Plantilla
                                </button>
                                <div class="input-group m-bot15 col-lg-4 pull-right">
                                    <span class="input-group-btn">
                                        <button class="btn btn-success btn-sm" id="btnUpload" onclick="uploadTemplatePedidos();">
                                            <i class="fa fa-upload"></i> Importar Plantilla
                                        </button>
                                    </span>
                                    <input class="form-control input-sm" type="file" readonly="" id="my_file">
                                </div>
                            </div>
                            <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                                <label>Codigo Producto</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-info btn-sm" id="search" type="button" onclick="busqueda('inventario', 'Consulta de Productos en Inventario', 'pedidos');"> 
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                    <input type="text" class="form-control input-sm pedidos" id="codigo" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                                loadProductoByCodigo();" tabindex="6" style="text-transform: uppercase !important;">
                                </div>
                            </div>
                            <div class="col-sx-3 col-sm-3 col-md-3 col-lg-3">
                                <label>Descripcion</label>
                                <input type="text" class="form-control input-sm" id="descProducto" readonly="">
                                <input type="hidden" id="idProducto"/>
                                <input type="hidden" id="tipoProducto"/>
                            </div>
                            <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                                <label>Existencias</label>
                                <input type="text" class="form-control input-sm" id="existencia" readonly="">
                            </div>
                            <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                                <label>Precio</label>
                                <?php
                                if($_SESSION['dbProject']==='erp_josue'){
                                    ?>
                                    <select class="form-control input-sm" id="precioProducto">
                                    </select>
                                    <?php
                                }else{
                                    ?>
                                    <input type="text" class="form-control input-sm" id="precioProducto">
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="col-sx-2 col-sm-2 col-md-2 col-lg-2">
                                <label>Cantidad</label>
                                <input type="text" class="form-control input-sm" id="cantidadProducto" value="" onKeydown="javascript: if (event.keyCode == 13 || event.keyCode == 9)
                                            agregarProductoPedido();">
                            </div>
                            <div class="clear">&nbsp;</div>
                            <div class="col-lg-12">
                                <table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead>
                                        <tr class="info">
                                            <td>No.</td>
                                            <td style="width: 10px;">&nbsp;</td>
                                            <td>Codigo</td>
                                            <td>Descripcion</td>
                                            <?php
                                            if ($_SESSION['dbProject'] === 'pos_kasualcosmeticos') {
                                                ?>
                                                <td>Item</td>
                                                <td>Marca</td>
                                                <?php
                                            }
                                            ?>
                                            <td class="text-right">Cantidad</td>
                                            <td class="text-right">Precio</td>
                                            <td class="text-right">Total</td>
                                        </tr>
                                    </thead>
                                    <tbody id="detalle">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <!-- END Listado de Productos venta -->
    </body>
</html>