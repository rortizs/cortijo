var pathJasper = "views/jasper/";
var today;
var todayUser;
var time;
var fondoCorte;
var totalEfectivo = 0;
var totalTJ = 0;
var diferencia = 0;
var totalCorte = 0;
var ingresoPago = 0;
var contador = 0;
var contadorE = 4;
var contadorA = 0;
var abono = 0;
var facturas = null;
var idCotizaciones = null;
//
function recalcularTotalCorte() {
    var efectivo   = accounting.unformat($("#totalEfectivo").val()) || 0;
    var dolares    = accounting.unformat($("#totalEfectivoDolares").val()) || 0;
    var tj         = accounting.unformat($("#totalesTJ").val()) || 0;
    var exenciones = accounting.unformat($("#totalExencion").val()) || 0;
    var cheques    = accounting.unformat($("#totalCheques").val()) || 0;
    var recibos    = accounting.unformat($("#totalRecibos").val()) || 0;
    var vales      = accounting.unformat($("#valesCorte").val()) || 0;
    var fondo      = fondoCorte || 0;
    var total      = efectivo + dolares + tj + exenciones + cheques + recibos - vales - fondo;
    $("#totalCorte").val(accounting.formatNumber(total, 2));
}
function recalcularDiferencia() {
    var ventasContado = accounting.unformat($("#totalVentasContado").val()) || 0;
    var corte         = accounting.unformat($("#totalCorte").val()) || 0;
    $("#diferencia").val(accounting.formatNumber(ventasContado - corte, 2));
}
//
$(document).on("keypress", ".facturacion", function (e) {
    if (e.keyCode == 13 || event.keyCode == 9) {
        var nextElement = $('[tabindex="' + (this.tabIndex + 1) + '"]');
        if (nextElement.length) {
            nextElement.focus();
        } else {
            $('[tabindex="1"]').focus();
        }
    }
});
//
$(document).on("keypress", ".pedidos", function (e) {
    if (e.keyCode == 13 || event.keyCode == 9) {
        var nextElement = $('[tabindex="' + (this.tabIndex + 1) + '"]');
        if (nextElement.length) {
            nextElement.focus();
        } else {
            $('[tabindex="1"]').focus();
        }
    }
});
//
$(document).on("keypress", ".corteCaja", function (e) {
    if (e.keyCode == 13) {
        var nextElement = $('[tabindex="' + (this.tabIndex + 1) + '"]');
        if (nextElement.length) {
            nextElement.focus();
        } else {
            $('[tabindex="1"]').focus();
        }
    }
});
//
$(document).ready(function () {
    //loadModuloPedidos();
    //loadModuloRecibos();
    loadModulo();
    today = moment().format('YYYY-MM-DD');
    todayUser = moment().format('DD-MM-YYYY');
    time = moment().format('H:mm:ss');
    //OpenLiquidarVenta();
    $("#loader").show();
});
//
function loadModulo() {
    switch ($("#idRoles").val()) {
        case '3':
            aperturaCaja();
            break;
        default:
            if ($("#dbProject").val() === 'erp_gsp') {
                loadModuloCotizaciones();
            } else {
                loadModuloPedidos();
            }
            break;
    }
}
//
function aperturaCaja() {
    $("#page-container").html('');
    params = {
        service: 'fondoCaja'
    };
    $.post('controllers/cajaController.php', params, function (data) {
        if (data === null) {
            $(".nav").hide();
            openFondoCaja();
        } else {
            fondoCorte = data[0]['monto'];
            loadModuloFacturacion();
        }
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function openFondoCaja() {
    $("#modal1").modal('show');
    $("#myModalLabel").html('Apertura de Caja');
    $.post('views/caja/fondoCaja.php', function (respuesta) {
        $("#controllers").html(respuesta);
        $('#modal1').on('shown.bs.modal', function () {
            $("#montoF").focus();
        });
    }).done(function () {
        $('#montoF').on('keydown', function (e) {
            if (e.which == 13) {
                $("#tasaCambioF").focus();
            }
        });
    });
}
//
function saveFondoCaja() {
    var monto = $("#montoF").val();
    var tasaCambio = $("#tasaCambioF").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de apertura de caja\n';
    }
    if (!tasaCambio) {
        flag = false;
        errorMsg += 'Ingrese tasa de cambio\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'saveFondoCaja',
            monto: accounting.unformat(monto),
            tasaCambio: tasaCambio
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadModuloFacturacion();
                    $("#modal1").modal('hide');
                    $("#monto").val('');
                    fondoCorte = accounting.unformat(monto);
                    //var url = pathJasper + "reciboFondo-pdf.php?idFondo=" + val.idFondo + "&print=1";
                    //window.open(url);
                } else {
                    alert('Error al generar apertura de caja');
                }
            });
        }, 'json');
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
        loadVendedores();
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
        //FORMATEO DE NIT
        $("#nit").blur(function () {
            if (!isNaN($("#nit").val().substr(0, 1))) {
                if ($("#nit").val().search('-') === -1) {
                    var nit = $("#nit").val().substr(0, ($("#nit").val().length - 1)) + "-" + $("#nit").val().substr(($("#nit").val().length - 1), 1);
                    $("#nit").val(nit.trim().replace(' ', '').toUpperCase());
                }
            } else {
                var nit = $("#nit").val();
                $("#nit").val(nit);
            }
        });
    });
    cancelarCotizacion(0);
}
// 
function loadModuloPedidos(idPedido, action) {
    $.post('views/caja/pedidos.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $('#fechaPedido').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).attr('readonly', true);
        $(".nav").show();
        $("#camposCredito").hide();
        if (idPedido === undefined) {
            loadDocumentos('pedidos');
        }
        cancelarPedido(1);
        loadProductosPedido();
        loadVendedores();
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
        $("#loader").hide();
        //FORMATEO DE NIT
        $("#nit").blur(function () {
            if (!isNaN($("#nit").val().substr(0, 1))) {
                if ($("#nit").val().search('-') === -1) {
                    var nit = $("#nit").val().substr(0, ($("#nit").val().length - 1)) + "-" + $("#nit").val().substr(($("#nit").val().length - 1), 1);
                    $("#nit").val(nit.trim().replace(' ', '').toUpperCase());
                }
            } else {
                var nit = $("#nit").val();
                $("#nit").val(nit);
            }
        });
    });
    cancelarPedido(0);
    idCotizaciones = [];
}
//
function loadTomaMedidas() {
    $.post('views/caja/tomaMedidas.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $('#fechaPedido').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).attr('readonly', true);
        $(".nav").show();
    });
}
//

function loadModuloFacturacion() {
    var page = "views/caja/facturacion.php";
    $.post(page, function (respuesta) {
        $("#page-container").html(respuesta);
        $('#fechaFactura').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        //.attr('readonly', true);
        $(".nav").show();
        $("#camposCredito").hide();
        loadDocumentos('facturacion');
        loadProductosVenta();
        loadVendedores('comercializacion');
        getTipoCambio();
        $("#opcion").html('Facturación');
    }).done(function () {
        $("#nit").focus();
        $("#loader").hide();
        loadCamposHidden();
        if(dbProject==='erp_constructorajimenez'){
        	$(".orden").show();
        }else{
        	$(".orden").hide();
        }
    });
    cancelarVenta(0);
}
//
function camposFormaPago(flag) {
    var formaPago = $("#formaPago").val();
    if (formaPago == 0) {
        $("#divEfectivo").hide();
        $("#divCambio").hide();
        $("#divValorVoucher").hide();
        $("#divEmisores").hide();
        $("#divNoAutorizacion").hide();
        $("#divTasaCambio").hide();
        $("#divTotalDolares").hide();
    }
    if (formaPago == 1) {
        $("#divEfectivo").show();
        $("#divValorVoucher").hide();
        $("#divEmisores").hide();
        $("#divNoAutorizacion").hide();
        $("#divTasaCambio").hide();
        $("#divTotalDolares").hide();
        $("#efectivo").focus().val('');
        if (flag === undefined) {
            $("#cambio").val('0');
            $("#txtCambio").html('0.00');
        }
    } else if (formaPago == 2) {
        $("#divEfectivo").hide();
        $("#divValorVoucher").show();
        $("#divEmisores").show();
        $("#divNoAutorizacion").show();
        $("#divTasaCambio").hide();
        $("#divTotalDolares").hide();
        $("#valorVoucher").focus().val('');
        if (flag === undefined) {
            $("#cambio").val('0');
            $("#txtCambio").html('0.00');
        }
    } else if (formaPago == 3) {
        $("#divEfectivo").show();
        $("#divValorVoucher").show();
        $("#divEmisores").show();
        $("#divNoAutorizacion").show();
        $("#divTasaCambio").hide();
        $("#divTotalDolares").hide();
        $("#efectivo").focus().val('');
    } else if (formaPago == 4) {
        $("#divEfectivo").show();
        $("#divValorVoucher").hide();
        $("#divEmisores").hide();
        $("#divNoAutorizacion").hide();
        $("#divTasaCambio").show();
        $("#divTotalDolares").show();
        var totalDolares = parseFloat($("#total").val()) / parseFloat($("#tasaCambio").val());
        $("#totalDolares").val(totalDolares.toFixed(2));
        if (flag === undefined) {
            $("#efectivo").focus().val('');
            $("#cambio").val('0');
            $("#txtCambio").html('0.00');
        }
    }
}
//
function loadCamposHidden() {
    switch ($("#tipoVenta").val()) {
        case '2':
            $("#cuotas").attr('readonly', false);
            $("#diasCredito").attr('readonly', false);
            $("#cuotas").on('keydown', function (e) {
                if (e.which == 13) {
                    var total = accounting.unformat($("#total").val());
                    var cuotas = accounting.unformat($("#cuotas").val());
                    var montoCuotas = (total / cuotas);
                    $("#montoCuotas").val(accounting.formatNumber(montoCuotas, 2));
                }
            });
            break;
        case '1':
            $("#cuotas").attr('readonly', true);
            $("#idPeriodoPago").attr('disabled', true);
            break;
    }
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
    var codigo = (codigo !== undefined ? codigo : $("#codigo").val());
    var descProducto = (descripcion !== undefined ? descripcion : $("#descProducto").val());
    var total = precio * cantidad;
    var errorMsg = "";
    var errorMsgPC = "";
    var flag = true;
    if (!cantidad || cantidad === 0 || cantidad < 0 && valExistencias !== 0) {
        flag = false;
        errorMsg += 'La cantidad ingresada no puede ser vacio, cero o menor a cero \n';
        errorMsg += 'Codigo: ' + codigo + '\n';
        errorMsg += 'Descripcion: ' + descripcion + '\n';
        errorMsg += 'Exitencia: ' + existencia + '\n';
        errorMsg += 'Cantidad solicitada: ' + cantidad + '\n';
    }
    if (tipoProducto !== 'Servicio' && tipoProducto !== 'Producto Fabricado' && cantidad > existencia && valExistencias !== 0) {
        flag = false;
        errorMsg += 'La cantidad ingresada es mayor que la existencia del producto\n';
        errorMsg += 'Codigo: ' + codigo + '\n';
        errorMsg += 'Descripcion: ' + descripcion + '\n';
        errorMsg += 'Exitencia: ' + existencia + '\n';
        errorMsg += 'Cantidad solicitada: ' + cantidad + '\n';
    }
    if (flag === false) {
        clear();
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
            valExistencias: valExistencias,
            sku: codigo,
            descProducto: descProducto
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadProductosPedido($("#idPedido").val(), action);
                    clear();
                } else {
                    if (val.action == 'false') {
                        errorMsg = 'No hay existencias de componentes del producto: ' + codigo + ' ' + descripcion + '\n';
                        errorMsgPC += 'Producto: ' + val.producto + '\n';
                        errorMsgPC += 'Exitencia: ' + val.existencia + '\n';
                        errorMsgPC += 'Cantidad solicitada: ' + val.cantidad + '\n';
                    }
                }
            });
        }, 'json').done(function () {
            $("#loader").hide();
            if (errorMsg !== '') {
                alert(errorMsg + errorMsgPC);
            }
            if (idCotizaciones.length > 0) {
                $("#codigo").attr('readonly', true);
                $("#search").attr('disabled', true);
                $("#precioProducto").attr('readonly', true);
                $("#cantidadProducto").attr('readonly', true);
            } else {
                $("#codigo").attr('readonly', false);
                $("#search").attr('disabled', false);
                $("#precioProducto").attr('readonly', false);
                $("#cantidadProducto").attr('readonly', false);
            }
        });
    }
}
//
function agregarProductoVenta(idProducto, tipoProducto, precioPublico, cantidad, existencia, codigo, action, descLarga) {
    var idProducto = (idProducto !== undefined ? idProducto : $("#idProducto").val());
    var tipoProducto = (tipoProducto !== undefined ? tipoProducto : $("#tipoProducto").val());
    var precio = accounting.unformat((precioPublico !== undefined ? precioPublico : $("#precioProducto").val()));
    var cantidad = accounting.unformat((cantidad !== undefined ? cantidad : $("#cantidad").val()));
    var existencia = accounting.unformat((existencia !== undefined ? existencia : $("#existencia").val()));
    var codigo = (codigo !== undefined ? codigo : $("#codigo").val());
    var valExistencias = accounting.unformat($("#valExistencias").val());
    var total = precio * cantidad;
    var tipoCambio = accounting.unformat($("#tasaCambio").val());
    var precioDolares = accounting.unformat((precio / tipoCambio), 2);
    var totalDolares = accounting.unformat((total / tipoCambio), 2);
    var costo = accounting.unformat($("#costoProducto").val());
    var descProducto = (descLarga !== undefined ? descLarga : $("#descProducto").val());
    var errorMsg = "";
    var errorMsgPC = "";
    var flag = true;
    if (tipoProducto !== 'Servicio' && tipoProducto !== 'Producto Fabricado' && cantidad > existencia & valExistencias === 1) {
        flag = false;
        errorMsg += `La cantidad ingresada es mayor que la existencia del producto\n
                     Producto: ${descLarga}\n
                     Cantidad: ${cantidad}\n
                     Existencia: ${existencia}`;
    }
    if (!cantidad) {
        flag = false;
        errorMsg += 'Ingrese cantidad de venta del producto\n';
    }
    if (cantidad === 0) {
        flag = false;
        errorMsg += 'La cantidad ingresada no puede ser vacio o cero \n';
    }

    if (precio < costo) {
        flag = false;
        errorMsg += `El precio de venta no puede ser menor a: ${$("#costoProducto").val()} \n`;
    }

    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'agregarProductoVenta',
            tipoProducto: tipoProducto,
            idProducto: idProducto,
            codigo: codigo,
            cantidad: cantidad,
            precio: precio,
            total: total,
            valExistencias: valExistencias,
            action: action,
            precioDolares: precioDolares,
            totalDolares: totalDolares,
            descProducto: descProducto
        };
        //return false;
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadProductosVenta();
                    // reset input
                    clear();
//                    if (action === undefined) {
//                        busqueda('inventario', 'Consulta de Productos en Inventario', 'FAC');
//                    } else {
//                        
//                    }
                    $("#modal1").modal('hide');
                } else {
                    if (val.action == 'false') {
                        errorMsg = 'No hay existencias de componentes del producto seleccionado\n';
                        errorMsg += 'Producto: ' + val.producto + '\n';
                        errorMsg += 'Componente: ' + val.componente + '\n';
                        errorMsg += 'Exitencia: ' + val.existencia + '\n';
                        errorMsg += 'Cantidad solicitada: ' + val.cantidad + '\n';
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
function eliminarProductoVenta(item, idProducto) {
    bootbox.confirm('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> ¿Desea eliminar este producto?</div>', function (respuesta) {
        if (respuesta) {
            params = {
                service: 'eliminarProductoVenta',
                idProducto: idProducto,
                item: item
            };
            $.post('controllers/cajaController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        loadProductosVenta();
                        $("#codigo").focus();
                    } else {
                        alert('Error al eliminar producto en venta');
                    }
                });
            }, 'json');
        } else {

        }
    });
}
//
function eliminarProductoPedido(item) {
    $("#loader").show();
    params = {
        service: 'eliminarProductoPedido',
        item: item
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                loadProductosPedido($("#idPedido").val(), 'edit');
                $("#codigo").focus();
            } else {
                alert('Error al eliminar producto en venta');
            }
        });
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function loadProductosVenta() {
    params = {
        service: 'getProductosVenta',
        descuentoP: accounting.unformat($("#descuentoP").val())
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $("#detalle").html('');
        var datos = "";
        var total = 0;
        var totalCosto = 0;
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='7' align='center'>0 productos encontrados</td>";
            datos += "</tr>";
            $("#numeroProductos").val(0);
        } else {
            $("#numeroProductos").val(data.length);
            $.each(data, function (key, val) {
                total += parseFloat(val.total);
                totalCosto += parseFloat(val.totalCosto);
                datos += "<tr>";
                datos += "<td>" + (key + 1) + "</td>";
                datos += "<td><button class='btn btn-xs btn-danger' onclick='eliminarProductoVenta(" + val.id + "," + val.idProductos + ");'><i class='fa fa-trash'></i></button></td>";
                datos += "<td>" + val.codigo + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += `<td align='right'>
                          <input type='hidden' class='form-control input-sm text-right' id='costo-${val.id}' value='${val.costo}'/>
                          <input type='text' class='form-control input-sm text-right' id='cantidad-${val.id}' value='${val.cantidad}' onKeydown='javascript: if (event.keyCode == 13 || event.keyCode == 9) updateItemVenta(${val.id},${val.idProductos});'/>
                          </td>`;
                datos += "<td align='right'><input type='text' class='form-control input-sm text-right precio' id='precio-" + val.id + "' value='" + accounting.formatNumber(val.precio, 2) + "' onKeydown='javascript: if (event.keyCode == 13 || event.keyCode == 9) updateItemVenta(" + val.id + "," + val.idProductos + ");'/></td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.total, 2) + "</td>";

                datos += "</tr>";
            });
            var valorFactura = accounting.unformat(total);
            var iva = accounting.unformat((valorFactura / 1.12 * 0.12 * 100) / 100);
            var subTotal = accounting.unformat((valorFactura - iva));
            var tasaCambio = accounting.unformat($("#tasaCambio").val());
            var totalDolares = accounting.unformat((valorFactura / tasaCambio));
            //
            $("#subTotal").val(subTotal.toFixed(2));
            $("#txtSubTotal").html(accounting.formatNumber(subTotal, 2));
            $("#iva").val(iva.toFixed(2));
            $("#txtIva").html(accounting.formatNumber(iva, 2));
            $("#total").val(valorFactura.toFixed(2));
            $("#totalDetalle").val(valorFactura.toFixed(2));
            $("#txtTotal").html(accounting.formatNumber(valorFactura, 2));
            $("#totalDolares").val(totalDolares);
            $("#detalle").append(datos);
            $("#costo_de_venta").val(totalCosto);
            //
            if (editarPrecio === 0) {
                $(".precio").attr('readonly', true);
            } else {
                $(".precio").attr('readonly', false);
            }
        }
    }, 'json').done(function () {
    });
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
        //console.log(idPedido);
        if (idPedido !== '' && params.action === 'get') {
            $.each(data, function (key, val) {
                agregarProductoVenta(val.idProductos, val.tipoProducto, val.precio, val.cantidad, val.existencia, val.sku, params.action, val.descLarga);
                //console.log(val.idProductos, val.tipoProducto, val.precio, val.cantidad, null);
            });
        } else {
            $("#detalle").html('');
            var action = "";
            if (idCotizaciones.length > 0) {
                action = "disabled=''";
            }
            var datos = "";
            var total2 = 0;
            if (data === null) {
                datos += "<tr>";
                if (dbProject === 'pos_kasualcosmeticos') {
                    datos += "<td colspan='9' align='center'>0 productos encontrados</td>";
                } else {
                    datos += "<td colspan='7' align='center'>0 productos encontrados</td>";
                }
                datos += "</tr>";
                $("#numeroProductos").val(0);
            } else {
                $("#numeroProductos").val(data.length);
                $.each(data, function (key, val) {
                    total2 += accounting.unformat(val.total);
                    datos += "<tr>";
                    datos += "<td>" + (key + 1) + "</td>";
                    datos += "<td><button class='btn btn-xs btn-danger' " + action + " onclick='eliminarProductoPedido(" + val.id + ")'><i class='fa fa-trash'></i></button></td>";
                    datos += "<td>" + val.sku + "</td>";
                    datos += "<td>" + val.descLarga + "</td>";
                    if (dbProject === 'pos_kasualcosmeticos') {
                        datos += "<td>" + val.item + "</td>";
                        datos += "<td>" + val.marca + "</td>";
                    }
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
        //$("#modal1").modal('hide');
    });
}
//
function generarCambio() {
    var total = '';
    var efectivo = parseFloat($("#efectivo").val() === '' ? '0' : $("#efectivo").val());
    if (efectivo > 0) {
        if ($("#formaPago").val() == 1) {
            total = parseFloat($("#total").val().replace(',', ''));
        } else if ($("#formaPago").val() == 4) {
            total = parseFloat($("#totalDolares").val().replace(',', ''));
        }
        if (efectivo < total) {
            alert('Efectivo es menor al Total');
        } else {
            var cambio = '';
            if ($("#formaPago").val() == 1) {
                cambio = (parseFloat(efectivo) - parseFloat(total));
            } else if ($("#formaPago").val() == 4) {
                cambio = (parseFloat(efectivo) - parseFloat(total)) * parseFloat($("#tasaCambio").val());
            }
            $("#txtCambio").html(cambio.toFixed(2));
            $("#cambio").val(cambio.toFixed(2));
        }
    }

}
//
function generarDescuentoMonedaCaja() {
    var descuentoP = $("#descuentoP").val() === '' ? '0' : $("#descuentoP").val();
    if (descuentoP > 100) {
        alert('Descuento ingresado es mayor al descuento permitido');
    } else {
        var total = $("#totalDetalle").val();
        var moneda = parseFloat(total) * parseFloat(descuentoP) / 100;
        var total = parseFloat(total) - parseFloat(moneda);
        //
        $("#txtDescuento").html(moneda.toFixed(2));
        $("#descuentoM").val(moneda.toFixed(2));
        $("#descuentoMoneda").val(moneda.toFixed(2));
        $("#txtTotal").html(total.toFixed(2));
        $("#total").val(total.toFixed(2));
        //
        var totalDolares = parseFloat($("#total").val()) / parseFloat($("#tasaCambio").val());
        $("#totalDolares").val(totalDolares.toFixed(2));
        //
        //RECALCULO DE TOTALES
        var valorFactura = accounting.unformat(total);
        var iva = accounting.unformat((valorFactura / 1.12 * 0.12 * 100) / 100);
        var subTotal = accounting.unformat((valorFactura - iva));
        var tasaCambio = accounting.unformat($("#tasaCambio").val());
        var totalDolares = accounting.unformat((valorFactura / tasaCambio));
        //
        $("#subTotal").val(subTotal.toFixed(2));
        $("#txtSubTotal").html(accounting.formatNumber(subTotal, 2));
        $("#iva").val(iva.toFixed(2));
        $("#txtIva").html(accounting.formatNumber(iva, 2));
        $("#total").val(valorFactura.toFixed(2));
        //$("#totalDetalle").val(valorFactura.toFixed(2));
        $("#txtTotal").html(accounting.formatNumber(valorFactura, 2));
        $("#totalDolares").val(totalDolares);
        $("#detalle").append(datos);
        $("#costo_de_venta").val(totalCosto);
    }
}
//
function generarDescuentoMonedaCajaQuetzales() {
    var descuento = accounting.unformat($("#descuentoMoneda").val() === '' ? '0' : $("#descuentoMoneda").val());
    var totalFactura = accounting.unformat($("#totalDetalle").val());
    var total = accounting.unformat($("#total").val());
    if (descuento > total) {
        alert('Descuento ingresado es mayor al descuento permitido');
    } else {
        var porcentaje = ((descuento * 100) / total);
        total = (totalFactura - descuento);
        var iva = accounting.unformat((total / 1.12 * 0.12 * 100) / 100);
        var subTotal = accounting.unformat((total - iva));
        var tasaCambio = accounting.unformat($("#tasaCambio").val());
        var totalDolares = accounting.unformat((total / tasaCambio));
        //
        $("#subTotal").val(subTotal.toFixed(2));
        $("#txtSubTotal").html(accounting.formatNumber(subTotal, 2));
        $("#iva").val(iva.toFixed(2));
        $("#txtIva").html(accounting.formatNumber(iva, 2));
        $("#total").val(total.toFixed(2));
        $("#valorFactura").val(total.toFixed(2));
        $("#txtTotal").html(accounting.formatNumber(total, 2));
        $("#totalDolares").val(totalDolares);
        $("#txtDescuento").html(accounting.formatNumber(descuento, 2));
        $("#descuentoM").val(accounting.formatNumber(descuento, 2));
        $("#totalDolares").val(totalDolares.toFixed(2));
        $("#descuentoP").val(accounting.formatNumber(porcentaje, 3));
        var totalDolares = parseFloat($("#total").val()) / parseFloat($("#tasaCambio").val());
        generarCambio();
    }
}
//
function cerrarVenta() {
    $("#loader").show();
    var fechaFactura = $("#fechaFactura").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var correlativo = $("#correlativo").val();
    var idClientes = $("#idClientes").val();
    var nit = $("#nit").val().replace(/-/g, "");
    var nombre = $("#nombre").val();
    var direccion = $("#direccion").val();
    var vendedores = $("#vendedores").val();
    var tipoVenta = $('#tipoVenta').val();
    var subTotal = $("#subTotal").val();
    var descuentoM = $("#descuentoM").val();
    var descuentoP = $("#descuentoP").val();
    var total = $("#total").val();
    var totalPagado = $("#totalPagado").val();
    var cambio = $("#cambio").val();
    var tasaCambio = $("#tasaCambio").val();
    var idPedido = $("#idPedido").val();
    var anticipoP = $("#anticipoP").val();
    var saldoP = $("#saldoP").val();
    var observaciones = $("#observaciones").val();
    var boletos = $("#boletos").val();
    var totalDolares = (accounting.unformat(total) / accounting.unformat(tasaCambio));
    var numeroItemsFactura = accounting.unformat($("#numeroItemsFactura").val());
    var doc = $("#correlativo").val().split("-");
    var cuotas = accounting.unformat($("#cuotas").val());
    var montoCuotas = accounting.unformat($("#montoCuotas").val());
    var diasCredito = accounting.unformat($("#diasCredito").val());
    //
    var formaPago = null;
    var valor = null;
    var emisores = null;
    var auth = null;
    var detalle = null;
    if (tipoVenta === '1') {
        formaPago = [];
        valor = [];
        emisores = [];
        auth = [];
        detalle = [];
        $(".emisores").each(function (index) {
            emisores.push($(this).val());
        });
        $(".auth").each(function (index) {
            auth.push($(this).val());
        });
        $(".formaPago").each(function (index) {
            var arr = {};
            formaPago.push($(this).data("type"));
            valor.push($(this).val());
            arr['formaPago'] = formaPago[index];
            arr['valor'] = valor[index];
            arr['emisores'] = emisores[index];
            arr['auth'] = auth[index];
            detalle.push(arr);
        });
    }
    params = {
        service: 'cerrarVenta',
        fechaFactura: fechaFactura,
        tipoDocumento: tipoDocumento,
        correlativo: correlativo,
        idClientes: idClientes,
        nit: nit,
        nombre: nombre,
        direccion: direccion,
        vendedores: vendedores,
        tipoVenta: tipoVenta,
        subtotal: subTotal,
        iva: $("#iva").val(),
        descuentoM: accounting.unformat(descuentoM),
        descuentoP: accounting.unformat(descuentoP),
        total: accounting.unformat(total),
        costo_de_venta: $("#costo_de_venta").val(),
        totalPagado: totalPagado,
        cambio: cambio,
        tasaCambio: tasaCambio,
        detallePago: detalle,
        idPedido: idPedido,
        anticipo: anticipoP,
        saldo: saldoP,
        observaciones: observaciones,
        totalDolares: totalDolares,
        boletos: boletos,
        numeroItemsFactura: numeroItemsFactura,
        idFormato: accounting.unformat($("#idFormato").val()),
        doc: doc[0],
        dbProject: dbProject,
        tipoTransaccion: accounting.unformat($("#tipoTransaccion option:selected").val()),
        cuotas: cuotas,
        montoCuotas: montoCuotas,
        diasCredito: diasCredito,
        mail: $("#mail").val(),
        telefono: $("#telefono").val(),
        noOrden:$("#noOrden").val(),
        fechaOrden:$("#fechaOrden").val(),
        usuarioOrden:$("#usuarioOrden").val()
    };
    $("#btnImprimir").attr({
        disabled: true
    });
//    console.log(params);
//    return false;
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            switch (val.message) {
                case 'success':
                    switch (dbProject) {
                        case 'pos_doanbo':
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=normal";
                            window.open(url);
                            location.reload();
                            break;
                        case 'pos_fashiongt':
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=normal";
                            window.open(url);
                            location.reload();
                            break;
                        case 'erp_cubix':
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=normal";
                            window.open(url);
                            location.reload();
                            break;
                        case 'pos_completehydralicseals':
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta;
                            window.open(url);
                            location.reload();
                            break;
                        case 'pos_plastica':
                            var url = `FEL_PLASTICA.php?idVentas=${val.idVenta}`;
                            window.open(url);
                            setTimeout(function () {
                                location.reload();
                            }, 5000);
                            break;
                        case 'erp_planetabebe':
                            var url = `FEL_PB.php?idVentas=${val.idVenta}`;
                            window.open(url);
                            setTimeout(function () {
                                location.reload();
                            }, 5000);
                            break;
                        case 'erp_elohim':
                            if ($("#idSucursales").val() === '13') {
                                var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=ticket";
                                window.open(url);
                                location.reload();
                            } else {
                                var url = `FEL_ELOHIM.php?idVentas=${val.idVenta}`;
                                window.open(url);
                                setTimeout(function () {
                                    location.reload();
                                }, 5000);
                            }
                            break;
                        case 'pos_doanbo':
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta;
                            window.open(url);
                            location.reload();
                            break;
                        case 'erp_constructorajimenez':
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=normal";
                            window.open(url);
                            location.reload();
                            break;
                        case 'erp_elsiglo':
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=ticket";
                            window.open(url);
                            location.reload();
                            break;
                        case 'erp_inversionesyproyectos':
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=ticket";
                            window.open(url);
                            location.reload();
                            break;
                        case 'pos_alimelmar':
                            if (accounting.unformat($("#tipoTransaccion option:selected").val()) === 1) {
                                var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=ticket";
                                window.open(url);
                            } else {
                                var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=recibo";
                                window.open(url);
                            }
                            location.reload();
                            break;
                        case 'pos_ferrumen':
                            var url = "";
                            switch ($("#idEmpresas").val()) {
                                case '1':
                                    url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=ticket";
                                    break;
                                case '2':
                                    url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=cambiaria";
                                    break;
                                case '3':
                                    url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=cambiaria";
                                    break;
                            }
                            window.open(url);
                            location.reload();
                            break;
                        case 'erp_provegas':
                            if (val.serie.substring(0, 1) === 'F') {
                                var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=provegas1";
                            } else {
                                var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=provegas2";
                            }
                            window.open(url);
                            location.reload();
                            break;
                        default:
//                            if (params.tipoTransaccion === 1) {
//                                var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=normal";
//                                window.open(url);
//                            } else {
//                                var url = pathJasper + "envio.php?idVenta=" + val.idVenta + "";
//                                window.open(url);
//                            }
                            var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=normal";
                            window.open(url);
                            location.reload();
                            break;
                    }
                    break;
                default:
                    alert(`Error FEL: ${val.message}`);
                    $("#loader").hide();
                    break;
            }
        });
    }, 'json');
}
//
//
function cerrarVentaAgenciaViajes() {
    var fechaFactura = $("#fechaFactura").val();
    var tipoDocumento = $("#tipoDocumento").val();
    var correlativoPagare = $("#correlativoPagare").val();
    var idCorrelativoPagare = $("#idCorrelativoPagare").val();
    var correlativoFactura = $("#correlativoFactura").val();
    var idCorrelativoFactura = $("#idCorrelativoFactura").val();
    var idClientes = $("#idClientes").val();
    var idProveedor = $("#proveedores").val();
    var nit = $("#nit").val();
    var nombre = $("#nombre").val();
    var direccion = $("#direccion").val();
    var vendedores = $("#vendedores").val();
    var tipoVenta = $('#tipoVenta').val();
    var subTotal = $("#subTotal").val();
    var descuentoM = $("#descuentoM").val();
    var descuentoP = $("#descuentoP").val();
    var total = $("#total").val();
    var totalPagado = $("#totalPagado").val();
    var cambio = $("#cambio").val();
    var tasaCambio = $("#tasaCambio").val();
    var idPedido = $("#idPedido").val();
    var anticipoP = $("#anticipoP").val();
    var saldoP = $("#saldoP").val();
    var observaciones = $("#observaciones").val();
    var boletos = $("#boletos").val();
    var fee = $("#fee").val();
    var totalImpuestos = $("#impuestoHidden").val();
    var totalDolares = (accounting.unformat(total) / accounting.unformat(tasaCambio));
    var otrosCargos = $("#otrosCargosHidden").val();
    var motivo = $("#motivo").val();
    var tipoImpresion = $("#tipoImpresion").val();
    var iva = $("#iva").val();
    var factura = $("#tipoFacturacion").val();
    var anticipo = 0
    //
    var formaPago = null;
    var valor = null;
    var emisores = null;
    var auth = null;
    var detalle = null;
    if (tipoVenta === '1') {
        formaPago = [];
        valor = [];
        emisores = [];
        auth = [];
        detalle = [];
        $(".emisores").each(function (index) {
            emisores.push($(this).val());
        });
        $(".auth").each(function (index) {
            auth.push($(this).val());
        });
        $(".formaPago").each(function (index) {
            var arr = {};
            formaPago.push($(this).data("type"));
            valor.push($(this).val());
            arr['formaPago'] = formaPago[index];
            arr['valor'] = valor[index];
            arr['emisores'] = emisores[index];
            arr['auth'] = auth[index];
            detalle.push(arr);
        });
    }
    params = {
        service: 'cerrarVentaAgenciaViajes',
        fechaFactura: fechaFactura,
        tipoDocumento: tipoDocumento,
        correlativoPagare: correlativoPagare,
        idCorrelativoPagare: idCorrelativoPagare,
        correlativoFactura: correlativoFactura,
        idCorrelativoFactura: idCorrelativoFactura,
        idClientes: idClientes,
        idProveedor: idProveedor,
        nit: nit,
        nombre: nombre,
        direccion: direccion,
        vendedores: vendedores,
        tipoVenta: tipoVenta,
        subTotal: subTotal,
        descuentoM: descuentoM,
        descuentoP: descuentoP,
        total: total,
        totalPagado: totalPagado,
        cambio: cambio,
        tasaCambio: tasaCambio,
        detallePago: detalle,
        idPedido: idPedido,
        anticipo: anticipoP,
        saldo: saldoP,
        observaciones: observaciones,
        totalDolares: totalDolares,
        boletos: boletos,
        fee: fee,
        totalImpuestos: totalImpuestos,
        motivo: motivo,
        otrosCargos: otrosCargos,
        tipoImpresion: tipoImpresion,
        iva: iva,
        tipoFacturacion: factura
    };
    $("#btnImprimir").attr({
        disabled: true
    });
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            switch (val.message) {
                case 'success':
                    if ($("#availablePrint").val() === 'Si') {
                        switch (tipoImpresion) {
                            case '1':
                                var url = pathJasper + "pagareLax.php?idVenta=" + val.idVenta + "";
                                window.open(url);
                                alert('Venta procesada exitosamente');
                                location.reload();
                                break;
                            case '2':
                                var url = pathJasper + "facturaLax.php?idVenta=" + val.idVenta + "&factura=" + factura + "";
                                window.open(url);
                                alert('Venta procesada exitosamente');
                                location.reload();
                                break;
                            case '3':
                                var url = pathJasper + "pagareLax.php?idVenta=" + val.idVenta + "";
                                window.open(url);
                                var url = pathJasper + "facturaLax.php?idVenta=" + val.idVenta + "&factura=" + factura + "";
                                window.open(url);
                                alert('Venta procesada exitosamente');
                                location.reload();
                                break;
                        }
                    } else {
                        alert('Venta procesada exitosamente');
                        location.reload();
                    }
                    break;
                case 'docExists':
                    alert('Correlativo de documento ya existe en el sistema');
                    break;
                default:
                    alert('Error cerrar venta');
                    break;
            }
        });
    }, 'json');
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
    var fechaEntrega = $("#fechaEntrega").val();
    var horaEntrega = $("#horaEntrega").val();
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
    if ($("#dbProject").val() === 'erp_elsiglo') {
        if (!fechaEntrega) {
            flag = false;
            errorMsg += 'Ingrese Fecha de Entrega\n';
        }
        if (!horaEntrega) {
            flag = false;
            errorMsg += 'Ingrese Hora de Entrega\n';
        }
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        $("#add").attr('disabled', true);
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
            idCotizaciones: idCotizaciones,
            fechaEntrega: fechaEntrega,
            horaEntrega: horaEntrega
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    switch (dbProject) {
                        default:
                            var url = pathJasper + "pedidos.php?idPedido=" + val.idPedido + "";
                            window.open(url);
                            break;
                    }
                    loadModuloPedidos();
                } else {
                    console.log(val.error);
                    console.log(val.query);
                    alert('Error al generar pedido, comuniquese con el administrador del sistema');
                }
            });
        }, 'json').done(function () {
            $("#loader").hide();
        });
    }
}
//
function examenesPedido(idPedido) {
    params = {
        service: 'examenesPedido',
        idPedido: idPedido
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            var url = pathJasper + "examenesPedidos.php?idPedido=" + idPedido + "&idFamiliaNivel1=" + val.idFamiliaNivel1 + "";
            window.open(url);
        });
    }, 'json');
}
//
function getTipoCambio() {
    params = {
        service: 'getTipoCambio'
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            $("#tasaCambio").val(val.tasaCambio);
        });
    }, 'json');
}
//
function loadConsultaFacturas() {
    $.post('views/caja/consultaFacturas.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Facturas');
        loadCajeros();
        loadVendedores();
        switch (dbProject) {
            case 'erp_gsp':
                $(".btn-reImprimirEnvio").show();
                $(".btn-reImprimirGarantia").show();
                break;
            default :
                $(".btn-reImprimirEnvio").hide();
                $(".btn-reImprimirGarantia").hide();
                break;
        }
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
    });
}
//
function loadConsultaPedidos() {
    $.post('views/caja/consultaPedidos.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Pedidos');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadCajeros();
        loadVendedores();
        switch (dbProject) {
            case 'erp_gsp':
                switch (idRoles) {
                    case '2':
                        $("#reImprimirPedidoMercaderia").show();
                        $("#confirmarPedido").show();
                        $("#reImprimirExamenesPedido").hide();
                        break;
                    case '19':
                        $("#reImprimirPedidoMercaderia").show();
                        $("#confirmarPedido").show();
                        $("#reImprimirExamenesPedido").hide();
                        break;
                    default :
                        $("#reImprimirPedidoMercaderia").show();
                        $("#confirmarPedido").hide();
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
                $("#confirmarPedido").show();
                $("#reImprimirExamenesPedido").hide();
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
function consultarFacturas() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var tipoVenta = $("#tipoVentaCF").val();
    var estatus = $("#estatus").val();
    var serie = $("#serieFactura").val();
    var correlativo = $("#correlativoFactura").val();
    var cliente = $("#cliente").val();
    var idSucursales = $("#idSucursalesCF").val();
    var idVendedor = $("#vendedores").val();
    //
    params = {
        service: 'consultarFacturas',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        tipoVenta: tipoVenta,
        estatus: estatus,
        serie: serie,
        correlativo: correlativo,
        cliente: cliente,
        idSucursales: idSucursales,
        idVendedor: idVendedor
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle,#summary").html('');
        var datos = "";
        var summary = "";
        var total1 = 0;
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='15' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var total1 = 0;
            var total2 = 0;
            var total3 = 0;
            var total4 = 0;
            var total5 = 0;
            $.each(data, function (key, val) {
                var status = "";
                if (val.estatus === 'ACTIVA') {
                    status = "alert-success";
                } else {
                    status = "alert-danger";
                }
                total1 += accounting.unformat(val.anticipo);
                total2 += accounting.unformat(val.saldo);
                total3 += accounting.unformat(val.subtotal);
                total4 += accounting.unformat(val.iva);
                total5 += accounting.unformat(val.total);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='facturas' data-doc='" + val.documento + "' title='" + val.fechaFactura + "/" + val.statusFactura + "'  data-sucursales='" + val.idSucursales + "' value='" + val.idVenta + "'/></td>";
                datos += "<td>" + val.fechaFactura + "</td>";
                datos += "<td>" + val.tipoVenta + "</td>";
                datos += "<td>" + val.serie + "-" + val.correlativo + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.nombreCliente + "</td>";
                datos += "<td>" + val.sucursal + "</td>";
                datos += "<td>" + val.vendedor + "</td>";
                datos += "<td class='" + status + " text-center'>" + val.estatus + "</td>";
                datos += "<td>" + val.fechaAnulacion + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.anticipo, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.saldo, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.subtotal, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.iva, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.total, 'Q. ') + "</td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='10'>Total Facturacion:</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total1, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total2, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total3, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total4, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total5, 'Q. ') + "</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
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
        var total1 = 0;
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='13' class='text-center'>0 registros encontrados</td>";
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
                datos += `<td>
                          <select class="form-control input-sm" id="estado-${val.idPedido}" onchange="cambiarEstadoOrden(${val.idPedido});">`;
                if (val.estadoOrden === 'NO ENTREGADO') {
                    datos += `<option value='1' selected="">NO ENTREGADO</option>
                              <option value='2'>ENTREGADO</option>`;
                } else {
                    datos += `<option value='1'>NO ENTREGADO</option>
                              <option value='2' selected="">ENTREGADO</option>`;
                }
                datos += `</select>
                          </td>`;
                datos += "</tr>";
            });
            summary += "<tr>";
            summary += "<td colspan='11'>Total Pedidos:</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
            summary += "<td></td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
}
//
function reImprimirFactura() {
    var id;
    var idSucursales;
    var serie;
    $('.facturas').each(function () {
        if (this.checked) {
            id = $(this).val();
            idSucursales = $(this).data('sucursales');
            serie = $(this).data('doc').split('-');
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        switch (dbProject) {
            default :
                var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=normal";
                window.open(url);
                break;
            case 'erp_sanjose':
                var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=reciboVenta";
                window.open(url);
                break;    
            case 'erp_constructorajimenez':
                var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=normal";
                window.open(url);
                break;
            case 'pos_plastica':
                var url = `FEL_PLASTICA.php?idVentas=${id}`;
                window.open(url);
                break;
            case 'pos_completehydralicseals':
                var url = pathJasper + "formatoFactura.php?idVenta=" + id;
                window.open(url);
                break;
            case 'erp_planetabebe':
                var url = `FEL_PB.php?idVentas=${id}`;
                window.open(url);
                break;
            case 'erp_elohim':
                if ($("#idSucursales").val() === '13') {
                    var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=ticket";
                    window.open(url);
                    location.reload();
                } else {
                    var url = `FEL_ELOHIM.php?idVentas=${val.idVenta}`;
                    window.open(url);
                    setTimeout(function () {
                        location.reload();
                    }, 5000);
                }
                break;
            case 'erp_elsiglo':
                var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=ticket";
                window.open(url);
                break;
            case 'pos_ferrumen':
                var url = "";
                switch ($("#idEmpresas").val()) {
                    case '1':
                        url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=ticket";
                        break;
                    case '2':
                        url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=cambiaria";
                        break;
                    case '3':
                        url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=cambiaria";
                        break;
                }
                window.open(url);
                break;
            case 'pos_alimelmar':
                var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=ticket";
                window.open(url);
                break;
            case 'erp_inversionesyproyectos':
                var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=ticket";
                window.open(url);
                break;
            case 'erp_cubix':
                var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=normal";
                window.open(url);
                break;
            case 'erp_provegas':
                if (serie[0].substring(0, 1) === 'F') {
                    var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=provegas1";
                    window.open(url);
                } else {
                    var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=provegas2";
                    window.open(url);
                }
                break;
        }
    }
}
//
function reImprimirEnvio() {
    var id;
    $('.facturas').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "envio.php?idVenta=" + id + "";
        window.open(url);
    }
}
//
function reImprimirGarantia() {
    var id;
    $('.facturas').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "formatoGarantia.php?idVenta=" + id + "";
        window.open(url);
    }
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
                var url = pathJasper + "ticket_vitalab.php?idPedido=" + id + "";
                window.open(url);
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
function anularFactura() {
    var id;
    var status;
    var fecha;
    $('.facturas').each(function () {
        if (this.checked) {
            id = $(this).val();
            $.jStorage.set("idFactura", id);
            var datosF = $(this).attr('title').split('/');
            status = datosF[1];
            fecha = datosF[0];
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        //Validar que factura sea del dia actual y que no este anulada
        var errorMsg = "Factura no puede ser anulada por los siguiente motivos:\n";
        var flag = true;
        if (status === 'Anulada') {
            flag = false;
            errorMsg += 'Status Anulada\n';
        }
        if (flag === false) {
            alert(errorMsg);
            return false;
        } else {
            if ($("#anularFactura").val() === '1') {
                loadAnulacionFactura();
            } else {
                $.jStorage.deleteKey("idUsuarioAdmin");
                $.post('views/admin/loginAdmin.php', function (respuesta) {
                    $("#controllers").html(respuesta);
                    $("#action").val('anularFactura');
                }).done(function () {
                    $("#modal1").modal('show');
                    $("#myModalLabel").html('Login Supervisor');
                });
            }
        }
    }
}
//
function eliminarFactura() {
    var id;
    var status;
    var fecha;
    $('.facturas').each(function () {
        if (this.checked) {
            id = $(this).val();
            $.jStorage.set("idFactura", id);
            var datosF = $(this).attr('title').split('/');
            status = datosF[1];
            fecha = datosF[0];
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        //Validar que factura sea del dia actual y que no este anulada
        var errorMsg = "Registro no puede ser eliminado por los siguiente motivos:\n";
        var flag = true;
        if (status === 'Anulada') {
            flag = false;
            errorMsg += 'Status Anulada\n';
        }
        if (flag === false) {
            alert(errorMsg);
            return false;
        } else {
            $.jStorage.deleteKey("idUsuarioAdmin");
            $.post('views/admin/loginAdmin.php', function (respuesta) {
                $("#controllers").html(respuesta);
                $("#action").val('eliminarFactura');
            }).done(function () {
                $("#modal1").modal('show');
                $("#myModalLabel").html('Login Supervisor');
            });
        }
    }
}
//
function loginAdmin() {
    params = {
        service: 'loginAdmin',
        user: $("#userAdmin").val(),
        pwd: $("#pwdAdmin").val()
    };
    $.post('controllers/adminController.php', params, function (data) {
        if (data == undefined) {
            alert('Usuario o contraseña incorrectos, verifique sus accesos');
            $("#userAdmin").val('').focus();
            $("#pwdAdmin").val('');
        } else {
            $.each(data, function (key, val) {
                $.jStorage.set("idUsuarioAdmin", val.id);
                $("#modal1").modal('hide');
                switch ($("#action").val()) {
                    case 'eliminarFactura':
                        var r = confirm("¿Esta seguro de eliminar este registro?");
                        if (r == true) {
                            eliminar();
                        }
                        break;
                    case 'habilitarDescuento':
                        $("#descuentoMoneda").attr('readonly', false);
                        break;
                    default :
                        loadAnulacionFactura();
                        break;
                }
            });
        }
    }, 'json');
}
//
function loadAnulacionFactura() {
    if ($.jStorage.get("idUsuarioAdmin") === null) {
        $.jStorage.set("idUsuarioAdmin", $("#idUsuarios").val());
    }
    console.log($.jStorage.get("idUsuarioAdmin"));
    console.log($.jStorage.get("idFactura"));
    $.post('views/caja/anulacionFactura.php', function (respuesta) {
        $("#controllers").html(respuesta);
    }).done(function () {
        $("#modal1").modal('show');
        $("#myModalLabel").html('Anulación de Factura');
    });
}
//
function anular() {
    var motivoAnulacion = $("#motivoAnulacion").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!motivoAnulacion) {
        flag = false;
        errorMsg += 'Ingrese el motivo de la anulación\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'anulacionFactura',
            idFactura: $.jStorage.get("idFactura"),
            idAdminUser: $.jStorage.get("idUsuarioAdmin"),
            motivoAnulacion: motivoAnulacion
        };
        $.post('controllers/cajaController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message === 'success') {
                    alert('Anulación exitosa');
                    $("#modal1").modal('hide');
                    loadConsultaFacturas();
                } else {
                    alert('Error en Anulación');
                }
            });
        }, 'json').done(function () {
            $("#loader").hide();
        });
    }
}
//
function eliminar() {
    params = {
        service: 'eliminarFactura',
        idFactura: $.jStorage.get("idFactura")
    };
    $.post('controllers/cajaController.php', params, function (respuesta) {
        $.each(respuesta, function (key, val) {
            if (val.message === 'success') {
                alert('Registro eliminado exitosamente');
                loadConsultaFacturas();
            } else {
                alert('Error al eliminar registro, comuniquese con el administrador del sistema');
            }
        });
    }, 'json');
}
//
function openVale() {
    $.post('views/caja/vale.php', function (respuesta) {
        $("#controllers").html(respuesta);
    }).done(function () {
        $("#modal1").modal('show');
        $("#myModalLabel").html('Vale Caja');
    });
}
//
function saveVal() {
    var solicitadoPor = $("#solicitado").val();
    var monto = $("#monto").val();
    var observaciones = $("#motivoVale").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!solicitadoPor) {
        flag = false;
        errorMsg += 'Ingrese nombre del solicitante\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto del vale\n';
    }
    if (!$.isNumeric(monto)) {
        flag = false;
        errorMsg += 'Valor en monto incorrecto\n';
    }
    if (!observaciones) {
        flag = false;
        errorMsg += 'Ingrese motivo del vale\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'saveVale',
            solicitadoPor: solicitadoPor,
            monto: monto,
            observaciones: observaciones
        };
        $.post('controllers/cajaController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message === 'success') {
                    $("#modal1").modal('hide');
                    var url = pathJasper + "vales.php?id=" + val.idVale;
                    window.open(url);
                } else {
                    alert('Error al ingresar vale');
                }
            });
        }, 'json');
    }
}
//
function getTotalVales() {
    params = {
        service: 'getTotalVales',
        fechaCorte: $("#fechaCorte").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        var valesCorte = 0;
        var t = 0;
        if (data !== null) {
            valesCorte = accounting.unformat(data[0]['totalVales']);
            t = accounting.unformat($("#totalCorte").val()) + valesCorte;
        }
        $("#valesCorte").val(accounting.formatNumber(valesCorte, 2));
        $("#totalCorte").val(accounting.formatNumber(t, 2));
    }, 'json').done(function () {
        getTotalVenta();
    });
}
//
function openCorteCaja() {
    $.post('views/caja/corteCaja.php', function (respuesta) {
        $("#controllers").html(respuesta);
    }).done(function () {
        $("#modal1").modal('show');
        $("#myModalLabel").html('Corte Caja');
        $(".modal-dialog").addClass('modal-lg');
        //
        $("#fechaCorte").datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).attr({
            value: todayUser,
        });
        $("#horaInicio").val(time);
        $("#tasaCambio2").val($("#tasaCambio").val());
        $("#fondoCorte").val(fondoCorte);
        $("#totalCorte").val();
        //
        $(".monedas").keyup(function () {
            var monto = $(this).val();
            var value = $(this).attr('title');
            var total = parseFloat(monto) * parseFloat(value);
            if (isNaN(total)) {
                total = 0;
            }
            $("#result" + value).val(total.toFixed(2));
        });
        $('.totalesM,.monedas').on('keydown', function (e) {
            if (e.which == 13) {
                if (totalEfectivo !== 0) {
                    totalEfectivo = 0;
                }
                $(".totalesM").each(function () {
                    if ($(this).data("title") === 'dolares') {
                        var totalEfectivoDolares = parseFloat(parseFloat($(this).val() === '' ? '0' : $(this).val()) * parseFloat($("#tasaCambio").val()));
                        $("#totalEfectivoDolares").val(totalEfectivoDolares.toFixed(2));
                    } else {
                        totalEfectivo += parseFloat($(this).val() === '' ? '0' : $(this).val());
                        $("#totalEfectivo").val(totalEfectivo.toFixed(2));
                    }
                });
                //
                recalcularTotalCorte();
                recalcularDiferencia();
            }
        });
        //
        $('.totalesTJ, .totalesM').on('click', function (e) {
            $(this).val('');
        });
        $('.totalesTJ').on('keydown', function (e) {
            if (e.which == 13) {
                if (totalTJ !== 0) {
                    totalTJ = 0;
                }
                $(".totalesTJ").each(function () {
                    totalTJ += accounting.unformat($(this).val() === '' ? '0' : $(this).val());
                    $("#totalesTJ").val(accounting.formatNumber(totalTJ, 2));
                });
                recalcularTotalCorte();
                recalcularDiferencia();
            }
        });
        //
        getTotalVales();
        //
        $("#fechaCorte").on('change', function () {
            getTotalVales();
        });
    });
}
//
function getTotalVenta() {
    params = {
        service: 'getTotalVenta',
        fechaCorte: $("#fechaCorte").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        var totalVentasContado = 0;
        var totalVentasCredito = 0;
        if (data !== null) {
            $.each(data, function (key, val) {
                if (val.idTipoVenta === "1") {
                    totalVentasContado = accounting.unformat(val.efectivo === null ? '0' : val.efectivo);
                }
                if (val.idTipoVenta === "2") {
                    totalVentasCredito = accounting.unformat(val.efectivo === null ? '0' : val.efectivo);
                }
            });
        }
        var diferencia = accounting.unformat(totalVentasContado - accounting.unformat($("#totalCorte").val()));
        console.log(diferencia);
        $("#totalVentasContado").val(accounting.formatNumber(totalVentasContado, 2));
        $("#totalVentasCredito").val(accounting.formatNumber(totalVentasCredito, 2));
        $("#diferencia").val(accounting.formatNumber(diferencia, 2));
    }, 'json').done(function () {
        getTotalVentaTJ();
    });
}
//
function getTotalVentaTJ() {
    params = {
        service: 'getTotalVentaTJ',
        fechaCorte: $("#fechaCorte").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        var total1 = 0;
        var total2 = 0;
        var total3 = 0;
        if (data !== null) {
            $.each(data, function (key, val) {
                switch (val.idEmisores) {
                    case '1':
                        total1 = accounting.formatNumber(val.total, 2);
                        break;
                    case '2':
                        total2 = accounting.formatNumber(val.total, 2);
                        break;
                    case '3':
                        total3 = accounting.formatNumber(val.total, 2);
                        break;
                }
            });
        }
        $("#totalVisa").val(total1);
        $("#totalMC").val(total2);
        $("#totalAM").val(total3);
    }, 'json').done(function () {
        getTotalVentaExencion();
    });
}
//
function getTotalVentaExencion() {
    params = {
        service: 'getTotalVentaExencion',
        fechaCorte: $("#fechaCorte").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        var total = 0;
        if (data !== null) {
            $.each(data, function (key, val) {
                total = (accounting.unformat(val.total) || 0);
            });
        }
        $("#totalExencion").val(accounting.formatNumber(total, 2));
        recalcularTotalCorte();
        recalcularDiferencia();
    }, 'json').done(function () {
        getTotalVentaCheques();
    });
}
//
function getTotalVentaCheques() {
    params = {
        service: 'getTotalVentaCheques',
        fechaCorte: $("#fechaCorte").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        var total = 0;
        if (data !== null) {
            $.each(data, function (key, val) {
                total = (accounting.unformat(val.total) || 0);
            });
        }
        $("#totalCheques").val(accounting.formatNumber(total, 2));
        recalcularTotalCorte();
        recalcularDiferencia();
    }, 'json').done(function(){
    	getTotalRecibos();
    });
}
//
function getTotalRecibos(){
	params = {
        service: 'getTotalRecibos',
        fechaCorte: $("#fechaCorte").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        var total = 0;
        if (data !== null) {
            $.each(data, function (key, val) {
                total = (accounting.unformat(val.total) || 0);
            });
        }
        $("#totalRecibos").val(accounting.formatNumber(total, 2));
        recalcularTotalCorte();
        recalcularDiferencia();
    }, 'json');
}
//
function generarCorte() {
    var r = confirm("¿Esta seguro de procesar este corte?");
    if (r == true) {
        var cantidad = [];
        var descripcion = [];
        var detalle = [];
        $(".cantidad").each(function (index) {
            cantidad.push(accounting.unformat($(this).val()));
            descripcion.push($(this).attr('title'));
        });
        $(".totalesM").each(function (index) {
            var arr = {};
            arr['cantidad'] = accounting.unformat(cantidad[index]);
            arr['descripcion'] = descripcion[index];
            arr['total'] = accounting.unformat($(this).val());
            detalle.push(arr);
        });
        $(".totalesTJ").each(function (index) {
            var arr = {};
            arr['cantidad'] = 'N/A';
            arr['descripcion'] = $(this).attr('title');
            arr['total'] = accounting.unformat($(this).val());
            detalle.push(arr);
        });
        params = {
            service: 'generarCorte',
            fondoCaja: accounting.unformat($("#fondoCorte").val()),
            totalVales: accounting.unformat($("#valesCorte").val()),
            totalEfectivo: accounting.unformat($("#totalEfectivo").val()),
            totalEfectivoDolares: accounting.unformat($("#totalEfectivoDolares").val()),
            totalExenciones: accounting.unformat($("#totalExencion").val()),
            totalCheques: accounting.unformat($("#totalCheques").val()),
            totalVouchers: accounting.unformat($("#totalesTJ").val()),
            totalCorte: accounting.unformat($("#totalCorte").val()),
            totalVentasContado: accounting.unformat($("#totalVentasContado").val()),
            totalVentasCredito: accounting.unformat($("#totalVentasCredito").val()),
            diferencia: accounting.unformat($("#diferencia").val()),
            totalRecibos: accounting.unformat($("#totalRecibos").val()),
            detalle: detalle,
            fechaCorte: $("#fechaCorte").val()
        };
        //
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = "";
                    if (dbProject === 'erp_elsiglo') {
                        url = pathJasper + "corteCaja.php?idCorte=" + val.idCorte;
                    } else {
                        url = pathJasper + "cierreCaja-pdf.php?idCorte=" + val.idCorte;
                    }
                    window.open(url);
                    var r = confirm("Corte procesado exitosamente, ¿desea realizar el cierre de caja?");
                    if (r == true) {
                        openCierreCaja();
                    } else {
                        cancelarModal();
                    }
                } else {
                    alert('Error al generar corte');
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function openCierreCaja() {
    procesarCierre();
}
//
function procesarCierre() {
    params = {
        service: 'procesarCierre',
        fechaCorte: $("#fechaCorte").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                switch (dbProject) {
                    case 'pos_lolascloset':
                        var url = pathJasper + "cierreCaja.php";
                        window.open(url);
                        break;
                    default:
                        var url = pathJasper + "cierreCaja-pdf.php?print=1";
                        window.open(url);
                }
            } else {
                alert('Error al generar cierre');
            }
        });
    }, 'json').done(function () {
        location.reload();
    });
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
            if (estado !== 'Abierto' && estado !== 'Confirmado' && estado !== undefined) {
                bootbox.alert('<br/><div class="alert alert-danger" role="alert"> <strong>Alerta!</strong> Solo se pueden cancelar pedidos abiertos o confirmados</div>');
            } else {
                params = {
                    service: 'cancelarPedido',
                    idPedido: id,
                    estado: estado
                };
                $.post('controllers/cajaController.php', params, function (data) {
                    $.each(data, function (key, val) {
                        if (val.message === 'success') {
                            alert('Cancelacion de pedido realizada exitosamente');
                            if (id !== undefined) {
                                consultarPedidos();
                            } else {
                                loadModuloPedidos();
                            }
                        } else {
                            alert('Error al cancelar pedido, comuniquese con el administrador del sistema');
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
function cancelarVenta(id) {
    params = {
        service: 'cancelarVenta',
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message !== 'success') {
                if (id !== undefined) {
                    loadModuloFacturacion();
                }
            } else {

            }
        });
    }, 'json');
}
//
function OpenLiquidarVenta() {
    var fechaFactura = $("#fechaFactura").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var correlativo = $("#correlativo").val();
    var nit = $("#nit").val();
    var nombre = $("#nombre").val();
    var direccion = $("#direccion").val();
    var vendedores = $("#vendedores option:selected").val();
    var numeroProductos = $("#numeroProductos").val();
    var tipoVenta = $("#tipoVenta").val();
    var formaPago = $("#formaPago").val();
	//estas nuevas variables vas a servir para evaluar si el total es > 2499 y el nit es CF
  	var total1 = parseFloat($("#total").val()).toFixed(2);
  	var tipoTransaccion1 = $("#tipoTransaccion option:selected").text();
    //var disponibleCredito = parseFloat($("#disponibleCredito").val().replace(",", ""));
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    //VALIDACIONES  
    if (!fechaFactura) {
        flag = false;
        errorMsg += 'Ingrese fecha de facturación\n';
    }
    if (!tipoDocumento) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!nit) {
        flag = false;
        errorMsg += 'Ingrese NIT a facturar\n';
    }
    if (!nombre) {
        flag = false;
        errorMsg += 'Ingrese Nombre a facturar\n';
    }
    if (!direccion) {
        flag = false;
        errorMsg += 'Ingrese Direccion de facturación\n';
    }
	if (tipoTransaccion1 === "1" && nit === "CF" && total1 > 2499) {
    	flag = false;
    	errorMsg +=
      	"El total a facturar es mayor a Q 2,499.00, No puede facturar con CF, debe ingresar el NIT o DPI del Cliente.\n";
  	}
    if (!accounting.unformat($("#diasCredito").val()) > 30) {
        flag = false;
        errorMsg += 'Dias Credito no puede ser mayor a 30 dias\n';
    }
    if (!accounting.unformat($("#cuotas").val()) > 6) {
        flag = false;
        errorMsg += 'Numero maximo de cuotas para factura cambiaria son 6\n';
    }
    if (dbProject === 'erp_provegas' && !vendedores) {
        flag = false;
        errorMsg += 'Seleccione vendedor\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        if (tipoVenta === '2') {
            cerrarVenta();
        } else {
            $.post('views/caja/liquidarVenta.php', function (respuesta) {
                $("#controllers").html(respuesta);
            }).done(function () {
                $("#modal1").modal('show');
                $("#myModalLabel").html('Liquidar Venta');
                $(".modal-dialog").addClass('modal-md');
                var total = parseFloat($("#total").val()).toFixed(2);
                var tasaCambio = parseFloat($("#tasaCambio").val());
                var totalPagado = parseFloat($("#totalPagado").val()).toFixed(2);
                var dif = parseFloat(totalPagado - total).toFixed(2);
                if (tasaCambio == 0) {
                    totalDolares = '0.00';
                    dif2 = '0.00';
                } else {
                    var totalDolares = parseFloat(total / tasaCambio).toFixed(2);
                    var dif2 = parseFloat((totalPagado - total) / tasaCambio).toFixed(2);
                }

                var exencion = parseFloat(total - (total / 1.12)).toFixed(2);
                $("#cambio").val(dif);
                $("#total1").html(total);
                $("#total2").html(totalDolares);
                $("#dif").html('(' + dif + ')');
                $("#dif2").html('(' + dif2 + ')');
                switch (dbProject) {
                    case 'erp_laxTravelTopacio':
                        if ($("#efectivoHidden").val() != '0') {

                            $("#efectivo").val(parseFloat($("#efectivoHidden").val()).toFixed(2));
                        }

                        var noTarjetas = $("#tarjetaHidden").val().split("/");
                        var state = true;
                        $.each(noTarjetas, function (key, val) {
                            if (noTarjetas[1] != val && key > 0) {
                                state = false;
                            }
                        });
                        if (state == false) {
                            var tarjetas = $("#valTarjetaHidden").val().split("/");
                            $.each(tarjetas, function (key, val) {
                                if (key == 1) {
                                    var autorizacion = $("#noAutorizacionHidden").val().split("/");
                                    var nombreTarjeta = $("#nombreTarjetaHidden").val().split("/");
                                    $("#tarjeta").val(parseFloat(val).toFixed(2));
                                    $("#noAutorizacion").val(autorizacion[key]);
                                    $("#nombreTarjeta option").each(function () {
                                        if ($(this).val() === nombreTarjeta[key]) {
                                            $(this).prop('selected', true);
                                        }
                                    });
                                } else if (key > 1) {
                                    var autorizacion = $("#noAutorizacionHidden").val().split("/");
                                    var nombreTarjeta = $("#nombreTarjetaHidden").val().split("/");
                                    addItem(val, nombreTarjeta[key], autorizacion[key]);
                                }
                            });
                        } else if (state == true) {
                            console.log("as");
                            var totalF = 0;
                            tarjetas = $("#valTarjetaHidden").val().split("/");
                            $.each(tarjetas, function (key, val) {
                                totalF += parseFloat(val);

                            });
                            var autorizacion = $("#noAutorizacionHidden").val().split("/");
                            var nombreTarjeta = $("#nombreTarjetaHidden").val().split("/");
                            if (totalF != 0) {
                                $("#tarjeta").val(parseFloat(totalF).toFixed(2));
                            }
                            $("#noAutorizacion").val(autorizacion[1]);
                            $("#nombreTarjeta option").each(function () {
                                if ($(this).val() === nombreTarjeta[1]) {
                                    $(this).prop('selected', true);
                                }
                            });
                        }

                        break;
                    default:
                        break;
                }
                if (dif < 0) {
                    $("#btnImprimir").attr({
                        disabled: true
                    });
                }
                //
                $('.ingresoPago, .tarjeta' + contador + ', .emisor' + contador + ', .retencion').on('keydown', function (e) {
                    if (e.which == 13 || e.which == 9) {
                        if (ingresoPago !== 0) {
                            ingresoPago = 0;
                        }
                        var flag = true;
                        $(".ingresoPago").each(function () {
                            var tipo = $(this).attr("title");
                            switch (tipo) {
                                case 'dolares':
                                    ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val()) * tasaCambio;
                                    $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                    break;
                                case 'tarjeta':
                                    if ($(this).val() !== '') {
                                        if ($(this).data("id") == contador) {
                                            if ($('.emisor' + contador).val() === '') {
                                                alert('Seleccione emisor de tarjeta ' + (contador + 1));
                                                $('.emisor' + contador).focus().select();
                                                $('.emisor' + contador).on('change', function () {
                                                    $('.tarjeta' + contador).focus();
                                                });
                                                flag = false;
                                            } else if ($('.tarjeta' + contador).val() === '') {
                                                alert('Ingrese Número de Autorización Tarjeta ' + (contador + 1));
                                                $('.tarjeta' + contador).focus();
                                                flag = false;
                                            } else {
                                                ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                                                $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                                $(this).focus();
                                            }
                                        } else {
                                            ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                                            $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                            $(this).focus();
                                        }
                                    }

                                    break;
                                case 'auth':
                                    ingresoPago += 0;
                                    $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                    break;
                                case 'emisor':
                                    ingresoPago += 0;
                                    $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                    break;
                                case 'retencion':
                                    if ($(".retencion").is(":focus")) {
                                        if ($(".retencion").val() === '') {
                                            alert('Ingrese número de retención');
                                        } else {
                                            $(this).val(exencion);
                                            ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                                            $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                        }
                                    } else {
                                        ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                                        $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                    }
                                    break;
                                default:
                                    ingresoPago += accounting.unformat($(this).val());
                                    console.log(ingresoPago);
                                    $("#totalPagado").val(accounting.unformat(ingresoPago));
                                    break;
                            }
                        });
                        if (flag === false) {
                            return false;
                        } else {
                            $("#totalP1").html(parseFloat(ingresoPago).toFixed(2));

                            dif = parseFloat(parseFloat($("#totalPagado").val()) - total).toFixed(2);
                            if (tasaCambio == 0) {
                                dif2 = '0.00';
                                $("#totalP2").html('0.00');
                            } else {
                                dif2 = parseFloat((parseFloat($("#totalPagado").val()) - total) / tasaCambio).toFixed(2);
                                $("#totalP2").html(parseFloat(ingresoPago / tasaCambio).toFixed(2));
                            }
//
                            //console.log($("#totalPagado").val());
                            //console.log(total);
                            //
                            $("#cambio").val(dif);
                            $("#dif").html('(' + dif + ')');
                            $("#dif2").html('(' + dif2 + ')');
                            if (dif < 0) {
                                $("#btnImprimir").attr({
                                    disabled: true
                                });
                            } else {
                                $("#btnImprimir").attr({
                                    disabled: false
                                });
                            }
                        }
                    }
                });
            });
        }
    }
}
//
function addItem(valor, emision, autorizacion) {
    var cln = $('#liquidarVenta tr:last').clone();
    $("#liquidarVenta").append(cln);
    $("#liquidarVenta tr:last td .ingresoPago").val(parseFloat(valor).toFixed(2)).focus();
    $("#liquidarVenta tr:last td .emisores").removeClass('emisor' + contador);
    $("#liquidarVenta tr:last td .auth").removeClass('tarjeta' + contador);
    $("#liquidarVenta tr:last td .emisores option").each(function () {
        if ($(this).val() === emision) {
            $(this).prop('selected', true);
        }
        $("#liquidarVenta tr:last td .auth").val(autorizacion);
        contador += 1;
        $("#liquidarVenta tr:last td .ingresoPago").attr('data-id', contador);
        $("#liquidarVenta tr:last td .emisores").addClass('emisor' + contador);
        $("#liquidarVenta tr:last td .auth").addClass('tarjeta' + contador);

    });
    //
    if (ingresoPago !== 0) {
        ingresoPago = 0;
    }
    var total = parseFloat($("#total").val()).toFixed(2);
    var tasaCambio = parseFloat($("#tasaCambio").val());
    var totalDolares = parseFloat(total / tasaCambio).toFixed(2);
    var totalPagado = parseFloat($("#totalPagado").val()).toFixed(2);
    var dif = parseFloat(totalPagado - total).toFixed(2);
    var dif2 = parseFloat((totalPagado - total) / tasaCambio).toFixed(2);
    var exencion = parseFloat(total - (total / 1.12)).toFixed(2);
    $("#cambio").val(dif);
    $("#total1").html(total);
    $("#total2").html(totalDolares);
    $("#dif").html('(' + dif + ')');
    $("#dif2").html('(' + dif2 + ')');
    if (dif < 0) {
        $("#btnImprimir").attr({
            disabled: true
        });
    }
    //
    $('.ingresoPago, .tarjeta' + contador + ', .emisor' + contador + ', .retencion').on('keydown', function (e) {
        if (e.which == 13) {
            if (ingresoPago !== 0) {
                ingresoPago = 0;
            }
            var flag = true;
            $(".ingresoPago").each(function () {
                var tipo = $(this).attr("title");
                switch (tipo) {
                    case 'dolares':
                        ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val()) * tasaCambio;
                        $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                        break;
                    case 'tarjeta':
                        if ($(this).val() !== '') {
                            if ($(this).data("id") == contador) {
                                if ($('.emisor' + contador).val() === '') {
                                    alert('Seleccione emisor de tarjeta ' + (contador + 1));
                                    $('.emisor' + contador).focus().select();
                                    $('.emisor' + contador).on('change', function () {
                                        $('.tarjeta' + contador).focus();
                                    });
                                    flag = false;
                                } else if ($('.tarjeta' + contador).val() === '') {
                                    alert('Ingrese Número de Autorización Tarjeta ' + (contador + 1));
                                    $('.tarjeta' + contador).focus();
                                    flag = false;
                                } else {
                                    ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                                    $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                    $(this).focus();
                                }
                            } else {
                                ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                                $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                                $(this).focus();
                            }
                        }

                        break;
                    case 'auth':
                        ingresoPago += 0;
                        $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                        break;
                    case 'emisor':
                        ingresoPago += 0;
                        $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                        break;
                    case 'retencion':
                        if ($(".retencion").is(":focus")) {
                            if ($(".retencion").val() === '') {
                                alert('Ingrese número de retención');
                            } else {
                                $(this).val(exencion);
                                ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                                $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                            }
                        } else {
                            ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                            $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                        }
                        break;
                    default:
                        ingresoPago += parseFloat($(this).val() === '' ? '0' : $(this).val());
                        $("#totalPagado").val(parseFloat(ingresoPago).toFixed(2));
                        break;
                }
            });
            if (flag === false) {
                return false;
            } else {
                $("#totalP1").html(parseFloat(ingresoPago).toFixed(2));
                $("#totalP2").html(parseFloat(ingresoPago / tasaCambio).toFixed(2));
                dif = parseFloat(parseFloat($("#totalPagado").val()) - total).toFixed(2);
                dif2 = parseFloat((parseFloat($("#totalPagado").val()) - total) / tasaCambio).toFixed(2);
                $("#cambio").val(dif);
                $("#dif").html('(' + dif + ')');
                $("#dif2").html('(' + dif2 + ')');
                if (dif < 0) {
                    $("#btnImprimir").attr({
                        disabled: true
                    });
                } else {
                    $("#btnImprimir").attr({
                        disabled: false
                    });
                }
            }
        }
    });
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
            if ($("#noPedido").val() != '') {
                alert("ya existe un pedido en curso");
                cancelarPedido(1);
            } else {
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
                        $.each(data, function (key, val) {
                            idPedido = val.id;
                            $("#nit").val(val.nit);
                            $("#idClientes").val(val.idClientes);
                            $("#idPedido").val(idPedido);
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
                            $("#tipoVenta option").each(function () {
                                if ($(this).val() === val.idTipoVenta) {
                                    $(this).prop('selected', true);
                                }
                            });
                        });
                        loadProductosPedido(idPedido, action);
                    }
                }, 'json');
            }
            break;
    }
}
//
function loadConsultaExistencias() {
    $.post('views/caja/consultaExistencias.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Existencias');
    });
}
//
function busquedaExistencias() {
    $("#loader").show();
    params = {
        busqueda: $("#busqueda").val(),
        ingresoA: $("#ingresoA").val()
    };
    $.post('views/caja/consultaExistencias.php', params, function (respuesta) {
        $("#loader").hide(function () {
            $("#page-container").html(respuesta);
            $("#opcion").html('Consulta de Existencias');
        });
    }).done(function () {
        $('#ingresoA option').each(function () {
            if ($(this).val() === params.ingresoA) {
                $(this).attr("selected", "selected");
                console.log('selected');
            } else {
                console.log('no selected');
            }
        });
    });
}
//
function guardarTarjeta(noTarjeta, valor, emisor, noAutorizacion) {
    $("#tarjetaHidden").val($("#tarjetaHidden").val() + '/' + noTarjeta);
    $("#valTarjetaHidden").val($("#valTarjetaHidden").val() + '/' + valor);
    $("#nombreTarjetaHidden").val($("#nombreTarjetaHidden").val() + '/' + emisor);
    $("#noAutorizacionHidden").val($("#noAutorizacionHidden").val() + '/' + noAutorizacion);
}
//
function cargarFormaPagoBoleto(formaPago, cantidadTotal) {
    var formas = formaPago.split("/");
    var total = 0;
    var total2 = 0;
    var noTarjeta = "";
    switch (formas.length) {
        case 1:
            if (formas == 'CASH' || formas == 'MCASH') {
                total += parseFloat(cantidadTotal) + parseFloat($("#efectivoHidden").val());
                $("#efectivoHidden").val(parseFloat(total));
            }
            break;
        case 2:
            if (formas[1] == 'CASH' || formas[1] == 'MCASH') {
                total += parseFloat(cantidadTotal) + parseFloat($("#efectivoHidden").val());
                $("#efectivoHidden").val(parseFloat(total));
            } else if (formas[1] == 'CCVI' || formas[1] == 'MCCVI') {
                guardarTarjeta(noTarjeta, cantidadTotal, "1", "");

            } else if (formas[1] == 'CCCA' || formas[1] == 'MCCCA') {
                guardarTarjeta(noTarjeta, cantidadTotal, "2", "");

            } else if (formas[1] == 'CCAX' || formas[1] == 'MCCAX') {
                guardarTarjeta(noTarjeta, cantidadTotal, "3", "");
            }
            break;
        case 3:
            if (formas[0].substring(0, 4) == 'CCVI' || formas[0].substring(0, 5) == 'MCCVI') {
                noTarjeta = formas[0].substring(4, formas[0].length);
                guardarTarjeta(noTarjeta, cantidadTotal, "1", formas[2]);

            } else if (formas[0].substring(0, 4) == 'CCCA' || formas[0].substring(0, 5) == 'MCCCA') {
                noTarjeta = formas[0].substring(4, formas[0].length);
                guardarTarjeta(noTarjeta, cantidadTotal, "2", formas[2]);


            } else if (formas[0].substring(0, 4) == 'CCAX' || formas[0].substring(0, 5) == 'MCCAX') {
                noTarjeta = formas[0].substring(4, formas[0].length);
                guardarTarjeta(noTarjeta, cantidadTotal, "3", formas[2]);

            } else if (formas[0] == 'O' && formas[2] == 'CASH') {
                total += parseFloat(cantidadTotal) + parseFloat($("#efectivoHidden").val());
                $("#efectivoHidden").val(parseFloat(total));
            }
            break;
        case 4:
            if (formas[0].substring(0, 5) == 'CASH+') {

                if (formas[0].substring(5, 9) == 'CCVI') {
                    total += parseFloat(formas[2].substring(3, formas[2].length));
                    total2 += (parseFloat(cantidadTotal) - parseFloat(formas[2].substring(3, formas[2].length))) + parseFloat($("#efectivoHidden").val());
                    $("#efectivoHidden").val(total2);
                    noTarjeta = formas[0].substring(9, formas[0].length);
                    guardarTarjeta(noTarjeta, total, "1", formas[3]);


                } else if (formas[0].substring(5, 9) == 'CCCA') {
                    total += parseFloat(formas[2].substring(3, formas[2].length)) + parseFloat($("#tarjetaHidden").val());
                    total2 += (parseFloat(cantidadTotal) - parseFloat(formas[2].substring(3, formas[2].length))) + parseFloat($("#efectivoHidden").val());
                    $("#efectivoHidden").val(total2);
                    noTarjeta = formas[0].substring(9, formas[0].length);
                    guardarTarjeta(noTarjeta, total, "2", formas[3]);

                } else if (formas[0].substring(5, 9) == 'CCAX') {
                    total += parseFloat(formas[2].substring(3, formas[2].length)) + parseFloat($("#tarjetaHidden").val());
                    total2 += (parseFloat(cantidadTotal) - parseFloat(formas[2].substring(3, formas[2].length))) + parseFloat($("#efectivoHidden").val());
                    $("#efectivoHidden").val(total2);
                    noTarjeta = formas[0].substring(9, formas[0].length);
                    guardarTarjeta(noTarjeta, total, "3", formas[3]);
                }
            }
            break;
        case 5:
            if (formas[0] == 'O' && formas[2].substring(0, 4) == 'CCVI') {

                noTarjeta = formas[2].substring(4, formas[0].length);
                guardarTarjeta(noTarjeta, cantidadTotal, "1", formas[4]);

            } else if (formas[0] == 'O' && formas[2].substring(0, 4) == 'CCCA') {
                noTarjeta = formas[2].substring(4, formas[0].length);
                guardarTarjeta(noTarjeta, cantidadTotal, "2", formas[4]);

            } else if (formas[0] == 'O' && formas[2].substring(0, 4) == 'CCAX') {
                guardarTarjeta(noTarjeta, cantidadTotal, "3", formas[4]);

            }
            break;
    }
}
//
function cargarBoletosDetalle() {
    var datos;
    var totalImpuestos = 0;
    var montoSinImpuestos = 0;
    var cargosQ = 0;
    var cargosD = 0;
    var otrosCargos = 0;
    var iva = 0;
    params = {
        service: 'getDetalleBoleto'
    };
    $("#detalle").html('');
    $("#impuestoHidden").val('0.00');
    $("#txtImpuestos").html('Q. 0.00');
    $("#txtImpuestoslDE").html('$. 0.00');
    $("#subTotal").val('0.00')
    $("#txtSubTotal").html('Q. 0.00');
    $("#txtSubTotalDE").html('$. 0.00');
    $("#total").val('0.00');
    $("#txtTotal").html('Q. 0.00');
    $("#txtTotalDE").html('$. 0.00');
    $("#totalDolares").val('0.00');
    $("#txtCargos").html('Q. 0.00');
    $("#txtCargosDE").html('$. 0.00');
    $("#txtOtrosCargos").html('Q. 0.00');
    $("#txtOtrosCargosDE").html('$. 0.00');
    $("#efectivoHidden").val('0');
    $("#tarjetaHidden").val('0');
    $("#noAutorizacionHidden").val('0');
    $("#valTarjetaHidden").val('0');
    $("#nombreTarjetaHidden").val('0');
    $("#noAutorizacionHidden").val('0');

    $("#nit").val('');
    $("#idClientes").val('');
    $("#nombre").val('');
    $("#noReserva").val('');
    $("#formaPago").val('');
    $("#direccion").append('');
    $("#lineaArea").val('');
    $("#motivo").val('');
    $.post('controllers/agenciasViajesController.php', params, function (data) {
        if ($("#tipoFacturacion").val() == '1') {
            $("#motivo").val('CARGO POR SERVICIO\n');
        } else {
            $("#motivo").val('BOLETO AEREO\n');
        }

        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='10' align='center'>0 boletos encontrados</td>";
            datos += "</tr>";
            $("#numeroProductos").val(0);
        } else {

            $.each(data, function (key, val) {
                $("#nit").val(val.nitCliente);
                $("#idClientes").val(val.idCliente);
                $("#nombre").val(val.nombreCliente);
                $("#noReserva").val(val.reserva);
                $("#formaPago").val(val.formaPago);
                $("#direccion").val(val.direccionCliente);
                $("#lineaArea").val(val.codigoLineaAerea2 + '-' + val.lineaArea);
                $("#tasaCambio").val(val.tasaCambio);
                $("#itinerario").val(val.itinerario);

                $("#vendedores option").each(function () {
                    if ($(this).val() === val.codVendedor) {
                        $(this).prop('selected', true);
                    }
                });

                montoSinImpuestos += parseFloat(val.montoSinImpuestos);
                totalImpuestos += parseFloat(val.totalImpuestos);
                cargosQ += parseFloat(val.valorFee) * parseFloat($("#tasaCambio").val());
                cargosD += parseFloat(val.valorFee);
                iva += parseFloat(val.montoSinImpuestos) * 0.12;
                var cantidadTotal = parseFloat(val.montoSinImpuestos) + parseFloat(val.totalImpuestos) + (parseFloat(val.valorFee) * parseFloat($("#tasaCambio").val()));
                datos += "<tr>";
                datos += "<td>" + (key + 1) + "</td>";
                datos += "<td><button class='btn-item btn btn-xs btn-danger' onclick='eliminarBoletoDetalle(" + val.id + ")'><i class='fa fa-trash'></i></button></td>";
                datos += "<td>" + val.boleto + "</td>";
                datos += "<td>" + val.pasajero + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.montoSinImpuestos, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.totalImpuestos, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.tasaCambio, 5) + "</td>";
                datos += "<td align='right'><input type='text' class='form-control input-sm text-right' onkeypress='updateFeeDetalle(event," + val.id + "," + val.tasaCambio + "," + val.montoTotal + ")' id='fee" + val.id + "' value=" + val.valorFee + "></td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.total, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.total / val.tasaCambio, 2) + "</td>";
                datos += "</tr>";
                $("#motivo").val($("#motivo").val() + ' ' + val.codigoLineaAerea2 + ' ' + val.boleto + ' ' + val.pasajero + '\n');
                cargarFormaPagoBoleto(val.formaPago, cantidadTotal);
            });
            $("#impuestoHidden").val(totalImpuestos);
            $("#txtImpuestos").html('Q.' + accounting.formatNumber(totalImpuestos, 5));
            $("#txtImpuestoslDE").html('$.' + accounting.formatNumber((totalImpuestos / accounting.unformat($("#tasaCambioEmision").val())), 5));
            $("#subTotal").val(montoSinImpuestos)
            $("#txtSubTotal").html('Q.' + accounting.formatNumber(montoSinImpuestos, 5));
            $("#txtSubTotalDE").html('$.' + accounting.formatNumber((montoSinImpuestos / accounting.unformat($("#tasaCambioEmision").val())), 5));
            var total = accounting.unformat(montoSinImpuestos) + accounting.unformat(totalImpuestos);
            $("#total").val(accounting.unformat(total));
            $("#txtTotal").html('Q.' + accounting.formatNumber(total, 5));
            $("#txtTotalDE").html('$.' + accounting.formatNumber((total / accounting.unformat($("#tasaCambioEmision").val())), 5));
            var totalDolares = accounting.unformat($("#total").val()) / accounting.unformat($("#tasaCambio").val());
            $("#totalDolares").val(accounting.formatNumber(totalDolares, 5));
        }
        var porcentajeCargos = $("#otrosCargos").val() / 100;
        var total = parseFloat(montoSinImpuestos) + parseFloat(totalImpuestos) + parseFloat(cargosQ);
        otrosCargos = porcentajeCargos * total;
        var otrosCargosE = $("#otrosCargosD").val();
        var otrosCargosTotal = parseFloat(otrosCargos) + parseFloat(otrosCargosE);
        var otrosCargosTotalDE = otrosCargosTotal / $("#tasaCambio").val();
        total += parseFloat(otrosCargosTotal);

        $("#iva").val(iva);
        $("#fee").val(cargosQ);
        $("#txtCargos").html('Q.' + accounting.formatNumber(cargosQ, 2));
        $("#txtCargosDE").html('$.' + accounting.formatNumber(cargosD, 2));

        $("#impuestoHidden").val(totalImpuestos);
        $("#txtImpuestos").html('Q.' + accounting.formatNumber(totalImpuestos, 2));
        $("#txtImpuestoslDE").html('$.' + accounting.formatNumber((totalImpuestos / accounting.unformat($("#tasaCambio").val())), 2));

        $("#subTotal").val(montoSinImpuestos);
        $("#txtSubTotal").html('Q.' + accounting.formatNumber(montoSinImpuestos, 2));
        $("#txtSubTotalDE").html('$.' + accounting.formatNumber((montoSinImpuestos / accounting.unformat($("#tasaCambio").val())), 2));

        $("#otrosCargosHidden").val(otrosCargosTotal);
        $("#txtOtrosCargos").html('Q.' + accounting.formatNumber(otrosCargosTotal, 2));
        $("#txtOtrosCargosDE").html('$.' + accounting.formatNumber(otrosCargosTotalDE, 2));

        $("#total").val(accounting.unformat(total));
        $("#txtTotal").html('Q.' + accounting.formatNumber(total, 2));

        var totalDolares = (parseFloat(total) / parseFloat($("#tasaCambio").val()));
        $("#totalDolares").val(accounting.formatNumber(totalDolares, 2));
        $("#txtTotalDE").html('$.' + accounting.formatNumber(totalDolares, 2));
        $("#detalle").append(datos);
    }, 'json');

}
//
function getBoleto(noReserva) {
    $("#boletos").html('');
    if ($('#' + noReserva).prop('checked')) {
        $("#loader").show();
        params = {
            service: 'getBoleto',
            noReserva: noReserva
        };

        $.post('controllers/agenciasViajesController.php', params, function (data) {
            if (data === null) {
                $("#loader").hide();
                alert('Boleto no existente');
            } else {

                $.each(data, function (key, val) {
                    if (($("#noReserva").val() == val.reserva || $("#noReserva").val() == '')) {
                        params = {
                            service: 'agregarBoletoDetalle',
                            noBoleto: val.id,
                            montoTotal: val.montoTotal,
                            fee: val.valorFee,
                            tasaCambio: val.tasaCambio
                        };
                        $.post('controllers/agenciasViajesController.php', params, function (data1) {
                            $.each(data1, function (key, val1) {
                                if (val1.message == 'success') {

                                }
                            });
                        }, 'json');
                        $("#modal1").modal('hide');
                    } else {
                        $("#" + noReserva).prop("checked", false);
                        alert("LOS BOLETOS NO COINCEDE CON LA RESERVA YA AGREGADA")
                    }

                });
            }
        }, 'json').done(function () {
            $("#loader").hide();
            cargarBoletosDetalle();
        });
    } else {
        eliminarBoletoDetalle(noReserva);
    }
}
//
function agregarOtrosCargos() {
    if (event.which == 13 || event.keyCode == 13) {
        cargarBoletosDetalle();
    }
}
//
function cargarTipoImpresion() {
    var datos = "";
    $('#tipoImpresion').html('');
    if ($('#tipoFacturacion').val() == '2') {
        datos += "<option value='2'>FACTURA</option>";
        $('.otrosCargosDiv').show();
    } else {
        datos += "<option value='1'>PAGARE</option>";
        datos += "<option value='3'>FACTURA Y PAGARE</option>";
        $('.otrosCargosDiv').hide();
    }
    $('#tipoImpresion').append(datos);
    cargarBoletosDetalle();
}
//
function updateFeeDetalle(event, id, tasaCambio, montoTotal) {

    if (event.which == 13 || event.keyCode == 13) {

        params = {
            service: 'updateFeeDetalle',
            fee: $('#fee' + id).val(),
            id: id,
            tasaCambio: tasaCambio,
            montoTotal: montoTotal
        }
        console.log(params);
        $.post('controllers/agenciasViajesController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message == 'success') {
                    cargarBoletosDetalle();
                }
            });
        }, 'json');
    }
}
//
function eliminarBoletoDetalle(boleto) {
    bootbox.confirm('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> ¿Desea quitar este boleto?</div>', function (respuesta) {
        if (respuesta) {

            params = {
                service: 'eliminarBoletoDetalle',
                boleto: boleto
            }
            $.post('controllers/agenciasViajesController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message == 'success') {
                        cargarBoletosDetalle();
                    }
                });
            }, 'json');

        } else {
            return false;
        }
    });
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
        $("#loader").show();
        $("#add").attr('disabled', true);
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
        }, 'json').done(function () {
            $("#loader").hide();
        });
    }
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
                    datos += "<td><button class='btn-item btn btn-xs btn-danger' onclick='eliminarProductoCotizacion(" + val.id + ")'><i class='fa fa-trash'></i></button></td>";
                    datos += "<td>" + val.sku + "</td>";
                    datos += "<td>" + val.descLarga + "</td>";
                    datos += "<td align='right'><input type='text' class='form-control input-sm text-right' id='cantidad-" + val.id + "' value='" + val.cantidad + "' onKeydown='javascript: if (event.keyCode == 13 || event.keyCode == 9) updateItemCotizacion(" + val.id + "," + val.idProductos + ");'/></td>";
                    datos += "<td align='right'><input type='text' class='form-control input-sm text-right' id='precio-" + val.id + "' value='" + accounting.formatNumber(val.precio, 2) + "' onKeydown='javascript: if (event.keyCode == 13 || event.keyCode == 9) updateItemCotizacion(" + val.id + "," + val.idProductos + ");'/></td>";
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
function agregarProductoCotizacion() {
    var idProducto = $("#idProducto").val();
    var tipoProducto = $("#tipoProducto").val();
    var precio = accounting.unformat($("#precioProducto").val());
    var descProducto = ($("#descProducto").val());
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
            descProducto: descProducto,
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
function eliminarProductoCotizacion(item) {
    $("#loader").show();
    params = {
        service: 'eliminarProductoCotizacion',
        item: item
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                loadProductosCotizacion($("#idCotizacion").val(), 'edit');
                $("#codigo").focus();
            } else {
                alert('Error al eliminar producto en cotizacion, comuniquese con el administrador del sistema');
            }
        });
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function loadConsultaCotizaciones() {
    $.post('views/caja/consultaCotizaciones.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Cotizaciones');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadCajeros();
        loadVendedores();
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
            summary += "<tr>";
            summary += "<td colspan='11'>Total Cotizaciones:</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
}
//
function getCotizacion(idCotizacion, action, nombreCotizacion) {
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
            var errorMsg = "Corrige los siguiente errores:\n";
            var flag = true;
            var validar = idCotizaciones.filter(p => p.cotizacion === idCotizacion);
            if (idCotizaciones.length > 0 && $("#nombre").val() !== nombreCotizacion) {
                flag = false;
                errorMsg += 'Cotizacion seleccionada no esta asociada al cliente ' + $("#nombre").val() + ' \n';
            }
            if (idCotizaciones.length > 0 && validar.length > 0) {
                flag = false;
                errorMsg += 'Cotizacion ' + idCotizacion + ' ya esta seleccionada\n';
            }
            if (flag === false) {
                alert(errorMsg);
                $('.cotizaciones').prop('checked', false);
                return false;
            } else {
                $("#loader").show();
                var arr = {};
                arr['cotizacion'] = idCotizacion;
                idCotizaciones.push(arr);
                params = {
                    service: 'getCotizacion',
                    idCotizacion: idCotizacion,
                    action: action
                };
                $.post('controllers/cajaController.php', params, function (data) {
                    if (data === null) {
                        $("#loader").hide();
                        alert('Cotizacion ya gestionada o no existente en el sistema');
                    } else {
                        var idCotizacion = "";
                        $.each(data, function (key, val) {
                            idCotizacion = val.id;
                            $("#nit").val(val.nit);
                            $("#nombre").val(val.nombre);
                            $("#telefono").val(val.telefono);
                            $("#direccion").val(val.direccion);
                            $("#observaciones").append("(" + val.documento + "): " + val.observaciones + ", ");
                            $("#vendedores option").each(function () {
                                if ($(this).val() === val.idUsuarios) {
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
function loadConsultaVales() {
    $.post('views/caja/consultaVales.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Vales');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
    });
}
//
function editarCotizacion() {
    var id;
    var estado;
    $('.cotizaciones').each(function () {
        if (this.checked) {
            if ($(this).val() !== 'on') {
                id = $(this).val();
                estado = $(this).attr("data-status");
                console.log($(this).val());
            }
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        loadModuloCotizaciones(id, 'edit');
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
        $("#loader").show();
        $("#edit").attr('disabled', true);
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
        }, 'json').done(function () {
            $("#loader").hide();
        });
    }
}
//
function editarPedido() {
    var id;
    var estado;
    $('.pedidos').each(function () {
        if (this.checked) {
            if ($(this).val() !== 'on') {
                id = $(this).val();
                estado = $(this).attr("data-status");
                console.log($(this).val());
            }
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        loadModuloPedidos(id, 'edit');
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
        $("#loader").show();
        $("#edit").attr('disabled', true);
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
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = pathJasper + "pedidos.php?idPedido=" + val.idPedido + "";
                    window.open(url);
                    if (dbProject == 'erp_gsp' || dbProject == 'pos_togasjulissa') {
                        pedidoMercaderia(val.idPedido);
                    }
                    loadConsultaPedidos();
                } else {
                    alert('Error al actualizar pedido, comuniquese con el administrador del sistema');
                }
            });
        }, 'json').done(function () {
            $("#loader").hide();
        });
    }
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
function generarEnvio(idVenta) {
    var url = pathJasper + "envio.php?idVenta=" + idVenta;
    window.open(url);
}
//
function pedidoMercaderia(idPedido) {
    var url = pathJasper + "pedidoMercaderia.php?idPedido=" + idPedido;
    window.open(url);
}
//
function formatoGarantia(idVenta) {
    var url = pathJasper + "formatoGarantia.php?idVenta=" + idVenta;
    window.open(url);
}
//
function getItinerario() {
    $("#modal1").modal('show');
    $("#myModalLabel").html('Consulta de Itinerario');
    var itinerario = $("#itinerario").val().split(",");
    var data = '<div class="table-responsive"><table id="example" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">';
    data += '<thead><tr class="info"><td>Descripcion</td></thead><tbody>';
    $.each(itinerario, function (key, val) {
        data += '<tr><td>' + val + '</td></tr>';
    });
    data += '</tbody></table></div><br/>';
    data += "<button class='btn btn-danger btn-sm' onclick='cancelarModal();'><i class='fa fa-times'></i> Cerrar</button>"
    $("#controllers").html(data);
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
                    if (val.message === 'success') {
                        alert('Pedido confirmado exitosamente');
                        loadConsultaPedidos();
                    } else {
                        alert('Error al confirmar pedido, comuniquese con el administrador del sistema');
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
function reImprimirExamenesPedido() {
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
        params = {
            service: 'examenesPedido',
            idPedido: id
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                var url = pathJasper + "examenesPedidos.php?idPedido=" + id + "&idFamiliaNivel1=" + val.idFamiliaNivel1 + "";
                window.open(url);
            });
        }, 'json');
    }
}
//
function updateItemVenta(item, idProducto) {
    var cantidad = accounting.unformat($("#cantidad-" + item + "").val());
    var precio = accounting.unformat($("#precio-" + item + "").val());
    var costo = accounting.unformat($("#costo-" + item + "").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!cantidad || cantidad === 0 || cantidad < 0) {
        flag = false;
        errorMsg += 'La cantidad ingresada no puede ser vacio, cero o menor a cero \n';
    }
    if (!precio || precio === 0 || precio < 0) {
        flag = false;
        errorMsg += 'El precio ingresado no puede ser vacio, cero o menor a cero\n';
    }
    if (precio < costo) {
        flag = false;
        errorMsg += `El precio de venta no puede ser menor a: ${$("#costo-" + item + "").val()} \n`;
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'updateItemVenta',
            item: item,
            idProductos: idProducto,
            cantidad: cantidad,
            precio: precio,
            total: accounting.unformat((precio * cantidad))
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        loadProductosVenta();
                        break;
                    case 'failed':
                        alert('Error al actualizar producto en venta, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json');
    }
}
//
function loadModuloRecibos() {
    $.post('views/caja/recibos.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Recibos');
        loadDocumentos('recibos');
    }).done(function () {
        $("#loader").hide();
    });
}
//
function cxc() {
    params = {
        service: 'cxc',
        idClientes: $("#idClientes").val(),
        nit: $("#nit").val()
    };
    $.post('controllers/bancosController.php', params, function (data) {
        var datos = "";
        var saldoActual = 0;
        var totalFacturas = 0;
        $("#detalle").html('');
        //$("#monto").val('');
        $("#motivo").html('');
        $("#totalAbonos").val('0.00');
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='7' align='center' class='warning text-warning'><b>No tiene facturas pendientes de pago</b></td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                saldoActual += accounting.unformat(val.saldo);
                totalFacturas += accounting.unformat(val.valorFactura);
                datos += "<tr>";
                datos += "<td>" + (key + 1) + "</td>";
                datos += "<td>" + val.factura + "</td>";
                datos += "<td>" + val.fechaFactura + "</td>";
                datos += "<td style='text-align:right;'>" + accounting.formatNumber(val.valorFactura, 2) + "</td>";
                datos += "<td style='text-align:right;'>" + accounting.formatNumber(val.saldo, 2) + "</td>";
                datos += "<td>" + val.fechaUltimoAbono + "</td>";
                datos += "<td style='text-align:right;'><input type='text' class='form-control input-sm abono' data-id='" + val.idVenta + "' data-title='" + val.factura + "' data-value='" + val.saldo + "'/></td>";
                datos += "</tr>";

            });
        }
        $("#totalFacturas").val(accounting.formatNumber(totalFacturas, 2));
        $("#totalSaldo").val(accounting.formatNumber(saldoActual, 2));
        $("#detalle").append(datos);
    }, 'json').done(function () {
        facturas = [];
        $('.abono').on('keydown', function (e) {
            if (e.which == 13) {
                if (accounting.unformat($(this).data("value")) < accounting.unformat($(this).val())) {
                    alert('Abono ingresado es mayor a saldo de factura');
                    $(this).val('');
                } else if (accounting.unformat($(this).val()) > accounting.unformat($("#monto").val())) {
                    alert('Abono ingresado es mayor a monto de cheque');
                    $(this).val('');
                } else if (accounting.unformat($(this).val()) !== '') {
                    if (abono !== 0) {
                        abono = 0;
                    }
                    $(".abono").each(function () {
                        abono += accounting.unformat($(this).val());
                    });
                }
                if ($(this).val() !== '') {
                    var validar = facturas.filter(p => p.idVentas === $(this).data("id"));
                    if (validar.length > 0) {
                        //QUITA EL ELEMENTO ACTUAL
                        $("#motivo").html($("#motivo").val().replace($(this).data("title") + '; ', ''));
                        facturas.splice(findIndex(facturas, $(this).data("id")), 1);
                        //ADICIONA EL ELEMENTO NUEVO
                        $("#motivo").append($(this).data("title") + '; ');
                        var arr = {};
                        arr['idVentas'] = $(this).data("id");
                        arr['saldo'] = accounting.unformat($(this).data("value"));
                        arr['factura'] = $(this).data("title");
                        arr['abono'] = accounting.unformat($(this).val());
                        facturas.push(arr);
                    } else {
                        if (accounting.unformat($(this).val()) <= accounting.unformat($(this).data("value")) && accounting.unformat($(this).val()) > 0) {
                            $("#motivo").append($(this).data("title") + '; ');
                            var arr = {};
                            arr['idVentas'] = $(this).data("id");
                            arr['saldo'] = accounting.unformat($(this).data("value"));
                            arr['factura'] = $(this).data("title");
                            arr['abono'] = accounting.unformat($(this).val());
                            facturas.push(arr);
                        } else {
                            alert('Valor ingresado no puede ser cero');
                            $(this).val('');
                            $("#totalAbonos").val('');
                        }
                    }
                } else {
                    $("#motivo").html($("#motivo").val().replace($(this).data("title") + '; ', ''));
                    facturas.splice(findIndex(facturas, $(this).data("id")), 1);
                }
                $("#totalAbonos").val(accounting.formatNumber(abono, 2));
                if (accounting.unformat($("#totalAbonos").val()) > $("#monto").val()) {
                    alert('Total de abonos ingresado es mayor a monto de cheque');
                    $("#totalAbonos").val(accounting.formatNumber((accounting.unformat($("#totalAbonos").val()) - accounting.unformat($(this).val())), 2));
                    $(this).val('');
                    $("#motivo").html($("#motivo").val().replace($(this).data("title") + '; ', ''));
                    facturas.splice(findIndex(facturas, $(this).data("id")), 1);
                }
                console.log(facturas);
            }
        });
    });
}
//
function generarReciboCXC() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var created_at = $("#created_at").val();
    var monto = accounting.unformat($("#monto").val());
    var motivo = $("#motivo").val();
    var idClientes = $("#idClientes").val();
    var nit = $("#nit").val();
    var totalAbonos = accounting.unformat($("#totalAbonos").val());
    var idFormasPago = $("#idFormasPago option:selected").val();
    var numeroDocumento = $("#chequeNo").val();
    var valor = accounting.unformat($("#valor").val());
    //VALIDACIONES
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!created_at) {
        flag = false;
        errorMsg += 'Ingrese fecha de recibo\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de recibo\n';
    }
    if (!idClientes) {
        flag = false;
        errorMsg += 'Seleccione cliente para emitir recibo\n';
    }
    if (!motivo) {
        flag = false;
        errorMsg += 'Ingrese motivo del recibo\n';
    }
    if (monto != totalAbonos) {
        flag = false;
        errorMsg += 'Total de abonos no es igual al monto del recibo\n';
    }
    if (!numeroDocumento || !valor) {
        flag = false;
        errorMsg += 'Ingrese informacion de forma de pago\n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'generarRecibo',
            idTipoDocumento: '2',
            idDocumento: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            created_at: created_at,
            monto: monto,
            motivo: motivo,
            idClientes: idClientes,
            nit: nit,
            facturas: facturas,
            idFormasPago: idFormasPago,
            numeroDocumento: numeroDocumento,
            valor: valor
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Recibo generado exitosamente');
                    var url = pathJasper + "reciboDeCaja.php?idRecibos=" + val.idRecibos + "";
                    window.open(url);
                    loadModuloRecibos();
                } else {
                    alert('Error al ingresar recibo, comuniquese con el administrador del sistema');
                }
            })
        }, 'json');
    }
}
//
function estadoCuentaCXC() {
    params = {
        idClientes: $("#idClientes").val()
    };
    var reporte = "estadoCuentaClientes-pdf.php";
    var url = "../views/jasper/" + reporte;
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadConsultaTomaMedidas() {
    $.post('views/caja/consultaTomaMedidas.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta Toma De Medidas');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).attr('readonly', true);
        $(".nav").show();
    });
}
//
function loadTomaMedidas(id, action) {
    $.post('views/caja/tomaMedidas.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $('#fechaEntrega,#fechaGraduacion,#fechaRecoger').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).attr('readonly', true);
        $(".nav").show();
        switch (action) {
            case 'get':
                $("#add").show();
                $("#delete").show();
                $("#edit").hide();
                $("#idTomaMedidasHidden").val("0");
                break;
            case'edit':
                $("#add").hide();
                $("#edit").show();
                $("#delete").hide();
                getTomaMedidas(id);
                break;
        }
        getTomaMedidasDetalle(id);
    });


}
//
function  getTomaMedidas(id) {
    params = {
        service: 'getTomaMedidas',
        idTomaMedidas: id
    };
    $("#idTomaMedidasHidden").val(id);
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {

            $("#idPedidoHidden").val(val.idPedidos);
            $("#noPedido").val(val.documento);
            $("#nombrePedido").val(val.nombreP);
            $("#observacionesT").append(val.observaciones);
            $("#fechaEntrega .form-control").val(val.fechaEntrega2);
            $("#fechaGraduacion .form-control").val(val.fechaGraduacion2);
            $("#fechaRecoger .form-control").val(val.fechaRecoger2);
        });
    }, 'json');

}
function agregarTomaMedidasDetalle() {
    var apellido = $("#apellido").val();
    var nombre = $("#nombre").val();
    var hombro = $("#hombro").val();
    var manga = $("#manga").val();
    var altura = $("#altura").val();
    var cabeza = $("#cabeza").val();
    var recibo = $("#recibo").val();
    var observaciones = $("#observaciones").val();
    var idTomaMedidas = $("#idTomaMedidasHidden").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!apellido) {
        flag = false;
        errorMsg += 'Ingrese apellido\n';
    }
    if (!nombre) {
        flag = false;
        errorMsg += 'Ingrese Nombre\n';
    }
    if (!hombro) {
        flag = false;
        errorMsg += 'Ingrese medida de hombros\n';
    }
    if (!altura) {
        flag = false;
        errorMsg += 'Seleccione medida de altura\n';
    }
    if (!cabeza) {
        flag = false;
        errorMsg += 'ingrese medida de cabeza\n';
    }
    if (flag == false) {
        alert(errorMsg);
    } else {
        params = {
            service: 'agregarTomaMedidasDetalle',
            apellido: apellido,
            nombre: nombre,
            hombro: hombro,
            manga: manga,
            altura: altura,
            cabeza: cabeza,
            recibo: recibo,
            observaciones: observaciones,
            idTomaMedidas: idTomaMedidas
        };
        $.post('controllers/cajaController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message == 'success') {
                    getTomaMedidasDetalle();
                } else {
                    alert('error al ingresar datos');
                }
            });
        }, 'json');
    }
}
//
function getTomaMedidasDetalle() {
    $("#apellido").val('');
    $("#nombre").val('');
    $("#hombro").val('');
    $("#manga").val('');
    $("#altura").val('');
    $("#cabeza").val('');
    $("#recibo").val('');
    $("#observaciones").val('');
    params = {
        service: 'getTomaMedidasDetalle',
        idTomaMedidas: $("#idTomaMedidasHidden").val()

    };
    //(params);
    $.post('controllers/cajaController.php', params, function (data) {
        $("#detalle").html('');
        $("#summary").html('');
        var datos = "";
        var summary = "";
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='12' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var n = 0;
            $.each(data, function (key, val) {
                n++;
                datos += "<tr>";
                datos += "<td>" + n + "</td>";
                datos += "<td><button class='btn-item btn btn-xs btn-danger' onclick='eliminarTomaMedidasDetalle(" + val.id + ")'><i class='fa fa-trash'></i></button></td>";
                datos += "<td>" + val.apellido + "</td>";
                datos += "<td>" + val.nombre + "</td>";
                datos += "<td>" + val.hombro + "</td>";
                datos += "<td>" + val.manga + "</td>";
                datos += "<td>" + val.altura + "</td>";
                datos += "<td>" + val.cabeza + "</td>";
                datos += "<td>" + val.noRecibo + "</td>";
                datos += "<td>" + val.observaciones + "</td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    }, 'json');

}
//
function eliminarTomaMedidasDetalle(id) {
    bootbox.confirm('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> ¿Desea eliminar este producto?</div>', function (respuesta) {
        if (respuesta) {
            params = {
                service: 'eliminarTomaMedidasDetalle',
                id: id

            };
            $.post('controllers/cajaController.php', params, function (data) {
                $.post('controllers/cajaController.php', params, function (respuesta) {
                    $.each(respuesta, function (key, val) {
                        if (val.message == 'success') {
                            getTomaMedidasDetalle(0);
                        } else {
                            alert('error al ingresar datos');
                        }
                    });
                }, 'json');

            }, 'json');
        } else {
            return false;
        }
    });

}
//
function generarTomaMedidas() {
    var idPedido = $("#idPedidoHidden").val();
    var noPedido = $("#noPedido").val();
    var observaciones = $("#observacionesT").val();
    var fechaEntrega = $("#fechaEntrega .form-control").val();
    var fechaGraduacion = $("#fechaGraduacion .form-control").val();
    var fechaRecoger = $("#fechaRecoger .form-control").val();
    var errorMsg = "";
    var flag = true;
    //VALIDACIONES 
    if (!noPedido) {
        flag = false;
        errorMsg += 'Ingrese Pedido\n';
    }

    if (!fechaEntrega) {
        flag = false;
        errorMsg += 'Ingrese fecha De Entrega\n';
    }
    if (!fechaGraduacion) {
        flag = false;
        errorMsg += 'Ingrese Fecha De Graduacion\n';
    }
    if (!fechaRecoger) {
        flag = false;
        errorMsg += 'Ingrese Fecha A Recoger\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'generarTomaMedidas',
            idPedido: idPedido,
            fechaEntrega: fechaEntrega,
            fechaGraduacion: fechaGraduacion,
            fechaRecoger: fechaRecoger,
            observaciones: observaciones

        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = pathJasper + "tomaMedidas.php?idTomaMedidas=" + val.idTomaMedidas + "";
                    window.open(url);
                    alert('Toma de medidas procesada exitosamente');
                    loadConsultaTomaMedidas();
                } else {
                    alert("Error al ingresar toma de medidas");
                }
            });
        }, 'json');
    }
}
//
function actualizarTomaMedidas() {
    var idPedido = $("#idPedidoHidden").val();
    var noPedido = $("#noPedido").val();
    var observaciones = $("#observacionesT").val();
    var fechaEntrega = $("#fechaEntrega .form-control").val();
    var fechaGraduacion = $("#fechaGraduacion .form-control").val();
    var fechaRecoger = $("#fechaRecoger .form-control").val();
    var errorMsg = "";
    var flag = true;
    //VALIDACIONES 
    if (!noPedido) {
        flag = false;
        errorMsg += 'Ingrese Pedido\n';
    }

    if (!fechaEntrega) {
        flag = false;
        errorMsg += 'Ingrese fecha De Entrega\n';
    }
    if (!fechaGraduacion) {
        flag = false;
        errorMsg += 'Ingrese Fecha De Graduacion\n';
    }
    if (!fechaRecoger) {
        flag = false;
        errorMsg += 'Ingrese Fecha A Recoger\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'actualizarTomaMedidas',
            idPedido: idPedido,
            fechaEntrega: fechaEntrega,
            fechaGraduacion: fechaGraduacion,
            fechaRecoger: fechaRecoger,
            observaciones: observaciones,
            idTomaMedidas: $("#idTomaMedidasHidden").val()

        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert("toma de medidas actualizada");
                    loadConsultaTomaMedidas();
                } else {
                    alert("Error al actualizar toma de medidas");
                }
            });
        }, 'json');
    }
}
//
function consultarTomaMedidas() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var noPedido = $("#noPedido").val();

    params = {
        service: 'consultarTomaMedidas',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        noPedido: noPedido

    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle,#summary").html('');
        var datos = "";
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='5' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {

            $.each(data, function (key, val) {

                datos += "<tr>";
                datos += "<td><input type='checkbox' class='medidas' value='" + val.id + "'/></td>";
                datos += "<td>" + val.id + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.fechaEntrega2 + "</td>";
                datos += "<td>" + val.fechaGraduacion2 + "</td>";
                datos += "<td>" + val.fechaRecoger2 + "</td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    }, 'json');
}
//
function loadEliminarTomaMedida(idTomaMedida) {
    bootbox.confirm('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> ¿Desea eliminar este producto?</div>', function (respuesta) {
        if (respuesta) {
            params = {
                service: 'eliminarTomaMedidas',
                idTomaMedida: idTomaMedida
            };
            $.post('controllers/cajaController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        loadConsultaTomaMedidas();
                    } else {
                        alert('Error al eliminar toma de medida');
                    }
                });
            }, 'json');
        } else {
            return false;
        }
    });
}

function eliminarTomaMedida() {
    var id;
    var estado;
    $('.medidas').each(function () {
        if (this.checked) {
            id = $(this).val();
            estado = $(this).attr("data-status");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        loadEliminarTomaMedida(id);
    }
}
//
function editarTomaMedidas() {
    var id;
    var estado;
    $('.medidas').each(function () {
        if (this.checked) {
            id = $(this).val();
            estado = $(this).attr("data-status");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        loadTomaMedidas(id, 'edit');
    }
}
//
function cancelarTomaMedidas() {
    params = {
        service: 'cancelarTomaMedidas'
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                $("#noPedido").val('');
                $("#nombrePedido").val('');
                loadTomaMedidas(0, 'get');
            } else {
                alert('Error al cancelar toma medidas');
            }
        });
    }, 'json');
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


function reImprimirTomaMedidas() {
    var id;
    $('.medidas').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "tomaMedidas.php?idTomaMedidas=" + id + "";
        window.open(url);
    }
}
//
function updateItemCotizacion(item, idProducto) {
    var cantidad = accounting.unformat($("#cantidad-" + item + "").val());
    var precio = accounting.unformat($("#precio-" + item + "").val());
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!cantidad || cantidad === 0 || cantidad < 0) {
        flag = false;
        errorMsg += 'La cantidad ingresada no puede ser vacio, cero o menor a cero \n';
    }
    if (!precio || precio === 0 || precio < 0) {
        flag = false;
        errorMsg += 'El precio ingresado no puede ser vacio, cero o menor a cero\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'updateItemCotizacion',
            item: item,
            idProductos: idProducto,
            cantidad: cantidad,
            precio: precio,
            total: accounting.unformat((precio * cantidad))
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        loadProductosCotizacion($("#idCotizacion").val(), 'edit');
                        break;
                    case 'failed':
                        alert('Error al actualizar producto en venta, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json');
    }
}
//
function findIndex(array, item) {
    var index;
    $.each(array, function (key, val) {
        if (val.idVentas === item) {
            index = key;
        }
    });
    return index;
}
//
function generarConsolidadoPedidos() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    params = {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin
    };
    var url = pathJasper + "consolidadoPedidos.php";
    $.redirect(url, params, 'post', '_blank');
}
//
function habilitarDescuento() {
    $.jStorage.deleteKey("idUsuarioAdmin");
    $.post('views/admin/loginAdmin.php', function (respuesta) {
        $("#controllers").html(respuesta);
        $("#action").val('habilitarDescuento');
    }).done(function () {
        $("#modal1").modal('show');
        $("#myModalLabel").html('Login Supervisor');
    });
}
//
function exportarReporteFacturacion() {
    var fechaInicio = $("#fechaInicio").val();
    var fechaFin = $("#fechaFin").val();
    var tipoVenta = $("#tipoVentaCF").val();
    var estatus = $("#estatus").val();
    var serie = $("#serieFactura").val();
    var correlativo = $("#correlativoFactura").val();
    var cliente = $("#cliente").val();
    var idSucursales = $("#idSucursalesCF").val();
    var idVendedor = $("#vendedores").val();
    //
    params = {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        tipoVenta: tipoVenta,
        estatus: estatus,
        serie: serie,
        correlativo: correlativo,
        cliente: cliente,
        idSucursales: idSucursales,
        idVendedor: idVendedor
    };
    var url = "views/reportes/facturacion-excel.php";
    $.redirect(url, params, 'post');
}
//
function cambiarEstadoOrden(idPedido) {
    params = {
        service: 'cambiarEstadoOrden',
        idPedido: idPedido,
        estado: $("#estado-" + idPedido).val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            switch (val.message) {
                case 'success':
                    consultarPedidos();
                    break;
                default :
                    alert('Error al actualizar ordenes, comuniquese con el administrador del sistema');
                    break;
            }
        });
    }, 'json');
}
//
function loadConsultaCortesCaja() {
    $.post('views/caja/consultaCortesCaja.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Cortes de Caja');
    });
}
//
function consultarCortesCaja() {
    var fechaInicio = $("#fechaInicio").val();
    var fechaFin = $("#fechaFin").val();
    //
    params = {
        service: 'consultarCortesCaja',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        idSucursales: $("#idSucursales").val()
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle,#summary").html('');
        var datos = "";
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='15' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='data' value='" + val.id + "'/></td>";
                datos += "<td>" + val.fechaCorte + "</td>";
                datos += "<td>" + val.fondoCaja + "</td>";
                datos += "<td>" + val.totalVales + "</td>";
                datos += "<td>" + val.totalEfectivo + "</td>";
                datos += "<td>" + val.totalEfectivoDolares + "</td>";
                datos += "<td>" + val.totalExenciones + "</td>";
                datos += "<td>" + val.totalCheques + "</td>";
                datos += "<td>" + val.totalVouchers + "</td>";
                datos += "<td>" + val.totalCorte + "</td>";
                datos += "<td>" + val.totalVentasContado + "</td>";
                datos += "<td>" + val.totalVentasCredito + "</td>";
                datos += "<td>" + val.diferencia + "</td>";
                datos += "<td>" + val.cajero + "</td>";
                datos += "<td>" + val.created_at + "</td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    }, 'json');
}
//
function imprimirCorteCaja() {
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
        var url = "";
        if (dbProject === 'erp_supleXela') {
            url = pathJasper + "cierreCaja-pdf.php?idCorte=" + id;
        } else {
            url = pathJasper + "corteCaja.php?idCorte=" + id;
        }
        window.open(url);
    }
}
//
function loadConsultaValesCaja() {
    $.post('views/caja/consultaValesCaja.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Vales de Caja');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
    });
}
//
function consultarValesCaja() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    params = {
        service: 'consultarVales',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $("#detalle").html('');
        $("#summary").html('');
        var datos = "";
        var summary = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='6' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var total = 0;
            $.each(data, function (key, val) {
                total += accounting.unformat(val.monto);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='data' value='" + val.idVale + "'/></td>";
                datos += `<td>${val.fecha}</td>`;
                datos += `<td>${val.solicitado}</td>`;
                datos += `<td>${val.observaciones}</td>`;
                datos += `<td>${val.pagador}</td>`;
                datos += "<td align='right'>" + accounting.formatMoney(val.monto, 'Q. ') + "</td>";
                datos += "</tr>";
            });
        }
        summary += "<tr>";
        summary += "<td colspan='5'>Total Vales</td>";
        summary += "<td align='right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
        summary += "</tr>";
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
}
//
function eliminarValeCaja() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        $("#loader").show();
        params = {
            service: 'eliminarValeCaja',
            idVale: id
        };
        $.post('controllers/cajaController.php', params, function (respuesta) {
            $.each(respuesta, function (key, val) {
                if (val.message === 'success') {
                    alert('Anulación exitosa');
                    consultarValesCaja();
                } else {
                    alert('Error en Anulación');
                }
            });
        }, 'json').done(function () {
            $("#loader").hide();
        });
    }
}
//
function imprimirValeCaja() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "vales.php?id=" + id;
        window.open(url);
    }
}