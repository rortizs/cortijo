/* 
 * INVENTARIOS
 * @author Jonathan Juarez
 * @version 2.0 20180719
 */
var contador1 = 0;
var idOrdenesCompra = null;
$(document).ready(function () {
    //loadRecepcionCompras();
    //loadFacturacion();
    //ingresarAjuste();
    //loadRecepcionImportacion();
    //loadTraslados();
    //nuevaOrdenCompra();
    //loadData('vw_compras', 'Compras', 'Operacion de Compra');
//    loadConsumoMaterialesDelispan();
});

var pathJasper = "./views/jasper/";

function crearOrdenCompra() {
    $.post('views/inventarios/ordenCompra.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Inventarios');
        $("#opcion").html('Ordenes de Compra');
        $("#subopcion").html('Nueva Orden');
        $("#fechaSolicitud,#fechaEntrega").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
        loadDocumentos();
        getOrdenCompraDetalleUsuario();
    });
}
//
function loadProveedor(id) {
    params = {
        service: 'getProveedor',
        idProveedor: id
    };
    $.post('controllers/adminController.php', params, function (data) {
        $.each(data, function (key, val) {
            $("#idProveedores").val(val.id);
            $("#nit").val(val.nitP);
            $("#nombre").val(val.descripcion);
            $("#direccion").val(val.direccionP);
            $("#diasCreditoP").val(val.diasCredito);
            $("#pequenoContribuyente").val(val.idPequenoContribuyente);
        });
    }, 'json').done(function () {
        $("#modal1").modal('hide');
        var fechaPago = moment($("#fechaPago").val(), "DD-MM-YYYY").add(accounting.unformat($("#diasCreditoP").val()), 'days').format('DD-MM-YYYY');
        $("#fechaPago").val(fechaPago);
    });
}
//
function loadProveedorByNit() {
    params = {
        service: 'getProveedorByNit',
        nit: $("#nit").val()
    };
    $.post('controllers/adminController.php', params, function (data) {
        if (data == null) {
            alert('NIT de proveedor no existe en el sistema');
            busqueda('proveedores', 'Listado de Proveedores', 'proveedores');
            AddRecordBusqueda('proveedores', 'Proveedores');
        } else {
            $.each(data, function (key, val) {
                $("#idProveedores").val(val.id);
                $("#nit").val(val.nitP);
                $("#nombre").val(val.descripcion);
                $("#direccion").val(val.direccionP);
                $("#diasCreditoP").val(val.diasCredito);
                $("#pequenoContribuyente").val(val.idPequenoContribuyente);
                $("#nombreCheque").val(val.nombreChequeP);
            });
        }
    }, 'json');
}
//
function addItemOrdenCompra() {
    var tipoCambio = accounting.unformat($("#tipoCambio").val());
    var cantidad = accounting.unformat($("#cantidadProducto").val());
    var precio = accounting.unformat($("#precioProducto").val());
    var total = precio * cantidad;
    if (!cantidad || !precio) {
        alert('Debe ingresar cantidad y precio para agregar un producto al listado');
    } else {
        params = {
            service: 'addItemOrdenCompra',
            idProductos: $("#idProducto").val(),
            cantidad: cantidad,
            precio: precio,
            total: total,
            idOrdenCompra: $("#idOrdenCompra").val()
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    getOrdenCompraDetalle(params.idOrdenCompra);
                    clear();
                } else if (val.message === 'exists') {
                    alert('Producto ya fue ingresado al listado');
                } else {
                    alert('Error al agregar producto ' + val.result);
                }
            });
        }, 'json');
    }
}
//
function getOrdenCompraDetalle(idOrdenCompra, action, modulo) {
    switch (action) {
        case 'get':
            params = {
                service: 'getOrdenCompraDetalle',
                idOrdenCompra: idOrdenCompra,
                modulo: modulo
            };
            $.post('controllers/inventariosController.php', params, function (data) {
                var modulos = params.modulo;
                $.each(data, function (key, val) {
                    switch (modulos) {
                        case 'importaciones':
                            addItemImportacion(undefined, undefined, val.idProductos, val.cantidad, 0, 0, val.precio);
                            break;
                        default :
                            addItemCompra(val.descLarga, val.idProductos, 0, val.precio, val.cantidad, 'get');
                            break;
                    }
                });
            }, 'json');
            break;
        default:
            $("#detalle").html('');
            $("#summary").html('');
            params = {
                service: 'getOrdenCompraDetalle',
                idOrdenCompra: idOrdenCompra
            };
            $.post('controllers/inventariosController.php', params, function (data) {
                if (data === null) {
                    $("#numeroProductos").val(0);
                } else {
                    $("#numeroProductos").val(data.length);
                }
                var datos = "";
                var summary = "";
                var total1 = 0;
                var total2 = 0;
                if (data === null) {
                    datos += "<tr>";
                    datos += "<td colspan='6' align='center'>0 productos encontrados</td>";
                    datos += "</tr>";
                } else {
                    $.each(data, function (key, val) {
                        total1 += parseFloat(val.cantidad);
                        total2 += parseFloat(val.total);
                        datos += "<tr>";
                        datos += "<td>";
                        datos += "<button class='btn btn-xs btn-danger' onclick='removeItemOrdenCompra(" + val.id + "," + idOrdenCompra + ")'><i class='fa fa-trash'></i></button>";
                        datos += "<button class='btn btn-xs btn-warning' onclick='updateItemOrdenCompra(" + val.id + "," + idOrdenCompra + ")'><i class='fa fa-refresh'></i></button>";
                        datos += "</td>";
                        datos += "<td>" + val.sku + "</td>";
                        datos += "<td>" + val.descLarga + "</td>";
                        datos += "<td><input type='text' value='" + val.precio + "' class='form-control input-sm text-right' id='precio-" + val.id + "'/></td>";
                        datos += "<td><input type='text' value='" + val.cantidad + "' class='form-control input-sm text-right' id='cantidad-" + val.id + "'/></td>";
                        datos += "<td align='right'>" + val.total + "</td>";
                        datos += "</tr>";
                    });
                    $("#totalOC").val(total2.toFixed(2));
                    summary += "<tr class='info'>";
                    summary += "<td colspan='4'>Totales</td>";
                    summary += "<td align='right'>" + accounting.unformat(total1) + "</td>";
                    summary += "<td align='right'>" + accounting.formatNumber(total2, 2) + "</td>";
                    summary += "</tr>";
                }
                $("#detalle").append(datos);
                $("#summary").append(summary);
            }, 'json').done(function () {
                if ($("#statusOrdenCompra").val() === '') {
                    $('button').attr('disabled', false);
                    $('input').attr('disabled', false);
                } else if ($("#statusOrdenCompra").val() !== 'Pendiente') {
                    $('button').attr('disabled', true);
                    $('input').attr('disabled', true);
                    $('#cancel').attr('disabled', false);
                }
            });
            break;
    }
}
//
function removeItemOrdenCompra(item) {
    params = {
        service: 'removeItemOrdenCompra',
        item: item
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
            console.log(val.message);
        });
    }, 'json').done(function () {
        getOrdenCompraDetalle();
    });
}
//
function loadComponentes() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        params = {
            idProductoPrincipal: id
        };
        $.post('views/inventarios/productosComponentes.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $("#add").show();
            $("#update").hide();
        });
    }
}
//
function loadProductoComponente(idProducto, modulo) {
    params = {
        service: 'productoComponente',
        idProducto: idProducto
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        if (modulo !== undefined) {
            $("#idProducto").val(data['id']);
            $("#codigo").val(data['sku']);
            $("#descProducto").val(data['descLarga']);
            $("#unidadMedida").val(data['medidaInventario']);
        } else {
            $("#idProducto").val(data['id']);
            $("#descProducto").val(data['sku'] + ' - ' + data['descLarga']);
            $("#medida").val(data['medidaInventario']);
            $("#idMedidas").val(data['idMedidasInventario']);
            $("#costo").val(data['precioCosto']);
        }
    }, 'json').done(function () {
        $("#modal1").modal('hide');
    });
}
//
function ingresarProductoListadoComponentes() {
    var idProductoPrincipal = $("#idProductoPrincipal").val();
    var idProducto = $("#idProducto").val();
    var idMedidas = $("#idMedidas").val();
    var unidades = $("#unidades").val();
    var costo = $("#costo").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!idProducto) {
        flag = false;
        errorMsg += 'No hay producto seleccionado para agregar como componente\n';
    }
    if (!unidades || unidades === '0') {
        flag = false;
        errorMsg += 'Ingrese la cantidad del producto a agregar\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            idProductoPrincipal: idProductoPrincipal,
            idProducto: idProducto,
            idMedidas: idMedidas,
            unidades: unidades,
            costo: costo,
            flag: 1
        };
        $.post('views/inventarios/productosComponentes.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $("#add").show();
            $("#update").hide();
            $("#message").fadeOut(3000);
        });
    }
}
//
function loadComponente(idProducto, codigo, descLarga, idMedidas, medida, costo, unidades) {
    $("#idProducto").val(idProducto);
    $("#descProducto").val(codigo + ' - ' + descLarga);
    $("#medida").val(medida);
    $("#idMedidas").val(idMedidas);
    $("#costo").val(costo);
    $("#unidades").val(unidades);
    $("#add").hide();
    $("#update").show();
}
//
function actualizarProductoListadoComponentes() {
    params = {
        idProductoPrincipal: $("#idProductoPrincipal").val(),
        idProducto: $("#idProducto").val(),
        idMedidas: $("#idMedidas").val(),
        unidades: $("#unidades").val(),
        costo: $("#costo").val(),
        flag: 2
    };
    $.post('views/inventarios/productosComponentes.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#add").show();
        $("#update").hide();
        $("#message").fadeOut(3000);
    });
}
//
function eliminarComponente(idComponente) {
    params = {
        idProductoPrincipal: $("#idProductoPrincipal").val(),
        idComponente: idComponente,
        flag: 3
    };
    $.post('views/inventarios/productosComponentes.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#add").show();
        $("#update").hide();
        $("#message").fadeOut(3000);
    });
}
//
//
function loadPresentaciones() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        params = {
            idProductoPrincipal: id
        };
        $.post('views/inventarios/productosPresentaciones.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $('#dataTable').DataTable({
                "pagingType": "full",
                "language": {
                    "processing": "Cargando informacion por favor espere un momento",
                    "search": "Buscar Registro&nbsp;",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "0 registros encontrados",
                    "info": "Mostrando pagina _PAGE_ de _PAGES_",
                    "infoEmpty": "0 registros encontrados",
                    "infoFiltered": "(filtrados de _MAX_ registros totales)",
                    "oPaginate": {
                        "sPrevious": "Anterior",
                        "sNext": "Siguiente",
                        "sFirst": "Inicio",
                        "sLast": "Final"
                    }
                }
            });
        });
    }
}
//
function ingresarPresentacion() {
    params = {
        descripcion: $("#descripcion").val(),
        idProductoPrincipal: $("#idProductoPrincipal").val(),
        idMedidas: $("#idMedidas").val(),
        unidades: $("#unidades").val(),
        precioVenta: $("#precioVenta").val(),
        flag: 1
    };
    $.post('views/inventarios/productosPresentaciones.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#dataTable').DataTable({
            "pagingType": "full",
            "language": {
                "processing": "Cargando informacion por favor espere un momento",
                "search": "Buscar Registro&nbsp;",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "0 registros encontrados",
                "info": "Mostrando pagina _PAGE_ de _PAGES_",
                "infoEmpty": "0 registros encontrados",
                "infoFiltered": "(filtrados de _MAX_ registros totales)",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                    "sFirst": "Inicio",
                    "sLast": "Final"
                }
            }
        });
        $("#add").show();
        $("#update").hide();
        $("#message").fadeOut(3000);
    });
}
//
function eliminarPresentacion(idComponente) {
    params = {
        idProductoPrincipal: $("#idProductoPrincipal").val(),
        idPresentacion: idComponente,
        flag: 3
    };
    $.post('views/inventarios/productosPresentaciones.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#dataTable').DataTable({
            "pagingType": "full",
            "language": {
                "processing": "Cargando informacion por favor espere un momento",
                "search": "Buscar Registro&nbsp;",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "0 registros encontrados",
                "info": "Mostrando pagina _PAGE_ de _PAGES_",
                "infoEmpty": "0 registros encontrados",
                "infoFiltered": "(filtrados de _MAX_ registros totales)",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                    "sFirst": "Inicio",
                    "sLast": "Final"
                }
            }
        });
        $("#message").fadeOut(3000);
    });
}
//
function guardarOrdenCompra() {
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var correlativo = $("#correlativo").val();
    var solicitadoPor = $("#solicitadoPor").val();
    var idHrmDepartamentos = $("#idHrmDepartamentos").val();
    var observaciones = $("#observaciones").val();
    var idTipoOrdenCompra = $("#idTipoOrdenCompra").val();
    var fechaArribo = $("#fechaArribo").val();
    var tipoCambio = $("#tipoCambio").val();
    var idMonedas = $("#idMonedas").val();
    var moneda = $("#idMonedas option:selected").text();
    var idProveedores = $("#idProveedores").val();
    var numeroProductos = accounting.unformat($("#numeroProductos").val());
    var totalOC = accounting.unformat($("#totalOC").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!solicitadoPor) {
        flag = false;
        errorMsg += 'Ingrese nombre del solicitante\n';
    }
    if (!idHrmDepartamentos) {
        flag = false;
        errorMsg += 'Seleccione departamento del solicitante\n';
    }
    if (!observaciones) {
        flag = false;
        errorMsg += 'Ingrese observaciones\n';
    }
    if (!idTipoOrdenCompra) {
        flag = false;
        errorMsg += 'Seleccione tipo de orden de compra\n';
    }
    if (!idMonedas) {
        flag = false;
        errorMsg += 'Seleccione moneda\n';
    }
    if (!fechaArribo) {
        flag = false;
        errorMsg += 'Ingrese fecha estimada de arribo\n';
    }
    if (!tipoCambio && idTipoOrdenCompra === '2') {
        flag = false;
        errorMsg += 'Ingrese tipo de cambio\n';
    }
    if (!idProveedores) {
        flag = false;
        errorMsg += 'Seleccione proveedor\n';
    }
    if (!numeroProductos) {
        flag = false;
        errorMsg += 'No tiene productos agregados a la orden de compra\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var r = confirm("¿Esta seguro de guardar esta orden de compra?");
        if (r == true) {
            params = {
                service: 'guardarOrdenCompra',
                tipoDocumento: tipoDocumento,
                correlativo: correlativo,
                solicitadoPor: solicitadoPor,
                idHrmDepartamentos: idHrmDepartamentos,
                observaciones: observaciones,
                idTipoOrdenCompra: idTipoOrdenCompra,
                fechaArribo: fechaArribo,
                tipoCambio: tipoCambio,
                idProveedores: idProveedores,
                monto: totalOC,
                idMonedas: idMonedas,
                moneda: moneda
            };
            $.post('controllers/inventariosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Orden de Compra guardada exitosamente');
                        var url = pathJasper + "ordenCompra_pdf.php?idComprasOrden=" + val.idOrdenCompra;
                        window.open(url);
                        loadData('vw_comprasOrdenes', 'Inventarios', 'Ordenes de Compra', 0, 0, 0);
                    } else {
                        alert(val.message);
                    }
                });
            }, 'json');
        } else {
            return false;
        }
    }
}
//RECEPCION DE COMPRAS
function loadRecepcionCompras(idCompra) {
    params = {
        idCompra: idCompra
    };
    $.post('views/compras/recepcionCompras.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Compras');
        $("#opcion").html('Ingreso de Compras/Gastos');
        $('#fechaContabilizacion,#fechaFactura,#fechaPago').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadDocumentos('recepcionCompra');
        //CALCULO IVA CUANDO SE APLICA DESCUENTO MONEDA, EXENTO O INGUAT
        $("#valorFactura,#descuentoM,#exento,#inguat").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                calculoIvaCompras('1');
            }
        });
        //RECALCULA IVA AL MOMENTO DE APLICAR DESCUENTO PORCENTAJE
        $("#descuentoP").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                calculoIvaCompras('2');
            }
        });
        //HABILITA BOTON DE GENERACION DE IMPUESTO
        $("#idTipoOperacion").on('change', function () {
            //tablaImpuesto
            if ($(this).val() === '4') {
                $("#tablaImpuesto").removeClass('hidden');
                $("#exento").attr({
                    readonly: true
                }).val('');
            } else {
                $("#tablaImpuesto").addClass('hidden');
                $("#exento").attr({
                    readonly: false
                });
            }
        });
    }).done(function () {
        if ($("#idTipoOperacion").val() === '4') {
            $("#tablaImpuesto").removeClass('hidden');
            $("#exento").attr({
                readonly: true
            });
        } else {
            $("#tablaImpuesto").addClass('hidden');
            $("#exento").attr({
                readonly: true
            });
        }
        if (idCompra !== undefined) {
            $("#tipoDocumento").attr('disabled', true);
            $("#serieFactura").attr('readonly', true);
            $("#noFactura").attr('readonly', true);
        } else {
            $("#tipoDocumento").attr('disabled', false);
            $("#serieFactura").attr('readonly', false);
            $("#noFactura").attr('readonly', false);
        }
        //
        $("#generaIva").on('change', function () {
            if (accounting.unformat($(this).val()) === 2) {
                $("#subTotal").val(accounting.formatNumber($("#valorFactura").val(), 2));
                $("#iva").val(0);
                $("#idSucursales").attr('disabled', true);
                $("#serieFactura").attr('readonly', true);
                $("#noFactura").attr('readonly', true);
            } else {
                calculoIvaCompras('1');
                $("#idSucursales").attr('disabled', false);
                $("#serieFactura").attr('readonly', false);
                $("#noFactura").attr('readonly', false);
            }
        });
        getCompraDetalle(idCompra);
        idOrdenesCompra = [];
    });
}
function loadBodegasEmpresa(container, idPuntoIngreso) {
    params = {
        service: 'getBodegas'
    };
    $("#" + container).html('');
    $.post('controllers/adminController.php', params, function (data) {
        $("#" + container).append("<option value=''>[Seleccione...]</option>");
        $.each(data, function (key, val) {
            if (val.id === idPuntoIngreso) {
                $("#" + container).append("<option value='" + val.id + "' selected>" + val.descripcion + "</option>");
            } else {
                $("#" + container).append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }
        });
    }, 'json');
    //loadSucursalesEmpresa();
}
//
function loadSucursalesEmpresa(container, idPuntoIngreso) {
    params = {
        service: 'getSucursales'
    };
    $("#" + container).html('');
    $.post('controllers/adminController.php', params, function (data) {
        $("#" + container).append("<option value=''>[Seleccione...]</option>");
        $.each(data, function (key, val) {
            if (val.id === idPuntoIngreso) {
                $("#" + container).append("<option value='" + val.id + "' selected>" + val.descripcion + "</option>");
            } else {
                $("#" + container).append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }

        });
    }, 'json');
    //loadSucursalesEmpresa();
}
//
function addItemCompra(descProducto, idProducto, descuento, precio, cantidad, action) {
    var descProducto = (descProducto !== undefined ? descProducto : $("#descProducto").val());
    var idProducto = (idProducto !== undefined ? idProducto : accounting.unformat($("#idProducto").val()));
    var descuento = (descuento !== undefined ? descuento : accounting.unformat($("#descuentoProducto").val()));
    var precio = (precio !== undefined ? precio : accounting.unformat($("#precioProducto").val()));
    var cantidad = (cantidad !== undefined ? cantidad : accounting.unformat($("#cantidadProducto").val()));
    var totalCompra = precio * cantidad;
    var idCompra = accounting.unformat($("#idCompra").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!cantidad) {
        flag = false;
        errorMsg += 'Ingrese cantidad de compra\n';
    }
    if (!precio) {
        flag = false;
        errorMsg += 'Ingrese precio unitario o total\n';
    }
    if (flag === false) {
        alert(errorMsg);
        $("#precioProducto").focus();
        return false;
    } else {
        params = {
            service: 'addItemCompra',
            idProductos: idProducto,
            descuento: descuento,
            precioCompra: precio,
            cantidad: cantidad,
            total: totalCompra,
            idCompra: idCompra,
            action: action,
            fechaVencimiento:$("#fechaVencimiento").val(),
            noLote:$("#noLote").val()
        };
        //console.log(params);
        //return false;
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        getCompraDetalle(params.idCompra);
                        if ($("#utilizaSerie").val() === 'Si') {
                            seriesProductos(idProducto, descProducto);
                        }
                        break;
                    case 'failed':
                        alert('Error al agregar producto en compra, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json').done(function () {
            $("#codigo").val('');
            $("#idProducto").val('');
            $("#tipoProducto").val('');
            $("#utilizaSerie").val('');
            $("#descProducto").val('');
            $("#descuentoProducto").val('');
            $("#cantidadProducto").val('');
            $("#precioProducto").val('');
            $("#totalProducto").val('');
            $("#codigo").focus();
        });
    }
}
//
function getCompraDetalle(idCompra) {
    console.log('estoy aqui');
    params = {
        service: 'getCompraDetalle',
        idCompra: idCompra
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        if (data === null) {
            $("#numeroProductos").val(0);
        } else {
            $("#numeroProductos").val(data.length);
        }
        var datos = "";
        var summary = "";
        var total2 = 0;
        //console.log(data.length);
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='9' class='warning text-center text-warning text-uppercase'><b>0 productos encontrados</b></td>";
            datos += "</tr>";
            $("#totalItems").val(total2);
            calculoIvaCompras('1');
        } else {
            $.each(data, function (key, val) {
                var descProducto = '"' + val.descLarga + '"';
                total2 += parseFloat(val.total);
                datos += "<tr>";
                datos += "<td class='action'>";
                datos += "<button class='btn btn-xs btn-danger' onclick='removeItemCompra(" + val.id + "," + val.idProductos + ")'><i class='fa fa-trash'></i></button>";
                datos += "</td>";
                datos += "<td>" + val.sku + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.descuento, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.precioCompra, 2) + "</td>";
                datos += "<td align='right'><a onclick='seriesProductos(" + val.idProductos + "," + descProducto + ");'>" + accounting.formatNumber(val.cantidad, 0) + "</a>";
                datos += "<br/><input type='hidden' id='cantidad-" + val.idProductos + "' value='" + accounting.formatNumber(val.cantidad, 0) + "'/>";
                datos += "<br/><input type='hidden' id='series-" + val.idProductos + "' value='" + accounting.formatNumber(val.series, 0) + "'/>";
                datos += "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.total, 2) + "</td>";
                datos += "<td>" + val.fechaVencimiento + "</td>";
                datos += "<td>" + val.noLote + "</td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='6'>Totales</td>";
            summary += "<td align='right'>" + accounting.formatNumber(total2, 2) + "</td>";
            summary += "<td>&nbsp;</td>";
            summary += "<td>&nbsp;</td>";
            summary += "</tr>";
            //
            $("#totalItems").val(accounting.formatNumber(total2, 2));
        }
        $("#detalleCompra").html(datos);
        $("#summaryCompra").html(summary);
    }, 'json');
}
//
function guardarCompra() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    //BLOQUE 1
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var idFormato = $("#idFormato").val();
    //BLOQUE 2
    var idProveedores = $("#idProveedores").val();
    var pequenoContribuyente = $("#pequenoContribuyente").val();
    var idTipoOperacion = $("#idTipoOperacion").val();
    var idTipoCompra = $("#idTipoCompra").val();
    var conceptoCompra = $("#conceptoCompra").val();
    //BLOQUE 3
    var ingresoA = $("#ingresoA").val();
    var idPuntoIngreso = $("#idPuntoIngreso").val();
    var idSucursales = $("#idSucursales").val();
    var serieFactura = $("#serieFactura").val();
    var noFactura = $("#noFactura").val();
    var fechaContabilizacion = $("#fechaContabilizacion").val();
    var fechaFactura = $("#fechaFactura").val();
    var fechaPago = $("#fechaPago").val();
    var valorFactura = accounting.unformat($("#valorFactura").val());
    var subTotal = accounting.unformat($("#subTotal").val());
    var exento = accounting.unformat($("#exento").val());
    var inguat = accounting.unformat($("#inguat").val());
    var descuentoM = accounting.unformat($("#descuentoM").val());
    var descuentoP = accounting.unformat($("#descuentoP").val());
    var total = accounting.unformat($("#total").val());
    var iva = accounting.unformat($("#iva").val());
    var totalItems = accounting.unformat($("#totalItems").val());
    var generaIva = accounting.unformat($("#generaIva").val());
    //BLOQUE 4
    var numeroProductos = $("#numeroProductos").val();
    //VALIDACIONES
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!idProveedores) {
        flag = false;
        errorMsg += 'Seleccione proveedor\n';
    }
    if (!idTipoOperacion) {
        flag = false;
        errorMsg += 'Seleccione tipo de operación\n';
    }
    if (!idTipoCompra) {
        flag = false;
        errorMsg += 'Seleccione tipo de compra\n';
    }
    if (!conceptoCompra) {
        flag = false;
        errorMsg += 'Ingrese concepto de compra\n';
    }
    if (generaIva !== 2 && !serieFactura) {
        flag = false;
        errorMsg += 'Ingrese Serie de Factura\n';
    }
    if (generaIva !== 2 && !noFactura) {
        flag = false;
        errorMsg += 'Ingrese Numero de Factura\n';
    }
    if (!fechaContabilizacion) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Contabilización\n';
    }
    if (!fechaFactura) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Factura\n';
    }
    if (!fechaPago) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Pago de Factura\n';
    }
    if (!valorFactura) {
        flag = false;
        errorMsg += 'Ingrese Valor de Factura\n';
    }
    if (!subTotal) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar subtotal\n';
    }
    if (generaIva !== 2 && !iva && pequenoContribuyente !== '1') {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar iva\n';
    }
    if (!total) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar total\n';
    }
    if (total !== totalItems && idTipoOperacion === '5') {
        flag = false;
        errorMsg += 'Total de factura no concide con el total de items ingresados\n';
    }
    if (numeroProductos === '0' && idTipoOperacion === '5') {
        flag = false;
        errorMsg += 'No tiene productos agregados a la compra\n';
    }
    if (!ingresoA && !idPuntoIngreso && idTipoOperacion === '5') {
        flag = false;
        errorMsg += 'Seleccione "Ingreso A" y "Lugar de Ingreso" para ingreso de items en compra\n';
    }
    if (generaIva !== 2 && !idSucursales) {
        flag = false;
        errorMsg += 'Seleccione un establecimiento\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'guardarCompra',
            idTipoDocumento: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            idFormato: idFormato,
            idProveedores: idProveedores,
            pequenoContribuyente: pequenoContribuyente,
            idTipoOperacion: idTipoOperacion,
            idTipoCompra: idTipoCompra,
            conceptoCompra: conceptoCompra,
            serieFactura: serieFactura,
            noFactura: noFactura,
            fechaContabilizacion: fechaContabilizacion,
            fechaFactura: fechaFactura,
            fechaPago: fechaPago,
            valorFactura: valorFactura,
            subtotal: subTotal,
            exento: exento,
            inguat: inguat,
            descuentoM: descuentoM,
            descuentoP: descuentoP,
            total: total,
            iva: iva,
            idSucursales: idSucursales,
            ingresoA: ingresoA,
            idPuntoIngreso: idPuntoIngreso,
            idCentrosCosto: $("#idCentrosCosto").val(),
            generaIva: generaIva,
            idOrdenCompra: idOrdenesCompra,
            idCajaChica: $("#idCajaChica").val()
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        alert('Compra ingresada exitosamente');
                        loadData('vw_compras', 'Inventarios', 'Listado de Compras', 0, 0, 0);
                        $("#loader").hide();
                        break;
                    case 'docExists':
                        alert('Serie y correlativo de factura ya fue ingresado al sistema');
                        $("#loader").hide();
                        break;
                    default :
                        alert('Error al ingresar compra, comuniquese con el administrador del sistema');
                        $("#loader").hide();
                        break;
                }
            });
        }, 'json');
    }
}
//
function generarDescuentoMoneda() {
    var subTotal = accounting.unformat($("#subTotal").val());
    var descuentoP = $("#descuentoP").val();
    var moneda = parseFloat(subTotal) * parseFloat(descuentoP) / 100;
    var total = parseFloat(subTotal) - parseFloat(moneda);
    var iva = Math.round(total / 1.12 * 0.12 * 100) / 100;
    var subTotalSinIva = subTotal - iva;
    //
    $("#subTotalSinIva").val(accounting.formatMoney(subTotalSinIva, ''));
    $("#descuentoM").val(accounting.formatMoney(moneda, ''));
    $("#total").val(accounting.formatMoney(total, ''));
    $("#iva").val(accounting.formatMoney(iva, ''));
}
//
function cargaInventario() {
    $.post('views/inventarios/cargaInventario.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Inventarios');
        $("#opcion").html('Carga de Inventarios');
    });
    loadBodegasEmpresa();
}
//
function downloadPlantilla(nombrePlantilla) {
    window.location.href = 'http://45.56.117.112/kairosV2/views/plantillas/' + nombrePlantilla;
}
//
function cargaPlanilla(ingresoA) {
    if (!$('#my_file').val()) {
        alert('Suba la plantilla con los productos a cargar');
    } else {
        var ftype = $('#my_file')[0].files[0].type;
        console.log(ftype);
        //return false;
        if (ftype !== 'application/download') {
            alert('Archivo invalido, solo se permiten archivos CSV');
            return false;
        }
        var idPuntoIngreso = "";
        if (ingresoA === 1) {
            idPuntoIngreso = $("#idBodegas").val();
        } else {
            idPuntoIngreso = $("#idSucursales").val();
        }
        var file_data = $('#my_file').prop('files')[0];
        var form_data = new FormData();
        form_data.append('service', 'cargarInventario');
        form_data.append('file', file_data);
        form_data.append('idPuntoIngreso', idPuntoIngreso);
        form_data.append('ingresoA', ingresoA);
        form_data.append('tipoDocumento', $("#tipoDocumento option:selected").text());
        form_data.append('correlativo', $("#correlativo").val());
        $.ajax({
            url: 'views/dynamic/uploadCSV.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                $.each(data, function (key, val) {
                    if (val.message === 'uploadSuccess') {
                        alert('Carga de Inventario Exitosa');
                        $("#btnUpload").removeClass('disabled');
                        if (ingresoA === 1) {
                            loadData('inventarioBodegas', 'Inventarios', 'Inventario de Bodegas', 0, 0, 0);
                        } else {
                            loadData('inventarioSucursales', 'Inventarios', 'Inventario Sucursales', 0, 0, 0);
                        }
                    } else {
                        alert('Error al cargar inventario');
                    }
                });
            }
        });
    }
}
//
function cargaPlanillaTraslados() {
    if (!$('#my_file').val()) {
        alert('Suba la plantilla con los productos a cargar');
    } else {
        var ftype = $('#my_file')[0].files[0].type;
        console.log(ftype);
        //return false;
        if (ftype !== 'application/download') {
            alert('Archivo invalido, solo se permiten archivos CSV');
            return false;
        }
        var file_data = $('#my_file').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('idBodegas', $("#idBodegas").val());
        form_data.append('idSucursales', $("#idSucursales").val());
        form_data.append('service', 'traslados');
        $.ajax({
            url: 'views/dynamic/uploadCSV.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                $.each(data, function (key, val) {
                    if (val.message === 'uploadSuccess') {
                        alert('Carga de Inventario Exitosa');
                        $("#btnUpload").removeClass('disabled');
                    } else {
                        alert('Error al cargar inventario');
                    }
                });
            }
        });
    }
}
//
function ingresoA2(idPuntoIngreso) {
    var tipo = $("#ingresoA").val();
    var container = "idPuntoIngreso";
    switch (tipo) {
        case '1':
            loadBodegasEmpresa(container, idPuntoIngreso);
            break;
        case '2':
            loadSucursalesEmpresa(container, idPuntoIngreso);
            break;
        default :
            $("#" + container).html('');
            alert('Debe seleccionar el tipo de ingreso');
            break;
    }
}
//
function removeItemCompra(item, idProductos) {
    params = {
        service: 'removeItemCompra',
        item: item,
        idCompra: accounting.unformat($("#idCompra").val()),
        documento: "COM " + $("#serieFactura").val() + "-" + $("#noFactura").val(),
        idProductos: idProductos
    };
    //console.log(params);
    //return false;
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
        });
    }, 'json').done(function () {
        getCompraDetalle(params.idCompra);
    });
}
//
function loadTraslados() {
    $("#page-container").load("views/inventarios/traslados.php", function () {
        $("#modulo").html('Inventarios');
        $("#opcion").html('Crear Traslado');
        loadBodegasEmpresa('idBodegas');
        loadDocumentos('traslados');
        getTrasladoDetalleUsuario();
        $("#bodegas").hide();
        $("#sucursales").hide();
        $("#fechaOperacion").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        }).attr('readonly', true).val(today);
    });
}
//
function ingresoA() {
    if ($("#ingresoA").val() === '1') {
        $("#bodegas").show();
        $("#sucursales").hide();
        loadBodegasEmpresa('idBodegasIngreso');
    } else {
        $("#bodegas").hide();
        $("#sucursales").show();
        loadSucursalesEmpresa('idSucursalesIngreso');
    }
}
//
function addItemTraslado() {
    var descProducto = $("#descProducto").val();
    var idProducto = $("#idProducto").val();
    var codigo = $("#codigo").val();
    var existencia = accounting.unformat($("#existencia").val());
    var cantidad = accounting.unformat($("#cantidad").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!cantidad) {
        flag = false;
        errorMsg += 'Ingrese la cantidad a trasladar\n';
    }
    if (cantidad > parseFloat(existencia)) {
        flag = false;
        errorMsg += 'Cantidad a trasladar es mayor a existencia\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'addItemTraslado',
            codigo: codigo,
            cantidad: cantidad
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                //console.log(val.message);
                if (val.message === 'success') {
                    if ($("#utilizaSerie").val() === 'Si') {
                        seriesProductos(idProducto, descProducto, 'traslados');
                    }
                    getTrasladoDetalleUsuario();
                    clear();
                } else {
                    alert('Error al agregar producto');
                }
            });
        }, 'json');
    }
}
//
function getTrasladoDetalleUsuario() {
    $("#detalle").html('');
    params = {
        service: 'getTrasladoDetalleUsuario'
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        if (data === null) {
            $("#numeroProductos").val(0);
        } else {
            $("#numeroProductos").val(data.length);
        }
        var datos = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='5' align='center'>0 productos encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                var descProducto = $("#descProducto").val();
                var action = '"traslados"';
                datos += "<tr>";
                datos += "<td><button class='btn btn-xs btn-danger' onclick='removeItemTraslado(" + val.id + ")'><i class='fa fa-trash'></i></button></td>";
                datos += "<td>" + val.sku + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += "<td align='right' colspan='2'><a onclick='seriesProductos(" + val.idProductos + "," + descProducto + "," + action + ");'>" + accounting.formatNumber(val.cantidad, 0) + "</a>";
                datos += "<br/><input type='hidden' id='cantidad-" + val.idProductos + "' value='" + accounting.formatNumber(val.cantidad, 0) + "'/>";
                datos += "<br/><input type='hidden' id='series-" + val.idProductos + "' value='" + accounting.formatNumber(val.series, 0) + "'/>";
                datos += "</td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    }, 'json');
}
//
function removeItemTraslado(item) {
    params = {
        service: 'removeItemTraslado',
        item: item
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
            console.log(val.message);
        });
    }, 'json').done(function () {
        getTrasladoDetalleUsuario();
    });
}
//
function finalizarTrasladoBodega() {
    var fechaOperacion = $("#fechaOperacion").val();
    var salidaDe = $("#salidaDe").val();
    var idPuntoSalida = $("#idPuntoSalida").val();
    var ingresoA = $("#ingresoA").val();
    var idBodegasIngreso = $("#idBodegasIngreso").val();
    var idSucursalesIngreso = $("#idSucursalesIngreso").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var correlativo = $("#correlativo").val();
    var numeroProductos = $("#numeroProductos").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var idPuntoIngreso = "";
    var observaciones = $("#observaciones").val();
    if ($("#ingresoA").val() === '1') {
        idPuntoIngreso = idBodegasIngreso;
    } else {
        idPuntoIngreso = idSucursalesIngreso;
    }
    if (!salidaDe) {
        flag = false;
        errorMsg += 'Seleccione opcion de salida de traslado\n';
    }
    if (!idPuntoSalida) {
        flag = false;
        errorMsg += 'Seleccione bodega o sucursal de salida de traslado\n';
    }
    if (!ingresoA) {
        flag = false;
        errorMsg += 'Seleccione opcion de ingreso de traslado\n';
    }
    if (!idPuntoIngreso) {
        flag = false;
        errorMsg += 'Seleccione bodega o sucursal de ingreso de traslado\n';
    }
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (numeroProductos === '0') {
        flag = false;
        errorMsg += 'No tiene productos agregados al traslado\n';
    }
    if (!observaciones) {
        flag = false;
        errorMsg += 'Ingrese observaciones del traslado\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'finalizarTraslado',
            salidaDe: salidaDe,
            idPuntoSalida: idPuntoSalida,
            ingresoA: ingresoA,
            idPuntoIngreso: idPuntoIngreso,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            fechaOperacion: fechaOperacion,
            observaciones: observaciones
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = pathJasper + "traslados.php?idTraslados=" + val.idTraslados;
                    window.open(url);
                    loadData('vw_traslados', 'Inventarios', 'Listado de Traslados');
                } else {
                    console.log(val.error);
                    alert('Error en procesar traslado, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function detalleTraslado() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        params = {
            idTraslado: id
        };
        $.post('views/inventarios/ingresoTraslado.php', params, function (respuesta) {
            $('#controllers').html(respuesta);
        }).done(function () {
            var estado = $("#estado").val();
            if (estado === 'Confirmado') {
                alert('Traslado ya fue ingresado');
            } else {
                $("#modal1").modal('show');
                $('.modal-title').html('Ingreso de Traslado');
                $("#idTraslado").val(id);
            }
        });

    }
}
//
function ingresarTraslado() {
    var formData = [];
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    $(".cantidadContada").each(function () {
        var arr = {};
        if (!$(this).val() || $(this).val() === '0') {
            errorMsg += 'Ingrese la cantidad contada del producto\n';
            flag = false;
        } else if ($(this).val() > parseFloat($(this).attr("title"))) {
            errorMsg += 'Cantidad contada es mayor a cantidad enviada\n';
            flag = false;
        } else {
            var dif = parseFloat($(this).attr("title")) - $(this).val();
            arr['idProducto'] = $(this).attr("name");
            arr['cantidadE'] = $(this).attr("title");
            arr['cantidadC'] = $(this).val();
            arr['diferencia'] = dif;
            formData.push(arr);
        }
    });
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'ingresarTraslado',
            productos: formData,
            observaciones: $("#observaciones").val(),
            idTraslado: $("#idTraslado").val()
        };
        //console.log(params);
        //return false;
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                console.log(val.message);
            });
        }, 'json').done(function () {
            cancelarModal();
            loadData('vw_traslados', 'Inventarios', 'Listado de Traslados', 0, 0, 0);
        });
    }
}
//
function ingresarAjuste() {
    $("#page-container").load("views/inventarios/ajustes.php", function () {
        $("#modulo").html('Inventarios');
        $("#opcion").html('Ingresar Ajuste');
        $("#fechaOperacion").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        }).attr('readonly', true).val(today);
        getAjusteDetalle();
    });
}
//
function loadDocAjustes() {
    var tipo = $("#operacion").val();
    $("#tipoDocumento").html('');
    switch (tipo) {
        case "1":
            loadDocumentos('entradas');
            break;
        case "2":
            loadDocumentos('salidas');
            break;
    }
}
//
function procesarAjuste() {
    var fechaOperacion = $("#fechaOperacion").val();
    var ingresoA = $("#ingresoA").val();
    var idPuntoIngreso = $("#idPuntoIngreso").val();
    var operacion = $("#operacion").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var correlativo = $("#correlativo").val();
    var numeroProductos = $("#numeroProductos").val();
    var descripcion = $("#descripcionAjuste").val();
    var serieFactura = $("#serieFactura").val();
    var noFactura = $("#noFactura").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!ingresoA) {
        flag = false;
        errorMsg += 'Seleccione la opcion de ingreso\n';
    }
    if (!idPuntoIngreso) {
        flag = false;
        errorMsg += 'Seleccione lugar donde ingreso\n';
    }
    if (!operacion) {
        flag = false;
        errorMsg += 'Seleccione tipo de ajuste\n';
    }
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!descripcion) {
        flag = false;
        errorMsg += 'Ingrese motivo de ajuste\n';
    }
    if (numeroProductos === '0') {
        flag = false;
        errorMsg += 'No tiene productos agregados\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'procesarAjuste',
            ingresoA: ingresoA,
            idPuntoIngreso: idPuntoIngreso,
            operacion: operacion,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            descripcion: descripcion,
            fechaOperacion: fechaOperacion,
            serieFactura: serieFactura,
            noFactura: noFactura
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = pathJasper + "ajustes.php?idAjustes=" + val.idAjustes;
                    window.open(url);
                    loadData('vw_ajustes', 'Inventarios', 'Listado de Ajustes');
                } else {
                    alert('Error en procesar ajuste, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//RECEPCION DE COMPRAS
function verCompra() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        params = {
            idCompra: id
        };
        $.post('views/inventarios/recepcionComprasView.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $("#modulo").html('Inventarios');
            $("#opcion").html('Ver Compra Ingresada');
            $(".form-control").attr({
                disabled: true
            });
        }).done(function () {
            getCompraDetalle(id);
        });
    }
}
//
function habilitarProductoVenta() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {

    }
}
//
function generarDescuento() {
    var descuentoP = $("#descuentoP").val();
    params = {
        service: 'generarDescuentoDetalleCompra',
        descuento: descuentoP
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
            console.log(val.message);
        });
    }, 'json');
    getCompraDetalle();
}

//REQUISICION DE COMPRAS
function nuevaRequisicion() {
    $.post('views/inventarios/requisicionCompras.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Inventarios');
        $("#opcion").html('Requisición de Compra');
        $('#fechaFactura,#fechaPago').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadDocumentos('requisicionCompras');
        loadHrmDepartamentos();
        getRequisicionDetalle();
    });
}
//
function getRequisicionDetalle(idRequisicion) {
    $("#detalle").html('');
    params = {
        service: 'getRequisicionDetalle',
        idRequisicion: idRequisicion
    };
    $.getJSON('controllers/inventariosController.php', params, function (data) {
        if (data === null) {
            $("#numeroProductos").val(0);
        } else {
            $("#numeroProductos").val(data.length);
        }
        var datos = "";
        var total2 = 0;
        //console.log(data.length);
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='6' align='center'>0 productos encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                total2 += parseFloat(val.total);
                datos += "<tr>";
                datos += "<td class='action' colspan='2'>";
                datos += "<button class='btn btn-xs btn-danger' onclick='removeItemRequision(" + val.id + ")'><i class='fa fa-trash'></i></button>";
                datos += "<button class='btn btn-xs btn-warning' onclick='updateItemRequision(" + val.id + ")'><i class='fa fa-refresh'></i></button>";
                datos += "</td>";
                datos += "<td><input type='text' value='" + val.codigo + "' class='form-control input-sm' id='codigo-" + val.id + "'/></td>";
                datos += "<td><input type='text' value='" + val.descProducto + "' class='form-control input-sm' id='descProducto-" + val.id + "'/></td>";
                datos += "<td><input type='text' value='" + val.unidadMedida + "' class='form-control input-sm' id='unidadMedida-" + val.id + "'/></td>";
                datos += "<td><input type='text' value='" + val.cantidad + "' class='form-control input-sm' id='cantidad-" + val.id + "'/></td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    });
}
//
function addItemRequisionCompra() {
    var idProducto = $("#idProducto").val();
    var codigo = $("#codigo").val();
    var descProducto = $("#descProducto").val();
    var unidadMedida = $("#unidadMedida").val();
    var cantidad = $("#cantidad").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!descProducto) {
        flag = false;
        errorMsg += 'Ingrese descripcion del item\n';
    }
    if (!cantidad) {
        flag = false;
        errorMsg += 'Ingrese cantidad solicitada\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'addItemRequisionCompra',
            idProductos: idProducto,
            codigo: codigo,
            descProducto: descProducto,
            unidadMedida: unidadMedida,
            cantidad: cantidad,
            idRequisicion: $("#idRequisicion").val()
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                //console.log(val.message);
                if (val.message === 'success') {
                    $("#idProducto").val('');
                    $("#codigo").val('');
                    $("#descProducto").val('');
                    $("#unidadMedida").val('');
                    $("#cantidad").val('');
                    getRequisicionDetalle($("#idRequisicion").val());
                } else {
                    alert('Error al agregar producto ' + val.result);
                }
            });
        }, 'json');
    }
}
//
function removeItemRequision(item) {
    var r = confirm("¿Esta seguro de eliminar este item?");
    if (r == true) {
        params = {
            service: 'removeItemRequision',
            item: item,
            idRequisicion: $("#idRequisicion").val()
        };
        $.getJSON('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                console.log(val.message);
            });
        }).done(function () {
            getRequisicionDetalle($("#idRequisicion").val());
        });
    } else {
        return false;
    }
}
//
function guardarRequisicion() {
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var correlativo = $("#correlativo").val();
    var solicitadoPor = $("#solicitadoPor").val();
    var idHrmDepartamentos = $("#idHrmDepartamentos").val();
    var observaciones = $("#observaciones").val();
    var numeroProductos = $("#numeroProductos").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!solicitadoPor) {
        flag = false;
        errorMsg += 'Ingrese nombre del solicitante\n';
    }
    if (!idHrmDepartamentos) {
        flag = false;
        errorMsg += 'Seleccione departamento del solicitante\n';
    }
    if (!observaciones) {
        flag = false;
        errorMsg += 'Ingrese observaciones de la requisición\n';
    }
    if (numeroProductos === '0') {
        flag = false;
        errorMsg += 'No tiene productos agregados a la requisición\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var r = confirm("¿Esta seguro de guardar esta requisición?");
        if (r == true) {
            params = {
                service: 'guardarRequisicion',
                tipoDocumento: tipoDocumento,
                correlativo: correlativo,
                solicitadoPor: solicitadoPor,
                idHrmDepartamentos: idHrmDepartamentos,
                observaciones: observaciones
            };
            $.getJSON('controllers/inventariosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Requisición ingresada exitosamente');
                        var url = pathJasper + "requisicionCompra_pdf.php?idRequisicion=" + val.idRequisicion + "&print=1";
                        window.open(url);
                        loadData('vw_comprasRequisicion', 'Inventarios', 'Requisiciones de Compra', 0, 0, 0);
                    } else {
                        alert('Error al ingresar requisición');
                    }
                });
            });
        } else {
            return false;
        }
    }
}
//
function verRequisicion() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        params = {
            idRequisicion: id
        };
        $.post('views/inventarios/requisicionComprasView.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $("#modulo").html('Inventarios');
            $("#opcion").html('Ver Requisición');
            $(".form-control").attr({
                disabled: true
            });
        }).done(function () {
            getRequisicionDetalle(id);
        });
    }
}
//
function imprimirRequisicion() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        var url = pathJasper + "requisicionCompra_pdf.php?idRequisicion=" + id + "&print=1";
        window.open(url);
    }
}
//
function crearOCRequisicion() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        nuevaOrdenCompra();
    }
}
//
function nuevaOrdenCompra(idOrdenCompra) {
    params = {
        idOrdenCompra: idOrdenCompra
    };
    $.post('views/compras/ordenCompra.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Inventarios');
        $("#opcion").html('Orden de Compra');
        $('#fechaArribo').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).attr('readonly', true);
        if (idOrdenCompra === undefined) {
            loadDocumentos('ordenCompra');
            loadHrmDepartamentos();
            $("#add").show();
            $("#update").hide();
            $("#fechaArribo").val(today);
        } else {
            $("#solicitadoPor").attr('disabled', true);
            $('#tipoDocumento').append('<option>' + $("#tipoDocumento2").val() + '</option>').attr('disabled', true);
            $('#idHrmDepartamentos').append('<option>' + $("#idHrmDepartamentos2").val() + '</option>').attr('disabled', true);
            $("#buscadorProveedores span button, #buscadorProveedores input").attr('disabled', true);
            $("#add").hide();
            $("#update").show();
        }
        if ($("#statusOrdenCompra").val() === '') {
            $("button").attr('disabled', false);
            $("input").attr('disabled', false);
            $("select").attr('disabled', false);
            $("textarea").attr('disabled', false);
            $("#cancel").attr('disabled', false);
        } else if ($("#statusOrdenCompra").val() !== 'Pendiente') {
            $("button").attr('disabled', true);
            $("input").attr('disabled', true);
            $("select").attr('disabled', true);
            $("textarea").attr('disabled', true);
            $("#cancel").attr('disabled', false);
        }
    }).done(function () {
        getOrdenCompraDetalle(idOrdenCompra);
    });
}
//
function imprimirOrdenCompra() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        var url = pathJasper + "ordenCompra_pdf.php?idComprasOrden=" + id + "&print=1";
        window.open(url);
    }
}
//
function gestionarOrdenCompra() {
    var id;
    var action;
    $('.data').each(function () {
        if (this.checked) {
            $.jStorage.deleteKey("modulo");
            id = $(this).val();
            action = $(this).data("value");
            $.jStorage.set("idOrdenCompra", id);
            $.jStorage.set("modulo", 'ordenCompra');
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        if (action !== 1) {
            alert('Orden de Compra ya fue gestionada');
            return false;
        } else {
            $.jStorage.deleteKey("idUsuarioAdmin");
            $.post('views/admin/loginAdmin.php', function (respuesta) {
                $("#controllers").html(respuesta);
            }).done(function () {
                $("#modal1").modal('show');
                $("#myModalLabel").html('Login Supervisor');
            });
        }
    }
}
//
function gestionarRC() {
    console.log($.jStorage.get("idUsuarioAdmin"));
    console.log($.jStorage.get("idOrdenCompra"));
    $.post('views/inventarios/gestionarRequisicion.php', function (respuesta) {
        $("#controllers").html(respuesta);
    }).done(function () {
        $("#modal1").modal('show');
        $("#myModalLabel").html('Gestionar Requisición');
    });
}
//
function gestionarOC() {
    console.log($.jStorage.get("idUsuarioAdmin"));
    console.log($.jStorage.get("idOrdenCompra"));
    $.post('views/inventarios/gestionarOrdenCompra.php', function (respuesta) {
        $("#controllers").html(respuesta);
    }).done(function () {
        $("#modal1").modal('show');
        $("#myModalLabel").html('Gestionar Orden de Compra');
    });
}
function procesarRC() {
    var statusRequisicion = $("#statusRequisicion").val();
    var observaciones = $("#observaciones").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!statusRequisicion) {
        flag = false;
        errorMsg += 'Seleccione estado de la orden\n';
    }
    if (!observaciones) {
        flag = false;
        errorMsg += 'Ingrese observaciones para la gestión\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'gestionarRC',
            idRequisicion: $.jStorage.get("idRequisicion"),
            idAdminUser: $.jStorage.get("idUsuarioAdmin"),
            observaciones: observaciones,
            statusRequisicion: statusRequisicion
        };
        $.post('controllers/inventariosController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message === 'success') {
                    alert('Requisicion gestionada exitosamente');
                    $("#modal1").modal('hide');
                    loadData('vw_comprasRequisicion', 'Inventarios', 'Requisiciones de Compra', 0, 0, 0);
                } else {
                    alert('Error al gestionar requisicion');
                }
            });
        }, 'json');
    }
}
//
function procesarOC() {
    var statusOrden = $("#statusOrden").val();
    var observaciones = $("#observaciones").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!statusOrden) {
        flag = false;
        errorMsg += 'Seleccione estado de la orden\n';
    }
    if (!observaciones) {
        flag = false;
        errorMsg += 'Ingrese observaciones para la gestión\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'gestionarOC',
            idOrdenCompra: $.jStorage.get("idOrdenCompra"),
            idAdminUser: $.jStorage.get("idUsuarioAdmin"),
            observaciones: observaciones,
            statusOrden: statusOrden
        };
        $.post('controllers/inventariosController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message === 'success') {
                    alert('Orden de Compra gestionada exitosamente');
                    $("#modal1").modal('hide');
                    loadData('vw_comprasOrdenes', 'Inventarios', 'Ordenes de Compra', 0, 0, 0);
                } else {
                    alert('Error al gestionar Orden de Compra');
                }
            });
        }, 'json');
    }
}
//
function importarRequisicionAOrden() {
    var id;
    var action;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
            action = $(this).data("value");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        switch (action) {
            case 2:
                nuevaOrdenCompra(id);
                break;
            case 4:
                alert('Requisición ya fue importada a compras');
                return false;
                break;
            default :
                alert('Requisición debe de estar aprobada para su importación');
                return false;
                break;
        }
    }
}
//
function importarOrdenesACompra() {
    var id;
    var action;
    var type;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
            action = $(this).data("value");
            type = $(this).data("type");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        switch (action) {
            case 2:
                if (type === 'Exterior') {
                    alert('importar a importaciones');
                    loadRecepcionImportacion(null, id);
                } else {
                    loadRecepcionCompras(id);
                }
                break;
            case 4:
                alert('Orden de Compra ya fue importada a compras');
                return false;
                break;
            default :
                alert('Orden de Compra debe de estar aprobada para su importación');
                return false;
                break;
        }
    }
}
//
function importarDetalleRequisicionaOrden(idRequisicion) {
    params = {
        service: 'importarDetalleRequisicionaOrden',
        idRequisicion: idRequisicion
    };
    $.post('controllers/inventariosController.php', params, function (respuesta) {
        $.each(respuesta, function (key, val) {
            if (val.message === 'success') {
                alert('Detalle de requisición de compra importado exitosamente');
                getOrdenCompraDetalle();
            } else {
                alert('Error al importar detale requisición de compra');
            }
        });
    }, 'json');
}
//
function importarDetalleOCaCompras(idOrdenCompra) {
    params = {
        service: 'importarDetalleOCaCompras',
        idOrdenCompra: idOrdenCompra
    };
    $.post('controllers/inventariosController.php', params, function (respuesta) {
        $.each(respuesta, function (key, val) {
            if (val.message === 'success') {
                alert('Detalle de orden de compra importado exitosamente');
                getCompraDetalle();
            } else {
                alert('Error al importar detale orden de compra');
            }
        });
    }, 'json');
}
//
function loadProveedorByOC(idOrdenCompra) {
    params = {
        service: 'getProveedorByOC',
        idOrdenCompra: idOrdenCompra
    };
    $.getJSON('controllers/adminController.php', params, function (data) {
        $.each(data, function (key, val) {
            $("#idProveedores").val(val.id);
            $("#nit").val(val.nit);
            $("#nombre").val(val.descripcion);
            $("#direccion").val(val.direccion);
        });
    }).done(function () {
        $("#modal1").modal('hide');
    });
}
//
function imprimirCompra() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        var url = pathJasper + "compras_pdf.php?idCompra=" + id + "&print=1";
        window.open(url);
    }
}
//
function cancelarProcesoCompra(modulo) {
    var r = confirm("¿Esta seguro de cancelar este proceso?");
    if (r == true) {
        params = {
            service: 'cancelarProcesoCompra',
            modulo: modulo
        };
        $.post('controllers/inventariosController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message === 'success') {
                    switch (modulo) {
                        case 'requisicionCompra':
                            loadData('vw_comprasRequisicion', 'Compras', 'Requisiciones de Compra', 0, 0, 0);
                            break;
                        case 'ordenCompra':
                            loadData('vw_comprasOrdenes', 'Compras', 'Ordenes de Compra', 0, 0, 0);
                            break;
                        case 'compra':
                            loadData('vw_compras', 'Compras', 'Compras', 0, 0, 0);
                            break;
                        case 'importaciones':
                            loadData('vw_importaciones', 'Compras', 'Importaciones', 0, 0, 0);
                            break;
                    }
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function updateItemRequision(item) {
    var codigo = $("#codigo-" + item).val();
    var descProducto = $("#descProducto-" + item).val();
    var unidadMedida = $("#unidadMedida-" + item).val();
    var cantidad = $("#cantidad-" + item).val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!cantidad || cantidad === '0') {
        flag = false;
        errorMsg += 'El campo cantidad no puede estar vacio o tener valor 0\n';
    }
    if (!descProducto) {
        flag = false;
        errorMsg += 'Ingrese descripción del item\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'updateItemRequision',
            codigo: codigo,
            descProducto: descProducto,
            unidadMedida: unidadMedida,
            cantidad: cantidad,
            item: item
        };
        $.post('controllers/inventariosController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message === 'success') {
                    getRequisicionDetalle();
                } else {
                    alert('Error al actualizar item ' + val.result);
                }
            });
        }, 'json');
    }
}
//
function updateItemOrdenCompra(item, idOrdenCompra) {
    var cantidad = $("#cantidad-" + item).val();
    var precio = $("#precio-" + item).val();
    var total = parseFloat(precio.replace(',', '')) * parseFloat(cantidad);
    if (!cantidad || !precio) {
        alert('Debe ingresar cantidad y precio para agregar un producto al listado');
    } else {
        params = {
            service: 'updateItemOrdenCompra',
            item: item,
            cantidad: cantidad,
            precio: precio,
            total: total,
            idOrdenCompra: idOrdenCompra
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    getOrdenCompraDetalle(idOrdenCompra);
                } else {
                    alert('Error al actualizar item ', val.error);
                }
            });
        }, 'json');
    }
}
//
function gestionarRequisicion() {
    var id;
    var action;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
            action = $(this).data("value");
            $.jStorage.set("idRequisicion", id);
            $.jStorage.set("modulo", 'requisicionCompra');
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        if (action !== 1) {
            alert('Orden de Compra ya fue gestionada');
            return false;
        } else {
            $.jStorage.deleteKey("idUsuarioAdmin");
            $.post('views/admin/loginAdmin.php', function (respuesta) {
                $("#controllers").html(respuesta);
            }).done(function () {
                $("#modal1").modal('show');
                $("#myModalLabel").html('Login Supervisor');
            });
        }
    }
}
//
function getRequisicion(idRequisicion) {
    params = {
        service: 'getRequisicion',
        idRequisicion: idRequisicion
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $("#solicitadoPor").val(data['solicitadoPor']).attr('readonly', true);
        $("#idHrmDepartamentos option").each(function () {
            if ($(this).text() === data['idHrmDepartamentos']) {
                $(this).attr("selected", "selected");
            }
        });
        $("#idHrmDepartamentos").attr('disabled', true);
        $("#observaciones").val('Importacion Requisicion #. ' + data['documento']).attr('readonly', true);
    }, 'json');
}
//
function updateItemCompra(item) {
    var cantidad = $("#cantidad-" + item).val();
    var precio = $("#precio-" + item).val().replace(',', '');
    var totalCompra = (precio * cantidad).toFixed(2);
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!cantidad || cantidad === '0') {
        flag = false;
        errorMsg += 'El campo cantidad no puede estar vacio o tener valor 0\n';
    }
    if (!precio) {
        flag = false;
        errorMsg += 'Ingrese precio de compra\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'updateItemCompra',
            cantidad: cantidad,
            precioCompra: precio,
            total: totalCompra,
            item: item
        };
//        console.log(params);
//        return false;
        $.post('controllers/inventariosController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message === 'success') {
                    getCompraDetalle();
                } else {
                    alert('Error al actualizar item ' + val.result);
                }
            });
        }, 'json');
    }
}
//
function updateCompra() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        loadRecepcionCompras(id);
    }
}
//
function actualizarCompra() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    //BLOQUE 1
    var idProveedores = $("#idProveedores").val();
    var idTipoOperacion = $("#idTipoOperacion").val();
    var idTipoCompra = $("#idTipoCompra").val();
    var conceptoCompra = $("#conceptoCompra").val();
    //BLOQUE 2
    var idSucursales = $("#idSucursales").val();
    var serieFactura = $("#serieFactura").val();
    var noFactura = $("#noFactura").val();
    var fechaContabilizacion = $("#fechaContabilizacion").val();
    var fechaFactura = $("#fechaFactura").val();
    var fechaPago = $("#fechaPago").val();
    var valorFactura = $("#valorFactura").val();
    var subTotal = $("#subTotal").val();
    var exento = $("#exento").val();
    var inguat = $("#inguat").val();
    var descuentoM = $("#descuentoM").val();
    var descuentoP = $("#descuentoP").val();
    var total = $("#total").val();
    var iva = $("#iva").val();
    var idFormato = $("#idFormato").val();
    var idPartida = $("#idPartida").val();
    var idCompra = $("#idCompra").val();
    //BLOQUE 3
    var ingresoA = $("#ingresoA").val();
    var idPuntoIngreso = $("#idPuntoIngreso").val();
    var totalItems = accounting.unformat($("#totalItems").val());
    var numeroProductos = $("#numeroProductos").val();
    //VALIDACIONES
    if (!idTipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!idFormato) {
        flag = false;
        errorMsg += 'Seleccione partida contable\n';
    }
    if (!idProveedores) {
        flag = false;
        errorMsg += 'Seleccione proveedor\n';
    }
    if (!idTipoOperacion) {
        flag = false;
        errorMsg += 'Seleccione tipo de operación\n';
    }
    if (!idTipoCompra) {
        flag = false;
        errorMsg += 'Seleccione tipo de compra\n';
    }
    if (!conceptoCompra) {
        flag = false;
        errorMsg += 'Ingrese concepto de compra\n';
    }
    if (!serieFactura) {
        flag = false;
        errorMsg += 'Ingrese Serie de Factura\n';
    }
    if (!noFactura) {
        flag = false;
        errorMsg += 'Ingrese Numero de Factura\n';
    }
    if (!fechaContabilizacion) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Contabilización\n';
    }
    if (!fechaFactura) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Factura\n';
    }
    if (!fechaPago) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Pago de Factura\n';
    }
    if (!valorFactura) {
        flag = false;
        errorMsg += 'Ingrese valor de factura\n';
    }
    if (!subTotal) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar subtotal\n';
    }
    if (!iva) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar iva\n';
    }
    if (!total && idTipoOperacion === '6') {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar total\n';
    }
    if (numeroProductos === '0' && idTipoOperacion === '6') {
        flag = false;
        errorMsg += 'No tiene productos agregados a la compra\n';
    }
    if (!idBodegas && idTipoOperacion === '6') {
        flag = false;
        errorMsg += 'Seleccione una bodega de ingreso\n';
    }
    if (!idSucursales) {
        flag = false;
        errorMsg += 'Seleccione una sucursal\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'actualizarCompra',
            idTipoDocumento: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            idFormato: idFormato,
            idProveedores: idProveedores,
            idTipoOperacion: idTipoOperacion,
            idTipoCompra: idTipoCompra,
            conceptoCompra: conceptoCompra,
            serieFactura: serieFactura,
            noFactura: noFactura,
            fechaContabilizacion: fechaContabilizacion,
            fechaFactura: fechaFactura,
            fechaPago: fechaPago,
            valorFactura: accounting.unformat(valorFactura),
            subtotal: accounting.unformat(subTotal),
            exento: accounting.unformat(exento),
            inguat: accounting.unformat(inguat),
            descuentoM: accounting.unformat(descuentoM),
            descuentoP: descuentoP,
            total: accounting.unformat(total),
            iva: accounting.unformat(iva),
            idPartida: $("#idPartida").val(),
            idCompra: $("#idCompra").val(),
            idSucursales: idSucursales,
            idBodegas: idBodegas
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Compra actualizada exitosamente');
                    loadData('vw_compras', 'Inventarios', 'Listado de Compras', 0, 0, 0);
                } else {
                    alert('Error al actualizar compra');
                }
            });
        }, 'json');
    }
}
//
function eliminarCompra() {
    var r = confirm("¿Esta seguro de eliminar esta compra?");
    if (r == true) {
        $("#loader").show();
        params = {
            service: 'eliminarCompra',
            idPartida: $("#idPartida").val(),
            idCompra: $("#idCompra").val(),
            idTipoCompra: $("#idTipoCompra option:selected").val()
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Compra eliminada exitosamente');
                    loadData('vw_compras', 'Inventarios', 'Listado de Compras', 0, 0, 0);
                } else {
                    alert('Error al eliminar compra');
                    $("#loader").hide();
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function inventarioSeries() {
    var contadorProducto = accounting.unformat($("#cantidad-" + $("#idProducto").val()).val());
    if (contador1 > contadorProducto) {
        alert('Numero de items del producto completos');
    } else {
        params = {
            service: 'inventarioSeries',
            serie: $("#serie").val(),
            idProducto: $("#idProducto").val(),
            tipoProducto: $("#tipoProducto").val()
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'exists':
                        alert('Serie No. ' + $("#serie").val() + ' ya fue ingresada');
                        break;
                    case 'failed':
                        alert('Error al ingresar serie, consulte con su administrador');
                        break;
                    case 'success':
                        getInventarioSeries();
                        $("#serie").val('');
                        break;
                }
            });
        }, 'json');
    }
}
//
function getInventarioSeries() {
    $("#detalleSeries").html('');
    params = {
        service: 'getInventarioSeries'
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        var datos = "";
        //console.log(data.length);
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='4' class='warning text-center text-warning text-uppercase'><b>0 productos encontrados</b></td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                datos += "<tr>";
                datos += "<td class='action'>";
                datos += "<button class='btn btn-xs btn-danger' onclick='removeItemCompra(" + val.id + ")'><i class='fa fa-trash'></i></button>";
                datos += "</td>";
                datos += "<td>" + val.sku + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += "<td>" + val.serie + "</td>";
                datos += "</tr>";
                contador1++;
            });
        }
        $("#detalleSeries").append(datos);
    }, 'json').done(function () {
        console.log(contador1);
    });
}
//
function agregarProductoVenta(idProducto, tipoProducto, precioPublico, cantidad, existencia) {
    var descProducto = $("#descProducto").val();
    var idProducto = (idProducto !== undefined ? idProducto : $("#idProducto").val());
    var tipoProducto = (tipoProducto !== undefined ? tipoProducto : $("#tipoProducto").val());
    var precio = accounting.unformat((precioPublico !== undefined ? precioPublico : $("#precioProducto").val()));
    var cantidad = accounting.unformat((cantidad !== undefined ? cantidad : $("#cantidadProducto").val()));
    var existencia = accounting.unformat((existencia !== undefined ? existencia : $("#existencia").val()));
    var valExistencias = accounting.unformat($("#valExistencias").val());
    var total = precio * cantidad;
    var errorMsg = "";
    var errorMsgPC = "";
    var flag = true;
    if (!cantidad) {
        flag = false;
        errorMsg += 'Ingrese cantidad de venta del producto\n';
    }
    if (cantidad === 0) {
        flag = false;
        errorMsg += 'La cantidad ingresada no puede ser vacio o cero \n';
    }
    if (tipoProducto !== 'Servicio' && tipoProducto !== 'Producto Fabricado' && cantidad > existencia && valExistencias !== 0) {
        flag = false;
        errorMsg += 'La cantidad ingresada es mayor que la existencia del producto\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'agregarProductoVenta',
            tipoProducto: tipoProducto,
            idProducto: idProducto,
            cantidad: cantidad,
            precio: precio,
            total: total,
            idSucursales: $("#idSucursales").val(),
            codigo: $("#codigo").val(),
            valExistencias: valExistencias,
            descProducto: $("#descProducto").val()
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadProductosVenta();
                    if ($("#utilizaSerie").val() === 'Si') {
                        seriesProductos(idProducto, descProducto, 'facturacion');
                    }
                    // reset input
                    clear();
                    $("#codigo").focus();
                } else {
                    if (val.action == 'false') {
                        errorMsg = 'No hay existencias de componentes del producto seleccionado\n';
                        errorMsgPC += 'Producto: ' + val.producto + '\n';
                    }
                }
            });
        }, 'json').done(function () {
            if (errorMsg !== '') {
                alert(errorMsg + errorMsgPC);
            }
        });
    }
}
//
function loadProductosVenta(idVenta) {
    params = {
        service: 'getProductosVenta',
        idSucursales: $("#idSucursales").val(),
        idVenta: idVenta
    };
    var total = 0;
    var datos = "";
    $.post('controllers/cajaController.php', params, function (data) {
        $("#detalle").html('');
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='7' align='center'>0 productos encontrados</td>";
            datos += "</tr>";
            $("#numeroProductos").val(0);
        } else {
            $("#numeroProductos").val(data.length);
            $.each(data, function (key, val) {
                var descProducto = '"' + val.descLarga + '"';
                total += accounting.unformat(val.total);
                datos += "<tr>";
                datos += "<td>" + (key + 1) + "</td>";
                datos += "<td><button class='btn btn-xs btn-danger' onclick='eliminarProductoVenta(" + val.id + "," + val.idProductos + ")'><i class='fa fa-trash'></i></button></td>";
                datos += "<td>" + val.codigo + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += "<td align='right'><a onclick='seriesProductos(" + val.idProductos + "," + descProducto + ");'>" + accounting.formatNumber(val.cantidad, 0) + "</a>";
                datos += "<br/><input type='hidden' id='cantidad-" + val.idProductos + "' value='" + accounting.formatNumber(val.cantidad, 0) + "'/>";
                datos += "<br/><input type='hidden' id='series-" + val.idProductos + "' value='" + accounting.formatNumber(val.series, 0) + "'/>";
                datos += "</td>";
                datos += "<td align='right'>" + val.precio + "</td>";
                datos += "<td align='right'>" + val.total + "</td>";
                datos += "</tr>";
            });
        }
    }, 'json').done(function () {
        if (total > 0) {
            var tasaCambio = accounting.unformat($("#tipoCambio").val());
            var valorFactura = accounting.unformat(total * tasaCambio);
            var iva = accounting.unformat((valorFactura / 1.12 * 0.12 * 100) / 100);
            var subTotal = accounting.unformat((valorFactura - iva));
            var totalDolares = accounting.unformat((valorFactura / tasaCambio));
            //
            $("#subTotal").val(subTotal.toFixed(2));
            $("#txtSubTotal").html(accounting.formatNumber(subTotal, 2));
            $("#iva").val(iva.toFixed(2));
            $("#txtIva").html(accounting.formatNumber(iva, 2));
            $("#total").val(valorFactura.toFixed(2));
            $("#valorFactura").val(valorFactura.toFixed(2));
            $("#txtTotal").html(accounting.formatNumber(valorFactura, 2));
            $("#totalDolares").val(accounting.formatNumber(total, 2));
            $("#detalle").append(datos);
        }
        /*
         var valorFactura = accounting.unformat($("#valorFactura").val());
         var iva = accounting.formatNumber((valorFactura / 1.12 * 0.12 * 100) / 100, 2);
         var subTotal = accounting.formatNumber(valorFactura - accounting.unformat(iva), 2);
         $("#subTotal").val(subTotal);
         $("#iva").val(iva);
         $("#total").val(accounting.formatNumber(valorFactura, 2));
         */
    });
}
//
function addItemAjuste() {
    var descProducto = $("#descProducto").val();
    var idProducto = $("#idProducto").val();
    var cantidad = parseFloat($("#cantidad").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!cantidad) {
        flag = false;
        errorMsg += 'Ingrese cantidad de compra\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'addItemAjuste',
            idProductos: idProducto,
            cantidad: cantidad
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                //console.log(val.message);
                if (val.message === 'success') {
                    if ($("#utilizaSerie").val() === 'Si') {
                        seriesProductos(idProducto, descProducto, $("#operacion option:selected").text());
                    }
                    $("#codigo").val('');
                    $("#idProducto").val('');
                    $("#descProducto").val('');
                    $("#unidadMedida").val('');
                    $("#cantidad").val('');
                    $("#precioCompra").val('');
                    $("#utilizaSerie").val('');
                    $("#total").val('');
                    getAjusteDetalle();
                    //busqueda('vw_productos', 'Listado de Productos', 'ajustes');
                    $("#codigo").focus();
                } else if (val.message === 'exists') {
                    alert('Producto ya fue ingresado al listado de compra');
                } else {
                    alert('Error al agregar producto');
                }
            });
        }, 'json');
    }
}
//
function getAjusteDetalle(idAjuste) {
    $("#detalle").html('');
    params = {
        service: 'getAjusteDetalle',
        idAjuste: idAjuste
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        if (data === null) {
            $("#numeroProductos").val(0);
        } else {
            $("#numeroProductos").val(data.length);
        }
        var datos = "";
        if (data === null) {
            datos += "<tr>";
            if (dbProject == 'pos_kasualcosmeticos') {
                datos += "<td colspan='7' class='warning text-center text-warning text-uppercase'><b>0 productos encontrados</b></td>";
            } else {
                datos += "<td colspan='5' class='warning text-center text-warning text-uppercase'><b>0 productos encontrados</b></td>";
            }
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                var descProducto = '"' + val.descLarga + '"';
                var action = '"' + $("#operacion option:selected").text() + '"';
                datos += "<tr>";
                datos += "<td class='action'>";
                datos += "<button class='btn btn-xs btn-danger' onclick='removeItemAjuste(" + val.id + "," + val.idProductos + ")'><i class='fa fa-trash'></i></button>";
                datos += "</td>";
                datos += "<td>" + val.sku + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                if (dbProject == 'pos_kasualcosmeticos') {
                    datos += "<td>" + val.item + "</td>";
                    datos += "<td>" + val.idMarcas + "</td>";
                }
                datos += "<td>" + val.tipoProducto + "</td>";
                datos += "<td align='right'><a onclick='seriesProductos(" + val.idProductos + "," + descProducto + "," + action + ");'>" + accounting.formatNumber(val.cantidad, 0) + "</a>";
                datos += "<br/><input type='hidden' id='cantidad-" + val.idProductos + "' value='" + accounting.formatNumber(val.cantidad, 0) + "'/>";
                datos += "<br/><input type='hidden' id='series-" + val.idProductos + "' value='" + accounting.formatNumber(val.series, 0) + "'/>";
                datos += "</td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    }, 'json');
}
//
function removeItemAjuste(item, idProductos) {
    params = {
        service: 'removeItemAjuste',
        item: item,
        idProductos: idProductos
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                getAjusteDetalle();
            } else {
                alert('Error al eliminar item en ajuste, consulte con su administrador');
            }
        });
    }, 'json');
}
//
function calculoIvaCompras(type) {
    switch (type) {
        case '1':
            console.log(type);
            var descuento = accounting.unformat($("#descuentoM").val());
            var exento = accounting.unformat($("#exento").val());
            var inguat = accounting.unformat($("#inguat").val());
            var valorFactura = accounting.unformat($("#valorFactura").val()) - (descuento + exento + inguat);
            var descuentoP = (descuento / accounting.unformat($("#valorFactura").val()) * 100);
            var generaIva = accounting.unformat($("#generaIva").val());
            var iva = 0;
            var ivaEmpresa = accounting.unformat($("#ivaEmpresa").val());
            if (generaIva !== 2 && $("#pequenoContribuyente").val() !== '1') {
                iva = accounting.formatNumber((valorFactura / ((ivaEmpresa / 100) + 1) * (ivaEmpresa / 100) * 100) / 100, 2);
            }
            var subTotal = accounting.formatNumber(valorFactura - accounting.unformat(iva), 2);
            $("#subTotal").val(subTotal);
            $("#descuentoP").val(accounting.formatNumber(descuentoP));
            $("#iva").val(iva);
            $("#total").val(accounting.formatNumber($("#valorFactura").val(), 2));
            break;
        case '2':
            console.log(type);
            var descuentoM = accounting.unformat($("#valorFactura").val()) * accounting.unformat($("#descuentoP").val()) / 100;
            var exento = accounting.unformat($("#exento").val());
            var inguat = accounting.unformat($("#inguat").val());
            var valorFactura = accounting.unformat($("#valorFactura").val() - (descuentoM + exento + inguat));
            var generaIva = accounting.unformat($("#generaIva").val());
            var iva = 0;
            var ivaEmpresa = accounting.unformat($("#ivaEmpresa").val());
            if (generaIva !== 2 && $("#pequenoContribuyente").val() !== '1') {
                iva = accounting.formatNumber((valorFactura / ((ivaEmpresa / 100) + 1) * (ivaEmpresa / 100) * 100) / 100, 2);
            }
            var subTotal = accounting.formatNumber(valorFactura - accounting.unformat(iva), 2);
            $("#subTotal").val(subTotal);
            $("#descuentoM").val(accounting.formatNumber(descuentoM, 2));
            $("#iva").val(iva);
            $("#total").val(accounting.formatNumber(valorFactura, 2));
            break;
        case '3':
            //IMPORTACIONES
            var valorGastosImportacion = accounting.unformat($("#valorGastosImportacion").val());
            var descuento = accounting.unformat($("#descuentoM").val());
            var valorFacturaImportacion = (accounting.unformat($("#valorFacturaImportacion").val()) - descuento);
            var valorTotalImportacion = ((accounting.unformat($("#valorFacturaImportacion").val())) - accounting.unformat($("#descuentoM").val()));
            var valorFacturaTipoCambio = (accounting.unformat($("#tipoCambioDUA").val()) * valorTotalImportacion);
            var valorFactura = valorTotalImportacion;
            var iva = accounting.formatNumber(((valorFacturaTipoCambio) * 0.12 * 100) / 100, 2);
            var subTotal = accounting.formatNumber(valorFacturaTipoCambio, 2);
            var cantidadItems = accounting.unformat($("#cantidadItems").val());
            var totalPeso = accounting.unformat($("#totalPeso").val());
            $("#valorFacturaImportacion").val(accounting.formatNumber(valorFacturaImportacion, 2));
            $("#valorTotalImportacion").val(accounting.formatNumber(valorTotalImportacion, 2));
            $("#valorFacturaTipoCambio").val(accounting.formatNumber(valorFacturaTipoCambio, 2));
            $("#subTotal").val(subTotal);
            $("#iva").val(iva);
            $("#total").val(accounting.formatNumber(valorFacturaTipoCambio, 2));
            //FACTORES
            var FI = 0;
            var FV = 0;
            var FP = 0;
            if (cantidadItems !== 0) {
                FI = (valorGastosImportacion / cantidadItems);
            }
            if (valorFacturaImportacion !== 0) {
                FV = (valorGastosImportacion / valorFacturaTipoCambio);
            }
            if (totalPeso !== 0) {
                FP = (valorGastosImportacion / totalPeso);
            }
            $("#factorInventario").val(accounting.formatNumber(FI, 2));
            $("#factorValor").val(accounting.formatNumber(FV, 2));
            $("#factorPeso").val(accounting.formatNumber(FP, 2));
            break;
    }
}
//
function seriesProductos(idProductos, descProducto, action) {
    $("#modal1").modal('show');
    $('.modal-title').html('Ingreso de Series<br/>Producto: ' + descProducto);
    $.post('views/inventarios/seriesProductos.php', params, function (respuesta) {
        $('#controllers').html(respuesta);
        $("#idProductoSerie").val(idProductos);
        $("#action").val(action);
    }).done(function () {
        getSeriesProducto(idProductos);
    });
    $("#serie").focus();
}
//
function ingresoSeriesProducto() {
    var seriesAIngresar = accounting.unformat($("#cantidad-" + $("#idProductoSerie").val()).val());
    var seriesIngresadas = accounting.unformat($("#cantidadSeries").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var serie = $("#serie").val();
    var flag = true;
    if (!serie) {
        flag = false;
        errorMsg += 'Ingrese serie de producto\n';
    }
    if (seriesAIngresar <= seriesIngresadas) {
        flag = false;
        errorMsg += 'Cantidad de series ingresadas completas\n';
        $("#serie").val('');
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'ingresoSeriesProducto',
            idProducto: $("#idProductoSerie").val(),
            serie: serie,
            action: $("#action").val()
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        getSeriesProducto($("#idProductoSerie").val());
                        break;
                    case 'exists':
                        alert('Numero de serie ya existente en el sistema');
                        $("#serie").val('');
                        break;
                    case 'failed':
                        alert('Error al agregar serie, comuniquese con el administrador del sistema');
                        break;
                    case 'noExists':
                        alert('Numero de serie no existente en el sistema');
                        $("#serie").val('');
                        break;
                }
            });
        }, 'json');
    }
}
//
function getSeriesProducto(idProductos) {
    $("#detalleSeries").html('');
    params = {
        service: 'getSeriesProducto',
        idProductos: idProductos
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        if (data === null) {
            $("#cantidadSeries").val(0);
        } else {
            $("#cantidadSeries").val(data.length);
        }
        var datos = "";
        //console.log(data.length);
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='2' class='warning text-center text-warning text-uppercase'><b>0 series ingresadas</b></td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                datos += "<tr>";
                datos += "<td class='action'>";
                datos += "<button class='btn btn-xs btn-danger' onclick='removeSerieProducto(" + val.id + "," + idProductos + ")'><i class='fa fa-trash'></i></button>";
                datos += "</td>";
                datos += "<td>" + val.serie + "</td>";
                datos += "</tr>";
            });
        }
        $("#detalleSeries").append(datos);
    }, 'json').done(function () {
        $("#serie").val('').focus();
    });
}
//
function removeSerieProducto(item, idProductos) {
    params = {
        service: 'removeSerieProducto',
        item: item,
        idProductos: idProductos
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
        });
    }, 'json').done(function () {
        getSeriesProducto(idProductos);
    });
}
//
function reImprimirAjuste() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "ajustes.php?idAjustes=" + id + "";
        window.open(url);
    }
}
//
function reImprimirTraslados() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "traslados.php?idTraslados=" + id + "";
        window.open(url);
    }
}
//
function loadRecepcionImportacion(idImportacion) {
    params = {
        idImportacion: idImportacion
    };
    $.post('views/compras/recepcionImportaciones.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Inventarios');
        $("#opcion").html('Ingreso de Importaciones');
        $('#fechaContabilizacion,#fechaFactura,#fechaPago,#fechaFacturaPG').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#tipoCambioDUA").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                $("#valorFacturaImportacion").focus();
                updateImportacionesDetalle($(this).val(), params.idImportacion);
            }
        });
        $("#tipoCambio").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                $("#valorFacturaImportacion").focus();
                updateImportacionesDetalle($(this).val(), params.idImportacion);
            }
        });
        $("#valorFacturaImportacion,#valorGastosImportacion,#descuentoM,#cantidadItems").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                calculoIvaCompras('3');
            }
        });
        //CAMPOS DETALLE DE GASTOS
        $("#idTipoDocumentoGasto").on('change', function (e) {
            $("#nitPG").focus();
        });
        $("#nitPG").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                $("#proveedorPG").focus();
            }
        });
        $("#proveedorPG").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                $("#conceptoPG").focus();
            }
        });
        $("#conceptoPG").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                $("#fechaFacturaPG").focus();
            }
        });
        $("#fechaFacturaPG").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                $("#serieFacturaPG").focus();
            }
        });
        $("#serieFacturaPG").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                $("#numeroFacturaPG").focus();
            }
        });
        $("#numeroFacturaPG").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                $("#idMoneda").focus();
            }
        });
        $("#idMoneda").on('change', function (e) {
            $("#valorPG").focus();
        });
    }).done(function () {
        loadDocumentos('importaciones', idImportacion);
        getImportacionesDetalle(idImportacion);
        getImportacionesGastos(idImportacion);
        idOrdenesCompra = [];
    });
}
//
function addItemImportacion(ingresoA, idPuntoIngreso, idProducto, cantidad, peso, arancel, precio) {
    var ingresoA = (ingresoA !== undefined ? ingresoA : $("#ingresoA").val());
    var idPuntoIngreso = (idPuntoIngreso !== undefined ? idPuntoIngreso : $("#idPuntoIngreso").val());
    var idProducto = (idProducto !== undefined ? idProducto : $("#idProducto").val());
    var cantidad = (cantidad !== undefined ? cantidad : accounting.unformat($("#cantidad").val()));
    var peso = (peso !== undefined ? peso : accounting.unformat($("#peso").val()));
    var arancel = (arancel !== undefined ? arancel : accounting.unformat($("#arancel").val()));
    var precio = (precio !== undefined ? precio : accounting.unformat($("#precioProducto").val()));
    var precioImport = (precio !== undefined ? precio : accounting.unformat($("#precioProducto").val()));
    var total = (precio * cantidad);
    var totalImport = (precioImport * cantidad);
    var monedaCompra = $("#monedaCompra").val();
    var tipoCambio = accounting.unformat($("#tipoCambio").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!ingresoA) {
        flag = false;
        errorMsg += 'Seleccione opcion de ingreso\n';
    }
    if (!idPuntoIngreso) {
        flag = false;
        errorMsg += 'Seleccione bodega o sucursal de ingreso\n';
    }
    if (!monedaCompra) {
        flag = false;
        errorMsg += 'Seleccione moneda de compra\n';
    }
    if (monedaCompra === '2' && !tipoCambio) {
        flag = false;
        errorMsg += 'Ingrese tipo de cambio\n';
    }
    if (!cantidad) {
        flag = false;
        errorMsg += 'Ingrese cantidad de compra\n';
    }
    if (!precio && !total) {
        flag = false;
        errorMsg += 'Ingrese precio unitario o total\n';

    }
    if (flag === false) {
        alert(errorMsg);
        $("#precioProducto").focus();
        return false;
    } else {
        if (monedaCompra === '2') {
            precio = (precio * tipoCambio);
            total = (precio * cantidad);
        }
        params = {
            service: 'addItemImportacion',
            ingresoA: ingresoA,
            idPuntoIngreso: idPuntoIngreso,
            idProductos: idProducto,
            cantidad: cantidad,
            peso: peso,
            arancel: arancel,
            precio: precio,
            precioImport: precioImport,
            total: total,
            totalImport: totalImport
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        getImportacionesDetalle();
                        console.log('item agregado exitosamente');
                        break;
                    case 'failed':
                        alert('Error al agregar producto en compra, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json').done(function () {
            clear();
        });
    }
}
//
function getImportacionesDetalle(idImportacion) {
    $("#detalleImportacion").html('');
    params = {
        service: 'getImportacionesDetalle',
        idImportacion: idImportacion
    };
    var ingreso = "";
    var idPuntoIngreso = "";
    $.post('controllers/inventariosController.php', params, function (data) {
        var datos = "";
        var summary = "";
        var total = 0;
        var totalItems = 0;
        var totalPeso = 0;

        //console.log(data.length);
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='8' class='warning text-center text-warning text-uppercase'><b>0 productos encontrados</b></td>";
            datos += "</tr>";
            $("#valorFacturaImportacion").val(total);
            calculoIvaCompras('3');
        } else {
            $.each(data, function (key, val) {
                if (key === 0) {
                    ingreso = val.ingresoA;
                    idPuntoIngreso = val.idPuntoIngreso;
                }
                total += accounting.unformat(val.total);
                totalItems += accounting.unformat(val.cantidad);
                totalPeso += accounting.unformat(val.peso);
                datos += "<tr>";
                datos += "<td class='action'>";
                datos += "<button class='btn btn-xs btn-danger btn-remove' onclick='removeItemImportacion(" + val.id + "," + val.idProductos + ")'><i class='fa fa-trash'></i></button>";
                //datos += "<button class='btn btn-xs btn-warning' onclick='updateItemImportacion(" + val.id + "," + val.idProductos + ")'><i class='fa fa-refresh'></i></button>";
                datos += "</td>";
                datos += "<td>" + val.sku + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += "<td align='right'><input type='text' class='form-control input-sm text-right' id='cantidad-" + val.id + "' value='" + accounting.formatNumber(val.cantidad, 2) + "'/></td>";
                datos += "<td align='right'><input type='text' class='form-control input-sm text-right' id='peso-" + val.id + "' value='" + accounting.formatNumber(val.peso, 2) + "'/></td>";
                datos += "<td align='right'><input type='text' class='form-control input-sm text-right' id='arancel-" + val.id + "' value='" + accounting.formatNumber(val.arancel, 2) + "'/></td>";
                datos += "<td align='right'><input type='text' class='form-control input-sm text-right' id='precio-" + val.id + "' value='" + accounting.formatNumber(val.precio, 2) + "'/></td>";
                datos += "<td align='right'><input type='text' class='form-control input-sm text-right' id='total-" + val.id + "' value='" + accounting.formatNumber(val.total, 2) + "' onKeydown='javascript: if (event.keyCode == 13 || event.keyCode == 9) updateItemImportacion(" + val.id + "," + val.idProductos + ");'/></td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='7'>Totales</td>";
            summary += "<td align='right'>" + accounting.formatNumber(total, 2) + "</td>";
            summary += "</tr>";
            //
            //$("#valorFacturaImportacion").val(accounting.formatNumber(total, 2));
            $("#cantidadItems").val(accounting.formatNumber(totalItems, 0));
            $("#totalPeso").val(accounting.formatNumber(totalPeso, 0));
            calculoIvaCompras('3');
        }
        $("#detalleImportacion").html(datos);
        $("#summary").html(summary);
    }, 'json').done(function () {
        if (idImportacion !== undefined) {
            $(".btn-remove").attr('disabled', true);
            $("input").prop("disabled", true);
            $("select").prop("disabled", true);
            $("textarea").prop("disabled", true);
            //
            $("#ingresoA option").each(function () {
                if ($(this).val() === ingreso) {
                    $(this).prop('selected', true);
                }
            });
            ingresoA2(idPuntoIngreso);
            $("#monedaCompra option").each(function () {
                if ($(this).val() === '2') {
                    $(this).prop('selected', true);
                }
            });
        } else {
            $(".btn-remove").attr('disabled', false);
            $("input").prop("disabled", false);
            $("select").prop("disabled", false);
            $("textarea").prop("disabled", false);
        }
    });
}
//
function removeItemImportacion(item, idProductos) {
    params = {
        service: 'removeItemImportacion',
        item: item,
        idProductos: idProductos
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
        });
    }, 'json').done(function () {
        getImportacionesDetalle();
    });
}
//
function addGastoImportacion() {
    var idTipoDocumentoGasto = $("#idTipoDocumentoGasto").val();
    var nit = $("#nitPG").val();
    var proveedor = $("#proveedorPG").val();
    var tipoGasto = $("#idTipoGasto").val();
    var fechaFactura = $("#fechaFacturaPG").val();
    var serieFactura = $("#serieFacturaPG").val();
    var noFactura = $("#numeroFacturaPG").val();
    var idMoneda = $("#idMoneda").val();
    var valor = accounting.unformat($("#valorPG").val());
    var tipoCambio = accounting.unformat($("#tipoCambio").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!idTipoDocumentoGasto) {
        flag = false;
        errorMsg += 'Seleccione tipo de documento\n';
    }
    if (!nit) {
        flag = false;
        errorMsg += 'Ingrese NIT del proveedor\n';
    }
    if (!proveedor) {
        flag = false;
        errorMsg += 'Ingrese Nombre del proveedor\n';
    }
    if (!tipoGasto) {
        flag = false;
        errorMsg += 'Seleccione Tipo de Gasto\n';
    }
    if (!fechaFactura) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Factura\n';
    }
    if (!serieFactura) {
        flag = false;
        errorMsg += 'Ingrese Serie de Factura\n';
    }
    if (!noFactura) {
        flag = false;
        errorMsg += 'Ingrese Numero de Factura\n';
    }
    if (!idMoneda) {
        flag = false;
        errorMsg += 'Seleccione moneda de factura\n';
    }
    if (!valor) {
        flag = false;
        errorMsg += 'Ingrese Valor de Factura\n';
    }
    if (!tipoCambio) {
        flag = false;
        errorMsg += 'Ingrese Tipo de Cambio\n';
    }
    if (flag === false) {
        alert(errorMsg);
        $("#precioProducto").focus();
        return false;
    } else {
        params = {
            service: 'addGastoImportacion',
            idTipoDocumentoGasto: idTipoDocumentoGasto,
            nit: nit,
            proveedor: proveedor,
            tipoGasto: tipoGasto,
            fechaFactura: fechaFactura,
            serieFactura: serieFactura,
            noFactura: noFactura,
            idMoneda: idMoneda,
            valor: valor,
            tipoCambio: tipoCambio
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        getImportacionesGastos();
                        console.log('gasto agregado exitosamente');
                        break;
                    case 'failed':
                        alert('Error al agregar producto en compra, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json').done(function () {
            $("#nitPG").val('');
            $("#proveedorPG").val('');
            $("#fechaFacturaPG").val(today);
            $("#serieFacturaPG").val('');
            $("#numeroFacturaPG").val('');
            $("#idMoneda").val('');
            $("#valorPG").val('');
            $("#conceptoPG").val('');
        });
    }
}
//
function getImportacionesGastos(idImportacion) {
    $("#detalle-gastos").html('');
    params = {
        service: 'getImportacionesGastos',
        idImportacion: idImportacion
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        var datos = "";
        var total = 0;
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='11' class='warning text-center text-warning text-uppercase'><b>0 registros encontrados</b></td>";
            datos += "</tr>";
            $("#valorGastosImportacion").val(total);
            calculoIvaCompras('3');
        } else {
            $.each(data, function (key, val) {
                var tipoDocumento = "";
                var tipoGasto = "";
                var tipoMoneda = "";
                if (val.idTipoDocumentoGasto === '1') {
                    tipoDocumento = "Recibo";
                } else {
                    tipoDocumento = "Factura";
                }
                switch (val.idTipoGasto) {
                    case '1':
                        tipoGasto = "Flete";
                        break;
                    case '2':
                        tipoGasto = "Seguro";
                        break;
                    case '3':
                        tipoGasto = "DUA";
                        break;
                    case '4':
                        tipoGasto = "Otros";
                        break;
                }
                if (val.idMoneda === '1') {
                    tipoMoneda = "Local";
                } else {
                    tipoMoneda = "Extranjera";
                }
                total += accounting.unformat(val.subtotal);
                datos += "<tr>";
                datos += "<td class='action'>";
                datos += "<button class='btn btn-xs btn-danger btn-remove' onclick='removeGastoImportacion(" + val.id + ");'><i class='fa fa-trash'></i></button>";
                datos += "</td>";
                datos += "<td>" + tipoDocumento + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.proveedor + "</td>";
                datos += "<td>" + tipoGasto + "</td>";
                datos += "<td>" + moment(val.fechaFactura).format('DD-MM-YYYY') + "</td>";
                datos += "<td>" + val.serieFactura + "</td>";
                datos += "<td>" + val.noFactura + "</td>";
                datos += "<td>" + tipoMoneda + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.valor, 2) + "</td>";
                datos += "</tr>";
            });
            //
            console.log('totalGastos: ' + total);
            $("#valorGastosImportacion").val(accounting.formatNumber(total, 2));
            calculoIvaCompras('3');
        }
        $("#detalle-gastos").append(datos);
    }, 'json').done(function () {
        if (idImportacion !== undefined) {
            $(".btn-remove").attr('disabled', true);
            $("input").prop("disabled", true);
            $("select").prop("disabled", true);
            $("textarea").prop("disabled", true);
        } else {
            $(".btn-remove").attr('disabled', false);
            $("input").prop("disabled", false);
            $("select").prop("disabled", false);
            $("textarea").prop("disabled", false);
        }
    });
}
//
function removeGastoImportacion(item) {
    params = {
        service: 'removeGastoImportacion',
        item: item
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
        });
    }, 'json').done(function () {
        getImportacionesGastos();
    });
}
//
function guardarImportacion() {
    var errorMsg = "Corrige los siguiente errores en la importacion:\n";
    var flag = true;
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var idFormato = $("#idFormato").val();
    var idProveedores = $("#idProveedores").val();
    var idTipoOperacion = $("#idTipoOperacion").val();
    var idTipoImportacion = $("#idTipoImportacion").val();
    var idTipoProrrateo = $("#idTipoProrrateo").val();
    var conceptoImportacion = $("#conceptoImportacion").val();
    var idSucursales = $("#idSucursales").val();
    var serieFactura = $("#serieFactura").val();
    var noFactura = $("#noFactura").val();
    var fechaContabilizacion = $("#fechaContabilizacion").val();
    var fechaFactura = $("#fechaFactura").val();
    var fechaPago = $("#fechaPago").val();
    var tipoCambio = accounting.unformat($("#tipoCambio").val());
    var valorFacturaImportacion = accounting.unformat($("#valorFacturaImportacion").val());
    var valorGastosImportacion = accounting.unformat($("#valorGastosImportacion").val());
    var valorTotalImportacion = accounting.unformat($("#valorTotalImportacion").val());
    var descuento = accounting.unformat($("#descuentoM").val());
    var valorFacturaTipoCambio = accounting.unformat($("#valorFacturaTipoCambio").val());
    var subTotal = accounting.unformat($("#subTotal").val());
    var iva = accounting.unformat($("#iva").val());
    var total = accounting.unformat($("#total").val());
    var cantidadItems = accounting.unformat($("#cantidadItems").val());
    //VALIDACIONES
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!idProveedores) {
        flag = false;
        errorMsg += 'Seleccione proveedor\n';
    }
    if (!idTipoOperacion) {
        flag = false;
        errorMsg += 'Seleccione tipo de operación\n';
    }
    if (!idTipoImportacion) {
        flag = false;
        errorMsg += 'Seleccione tipo de importacion\n';
    }
    if (!idTipoProrrateo) {
        flag = false;
        errorMsg += 'Seleccione tipo de prorrateo\n';
    }
    if (!conceptoImportacion) {
        flag = false;
        errorMsg += 'Ingrese concepto de importacion\n';
    }
    if (!serieFactura) {
        flag = false;
        errorMsg += 'Ingrese Serie de Factura\n';
    }
    if (!noFactura) {
        flag = false;
        errorMsg += 'Ingrese Numero de Factura\n';
    }
    if (!fechaContabilizacion) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Contabilización\n';
    }
    if (!fechaFactura) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Factura\n';
    }
    if (!fechaPago) {
        flag = false;
        errorMsg += 'Ingrese Fecha de Pago de Factura\n';
    }
    if (!tipoCambio) {
        flag = false;
        errorMsg += 'Ingrese Tipo de Cambio\n';
    }
    if (!valorFacturaImportacion) {
        flag = false;
        errorMsg += 'Ingrese Valor de Factura Importacion\n';
    }
    if (cantidadItems === '0') {
        flag = false;
        errorMsg += 'No tiene productos agregados a la importacion\n';
    }
    if (!idSucursales) {
        flag = false;
        errorMsg += 'Seleccione un establecimiento\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'guardarImportacion',
            idTipoDocumento: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            idFormato: idFormato,
            idProveedores: idProveedores,
            idTipoOperacion: idTipoOperacion,
            idTipoImportacion: idTipoImportacion,
            idTipoProrrateo: idTipoProrrateo,
            conceptoImportacion: conceptoImportacion,
            serieFactura: serieFactura,
            noFactura: noFactura,
            fechaContabilizacion: fechaContabilizacion,
            fechaFactura: fechaFactura,
            fechaPago: fechaPago,
            tipoCambio: tipoCambio,
            valorFacturaImportacion: valorFacturaImportacion,
            valorGastosImportacion: valorGastosImportacion,
            valorTotalImportacion: valorTotalImportacion,
            descuento: descuento,
            valorFacturaTipoCambio: valorFacturaTipoCambio,
            subtotal: subTotal,
            iva: iva,
            total: total,
            cantidadItems: cantidadItems,
            fi: $("#factorInventario").val(),
            fv: $("#factorValor").val(),
            fp: $("#factorPeso").val(),
            idSucursales: idSucursales,
            idOrdenCompra: idOrdenesCompra
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Importacion ingresada exitosamente');
                    loadData('vw_importaciones', 'Inventarios', 'Listado de Importaciones', 0, 0, 0);
                } else {
                    alert('Error al ingresar importacion');
                }
            });
        }, 'json');
    }
}
//
function updateItemImportacion(item, idProducto) {
    var cantidad = accounting.unformat($("#cantidad-" + item + "").val());
    var peso = accounting.unformat($("#peso-" + item + "").val());
    var arancel = accounting.unformat($("#arancel-" + item + "").val());
    var precio = accounting.unformat($("#precio-" + item + "").val());
    var total = accounting.unformat($("#total-" + item + "").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!cantidad) {
        flag = false;
        errorMsg += 'Ingrese cantidad de compra\n';
    }
    if (!precio && !total) {
        flag = false;
        errorMsg += 'Ingrese precio unitario o total\n';

    }
    if (flag === false) {
        alert(errorMsg);
        $("#precioProducto").focus();
        return false;
    } else {
        params = {
            service: 'updateItemImportacion',
            item: item,
            idProductos: idProducto,
            cantidad: cantidad,
            peso: peso,
            arancel: arancel,
            precio: precio,
            total: (precio * cantidad)
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        getImportacionesDetalle();
                        alert('item actualizado exitosamente');
                        $("#total-" + idProducto + "").focus();
                        break;
                    case 'failed':
                        alert('Error al agregar producto en compra, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json');
    }
}
//
//
function loadCodigoBarra() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {

        var url = pathJasper + "codigoBarra.php?idProducto=" + id + "";
        window.open(url);
        if (dbProject == 'pos_togasjulissa') {
            var url = pathJasper + "descripcionProducto.php?idProducto=" + id + "";
            window.open(url);
        }


    }
}
//
function salidaTraslados() {
    var tipo = $("#salidaDe").val();
    var container = "idPuntoSalida";
    switch (tipo) {
        case '1':
            loadBodegasEmpresa(container);
            break;
        case '2':
            loadSucursalesEmpresa(container);
            break;
        default :
            $("#" + container).html('');
            alert('Debe seleccionar el tipo de ingreso');
            break;
    }
}
//
function imprimirImportacion() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        var url = pathJasper + "importacion_pdf.php?idImportacion=" + id;
        window.open(url);
    }
}
//
function eliminarImportacion() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        var r = confirm("¿Esta seguro de eliminar esta importacion?");
        if (r == true) {
            params = {
                service: 'eliminarImportacion',
                idImportacion: id
            };
            $.post('controllers/inventariosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Importacion eliminada exitosamente');
                        loadData('vw_importaciones', 'Inventarios', 'Importaciones');
                    } else {
                        alert('Error al eliminar importacion, comuniquese con el administrador del sistema');
                    }
                });
            }, 'json');
        } else {
            return false;
        }
    }
}
//
function eliminarOrdenCompra() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        var r = confirm("¿Esta seguro de eliminar esta orden compra?");
        if (r == true) {
            params = {
                service: 'eliminarOrdenCompra',
                idComprasOrdenes: id
            };
            $.post('controllers/inventariosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Orden de compra eliminada exitosamente');
                        loadData('vw_compras', 'Inventarios', 'Listado de Compras', 0, 0, 0);
                    } else {
                        alert('Error al eliminar compra');
                    }
                });
            }, 'json');
        } else {
            return false;
        }
    }
}
//
function loadCodigosAlternos() {
    $("#loader").show();
    params = {
        idProductos: $("#idProductos").val()
    };
    $.post('views/inventarios/productosCodigosAlternos.php', params, function (respuesta) {
        $('#codigosAlternos').html(respuesta);
    }).done(function () {
        $("#loader").hide();
    });
}
//
function saveCodigoAlterno() {
    $("#loader").show();
    params = {
        idProductos: $("#idProductos").val(),
        skuNuevo: $("#skuNuevo").val(),
        flag: 1
    };
    $.post('views/inventarios/productosCodigosAlternos.php', params, function (respuesta) {
        $('#codigosAlternos').html(respuesta);
        $("#message").fadeOut(7000);
    }).done(function () {
        $("#loader").hide();
    });
}
//
function updateCodigoAlterno(item) {
    $("#loader").show();
    params = {
        idProductos: $("#idProductos").val(),
        sku: $("#sku-" + item).val(),
        item: item,
        flag: 2
    };
    $.post('views/inventarios/productosCodigosAlternos.php', params, function (respuesta) {
        $('#codigosAlternos').html(respuesta);
        $("#message").fadeOut(7000);
    }).done(function () {
        $("#loader").hide();
    });
}
//
function deleteCodigoAlterno(item) {
    $("#loader").show();
    params = {
        idProductos: $("#idProductos").val(),
        item: item,
        flag: 3
    };
    $.post('views/inventarios/productosCodigosAlternos.php', params, function (respuesta) {
        $('#codigosAlternos').html(respuesta);
        $("#message").fadeOut(7000);
    }).done(function () {
        $("#loader").hide();
    });
}
//
function loadProductosContabilidad(option) {
    var title = "Nueva Partida Automática";
    var idFormato;
    if (option !== undefined) {
        title = "Editar Partida Automática";
        $('.data').each(function () {
            if (this.checked) {
                idFormato = $(this).val();
            }
        });
        if (idFormato === undefined) {
            bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
                $('#modal1').modal('hide');
            });
            return false;
        }
    }
    params = {
        idFormato: idFormato
    };
    $.post('views/inventarios/productosContabilidad.php', params, function (respuesta) {
        $('#contabilidad').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html(title);
        $("#partida_at").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
    }).done(function () {
        if (idFormato === undefined) {
            $("#save").show();
            $("#update").hide();
        } else {
            $("#save").hide();
            $("#update").show();
            //getFormatoDetalle(idFormato);
        }
    });
}
//
function addRowProductoContabilidad(table) {
    //
    var table1 = '"vw_nomenclatura"';
    var table2 = '"vw_centrosCosto"';
    var title1 = '"Cuentas Contables"';
    var title2 = '"Centros de Costo"';
    var modulo1 = '"cuentasContables"';
    var modulo2 = '"centrosCosto"';
    //
    var datos = "<tr id='item-" + contador + "'>";
    datos += "<td>";
    datos += "<button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + table1 + "," + title1 + "," + modulo1 + "," + contador + ");'>";
    datos += "<i class='fa fa-question'></i>";
    datos += "</button>";
    datos += "<input type='hidden' class='idNomenclatura' id='idNomenclaturaV-" + contador + "'/>";
    datos += "<input class='form-control input-sm' id='idNomenclatura-" + contador + "' readonly='' style='width: 92%;'/>";
    datos += "</td>";
    datos += " <td>";
    datos += "<select class='form-control input-sm debe' id='debe-" + contador + "'></select>";
    datos += "</td>";
    datos += "<td>";
    datos += "<select class='form-control input-sm haber' id='haber-" + contador + "'></select>";
    datos += "</td>";
    datos += "<td>";
    datos += "<button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + table2 + "," + title2 + "," + modulo2 + "," + contador + ");'>";
    datos += "<i class='fa fa-question'></i>";
    datos += "</button>";
    datos += "<input type='hidden' class='idCentrosCosto' id='idCentrosCostoV-" + contador + "'/>";
    datos += "<input class='form-control input-sm' id='idCentrosCosto-" + contador + "' readonly='' style='width: 85%;'/>";
    datos += "</td>";
    datos += "</td>";
    datos += "<td align='center'>";
    datos += "<select class='form-control input-sm'></select>";
    datos += "</td>";
    datos += "<td align='center'>";
    datos += "<button class='btn btn-danger btn-sm' onclick='removeRowPartida(" + contador + ");'>";
    datos += "<i class='fa fa-trash-o'></i>";
    datos += "</button>";
    datos += "</tr>";
    $("#" + table + " tbody").append(datos);
    getFormulas(contador);
    contador += 1;
}
//
function updateOrdenCompra() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        nuevaOrdenCompra(id);
    }
}
//
function actualizarOrdenCompra() {
    var observaciones = $("#observaciones").val();
    var idTipoOrdenCompra = $("#idTipoOrdenCompra").val();
    var fechaArribo = $("#fechaArribo").val();
    var tipoCambio = $("#tipoCambio").val();
    var idMonedas = $("#idMonedas").val();
    var numeroProductos = accounting.unformat($("#numeroProductos").val());
    var totalOC = accounting.unformat($("#totalOC").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!observaciones) {
        flag = false;
        errorMsg += 'Ingrese observaciones\n';
    }
    if (!idTipoOrdenCompra) {
        flag = false;
        errorMsg += 'Seleccione tipo de orden de compra\n';
    }
    if (!idMonedas) {
        flag = false;
        errorMsg += 'Seleccione moneda\n';
    }
    if (!tipoCambio) {
        flag = false;
        errorMsg += 'Ingrese tipo de cambio\n';
    }
    if (!numeroProductos) {
        flag = false;
        errorMsg += 'No tiene productos agregados a la orden de compra\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var r = confirm("¿Esta seguro de actualizar esta orden de compra?");
        if (r == true) {
            params = {
                service: 'actualizarOrdenCompra',
                observaciones: observaciones,
                idTipoOrdenCompra: idTipoOrdenCompra,
                fechaArribo: fechaArribo,
                tipoCambio: tipoCambio,
                monto: totalOC,
                idMonedas: idMonedas,
                idOrdenCompra: $("#idOrdenCompra").val()
            };
            $.post('controllers/inventariosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Orden de compra actualizada exitosamente');
                        var url = pathJasper + "ordenCompra_pdf.php?idComprasOrden=" + val.idOrdenCompra;
                        window.open(url);
                        loadData('vw_comprasOrdenes', 'Inventarios', 'Ordenes de Compra', 0, 0, 0);
                    } else {
                        alert(val.message);
                    }
                });
            }, 'json');
        } else {
            return false;
        }
    }
}
//
function getOrdenCompra(idOrdenCompra, proveedor) {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var validar = idOrdenesCompra.filter(p => p.ordenCompra === idOrdenCompra);
    var modulo = $("#moduloProceso").val();
    var ingresoA = $("#ingresoA").val();
    var idPuntoIngreso = $("#idPuntoIngreso").val();
    if (idOrdenesCompra.length > 0 && validar.length > 0) {
        flag = false;
        errorMsg += 'Orden de compra ya esta seleccionada\n';
    }
    if (idOrdenesCompra.length > 0 && $("#nombre").val() !== proveedor) {
        flag = false;
        errorMsg += 'Orden de compra seleccionada no esta asociada al proveedor ' + $("#nombre").val() + ' \n';
    }
    if (modulo === 'importaciones') {
        if (!ingresoA) {
            flag = false;
            errorMsg += 'Seleccione opcion de ingreso\n';
        }
        if (!idPuntoIngreso) {
            flag = false;
            errorMsg += 'Seleccione bodega o sucursal de ingreso\n';
        }
    }
    if (flag === false) {
        alert(errorMsg);
        cancelarModal();
        $("#ingresoA").focus();
        return false;
    } else {
        var arr = {};
        arr['ordenCompra'] = idOrdenCompra;
        idOrdenesCompra.push(arr);
        params = {
            service: 'getOrdenCompraImport',
            idOrdenCompra: idOrdenCompra,
            action: 'get',
            modulo: modulo
        };
        $("#loader").show();
        $.post('controllers/inventariosController.php', params, function (data) {
            if (data === null) {
                $("#loader").hide();
                alert('Orden de compra ya gestionada o no existente en el sistema');
                $('.ordenesCompra').prop('checked', false);
            } else {
                $.each(data, function (key, val) {
                    $("#idOrdenCompra").val(params.idOrdenCompra);
                    $("#noOrdenCompra").val(val.documento);
                    $("#idProveedores").val(val.idProveedores);
                    $("#nit").val(val.nitP);
                    $("#nombre").val(val.proveedor);
                    $("#direccion").val(val.direccionP);
                    $("#diasCreditoP").val(val.diasCredito);
                    $("#pequenoContribuyente").val(val.idPequenoContribuyente);
                    $("#conceptoCompra").append(val.documento + ' - ' + val.observaciones + '; ');
                    $("#conceptoImportacion").append(val.documento + ' - ' + val.observaciones + '; ');
                    $("#tipoCambio").val(val.tipoCambio);
                    $("#monedaCompra option").each(function () {
                        if ($(this).val() === val.idTipoOrdenCompra) {
                            $(this).prop('selected', true);
                        }
                    });
                });
                getOrdenCompraDetalle(params.idOrdenCompra, params.action, params.modulo);
            }
        }, 'json').done(function () {
            $("#loader").hide();
            $("#modal1").modal('hide');
            console.log(idOrdenesCompra);
        });
    }
}
//
function loadProductosEquivalencias() {
    $("#loader").show();
    params = {
        idProductos: $("#idProductos").val()
    };
    $.post('views/inventarios/productosEquivalencias.php', params, function (respuesta) {
        $('#productosEquivalencias').html(respuesta);
    }).done(function () {
        $("#loader").hide();
    });
}
//
function saveProductosEquivalencias(idProductos) {
    $("#loader").show();
    params = {
        idProductos: idProductos,
        idMedidas: $("#idMedidasPE").val(),
        equivalente: $("#equivalentePE").val(),
        costo: $("#costoPE").val(),
        flag: 1
    };
    $.post('views/inventarios/productosEquivalencias.php', params, function (respuesta) {
        $('#codigosAlternos').html(respuesta);
        $("#message").fadeOut(7000);
    }).done(function () {
        $("#loader").hide();
    });
}
//
function updateImportacionesDetalle(tipoCambio, idImportacion) {
    params = {
        service: 'updateImportacionesDetalle',
        tipoCambio: tipoCambio,
        idImportacion: idImportacion
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                getImportacionesDetalle(params.idImportacion);
            } else {
                alert(val.message);
            }
        });
    }, 'json');
}
//
function consultarImportacion() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        //$("#controllers").html('<div class="alert alert-warning" role="alert">Error: Debe seleccionar un registro a editar</div>');
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        loadRecepcionImportacion(id);
    }
}
//
function loadConsumoMaterialesDelispan() {
    $.post('views/inventarios/consumoMaterialesDelispan.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Inventarios');
        $("#opcion").html('Consumo de Materiales');
        $("#fechaInicio,#fechaFin").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
    });
}