$(document).ready(function () {
    //loadFacturacion();
    //loadModuloRecibos();
});
//
function loadFacturacion(idVenta) {
    params = {
        idVenta: idVenta
    };
    $.post('views/facturacion/recepcionVentas.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Facturación');
        $("#opcion").html('Ingreso de Facturas');
        $("#fechaFactura").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
        //CALCULO IVA
        $("#valorFactura").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                var valorFactura = accounting.unformat($(this).val());
                var iva = accounting.formatNumber((valorFactura / 1.12 * 0.12 * 100) / 100, 2);
                var subTotal = accounting.formatNumber(valorFactura - accounting.unformat(iva), 2);
                $("#subTotal").val(subTotal);
                $("#iva").val(iva);
                $("#total").val(accounting.formatNumber(valorFactura, 2));
            }
        });
        //RECALCULA IVA AL MOMENTO DE APLICAR DESCUENTO MONEDA
        $("#descuentoM").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                var valorFactura = accounting.unformat($("#valorFactura").val()) - accounting.unformat($(this).val());
                var iva = accounting.formatNumber((valorFactura / 1.12 * 0.12 * 100) / 100, 2);
                var subTotal = accounting.formatNumber(valorFactura - accounting.unformat(iva), 2);
                var descuentoP = accounting.formatNumber(accounting.unformat($(this).val()) / accounting.unformat($("#valorFactura").val()) * 100, 2);
                $("#subTotal").val(subTotal);
                $("#descuentoP").val(descuentoP);
                $("#iva").val(iva);
                $("#total").val(accounting.formatNumber(valorFactura, 2));
            }
        });
        //RECALCULA IVA AL MOMENTO DE APLICAR DESCUENTO PORCENTAJE
        $("#descuentoP").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                var descuentoM = accounting.unformat($("#valorFactura").val()) * accounting.unformat($(this).val()) / 100;
                var valorFactura = accounting.unformat($("#valorFactura").val()) - descuentoM;
                var iva = accounting.formatNumber((valorFactura / 1.12 * 0.12 * 100) / 100, 2);
                var subTotal = accounting.formatNumber(valorFactura - accounting.unformat(iva), 2);
                $("#subTotal").val(subTotal);
                $("#descuentoM").val(accounting.formatNumber(descuentoM, 2));
                $("#iva").val(iva);
                $("#total").val(accounting.formatNumber(valorFactura, 2));
            }
        });
    }).done(function () {
        loadProductosVenta(idVenta);
        //
        $("#totalDolares,#tipoCambio").on('keydown', function (e) {
            if (e.keyCode === 9 || e.keyCode === 13) {
                let valorFactura = accounting.formatNumber(accounting.unformat($("#totalDolares").val()) * accounting.unformat($("#tipoCambio").val()), 2);
                $("#valorFactura").val(valorFactura);
            }
        });
        loadDocumentos('facturacion');
        $("#loader").hide();
    });
}
//
function cancelarVenta() {
    params = {
        service: 'cancelarVenta',
        idSucursales: $("#idSucursales").val()
    };
    console.log('--');
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                loadFacturacion();
            } else {
                alert('Error al cancelar venta');
            }
        });
    }, 'json');
}
//
function guardarVenta() {
    var idPedido = $("#idPedido").val();
    var autorizacion = $("#autorizacion").val();
    var serie = $("#serieFactura").val();
    var correlativo = $("#correlativo").val();
    var fechaEmision = $("#fechaEmision").val();
    var horaEmision = $("#horaEmision").val();
    var valorFactura = $("#valorFactura").val();
    var subtotal = $("#subTotal").val();
    var descuento = $("#descuentoM").val();
    var descuentoP = $("#descuentoP").val();
    var total = $("#total").val();
    var iva = $("#iva").val();
    var idFormato = $("#idFormato").val();
    var nit = $("#nit").val();
    var nombre = $("#nombre").val();
    var direccion = $("#direccion").val();
    var idTipoOperacion = $("#idTipoOperacion").val();
    var idTipoVenta = $("#idTipoVenta").val();
    var conceptoVenta = $("#conceptoVenta").val();
    var idSucursales = $("#idSucursales").val();
    var numeroProductos = accounting.unformat($("#numeroProductos").val());
    var idClientes = $("#idClientes").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!nit) {
        flag = false;
        errorMsg += 'Ingrese NIT del cliente\n';
    }
    if (!nombre) {
        flag = false;
        errorMsg += 'Ingrese Nombre del cliente\n';
    }
    if (!direccion) {
        flag = false;
        errorMsg += 'Ingrese Direccion del cliente\n';
    }
    if (!idTipoOperacion) {
        flag = false;
        errorMsg += 'Seleccione tipo de operacion\n';
    }
    if (!idTipoVenta) {
        flag = false;
        errorMsg += 'Seleccione tipo de venta\n';
    }
    if (!conceptoVenta && numeroProductos === 0) {
        flag = false;
        errorMsg += 'Ingrese concepto de venta\n';
    }
    if (!autorizacion) {
        flag = false;
        errorMsg += 'Ingrese numero de autorizacion\n';
    }
    if (!serie) {
        flag = false;
        errorMsg += 'Ingrese serie de factura\n';
    }
    if (!correlativo) {
        flag = false;
        errorMsg += 'Ingrese correlativo\n';
    }
    if (!fechaEmision && !horaEmision) {
        flag = false;
        errorMsg += 'Ingrese fecha y hora de emision\n';
    }
    if (!valorFactura) {
        flag = false;
        errorMsg += 'Ingrese valor de factura\n';
    }
    if (!subtotal) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar subtotal\n';
    }
    if (!iva) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar iva\n';
    }
    if (!total) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar total\n';
    }
//    if (!idFormato) {
//        flag = false;
//        errorMsg += 'Seleccione partida contable\n';
//    }
    if (!idSucursales) {
        flag = false;
        errorMsg += 'Seleccione una sucursal\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'guardarVenta',
            autorizacion: autorizacion,
            serie: serie,
            correlativo: correlativo,
            fechaEmision: fechaEmision,
            horaEmision: horaEmision,
            valorFactura: accounting.unformat(valorFactura),
            subtotal: accounting.unformat(subtotal),
            descuento: accounting.unformat(descuento),
            descuentoP: descuentoP,
            total: accounting.unformat(total),
            iva: accounting.unformat(iva),
            idFormato: idFormato,
            nit: nit,
            nombre: nombre,
            direccion: direccion,
            idTipoOperacion: idTipoOperacion,
            idTipoVenta: idTipoVenta,
            concepto: conceptoVenta,
            idSucursales: idSucursales,
            idClientes: idClientes,
            idPedido: idPedido,
            totalDolares: accounting.unformat($("#totalDolares").val()),
            tipoCambio: accounting.unformat($("#tipoCambio").val())
        };
        //console.log(params);
        //return false;
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        reImprimirFactura(val.idVenta);
                        alert('Venta ingresada exitosamente');
                        loadFacturacion();
                        //loadData('vw_ventas', 'Facturación', 'Listado de Ventas', 0, 0, 0);
                        break;
                    case 'docExists':
                        alert('Serie y correlativo de factura ya fue ingresado al sistema');
                        $("#loader").hide();
                        break;
                    default :
                        alert('Error al ingresar venta, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json');
    }
}
//
function updateVenta() {
    var id;
    $('input[type=checkbox]').each(function () {
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
        loadFacturacion(id);
    }
}
//
function actualizarVenta() {
    var serie = $("#serieFactura").val();
    var correlativo = $("#correlativo").val();
    var fechaFactura = $("#fechaFactura").val();
    var valorFactura = $("#valorFactura").val();
    var subtotal = $("#subTotal").val();
    var descuento = $("#descuentoM").val();
    var descuentoP = $("#descuentoP").val();
    var total = $("#total").val();
    var iva = $("#iva").val();
    var idFormato = $("#idFormato").val();
    var nit = $("#nit").val();
    var nombre = $("#nombre").val();
    var direccion = $("#direccion").val();
    var idTipoOperacion = $("#idTipoOperacion").val();
    var idTipoVenta = $("#idTipoVenta").val();
    var conceptoVenta = $("#conceptoVenta").val();
    var idSucursales = $("#idSucursales").val();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!nit) {
        flag = false;
        errorMsg += 'Ingrese NIT del cliente\n';
    }
    if (!nombre) {
        flag = false;
        errorMsg += 'Ingrese Nombre del cliente\n';
    }
    if (!direccion) {
        flag = false;
        errorMsg += 'Ingrese Direccion del cliente\n';
    }
    if (!idTipoOperacion) {
        flag = false;
        errorMsg += 'Seleccione tipo de operacion\n';
    }
    if (!idTipoVenta) {
        flag = false;
        errorMsg += 'Seleccione tipo de venta\n';
    }
    if (!conceptoVenta) {
        flag = false;
        errorMsg += 'Ingrese concepto de venta\n';
    }
    if (!serie) {
        flag = false;
        errorMsg += 'Ingrese serie de factura\n';
    }
    if (!correlativo) {
        flag = false;
        errorMsg += 'Ingrese correlativo\n';
    }
    if (!fechaFactura) {
        flag = false;
        errorMsg += 'Ingrese fecha de factura\n';
    }
    if (!valorFactura) {
        flag = false;
        errorMsg += 'Ingrese valor de factura\n';
    }
    if (!subtotal) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar subtotal\n';
    }
    if (!iva) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar iva\n';
    }
    if (!total) {
        flag = false;
        errorMsg += 'Ingrese valor de factura para generar total\n';
    }
    if (!idFormato) {
        flag = false;
        errorMsg += 'Seleccione partida contable\n';
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
            service: 'actualizarVenta',
            serie: serie,
            correlativo: correlativo,
            fechaFactura: fechaFactura,
            valorFactura: accounting.unformat(valorFactura),
            subtotal: accounting.unformat(subtotal),
            descuento: accounting.unformat(descuento),
            descuentoP: descuentoP,
            total: accounting.unformat(total),
            iva: accounting.unformat(iva),
            idFormato: idFormato,
            nit: nit,
            nombre: nombre,
            direccion: direccion,
            idTipoOperacion: idTipoOperacion,
            idTipoVenta: idTipoVenta,
            concepto: conceptoVenta,
            idPartida: $("#idPartida").val(),
            idVenta: $("#idVenta").val(),
            idSucursales: idSucursales,
            totalDolares: accounting.unformat($("#totalDolares").val()),
            tipoCambio: accounting.unformat($("#tipoCambio").val())
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Venta actualizada exitosamente');
                    loadData('vw_ventas', 'Facturación', 'Listado de Ventas', 0, 0, 0);
                } else {
                    alert('Error al actualizar venta');
                }
            });
        }, 'json');
    }
}
//
function eliminarVenta() {
    var r = confirm("¿Esta seguro de eliminar esta venta?");
    if (r == true) {
        params = {
            service: 'eliminarVenta',
            idPartida: $("#idPartida").val(),
            idVenta: $("#idVenta").val(),
            idTipoVenta: $("#idTipoVenta option:selected").val()
        };
        $.post('controllers/inventariosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Venta eliminada exitosamente');
                    loadData('vw_ventas', 'Facturación', 'Listado de Ventas', 0, 0, 0);
                } else {
                    alert('Error al eliminar venta');
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function eliminarProductoVenta(item, idProducto) {
    bootbox.confirm('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> ¿Desea eliminar este producto?</div>', function (respuesta) {
        if (respuesta) {
            params = {
                service: 'eliminarProductoVenta',
                idProducto: idProducto,
                item: item,
                idSucursales: $("#idSucursales").val()
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
            return false;
        }
    });
}
//
function loadConsultaFacturas() {
    $.post('views/caja/consultaFacturas.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Facturas');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadSucursalesEmpresa('idSucursalesCF');
        loadVendedores('admin');
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
    });
}
//
function loadConsultaFacturasDetallado() {
    $.post('views/caja/consultaFacturasDetallado.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Facturas Detallado');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadSucursalesEmpresa('idSucursalesCF');
        loadVendedores('admin');
    });
}
//
function loadConsultaFacturasLaxTravel() {
    $.post('views/caja/consultaFacturasLaxTravel.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Facturas');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadCajeros('admin');
        loadVendedores('admin');
    });
}
//
function loadConsultaBoletos() {
    $.post('views/caja/consultaBoletos.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Boletos');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadVendedores2('admin');
    });
}
//
function loadModuloFacturacion() {
    $.post('views/caja/facturacionAgenciasViajes.php', function (respuesta) {
        // $.post('views/caja/facturacion.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $('#fechaFactura').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).attr('readonly', true);
        $(".nav").show();
        $("#camposCredito").hide();
        loadDocumentos('facturacion');
        loadDocumentosCorrelativoLax('facturacion');
        loadVendedoresLaxTravel();
        loadProveedoresLaxTravel();
        //loadProductosVenta();
        cargarBoletosDetalle();
        //loadEmisores();
        getTipoCambio();
        $("#opcion").html('Facturación');
    }).done(function () {
        $("#noPedido").focus();
        $("#loader").hide();
    });
    cancelarVenta();
}
//
function consultarBoletos() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var noPagare = $("#noPagare").val();
    var noBoleto = $("#noBoleto").val();
    var reserva = $("#reserva").val();
    var proveedor = $("#proveedor").val();
    var cliente = $("#cliente").val();
    var pasajero = $("#pasajero").val();
    var vendedor = $("#vendedores").val();
    var cliente = $("#cliente").val();
    var tipoPago = $("#tipoPago").val();
    var lineaAerea = $("#lineaAerea").val();
    params = {
        service: 'consultarBoletos',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        noPagare: noPagare,
        noBoleto: noBoleto,
        reserva: reserva,
        proveedor: proveedor,
        pasajero: pasajero,
        vendedor: vendedor,
        cliente: cliente,
        tipoPago: tipoPago,
        lineaAerea: lineaAerea
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle,#summary").html('');
        var datos = "";
        var summary = "";
        var total1 = 0;
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='18' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var totalTarifa = 0;
            var totalImpuestoNacional = 0;
            var totalImpuestoExtranjero = 0;
            var totalTotal = 0;
            var totalTarjeta = 0;
            var totalComision = 0;
            var totalIva = 0;
            var totalPagar = 0;
            $.each(data, function (key, val) {

                totalTarifa += parseFloat(val.tarifa);
                totalImpuestoNacional += parseFloat(val.impNacional);
                totalImpuestoExtranjero += parseFloat(val.impExtranjero);
                totalTotal += parseFloat(val.total);
                totalTarjeta += parseFloat(val.tarjeta);
                totalComision += parseFloat(val.comision);
                totalIva += parseFloat(val.iva);
                totalPagar += parseFloat(val.aPagar);

                datos += "<tr>";
                datos += "<td><input type='checkbox' class='boletos' value='" + val.idBoleto + "'/></td>";
                datos += "<td>" + val.codigoLineaAerea + "</td>";
                datos += "<td>" + val.codCliente + "</td>";
                datos += "<td>" + val.pagare + "</td>";
                datos += "<td>" + val.boleto + "</td>";
                datos += "<td>" + val.fecha + "</td>";
                datos += "<td style='max-width: 200px; overflow: hidden;'>" + val.pasajero + "</td>";
                datos += "<td>" + accounting.formatNumber(val.tarifa, 2) + "</td>";
                datos += "<td>" + accounting.formatNumber(val.impNacional, 2) + "</td>";
                datos += "<td>" + accounting.formatNumber(val.impExtranjero, 2) + "</td>";
                datos += "<td>" + accounting.formatNumber(val.total, 2) + "</td>";
                datos += "<td>" + accounting.formatNumber(val.tarjeta, 2) + "</td>";
                datos += "<td>" + accounting.formatNumber(val.comision, 2) + "</td>";
                datos += "<td>" + accounting.formatNumber(val.iva, 2) + "</td>";
                datos += "<td>" + accounting.formatNumber(val.aPagar, 2) + "</td>";
                datos += "<td>" + val.tasa + "</td>";
                datos += "<td>" + val.codVendedor + "</td>";
                datos += "</tr>";
            });


            summary += "<><tr>";
            summary += "<td colspan='7' class='text-center'>TOTALES</td>";
            summary += "<td>" + accounting.formatNumber(totalTarifa, 2) + "</td>";
            summary += "<td>" + accounting.formatNumber(totalImpuestoNacional, 2) + "</td>";
            summary += "<td>" + accounting.formatNumber(totalImpuestoExtranjero, 2) + "</td>";
            summary += "<td>" + accounting.formatNumber(totalTotal, 2) + "</td>";
            summary += "<td>" + accounting.formatNumber(totalTarjeta, 2) + "</td>";
            summary += "<td>" + accounting.formatNumber(totalComision, 2) + "</td>";
            summary += "<td>" + accounting.formatNumber(totalIva, 2) + "</td>";
            summary += "<td>" + accounting.formatNumber(totalPagar, 2) + "</td>";
            summary += "<td>-</td>";
            summary += "<td>-</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
}
//
function verBoleto() {
    var id;
    var estado;
    console.log(id);
    $('.boletos').each(function () {
        if (this.checked) {
            id = $(this).val();
            estado = $(this).attr("data-status");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un boleto para realizar esta acción</div>');
    } else {
        loadBoleto(id);
    }

}
//
function loadBoleto(id) {

    $.post('views/caja/boleto.php', function (respuesta) {
        $("#page-container").html(respuesta);
        params = {
            service: 'getBoleto',
            idBoleto: id
        };
        $.post('controllers/agenciasViajesController.php', params, function (data) {
            $.each(data, function (key, val) {
                $("#idBoleto").val(val.id);
                $("#fecha").val(val.fecha);
                $("#gua").val(val.gua);
                $("#reserva").val(val.reserva);
                $("#file").val(val.fileName);
                $("#codigoLineaAerea").val(val.codigoLineaArea);
                $("#lineaAerea").val(val.lineaArea);
                $("#boleto").val(val.boleto);
                $("#pasajero").val(val.pasajero);
                $("#codigoAuth").val(val.codigoAuth);
                $("#endosos").val(val.endosos);
                $("#statusBoleto").val(val.estatusBoleto);
                $("#itinerario").html(val.itinerario);
                $("#codigoVendedor").val(val.codVendedor);
                $("#codigoCliente").val(val.codCliente);
                $("#nombreFacturacion").val(val.nombreCliente);
                $("#direccion").val(val.direccion);
                $("#tasaCambio").val(val.tasaCambio);
                $("#montoSinImpuestos").val(val.montoSinImpuestos);
                $("#impuestos").val(val.totalImpuestos);
                $("#total").val(val.montoTotal);
                $("#fee").val(val.valorFee);

            });

        }, 'json');
    });


}
//
function actualizarBoleto() {
    var codVendedor = $("#codigoVendedor").val();
    var codCliente = $("#codigoCliente").val();
    var idBoleto = $("#idBoleto").val();
    var fee = $("#fee").val();
    params = {
        service: 'updateBoleto',
        codVendedor: codVendedor,
        codCliente: codCliente,
        idBoleto: idBoleto,
        fee: fee
    };
    $.post('controllers/agenciasViajesController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message == 'success') {
                loadBoleto(idBoleto);
            }
        });

    }, 'json');

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
    var monedaFacturacion = $("#monedaFacturacion").val();
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
        idVendedor: idVendedor,
        monedaFacturacion: monedaFacturacion
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle,#summary").html('');
        var datos = "";
        var summary = "";
        var total1 = 0;
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='17' class='text-center'>0 registros encontrados</td>";
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
                datos += "<td><input type='checkbox' class='facturas' data-doc='" + val.autorizacionFEL + "' title='" + val.fechaFactura + "/" + val.estatus + "'  data-sucursales='" + val.idSucursales + "' value='" + val.idVenta + "'/></td>";
                datos += "<td>" + val.fechaEmisionFEL + "</td>";
                datos += "<td>" + val.tipoVenta + "</td>";
                datos += "<td>" + val.serie + "</td>";
                datos += "<td>" + val.correlativo + "</td>";
                datos += "<td>" + val.autorizacionFEL + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.nombreCliente + "</td>";
                datos += "<td>" + val.sucursal + "</td>";
                datos += "<td>" + val.vendedor + "</td>";
                datos += "<td class='" + status + " text-center'>" + val.estatus + "</td>";
                datos += "<td>" + val.fechaAnulacion + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.anticipo, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.saldo, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.subtotal, 'Q. ') + "</td>";
                if (params.monedaFacturacion === '1') {
                    datos += "<td class='text-right'>" + accounting.formatMoney(val.iva, 'Q. ') + "</td>";
                } else {
                    datos += "<td class='text-right'>0.00</td>";
                }
                if (params.monedaFacturacion === '1') {
                    datos += "<td class='text-right'>" + accounting.formatMoney(val.total, 'Q. ') + "</td>";
                } else {
                    datos += "<td class='text-right'>" + accounting.formatMoney(val.totalDolares, '$. ') + "</td>";
                }
                datos += "<td id='concepto-" + val.idVenta + "'></td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='12'>Total Facturacion:</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total1, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total2, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total3, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total4, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total5, 'Q. ') + "</td>";
            summary += "<td>&nbsp;</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
}
//
function conceptoVenta(idVenta) {
    console.log(idVenta);
    //$("#concepto-"+idVenta).html('Concepto de venta idVenta'+idVenta);
}
//
function consultarFacturasDetallado() {
    $("#loader").show();
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
        service: 'consultarFacturasDetallado',
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
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='13' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var total1 = 0;
            var total2 = 0;
            var total3 = 0;
            var total4 = 0;
            $.each(data, function (key, val) {
                total1 += accounting.unformat(val.cantidad);
                total2 += accounting.unformat(val.total);
                total3 += accounting.unformat(val.totalCosto);
                total4 += accounting.unformat(val.utilidad);
                //
                datos += "<tr>";
                datos += "<td>" + val.fechaFactura + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.cliente + "</td>";
                datos += "<td>" + val.vendedor + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.totalDocumento, 'Q. ') + "</td>";
                datos += "<td>" + val.sku + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += "<td class='text-right'>" + accounting.formatNumber(val.cantidad, 0) + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.costo, 'Q. ', 2) + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.precio, 'Q. ', 2) + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.total, 'Q. ', 2) + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.totalCosto, 'Q. ', 2) + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.utilidad, 'Q. ', 2) + "</td>";
                datos += "<td class='text-right'>" + accounting.formatNumber(val.margen, 2) + " %</td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='7'>Totales:</td>";
            summary += "<td align='right'>" + accounting.formatNumber(total1, 0) + "</td>";
            summary += "<td colspan='2'>&nbsp;</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total2, 'Q. ', 2) + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total3, 'Q. ', 2) + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total4, 'Q. ', 2) + "</td>";
            summary += "<td align='right'>" + accounting.formatNumber(((total2 - total3) / total2) * 100, 2) + " %</td>";
            summary += "<td>&nbsp;</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function consultarFacturasLaxTravel() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var documento = $("#documento").val();
    var vendedor = $("#vendedores").val();
    var cliente = $("#cliente").val();
    var pagare = $("#noPagare").val();
    var tipoVenta = $("#tipoVentaCF").val();
    var tipoFacturacion = $("#tipoFacturacion").val();
    params = {
        service: 'consultarFacturasLaxTravel',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        documento: documento,
        vendedor: vendedor,
        pagare: pagare,
        cliente: cliente,
        tipoVenta: tipoVenta,
        tipoFacturacion: tipoFacturacion
    };
    //console.log(params);
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle,#summary").html('');
        var datos = "";
        var summary = "";
        var total1 = 0;
        var totalCargos = 0;
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='13' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                var total = 0;
                var status = "";
                if (val.statusFactura === 'Activa') {
                    status = "alert-success";
                    total = parseFloat(val.total);
                } else {
                    status = "alert-danger";
                }
                total1 += parseFloat(total);
                totalCargos += parseFloat(val.totalCargos);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='facturas' title='" + val.fechaFactura + "/" + val.statusFactura + "' value='" + val.idVenta + "/" + val.documento + "/" + val.pagare + "/" + val.valTipoFacturacion + "'></td>";
                datos += "<td>" + val.fecha + "</td>";
                datos += "<td>" + val.tipoVenta + "</td>";
                datos += "<td>" + val.tipoFacturacion + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.pagare + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.codigoCliente + "</td>";
                datos += "<td style='max-width: 200px; overflow: hidden;'>" + val.nombre + "</td>";
                datos += "<td>" + val.codVendedor + "</td>";
                datos += "<td class='" + status + " text-center'>" + val.statusFactura + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.totalCargos, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='11'>Total Facturacion:</td>";
            summary += "<td align='right'>" + accounting.formatMoney(totalCargos, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total1, 'Q. ') + "</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
}
//
function reImprimirFactura(idVenta) {
    var id;
    var idSucursales;
    var serie;
    if (idVenta === undefined) {
        $('.facturas').each(function () {
            if (this.checked) {
                id = $(this).val();
                idSucursales = $(this).data('sucursales');
                serie = $(this).data('doc').split('-');
            }
        });
    } else {
        id = idVenta;
    }
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "formatoFactura.php?idVenta=" + id + "&modulo=ticket";
        window.open(url);
    }
}
//
function reImprimirFacturaLaxTravel() {
    var id;
    $('.facturas').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        console.log(id);
        var documentos = id.split('/');
        if (documentos[1] != "-") {
            var url = pathJasper + "facturaLax.php?idVenta=" + documentos[0] + "&factura=" + documentos[3] + "";
            window.open(url);
        }
        if (documentos[2] != "-") {
            var url = pathJasper + "pagareLax.php?idVenta=" + documentos[0] + "";
            window.open(url);
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
function anularFactura() {
    var id;
    var status;
    var fecha;
    var documento;
    $('.facturas').each(function () {
        if (this.checked) {
            $.jStorage.deleteKey("modulo");
            id = $(this).val();
            documento = $(this).data('doc');
            if (dbProject == 'erp_laxTravelTopacio') {
                var documentos = id.split('/');
                id = documentos[0];
            }
            $.jStorage.set("idFactura", id);
            $.jStorage.set("modulo", 'anularFactura');
            $.jStorage.set("documento2", documento);
            var datosF = $(this).attr('title').split('/');
            status = datosF[1];
            fecha = datosF[0];
            console.log('documento: ', $.jStorage.get("documento2"));
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        //Validar que factura sea del dia actual y que no este anulada
        var errorMsg = "Factura no puede ser anulada por los siguiente motivos:\n";
        var flag = true;
        if (status === 'ANULADA') {
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
                $("#action").val('anularFactura');
            }).done(function () {
                $("#modal1").modal('show');
                $("#myModalLabel").html('Login Supervisor');
            });
        }
    }
}
//
function eliminarFactura() {
    var id;
    var status;
    var fecha;
    var documento;
    $('.facturas').each(function () {
        if (this.checked) {
            $.jStorage.deleteKey("modulo");
            id = $(this).val();
            documento = $(this).data('doc');
            if (dbProject == 'erp_laxTravelTopacio') {
                var documentos = id.split('/');
                id = documentos[0];
            }
            $.jStorage.set("idFactura", id);
            $.jStorage.set("documento", documento);
            $.jStorage.set("modulo", 'eliminarFactura');
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
                if (val.message === 'noExist') {
                    alert('Usuario no tiene autorizacion para realizar esta operacion');
                } else {
                    $.jStorage.set("idUsuarioAdmin", val.id);
                    $("#modal1").modal('hide');
                    switch ($.jStorage.get("modulo")) {
                        case 'requisicionCompra':
                            gestionarRC();
                            break;
                        case 'ordenCompra':
                            gestionarOC();
                            break;
                        case 'eliminarFactura':
                            var r = confirm("¿Esta seguro de eliminar este registro?");
                            if (r == true) {
                                eliminar();
                            }
                            break;
                        case 'eliminarRecibo':
                            var r = confirm("¿Esta seguro de eliminar este registro?");
                            if (r == true) {
                                eliminarR();
                            }
                            break;
                        case 'anularFactura':
                            loadAnulacionFactura();
                            break;
                        case 'habilitarDescuento':
                            $("#descuentoMoneda").attr('readonly', false);
                            break;
                    }
                }
            });
        }
    }, 'json');
}
//
function loadAnulacionFactura() {
    console.log($.jStorage.get("idUsuarioAdmin"));
    console.log($.jStorage.get("idFactura"));
    console.log($.jStorage.get("documento2"));
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
        params = {
            service: 'anulacionFactura',
            idFactura: $.jStorage.get("idFactura"),
            idAdminUser: $.jStorage.get("idUsuarioAdmin"),
            documento2: $.jStorage.get("documento2"),
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
        }, 'json');
    }
}
//
function eliminar() {
    params = {
        service: 'eliminarFactura',
        idFactura: $.jStorage.get("idFactura"),
        documento: $.jStorage.get("documento")
    };
    $.post('controllers/cajaController.php', params, function (respuesta) {
        $.each(respuesta, function (key, val) {
            if (val.message === 'success') {
                alert('Registro eliminado exitosamente');
                if (dbProject == 'erp_laxTravelTopacio') {
                    loadConsultaFacturasLaxTravel();
                } else {
                    loadConsultaFacturas();
                }
            } else {
                alert('Error al eliminar registro, comuniquese con el administrador del sistema');
            }
        });
    }, 'json');
}
//
function exportarReporteFacturacion() {
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
    var url = "views/reportes/facturacion-excel.php";
    $.redirect(url, params, 'post');
}
//
function exportarReporteFacturacionDetallado() {
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
        service: 'consultarFacturasDetallado',
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
    var url = "views/reportes/facturacionDetallado-excel.php";
    $.redirect(url, params, 'post');
}
//

function exportarReporteFacturacionLaxTravel() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var documento = $("#documento").val();
    var vendedor = $("#vendedores").val();
    var cliente = $("#cliente").val();
    var pagare = $("#noPagare").val();
    var tipoVenta = $("#tipoVentaCF").val();
    var tipoFacturacion = $("#tipoFacturacion").val();
    params = {
        service: 'consultarFacturasLaxTravel',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        documento: documento,
        vendedor: vendedor,
        pagare: pagare,
        cliente: cliente,
        tipoVenta: tipoVenta,
        tipoFacturacion: tipoFacturacion
    };
    var url = "views/reportes/facturacion-excelLaxTravel.php";
    $.redirect(url, params, 'post');
}

function exportarReporteBoletos() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var noPagare = $("#noPagare").val();
    var noBoleto = $("#noBoleto").val();
    var reserva = $("#reserva").val();
    var proveedor = $("#proveedor").val();
    var cliente = $("#cliente").val();
    var pasajero = $("#pasajero").val();
    var vendedor = $("#vendedores").val();
    var cliente = $("#cliente").val();
    var tipoPago = $("#tipoPago").val();
    var lineaAerea = $("#lineaAerea").val();
    params = {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        noPagare: noPagare,
        noBoleto: noBoleto,
        reserva: reserva,
        proveedor: proveedor,
        pasajero: pasajero,
        vendedor: vendedor,
        cliente: cliente,
        tipoPago: tipoPago,
        lineaAerea: lineaAerea
    };
    //console.log(params);

    var url = "views/reportes/boletos-excel.php";
    $.redirect(url, params, 'post');
}

function ExportarReporteFacturacionLaxTravelPDF() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var documento = $("#documento").val();
    var vendedor = $("#vendedores").val();
    var cliente = $("#cliente").val();
    var pagare = $("#noPagare").val();
    var tipoVenta = $("#tipoVentaCF").val();
    var tipoFacturacion = $("#tipoFacturacion").val();
    params = {
        service: 'consultarFacturasLaxTravel',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        documento: documento,
        vendedor: vendedor,
        pagare: pagare,
        cliente: cliente,
        tipoVenta: tipoVenta,
        tipoFacturacion: tipoFacturacion
    };
    //console.log(params);
    var reporte = "consultaFacturas-pdf.php";
    var url = "../views/jasper/" + reporte;
    $.redirect(url, params, 'POST', '_blank');

}

function ExportarReporteBoletosPDF() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var noPagare = $("#noPagare").val();
    var noBoleto = $("#noBoleto").val();
    var reserva = $("#reserva").val();
    var proveedor = $("#proveedor").val();
    var cliente = $("#cliente").val();
    var pasajero = $("#pasajero").val();
    var vendedor = $("#vendedores").val();
    var cliente = $("#cliente").val();
    var tipoPago = $("#tipoPago").val();
    var lineaAerea = $("#lineaAerea").val();
    params = {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        noPagare: noPagare,
        noBoleto: noBoleto,
        reserva: reserva,
        proveedor: proveedor,
        pasajero: pasajero,
        vendedor: vendedor,
        cliente: cliente,
        tipoPago: tipoPago,
        lineaAerea: lineaAerea
    };
    var reporte = "consultaBoletos-pdf.php";
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
function consultarFacturasSuple() {
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
        service: 'consultarFacturasSuple',
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
            var total6 = 0;
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
                total6 += accounting.unformat(val.cantidad);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='facturas' data-doc='" + val.documento + "' title='" + val.fechaFactura + "/" + val.statusFactura + "' value='" + val.idVenta + "'/></td>";
                datos += "<td>" + val.fechaFactura + "</td>";
                datos += "<td>" + val.tipoVenta + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.nombreCliente + "</td>";
                datos += "<td>" + val.sucursal + "</td>";
                datos += "<td>" + val.vendedor + "</td>";
                datos += "<td class='" + status + " text-center'>" + val.estatus + "</td>";
                datos += "<td>" + val.fechaAnulacion + "</td>";
                datos += "<td class='text-right'>" + accounting.formatNumber(val.cantidad, 2) + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.anticipo, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.saldo, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.subtotal, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.iva, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.total, 'Q. ') + "</td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='10'>Total Facturacion:</td>";
            summary += "<td align='right'>" + accounting.formatNumber(total6, 2) + "</td>";
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
function loadConsultaFacturasSuple() {
    $.post('views/caja/consultaFacturasSuple.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Facturas');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadSucursalesEmpresa('idSucursalesCF');
        loadVendedores('admin');
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
    });
}
//
function exportarReporteFacturacionSuple() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var nit = $("#nit").val();
    var documento = $("#documento").val();
    var idCajero = $("#cajeros").val();
    var idVendedor = $("#vendedores").val();
    var tipoVenta = $("#tipoVentaCF").val();
    var cliente = $("#cliente").val();
    params = {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        documento: documento,
        nit: nit,
        idCajero: idCajero,
        idVendedor: idVendedor,
        tipoVenta: tipoVenta,
        cliente: cliente
    };
    var url = "views/reportes/facturacionSuple-excel.php";
    $.redirect(url, params, 'post');
}
//
function loadConsultaFacturasGeneral() {
    $.post('views/caja/consultaFacturasGeneral.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Facturas General');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        loadSucursalesEmpresa('idSucursalesCF');
        loadVendedores('admin');
    });
}
//
function consultarFacturasGeneral() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    //
    params = {
        service: 'consultarFacturasGeneral',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle,#summary").html('');
        var datos = "";
        var summary = "";
        var total1 = 0;
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='9' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var total1 = 0;
            var total2 = 0;
            $.each(data, function (key, val) {
                total1 += accounting.unformat(val.galones);
                total2 += accounting.unformat(val.total);
                datos += "<tr>";
                datos += "<td>" + val.fechaDocumento + "</td>";
                datos += "<td>" + val.tipoDocumento + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.nombreCliente + "</td>";
                datos += "<td>" + val.vendedor + "</td>";
                datos += "<td>" + val.estatus + "</td>";
                datos += "<td class='text-right'>" + accounting.formatNumber(val.galones, 2) + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.total, 'Q. ') + "</td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='7'>Total Facturacion:</td>";
            summary += "<td align='right'>" + accounting.formatNumber(total1, 2) + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total2, 'Q. ') + "</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
}
//
function loadModuloRecibos() {
    $.post('views/facturacion/reciboDeCaja.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#modulo").html('Facturacion');
        $("#opcion").html('Recibo de Caja');
        $('#fechaRecibo').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).val(today);
        loadDocumentos('recibos');
    }).done(function () {
        $("#loader").hide();
    });
}
//
function generarReciboDeCaja() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var fechaRecibo = $("#fechaRecibo").val();
    var monto = accounting.unformat($("#monto").val());
    var motivo = $("#motivo").val();
    var idClientes = $("#idClientes").val();
    var nit = $("#nit").val();
    var totalAbonos = accounting.unformat($("#totalAbonos").val());
    var idMonedas = $("#idMonedas").val()
    var chequeNo = $("#chequeNo").val();
    var noDeCuenta = $("#noDeCuenta").val();
    var nombreDelBanco = $("#nombreDelBanco").val();
    var nombreDelaCuenta = $("#nombreDelaCuenta").val();
    var valorDeCheque = $("#valorDeCheque").val();
    //VALIDACIONES
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!fechaRecibo) {
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
    if (!idMonedas) {
        flag = false;
        errorMsg += 'Seleccione tipo moneda\n';
    }
    if (!nombreDelBanco || !valorDeCheque) {
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
            fechaDeposito: fechaRecibo,
            monto: monto,
            motivo: motivo,
            idClientes: idClientes,
            nit: nit,
            facturas: facturas,
            idMonedas: idMonedas,
            chequeNo: chequeNo,
            noDeCuenta: noDeCuenta,
            nombreDelBanco: nombreDelBanco,
            nombreDelaCuenta: nombreDelaCuenta,
            valorDeCheque: valorDeCheque,
            moneda: $("#idMonedas option:selected").text()
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Recibo generado exitosamente');
                    loadModuloRecibos();
                } else {
                    alert('Error al ingresar recibo de caja, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function eliminarRecibo() {
    var id;
    $('.recibos').each(function () {
        if (this.checked) {
            $.jStorage.deleteKey("modulo");
            id = $(this).val();
            $.jStorage.set("idRecibo", id);
            $.jStorage.set("modulo", 'eliminarRecibo');
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        $.jStorage.deleteKey("idUsuarioAdmin");
        $.post('views/admin/loginAdmin.php', function (respuesta) {
            $("#controllers").html(respuesta);
            $("#action").val('eliminarRecibo');
        }).done(function () {
            $("#modal1").modal('show');
            $("#myModalLabel").html('Login Supervisor');
        });
    }
}
//
function eliminarR() {
    params = {
        service: 'eliminarRecibo',
        idRecibo: $.jStorage.get("idRecibo")
    };
    $.post('controllers/cajaController.php', params, function (respuesta) {
        $.each(respuesta, function (key, val) {
            if (val.message === 'success') {
                alert('Registro eliminado exitosamente');
                loadConsultaRecibos();
            } else {
                alert('Error al eliminar registro, comuniquese con el administrador del sistema');
            }
        });
    }, 'json');
}
//
//
function reImprimirRecibo() {
    var id;
    $('.recibos').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var url = pathJasper + "reciboDeCaja.php?idRecibos=" + id;
        window.open(url);
    }
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
        var url = pathJasper + "cierreCaja-pdf.php?idCorte=" + id + "&print=1";
        window.open(url);
    }
}