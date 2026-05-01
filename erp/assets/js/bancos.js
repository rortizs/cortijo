/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var abono = 0;
var facturas = null;
$(document).ready(function () {
    //loadCheques();
    //loadDepositos();
    //loadDocumentosBancarios();
});
function getFacturasCxp(idDocumento) {
    params2 = {
        service: 'getFacturasCxp',
        idDocumento: idDocumento
    };
    console.log(params2);
    var totalAbonos = 0;
    $.post('controllers/bancosController.php', params2, function (data) {
        var datos = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='5' align='center' class='warning text-warning'><b>No existen facturas liquidadas con este cheque</b></td>";
            datos += "</tr>";
        } else {
            $("#detalle").html('');
            $.each(data, function (key, val) {
                totalAbonos += accounting.unformat(val.abono);
                datos += "<tr>";
                datos += "<td>" + val.factura + "</td>";
                datos += "<td>" + val.concepto + "</td>";
                datos += "<td>" + val.fechaPago + "</td>";
                datos += "<td style='text-align:right;'>" + val.saldo + "</td>";
                datos += "<td style='text-align:right;'>" + val.abono + "</td>";
                datos += "</tr>";
            });
            $("#totalAbonos").val(accounting.formatNumber(totalAbonos, 2));

        }
        $("#detalle").append(datos);
    }, 'json');
}

function getFacturasCxc(idDocumento) {
    params2 = {
        service: 'getFacturasCxc',
        idDocumento: idDocumento
    };
    console.log(params2);
    var totalAbonos = 0;
    $.post('controllers/bancosController.php', params2, function (data) {
        var datos = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='5' align='center' class='warning text-warning'><b>No existen facturas liquidadas con este cheque</b></td>";
            datos += "</tr>";
        } else {
            $("#detalle").html('');
            $.each(data, function (key, val) {
                totalAbonos += accounting.unformat(val.abono);
                datos += "<tr>";
                datos += "<td>" + val.factura + "</td>";
                datos += "<td>" + val.concepto + "</td>";
                datos += "<td>" + val.fecha + "</td>";
                datos += "<td style='text-align:right;'>" + val.saldo + "</td>";
                datos += "<td style='text-align:right;'>" + val.abono + "</td>";
                datos += "</tr>";
            });
            $("#totalAbonos").val(accounting.formatNumber(totalAbonos, 2));

        }
        $("#detalle").append(datos);
    }, 'json');
}

function loadCheques(idCheque) {
    params = {
        idCheque: idCheque
    };
    $.post('views/bancos/cheques.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Bancos');
        $("#opcion").html('Cheques');
        $("#fechaCheque,#fechaCobro").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
        $("#fechaCobro").attr('disabled', true);
        loadDocumentos('cheques', $("#correlativo").val());
    }).done(function () {
        if (idCheque !== undefined) {
            switch ($("#status").val()) {
                case '1':
                    $(".btn-cxp").hide();
                    $(".btn-save").hide();
                    $(".btn-update").show();
                    $(".btn-print").show();
                    $(".btn-anular").show();
                    //$(".btn-eliminar").show();
                    $(".btn-conciliar").hide();
                    $("#btnCuentaBancaria,#tipoDocumento").attr('disabled', true);
                    break;
                case '2':
                    $(".btn-cxp").hide();
                    $(".btn-save").hide();
                    $(".btn-update").hide();
                    $(".btn-print").hide();
                    $(".btn-anular").show();
                    //$(".btn-eliminar").hide();
                    $(".btn-conciliar").show();
                    $(".form-control").attr('disabled', true);
                    $("#fechaCobro").attr({
                        disabled: false,
                        readonly: true
                    });
                    break;
                default:
                    $(".btn-cxp").hide();
                    $(".btn-save").hide();
                    $(".btn-update").hide();
                    $(".btn-print").hide();
                    $(".btn-anular").hide();
                    //$(".btn-eliminar").hide();
                    $(".btn-conciliar").hide();
                    $(".form-control").attr('disabled', true);
                    break;
            }
        } else {
            $(".btn-cxp").show();
            $(".btn-save").show();
            $(".btn-update").hide();
            $(".btn-print").hide();
            $(".btn-anular").hide();
            //$(".btn-eliminar").hide();
            $(".btn-conciliar").hide();
            $(".statusBancos").hide();
        }
    });
    getFacturasCxp(idCheque);
}
//
function getCuentaBancaria(idCuenta, numero, nombre, banco, saldoLibro, saldoBanco) {
    $("#idCuentaBancaria").val(idCuenta);
    $("#cuentaBancaria").val(numero);
    $("#nombreCuenta").val(nombre);
    $("#banco").val(banco);
    $("#saldoLibros").val(saldoLibro);
    $("#saldoBanco").val(saldoBanco);
    cancelarModal();
}
//
function guardarCheque() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var idCuentaBancaria = $("#idCuentaBancaria").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var noCheque = $("#noCheque").val();
    var fechaCheque = $("#fechaCheque").val();
    var fechaCobro = $("#fechaCobro").val();
    var monto = accounting.unformat($("#monto").val());
    var nombreCheque = $("#nombre").val();
    var motivo = $("#motivo").val();
    var idFormato = $("#idFormato").val();
    var idProveedores = $("#idProveedores").val();
    var totalAbonos = $("#totalAbonos").val();
    var noNegociable = 0;
    if ($('#noNegociable').prop('checked')) {
        noNegociable = 1;
    }
    //VALIDACIONES
    if (!idCuentaBancaria) {
        flag = false;
        errorMsg += 'Seleccione cuenta bancaria\n';
    }
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!noCheque) {
        flag = false;
        errorMsg += 'Ingrese numero de cheque\n';
    }
    if (!fechaCheque) {
        flag = false;
        errorMsg += 'Ingrese fecha de cheque\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de cheque\n';
    }
    if (!nombreCheque) {
        flag = false;
        errorMsg += 'Ingrese pago a la orden de\n';
    }
    if (!motivo) {
        flag = false;
        errorMsg += 'Ingrese motivo de cheque\n';
    }
    if (idProveedores !== "" && monto > totalAbonos) {
        flag = false;
        errorMsg += 'Total de abonos menor al monto \n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'guardarCheque',
            idCuentaBancaria: idCuentaBancaria,
            idTipoDocumento: '2',
            idTipoDocumentoBanco: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            noCheque: noCheque,
            fechaCheque: fechaCheque,
            fechaCobro: fechaCobro,
            monto: monto,
            nombreCheque: nombreCheque,
            motivo: motivo,
            idFormato: idFormato,
            idProveedores: idProveedores,
            facturas: facturas,
            noNegociable: noNegociable,
            idCajaChica: $("#idCajaChica").val(),
            idTipoLiquidaciones: accounting.unformat($("#idTipoLiquidaciones").val()),
            moduloLiquidaciones: $("#moduloLiquidaciones").val()
        };
        //console.log(params);
        //return false;
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        alert('Cheque ingresado exitosamente');
                        loadData('vw_cheques', 'Bancos', 'Listado de Cheques', 0, 0, 0);
                        break;
                    case 'docExists':
                        alert('Numero de cheque ya fue ingresado en el sistema');
                        break;
                    default:
                        alert('Error al ingresar cheque, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json');
    }
}
//

function getCheque() {
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
        loadCheques(id);
    }
}
//
function getDeposito() {
    var id;
    $('input[type=checkbox]').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        loadDepositos(id);
    }
}
//
function getNCBancos() {
    var id;
    $('input[type=checkbox]').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        loadNCBancos(id, 'update');
    }
}
//
function getNDBancos() {
    var id;
    $('input[type=checkbox]').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        loadNDBancos(id);
    }
}
//
function cxp() {
    params = {
        service: 'cxp',
        idProveedores: $("#idProveedores").val()
    };
    $.post('controllers/bancosController.php', params, function (data) {
        var datos = "";
        var saldoActual = 0;
        var totalFacturas = 0;
        $("#detalle").html('');
        $("#monto").val('');
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
                datos += "<td style='text-align:right;'><input type='text' class='form-control input-sm abono' data-id='" + val.idCompras + "' data-title='" + val.factura + "' data-value='" + val.saldo + "'/></td>";
                datos += "</tr>";

            });
        }
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
                        facturas.splice(findIndexCXP(facturas, $(this).data("id")), 1);
                        //ADICIONA EL ELEMENTO NUEVO
                        $("#motivo").append($(this).data("title") + '; ');
                        var arr = {};
                        arr['idCompras'] = $(this).data("id");
                        arr['saldo'] = accounting.unformat($(this).data("value"));
                        arr['factura'] = $(this).data("title");
                        arr['abono'] = accounting.unformat($(this).val());
                        facturas.push(arr);
                    } else {
                        if (accounting.unformat($(this).val()) <= accounting.unformat($(this).data("value")) && accounting.unformat($(this).val()) > 0) {
                            $("#motivo").append($(this).data("title") + '; ');
                            var arr = {};
                            arr['idCompras'] = $(this).data("id");
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
                    facturas.splice(findIndexCXP(facturas, $(this).data("id")), 1);
                }
                $("#totalAbonos").val(accounting.formatNumber(abono, 2));
                if (accounting.unformat($("#totalAbonos").val()) > $("#monto").val()) {
                    alert('Total de abonos ingresado es mayor a monto de cheque');
                    $("#totalAbonos").val(accounting.formatNumber((accounting.unformat($("#totalAbonos").val()) - accounting.unformat($(this).val())), 2));
                    $(this).val('');
                    $("#motivo").html($("#motivo").val().replace($(this).data("title") + '; ', ''));
                    facturas.splice(findIndexCXP(facturas, $(this).data("id")), 1);
                }
                //console.log(facturas);
            }
        });
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
        $("#monto").val('');
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

function loadDepositos(idDeposito) {
    params = {
        idDeposito: idDeposito
    };
    $.post('views/bancos/depositos.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Bancos');
        $("#opcion").html('Depositos');
        $("#fechaDeposito").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false,
            maxDate: today
        });
        $("#fechaDeposito").val(today).attr('readonly', true);
        loadDocumentos('depositos');
    });
    //getFacturasCxc(idDeposito);
}
//
function guardarDeposito() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var idCuentaBancaria = $("#idCuentaBancaria").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var noBoleta = $("#noBoleta").val();
    var fechaDeposito = $("#fechaDeposito").val();
    var monto = accounting.unformat($("#monto").val());
    var nombreDeposito = $("#nombre").val();
    var motivo = $("#motivo").val();
    var idFormato = $("#idFormato").val();
    var idClientes = $("#idClientes").val();
    var nit = $("#nit").val();
    var totalAbonos = $("#totalAbonos").val();
    //VALIDACIONES
    if (!idCuentaBancaria) {
        flag = false;
        errorMsg += 'Seleccione cuenta bancaria\n';
    }
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!noBoleta) {
        flag = false;
        errorMsg += 'Ingrese numero de boleta del deposito\n';
    }
    if (!fechaDeposito) {
        flag = false;
        errorMsg += 'Ingrese fecha de deposito\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de cheque\n';
    }
    if (!nombreDeposito) {
        flag = false;
        errorMsg += 'Ingrese nombre de la persona que realizo el deposito\n';
    }
    if (!motivo && idClientes !== "") {
        flag = false;
        errorMsg += 'Ingrese motivo del deposito\n';
    }
    //  if (!idFormato) {
    //      flag = false;
    //      errorMsg += 'Seleccione partida contable\n';
    //  }
    if (monto > totalAbonos) {
        flag = false;
        errorMsg += 'Total de abonos menor al monto \n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'guardarDeposito',
            idCuentaBancaria: idCuentaBancaria,
            idTipoDocumento: '4',
            idTipoDocumentoD: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            noBoleta: noBoleta,
            fechaDeposito: fechaDeposito,
            monto: monto,
            nombreDeposito: nombreDeposito,
            motivo: motivo,
            idFormato: idFormato,
            idClientes: idClientes,
            nit: nit,
            facturas: facturas
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Deposito ingresado exitosamente');
                    var url = pathJasper + "bancos_pdf.php?documento=1&idDeposito=" + val.idDeposito;
                    window.open(url);
                    loadData('vw_depositos', 'Bancos', 'Listado de Depositos', 0, 0, 0);
                } else {
                    console.log(val.error);
                    alert('Error al ingresar deposito, comuniquese con el administrador del sistema');
                    $("#loader").hide();
                }
            });
        }, 'json');
    }

}
//
function loadNDBancos(idNDBancos, action) {
    params = {
        idNDBancos: idNDBancos
    };
    $.post('views/bancos/notasDebito.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Bancos');
        $("#opcion").html('Notas de Debito');
        $("#fechaND").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
        $("#fechaND").attr('readonly', true);
        loadDocumentos('notasDebitoBancos');
        switch (action) {
            case 'update':
                $("#save").hide();
                $("#update").show();
                $("#noNotaDebito").attr('disabled', true);
                $("#tipoDocumento").attr('disabled', true);
                break;
            default :
                $("#save").show();
                $("#update").hide();
                break;
        }
    });
}
//
function guardarNDBancos() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var idCuentaBancaria = $("#idCuentaBancaria").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var noNotaDebito = $("#noNotaDebito").val();
    var fechaND = $("#fechaND").val();
    var monto = accounting.unformat($("#monto").val());
    var nombrePagoND = $("#nombre").val();
    var motivo = $("#motivo").val();
    var idFormato = $("#idFormato").val();
    var idProveedores = $("#idProveedores").val();
    var totalAbonos = $("#totalAbonos").val();
    //VALIDACIONES
    if (!idCuentaBancaria) {
        flag = false;
        errorMsg += 'Seleccione cuenta bancaria\n';
    }
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!noNotaDebito) {
        flag = false;
        errorMsg += 'Ingrese numero de nota de debito\n';
    }
    if (!fechaND) {
        flag = false;
        errorMsg += 'Ingrese fecha de nota de debito\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de nota de debito\n';
    }
    if (!nombrePagoND) {
        flag = false;
        errorMsg += 'Ingrese nombre de la persona que realizo nota de debito\n';
    }
    if (!motivo) {
        flag = false;
        errorMsg += 'Ingrese motivo de la nota de debito\n';
    }
    //   if (!idFormato) {
    //       flag = false;
    //       errorMsg += 'Seleccione partida contable\n';
    //   }
    if (idProveedores !== "" && monto > totalAbonos) {
        flag = false;
        errorMsg += 'Total de abonos menor al monto \n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'guardarNDBancos',
            idCuentaBancaria: idCuentaBancaria,
            idTipoDocumento: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            noNotaDebito: noNotaDebito,
            fechaND: fechaND,
            monto: monto,
            nombrePagoND: nombrePagoND,
            motivo: motivo,
            idFormato: idFormato,
            idProveedores: idProveedores,
            facturas: facturas
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Nota de Debito ingresada exitosamente');
                    var url = pathJasper + "bancos_pdf.php?documento=3&idNDBancos=" + val.idNDBancos;
                    window.open(url);
                    loadData('vw_notasDebitoBancos', 'Bancos', 'Listado de Notas de Debito', 0, 0, 0);
                } else {
                    console.log(val.error);
                    alert('Error al ingresar Nota de Debito, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function loadNCBancos(idNCBancos, action) {
    params = {
        idNCBancos: idNCBancos
    };
    $.post('views/bancos/notasCredito.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Bancos');
        $("#opcion").html('Notas de Credito');
        $("#fechaNC").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
        $("#fechaNC").attr('readonly', true);
        loadDocumentos('notasCreditoBancos');
        switch (action) {
            case 'update':
                $("#save").hide();
                $("#update").show();
                $("#noNotaCredito").attr('disabled', true);
                $("#tipoDocumento").attr('disabled', true);
                break;
            default :
                $("#save").show();
                $("#update").hide();
                break;
        }
    });
}
//
function guardarNCBancos() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var idCuentaBancaria = $("#idCuentaBancaria").val();
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var noNotaCredito = $("#noNotaCredito").val();
    var fechaNC = $("#fechaNC").val();
    var monto = accounting.unformat($("#monto").val());
    var nombrePagoNC = $("#nombre").val();
    var motivo = $("#motivo").val();
    var idFormato = $("#idFormato").val();
    var idClientes = $("#idClientes").val();
    var totalAbonos = $("#totalAbonos").val();
    //VALIDACIONES
    if (!idCuentaBancaria) {
        flag = false;
        errorMsg += 'Seleccione cuenta bancaria\n';
    }
    if (!tipoDocumento || !correlativo) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!noNotaCredito) {
        flag = false;
        errorMsg += 'Ingrese numero de nota de credito\n';
    }
    if (!fechaNC) {
        flag = false;
        errorMsg += 'Ingrese fecha de nota de credito\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de nota de credito\n';
    }
    if (!nombrePagoNC) {
        flag = false;
        errorMsg += 'Ingrese nombre de la persona que realizo nota de credito\n';
    }
    if (!motivo) {
        flag = false;
        errorMsg += 'Ingrese motivo de la nota de credito\n';
    }
    //  if (!idFormato) {
    //      flag = false;
    //      errorMsg += 'Seleccione partida contable\n';
    //  }
    if (idClientes !== "" && monto > totalAbonos) {
        flag = false;
        errorMsg += 'Total de abonos menor al monto \n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'guardarNCBancos',
            idCuentaBancaria: idCuentaBancaria,
            idTipoDocumento: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            noNotaCredito: noNotaCredito,
            fechaNC: fechaNC,
            monto: monto,
            nombrePagoNC: nombrePagoNC,
            motivo: motivo,
            idFormato: idFormato,
            idClientes: idClientes,
            facturas: facturas
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Nota de Credito ingresada exitosamente');
                    var url = pathJasper + "bancos_pdf.php?documento=2&idNCBancos=" + val.idNCBancos;
                    window.open(url);
                    loadData('vw_notasCreditoBancos', 'Bancos', 'Listado de Notas de Credito', 0, 0, 0);
                } else {
                    console.log(val.error);
                    alert('Error al ingresar Nota de Credito, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function imprimirCheque(idCheque) {
    var r = confirm("¿Esta seguro de imprimir este cheque?");
    if (r == true) {
        params = {
            service: 'impresionCheque',
            idCheque: idCheque
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    var url = pathJasper + "cheque.php?idCheque=" + idCheque;
                    window.open(url);
                    loadData('vw_cheques', 'Bancos', 'Listado de Cheques', 0, 0, 0);
                } else {
                    console.log(val.error);
                    alert('Error al imprimir cheque, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function anularCheque(idCheque) {
    var r = confirm("¿Esta seguro de anular este cheque?");
    if (r == true) {
        params = {
            service: 'anularCheque',
            idCheque: idCheque,
            monto: $("#monto").val(),
            idPartida: $("#idPartida").val(),
            idCuentaBancaria: $("#idCuentaBancaria").val()
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Cheque anulado exitosamente');
                    loadData('vw_cheques', 'Bancos', 'Listado de Cheques', 0, 0, 0);
                } else {
                    console.log(val.message);
                    console.log(val.error);
                    alert('Error al anular cheque, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function conciliarCheque(idCheque) {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var fechaCobro = $("#fechaCobro").val();
    if (!fechaCobro) {
        flag = false;
        errorMsg += 'Ingrese fecha de cobro del cheque\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var r = confirm("¿Esta seguro de conciliar este cheque?");
        if (r == true) {
            params = {
                service: 'conciliarCheque',
                idCheque: idCheque,
                monto: $("#monto").val(),
                fechaCobro: $("#fechaCobro").val(),
                idCuentaBancaria: $("#idCuentaBancaria").val()
            };
            $.post('controllers/bancosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Cheque conciliado exitosamente');
                        loadData('vw_cheques', 'Bancos', 'Listado de Cheques', 0, 0, 0);
                    } else {
                        console.log(val.message);
                        console.log(val.error);
                        alert('Error al conciliar cheque, comuniquese con el administrador del sistema');
                    }
                });
            }, 'json');
        }
    }
}
//
function eliminarCheque(idCheque) {
    var r = confirm("¿Esta seguro de eliminar este cheque?");
    if (r == true) {
        params = {
            service: 'eliminarCheque',
            idCheque: idCheque,
            monto: $("#monto").val(),
            idPartida: $("#idPartida").val(),
            idCuentaBancaria: $("#idCuentaBancaria").val()
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Cheque eliminado exitosamente');
                    loadData('vw_cheques', 'Bancos', 'Listado de Cheques', 0, 0, 0);
                } else {
                    console.log(val.message);
                    console.log(val.error);
                    alert('Error al eliminar cheque, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function loadReporteSaldoLibros() {
    $.post('views/bancos/saldoEnLibros.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Bancos');
        $("#opcion").html('Reporte Saldo en Libros');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).val(today);
    });
}
//
function imprimirReporteSaldoLibros() {
    params = {
        fechaInicio: $("#fechaInicio").val(),
        fechaFin: $("#fechaFin").val(),
        idCuentaBancaria: $("#idCuentaBancaria").val()
    };
    var url = "views/bancos/saldoEnLibros_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadReporteSaldoBancos() {
    $.post('views/bancos/saldoEnBancos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Bancos');
        $("#opcion").html('Reporte Saldo en Bancos');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).val(today);
    });
}
//
function imprimirReporteSaldoBancos() {
    params = {
        fechaInicio: $("#fechaInicio").val(),
        fechaFin: $("#fechaFin").val(),
        idCuentaBancaria: $("#idCuentaBancaria").val()
    };
    var url = "views/bancos/saldoEnBancos_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function eliminarDeposito() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var r = confirm("¿Esta seguro de cancelar este deposito?");
        if (r == true) {
            params = {
                service: 'eliminarDeposito',
                idDeposito: id
            };
            $.post('controllers/bancosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        loadData('vw_depositos', 'Bancos', 'Listado de Depositos');
                    } else {
                        alert('Error al eliminar deposito, comuniquese con el administrador del sistema');
                    }
                });
            }, 'json');
        } else {
            return false;
        }
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
function eliminarNDBancos() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var r = confirm("¿Esta seguro de eliminar este documento?");
        if (r == true) {
            params = {
                service: 'eliminarNDBancos',
                idNDBancos: id
            };
            $.post('controllers/bancosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        loadData('vw_notasDebitoBancos', 'Bancos', 'Listado de Notas de Debito');
                    } else {
                        alert('Error al eliminar este documento, comuniquese con el administrador del sistema');
                    }
                });
            }, 'json');
        } else {
            return false;
        }
    }
}
//
function eliminarNCBancos() {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        var r = confirm("¿Esta seguro de eliminar este documento?");
        if (r == true) {
            params = {
                service: 'eliminarNCBancos',
                idNCBancos: id
            };
            $.post('controllers/bancosController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        loadData('vw_notasCreditoBancos', 'Bancos', 'Listado de Notas de Credito');
                    } else {
                        alert('Error al eliminar este documento, comuniquese con el administrador del sistema');
                    }
                });
            }, 'json');
        } else {
            return false;
        }
    }
}
//
function updateNCBancos(idNCBancos) {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var fechaNC = $("#fechaNC").val();
    var monto = accounting.unformat($("#monto").val());
    var nombrePagoNC = $("#nombre").val();
    var motivo = $("#motivo").val();
    var idFormato = $("#idFormato").val();
    //VALIDACIONES
    if (!fechaNC) {
        flag = false;
        errorMsg += 'Ingrese fecha de nota de credito\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de nota de credito\n';
    }
    if (!nombrePagoNC) {
        flag = false;
        errorMsg += 'Ingrese nombre de la persona que realizo nota de credito\n';
    }
    if (!motivo) {
        flag = false;
        errorMsg += 'Ingrese motivo de la nota de credito\n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'updateNCBancos',
            idNCBancos: accounting.unformat(idNCBancos),
            fechaNC: fechaNC,
            monto: monto,
            nombrePagoNC: nombrePagoNC,
            motivo: motivo,
            idFormato: accounting.unformat(idFormato)
        };
        //console.log(params);
        //return false;
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Nota de Credito actualizada exitosamente');
                    loadData('vw_notasCreditoBancos', 'Bancos', 'Listado de Notas de Credito', 0, 0, 0);
                } else {
                    console.log(val.error);
                    alert('Error al actualizar Nota de Credito, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function getNDBancos() {
    var id;
    $('input[type=checkbox]').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        loadNDBancos(id, 'update');
    }
}
//
function updateNDBancos(idNDBancos) {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var fechaND = $("#fechaND").val();
    var monto = accounting.unformat($("#monto").val());
    var nombrePagoND = $("#nombre").val();
    var motivo = $("#motivo").val();
    var idFormato = $("#idFormato").val();
    //VALIDACIONES
    if (!fechaND) {
        flag = false;
        errorMsg += 'Ingrese fecha de nota de credito\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de nota de credito\n';
    }
    if (!nombrePagoND) {
        flag = false;
        errorMsg += 'Ingrese nombre de la persona que realizo nota de credito\n';
    }
    if (!motivo) {
        flag = false;
        errorMsg += 'Ingrese motivo de la nota de credito\n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'updateNDBancos',
            idNDBancos: accounting.unformat(idNDBancos),
            fechaND: fechaND,
            monto: monto,
            nombrePagoND: nombrePagoND,
            motivo: motivo,
            idFormato: accounting.unformat(idFormato)
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Nota de Debito actualizada exitosamente');
                    loadData('vw_notasDebitoBancos', 'Bancos', 'Listado de Notas de Debito', 0, 0, 0);
                } else {
                    console.log(val.error);
                    alert('Error al actualizar Nota de Debito, comuniquese con el administrador del sistema');
                }
            });
        }, 'json');
    }
}
//
function findIndexCXP(array, item) {
    var index;
    $.each(array, function (key, val) {
        if (val.idCompras === item) {
            index = key;
        }
    });
    return index;
}
//
function crearCajaChica() {
    $.post('views/bancos/cajaChica.php', function (respuesta) {
        $("#controllers").html(respuesta);
        $("#myModalLabel").html('Crear Liquidacion');
        loadDocumentos('liquidaciones');
    }).done(function () {
        $("#loader").hide();
        $("#modal1").modal('show');
        $("#tipoLiquidacion").on('change', function () {
            if ($(this).val() === '1') {
                $("#monto").attr('disabled', true);
            } else {
                $("#monto").attr('disabled', false);
            }
        });
    });
}
//
function guardarCajaChica() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var tipoDocumento = $("#tipoDocumento option:selected").text();
    var idTipoDocumento = $("#tipoDocumento").val();
    var correlativo = $("#correlativo").val();
    var entregadoA = $("#entregadoA").val();
    var monto = accounting.unformat($("#monto").val());
    var descripcion = $("#descripcion").val();
    var tipoLiquidacion = accounting.unformat($("#tipoLiquidacion").val());
    if (!tipoDocumento) {
        flag = false;
        errorMsg += 'Seleccione un documento para generar correlativo\n';
    }
    if (!tipoLiquidacion) {
        flag = false;
        errorMsg += 'Seleccione tipo de liquidacion\n';
    }
    if (!entregadoA) {
        flag = false;
        errorMsg += 'Ingrese campo entragado a\n';
    }
    if (!monto && tipoLiquidacion === 2) {
        flag = false;
        errorMsg += 'Ingrese campo monto\n';
    }
    if (!descripcion) {
        flag = false;
        errorMsg += 'Ingrese campo descripcion\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'guardarCajaChica',
            idDocumento: idTipoDocumento,
            tipoDocumento: tipoDocumento,
            correlativo: correlativo,
            entregadoA: entregadoA,
            monto: monto,
            descripcion: descripcion,
            tipoLiquidacion: tipoLiquidacion
        };
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Caja chica creada exitosamente');
                    loadData('vw_cajaChica', 'Bancos', 'Listado de Cajas Chicas', 0, 0, 0);
                    $("#loader").hide();
                    cancelarModal();
                } else {
                    alert('Error al crear caja chica, comuniquese con el administrador del sistema');
                    $("#loader").hide();
                }
            });
        }, 'json');
    }
}
//
function imprimirLiquidacionCajaChica() {
    var id;
    var action
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
            action = $(this).data("value");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        switch (action) {
            case 3:
                var r = confirm("¿Esta seguro de cerrar esta caja chica?");
                if (r == true) {
                    params = {
                        service: 'cerrarCajaChica',
                        idCajaChica: id
                    };
                    $.post('controllers/bancosController.php', params, function (data) {
                        $.each(data, function (key, val) {
                            if (val.message === 'success') {
                                alert('Caja chica cerrada exitosamente');
                                var url = pathJasper + "liquidacionesCajaChica.php?idCajaChica=" + params.idCajaChica;
                                window.open(url);
                                loadData('vw_cajaChica', 'Bancos', 'Listado de Cajas Chicas', 0, 0, 0);
                                $("#loader").hide();
                                cancelarModal();
                            } else {
                                alert('Error al cerrar caja chica, comuniquese con el administrador del sistema');
                                $("#loader").hide();
                            }
                        });
                    }, 'json');
                }
                break;
            case 2:
                var url = pathJasper + "liquidacionesCajaChica.php?idCajaChica=" + id;
                window.open(url);
                break;
            default:
                alert('Caja chica solo se puede cerrar en estatus CHEQUE ASIGNADO');
                break;
        }

    }
}
//
function getCajaChica(idCajaChica, documento, monto, responsable, descripcion, tipoLiquidacion, modulo) {
    $("#idCajaChica").val(idCajaChica);
    $("#cajaChicaDoc").val(documento);
    $("#monto").val(monto);
    $("#nombre").val(responsable);
    $("#motivo").val(descripcion);
    var idTipoLiquidacion;
    switch (tipoLiquidacion) {
        case 'CAJA CHICA':
            idTipoLiquidacion = 1;
            break;
        case 'GASTOS':
            idTipoLiquidacion = 2;
            break;
    }
    $("#idTipoLiquidaciones").val(idTipoLiquidacion);
    $("#moduloLiquidaciones").val(modulo);
    cancelarModal();
}
//
function abrirCajaChica() {
    var id;
    var action
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
            action = $(this).data("value");
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        switch (action) {
            case 2:
                var r = confirm("¿Esta seguro de abrir esta caja chica?");
                if (r == true) {
                    params = {
                        service: 'abrirCajaChica',
                        idCajaChica: id
                    };
                    $.post('controllers/bancosController.php', params, function (data) {
                        $.each(data, function (key, val) {
                            if (val.message === 'success') {
                                alert('Caja chica abierta exitosamente');
                                loadData('vw_cajaChica', 'Bancos', 'Listado de Cajas Chicas', 0, 0, 0);
                                $("#loader").hide();
                                cancelarModal();
                            } else {
                                alert('Error al abrir caja chica, comuniquese con el administrador del sistema');
                                $("#loader").hide();
                            }
                        });
                    }, 'json');
                }
                break;
            default:
                alert('Caja chica solo se puede volver a abrir en estatus CERRADA');
                break;
        }
    }
}
//
function actualizarCheque() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var idCuentaBancaria = $("#idCuentaBancaria").val();
    var noCheque = $("#noCheque").val();
    var fechaCheque = $("#fechaCheque").val();
    var fechaCobro = $("#fechaCobro").val();
    var monto = accounting.unformat($("#monto").val());
    var nombreCheque = $("#nombre").val();
    var motivo = $("#motivo").val();
    var idFormato = $("#idFormato").val();
    var idProveedores = $("#idProveedores").val();
    var totalAbonos = $("#totalAbonos").val();
    var noNegociable = 0;
    if ($('#noNegociable').prop('checked')) {
        noNegociable = 1;
    }
    //VALIDACIONES
    if (!noCheque) {
        flag = false;
        errorMsg += 'Ingrese numero de cheque\n';
    }
    if (!fechaCheque) {
        flag = false;
        errorMsg += 'Ingrese fecha de cheque\n';
    }
    if (!monto) {
        flag = false;
        errorMsg += 'Ingrese monto de cheque\n';
    }
    if (!nombreCheque) {
        flag = false;
        errorMsg += 'Ingrese pago a la orden de\n';
    }
    if (!motivo) {
        flag = false;
        errorMsg += 'Ingrese motivo de cheque\n';
    }
    if (idProveedores !== "" && monto > totalAbonos) {
        flag = false;
        errorMsg += 'Total de abonos menor al monto \n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'actualizarCheque',
            idCheques: $("#idCheques").val(),
            idCuentaBancaria: idCuentaBancaria,
            noCheque: noCheque,
            fechaCheque: fechaCheque,
            fechaCobro: fechaCobro,
            monto: monto,
            nombreCheque: nombreCheque,
            motivo: motivo,
            idFormato: idFormato,
            idProveedores: idProveedores,
            facturas: facturas,
            noNegociable: noNegociable,
            idCajaChica: $("#idCajaChica").val(),
            idTipoLiquidaciones: accounting.unformat($("#idTipoLiquidaciones").val()),
            moduloLiquidaciones: $("#moduloLiquidaciones").val()
        };
        //console.log(params);
        //return false;
        $.post('controllers/bancosController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        alert('Cheque actualizado exitosamente');
                        loadData('vw_cheques', 'Bancos', 'Listado de Cheques', 0, 0, 0);
                        break;
                    case 'docExists':
                        alert('Numero de cheque ya fue ingresado en el sistema');
                        break;
                    default:
                        alert('Error al actualizar cheque, comuniquese con el administrador del sistema');
                        break;
                }
            });
        }, 'json');
    }
}
//
//
function reImprimirDocBancos(documento) {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        switch (documento) {
            case 1:
                var url = pathJasper + "bancos_pdf.php?documento=" + documento + "&idDeposito=" + id;
                window.open(url);
                break;
            case 2:
                var url = pathJasper + "bancos_pdf.php?documento=" + documento + "&idNCBancos=" + id;
                window.open(url);
                break;
            case 3:
                var url = pathJasper + "bancos_pdf.php?documento=" + documento + "&idNDBancos=" + id;
                window.open(url);
                break;
        }
    }
}
//
function loadDocumentosBancarios() {
    $.post('views/bancos/documentosBancarios.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Bancos');
        $("#opcion").html('Documentos Bancarios');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio").val(today);
        $("#fechaFin").val(today);
    });
}
//
function imprimirDocumentosBancarios() {
    params = {
        idCuentaBancaria: $("#idCuentaBancaria").val(),
        tipoDocumento: $("#tipoDocumento").val(),
        fechaInicio: $("#fechaInicio").val(),
        fechaFin: $("#fechaFin").val()
    };
    var url = "views/bancos/documentosBancarios_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadConciliacionBancaria() {
    $.post('views/bancos/conciliacionBancaria.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Bancos');
        $("#opcion").html('Conciliacion Bancaria');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        }).val(today);
    });
}
//
function imprimirConciliacionBancaria() {
    params = {
        fechaInicio: $("#fechaInicio").val(),
        fechaFin: $("#fechaFin").val(),
        idCuentaBancaria: $("#idCuentaBancaria").val()
    };
    var url = "views/bancos/conciliacionBancaria_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}