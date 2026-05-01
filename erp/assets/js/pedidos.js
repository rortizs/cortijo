$(document).ready(function () {
    //loadConsultaPedidos();
});
//
function loadConsultaPedidos() {
    $.post('views/caja/consultaPedidos.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Pedidos');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control,#fechaFin .form-control").attr('readonly', true);
        loadVendedores('admin');
        switch (dbProject) {
            case 'erp_gsp':
                switch (idRoles) {
                    case '1':
                        $("#reImprimirPedidoMercaderia").show();
                        $("#confirmarPedido").show();
                        $("#cancel").show();
                        $("#update").show();
                        $("#reImprimirExamenesPedido").hide();
                        break;
                    case '2':
                        $("#reImprimirPedidoMercaderia").show();
                        $("#confirmarPedido").show();
                        $("#cancel").show();
                        $("#update").show();
                        $("#reImprimirExamenesPedido").hide();
                        break;
                    case '19':
                        $("#reImprimirPedidoMercaderia").show();
                        $("#confirmarPedido").show();
                        $("#cancel").hide();
                        $("#update").hide();
                        $("#reImprimirExamenesPedido").hide();
                        break;
                    default :
                        $("#reImprimirPedidoMercaderia").hide();
                        $("#confirmarPedido").hide();
                        $("#cancel").hide();
                        $("#update").hide();
                        $("#reImprimirExamenesPedido").hide();
                        break;
                }
                break;
            case 'pos_vitalab':
                $("#reImprimirPedidoMercaderia").hide();
                $("#confirmarPedido").hide();
                $("#reImprimirExamenesPedido").show();
                break;
            case 'pos_togasjulissa':
                $("#reImprimirPedidoMercaderia").show();
                $("#confirmarPedido").hide();
                $("#reImprimirExamenesPedido").hide();
                break;
            case 'erp_kairos':
                $("#reImprimirPedidoMercaderia").show();
                $("#confirmarPedido").show();
                $("#reImprimirExamenesPedido").show();
                break;
            default :
                $("#reImprimirPedidoMercaderia").hide();
                $("#confirmarPedido").hide();
                $("#reImprimirExamenesPedido").hide();
                break;
        }
    });
}
//
function consultarPedidos() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var estadoPedido = $("#estadoPedido").val();
    var idVendedores = $("#vendedores").val();
    var cliente = $("#cliente").val();
    var noPedido = $("#noPedido").val();
    params = {
        service: 'consultarPedidos',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        estadoPedido: estadoPedido,
        idVendedores: idVendedores,
        cliente: cliente,
        noPedido: noPedido
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $("#detalle").html('');
        $("#summary").html('');
        var datos = "";
        var summary = "";
        var total = 0;
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='12' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var total = 0;
            $.each(data, function (key, val) {
                var status = "";
                switch (val.estado) {
                    case 'Abierto':
                        status = "alert-info";
                        break;
                    case 'Cancelado':
                        status = "alert-danger";
                        break;
                    case 'Facturado':
                        status = "alert-success";
                        break;
                    case 'Confirmado':
                        status = "alert-success";
                        break;
                }
                total += accounting.unformat(val.total);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='pedidos' value='" + val.idPedido + "' data-status='" + val.estado + "'/></td>";
                datos += "<td>" + val.fecha + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.nombre + "</td>";
                datos += "<td>" + val.direccion + "</td>";
                datos += "<td>" + val.observaciones + "</td>";
                datos += "<td>" + val.idSucursales + "</td>";
                datos += "<td>" + val.vendedor + "</td>";
                datos += "<td class='" + status + " text-center'>" + val.estado + "</td>";
                datos += "<td>" + val.noVentas + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.total, 'Q. ') + "</td>";
                datos += "</tr>";
            });
            summary += "<tr>";
            summary += "<td colspan='11'>Total Pedidos:</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json').done(function () {
        $("#reportContainer").height((alto / 2) - 45);
    });
}
//
function reImprimirPedido() {
    var id;
    $('.pedidos').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        switch (dbProject) {
            case 'pos_vitalab':
                alert('Pedido generado No. ' + correlativo);
                break;
            case 'pos_lolascloset':
                var url = pathJasper + "ticket.php?idPedido=" + id + "";
                window.open(url);
                break;
            default:
                var url = pathJasper + "pedidos.php?idPedido=" + id + "";
                window.open(url);
                break;
        }
    }
}
//
function reImprimirPedidoMercaderia() {
    var id;
    $('.pedidos').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "pedidoMercaderia.php?idPedido=" + id + "";
        window.open(url);
    }
}
//
function editarPedido() {
    var id;
    var estado;
    $('.pedidos').each(function () {
        if (this.checked) {
            id = $(this).val();
            estado = $(this).attr("data-status");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        loadModuloPedidos(id, 'edit');
    }
}
//
function loadModuloPedidos(idPedido, action) {
    $.post('views/caja/pedidos.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $('#fechaFactura').datetimepicker({
            pickTime: false,
            format: 'DD/MM/YYYY'
        });
        $(".nav").show();
        $("#camposCredito").hide();
        if (idPedido === undefined) {
            loadDocumentos('pedidos');
        }
        loadVendedores('admin');
        loadProductosPedido();
        $("#nit").focus();
        $("#opcion").html('Pedidos');
    }).done(function () {
        if (idPedido !== undefined) {
            getPedido(idPedido, action);
            $("#add").hide();
            $("#edit").show();
            $("#back").show();
        } else {
            $("#add").show();
            $("#edit").hide();
            $("#back").hide();
        }
    });
    cancelarPedido(0);
}
//
function cancelarPedido(flag) {
    if (flag !== undefined) {
        var id = '';
        params = {
            service: 'cancelarPedido',
            idPedido: id
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message !== 'success') {
                    alert('Error al cancelar pedido');
                }
            });
        }, 'json');
    } else {
        var r = confirm("¿Esta seguro de cancelar este pedido?");
        if (r == true) {
            var id;
            var estado;
            $('.pedidos').each(function () {
                if (this.checked) {
                    id = $(this).val();
                    estado = $(this).attr("data-status");
                }
            });
            console.log(estado);
            if (estado !== 'Abierto' && estado !== 'Confirmado' && estado !== undefined) {
                bootbox.alert('<br/><div class="alert alert-danger" role="alert"> <strong>Alerta!</strong> Pedido no puede ser cancelado</div>');
            } else {
                params = {
                    service: 'cancelarPedido',
                    idPedido: id,
                    estado: estado
                };
                $.post('controllers/cajaController.php', params, function (data) {
                    $.each(data, function (key, val) {
                        if (val.message === 'success') {
                            if (id !== undefined) {
                                loadModuloPedidos();
                            } else {
                                loadModuloPedidos();
                            }
                        } else {
                            alert('Error al cancelar pedido');
                        }
                    });
                }, 'json');
            }
        } else {
            return false;
        }
    }
}
//
function actualizarPedido() {
    var fechaPedido = $("#fechaPedido").val();
    var nit = $("#nit").val();
    var nombre = $("#nombre").val();
    var telefono = $("#telefono").val();
    var direccion = $("#direccion").val();
    var total = $("#total").val();
    var observaciones = $("#observaciones").val();
    var tipoVenta = $("#tipoVenta").val();
    var numeroProductos = $("#numeroProductos").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    //VALIDACIONES
    if (!fechaPedido) {
        flag = false;
        errorMsg += 'Ingrese fecha de pedido\n';
    }
    if (!nit) {
        flag = false;
        errorMsg += 'Ingrese NIT del Cliente\n';
    }
    if (!nombre) {
        flag = false;
        errorMsg += 'Ingrese Nombre del Cliente\n';
    }
    if (!direccion) {
        flag = false;
        errorMsg += 'Ingrese Direccion del Cliente\n';
    }
    if (!tipoVenta) {
        flag = false;
        errorMsg += 'Seleccione tipo de venta\n';
    }
    if (numeroProductos === '0') {
        flag = false;
        errorMsg += 'No tiene productos agregados al pedido\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'actualizarPedido',
            fechaPedido: fechaPedido,
            nit: nit,
            nombre: nombre,
            telefono: telefono,
            direccion: direccion,
            total: total,
            observaciones: observaciones,
            tipoVenta: tipoVenta,
            idPedido: $("#idPedido").val()
        };
        $("#edit").attr({
            disabled: true
        });
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = pathJasper + "pedidos.php?idPedido=" + val.idPedido + "";
                    window.open(url);
                    pedidoMercaderia(val.idPedido);
                    loadConsultaPedidos();
                } else {
                    alert('Error al actualizar pedido, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function getPedidoMedida(idPedido, action) {

    switch (action) {
        case 'get':

            params = {
                service: 'getPedido',
                idPedido: idPedido
            };
            $("#loader").show();
            $.post('controllers/cajaController.php', params, function (data) {
                if (data === null) {
                    $("#loader").hide();
                    alert('Pedido ya gestionado o no existente en el sistema');
                } else {
                    var idPedido = "";
                    $.each(data, function (key, val) {
                        idPedido = val.id;
                        $("#idPedidoHidden").val(val.id);
                        $("#noPedido").val(val.documento);
                        $("#nombrePedido").val(val.nombre);

                    });
                }
            }, 'json');
            break;
    }
    $("#loader").hide();
    $("#modal1").modal('hide');
}
//
function getPedido(idPedido, action) {
    switch (action) {
        case 'edit':
            params = {
                service: 'getPedido',
                idPedido: idPedido,
                action: action
            };
            $("#loader").show();
            $.post('controllers/cajaController.php', params, function (data) {
                if (data === null) {
                    $("#loader").hide();
                    alert('Pedido ya gestionado o no existente en el sistema');
                } else {
                    var idPedido = "";
                    var saldo = "";
                    $.each(data, function (key, val) {
                        idPedido = val.id;
                        $("#btnNIT").attr('disabled', true);
                        $("#btnCotizacion,#noCotizacion").attr('disabled', true);
                        $("#nit").val(val.nit).attr('disabled', true);
                        $("#idPedido").val(idPedido);
                        $("#fechaPedido").val(val.fechaPedido);
                        $("#tipoDocumento").attr('disabled', true);
                        $("#correlativo").val(val.documento);
                        $("#nombre").val(val.nombre).attr('disabled', true);
                        $("#telefono").val(val.telefono);
                        $("#direccion").val(val.direccion);
                        $("#observaciones").val(val.observaciones);
                        $("#vendedores option").each(function () {
                            if ($(this).val() === val.idUsuarios) {
                                $(this).prop('selected', true);
                            }
                        });
                        $("#vendedores").attr({
                            disabled: true
                        });
                        $("#tipoVenta option").each(function () {
                            if ($(this).val() === val.idTipoVenta) {
                                $(this).prop('selected', true);
                            }
                        });
                        if (val.estado !== '1') {
                            $("#edit,#delete").attr('disabled', true);
                        }
                    });
                    loadProductosPedido(idPedido, action);
                }
            }, 'json');
            break;
        case 'get':
            if (!$("#idSucursales").val()) {
                alert('Seleccione un establecimiento');
            } else if ($("#noPedido").val() != '') {
                alert("ya existe un pedido en curso");
            } else {
                params = {
                    service: 'getPedido',
                    idPedido: idPedido
                };
                $("#loader").show();
                $.post('controllers/cajaController.php', params, function (data) {
                    if (data === null) {
                        $("#loader").hide();
                        alert('Pedido ya gestionado o no existente en el sistema');
                    } else {
                        var idPedido = "";
                        $.each(data, function (key, val) {
                            idPedido = val.id;
                            $("#nit").val(val.nit);
                            $("#idPedido").val(idPedido);
                            $("#idClientes").val(val.idClientes);
                            $("#noPedido").val(val.noPedido);
                            $("#nombre").val(val.nombre);
                            $("#telefono").val(val.telefono);
                            $("#direccion").val(val.direccion);
                            $("#observaciones").val(val.observaciones);
                            $("#vendedores option").each(function () {
                                if ($(this).val() === val.idUsuarios) {
                                    $(this).prop('selected', true);
                                }
                            });
                        });
                        loadProductosPedido(idPedido, 'get');
                    }
                }, 'json');
            }
            break;
    }
}
//
function confirmarPedido() {
    var id;
    var estado;
    $('.pedidos').each(function () {
        if (this.checked) {
            id = $(this).val();
            estado = $(this).attr("data-status");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else if (estado !== 'Abierto') {
        bootbox.alert('<br/><div class="alert alert-danger" role="alert"> <strong>Alerta!</strong> Solo se pueden confirmar pedidos con estatus Abierto</div>');
    } else {
        var r = confirm("¿Esta seguro de procesar este pedido?");
        if (r == true) {
            $("#loader").show();
            params = {
                service: 'confirmarPedido',
                idPedido: id
            };
            $.post('controllers/inventariosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    switch (val.message) {
                        case 'success':
                            alert('Pedido confirmado exitosamente');
                            loadConsultaPedidos();
                            break;
                        case 'sinExistencias':
                            alert('No se puede confirmar pedido, verifique las existencias en bodega');
                            break;
                        default:
                            alert('Error al confirmar pedido, comuniquese con el administrador del sistema');
                            break;
                    }
                });
            }, 'json').done(function () {
                $("#loader").hide();
            });
        } else {
            return false;
        }
    }
}
//
function loadProductosPedido(idPedido, action) {
    var ingresoA = $("#ingresoA").val();
    var idPuntoIngreso = $("#idPuntoIngreso").val();
    params = {
        service: 'getProductosPedido',
        idPedido: idPedido,
        ingresoA: ingresoA,
        idPuntoIngreso: idPuntoIngreso,
        action: action
    };
    $.post('controllers/cajaController.php', params, function (data) {
        console.log(idPedido);
        if (idPedido !== '' && action === 'get') {
            $.each(data, function (key, val) {
                agregarProductoVenta(val.idProductos, val.tipoProducto, val.precio, val.cantidad, val.existencia);
            });
        } else {
            $("#detalle").html('');
            var datos = "";
            var total2 = 0;
            if (data === null) {
                datos += "<tr>";
                datos += "<td colspan='7' align='center'>0 productos encontrados</td>";
                datos += "</tr>";
                $("#numeroProductos").val(0);
            } else {
                $("#numeroProductos").val(data.length);
                $.each(data, function (key, val) {
                    total2 += parseFloat(val.total);
                    datos += "<tr>";
                    datos += "<td>" + (key + 1) + "</td>";
                    datos += "<td><button class='btn btn-xs btn-danger' onclick='eliminarProductoPedido(" + val.idProductos + ")'><i class='fa fa-trash'></i></button></td>";
                    datos += "<td>" + val.sku + "</td>";
                    datos += "<td>" + val.descLarga + "</td>";
                    datos += "<td align='right'>" + val.cantidad + "</td>";
                    datos += "<td align='right'>" + accounting.formatNumber(val.precio, 2) + "</td>";
                    datos += "<td align='right'>" + accounting.formatNumber(val.total, 2) + "</td>";
                    datos += "</tr>";
                });
            }
            //
            $("#subTotal").val(total2.toFixed(2));
            $("#txtSubTotal").html(accounting.formatNumber(total2, 2));
            $("#total").val(total2.toFixed(2));
            $("#txtTotal").html(accounting.formatNumber(total2, 2));
            $("#detalle").append(datos);
            var totalDolares = parseFloat($("#total").val()) / parseFloat($("#tasaCambio").val());
            $("#totalDolares").val(totalDolares.toFixed(2));
        }
    }, 'json').done(function () {
        $("#loader").hide();
        $("#modal1").modal('hide');
    });
}
//
function pedidoMercaderia(idPedido) {
    var url = pathJasper + "pedidoMercaderia.php?idPedido=" + idPedido;
    window.open(url);
}
//
function agregarProductoPedido(idProducto, tipoProducto, precioPublico, cantidad, existencia, action, codigo, descripcion) {
    var ingresoA = $("#ingresoA").val();
    var idPuntoIngreso = $("#idPuntoIngreso").val();
    var valExistencias = accounting.unformat($("#valExistencias").val());
    var idProducto = (idProducto !== undefined ? idProducto : $("#idProducto").val());
    var tipoProducto = (tipoProducto !== undefined ? tipoProducto : $("#tipoProducto").val());
    var precio = accounting.unformat((precioPublico !== undefined ? precioPublico : $("#precioProducto").val()));
    var cantidad = accounting.unformat((cantidad !== undefined ? cantidad : $("#cantidadProducto").val()));
    var existencia = accounting.unformat((existencia !== undefined ? existencia : $("#existencia").val()));
    var total = precio * cantidad;
    var errorMsg = "";
    var errorMsgPC = "";
    var flag = true;
    console.log(existencia);
    console.log('-');
    console.log(cantidad);
    console.log('-');
    console.log(tipoProducto);
    if(!cantidad || cantidad === 0 || cantidad < 0 && valExistencias !== 0){
        flag + false;
        errorMsg += 'La cantidad ingresada no puede ser vacio, cero o menor a cero \n';
        errorMsg += 'Codigo: ' + codigo + '\n';
        errorMsg += 'Descripcion: ' + descripcion + '\n';
        errorMsg += 'Exitencia: ' + existencia + '\n';
        errorMsg += 'Cantidad solicitada: ' + cantidad + '\n';
    }
    if (
      tipoProducto !== "Servicio" &&
      tipoProducto !== "Producto Fabricado" &&
      (cantidad > existencia) & (valExistencias === 0)
    ) {
      flag = false;
      errorMsg += `La cantidad ingresada es mayor que la existencia del producto\n
                    Producto: ${descLarga}\n
                    Cantidad: ${cantidad}\n
                    Existencia: ${existencia}\n`;
    if(!cantidad){
        flag = false;
        errorMsg += `Ingrese cantidad del producto\n`;
    }if(precio < costo){
        flag = false;
        errorMsg += `El precio de venta no puede ser menor a: ${$('#costoProducto').val()}\n`;
    }
    if(flag === false){
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'agregarProductoPedido',
            tipoProducto: tipoProducto,
            idProducto: idProducto,
            cantidad: cantidad,
            precio: precio,
            total: total,
            idPedido: $("#idPedido").val(),
            ingresoA: ingresoA,
            idPuntoIngreso: idPuntoIngreso,
            valExistencias: valExistencias
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadProductosPedido($("#idPedido").val(), action);
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
            $("#loader").hide();
            if (errorMsg !== '') {
                alert(errorMsg + errorMsgPC);
            }
        });
    }
}
//
function generarPedido() {
    var fechaPedido = $("#fechaPedido").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var correlativo = $("#correlativo").val();
    var idClientes = $("#idClientes").val();
    var nit = $("#nit").val();
    var nombre = $("#nombre").val();
    var telefono = $("#telefono").val();
    var direccion = $("#direccion").val();
    var total = $("#total").val();
    var observaciones = $("#observaciones").val();
    var idVendedores = $("#vendedores").val();
    var tipoVenta = $("#tipoVenta").val();
    var numeroProductos = $("#numeroProductos").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    //VALIDACIONES 
    if (!fechaPedido) {
        flag = false;
        errorMsg += 'Ingrese fecha de facturación\n';
    }
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!nit) {
        flag = false;
        errorMsg += 'Ingrese NIT del Cliente\n';
    }
    if (!nombre) {
        flag = false;
        errorMsg += 'Ingrese Nombre del Cliente\n';
    }
    if (!direccion) {
        flag = false;
        errorMsg += 'Ingrese Direccion del Cliente\n';
    }
    if (!idVendedores) {
        flag = false;
        errorMsg += 'Ingrese Vendedor del pedido\n';
    }
    if (!tipoVenta) {
        flag = false;
        errorMsg += 'Seleccione tipo de venta\n';
    }
    if (numeroProductos === '0') {
        flag = false;
        errorMsg += 'No tiene productos agregados al pedido\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'generarPedido',
            fechaPedido: fechaPedido,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            idClientes: idClientes,
            nit: nit,
            nombre: nombre,
            telefono: telefono,
            direccion: direccion,
            total: total,
            observaciones: observaciones,
            tipoVenta: tipoVenta,
            idVendedores: idVendedores,
            idCotizaciones: idCotizaciones
        };
        $("#btnImprimir").attr({
            disabled: true
        });
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    switch (dbProject) {
                        case 'pos_vitalab':
                            alert('Pedido generado No. ' + correlativo);
                            break;
                        case 'pos_lolascloset':
                            var url = pathJasper + "ticket.php?idPedido=" + val.idPedido + "";
                            window.open(url);
                            break;
                        case 'erp_gsp':
                            var url = pathJasper + "pedidos.php?idPedido=" + val.idPedido + "";
                            window.open(url);
                            pedidoMercaderia(val.idPedido);
                            break;
                        case 'pos_togasjulissa':
                            var url = pathJasper + "pedidos.php?idPedido=" + val.idPedido + "";
                            window.open(url);
                            pedidoMercaderia(val.idPedido);
                            break;
                    }
                    loadModuloPedidos();
                } else {
                    console.log(val.error);
                    console.log(val.query);
                    alert('Error al generar pedido, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function exportarReportePedidos() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var noPedido = $("#noPedido").val();
    var estadoPedido = $("#estadoPedido").val();
    var cliente = $("#cliente").val();
    var idVendedores = $("#vendedores").val();

    params = {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        noPedido: noPedido,
        estadoPedido: estadoPedido,
        cliente: cliente,
        idVendedores: idVendedores
    };
    var url = "views/reportes/pedidos-excel.php";
    $.redirect(url, params, 'post');
}