$(document).ready(function () {
    //loadReporteComisionesSuple();
    //loadVentasPorProducto();
    //loadKardex();
});
//
function loadKardex() {
    $.post('views/reportes/kardex.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Reportes');
        $("#opcion").html('Kardex');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#divReporte").hide();
    });
}
//
function generarKardex() {
    params = {
        ingresoA: $("#ingresoA").val(),
        ingresoATxt: $("#ingresoA option:selected").text(),
        idPuntoIngreso: $("#idPuntoIngreso").val(),
        idPuntoIngresoTxt: $("#idPuntoIngreso option:selected").text(),
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        codigo: $("#codigo").val(),
        tipoOrdenamiento: $("#tipoOrdenamiento").val(),
        idTipoProductos: $("#idTipoProductos").val()
    };
    $.post('views/reportes/kardexProductos.php', params, function (respuesta) {
        $('#reporte-container').html(respuesta);
        var table = $('#example').DataTable({
            "columnDefs": [
                {"visible": false, "targets": 5}
            ],
            "order": [[5, 'asc']],
            "paging": false,
            "ordering": false,
            "info": false,
            "drawCallback": function (settings) {
                var api = this.api();
                var rows = api.rows({page: 'current'}).nodes();
                var last = null;

                api.column(5, {page: 'current'}).data().each(function (group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                                '<tr class="group"><td colspan="11">' + group + '</td></tr>'
                                );

                        last = group;
                    }
                });
            }
        });
        //Order by the grouping
        $('#example tbody').on('click', 'tr.group', function () {
            var currentOrder = table.order()[0];
            if (currentOrder[0] === 5 && currentOrder[1] === 'asc') {
                table.order([5, 'desc']).draw();
            } else {
                table.order([5, 'asc']).draw();
            }
        });
        $("#divReporte").show();
    }).done(function () {
        totalesKardex();
        $('input[type=search]').on('keydown', function () {
            totalesKardex();
            //console.log(($(this).val().length) + 1);
            //console.log('do something');
        });
    });
}
//
function totalesKardex() {
    //var fila = $("#example").find("tbody tr").length;
    var sum1 = 0;
    var sum2 = 0;
    //for (var i = 0, max = fila; i < max; i++) {
    //INGRESOS
    $(".ingresos").each(function () {
        var value = accounting.unformat($(this).text());
        // add only if the value is number
        if (!isNaN(value) && value.length != 0) {
            sum1 += value;
        }
    });
    //SALIDAS
    $(".salidas").each(function () {
        var value = accounting.unformat($(this).text());
        // add only if the value is number
        if (!isNaN(value) && value.length != 0) {
            sum2 += value;
        }
    });
    //}
    $("#totalIngresos").html(accounting.formatNumber(sum1, 2));
    $("#totalSalidas").html(accounting.formatNumber(sum2, 2));
}
//
function exportarKardex() {
    params = {
        ingresoA: $("#ingresoA").val(),
        ingresoATxt: $("#ingresoA option:selected").text(),
        idPuntoIngreso: $("#idPuntoIngreso").val(),
        idPuntoIngresoTxt: $("#idPuntoIngreso option:selected").text(),
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        codigo: $("#codigo").val(),
        tipoOrdenamiento: $("#tipoOrdenamiento").val(),
        idTipoProductos: $("#idTipoProductos").val()
    };
    var url = "views/reportes/kardex-excel.php";
    $.redirect(url, params, 'POST');
}
//
function getVendedoresReporte(id) {
    params = {
        service: 'getVendedores',
        idVendedor: id
    };
    $.post('controllers/adminController.php', params, function (data) {

        $.each(data, function (key, val) {
            $("#nombreVendedor").val(val.userName);
            $("#idVendedor").val(val.id);
            console.log(val.userName);
        });

    }, 'json');
    $("#modal1").modal('hide');
}
//
function loadVentasPorProducto() {
    $.post('views/reportes/ventasPorProducto.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Facturacion');
        $("#opcion").html('Consulta de Facturacion Por Producto');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#divReporte").hide();
        loadSucursalesEmpresa('idSucursales');
        loadVendedores('admin');
    }).done(function () {
        getFamiliaNivel1();
        $("#idFamiliaNivel1").on('change', function () {
            getFamiliaNivel2();
        });
        $("#idFamiliaNivel2").on('change', function () {
            getFamiliaNivel3();
        });
    });
}
//
function generarReporteVentasPorProducto() {
    params = {
        service: 'ventasPorProducto',
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idSucursales: $("#idSucursales").val(),
        idFamiliaNivel1: $("#idFamiliaNivel1").val(),
        idFamiliaNivel2: $("#idFamiliaNivel2").val(),
        idFamiliaNivel3: $("#idFamiliaNivel3").val(),
        codigo: $("#codigo").val(),
        idVendedores: $("#vendedores").val()
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle").html('');
        $("#summary").html('');
        var datos = "";
        var summary = "";
        var total1 = 0;
        var total2 = 0;
        var total3 = 0;
        var total4 = 0;
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='8' align='center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                total1 += parseFloat(val.cantidad);
                total2 += parseFloat(val.totalVenta);
                total3 += parseFloat(val.totalCosto);
                total4 += parseFloat(val.utilidad);
                datos += "<tr>";
                datos += "<td>" + val.codigo + "</td>";
                datos += "<td>" + val.descripcion + "</td>";
                datos += "<td class='text-right'>" + val.cantidad + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.totalVenta, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.totalCosto, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.utilidad, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + val.margen + " %</td>";
                datos += "<td class='text-right'>" + val.vendedor + "</td>";
                datos += "</tr>";
            });
            summary += "<tr>";
            summary += "<td colspan='2'>Totales</td>";
            summary += "<td align='right'>" + total1 + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total2.toFixed(2), 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total3.toFixed(2), 'Q. ') + "</td>";
            summary += "<td align='right'>" + accounting.formatMoney(total4.toFixed(2), 'Q. ') + "</td>";
            summary += "<td></td>";
            summary += "<td></td>";
            summary += "</tr>";
        }
        $("#detalle").html(datos);
        $("#summary").html(summary);
    }, 'json').done(function () {
        $("#divReporte").show();
    });
}
//
function loadVentasPorVendedor() {
    $.post('views/reportes/ventasPorVendedor.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Reportes');
        $("#opcion").html('Ventas por Vendedor');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#divReporte").hide();
        loadFamilias();
    });
}
//
function generarReporteVentasPorVendedor() {
    params = {
        service: 'getVendedores',
        idVendedor: $("#idVendedor").val(),

    };
    $("#panel").html('');
    $.post('controllers/adminController.php', params, function (data) {
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='7' align='center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                var datos = "";
                datos += "<table class='table table-striped table-bordered' cellspacing='0' width='100%'>";
                datos += "<thead>";
                datos += "<tr>";
                datos += "<td width='900'>" + val.userName + "</td>";
                datos += "<td class='text-right' width='200'>Total</td>";
                datos += "</tr>";
                datos += " </thead>";
                datos += "  <tbody>"
                params = {
                    service: 'ventasPorFamilia',
                    fechaInicio: $("#fechaInicio .form-control").val(),
                    fechaFin: $("#fechaFin .form-control").val(),
                    idSucursales: $("#idSucursales").val(),
                    nivelFamilias: $("#nivelFamilias").val(),
                    idFamilia: $("#idFamilias").val(),
                    idVendedor: val.id
                };
                $.post('controllers/reportesController.php', params, function (data2) {
                    var total1 = 0;
                    var total2 = 0;
                    var total3 = 0;
                    var total4 = 0;
                    if (data2 == null) {

                    } else {
                        $.each(data2, function (key2, val2) {
                            total2 += parseFloat(val2.total);
                            datos += "<tr>";
                            datos += "<td>" + val2.descripcion + "</td>";
                            datos += "<td class='text-right'>" + accounting.formatMoney(val2.total, 'Q. ') + "</td>";
                            datos += "</tr>";
                        });
                    }
                    datos += "<tr class='info'>";
                    datos += "<td>Totales</td>";
                    datos += "<td align='right'>" + accounting.formatMoney(total2.toFixed(2), 'Q. ') + "</td>";
                    datos += "<td></td>";
                    datos += "</tr>";
                    datos += "</tbody>";
                    datos += " </table>";
                    $("#panel").append(datos);
                }, 'json');



            });

        }
    }, 'json').done(function () {
        $("#divReporte").show();
    });
}
//
function loadVentasPorFamilia() {
    $.post('views/reportes/ventasPorFamilia.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Reportes');
        $("#opcion").html('Ventas por Familia');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#divReporte").hide();
    });
}
//
function ventasPorFamilia(idVendedor) {

    return datos;
}
//
function loadVentasPorDocumento() {
    $.post('views/reportes/ventasPorDocumento.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Reportes');
        $("#opcion").html('Ventas por Documento');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#divReporte").hide();
        loadSucursalesEmpresa('idSucursales');
    });
}
//
function generarReporteVentasPorDocumento() {
    params = {
        service: 'ventasPorDocumento',
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idSucursales: $("#idSucursales").val(),
        documento: $("#documento").val(),
        status: '0'
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle").html('');
        var datos = "";
        var total1 = 0;
        var total2 = 0;
        var total3 = 0;
        var total4 = 0;
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='7' align='center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                var total = 0;
                var totalCosto = 0;
                var utilidad = 0;
                var margen = 0;
                var iva = 0;
                var status = "alert-danger";
                if (val.statusFactura === 'Activa') {
                    total = val.total;
                    totalCosto = val.totalCosto;
                    utilidad = val.utilidad;
                    margen = val.margen;
                    iva = parseFloat(val.total) * 0.12;
                    status = "alert-success";
                }
                total1 += parseFloat(total);
                total2 += parseFloat(totalCosto);
                total3 += parseFloat(utilidad);
                total4 += parseFloat(iva);
                datos += "<tr>";
                datos += "<td>" + val.fecha + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td class='" + status + " text-center'>" + val.statusFactura + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(total, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(totalCosto, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(utilidad, 'Q. ') + "</td>";
                datos += "<td class='text-right'>" + margen + " %</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(iva, 'Q. ') + "</td>";
                datos += "</tr>";
            });
            datos += "<tr class='info'>";
            datos += "<td colspan='3'>Totales</td>";
            datos += "<td align='right'>" + accounting.formatMoney(total1.toFixed(2), 'Q. ') + "</td>";
            datos += "<td align='right'>" + accounting.formatMoney(total2.toFixed(2), 'Q. ') + "</td>";
            datos += "<td align='right'>" + accounting.formatMoney(total3.toFixed(2), 'Q. ') + "</td>";
            datos += "<td></td>";
            datos += "<td align='right'>" + accounting.formatMoney(total4.toFixed(2), 'Q. ') + "</td>";
            datos += "</tr>";
        }
        $("#detalle").append(datos);
    }, 'json').done(function () {
        $("#divReporte").show();
    });
}
//
function loadHistorialCostos() {
    $.post('views/reportes/historialCostos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Reportes');
        $("#opcion").html('Historial de Costos Por Producto');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#divReporte").hide();
    });
}
//
function generarHistorialCostos() {
    params = {
        service: 'historialCostos',
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        codigo: $("#codigo").val()
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle").html('');
        var datos = "";
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='11' align='center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                datos += "<tr>";
                datos += "<td>" + val.fechaCompra2 + "</td>";
                datos += "<td>" + val.documento + "</td>";
                datos += "<td>" + val.sku + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += "<td class='text-right'>" + val.existInventario + "</td>";
                datos += "<td class='text-right'>" + val.costoUnitarioInv + "</td>";
                datos += "<td class='text-right'>" + val.costoExistActual + "</td>";
                datos += "<td class='text-right'>" + val.existCompra + "</td>";
                datos += "<td class='text-right'>" + val.costoUnitarioCompra + "</td>";
                datos += "<td class='text-right'>" + val.costoCompra + "</td>";
                datos += "<td class='text-right'>" + val.costoPromedio + "</td>";
                datos += "</tr>";
            });
        }
        $("#detalle").append(datos);
    }, 'json').done(function () {
        /*
         $('#table').DataTable({
         "scrollX": true
         });
         */
        $("#divReporte").show();
    });
}
//
function loadConsumoMateriales() {
    $.post('views/reportes/consumoMateriales.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Reportes');
        $("#opcion").html('Consumo de Materiales');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#divReporte").hide();
        loadSucursalesEmpresa('idSucursales');
    });
}
//
function generarReporteConsumoMateriales() {
    params = {
        service: 'consumoMateriales',
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idSucursales: $("#idSucursales").val(),
        codigo: $("#codigo").val()
    };
    $.post('controllers/reportesController.php', params, function (data) {
        $("#detalle").html('');
        var datos = "";
        var total1 = 0;
        if (data === null) {
            datos += "<tr>";
            datos += "<td colspan='5' align='center'>0 registros encontrados</td>";
            datos += "</tr>";
        } else {
            $.each(data, function (key, val) {
                total1 += parseFloat(val.totalCosto);
                datos += "<tr>";
                datos += "<td>" + val.sku + "</td>";
                datos += "<td>" + val.descLarga + "</td>";
                datos += "<td>" + val.unidadMedida + "</td>";
                datos += "<td class='text-right'>" + val.unidadesUtilizadas + "</td>";
                datos += "<td class='text-right'>" + accounting.formatMoney(val.totalCosto, 'Q. ') + "</td>";
                datos += "</tr>";
            });
            datos += "<tr class='info'>";
            datos += "<td colspan='4'>Total</td>";
            datos += "<td align='right'>" + accounting.formatMoney(total1.toFixed(2), 'Q. ') + "</td>";
            datos += "</tr>";
        }
        $("#detalle").append(datos);
    }, 'json').done(function () {
        $("#divReporte").show();
    });
}
//
function estadoCuentaClientes() {
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
        params = {
            idclientes: id,
            fechaInicio: $("#fechaInicioCXC").val(),
            fechaFin: $("#fechaFinCXC").val()
        };
        var reporte = "estadoCuentaClientes-pdf.php";
        var url = "../views/jasper/" + reporte;
        $.redirect(url, params, 'POST', '_blank');
    }
}
//
function estadoCuentaProveedores() {

    var id;
    $('input[type=checkbox]').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
        console.log(id);
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>', function () {
            $('#modal1').modal('hide');
        });
    } else {
        params = {
            idProveedores: id
        };
        var reporte = "estadoCuentaProveedores-pdf.php";
        var url = "../views/jasper/" + reporte;
        $.redirect(url, params, 'POST', '_blank');
    }
}
//
function loadReporteExistencias() {
    $.post('views/reportes/existencias.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Reportes');
        $("#opcion").html('Existencias');
        $("#divReporte").hide();
        $('.selectpicker').selectpicker();
        $('.bootstrap-select .btn').addClass('btn-sm');
    });
}
//
function exportarReporteExistencias() {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    if (!$("#ingresoA").val()) {
        flag = false;
        errorMsg += 'Seleccione el campo Inventario De\n';
    }
    if (!$("#idPuntoIngreso").val()) {
        flag = false;
        errorMsg += 'Seleccione el campo Lugar\n';
    }
    if (!$("#tipoReporte").val()) {
        flag = false;
        errorMsg += 'Seleccione el campo Tipo de Reporte\n';
    }
    if (!$("#tipoValor").val()) {
        flag = false;
        errorMsg += 'Seleccione el campo Tipo de Valor\n';
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        params = {
            ingresoA: $("#ingresoA").val(),
            ingresoATxt: $("#ingresoA option:selected").text(),
            idPuntoIngreso: $("#idPuntoIngreso").val(),
            idPuntoIngresoTxt: $("#idPuntoIngreso option:selected").text(),
            tipoReporte: $("#tipoReporte").val(),
            tipoReporteTxt: $("#tipoReporte option:selected").text(),
            codigo: $("#codigo").val(),
            periodo: $("#periodo option:selected").text(),
            mes: $("#mes").val(),
            mesTxt: $("#mes option:selected").text(),
            tipoValor: $("#tipoValor").val(),
            tipoValorTxt: $("#tipoValor option:selected").text()
        };
        var url = "";
        url = "views/reportes/existencias-excel.php";
        $.redirect(url, params, 'post');
    }
}
//
function loadReporteComisionesSuple() {
    $.post('views/reportes/comisionesSuple.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Reportes');
        $("#opcion").html('Comisiones');
        $('#fechaInicio,#fechaFin').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
    });
}
//
function exportarReporteComisionesSuple() {
    params = {
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idVendedores: $("#idVendedores").val(),
        vendedor: $("#idVendedores option:selected").text(),
        idCentrosCosto: $("#idCentrosCosto").val()
    };
    var url = "views/reportes/comisionesSuple-excel.php";
    $.redirect(url, params, 'POST', '_blank');
}
//
function estadoCuentaProveedores() {
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
        params = {
            idProveedores: id,
            fechaInicio: $("#fechaInicioCXP").val(),
            fechaFin: $("#fechaFinCXP").val()
        };
        var reporte = "estadoCuentaProveedores-pdf.php";
        var url = "../views/jasper/" + reporte;
        $.redirect(url, params, 'POST', '_blank');
    }
}
//
function getFamiliaNivel1() {
    params = {
        service: 'getFamiliaNivel1'
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $("#idFamiliaNivel1").html('');
        $("#idFamiliaNivel1").append("<option value=''>[Seleccione...]</option>");
        $.each(data, function (key, val) {
            if ($("#idFamiliaNivel1").val() === val.id) {
                $("#idFamiliaNivel1").append("<option value='" + val.id + "' selected=''>" + val.descripcion + "</option>");
            } else {
                $("#idFamiliaNivel1").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }
        });
    }, 'json');
}
//
function getFamiliaNivel2() {
    params = {
        service: 'getFamiliaNivel2',
        idFamiliaNivel1: $("#idFamiliaNivel1").val()
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $("#idFamiliaNivel2").html('');
        $("#idFamiliaNivel2").append("<option value=''>[Seleccione...]</option>");
        $.each(data, function (key, val) {
            if ($("#idFamiliaNivel2").val() === val.id) {
                $("#idFamiliaNivel2").append("<option value='" + val.id + "' selected=''>" + val.descripcion + "</option>");
            } else {
                $("#idFamiliaNivel2").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }
        });
    }, 'json').done(function () {
    });
}
//
function getFamiliaNivel3() {
    params = {
        service: 'getFamiliaNivel3',
        idFamiliaNivel1: $("#idFamiliaNivel2").val()
    };
    $.post('controllers/inventariosController.php', params, function (data) {
        $("#idFamiliaNivel3").html('');
        $("#idFamiliaNivel3").append("<option value=''>[Seleccione...]</option>");
        $.each(data, function (key, val) {
            if ($("#idFamiliaNivel3").val() === val.id) {
                $("#idFamiliaNivel3").append("<option value='" + val.id + "' selected=''>" + val.descripcion + "</option>");
            } else {
                $("#idFamiliaNivel3").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }
        });
    }, 'json').done(function () {
    });
}
//
function generarReporteVentasExcel() {
    params = {
        service: 'ventasPorProducto',
        fechaInicio: $("#fechaInicio .form-control").val(),
        fechaFin: $("#fechaFin .form-control").val(),
        idSucursales: $("#idSucursales").val(),
        idFamiliaNivel1: $("#idFamiliaNivel1").val(),
        idFamiliaNivel2: $("#idFamiliaNivel2").val(),
        idFamiliaNivel3: $("#idFamiliaNivel3").val(),
        codigo: $("#codigo").val(),
        idVendedores: $("#vendedores").val()
    };
    var url = "views/reportes/ventasPorProductos-excel.php";
    $.redirect(url, params, 'post');
}