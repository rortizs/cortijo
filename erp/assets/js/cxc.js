/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {
    //loadReporteAbonos();
    //loadReporteSaldos();
    //loadConsultaRecibos();
    //loadNotasCredito();
});
//
function loadAntiguedadSaldos() {
    $.post('views/cxc/antiguedadSaldos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('CXC');
        $("#opcion").html('Antiguedad de Saldos');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#search").attr('disabled', true);
        $("#nit").attr('readonly', true);
    }).done(function () {
        $("#tipoReporte").on('change', function () {
            if ($(this).val() === '2') {
                $("#search").attr('disabled', false);
            } else {
                $("#search").attr('disabled', true);
                $("#nit").val('');
                $("#idClientes").val('');
                $("#nombre").val('');
            }
        });
    });
}
//
function imprimirAntiguedadSaldos() {
    params = {
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idClientes: $("#idClientes").val(),
        idCentrosCosto: $("#idCentrosCosto").val(),
        tipoConsulta: $("#tipoConsulta").val()
    };
    var url = "views/cxc/antiguedadSaldos_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadReporteAbonos() {
    $.post('views/cxc/reporteAbonos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('CXC');
        $("#opcion").html('Reporte de Abonos');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
    });
}
//
function exportarReporteAbonos() {
    params = {
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idCentrosCosto: $("#idCentrosCosto").val()
    };
    var url = "views/cxc/reporteAbonos-excel.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadReporteSaldos() {
    $.post('views/cxc/reporteSaldos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('CXC');
        $("#opcion").html('Reporte de Saldos');
    });
}
//
function exportarReporteSaldos() {
    params = {
        year: $("#year option:selected").text(),
        month: $("#month").val(),
        idCentrosCosto: $("#idCentrosCosto").val()
    };
    var url = "";
    switch ($("#tipoReporte").val()) {
        case '1':
            url = "views/cxc/reporteSaldosResumen-excel.php";
            break;
        case '2':
            url = "views/cxc/reporteSaldosDetallado-excel.php";
            break;
    }
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadConsultaRecibos() {
    $.post('views/cxc/consultaRecibos.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#opcion").html('Consulta de Recibos');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
    });
}
//
function consultarRecibos() {
    $("#loader").show();
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var recibo = $("#recibo").val();
    var factura = $("#factura").val();
    var cliente = $("#cliente").val();
    //
    params = {
        service: 'consultarRecibos',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        recibo: recibo,
        factura: factura,
        cliente: cliente
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle,#summary").html('');
        var datos = "";
        var summary = "";
        if (data === null) {
            datos += "<tr class='info'>";
            datos += "<td colspan='9' class='text-center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            var total1 = 0;
            var total2 = 0;
            var total3 = 0;
            $.each(data, function (key, val) {
                total1 += accounting.unformat(val.montoFactura);
                total2 += accounting.unformat(val.monto);
                total3 += accounting.unformat(val.saldo);
                datos += "<tr>";
                datos += "<td><input type='checkbox' class='recibos' data-doc='" + val.documento + "' title='" + val.fechaFactura + "/" + val.statusFactura + "' value='" + val.id + "'/></td>";
                datos += "<td>" + val.fechaRecibo + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.nombreCliente + "</td>";
                datos += "<td>" + val.nit + "</td>";
                datos += "<td>" + val.factura + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.montoFactura, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.monto, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.saldo, 'Q. ') + "</td>";
                datos += "</tr>";
            });
            summary += "<tr class='info'>";
            summary += "<td colspan='6'>Total Recibos:</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total1, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total2, 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total3, 'Q. ') + "</td>";
            summary += "</tr>";
        }
        $("#detalle").append(datos);
        $("#summary").append(summary);
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function saveFactRecurrente() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var idProductos = $("#idProducto").val();
    var monto = accounting.unformat($("#monto").val());
    var idTipoCuota = accounting.unformat($("#idTipoCuota").val());
    var idPeriodoPago = $("#idPeriodoPago").val();
    var fechaInicio = $("#fechaInicio").val();
    var fechaFin = $("#fechaFin").val();
    var noCuotas = accounting.unformat($("#noCuotas").val());
    var flag = true;
    if (!idProductos) {
        flag = false;
        errorMsg += 'Oprima boton de busqueda para agregar producto\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto\n';
    }
    if (!idTipoCuota) {
        flag = false;
        errorMsg += 'Seleccione tipo de cuota\n';
    }
    if (!idPeriodoPago) {
        flag = false;
        errorMsg += 'Seleccione periodo de pago\n';
    }
    if (!fechaInicio) {
        flag = false;
        errorMsg += 'Ingrese fecha inicio\n';
    }
    if (!fechaFin && idTipoCuota === 2) {
        flag = false;
        errorMsg += 'Ingrese fecha fin\n';
    }
    if (!noCuotas && idTipoCuota === 2) {
        flag = false;
        errorMsg += 'Ingrese numero de cuotas\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'saveFactRecurrente',
            idClientes: $("#idClientes").val(),
            idProductos: idProductos,
            monto: monto,
            idTipoCuota: idTipoCuota,
            idPeriodoPago: idPeriodoPago,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin,
            noCuotas: noCuotas
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        alert('Ingresado exitosamente');
                        clear();
                        getFactRecurrente();
                        $("#loader").hide();
                        break;
                    default :
                        alert('Error al ingresar datos, comuniquese con el administrador del sistema');
                        $("#loader").hide();
                        break;
                }
            });
        }, 'json');
    }
}
//
function getFactRecurrente() {
    $("#loader").show();
    params = {
        service: 'getFactRecurrente',
        idClientes: $("#idClientes").val()
    };
    $.post('controllers/cajaController.php', params, function (data) {
        $("#detalleFR").html('');
        var datos = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='8' align='center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                datos += "<tr>";
                datos += "<td style='width: 1% !important;'>";
                datos += "<button class='btn btn-xs btn-danger' onclick='eliminarFactRecurrente(" + val.id + ")'><i class='fa fa-trash'></i></button>";
                datos += "</td>";
                datos += "<td class='text-center'>" + val.idProductos + "</td>";
                datos += "<td class='text-center'>" + accounting.formatNumber(val.monto, 2) + "</td>";
                datos += "<td class='text-center'>" + val.idTipoCuota + "</td>";
                datos += "<td class='text-center'>" + val.idPeriodoPago + "</td>";
                datos += "<td class='text-center'>" + val.fechaInicio + "</td>";
                datos += "<td class='text-center'>" + val.fechaFin + "</td>";
                datos += "<td class='text-center'>" + val.noCuotas + "</td>";
                datos += "</tr>";
            });
        }
        $("#detalleFR").append(datos);
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function eliminarFactRecurrente(item) {
    var r = confirm("¿Esta seguro de eliminar este registro?");
    if (r == true) {
        $("#loader").show();
        params = {
            service: 'eliminarFactRecurrente',
            item: item,
            idClientes: $("#idClientes").val()
        };
        $.post('controllers/cajaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Eliminado exitosamente');
                    getFactRecurrente();
                } else {
                    alert('Error al eliminar registro, comuniquese con el administrador del sistema');
                    $("#loader").hide();
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function loadNotasCredito() {
    $.post('views/cxc/notasCredito.php', function (respuesta) {
        $("#page-container").html(respuesta);
        $("#modulo").html('CXC');
        $("#opcion").html('Operacion de Notas de Credito');
        $('#fechaRecibo').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).val(today);
        loadDocumentos('notasCreditoClientes');
    }).done(function () {
        $("#loader").hide();
    });
}
//
function generarNotaCredito() {
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
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'generarNotaCredito',
            idTipoDocumento: '3',
            idDocumento: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            fechaDeposito: fechaRecibo,
            monto: monto,
            motivo: motivo,
            idClientes: idClientes,
            nit: nit,
            facturas: facturas
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Nota de credito generada exitosamente');
                    loadNotasCredito();
                } else {
                    alert('Error al ingresar deposito, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}