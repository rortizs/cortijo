/**
 * @version 1.0 20180322
 */
$(document).ready(function () {
    //loadPlanillasFarmandina();
});
//
function loadPlanillasFarmandina() {
    $.post('views/planilla/planillasFarmandina.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Planilla - Procesos');
        $("#opcion").html('Nominas');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
    });
}
//
function exportarPlanillaFarmandina() {
    params = {
        year: $("#year option:selected").text(),
        month: $("#month option:selected").text(),
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val()
    };
    var url = "";
    switch ($("#idEmpresas").val()) {
        case '1':
            url = "views/planilla/farmandina-nomina-rd-excel.php";
            break;
        case '2':
            url = "views/planilla/farmandina-nomina-pn-excel.php";
            break;
        case '3':
            url = "views/planilla/farmandina-nomina-cr-excel.php";
            break;
        case '4':
            url = "views/planilla/farmandina-nomina-sv-excel.php";
            break;
        case '5':
            url = "views/planilla/farmandina-nomina-hn-excel.php";
            break;
        case '6':
            url = "views/planilla/farmandina-nomina-nc-excel.php";
            break;
    }
    $.redirect(url, params, 'POST', '_blank');
}
//
function subirExpediente() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var nameFile = $('#nameFile').val();
    var file = $('#file').val();
    if (!nameFile) {
        flag = false;
        errorMsg += 'Ingrese nombre para el archivo a cargar al expediente\n';
    }
    if (!file) {
        flag = false;
        errorMsg += 'Seleccione un archivo para cargar al expediente\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var fsize = Math.round(parseFloat($('#file')[0].files[0].size) / 1048576 * 100) / 100;
        var ftype = $('#file')[0].files[0].type;
        var fileData = $('#file').prop('files')[0];
        if (fsize > '4') {
            alert('Tamano maximo de imagen permitido 4 megas');
            return false;
        }
        $("#loader").show();
        var form_data = new FormData();
        form_data.append('service', 'subirExpediente');
        form_data.append('idHrmEmpleados', $("#idHrmEmpleados").val());
        form_data.append('descripcion', nameFile);
        form_data.append('file', fileData);
        $.ajax({
            url: 'controllers/planillaController.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Archivo cargado al expediente exitosamente');
                        $('#nameFile').val('');
                        $('#file').val('');
                        hrmEmpleadosExpediente();
                    } else {
                        alert('Error al cargar archivo al expediente');
                        $("#loader").hide();
                    }
                });
            }
        });
    }
}
//
function hrmEmpleadosExpediente() {
    $("#loader").show();
    params = {
        service: 'hrmEmpleadosExpediente',
        idHrmEmpleados: $("#idHrmEmpleados").val()
    };
    $.post('controllers/planillaController.php', params, function (data) {
        $("#detalle").html('');
        var datos = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='4' align='center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                var file = '"' + val.file + '"';
                datos += "<tr>";
                datos += "<td style='width: 1% !important;'>";
                datos += "<button class='btn btn-xs btn-danger' onclick='eliminarExpediente(" + val.id + "," + file + ")'><i class='fa fa-trash'></i></button>";
                datos += "</td>";
                datos += "<td class='text-center'>" + val.descripcion + "</td>";
                datos += "<td class='text-center'><a href='./assets/expedientes/" + val.file + "' target='_blank'>" + val.file + "</a></td>";
                datos += "<td class='text-center' style='width: 10% !important;'>" + val.created_at + "</td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function eliminarExpediente(item, file) {
    var r = confirm("¿Esta seguro de eliminar este registro del expediente?");
    if (r == true) {
        $("#loader").show();
        params = {
            service: 'eliminarExpediente',
            file: file,
            item: item,
            idHrmEmpleados: $("#idHrmEmpleados").val()
        };
        $.post('controllers/planillaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Archivo eliminado del expediente exitosamente');
                    hrmEmpleadosExpediente();
                } else {
                    alert('Error al eliminar archivo del expediente');
                    $("#loader").hide();
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function subirFotoFicha() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var file = $('#photo').val();
    if (!file) {
        flag = false;
        errorMsg += 'Seleccione un archivo para cargar al expediente\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var fsize = Math.round(parseFloat($('#photo')[0].files[0].size) / 1048576 * 100) / 100;
        var ftype = $('#photo')[0].files[0].type;
        var fileData = $('#photo').prop('files')[0];
        if (fsize > '2') {
            alert('Tamano maximo de imagen permitido 2 megas');
            return false;
        }
        if (ftype !== 'image/png' && ftype !== 'image/jpeg') {
            alert('Tipos de imagenes permitidas png o jpg');
            return false;
        }
        $("#loader").show();
        var form_data = new FormData();
        form_data.append('service', 'subirFotoFicha');
        form_data.append('idHrmEmpleados', $("#idHrmEmpleados").val());
        form_data.append('file', fileData);
        $.ajax({
            url: 'controllers/planillaController.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Foto cargada exitosamente');
                        $('#photo').val('');
                        $("#loader").hide();
                    } else {
                        alert('Error al cargar foto');
                        $("#loader").hide();
                    }
                });
            }
        });
    }
}
//
function subirFotoFichaCliente() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var file = $('#photo').val();
    if (!file) {
        flag = false;
        errorMsg += 'Seleccione un archivo para cargar al expediente\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var fsize = Math.round(parseFloat($('#photo')[0].files[0].size) / 1048576 * 100) / 100;
        var ftype = $('#photo')[0].files[0].type;
        var fileData = $('#photo').prop('files')[0];
        if (fsize > '2') {
            alert('Tamano maximo de imagen permitido 2 megas');
            return false;
        }
        if (ftype !== 'image/png' && ftype !== 'image/jpeg') {
            alert('Tipos de imagenes permitidas png o jpg');
            return false;
        }
        $("#loader").show();
        var form_data = new FormData();
        form_data.append('service', 'subirFotoFichaCliente');
        form_data.append('idClientes', $("#idClientes").val());
        form_data.append('file', fileData);
        $.ajax({
            url: 'controllers/planillaController.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Foto cargada exitosamente');
                        $('#photo').val('');
                        $("#loader").hide();
                    } else {
                        alert('Error al cargar foto');
                        $("#loader").hide();
                    }
                });
            }
        });
    }
}
//
function clientesDocumentosAdjuntos() {
    $("#loader").show();
    params = {
        service: 'clientesDocumentosAdjuntos',
        idClientes: $("#idClientes").val()
    };
    $.post('controllers/planillaController.php', params, function (data) {
        $("#detalle").html('');
        var datos = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='4' align='center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                var file = '"' + val.file + '"';
                datos += "<tr>";
                datos += "<td style='width: 1% !important;'>";
                datos += "<button class='btn btn-xs btn-danger' onclick='eliminarClientesDocumentosAdjuntos(" + val.id + "," + file + ")'><i class='fa fa-trash'></i></button>";
                datos += "</td>";
                datos += "<td class='text-center'>" + val.descripcion + "</td>";
                datos += "<td class='text-center'><a href='./assets/docClientes/" + val.file + "' target='_blank'>" + val.file + "</a></td>";
                datos += "<td class='text-center' style='width: 10% !important;'>" + val.created_at + "</td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function subirClientesDocumentosAdjuntos() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var nameFile = $('#nameFile').val();
    var file = $('#file').val();
    if (!nameFile) {
        flag = false;
        errorMsg += 'Ingrese nombre para el archivo a cargar al expediente\n';
    }
    if (!file) {
        flag = false;
        errorMsg += 'Seleccione un archivo para cargar al expediente\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var fsize = Math.round(parseFloat($('#file')[0].files[0].size) / 1048576 * 100) / 100;
        var ftype = $('#file')[0].files[0].type;
        var fileData = $('#file').prop('files')[0];
        if (fsize > '4') {
            alert('Tamano maximo de imagen permitido 4 megas');
            return false;
        }
        $("#loader").show();
        var form_data = new FormData();
        form_data.append('service', 'subirClientesDocumentosAdjuntos');
        form_data.append('idClientes', $("#idClientes").val());
        form_data.append('descripcion', nameFile);
        form_data.append('file', fileData);
        $.ajax({
            url: 'controllers/planillaController.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Archivo cargado al expediente exitosamente');
                        $('#nameFile').val('');
                        $('#file').val('');
                        clientesDocumentosAdjuntos();
                    } else {
                        alert('Error al cargar archivo al expediente');
                        $("#loader").hide();
                    }
                });
            }
        });
    }
}
//
function eliminarClientesDocumentosAdjuntos(item, file) {
    var r = confirm("¿Esta seguro de eliminar este registro del expediente?");
    if (r == true) {
        $("#loader").show();
        params = {
            service: 'eliminarClientesDocumentosAdjuntos',
            file: file,
            item: item,
            idClientes: $("#idClientes").val()
        };
        $.post('controllers/planillaController.php', params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    alert('Archivo eliminado del expediente exitosamente');
                    clientesDocumentosAdjuntos();
                } else {
                    alert('Error al eliminar archivo del expediente');
                    $("#loader").hide();
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function subirFotoFichaProducto() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var file = $('#photo').val();
    if (!file) {
        flag = false;
        errorMsg += 'Seleccione un archivo para cargar al expediente\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var fsize = Math.round(parseFloat($('#photo')[0].files[0].size) / 1048576 * 100) / 100;
        var ftype = $('#photo')[0].files[0].type;
        var fileData = $('#photo').prop('files')[0];
        if (fsize > '2') {
            alert('Tamano maximo de imagen permitido 2 megas');
            return false;
        }
        if (ftype !== 'image/png' && ftype !== 'image/jpeg') {
            alert('Tipos de imagenes permitidas png o jpg');
            return false;
        }
        $("#loader").show();
        var form_data = new FormData();
        form_data.append('service', 'subirFotoFichaProducto');
        form_data.append('idProductos', $("#idProductos").val());
        form_data.append('file', fileData);
        $.ajax({
            url: 'controllers/planillaController.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                $.each(data, function (key, val) {
                    if (val.message === 'success') {
                        alert('Foto cargada exitosamente');
                        $('#photo').val('');
                        $("#loader").hide();
                    } else {
                        alert('Error al cargar foto');
                        $("#loader").hide();
                    }
                });
            }
        });
    }
}