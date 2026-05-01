$(document).ready(function () {
    //constructorPlanilla(3);
    //loadSabanaPlanilla();
});
//
function loadSabanaPlanilla() {
    $.post('views/planilla/sabanaPlanilla.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Planilla');
        $("#opcion").html('Nominas');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
    }).done(function () {
        $('#myTable').DataTable({
            "searching": false,
            "bPaginate": false,
            "bInfo": false,
            "ordering": false
        });
        //SUMA DE VALORES POR FILA
        var fila = $("#myTable").find("tr").length;
        for (var i = 0, max = fila; i < max; i++) {
            var sum1 = 0;
            var sum2 = 0;
            //PAGOS
            $(".dataL1-" + i).each(function () {
                var value = accounting.unformat($(this).text());
                // add only if the value is number
                if (!isNaN(value) && value.length != 0) {
                    sum1 += value;
                }
            });
            //DESCUENTOS
            $(".dataL2-" + i).each(function () {
                var value = accounting.unformat($(this).text());
                // add only if the value is number
                if (!isNaN(value) && value.length != 0) {
                    sum2 += value;
                }
            });
            $(".resultL1-" + i).html(accounting.formatNumber(sum1, 2));
            $(".resultL2-" + i).html(accounting.formatNumber(sum2, 2));
            //PAGOS-DESCUENTOS
            $(".resultLR-" + i).html(accounting.formatNumber((sum1 - sum2), 2));
        }
        //SUMA DE VALORES POR COLUMNA
        var columns = $("#myTable").find("tr:first td").length;
        for (var i = 0, max = columns; i < max; i++) {
            var sum = 0;
            $(".data-" + i).each(function () {
                var value = accounting.unformat($(this).text());
                // add only if the value is number
                if (!isNaN(value) && value.length != 0) {
                    sum += value;
                }
            });
            $(".result-" + i).html(accounting.formatNumber(sum, 2));
        }
    });
}
//
function loadConstructorSabana() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var idHrmPlanillas = $("#idHrmPlanillas").val();
    var periodo = $("#periodo option:selected").text();
    var mes = accounting.unformat($("#mes").val());
    //VALIDACIONES
    if (!idHrmPlanillas) {
        flag = false;
        errorMsg += 'Seleccione planilla a generar\n';
    }
    if (periodo === 'Seleccione...') {
        flag = false;
        errorMsg += 'Seleccione periodo de generacion\n';
    }
    if (!mes) {
        flag = false;
        errorMsg += 'Seleccione mes de generacion\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            idHrmPlanillas: idHrmPlanillas,
            periodo: periodo,
            mes: mes
        };
        $.post('views/planilla/constructorSabana.php', params, function (respuesta) {
            $('#constructorSabana').html(respuesta);
        }).done(function () {
            $('#myTable').DataTable({
                "searching": false,
                "bPaginate": false,
                "bInfo": false,
                "ordering": false
            });
            //SUMA DE VALORES POR FILA
            var fila = $("#myTable").find("tr").length;
            for (var i = 0, max = fila; i < max; i++) {
                var sum1 = 0;
                var sum2 = 0;
                //PAGOS
                $(".dataL1-" + i).each(function () {
                    var value = accounting.unformat($(this).text());
                    // add only if the value is number
                    if (!isNaN(value) && value.length != 0) {
                        sum1 += value;
                    }
                });
                //DESCUENTOS
                $(".dataL2-" + i).each(function () {
                    var value = accounting.unformat($(this).text());
                    // add only if the value is number
                    if (!isNaN(value) && value.length != 0) {
                        sum2 += value;
                    }
                });
                $(".resultL1-" + i).html(accounting.formatNumber(sum1, 2));
                $(".resultL2-" + i).html(accounting.formatNumber(sum2, 2));
                //PAGOS-DESCUENTOS
                $(".resultLR-" + i).html(accounting.formatNumber((sum1 - sum2), 2));
            }
            //SUMA DE VALORES POR COLUMNA
            var columns = $("#myTable").find("tr:first td").length;
            for (var i = 0, max = columns; i < max; i++) {
                var sum = 0;
                $(".data-" + i).each(function () {
                    var value = accounting.unformat($(this).text());
                    // add only if the value is number
                    if (!isNaN(value) && value.length != 0) {
                        sum += value;
                    }
                });
                $(".result-" + i).html(accounting.formatNumber(sum, 2));
            }
            $("#validate").val(1);
        });
    }
}
//
function imprimirConstructorSabana() {
    params = {
        idHrmPlanillas: $("#idHrmPlanillas").val(),
        periodo: $("#periodo option:selected").text(),
        mes: accounting.unformat($("#mes").val())
    };
    var url = "views/planilla/sabanaPlanilla-excel.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function constructorPlanilla(idHrmPlanilla) {
    var id;
    if (idHrmPlanilla === undefined) {
        $('.data').each(function () {
            if (this.checked) {
                id = $(this).val();
            }
        });
    } else {
        id = idHrmPlanilla;
    }
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        params = {
            idHrmPlanillas: id
        };
        $.post('views/planilla/constructorPlanilla.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $("#modulo").html('Planilla');
            $("#opcion").html('Constructor de Planilla');
        }).done(function () {
            $("#loader").hide();
            $(".variables").on('click', function () {
                $("#valor").focus().val($('#valor').val() + $(this).val());
            });
            $("#add").show();
            $("#update").hide();
            //
            $("#idTipoCampo").on('change', function () {
                switch (accounting.unformat($(this).val())) {
                    case 1:
                        $("#idTipoOperacion").attr('disabled', true);
                        $("#valorMaximo").attr('disabled', true);
                        break;
                    case 3:
                        $("#valor").attr('disabled', true);
                        $("#valorMaximo").attr('disabled', false);
                        break;
                    case 4:
                        $("#valor").attr('disabled', true);
                        $("#valorMaximo").attr('disabled', true);
                        break;
                    case 5:
                        $("#valor").attr('disabled', true);
                        $("#valorMaximo").attr('disabled', true);
                        break;
                    case 6:
                        $("#valor").attr('disabled', true);
                        $("#idTipoOperacion").attr('disabled', true);
                        $("#valorMaximo").attr('disabled', true);
                        break;
                    default:
                        $("#valor").attr('disabled', false);
                        $("#idTipoOperacion").attr('disabled', false);
                        $("#valorMaximo").attr('disabled', true);
                        break;
                }
            });
        });
    }
}
//
function saveCampoConstructorPlanilla() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var nombreCampo = $("#nombreCampo").val();
    var valor = $("#valor").val();
    var idTipoValor = accounting.unformat($("#idTipoValor").val());
    var orden = $("#orden").val();
    var idTipoCampo = accounting.unformat($("#idTipoCampo").val());
    var idTipoOperacion = accounting.unformat($("#idTipoOperacion").val());
    var valorMaximo = $("#valorMaximo").val();
    //VALIDACIONES
    if (!nombreCampo) {
        flag = false;
        errorMsg += 'Ingrese nombre del nuevo campo\n';
    }
    if (!valor && idTipoCampo < 4) {
        flag = false;
        errorMsg += 'Ingrese valor o formula del nuevo campo\n';
    }
    if (!idTipoValor) {
        flag = false;
        errorMsg += 'Seleccione tipo de valor\n';
    }
    if (!orden) {
        flag = false;
        errorMsg += 'Ingrese orden del nuevo campo\n';
    }
    if (!idTipoCampo) {
        flag = false;
        errorMsg += 'Seleccione tipo de campo\n';
    }
    if (!idTipoOperacion && idTipoCampo !== 1 && idTipoCampo !== 6) {
        flag = false;
        errorMsg += 'Seleccione tipo de operacion\n';
    }
    if (!valorMaximo && idTipoCampo === 3) {
        flag = false;
        errorMsg += 'Ingrese valor máximo para este campo\n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'saveCampoConstructorPlanilla',
            idHrmPlanilla: $("#idHrmPlanilla").val(),
            nombreCampo: nombreCampo,
            valor: valor,
            idTipoValor: idTipoValor,
            orden: orden,
            idTipoCampo: idTipoCampo,
            idTipoOperacion: idTipoOperacion,
            valorMaximo: valorMaximo
        };
        $.post('controllers/planillaController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        constructorPlanilla(params.idHrmPlanilla);
                        break;
                    case 'exists':
                        alert('Error ya existe un campo en el orden ' + params.orden + ' por favor cambie el valor ingresado');
                        break;
                    default:
                        alert('Error al crear nuevo campo, comuniquese con el administrador del sistema');
                        console.log(val.error);
                        break;
                }
            });
        }, 'json').done(function () {
            $("#loader").hide();
        });
    }
}
//
function updateCampoConstructorPlanilla() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var nombreCampo = $("#nombreCampo").val();
    var valor = $("#valor").val();
    var idTipoValor = accounting.unformat($("#idTipoValor").val());
    var orden = $("#orden").val();
    var idTipoCampo = accounting.unformat($("#idTipoCampo").val());
    var idTipoOperacion = accounting.unformat($("#idTipoOperacion").val());
    var valorMaximo = $("#valorMaximo").val();
    //VALIDACIONES
    if (!nombreCampo) {
        flag = false;
        errorMsg += 'Ingrese nombre del nuevo campo\n';
    }
    if (!valor && idTipoCampo < 4) {
        flag = false;
        errorMsg += 'Ingrese valor o formula del nuevo campo\n';
    }
    if (!idTipoValor) {
        flag = false;
        errorMsg += 'Seleccione tipo de valor\n';
    }
    if (!orden) {
        flag = false;
        errorMsg += 'Ingrese orden del nuevo campo\n';
    }
    if (!idTipoCampo) {
        flag = false;
        errorMsg += 'Seleccione tipo de campo\n';
    }
    if (!idTipoOperacion && idTipoCampo !== 1 && idTipoCampo !== 6) {
        flag = false;
        errorMsg += 'Seleccione tipo de operacion\n';
    }
    if (!valorMaximo && idTipoCampo === 3) {
        flag = false;
        errorMsg += 'Ingrese valor máximo para este campo\n';
    }
    //END VALIDACIONES
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        params = {
            service: 'updateCampoConstructorPlanilla',
            idHrmConstructorPlanilla: $("#idHrmConstructorPlanilla").val(),
            idHrmPlanilla: $("#idHrmPlanilla").val(),
            nombreCampoOld: $("#nombreCampoOld").val(),
            nombreCampo: nombreCampo,
            valor: valor,
            idTipoValor: idTipoValor,
            orden: orden,
            idTipoCampo: idTipoCampo,
            idTipoOperacion: idTipoOperacion,
            valorMaximo: valorMaximo
        };
        $.post('controllers/planillaController.php', params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        constructorPlanilla(params.idHrmPlanilla);
                        break;
                    default:
                        alert('Error al crear nuevo campo, comuniquese con el administrador del sistema');
                        console.log(val.error);
                        break;
                }
            });
        }, 'json').done(function () {
            $("#loader").hide();
        });
    }
}
//
function getCampoConstructorPlanilla(item) {
    $("#loader").show();
    params = {
        service: 'getCampoConstructorPlanilla',
        item: item,
        idHrmPlanilla: $("#idHrmPlanilla").val()
    };
    $.post('controllers/planillaController.php', params, function (data) {
        $.each(data, function (key, val) {
            $("#idHrmConstructorPlanilla").val(val.id);
            $("#nombreCampoOld").val(val.nombreCampo);
            $("#nombreCampo").val(val.nombreCampo);
            $("#valor").val(val.valor);
            $("#idTipoValor option").each(function () {
                if ($(this).val() === val.idTipoValor) {
                    $(this).prop('selected', true);
                }
            });
            $("#orden").val(val.orden);
            $("#idTipoCampo option").each(function () {
                if ($(this).val() === val.idTipoCampo) {
                    $(this).prop('selected', true);
                }
            });
            $("#idTipoOperacion option").each(function () {
                if ($(this).val() === val.idTipoOperacion) {
                    $(this).prop('selected', true);
                }
            });
            $("#valorMaximo").val(val.valorMaximo);
        });
    }, 'json').done(function () {
        $("#loader").hide();
        $("#add").hide();
        $("#update").show();
    });
}
//
function deleteCampoConstructorPlanilla(item) {
    var r = confirm("¿Esta seguro de eliminar este campo de esta planilla?");
    if (r == true) {
        $("#loader").show();
        params = {
            service: 'deleteCampoConstructorPlanilla',
            item: item,
            idHrmPlanilla: $("#idHrmPlanilla").val()
        };
        $.post('controllers/planillaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    constructorPlanilla(params.idHrmPlanilla);
                } else {
                    alert('Error al eliminar campo de planilla, comuniquese con el administrador del sistema');
                    console.log(val.error);
                }
            });
        }, 'json').done(function () {
            $("#loader").hide();
        });
    } else {
        return false;
    }
}
//
function saveSabanaPlanilla() {
    var validate = accounting.unformat($("#validate").val());
    if (validate === 0) {
        alert('Alerta, debe generar una nomina para realizar este proceso');
    } else {
        var r = confirm("¿Esta seguro de guardar esta planilla?");
        if (r == true) {
            $("#loader").show();
            var filas = accounting.unformat($("#myTable").find("tr").length) - 2;
            var columnas = accounting.unformat($("#myTable").find("tr:first td").length);
            var obj = {};
            obj.columnas = columnas;
            obj.idHrmPlanillas = $("#idHrmPlanillas").val();
            obj.periodo = $("#periodo option:selected").text();
            obj.mes = accounting.unformat($("#mes").val());
            obj.sabana = [];
            for (var a = 0; a < filas; a++) {
                var arr = {};
                $(".sabana-" + a).each(function (key) {
                    var value = $.trim($(this).text());
                    arr['campo' + (key + 1)] = value;
                });
                obj.sabana.push(arr);
            }
            $.ajax({
                url: 'controllers/planillaController.php',
                type: 'post',
                data: obj,
                success: function (response) {
                    var data = JSON.parse(response);
                    $.each(data, function (key, val) {
                        switch (val.message) {
                            case 'success':
                                alert('Nomina guardada exitosamente');
                                $("#loader").hide();
                                loadSabanaPlanilla();
                                break;
                            case 'autorizada':
                                alert('Nomina en status autorizada, no es posible volver a guardar la nomina generada');
                                $("#loader").hide();
                                break;
                            case 'cerrada':
                                alert('Nomina en estatus cerrada');
                                $("#loader").hide();
                                break;
                            default:
                                alert('Error al cerrar planilla, comuniquese con el administrador del sistema');
                                $("#loader").hide();
                                console.log(val.error);
                                break;
                        }
                    });
                }
            });
        } else {
            return false;
        }
    }
}
//
function authSabanaPlanilla() {
    var validate = accounting.unformat($("#validate").val());
    if (validate === 0) {
        alert('Alerta, debe consultar la nomina para realizar este proceso');
    } else {
        var idHrmPlanillas = $("#idHrmPlanillas").val();
        var periodo = $("#periodo option:selected").text();
        var mes = accounting.unformat($("#mes").val());
        params = {
            idHrmPlanillas: idHrmPlanillas,
            periodo: periodo,
            mes: mes
        };

    }
}