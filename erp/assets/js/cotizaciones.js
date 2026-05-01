$(document).ready(function () {
    //loadConsultaCotizaciones();
});
//
function loadConsultaCotizaciones() {
    $.post('views/caja/consultaCotizaciones.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Cotizaciones');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control,#fechaFin .form-control").attr('readonly', true);
        loadVendedores('admin');
        switch (accounting.unformat($("#idRolesUsuario").val())) {
            case 1:
                $("#add").show();
                $("#update").show();
                break;
            case 2:
                $("#add").show();
                $("#update").show();
                break;
            default:
                $("#add").hide();
                $("#update").hide();
                break;
        }
    });
}
//
function consultarCotizaciones() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var estadoCotizacion = $("#estadoCotizacion").val();
    var cliente = $("#cliente").val();
    var idVendedores = $("#vendedores").val();
    var noCotizacion = $("#noCotizacion").val();
    params = {
        service: 'consultarCotizaciones',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        estadoCotizacion: estadoCotizacion,
        cliente: cliente,
        idVendedores: idVendedores,
        noCotizacion: noCotizacion
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $("#detalle").html('');
        $("#summary").html('');
        var datos = "";
        var summary = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='12' class='text-info text-center'><b>0 registros encontrados</b></td>";
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
                    case 'Procesada':
                        status = "alert-success";
                        break;
                }
                total += accounting.unformat(val.total);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='cotizaciones' value='" + val.idCotizacion + "' data-status='" + val.estado + "'/></td>";
                datos += "<td>" + val.fecha + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.nombre + "</td>";
                datos += "<td>" + val.direccion + "</td>";
                datos += "<td>" + val.observaciones + "</td>";
                datos += "<td>" + val.idSucursales + "</td>";
                datos += "<td>" + val.vendedor + "</td>";
                datos += "<td class='" + status + " text-center'>" + val.estado + "</td>";
                datos += "<td>" + val.noPedido + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.total, 'Q. ') + "</td>";
                datos += "</tr>";
            });
        }
        summary += "<tr>";
        summary += "<td colspan='11'>Total Cotizaciones</td>";
        summary += "<td align='right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
        summary += "</tr>";
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json').done(function () {
        $("#reportContainer").height((alto / 2) - 45);
    });
}
//
function reImprimirCotizacion() {
    var id;
    $('.cotizaciones').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "cotizaciones.php?idCotizacion=" + id + "";
        window.open(url);
    }
}
//
function cancelarCotizacion(flag) {
    if (flag !== undefined) {
        var id = '';
        params = {
            service: 'cancelarCotizacion',
            idCotizacion: id
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message !== 'success') {
                    alert('Error al cancelar cotizacion');
                }
            });
        }, 'json');
    } else {
        var r = confirm("¿Esta seguro de cancelar esta cotizacion?");
        if (r == true) {
            var id;
            var estado;
            $('.cotizaciones').each(function () {
                if (this.checked) {
                    id = $(this).val();
                    estado = $(this).attr("data-status");
                }
            });
            if (estado !== 'Abierto' && estado !== undefined) {
                bootbox.alert('<br/><div class="alert alert-danger" role="alert"> <strong>Alerta!</strong> Solo se pueden cancelar cotizaciones abiertas.</div>');
            } else {
                params = {
                    service: 'cancelarCotizacion',
                    idCotizacion: (id === undefined ? $("#idCotizacion").val() : id)
                };
                $.post('controllers/cajaController.php', params, function (data) {
                    $.each(data, function (key, val) {
                        if (val.message === 'success') {
                            if (id !== undefined) {
                                consultarCotizaciones();
                            } else {
                                loadModuloCotizaciones();
                            }
                        } else {
                            alert('Error al cancelar cotizacion, comuniquese con el administrador del sistema');
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
function editarCotizacion() {
    var id;
    var estado;
    $('.cotizaciones').each(function () {
        if (this.checked) {
            id = $(this).val();
            estado = $(this).attr("data-status");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        loadModuloCotizaciones(id, 'edit');
    }
}
//
function loadModuloCotizaciones(idCotizacion, action) {
    $.post('views/caja/cotizaciones.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $('#fechaCotizacion').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).attr('readonly', true);
        $(".nav").show();
        $("#camposCredito").hide();
        if (idCotizacion === undefined) {
            loadDocumentos('cotizaciones');
        }
        loadProductosCotizacion();
        loadVendedores('admin');
        $("#nit").focus();
        $("#opcion").html('Cotizaciones');
    }).done(function () {
        if (idCotizacion !== undefined) {
            getCotizacion(idCotizacion, action);
            $("#add").hide();
            $("#edit").show();
            $("#back").show();
        } else {
            $("#add").show();
            $("#edit").hide();
            $("#back").hide();
        }
        $("#loader").hide();
    });
    cancelarCotizacion(0);
}
//
function loadProductosCotizacion(idCotizacion, action) {
    params = {
        service: 'getProductosCotizacion',
        idCotizacion: idCotizacion
    };
    $.post('controllers/cajaController.php', params, function (data) {
        if (idCotizacion !== undefined && action === 'get') {
            $.each(data, function (key, val) {
                agregarProductoPedido(val.idProductos, val.tipoProducto, val.precio, val.cantidad, val.existencia, action, val.sku, val.descLarga);
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
                    datos += "<td><button class='btn-item btn btn-xs btn-danger' onclick='eliminarProductoCotizacion(" + val.idProductos + ")'><i class='fa fa-trash'></i></button></td>";
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
function getCotizacion(idCotizacion, action) {
    switch (action) {
        case 'edit':
            params = {
                service: 'getCotizacion',
                idCotizacion: idCotizacion,
                action: action
            };
            $("#loader").show();
            $.post('controllers/cajaController.php', params, function (data) {
                if (data === null) {
                    $("#loader").hide();
                    alert('Cotizacion ya gestionada o no existente en el sistema');
                } else {
                    var idCotizacion = "";
                    $.each(data, function (key, val) {
                        idCotizacion = val.id;
                        $("#btnNIT").attr('disabled', true);
                        $("#nit").val(val.nit).attr('disabled', true);
                        $("#idCotizacion").val(idCotizacion);
                        $("#fechaCotizacion").val(val.fechaCotizacion);
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
                    loadProductosCotizacion(idCotizacion, action);
                }
            }, 'json');
            break;
        case 'get':
            if ($("#noCotizacion").val() != '') {
                alert("ya existe un pedido en curso");
                cancelarPedido(1);
            } else {
                params = {
                    service: 'getCotizacion',
                    idCotizacion: idCotizacion,
                    action: action
                };
                $("#loader").show();
                $.post('controllers/cajaController.php', params, function (data) {
                    if (data === null) {
                        $("#loader").hide();
                        alert('Cotizacion ya gestionada o no existente en el sistema');
                    } else {
                        var idCotizacion = "";
                        $.each(data, function (key, val) {
                            idCotizacion = val.id;
                            $("#nit").val(val.nit);
                            $("#idCotizacion").val(val.id);
                            $("#noCotizacion").val(val.noCotizacion);
                            $("#nombre").val(val.nombre);
                            $("#telefono").val(val.telefono);
                            $("#direccion").val(val.direccion);
                            $("#observaciones").val(val.observaciones);
                            $("#vendedores option").each(function () {
                                if ($(this).val() === val.idUsuarios) {
                                    $(this).prop('selected', true);
                                }
                            });
                            $("#tipoVenta option").each(function () {
                                if ($(this).val() === val.idTipoVenta) {
                                    $(this).prop('selected', true);
                                }
                            });
                        });
                        loadProductosCotizacion(idCotizacion, action);
                    }
                }, 'json');
            }
            break;
    }
}
//
function actualizarCotizacion() {
    var fechaCotizacion = $("#fechaCotizacion").val();
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
    if (!fechaCotizacion) {
        flag = false;
        errorMsg += 'Ingrese fecha de cotizacion\n';
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
            service: 'actualizarCotizacion',
            fechaCotizacion: fechaCotizacion,
            nit: nit,
            nombre: nombre,
            telefono: telefono,
            direccion: direccion,
            total: total,
            observaciones: observaciones,
            tipoVenta: tipoVenta,
            idVendedores: idVendedores,
            idCotizacion: $("#idCotizacion").val()
        };
        $("#edit").attr({
            disabled: true
        });
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = pathJasper + "cotizaciones.php?idCotizacion=" + val.idCotizacion + "";
                    window.open(url);
                    loadConsultaCotizaciones();
                } else {
                    alert('Error al actualizar cotizacion, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function exportarReporteCotizaciones() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var estadoCotizacion = $("#estadoCotizacion").val();
    var cliente = $("#cliente").val();
    var idVendedores = $("#vendedores").val();
    var noCotizacion = $("#noCotizacion").val();
    params = {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        estadoCotizacion: estadoCotizacion,
        cliente: cliente,
        idVendedores: idVendedores,
        noCotizacion: noCotizacion,
    };
    var url = "views/reportes/cotizaciones-excel.php";
    $.redirect(url, params, 'post');
}
//
function agregarProductoCotizacion() {
    var idProducto = $("#idProducto").val();
    var tipoProducto = $("#tipoProducto").val();
    var precio = accounting.unformat($("#precioProducto").val());
    var cantidad = accounting.unformat($("#cantidadProducto").val());
    var total = precio * cantidad;
    var errorMsg = "";
    var errorMsgPC = "";
    var flag = true;
    if (!idProducto) {
        flag = false;
        errorMsg += 'Ingrese codigo del producto y oprima enter para consultarlo en el sistema\n';
    }
    if (!cantidad || cantidad === 0 || cantidad < 0) {
        flag = false;
        errorMsg += 'La cantidad ingresada no puede ser vacio, cero o menor a cero \n';
    }
    if (flag === false) {
        clear();
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'agregarProductoCotizacion',
            tipoProducto: tipoProducto,
            idProducto: idProducto,
            cantidad: cantidad,
            precio: precio,
            total: total,
            idCotizacion: $("#idCotizacion").val()
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadProductosCotizacion($("#idCotizacion").val(), 'edit');
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
            $("#loader").hide();
        });
    }
}
//
function generarCotizacion() {
    var fechaCotizacion = $("#fechaCotizacion").val();
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
    if (!fechaCotizacion) {
        flag = false;
        errorMsg += 'Ingrese fecha de cotizacion\n';
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
        errorMsg += 'Seleccione vendedor\n';
    }
    if (!tipoVenta) {
        flag = false;
        errorMsg += 'Seleccione tipo de venta\n';
    }
    if (numeroProductos === '0') {
        flag = false;
        errorMsg += 'No tiene productos agregados a la cotizacion\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'generarCotizacion',
            fechaCotizacion: fechaCotizacion,
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
            idVendedores: idVendedores
        };
        $("#btnImprimir").attr({
            disabled: true
        });
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = pathJasper + "cotizaciones.php?idCotizacion=" + val.idCotizacion + "";
                    window.open(url);
                    loadModuloCotizaciones();
                } else {
                    console.log(val.error);
                    console.log(val.query);
                    alert('Error al generar cotizacion, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}