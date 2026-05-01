/**
 * FORM JS
 * @author Jonathan Juarez
 * @version 1.0 20170129
 */
var contador = 1;
var totalIngreso1 = 0;
var totalIngreso2 = 0;
var totalIngreso3 = 0;
var totalIngreso4 = 0;
var totalIngreso = 0;
var tasaCambio = 1;
var secciones = ['formArqueo1', 'formArqueo2', 'formArqueo3', 'formArqueo4'];
$(document).ready(function () {
    //loadForms();
    if (contador > 1) {
        contador = 1;
    }
});
function loadForms() {
    $.post('views/flujosBovedas/forms.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Cierre de Boveda Maquinas');
        $("#save").show();
        $("#update").hide();
        $("#cancel").show();
        loadSucursalesEmpresa('idSalas', $("#idEmpresaIngreso").val());
        $("table").hide();
    });
}
//
function getFormSala() {
    $.each(secciones, function (key, val) {
        getFormArqueoBoveda(val);
    });
}
//
function getFormArqueoBoveda(parent) {
    params = {
        service: 'getFormArqueoBoveda',
        idSucursales: $("#idSalas").val(),
        parent: parent
    };
    $.post('controllers/flujosBovedasController.php', params, function (data) {
        var datos = "";
        $("#" + parent + " tbody").html('');
        var table = '"vw_nomenclatura"';
        var title = '"Cuentas Contables"';
        var modulo = '"cuentasContables"';
        var tableCC = '"vw_centrosCosto"';
        var titleCC = '"Centros de Costo"';
        var moduloCC = '"centrosCosto"';
        var items = [];
        $.each(data, function (key, val) {
            var statusBtn = "";
            var statusInput = "";
            if (val.id === '28' || val.id === '29' || val.id === '30' || val.id === '38' || val.id === '39' || val.id === '40' || val.id === '11' || val.id === '12' || val.id === '13' || val.id === '18' || val.id === '19' || val.id === '8' || val.id === '22') {
                statusBtn = "hidden";
            }
            if (val.id === '13' || val.id === '18' || val.id === '19') {
                statusInput = "readonly=''";
            }
            var arr = {};
            arr['item'] = val.id;
            arr['operacion'] = val.operacion;
            items.push(arr);
            //
            datos += "<tr id='item-" + contador + "'>";
            datos += "<td><input type='text' class='form-control input-sm' value='" + val.descripcion + "' id='descripcion-" + val.id + "'/></td>";
            datos += "<td><input type='hidden' id='idNomenclaturaV-" + val.id + "' value='" + val.idNomenclatura + "'/><button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + table + "," + title + "," + modulo + "," + val.id + ");'><i class='fa fa-question'></i></button><input class='form-control input-sm nomenclatura' id='idNomenclatura-" + val.id + "' value='" + val.nomenclatura + "' readonly='' style='width: 85%;'/></td>";
            datos += "<td><input type='hidden' id='idCentrosCostoV-" + val.id + "' value='" + val.idCentrosCosto + "'/><button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + tableCC + "," + titleCC + "," + moduloCC + "," + val.id + ");'><i class='fa fa-question'></i></button><input class='form-control input-sm centrosCosto' id='idCentrosCosto-" + val.id + "' value='" + val.centrosCosto + "' readonly='' style='width: 85%;'/></td>";
            datos += "<td><select class='form-control input-sm' id='operacion-" + val.id + "'>";
            datos += "<option value=''>Seleccione</option>";
            datos += "<option value='1'>Suma</option>";
            datos += "<option value='2'>Resta</option>";
            datos += "</select></td>";
            datos += "<td><input type='text' class='form-control input-sm valor valor-" + parent + "'' data-id='" + val.id + "' id='idFormArqueoBoveda-" + val.id + "' " + statusInput + "/></td>";
            datos += "<td>\n\
                        <button class='btn btn-danger btn-sm " + statusBtn + "' onclick='removeRow(" + contador + "," + val.id + ");'><i class='fa fa-trash'></i></button>\n\
                        <button class='btn btn-primary btn-sm' onclick='updateRow(" + val.id + ");'><i class='fa fa-refresh'></i></button>\n\
                      </td>";
            datos += "</tr>";
            contador += 1;
        });
        $("#" + parent + " tbody").append(datos);
        operaciones(items);
        if (parent === 'formArqueo1') {
            saldosInicialesBovedas();
        }
        //
    }, 'json').done(function () {
        $("table").show();
        $('.valor, .valor-' + parent + '').on('keydown', function (e) {
            if (e.keyCode === 9) {
                if (tasaCambio !== 1) {
                    tasaCambio = 1;
                }
                if (totalIngreso1 !== 0) {
                    totalIngreso1 = 0;
                }
                if (totalIngreso2 !== 0) {
                    totalIngreso2 = 0;
                }
                if (totalIngreso3 !== 0) {
                    totalIngreso3 = 0;
                }
                if (totalIngreso4 !== 0) {
                    totalIngreso4 = 0;
                }
                if (totalIngreso !== 0) {
                    totalIngreso = 0;
                }
                //
                $(".valor-" + parent + "").each(function () {
                    var v1 = 0;
                    var v2 = 0;
                    var v3 = 0;
                    var v4 = 0;
                    var comision = 0;
                    var isr = 0;
                    var timbres = 0;
                    // CALCULO COMISIONES VISANET
                    if ($(this).data("id") === 12) {
                        //v1 = accounting.unformat($("#idFormArqueoBoveda-11").val());
                        v2 = accounting.unformat($(this).val());
                        comision = accounting.formatNumber((v2 / 1.06) * 0.06, 2);
                        $("#idFormArqueoBoveda-13").val(comision);
                    }
                    // END CALCULO COMISIONES VISANET
                    // CALCULOS 10% ISR & 3% TIMBRES
                    if ($(this).data("id") === 8) {
                        v3 = accounting.unformat($(this).val());
                        v4 = accounting.unformat($("#idFormArqueoBoveda-22").val());
                        isr = accounting.formatNumber(((v3 + v4) - ((v3 + v4) * 0.03)) * 0.10, 2);
                        timbres = accounting.formatNumber((v3 + v4) * 0.03, 2);
                        $("#idFormArqueoBoveda-18").val(isr);
                        $("#idFormArqueoBoveda-19").val(timbres);
                    }
                    if ($(this).data("id") === 22) {
                        v3 = accounting.unformat($("#idFormArqueoBoveda-8").val());
                        v4 = accounting.unformat($(this).val());
                        isr = accounting.formatNumber(((v3 + v4) - ((v3 + v4) * 0.03)) * 0.10, 2);
                        timbres = accounting.formatNumber((v3 + v4) * 0.03, 2);
                        $("#idFormArqueoBoveda-18").val(isr);
                        $("#idFormArqueoBoveda-19").val(timbres);
                    }
                    // END CALCULOS 10% ISR & 3% TIMBRES
                    if ($('.operacion-' + $(this).data("id")).val() === '') {
                        alert('Debe seleccionar operacion');
                        $('.operacion-' + $(this).data("id")).focus().select();
                        $('.operacion-' + $(this).data("id")).on('change', function () {
                            $('.valor-' + $(this).data("id")).focus();
                        });
                    } else {
                        if ($('#operacion-' + $(this).data("id")).val() === '1') {
                            switch (parent) {
                                case 'formArqueo1':
                                    totalIngreso1 += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                    $("#totalIngreso1").val(accounting.formatNumber(totalIngreso1, 2));
                                    break;
                                case 'formArqueo2':
                                    totalIngreso2 += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                    $("#totalIngreso2").val(accounting.formatNumber(totalIngreso2, 2));
                                    break;
                                case 'formArqueo3':
                                    totalIngreso3 += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val()));
                                    $("#totalIngreso3").val(accounting.formatNumber(parseFloat(totalIngreso3) + accounting.unformat($("#totalIngreso2").val()), 2)) * tasaCambio;
                                    break;
                                case 'formArqueo4':
                                    totalIngreso4 += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                    $("#totalIngreso4").val(accounting.formatNumber(totalIngreso4, 2));
                                    break;
                            }
                        } else {
                            switch (parent) {
                                case 'formArqueo1':
                                    totalIngreso1 -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                    $("#totalIngreso1").val(accounting.formatNumber(totalIngreso1, 2));
                                    break;
                                case 'formArqueo2':
                                    totalIngreso2 -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                    $("#totalIngreso2").val(accounting.formatNumber(totalIngreso2, 2));
                                    break;
                                case 'formArqueo3':
                                    totalIngreso3 -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val()));
                                    $("#totalIngreso3").val(accounting.formatNumber(parseFloat(totalIngreso3) + accounting.unformat($("#totalIngreso2").val()), 2)) * tasaCambio;
                                    break;
                                case 'formArqueo4':
                                    totalIngreso4 -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                    $("#totalIngreso4").val(accounting.formatNumber(totalIngreso4, 2));
                                    break;
                            }
                        }
                    }
                });
                $(".valor").each(function () {
                    //console.log($('.operacion-' + $(this).data("id")).val());
                    if ($('.operacion-' + $(this).data("id")).val() === '') {
                        alert('Debe seleccionar operacion');
                        $('.operacion-' + $(this).data("id")).focus().select();
                        $('.operacion-' + $(this).data("id")).on('change', function () {
                            $('.valor-' + $(this).data("id")).focus();
                        });
                    } else {
                        if ($('#operacion-' + $(this).data("id")).val() === '1') {
                            totalIngreso += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                        } else {
                            totalIngreso -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                        }
                        $("#totalIngreso").val(accounting.formatNumber(accounting.unformat(totalIngreso) - accounting.unformat($("#totalIngreso4").val()), 2));
                        $("#totalIngreso").val(accounting.formatNumber(accounting.unformat($("#totalIngreso").val()) - accounting.unformat($("#totalIngreso4").val()), 2));
                    }
                });
                //
                var index = $('.valor').index(this) + 1;
                $('.valor').eq(index).focus();
                e.preventDefault();
            }
        });
    });
}

function addRow(table) {
    var tableV = '"vw_nomenclatura"';
    var tableCC = '"vw_centrosCosto"';
    var parent = '"' + table + '"';
    var title = '"Cuentas Contables"';
    var titleCC = '"Centros de Costo"';
    var modulo = '"cuentasContables"';
    var moduloCC = '"centrosCosto"';
    //
    var datos = "<tr id='item-" + contador + "'>";
    datos += "<td><input type='text' class='form-control input-sm' value='' id='descripcion-" + contador + "'/></td>";
    datos += "<td><input type='hidden' id='idNomenclaturaV-" + contador + "' value=''/><button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + tableV + "," + title + "," + modulo + "," + contador + ");'><i class='fa fa-question'></i></button><input class='form-control input-sm nomenclatura' id='idNomenclatura-" + contador + "' value='' readonly='' style='width: 85%;'/></td>";
    datos += "<td><input type='hidden' id='idCentrosCostoV-" + contador + "' value=''/><button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + tableCC + "," + titleCC + "," + moduloCC + "," + contador + ");'><i class='fa fa-question'></i></button><input class='form-control input-sm centrosCosto' id='idCentrosCosto-" + contador + "' value='' readonly='' style='width: 85%;'/></td>";
    datos += "<td><select class='form-control input-sm' id='operacion-" + contador + "'>";
    datos += "<option value=''>Seleccione</option>";
    datos += "<option value='1'>Suma</option>";
    datos += "<option value='2'>Resta</option>";
    datos += "</select></td>";
    datos += "<td><input type='text' class='form-control input-sm valor valor-" + table + "'' data-id='" + contador + "'/></td>";
    datos += "<td>\n\
                <button class='btn btn-danger btn-sm' onclick='removeRow(" + contador + ");'><i class='fa fa-trash'></i></button>\n\
                <button class='btn btn-primary btn-sm' onclick='saveRow(" + contador + "," + parent + ");'><i class='fa fa-floppy-o'></i></button>\n\
              </td>";
    datos += "</tr>";
    $("#" + table + " tbody").append(datos);
    contador += 1;
    //
    $('.valor, .operacion-' + contador + ', .valor-' + table + '').on('keydown', function (e) {
        if (e.which === 9) {
            if (tasaCambio !== 1) {
                tasaCambio = 1;
            }
            if (totalIngreso1 !== 0) {
                totalIngreso1 = 0;
            }
            if (totalIngreso2 !== 0) {
                totalIngreso2 = 0;
            }
            if (totalIngreso3 !== 0) {
                totalIngreso3 = 0;
            }
            if (totalIngreso4 !== 0) {
                totalIngreso4 = 0;
            }
            if (totalIngreso !== 0) {
                totalIngreso = 0;
            }
            $(".valor-" + table + "").each(function () {
                if ($('.operacion-' + $(this).data("id")).val() === '') {
                    alert('Debe seleccionar operacion');
                    $('.operacion-' + $(this).data("id")).focus().select();
                    $('.operacion-' + $(this).data("id")).on('change', function () {
                        $('.valor-' + $(this).data("id")).focus();
                    });
                } else {
                    if ($('#operacion-' + $(this).data("id")).val() === '1') {
                        switch (table) {
                            case 'formArqueo1':
                                totalIngreso1 += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                $("#totalIngreso1").val(accounting.formatNumber(totalIngreso1, 2));
                                break;
                            case 'formArqueo2':
                                totalIngreso2 += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                $("#totalIngreso2").val(accounting.formatNumber(totalIngreso2, 2));
                                break;
                            case 'formArqueo3':
                                totalIngreso3 += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val()));
                                $("#totalIngreso3").val(accounting.formatNumber(parseFloat(totalIngreso3) + accounting.unformat($("#totalIngreso2").val()), 2)) * tasaCambio;
                                break;
                            case 'formArqueo4':
                                totalIngreso4 += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                $("#totalIngreso4").val(accounting.formatNumber(totalIngreso4, 2));
                                break;
                        }
                    } else {
                        switch (table) {
                            case 'formArqueo1':
                                totalIngreso1 -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                $("#totalIngreso1").val(accounting.formatNumber(totalIngreso1, 2));
                                break;
                            case 'formArqueo2':
                                totalIngreso2 -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                                $("#totalIngreso2").val(accounting.formatNumber(totalIngreso2, 2));
                                break;
                            case 'formArqueo3':
                                totalIngreso3 -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val()));
                                $("#totalIngreso3").val(accounting.formatNumber(parseFloat(totalIngreso3) + accounting.unformat($("#totalIngreso2").val()), 2)) * tasaCambio;
                                break;
                        }
                    }
                }
            });
            $(".valor").each(function () {
                //console.log($('.operacion-' + $(this).data("id")).val());
                if ($('.operacion-' + $(this).data("id")).val() === '') {
                    alert('Debe seleccionar operacion');
                    $('.operacion-' + $(this).data("id")).focus().select();
                    $('.operacion-' + $(this).data("id")).on('change', function () {
                        $('.valor-' + $(this).data("id")).focus();
                    });
                } else {
                    if ($('.operacion-' + $(this).data("id")).val() === '1') {
                        totalIngreso += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                    } else {
                        totalIngreso -= parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val())) * tasaCambio;
                    }
                    $("#totalIngreso").val(accounting.formatNumber(accounting.unformat(totalIngreso) - accounting.unformat($("#totalIngreso4").val()), 2));
                    $("#totalIngreso").val(accounting.formatNumber(accounting.unformat($("#totalIngreso").val()) - accounting.unformat($("#totalIngreso4").val()), 2));
                }
            });
            //
            var index = $('.valor').index(this) + 1;
            $('.valor').eq(index).focus();
            e.preventDefault();
        }
    });
}
//
function saveRow(item, parent) {
    var descripcion = $("#descripcion-" + item).val();
    var idNomenclatura = $("#idNomenclaturaV-" + item).val();
    var idCentrosCosto = $("#idCentrosCostoV-" + item).val();
    var operacion = $("#operacion-" + item).val();
    params = {
        service: 'saveRowFormArqueoBoveda',
        id: item,
        descripcion: descripcion,
        idNomenclatura: (idNomenclatura === '' ? '0' : idNomenclatura),
        idCentrosCosto: (idCentrosCosto === '' ? '0' : idCentrosCosto),
        operacion: operacion,
        parent: parent,
        idSucursales:$("#idSalas").val()
    };
//    console.log(params);
//    return false;
    $.post('controllers/flujosBovedasController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                alert('Fila agregada exitosamente');
                getFormArqueoBoveda(parent);
            } else {
                alert('Error al agregar fila');
            }
        });
    }, 'json');
}
//
function removeRow(item, idItemForm) {
    var r = confirm("¿Esta seguro de eliminar esta fila?");
    if (r == true) {
        if (idItemForm === undefined) {
            $("#item-" + item).remove();
        } else {
            params = {
                service: 'deleteRowFormArqueoBoveda',
                id: idItemForm
            };
            $.post('controllers/flujosBovedasController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Fila eliminada exitosamente');
                        $("#item-" + item).remove();
                    } else {
                        alert('Error al eliminar fila');
                    }
                });
            }, 'json');
        }
    } else {
        return false;
    }
}
//
function updateRow(item) {
    var descripcion = $("#descripcion-" + item).val();
    var idNomenclatura = $("#idNomenclaturaV-" + item).val();
    var idCentrosCosto = $("#idCentrosCostoV-" + item).val();
    var operacion = $("#operacion-" + item).val();
    params = {
        service: 'updateRowFormArqueoBoveda',
        id: item,
        descripcion: descripcion,
        idNomenclatura: idNomenclatura,
        idCentrosCosto: idCentrosCosto,
        operacion: operacion
    };
    $.post('controllers/flujosBovedasController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                alert('Fila actualizada exitosamente');
            } else {
                alert('Error al actualizar fila');
            }
        });
    }, 'json');
}
//
function operaciones(items) {
    $.each(items, function (key, val) {
        //console.log(val.item,' ',val.operacion);
        $("#operacion-" + val.item + " option").each(function () {
            if ($(this).val() === val.operacion) {
                $(this).attr("selected", "selected");
            }
        });
    });
}
//
function guardarArqueo() {
    var r = confirm("¿Esta seguro de procesar este arqueo?");
    if (r == true) {
        var detalle = [];
        $(".valor").each(function (index) {
            var arr = {};
            arr['idFormArqueoBoveda'] = $(this).data("id");
            arr['valor'] = $(this).val();
            detalle.push(arr);
        });
        params = {
            service: 'guardarArqueoMaquinas',
            idSalas: $("#idSalas").val(),
            fechaArqueo: $("#fechaArqueo .form-control").val(),
            totalIngreso1: $("#totalIngreso1").val(),
            totalIngreso2: $("#totalIngreso2").val(),
            totalIngreso3: $("#totalIngreso3").val(),
            totalIngreso4: $("#totalIngreso4").val(),
            totalIngreso: $("#totalIngreso").val(),
            detalle: detalle
        };
//        console.log(params);
//        return false;
        $.post('controllers/flujosBovedasController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Arqueo guardado exitosamente');
                    loadForms();
                } else if (val.message === 'exists') {
                    alert('Ya existe un arqueo en el dia seleccionado');
                } else {
                    alert('Error al guardar arqueo');
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function saldosInicialesBovedas() {
    params = {
        service: 'saldosInicialesBovedas',
        idSalas: $("#idSalas").val()
    };
    $.post('controllers/flujosBovedasController.php', params, function (data) {
        if (data !== null) {
            var total = 0;
            $.each(data, function (key, val) {
                if (key === 0) {
                    $('#fechaArqueo').datetimepicker({
                        pickTime: false,
                        format: 'DD-MM-YYYY',
                        defaultDate: val.arqueo_at,
                        maxDate: val.arqueo_at
                    });
                }
                total += accounting.unformat(val.valor);
                if (val.idFormArqueoBoveda === '28') {
                    $("#idFormArqueoBoveda-38").val(val.valor);
                }
                if (val.idFormArqueoBoveda === '29') {
                    $("#idFormArqueoBoveda-39").val(val.valor);
                }
                if (val.idFormArqueoBoveda === '30') {
                    $("#idFormArqueoBoveda-40").val(val.valor);
                }
            });
            //console.log(total);
            $("#totalIngreso1").val(accounting.formatNumber(total, 2));
            $("#totalIngreso").val(accounting.formatNumber(total, 2));
        } else {
            $(".valor").val('');
            $("#totalIngreso1").val('');
            $("#totalIngreso").val('');
        }
    }, 'json');
}
//
function consultarCierre() {
    params = {
        service: 'consultarCierre',
        idSalas: $("#idSalas").val(),
        fechaArqueo: $("#fechaArqueo .form-control").val()
    };
    $.post('controllers/flujosBovedasController.php', params, function (data) {
        var total = 0;
        var total1 = 0;
        var total2 = 0;
        var total3 = 0;
        var total4 = 0;
        if (data === null) {
            alert('No hay información ingresada en la sala y dia seleccionado');
            loadForms();
            return false;
        } else {
            //$(".valor").val('');
            $("#save").hide();
            $("#update").show();
            $("#cancel").show();
            $.each(data, function (key, val) {
                if (key === 0) {
                    $("#idArqueoBoveda").val(val.idArqueoBoveda);
                }
                $("#idFormArqueoBoveda-" + val.idFormArqueoBoveda).val(accounting.formatNumber(val.valor, 2));
                switch (val.parent) {
                    case 'formArqueo1':
                        if (val.operacion === '1') {
                            total1 += accounting.unformat(val.valor);
                        } else {
                            total1 -= accounting.unformat(val.valor);
                        }
                        break;
                    case 'formArqueo2':
                        if (val.operacion === '1') {
                            total2 += accounting.unformat(val.valor);
                        } else {
                            total2 -= accounting.unformat(val.valor);
                        }
                        break;
                    case 'formArqueo3':
                        if (val.operacion === '1') {
                            total3 += accounting.unformat(val.valor);
                        } else {
                            total3 -= accounting.unformat(val.valor);
                        }
                        break;
                    case 'formArqueo4':
                        if (val.operacion === '1') {
                            total4 += accounting.unformat(val.valor);
                        } else {
                            total4 -= accounting.unformat(val.valor);
                        }
                        break;
                }
            });
            total = (total1 + total2 + total3) - total4;
            $("#totalIngreso1").val(accounting.formatNumber(total1, 2));
            $("#totalIngreso2").val(accounting.formatNumber(total2, 2));
            $("#totalIngreso3").val(accounting.formatNumber(total3 + total2, 2));
            $("#totalIngreso4").val(accounting.formatNumber(total4, 2));
            $("#totalIngreso").val(accounting.formatNumber(total, 2));
        }
    }, 'json');
}
//
function updateArqueo() {
    var r = confirm("¿Esta seguro de actualizar este arqueo?");
    if (r == true) {
        var detalle = [];
        $(".valor").each(function (index) {
            var arr = {};
            arr['idFormArqueoBoveda'] = $(this).data("id");
            arr['valor'] = $(this).val();
            detalle.push(arr);
        });
        params = {
            service: 'updateArqueoMaquinas',
            idArqueoBoveda: $("#idArqueoBoveda").val(),
            totalIngreso1: $("#totalIngreso1").val(),
            totalIngreso2: $("#totalIngreso2").val(),
            totalIngreso3: $("#totalIngreso3").val(),
            totalIngreso4: $("#totalIngreso4").val(),
            totalIngreso: $("#totalIngreso").val(),
            detalle: detalle
        };
//        console.log(params);
//        return false;
        $.post('controllers/flujosBovedasController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Arqueo actualizado exitosamente');
                    loadForms();
                } else {
                    alert('Error al actualizar arqueo');
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function cancelUpdateArqueo(flag) {
    if ($("#idArqueoBoveda").val() === '') {
        alert('Debe buscar el cierre para poder eliminarlo');
    } else {
        var r = confirm("¿Esta seguro de eliminar este cierre?");
        if (r == true) {
            params = {
                service: 'eliminarCierre',
                idArqueoBoveda: $("#idArqueoBoveda").val(),
                flag: flag
            };
            $.post('controllers/flujosBovedasController.php', params, function (data) {
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Cierre eliminado exitosamente');
                        switch (flag) {
                            case '1':
                                loadForms();
                                break;
                            case '2':
                                loadForms2();
                                break;
                        }
                    } else {
                        alert('Error al eliminar cierre');
                    }
                });
            }, 'json');
        } else {
            return false;
        }
    }

}