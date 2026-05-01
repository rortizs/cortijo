$(document).ready(function () {
    //loadGeneracionPlanilla();
});
//
function loadGeneracionPlanilla() {
    $.post('views/planilla/generacionPlanilla.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Planilla');
        $("#opcion").html('Generación de Planilla');
        $("#fixTable").tableHeadFixer({"head": false, "left": 3});
    });
}
//
function generarPlanilla() {
    var idHrmDepartamentos = $("#idHrmDepartamentos").val();
    var idHrmPlanilla = $("#idHrmPlanillas").val();
    var idHrmPlanillaTXT = $("#idHrmPlanillas option:selected").text();
    var periodo = $("#periodo option:selected").text();
    var mes = $("#mes").val();
    var mesTXT = $("#mes option:selected").text();
    var quincena = $("#quincena").val();
    var quincenaTXT = $("#quincena option:selected").text();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!idHrmDepartamentos) {
        flag = false;
        errorMsg += 'Campo Departamento es Requerido\n';
    }
    if (!idHrmPlanilla) {
        flag = false;
        errorMsg += 'Campo Nomina a Trabajar es Requerido\n';
    }
    if (!$("#periodo").val()) {
        flag = false;
        errorMsg += 'Campo Periodo es Requerido\n';
    }
    if (!mes) {
        flag = false;
        errorMsg += 'Campo Mes es Requerido\n';
    }
    if (!quincena) {
        flag = false;
        errorMsg += 'Campo Quincena a Trabajar es Requerido\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            idHrmDepartamentos: idHrmDepartamentos,
            idHrmPlanilla: idHrmPlanilla,
            idHrmPlanillaTXT: idHrmPlanillaTXT,
            periodo: periodo,
            mes: mes,
            mesTXT: mesTXT,
            quincena: quincena,
            quincenaTXT: quincenaTXT
        };
        $.post('views/planilla/generacionPlanilla.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $("#modulo").html('Planilla');
            $("#opcion").html('Generación de Planilla');
        }).done(function () {
            $("#reportContainer").height((alto / 2) - 45);
            if (quincena === '1') {
                $(".primera").show();
                $(".segunda").hide();
            } else {
                $(".primera").hide();
                $(".segunda").show();
            }
            $("#idHrmPlanillas option").each(function () {
                if ($(this).val() === idHrmPlanilla) {
                    $(this).prop('selected', true);
                    loadDepartamentos();
                }
            });
            $("#periodo option").each(function () {
                if ($(this).text() === periodo) {
                    $(this).prop('selected', true);
                }
            });
            $("#mes option").each(function () {
                if ($(this).val() === mes) {
                    $(this).prop('selected', true);
                }
            });
            $("#quincena option").each(function () {
                if ($(this).val() === quincena) {
                    $(this).prop('selected', true);
                }
            });
        });
    }
}
//
function generarPlanillaPDF(option) {
    var url = "";
    var params = "";
    if (option === 3) {
        var tipoPlanilla = $("#tipoPlanilla").val();
        var tipoPlanillaTXT = $("#tipoPlanilla option:selected").text();
        var year = $("#year").val();
        var yearTXT = $("#year option:selected").text();
        var month = $("#month").val();
        var monthTXT = $("#month option:selected").text();
        var observaciones = $("#observaciones").val();
        params = {
            tipoPlanilla: tipoPlanilla,
            tipoPlanillaTXT: tipoPlanillaTXT,
            year: year,
            yearTXT: yearTXT,
            month: month,
            monthTXT: monthTXT,
            observaciones: observaciones,
            dias: 15
        };
    } else {
        var fechaInicio = $("#fechaInicio .form-control").val();
        var fechaFin = $("#fechaFin .form-control").val();
        var fechaInicioHE = $("#fechaInicioHE .form-control").val();
        var fechaFinHE = $("#fechaFinHE .form-control").val();
        var fuenteHorasExtras = $("#fuenteHorasExtras").val();
        params = {
            fechaInicio: fechaInicio,
            fechaFin: fechaFin,
            fechaInicioHE: fechaInicioHE,
            fechaFinHE: fechaFinHE,
            fuenteHorasExtras: fuenteHorasExtras,
            dias: restaFechas(fechaInicio, fechaFin)
        };
    }
    switch (option) {
        case 1:
            url = "views/planilla/generacionPlanilla_pdf.php";
            break;
        case 2:
            url = "views/planilla/generacionPlanillaLegal_pdf.php";
            break;
        case 3:
            url = "views/planilla/generacionPlanillaDivertia_pdf.php";
            break;
    }
    $.redirect(url, params, 'POST', '_blank');
}
//
function imprimirRecibosPdf() {
    var idHrmDepartamentos = $("#idHrmDepartamentos").val();
    var idHrmPlanilla = $("#idHrmPlanillas").val();
    var idHrmPlanillaTXT = $("#idHrmPlanillas option:selected").text();
    var periodo = $("#periodo option:selected").text();
    var mes = $("#mes").val();
    var mesTXT = $("#mes option:selected").text();
    var quincena = $("#quincena").val();
    var quincenaTXT = $("#quincena option:selected").text();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!idHrmDepartamentos) {
        flag = false;
        errorMsg += 'Campo Departamento es Requerido\n';
    }
    if (!idHrmPlanilla) {
        flag = false;
        errorMsg += 'Campo Nomina a Trabajar es Requerido\n';
    }
    if (!$("#periodo").val()) {
        flag = false;
        errorMsg += 'Campo Periodo es Requerido\n';
    }
    if (!mes) {
        flag = false;
        errorMsg += 'Campo Mes es Requerido\n';
    }
    if (!quincena) {
        flag = false;
        errorMsg += 'Campo Quincena a Trabajar es Requerido\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            idHrmDepartamentos: idHrmDepartamentos,
            idHrmPlanilla: idHrmPlanilla,
            idHrmPlanillaTXT: idHrmPlanillaTXT,
            periodo: periodo,
            mes: mes,
            mesTXT: mesTXT,
            quincena: quincena,
            quincenaTXT: quincenaTXT
        };
        //console.log(params);
        var url = "views/jasper/recibosPlanilla.php";
        $.redirect(url, params, 'POST', '_blank');
    }

}
//
function generarExcelPlanilla() {
    var idHrmDepartamentos = $("#idHrmDepartamentos").val();
    var idHrmPlanilla = $("#idHrmPlanillas").val();
    var idHrmPlanillaTXT = $("#idHrmPlanillas option:selected").text();
    var periodo = $("#periodo option:selected").text();
    var mes = $("#mes").val();
    var mesTXT = $("#mes option:selected").text();
    var quincena = $("#quincena").val();
    var quincenaTXT = $("#quincena option:selected").text();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!idHrmDepartamentos) {
        flag = false;
        errorMsg += 'Campo Departamento es Requerido\n';
    }
    if (!idHrmPlanilla) {
        flag = false;
        errorMsg += 'Campo Nomina a Trabajar es Requerido\n';
    }
    if (!$("#periodo").val()) {
        flag = false;
        errorMsg += 'Campo Periodo es Requerido\n';
    }
    if (!mes) {
        flag = false;
        errorMsg += 'Campo Mes es Requerido\n';
    }
    if (!quincena) {
        flag = false;
        errorMsg += 'Campo Quincena a Trabajar es Requerido\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            idHrmDepartamentos: idHrmDepartamentos,
            idHrmPlanilla: idHrmPlanilla,
            idHrmPlanillaTXT: idHrmPlanillaTXT,
            periodo: periodo,
            mes: mes,
            mesTXT: mesTXT,
            quincena: quincena,
            quincenaTXT: quincenaTXT
        };
        //console.log(params);
        var url = "views/reportes/reportePlanilla-excel.php";
        $.redirect(url, params);
    }
}
//
function cargaBancosTxt() {
    var idHrmDepartamentos = $("#idHrmDepartamentos").val();
    var idHrmPlanilla = $("#idHrmPlanillas").val();
    var idHrmPlanillaTXT = $("#idHrmPlanillas option:selected").text();
    var periodo = $("#periodo option:selected").text();
    var mes = $("#mes").val();
    var mesTXT = $("#mes option:selected").text();
    var quincena = $("#quincena").val();
    var quincenaTXT = $("#quincena option:selected").text();
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!idHrmDepartamentos) {
        flag = false;
        errorMsg += 'Campo Departamento es Requerido\n';
    }
    if (!idHrmPlanilla) {
        flag = false;
        errorMsg += 'Campo Nomina a Trabajar es Requerido\n';
    }
    if (!$("#periodo").val()) {
        flag = false;
        errorMsg += 'Campo Periodo es Requerido\n';
    }
    if (!mes) {
        flag = false;
        errorMsg += 'Campo Mes es Requerido\n';
    }
    if (!quincena) {
        flag = false;
        errorMsg += 'Campo Quincena a Trabajar es Requerido\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            idHrmDepartamentos: idHrmDepartamentos,
            idHrmPlanilla: idHrmPlanilla,
            idHrmPlanillaTXT: idHrmPlanillaTXT,
            periodo: periodo,
            mes: mes,
            mesTXT: mesTXT,
            quincena: quincena,
            quincenaTXT: quincenaTXT
        };
        //console.log(params);
        var url = "views/planilla/cargarBancoTxt.php";
        $.redirect(url, params, 'POST', '_blank');
    }

}
//
function restaFechas(f1, f2) {
    var aFecha1 = f1.split('-');
    var aFecha2 = f2.split('-');
    var fFecha1 = Date.UTC(aFecha1[2], aFecha1[1] - 1, aFecha1[0]);
    var fFecha2 = Date.UTC(aFecha2[2], aFecha2[1] - 1, aFecha2[0]);
    var dif = fFecha2 - fFecha1;
    var dias = Math.floor(dif / (1000 * 60 * 60 * 24));
    return dias + 1;
}
//
function loadDepartamentos() {
    params = {
        service: 'loadDepartamentos',
        idHrmPlanillas: $("#idHrmPlanillas").val(),
        idHrmDepartamentos: ''
    };
    $("#idHrmDepartamentos").html('');
    $.post('controllers/planillaController.php', params, function (data) {
        $("#idHrmDepartamentos").append("<option value=''>[Seleccione...]</option>");
        $("#idHrmDepartamentos").append("<option value='*'>TODOS LOS DEPARTAMENTOS</option>");
        $.each(data, function (key, val) {
            $("#idHrmDepartamentos").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
        });
    }, 'json');
}
//
function loadHrmDepartamentos() {
    params = {
        service: 'getHrmDepartamentos2'
    };
    $("#idHrmDepartamentos").html('');
    $.post('controllers/planillaController.php', params, function (data) {
        $("#idHrmDepartamentos").append("<option value=''>[Seleccione...]</option>");
        $.each(data, function (key, val) {
            $("#idHrmDepartamentos").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
        });
    }, 'json');
}
//
function impresionBoletaPago() {
    var url = "views/planilla/boletaPago_pdf.php";
    var params = "";
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var fechaInicioHE = $("#fechaInicioHE .form-control").val();
    var fechaFinHE = $("#fechaFinHE .form-control").val();
    var fuenteHorasExtras = $("#fuenteHorasExtras").val();
    params = {
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        fechaInicioHE: fechaInicioHE,
        fechaFinHE: fechaFinHE,
        fuenteHorasExtras: fuenteHorasExtras,
        dias: restaFechas(fechaInicio, fechaFin)
    };
    $.redirect(url, params, 'POST', '_blank');
}