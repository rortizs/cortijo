$(document).ready(function () {
    //loadReporteMarcajes();
});
//
function loadReporteMarcajes() {
    $.post('views/planilla/marcajesGSP.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#modulo").html('Planilla');
        $("#opcion").html('Reporte de Marcajes');
    });
}
//
function generarReporteMarcajes() {
    var idEmpleado = $("#idEmpleados").val();
    params = {
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idDepartamentos: $("#idDepartamentos").val(),
        idEmpleados: $("#idEmpleados").val(),
        nombreEmpleado: $("#idEmpleados option:selected").text(),
        flag: 1
    };
    $.post('views/planilla/marcajesGSP.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#modulo").html('Planilla');
        $("#opcion").html('Reporte de Marcajes');
    }).done(function () {
        loadEmpleados(idEmpleado);
    });
}
//
function loadReporteAsistencias() {
    $.post('views/planilla/asistencias.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#modulo").html('Planilla');
        $("#opcion").html('Detalle de Asistencia Diario');
    });
}
//
function generarReporteAsistencias() {
    params = {
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idDepartamentos: $("#idDepartamentos").val(),
        idEmpleados: $("#idEmpleados").val(),
        nombreEmpleado: $("#idEmpleados option:selected").text(),
        flag: 1
    };
    $.post('views/planilla/asistencias.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#modulo").html('Planilla');
        $("#opcion").html('Detalle de Asistencia Diario');
    });
}
//
function loadReporteAsistenciaDetalle() {
    $.post('views/planilla/asistenciasDetalle.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Planilla');
        $("#opcion").html('Detalle de Asistencia Mensual');
    });
}
//
function loadReporteAsistenciaHoras() {
    $.post('views/planilla/asistenciasHoras.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Planilla');
        $("#opcion").html('Detalle de Horas Extras');
    });
}

//
function generarReporteAsistenciasDetalle() {
    params = {
        month: $("#month").val(),
        year: $("#year").val(),
        idDepartamentos: $("#idDepartamentos").val(),
        idEmpleados: $("#idEmpleados").val(),
        txtDepartamentos: $("#idDepartamentos option:selected").text(),
        txtEmpleados: $("#idEmpleados option:selected").text()
    };
    $.post('views/planilla/asistenciasDetalle.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        loadEmpleados();
    });
}
//
function generarReporteAsistenciasHoras() {
    params = {
        month: $("#month").val(),
        year: $("#year").val(),
        idDepartamentos: $("#idDepartamentos").val(),
        idEmpleados: $("#idEmpleados").val(),
        txtDepartamentos: $("#idDepartamentos option:selected").text(),
        txtEmpleados: $("#idEmpleados option:selected").text()
    };
    $.post('views/planilla/asistenciasHoras.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        loadEmpleados();
    });
}
//
function generarReporteAsistenciasDetalleExcel() {
    var month = $("#month").val();
    var year = $("#year").val();
    var idSucursales = $("#idSucursal").val();
    var idEmpleados = $("#idEmpleados").val();
    var txtSucursal = $("#idSucursal option:selected").text();
    var txtEmpleados = $("#idEmpleados option:selected").text();
    window.location = "views/planilla/asistenciasDetalleExcel.php?month=" + month + "&year=" + year + "&idSucursales=" + idSucursales + "&idEmpleados=" + idEmpleados + "&txtSucursal=" + txtSucursal + "&txtEmpleados=" + txtEmpleados + "";
}
//
function generarReporteAsistenciasHorasExcel() {
    var month = $("#month").val();
    var year = $("#year").val();
    var idSucursales = $("#idSucursal").val();
    var idEmpleados = $("#idEmpleados").val();
    var txtSucursal = $("#idSucursal option:selected").text();
    var txtEmpleados = $("#idEmpleados option:selected").text();
    window.location = "views/planilla/asistenciasHorasExcel.php?month=" + month + "&year=" + year + "&idSucursales=" + idSucursales + "&idEmpleados=" + idEmpleados + "&txtSucursal=" + txtSucursal + "&txtEmpleados=" + txtEmpleados + "";
}
//
function loadEmpleados(idEmpleado) {
    $("#idEmpleados").html('');
    params = {
        service: 'getEmpleados',
        idDepartamentos: $("#idDepartamentos").val()
    };
    $.post('controllers/adminController.php', params, function (data) {
        $("#idEmpleados").append('<option value="*">Todos los empleados</option>');
        $.each(data, function (key, val) {
            if (idEmpleado === val.idUsuarios) {
                $("#idEmpleados").append('<option value="' + val.idUsuarios + '" selected="">' + val.nombreCompleto + '</option>');
            } else {
                $("#idEmpleados").append('<option value="' + val.idUsuarios + '">' + val.nombreCompleto + '</option>');
            }

        });
    }, 'json');
}
//
function guardarMarcaje() {
    var fecha = $("#fecha").val();
    var horaEntrada = $("#horaEntrada").val();
    var horaSalida = $("#horaSalida").val();
    var numeroHorasExtrasDiurnas = $("#numeroHorasExtrasDiurnas").val();
    var numeroHorasExtrasNocturas = $("#numeroHorasExtrasNocturas").val();
    params = {
        service: 'guardarMarcaje',
        fecha: fecha,
        horaEntrada: horaEntrada,
        horaSalida: horaSalida,
        numeroHorasExtrasDiurnas: numeroHorasExtrasDiurnas,
        numeroHorasExtrasNocturas: numeroHorasExtrasNocturas,
        idEmpleados: $("#idEmpleados").val()
    };
    $.post('controllers/planillaController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                generarReporteMarcajes();
            } else {
                alert('Error al ingresar marcaje, comuniquese con el administrador del sistema');
            }
        });
    }, 'json');
}
//
function updateMarcaje(idMarcaje) {
    var horaEntrada = $("#horaEntrada-" + idMarcaje).val();
    var horaSalida = $("#horaSalida-" + idMarcaje).val();
    var numeroHorasExtrasDiurnas = $("#numeroHorasExtrasDiurnas-" + idMarcaje).val();
    var numeroHorasExtrasNocturas = $("#numeroHorasExtrasNocturas-" + idMarcaje).val();
    params = {
        service: 'updateMarcaje',
        horaEntrada: horaEntrada,
        horaSalida: horaSalida,
        numeroHorasExtrasDiurnas: numeroHorasExtrasDiurnas,
        numeroHorasExtrasNocturas: numeroHorasExtrasNocturas,
        idMarcaje: idMarcaje
    };
    $.post('controllers/planillaController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                generarReporteMarcajes();
            } else {
                alert('Error al ingresar marcaje, comuniquese con el administrador del sistema');
            }
        });
    }, 'json');
}
//
function imprimirReporteMarcajes() {
    params = {
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idEmpleados: $("#idEmpleados").val()
    };
    //console.log(params);
    var url = "views/jasper/marcajesGSP.php";
    $.redirect(url, params, 'POST', '_blank');
}