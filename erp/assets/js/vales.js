var montoLiquidar = 0;
var valesLiquidar;
var galonesLiquidar = 0;
$(document).ready(function () {
    //loadConsultaVales();
    //loadLiquidacionVales();
});
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
function consultarVales() {
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    params = {
        service: 'consultarVales',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        estado: $("#estado").val(),
        documento: $("#documento").val(),
        cliente: $("#cliente").val()
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle").html('');
        $("#summary").html('');
        var datos = "";
        var summary = "";
        var total1 = 0;
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='12' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var total = 0;
            var totalGalones = 0;
            var totalCosto2 = 0;
            $.each(data, function (key, val) {
                total += accounting.unformat(val.valorVale);
                totalGalones += accounting.unformat(val.galones);
                totalCosto2 += accounting.unformat(val.totalCosto);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='data' value='" + val.id + "' data-doc='" + val.documento + "' data-estado='" + val.estado + "'/></td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.fechaVale + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.solicitadoPor + "</td>";
                datos += "<td>" + val.realizadoPor + "</td>";
                datos += "<td>" + val.estado + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.valorVale, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.galones, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.precio, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.costo, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.totalCosto, 2) + "</td>";
                datos += "<td>" + val.idVentas + "</td>";
                datos += "<td>" + val.fechaFactura + "</td>";
                datos += "</tr>";
            });
        }
        summary += "<tr>";
        summary += "<td colspan='7'>Total Vales</td>";
        summary += "<td align='right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
        summary += "<td align='right'>" + accounting.formatNumber(totalGalones, 2) + "</td>";
        summary += "<td align='right'>-</td>";
        summary += "<td align='right'>-</td>";
        summary += "<td align='right'>" + accounting.formatMoney(totalCosto2, 'Q. ') + "</td>";
        summary += "<td align='right'>-</td>";
        summary += "<td align='right'>-</td>";
        summary += "</tr>";
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json');
}
//
function eliminarVale() {
    var id;
    var documento;
    var estado
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
            documento = $(this).data("doc");
            estado = $(this).data("estado");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        if (estado !== 'Abierto') {
            bootbox.alert('<br/><div class="alert alert-danger" role="alert"> <strong>Alerta!</strong> Vale no puede ser eliminado porque ya fue facturado</div>');
        } else {
            var r = confirm("¿Esta seguro de eliminar este vale?");
            if (r == true) {
                params = {
                    service: 'eliminarVale',
                    idVale: id,
                    documento: documento
                };
                $.post('controllers/reportesController.php', params, function (data) {
                    $.each(data, function (key, val) {
                        if (val.message === 'success') {
                            consultarVales();
                        } else {
                            alert('Error al eliminar vale, comuniquese con el administrador del sistema');
                        }
                    });
                }, 'json');
            } else {
                return false;
            }
        }
    }
}
//
function loadLiquidacionVales() {
    $.post('views/caja/liquidacionVales.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Liquidacion de Vales');
        $('#fechaFactura').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).val(today).attr('readonly', true);
        loadDocumentos('facturacion');
        loadSucursalesEmpresa('idSucursales', $("#idEmpresas").val());
    });
}
//
function consultarValesLiquidar() {
    params = {
        service: 'consultarValesLiquidar',
        idClientes: $("#idClientes").val()
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle").html('');
        $("#summary").html('');
        $("#motivo").html('');
        $("#monto").val('');
        var datos = "";
        var summary = "";
        montoLiquidar = 0;
        galonesLiquidar = 0;
        valesLiquidar = [];
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='12' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var total = 0;
            var totalGalones = 0;
            var totalCosto2 = 0;
            $.each(data, function (key, val) {
                total += accounting.unformat(val.valorVale);
                totalGalones += accounting.unformat(val.galones);
                totalCosto2 += accounting.unformat(val.totalCosto);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='data' value='" + val.id + "' data-galones='"+val.galones+"' data-value='" + val.valorVale + "' data-doc='" + val.documento + "'/></td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.fechaVale + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.solicitadoPor + "</td>";
                datos += "<td>" + val.realizadoPor + "</td>";
                datos += "<td>" + val.estado + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.valorVale, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.galones, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.precio, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.costo, 2) + "</td>";
                datos += "<td align='right'>" + accounting.formatNumber(val.totalCosto, 2) + "</td>";
                datos += "</tr>";
            });
        }
        summary += "<tr>";
        summary += "<td colspan='7'>Total Vales</td>";
        summary += "<td align='right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
        summary += "<td align='right'>" + accounting.formatNumber(totalGalones, 2) + "</td>";
        summary += "<td align='right'>-</td>";
        summary += "<td align='right'>-</td>";
        summary += "<td align='right'>" + accounting.formatMoney(totalCosto2, 'Q. ') + "</td>";
        summary += "</tr>";
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json').done(function () {
        $('.data').on('click', function () {
            if (this.checked) {
                montoLiquidar += accounting.unformat($(this).attr("data-value"));
                galonesLiquidar += accounting.unformat($(this).data("galones"));
                var arr = {};
                arr['idVales'] = $(this).val();
                arr['idDocumento'] = $(this).data("doc");
                valesLiquidar.push(arr);
                $("#motivo").append($(this).data("doc") + '; ');
            } else {
                montoLiquidar -= accounting.unformat($(this).attr("data-value"));
                galonesLiquidar -= accounting.unformat($(this).data("galones"));
                valesLiquidar.splice(findIndexVales(valesLiquidar, $(this).val()), 1);
                $("#motivo").html($("#motivo").val().replace($(this).data("doc") + '; ', ''));
            }
            $("#monto").val(accounting.formatNumber(montoLiquidar, 2));
            $("#galones").val(accounting.formatNumber(galonesLiquidar, 2));
            console.log(valesLiquidar);
        });
    });
}
//
function findIndexVales(array, item) {
    var index;
    $.each(array, function (key, val) {
        if (val.idVales === item) {
            index = key;
        }
    });
    return index;
}
//
function liquidarVales() {
    var doc = $("#correlativo").val().split("-");
    var valorFactura = accounting.unformat($("#monto").val());
    params = {
        service: 'liquidarVales',
        tipoDocumento: $("#tipoDocumento option:selected").text(),
        serie: doc[0],
        correlativo: doc[1],
        fechaFactura: $("#fechaFactura").val(),
        valorFactura: valorFactura,
        subtotal: accounting.unformat((valorFactura / 1.12)),
        descuento: 0,
        descuentoP: 0,
        total: valorFactura,
        anticipo: 0,
        saldo: valorFactura,
        iva: accounting.unformat(((valorFactura / 1.12) * 0.12)),
        tasaCambio: 0,
        totalDolares: 0,
        nit: $("#nit").val(),
        nombre: $("#nombre").val(),
        direccion: 'ciudad',
        tipoVenta: 1,
        observaciones: 'Liquidacion de vales ' + $("#motivo").val(),
        idSucursales: $("#idSucursales").val(),
        vales: valesLiquidar,
        idClientes: $("#idClientes").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $.each(data, function (key, val) {
            switch (val.message) {
                case 'success':
                    alert('Venta ingresada exitosamente');
                    loadLiquidacionVales();
                    break;
                case 'docExists':
                    alert('Serie y correlativo de factura ya fue ingresado al sistema');
                    break;
                default :
                    alert('Error al ingresar venta, comuniquese con el administrador del sistema');
                    break;
            }
        });
    }, 'json');
}