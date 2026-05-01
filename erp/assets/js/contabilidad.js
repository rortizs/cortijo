/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var contador = 1;
var totalDebe = 0;
var totalHaber = 0;
$(document).ready(function () {
    //loadPartida();
    //loadDiario();
    //loadMayor();
    //loadFormato(8);
    //loadRecepcionCompras();
    //loadLibroCompras();
    //loadLibroVentas();
});
//
//
function imprimirNomenclatura() {
    var reporte = "nomenclatura_pdf.php";
    var url = "views/jasper/" + reporte;
    $.redirect(url, '', 'POST', '_blank');
}
//
function loadFormato(option) {
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
    $.post('views/contabilidad/formatos.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
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
            getFormatoDetalle(idFormato);
        }
    });
}
//
function loadPartida(option) {
    var title = "Nueva Partida Manual";
    var idPartida;
    if (option !== undefined) {
        title = "Editar Partida Manual";
        $('.data').each(function () {
            if (this.checked) {
                idPartida = $(this).val();
            }
        });
        if (idPartida === undefined) {
            bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
                $('#modal1').modal('hide');
            });
            return false;
        }
    }
    params = {
        idPartida: idPartida
    };
    $.post('views/contabilidad/partidas.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html(title);
        $("#partida_at").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
    }).done(function () {
        if (idPartida === undefined) {
            getCorrelativoPartidas();
            $("#save").show();
            $("#update").hide();
        } else {
            $("#save").hide();
            $("#update").show();
            getPartidaDetalle(idPartida);
        }
        getTipoOperacionesPartidas();
    });
}
//
function getPartidaDetalle(idPartida) {
    params = {
        service: 'getPartidaDetalle',
        idPartida: idPartida
    };
    $.post('controllers/contabilidadController.php', params, function (data) {
        var table = 'partidaDetalle';
        var table1 = '"vw_nomenclatura"';
        var table2 = '"vw_centrosCosto"';
        var title1 = '"Cuentas Contables"';
        var title2 = '"Centros de Costo"';
        var modulo1 = '"cuentasContables"';
        var modulo2 = '"centrosCosto"';
        var totalDebe = 0;
        var totalHaber = 0;
        $.each(data, function (key, val) {
            totalDebe += accounting.unformat(val.debe);
            totalHaber += accounting.unformat(val.haber);
            var datos = "<tr id='item-" + contador + "'>";
            datos += "<td>";
            datos += "<button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + table1 + "," + title1 + "," + modulo1 + "," + contador + ");'>";
            datos += "<i class='fa fa-question'></i>";
            datos += "</button>";
            datos += "<input type='hidden' class='idNomenclatura' id='idNomenclaturaV-" + contador + "' value='" + val.idCuenta + "'/>";
            datos += "<input class='form-control input-sm' id='idNomenclatura-" + contador + "' readonly='' style='width: 93%;' value='" + val.cuentaContable + "'/>";
            datos += "</td>";
            datos += " <td>";
            datos += "<input type='text' class='form-control input-sm debe' id='debe' value='" + val.debe + "'/>";
            datos += "</td>";
            datos += "<td>";
            datos += " <input type='text' class='form-control input-sm haber' id='haber' value='" + val.haber + "'/>";
            datos += "</td>";
            datos += "<td>";
            datos += "<button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + table2 + "," + title2 + "," + modulo2 + "," + contador + ");'>";
            datos += "<i class='fa fa-question'></i>";
            datos += "</button>";
            datos += "<input type='hidden' class='idCentrosCosto' id='idCentrosCostoV-" + contador + "' value='" + val.idCentroCosto + "'/>";
            datos += "<input class='form-control input-sm' id='idCentrosCosto-" + contador + "' readonly='' style='width: 85%;' value='" + val.centroCosto + "'/>";
            datos += "</td>";
            datos += "<td align='center'>";
            datos += "<button class='btn btn-danger btn-sm' onclick='removeRowPartida(" + contador + ");'>";
            datos += "<i class='fa fa-trash-o'></i>";
            datos += "</button>";
            datos += "</td>";
            datos += "</tr>";
            $("#" + table + " tbody").append(datos);
            contador += 1;
            //
            $('.debe').on('keydown', function (e) {
                if (e.keyCode === 9 || e.keyCode === 13) {
                    if (totalDebe !== 0) {
                        totalDebe = 0;
                    }
                    $(".debe").each(function () {
                        totalDebe += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val()));
                        $("#totalDebe").val(accounting.formatNumber(totalDebe, 2));
                    });
                    //
                    var index = $('.debe').index(this) + 1;
                    $('.debe').eq(index).focus();
                    e.preventDefault();
                }
            });
            //
            $('.haber').on('keydown', function (e) {
                if (e.keyCode === 9 || e.keyCode === 13) {
                    if (totalHaber !== 0) {
                        totalHaber = 0;
                    }
                    $(".haber").each(function () {
                        totalHaber += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val()));
                        $("#totalHaber").val(accounting.formatNumber(totalHaber, 2));
                    });
                    //
                    var index = $('.haber').index(this) + 1;
                    $('.haber').eq(index).focus();
                    e.preventDefault();
                }
            });
        });
        $("#totalDebe").val(accounting.formatNumber(totalDebe, 2));
        $("#totalHaber").val(accounting.formatNumber(totalHaber, 2));
    }, 'json');
}
//
function getFormatoDetalle(idFormato) {
    params = {
        service: 'getFormatoDetalle',
        idFormato: idFormato
    };
    $.post('controllers/contabilidadController.php', params, function (data) {
        var table = 'formatoDetalle';
        var table1 = '"vw_nomenclatura"';
        var table2 = '"vw_centrosCosto"';
        var title1 = '"Cuentas Contables"';
        var title2 = '"Centros de Costo"';
        var modulo1 = '"cuentasContables"';
        var modulo2 = '"centrosCosto"';
        $.each(data, function (key, val) {
            var datos = "<tr id='item-" + contador + "'>";
            datos += "<td>";
            datos += "<button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + table1 + "," + title1 + "," + modulo1 + "," + contador + ");'>";
            datos += "<i class='fa fa-question'></i>";
            datos += "</button>";
            datos += "<input type='hidden' class='idNomenclatura' id='idNomenclaturaV-" + contador + "' value='" + val.idNomenclatura + "'/>";
            datos += "<input class='form-control input-sm' id='idNomenclatura-" + contador + "' readonly='' style='width: 93%;' value='" + val.cuentaContable + "'/>";
            datos += "</td>";
            datos += " <td>";
            datos += "<input type='hidden' id='nombreDebe-" + contador + "' value='" + val.debe + "'/>";
            datos += "<select class='form-control input-sm debe' id='debe-" + contador + "'></select>";
            datos += "</td>";
            datos += "<td>";
            datos += "<input type='hidden' id='nombreHaber-" + contador + "' value='" + val.haber + "'/>";
            datos += "<select class='form-control input-sm haber' id='haber-" + contador + "'></select>";
            datos += "</td>";
            datos += "<td>";
            datos += "<button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + table2 + "," + title2 + "," + modulo2 + "," + contador + ");'>";
            datos += "<i class='fa fa-question'></i>";
            datos += "</button>";
            datos += "<input type='hidden' class='idCentrosCosto' id='idCentrosCostoV-" + contador + "' value='" + val.idCentrosCosto + "'/>";
            datos += "<input class='form-control input-sm' id='idCentrosCosto-" + contador + "' readonly='' style='width: 85%;' value='" + val.centroCosto + "'/>";
            datos += "</td>";
            datos += "<td align='center'>";
            datos += "<button class='btn btn-danger btn-sm' onclick='removeRowPartida(" + contador + ");'>";
            datos += "<i class='fa fa-trash-o'></i>";
            datos += "</button>";
            datos += "</td>";
            datos += "</tr>";
            $("#" + table + " tbody").append(datos);
            getFormulas(contador);
            contador += 1;
        });
    }, 'json');
}
//
function addRowFormato(table) {
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
    datos += "<input class='form-control input-sm' id='idNomenclatura-" + contador + "' readonly='' style='width: 93%;'/>";
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
    datos += "<td align='center'>";
    datos += "<button class='btn btn-danger btn-sm' onclick='removeRowPartida(" + contador + ");'>";
    datos += "<i class='fa fa-trash-o'></i>";
    datos += "</button>";
    datos += "</td>";
    datos += "</tr>";
    $("#" + table + " tbody").append(datos);
    getFormulas(contador);
    contador += 1;
}
//
function addRowPartida(table) {
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
    datos += "<input class='form-control input-sm' id='idNomenclatura-" + contador + "' readonly='' style='width: 93%;'/>";
    datos += "</td>";
    datos += " <td>";
    datos += "<input type='text' class='form-control input-sm debe' id='debe'/>";
    datos += "</td>";
    datos += "<td>";
    datos += " <input type='text' class='form-control input-sm haber' id='haber'/>";
    datos += "</td>";
    datos += "<td>";
    datos += "<button class='btn btn-primary btn-sm' style='float: left;' onclick='busqueda(" + table2 + "," + title2 + "," + modulo2 + "," + contador + ");'>";
    datos += "<i class='fa fa-question'></i>";
    datos += "</button>";
    datos += "<input type='hidden' class='idCentrosCosto' id='idCentrosCostoV-" + contador + "'/>";
    datos += "<input class='form-control input-sm' id='idCentrosCosto-" + contador + "' readonly='' style='width: 85%;'/>";
    datos += "</td>";
    datos += "<td align='center'>";
    datos += "<button class='btn btn-danger btn-sm' onclick='removeRowPartida(" + contador + ");'>";
    datos += "<i class='fa fa-trash-o'></i>";
    datos += "</button>";
    datos += "</td>";
    datos += "</tr>";
    $("#" + table + " tbody").append(datos);
    contador += 1;
    //
    $('.debe').on('keydown', function (e) {
        if (e.keyCode === 9 || e.keyCode === 13) {
            if (totalDebe !== 0) {
                totalDebe = 0;
            }
            $(".debe").each(function () {
                totalDebe += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val()));
                $("#totalDebe").val(accounting.formatNumber(totalDebe, 2));
            });
            //
            var index = $('.debe').index(this) + 1;
            $('.debe').eq(index).focus();
            e.preventDefault();
        }
    });
    //
    $('.haber').on('keydown', function (e) {
        if (e.keyCode === 9 || e.keyCode === 13) {
            if (totalHaber !== 0) {
                totalHaber = 0;
            }
            $(".haber").each(function () {
                totalHaber += parseFloat($(this).val() === '' ? '0' : accounting.unformat($(this).val()));
                $("#totalHaber").val(accounting.formatNumber(totalHaber, 2));
            });
            //
            var index = $('.haber').index(this) + 1;
            $('.haber').eq(index).focus();
            e.preventDefault();
        }
    });
}
//
function getCuentaContable(idNomenclatura, cuenta, descripcion, item) {
    $("#idNomenclaturaV-" + item).val(idNomenclatura);
    $("#idNomenclatura-" + item).val(cuenta + ' - ' + descripcion);
    cancelarModal();
}
//
function getCentrosCosto(idCentrosCosto, cuenta, descripcion, item) {
    $("#idCentrosCostoV-" + item).val(idCentrosCosto);
    $("#idCentrosCosto-" + item).val(cuenta + ' - ' + descripcion);
    cancelarModal();
}
//
function getFormato(idFormato, descripcion) {
    $("#idFormato").val(idFormato);
    $("#formato").val(descripcion);
    cancelarModal();
}
//
function getTipoOperacionesPartidas() {
    params = {
        service: 'getTipoOperacionesPartidas'
    };
    //
    $.post('controllers/contabilidadController.php', params, function (data) {
        $("#idTipoOperacionPartida").append("<option value=''>[Seleccione...]</option>");
        $.each(data, function (key, val) {
            if ($("#idTipoOperacionPartidaEdit").val() === val.id) {
                $("#idTipoOperacionPartida").append("<option value='" + val.id + "' selected=''>" + val.descripcion + "</option>");
            } else {
                $("#idTipoOperacionPartida").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }
        });
    }, 'json');
}
//
function saveFormato() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var descripcion = $("#descripcion").val();
    var detalle = [];
    var debe = [];
    var haber = [];
    var centrosCosto = [];
    $(".debe").each(function (index) {
        debe.push($(this).find('option:selected').text());
    });
    $(".haber").each(function (index) {
        haber.push($(this).find('option:selected').text());
    });
    $(".idCentrosCosto").each(function (index) {
        centrosCosto.push(($(this).val() === '' ? '0' : $(this).val()));
    });
    $(".idNomenclatura").each(function (index) {
        var arr = {};
        arr['idNomenclatura'] = $(this).val();
        arr['debe'] = debe[index];
        arr['haber'] = haber[index];
        arr['idCentrosCosto'] = centrosCosto[index];
        detalle.push(arr);
    });
    if (!descripcion) {
        flag = false;
        errorMsg += 'Ingrese descripción de partida\n';
    }
    if (detalle.length === 0) {
        flag = false;
        errorMsg += 'Ingrese detalle de partida\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'saveFormato',
            descripcion: descripcion,
            detalle: detalle
        };
        $.post('controllers/contabilidadController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Partida ingresada exitosamente');
                    loadData('vw_formatos', 'Contabilidad', 'Formatos', 0, 0, 1);
                } else {
                    alert('Error al ingresar partida');
                }
            });
        }, 'json');
    }
}
//
function savePartida() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var numero = $("#numero").val();
    var partida_at = $("#partida_at").val();
    var descripcion = $("#descripcion").val();
    var idTipoOperacionPartida = $("#idTipoOperacionPartida").val();
    var idDocumento = $("#idDocumento").val();
    var detalle = [];
    var debe = [];
    var haber = [];
    var totalDebe = $("#totalDebe").val();
    var totalHaber = $("#totalHaber").val();
    var centrosCosto = [];
    $(".debe").each(function (index) {
        debe.push($(this).val());
    });
    $(".haber").each(function (index) {
        haber.push($(this).val());
    });
    $(".idCentrosCosto").each(function (index) {
        centrosCosto.push(($(this).val() === '' ? '0' : $(this).val()));
    });
    $(".idNomenclatura").each(function (index) {
        var arr = {};
        arr['idNomenclatura'] = $(this).val();
        arr['debe'] = debe[index];
        arr['haber'] = haber[index];
        arr['idCentrosCosto'] = centrosCosto[index];
        detalle.push(arr);
    });
    if (!numero) {
        flag = false;
        errorMsg += 'Debe crear un documento tipo "Partidas" para generar el número de partida en Configuraciones/Documentos\n';
    }
    if (!partida_at) {
        flag = false;
        errorMsg += 'Ingrese fecha de partida\n';
    }
    if (!descripcion) {
        flag = false;
        errorMsg += 'Ingrese descripción de partida\n';
    }
    if (!idTipoOperacionPartida) {
        flag = false;
        errorMsg += 'Seleccione tipo de partida\n';
    }
    if (detalle.length === 0) {
        flag = false;
        errorMsg += 'Ingrese detalle de partida\n';
    }
    if (!accounting.unformat(totalDebe) && !accounting.unformat(totalHaber)) {
        flag = false;
        errorMsg += 'Totales de partida no pueden estar vacios\n';
    }
    if (accounting.unformat(totalDebe) !== accounting.unformat(totalHaber)) {
        flag = false;
        errorMsg += 'Totales de partida no coinciden\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'savePartida',
            numero: numero,
            partida_at: partida_at,
            descripcion: descripcion,
            idTipoOperacionPartida: idTipoOperacionPartida,
            idDocumento: idDocumento,
            detalle: detalle
        };
        $.post('controllers/contabilidadController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Partida ingresada exitosamente');
                    loadData('vw_partidas', 'Contabilidad', 'Partidas Contables', 0, 0, 1);
                } else {
                    alert('Error al ingresar partida');
                }
            });
        }, 'json');
    }
}
//
function removeRowPartida(item, idItemForm) {
    var r = confirm("¿Esta seguro de eliminar esta fila?");
    if (r == true) {
        if (idItemForm === undefined) {
            $("#item-" + item).remove();
        } else {
            params = {
                service: 'deleteRowFormArqueoBoveda',
                id: idItemForm
            };
            $.post('controllers/adminController.php', params, function (data) {
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
function getCorrelativoPartidas() {
    params = {
        service: 'getCorrelativoPartidas'
    };
    //
    $.post('controllers/adminController.php', params, function (data) {
        $.each(data, function (key, val) {
            $("#numero").val(val.correlativo);
            $("#idDocumento").val(val.idDocumentos);
        });
    }, 'json');
}
//
function loadDiario() {
    $.post('views/contabilidad/diario.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Libro Diario');
    });
}
//
function imprimirDiario() {
    params = {
        yearInicial: $("#yearInicial option:selected").text(),
        mesInicial: $("#mesInicial").val(),
        mesInicialTXT: $("#mesInicial option:selected").text(),
        yearFinal: $("#yearFinal option:selected").text(),
        mesFinal: $("#mesFinal").val(),
        mesFinalTXT: $("#mesFinal option:selected").text(),
        partidaInicial: $("#partidaInicial").val(),
        partidaFinal: $("#partidaFinal").val(),
        idCentrosCosto: $("#idCentrosCosto").val(),
        tipoReporte: $("#tipoReporte").val(),
        folioInicio: $("#folioInicio").val()
    };
    var url = "views/contabilidad/diario_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadMayor() {
    $.post('views/contabilidad/mayor.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Libro Mayor');
    });
}
//
function imprimirMayor() {
    params = {
        yearInicial: $("#yearInicial option:selected").text(),
        mesInicial: $("#mesInicial").val(),
        mesInicialTXT: $("#mesInicial option:selected").text(),
        yearFinal: $("#yearFinal option:selected").text(),
        mesFinal: $("#mesFinal").val(),
        mesFinalTXT: $("#mesFinal option:selected").text(),
        cuentaInicial: $("#idNomenclatura-1").val(),
        cuentaFinal: $("#idNomenclatura-2").val(),
        idCentroCosto: $("#idCentrosCosto-1").val(),
        todosCentros: $("#todosCentros").val(),
        todasCuentas: $("#todasCuentas").val(),
        tipoReporte: $("#tipoReporte").val()
    };
    var url = "views/contabilidad/mayor_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function getFormulas(item) {
    params = {
        service: 'getFormulas'
    };
    //
    $.post('controllers/adminController.php', params, function (data) {
        $("#debe-" + item).append("<option value=''>[Seleccione...]</option>");
        $("#haber-" + item).append("<option value=''>[Seleccione...]</option>");
        $.each(data, function (key, val) {
            if ($("#nombreDebe-" + item).val() === val.descripcion) {
                $("#debe-" + item).append("<option value='" + val.id + "' selected=''>" + val.descripcion + "</option>");
            } else {
                $("#debe-" + item).append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }
            //
            if ($("#nombreHaber-" + item).val() === val.descripcion) {
                $("#haber-" + item).append("<option value='" + val.id + "' selected=''>" + val.descripcion + "</option>");
            } else {
                $("#haber-" + item).append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }
        });
    }, 'json');
}
//
function tablaImpuesto() {
    if ($("#valorFactura").val() === '') {
        alert('Ingrese valor de factura');
    } else {
        params = {
            service: 'getIDP'
        };
        $.post('controllers/contabilidadController.php', params, function (data) {
            var datos = "<table class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            datos += "<thead>";
            datos += "<tr>";
            datos += "<td>Tipo de Combustible</td>";
            datos += "<td>Factor</td>";
            datos += "<td>Galones</td>";
            datos += "<td>Exento</td>";
            datos += "</tr>";
            datos += "</thead>";
            $.each(data, function (key, val) {
                datos += "<tr>";
                datos += "<td>" + val.idCombustibles + "</td>";
                datos += "<td><input type='text' class='form-control input-sm' value='" + val.factor + "' readonly=''/></td>";
                datos += "<td><input type='text' class='form-control input-sm galones' id='galones-" + val.id + "' data-value='" + val.factor + "' data-id='" + val.id + "'/></td>";
                datos += "<td><input type='text' class='form-control input-sm exentos' id='exento-" + val.id + "' readonly=''/></td>";
                datos += "</tr>";
            });
            datos += "<thead>";
            datos += "<tr>";
            datos += "<td colspan='3'>Valor Exento</td>";
            datos += "<td><input type='text' class='form-control input-sm' id='valorExento' readonly=''/></td>";
            datos += "</tr>";
            datos += "</thead>";
            datos += "<tfoot>";
            datos += "<tr>";
            datos += "<td colspan='4' align='right'>";
            datos += "<button class='btn btn-primary btn-sm' id='continuarIDP'><i class='fa fa-check'></i> Continuar</button>";
            datos += " <button class='btn btn-danger btn-sm' onclick='cancelarModal();'><i class='fa fa-trash'></i> Cancelar</button>";
            datos += "</td>";
            datos += "</tfoot>";
            datos += "</table>";
            $("#modal1").modal('show');
            $("#myModalLabel").html('Tabla IDP');
            $("#controllers").html(datos);
        }, 'json').done(function () {
            $(".galones").on('keydown', function (e) {
                if (e.keyCode === 9 || e.keyCode === 13) {
                    var exento = (accounting.unformat($(this).val()) * $(this).data('value'));
                    $("#exento-" + $(this).data('id')).val(accounting.formatNumber(exento, 2));
                }
                var total = 0;
                $(".exentos").each(function () {
                    total += accounting.unformat($(this).val());
                });
                $("#valorExento").val(accounting.formatNumber(total, 2));
            });
            $("#continuarIDP").on('click', function () {
                var subTotalIDP = (accounting.unformat($("#valorFactura").val()) - accounting.unformat($("#valorExento").val()));
                var iva = accounting.formatNumber((subTotalIDP / 1.12 * 0.12 * 100) / 100, 2);
                var subTotal = accounting.formatNumber(subTotalIDP - accounting.unformat(iva), 2);
                $("#subTotal").val(subTotal);
                $("#iva").val(iva);
                $("#exento").val(accounting.formatNumber($("#valorExento").val(), 2));
                cancelarModal();
            });
        });
    }
}
//
function verPartida() {
    var idFormato = $("#idFormato").val();
    if (idFormato === '') {
        alert('Seleccione partida para visualizar su detalle');
        return false;
    } else {
        params = {
            service: 'getFormatoDetalle',
            idFormato: idFormato
        };
        $.post('controllers/contabilidadController.php', params, function (data) {
            var datos = "<table class='table table-striped table-bordered' cellspacing='0' width='100%'>";
            datos += "<thead>";
            datos += "<tr>";
            datos += "<td>Cuenta Contable</td>";
            datos += "<td>Debe</td>";
            datos += "<td>Haber</td>";
            datos += "<td>Centro Costo</td>";
            datos += "</tr>";
            datos += "</thead>";
            var totalDebe = 0;
            var totalHaber = 0;
            $.each(data, function (key, val) {
                totalDebe += accounting.unformat(($("input[name=" + (val.debe).toLowerCase() + "]").val() === undefined ? '0.00' : $("input[name=" + (val.debe).toLowerCase() + "]").val()));
                totalHaber += accounting.unformat(($("input[name=" + (val.haber).toLowerCase() + "]").val() === undefined ? '0.00' : $("input[name=" + (val.haber).toLowerCase() + "]").val()));
                datos += "<tr>";
                datos += "<td>" + val.cuentaContable + "</td>";
                datos += "<td align='right'>" + ($("input[name=" + (val.debe).toLowerCase() + "]").val() === undefined ? '0.00' : $("input[name=" + (val.debe).toLowerCase() + "]").val()) + "</td>";
                datos += "<td align='right'>" + ($("input[name=" + (val.haber).toLowerCase() + "]").val() === undefined ? '0.00' : $("input[name=" + (val.haber).toLowerCase() + "]").val()) + "</td>";
                datos += "<td>" + val.centroCosto + "</td>";
                datos += "</tr>";
            });
            datos += "<thead>";
            datos += "<tr>";
            datos += "<td>Totales</td>";
            datos += "<td align='right'>" + accounting.formatNumber(totalDebe, 2) + "</td>";
            datos += "<td align='right'>" + accounting.formatNumber(totalHaber, 2) + "</td>";
            datos += "<td>-</td>";
            datos += "</tr>";
            datos += "</thead>";
            datos += "</table>";
            $("#detallePartida").html(datos);
        }, 'json');
    }
}
//
function updatePartida() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var numero = $("#numero").val();
    var partida_at = $("#partida_at").val();
    var descripcion = $("#descripcion").val();
    var idTipoOperacionPartida = $("#idTipoOperacionPartida").val();
    var idDocumento = $("#idDocumento").val();
    var detalle = [];
    var debe = [];
    var haber = [];
    var totalDebe = $("#totalDebe").val();
    var totalHaber = $("#totalHaber").val();
    var centrosCosto = [];
    $(".debe").each(function (index) {
        debe.push($(this).val());
    });
    $(".haber").each(function (index) {
        haber.push($(this).val());
    });
    $(".idCentrosCosto").each(function (index) {
        centrosCosto.push(($(this).val() === '' ? '0' : $(this).val()));
    });
    $(".idNomenclatura").each(function (index) {
        var arr = {};
        arr['idNomenclatura'] = $(this).val();
        arr['debe'] = debe[index];
        arr['haber'] = haber[index];
        arr['idCentrosCosto'] = centrosCosto[index];
        detalle.push(arr);
    });
    if (!numero) {
        flag = false;
        errorMsg += 'Debe crear un documento tipo "Partidas" para generar el número de partida en Configuraciones/Documentos\n';
    }
    if (!partida_at) {
        flag = false;
        errorMsg += 'Ingrese fecha de partida\n';
    }
    if (!descripcion) {
        flag = false;
        errorMsg += 'Ingrese descripción de partida\n';
    }
    if (!idTipoOperacionPartida) {
        flag = false;
        errorMsg += 'Seleccione tipo de partida\n';
    }
    if (detalle.length === 0) {
        flag = false;
        errorMsg += 'Ingrese detalle de partida\n';
    }
    if (!accounting.unformat(totalDebe) && !accounting.unformat(totalHaber)) {
        flag = false;
        errorMsg += 'Totales de partida no pueden estar vacios\n';
    }
    if (accounting.unformat(totalDebe) !== accounting.unformat(totalHaber)) {
        flag = false;
        errorMsg += 'Totales de partida no coinciden\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'updatePartida',
            numero: numero,
            partida_at: partida_at,
            descripcion: descripcion,
            idTipoOperacionPartida: idTipoOperacionPartida,
            idDocumento: idDocumento,
            detalle: detalle,
            idPartida: $("#idPartida").val()
        };
//        console.log(params);
//        return false;
        $.post('controllers/contabilidadController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Partida actualizada exitosamente');
                    loadData('vw_partidas', 'Contabilidad', 'Partidas Contables', 0, 0, 1);
                } else {
                    alert('Error al ingresar partida');
                }
            });
        }, 'json');
    }
}
//
function updateFormato() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var descripcion = $("#descripcion").val();
    var detalle = [];
    var debe = [];
    var haber = [];
    var centrosCosto = [];
    $(".debe").each(function (index) {
        debe.push($(this).find('option:selected').text());
    });
    $(".haber").each(function (index) {
        haber.push($(this).find('option:selected').text());
    });
    $(".idCentrosCosto").each(function (index) {
        centrosCosto.push(($(this).val() === '' ? '0' : $(this).val()));
    });
    $(".idNomenclatura").each(function (index) {
        var arr = {};
        arr['idNomenclatura'] = $(this).val();
        arr['debe'] = debe[index];
        arr['haber'] = haber[index];
        arr['idCentrosCosto'] = centrosCosto[index];
        detalle.push(arr);
    });
    if (!descripcion) {
        flag = false;
        errorMsg += 'Ingrese descripción de partida\n';
    }
    if (detalle.length === 0) {
        flag = false;
        errorMsg += 'Ingrese detalle de partida\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            service: 'updateFormato',
            descripcion: descripcion,
            detalle: detalle,
            idFormato: $("#idFormato").val()
        };
        $.post('controllers/contabilidadController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Partida actualizada exitosamente');
                    loadData('vw_formatos', 'Contabilidad', 'Partidas Automáticas', 0, 0, 1);
                } else {
                    alert('Error al ingresar partida');
                }
            });
        }, 'json');
    }
}
//
function loadLibroCompras() {
    $.post('views/contabilidad/libroCompras.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Libro de Compras');
    }).done(function () {
        loadSucursalesEmpresa('idSucursales');
    });
}
//
function imprimirLibroCompras(exportType) {
    params = {
        yearInicial: $("#yearInicial option:selected").text(),
        mesInicial: $("#mesInicial").val(),
        mesInicialTXT: $("#mesInicial option:selected").text(),
        idSucursales: $("#idSucursales").val(),
        sucursalesTXT: $("#idSucursales option:selected").text(),
        exportType: exportType
    };
    var url = "views/contabilidad/libroCompras_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadLibroVentas() {
    $.post('views/contabilidad/libroVentas.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Libro de Ventas');
    }).done(function () {
        loadSucursalesEmpresa('idSucursales');
    });
}
//
function imprimirLibroVentas() {
    params = {
        yearInicial: $("#yearInicial option:selected").text(),
        mesInicial: $("#mesInicial").val(),
        mesInicialTXT: $("#mesInicial option:selected").text(),
        idSucursales: $("#idSucursales").val(),
        sucursalesTXT: $("#idSucursales option:selected").text()
    };
    var url = "views/contabilidad/libroVentas_pdf.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function loadBalanceSaldos() {
    $.post('views/contabilidad/balanceSaldos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Balance de Saldos');
        $("#yearFinal,#monthFinal").attr({
            disabled: true
        });
        $("#tipoReporte").on('change', function () {
            if ($(this).val() === '2') {
                $("#yearFinal,#monthFinal").attr({
                    disabled: false
                });
            } else {
                $("#yearFinal,#monthFinal").attr({
                    disabled: true
                });
            }
        });
    });
}
//
function imprimirBalanceSaldos() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!$("#tipoReporte").val()) {
        flag = false;
        errorMsg += 'Seleccione tipo de reporte\n';
    }
    if (!$("#yearInicial").val()) {
        flag = false;
        errorMsg += 'Seleccione año inicial\n';
    }
    if (!$("#yearFinal").val() && $("#tipoReporte").val() === '2') {
        flag = false;
        errorMsg += 'Seleccione año final\n';
    }
    if (!$("#mesInicial").val()) {
        flag = false;
        errorMsg += 'Seleccione mes inicial\n';
    }
    if (!$("#mesFinal").val() && $("#tipoReporte").val() === '2') {
        flag = false;
        errorMsg += 'Seleccione mes final\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            yearInicial: $("#yearInicial option:selected").text(),
            yearFinal: $("#yearFinal option:selected").text(),
            mesInicial: $("#mesInicial").val(),
            mesFinal: $("#mesFinal").val(),
            tipoReporte: $("#tipoReporte").val()
        };
        var reporte = "balanceSaldos_pdf.php";
        var url = "views/contabilidad/" + reporte;
        $.redirect(url, params, 'POST', '_blank');
    }
}
//
function loadCierreContables() {
    $.post('views/contabilidad/cierreContables.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Proceso de Cierre');
    });
}
//
function procesarCierreContable() {
}
//
function loadEstadoResultados() {
    $.post('views/contabilidad/estadoResultados.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Estados de Resultados');
        $("#yearFinal,#monthFinal").attr({
            disabled: true
        });
        $("#tipoReporte").on('change', function () {
            if ($(this).val() === '2') {
                $("#yearFinal,#monthFinal").attr({
                    disabled: false
                });
            } else {
                $("#yearFinal,#monthFinal").attr({
                    disabled: true
                });
            }
        });
    });
}
//
function imprimirEstadoResultados() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!$("#tipoReporte").val()) {
        flag = false;
        errorMsg += 'Seleccione tipo de reporte\n';
    }
    if (!$("#yearInicial").val()) {
        flag = false;
        errorMsg += 'Seleccione año inicial\n';
    }
    if (!$("#yearFinal").val() && $("#tipoReporte").val() === '2') {
        flag = false;
        errorMsg += 'Seleccione año final\n';
    }
    if (!$("#monthInicial").val()) {
        flag = false;
        errorMsg += 'Seleccione mes inicial\n';
    }
    if (!$("#monthFinal").val() && $("#tipoReporte").val() === '2') {
        flag = false;
        errorMsg += 'Seleccione mes final\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            yearInicial: $("#yearInicial option:selected").text(),
            yearFinal: $("#yearFinal option:selected").text(),
            mesInicial: $("#monthInicial").val(),
            mesInicialTXT: $("#monthInicial option:selected").text(),
            mesFinal: $("#monthFinal").val(),
            mesFinalTXT: $("#monthFinal option:selected").text(),
            tipoReporte: $("#tipoReporte").val()
        };
        var reporte = "estadoResultados_pdf.php";
        var url = "views/contabilidad/" + reporte;
        $.redirect(url, params, 'POST', '_blank');
    }
}
//
function loadBalanceGeneral() {
    $.post('views/contabilidad/balanceGeneral.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Balance General');
        $("#yearFinal,#monthFinal").attr({
            disabled: true
        });
        $("#tipoReporte").on('change', function () {
            if ($(this).val() === '2') {
                $("#yearFinal,#monthFinal").attr({
                    disabled: false
                });
            } else {
                $("#yearFinal,#monthFinal").attr({
                    disabled: true
                });
            }
        });
    });
}
//
function imprimirBalanceGeneral() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!$("#tipoReporte").val()) {
        flag = false;
        errorMsg += 'Seleccione tipo de reporte\n';
    }
    if (!$("#yearInicial").val()) {
        flag = false;
        errorMsg += 'Seleccione año inicial\n';
    }
    if (!$("#yearFinal").val() && $("#tipoReporte").val() === '2') {
        flag = false;
        errorMsg += 'Seleccione año final\n';
    }
    if (!$("#monthInicial").val()) {
        flag = false;
        errorMsg += 'Seleccione mes inicial\n';
    }
    if (!$("#monthFinal").val() && $("#tipoReporte").val() === '2') {
        flag = false;
        errorMsg += 'Seleccione mes final\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            yearInicial: $("#yearInicial option:selected").text(),
            yearFinal: $("#yearFinal option:selected").text(),
            mesInicial: $("#monthInicial").val(),
            mesFinal: $("#monthFinal").val(),
            tipoReporte: $("#tipoReporte").val()
        };
        var reporte = "balanceGeneral_pdf.php";
        var url = "views/contabilidad/" + reporte;
        $.redirect(url, params, 'POST', '_blank');
    }
}
//
function procesarCierreContable() {
    params = {
        service: 'procesarCierreContable',
        periodo: $("#periodo option:selected").text(),
        mes: $("#mes").val(),
        mesTxt: $("#mes option:selected").text()
    };
    $.post('views/contabilidad/cierreContables.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Contabilidad');
        $("#opcion").html('Proceso de Cierre');
    });
}