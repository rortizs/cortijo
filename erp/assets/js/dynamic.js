/**
 * FUNCIONES
 */
var alto = jQuery(window).height();
function loadData(table, modulo, opcion, btnNew, btnUpdate, btnDelete) {
    $("#loader").show();
    params = {
        table: table,
        btnNew: btnNew,
        btnUpdate: btnUpdate,
        btnDelete: btnDelete,
        modulo: modulo,
        opcion: opcion
    };
    $.post('views/dynamic/busquedas.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html(modulo);
        $("#opcion").html(opcion);
        $('#dynamicTable .filters td').each(function () {
            var title = $(this).text();
            if (title !== 'No.' && title !== 'Item') {
                $(this).html('<input type="text" class="form-control input-sm" placeholder="Buscar" style="width:100%;"/>');
            }
        });
        var t = $('#dynamicTable').DataTable({
            "order": [[1, "asc"]],
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "views/dynamic/grid.php",
                "type": "POST",
                "data": function (d) {
                    d.table = table;
                }
            },
            "scrollX": true,
            "scrollY": (alto / 2) - 50,
            "scrollCollapse": true,
            "paging": true,
            "bFilter": true,
            "pagingType": "full",
            "language": {
                "processing": "Cargando informacion por favor espere un momento",
                "search": "Buscar Registro&nbsp;",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "0 registros encontrados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "0 registros encontrados",
                "infoFiltered": "(filtrados de _MAX_ registros totales)",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                    "sFirst": "Inicio",
                    "sLast": "Final"
                }
            },
            "columnDefs": [
                {"width": "auto", "targets": 0}
            ]
        });
        t.columns().eq(0).each(function (colIdx) {
            $('input', $('.filters td')[colIdx]).on('keyup change', function () {
                t.column(colIdx).search(this.value).draw();
            });
        });
    }).done(function () {
        switch (table) {
            case 'inventarioBodegas':
                loadBodegasEmpresa('idBodegas');
                loadDocumentos('entradas');
                break;
            case 'inventarioSucursales':
                loadSucursalesEmpresa('idSucursales');
                loadDocumentos('entradas');
                break;
        }
        $("#loader").hide();
    });
}
//
function AddRecord(table, btnNew, btnUpdate, btnDelete, modulo, opcion) {
    $("#loader").show();
    params = {
        table: table,
        flag: 1,
        btnNew: btnNew,
        btnUpdate: btnUpdate,
        btnDelete: btnDelete,
        modulo: modulo,
        opcion: opcion
    };
    //var page = "forms.php";
    var page = "";
    switch (table) {
        case 'hrmEmpleados':
            page = "formsEmpleados.php";
            break;
        case 'clientes':
            page = "formsClientes.php";
            break;
        case 'productos':
            page = "formsProductos.php";
            break;
        default:
            page = "forms.php";
            break;
    }
    $.post('views/dynamic/' + page, params, function (respuesta) {
        $('#controllers').html(respuesta);
        switch (table) {
            case 'hrmEmpleados':
                $(".modal-dialog").addClass('modal-lg');
                $('.modal-title').html('Ficha del Empleado');
                break;
            case 'clientes':
                $(".modal-dialog").addClass('modal-lg');
                $('.modal-title').html('Ficha del Cliente');
                break;
            case 'productos':
                $(".modal-dialog").addClass('modal-lg');
                $('.modal-title').html('Ficha del Producto');
                break;
            default:
                $(".modal-dialog").removeClass('modal-lg');
                $('.modal-title').html('Agregar ' + opcion);
                break;
        }
        $("#seccion1").removeClass('col-sm-9').addClass('col-sm-12');
        $("#seccion2").hide();
        $('.selectpicker').selectpicker();
        $('.bootstrap-select .btn').addClass('btn-sm');
    }).done(function () {
        $("#modal1").modal('show');
        $(".date").datetimepicker({
            format: 'DD-MM-YYYY',
            pickTime: false
        });
        $(".time").datetimepicker({
            pickDate: false,
            use24hours: true,
            format: 'HH:mm:[00]'
        });
        $(".timestamp").datetimepicker({
            use24hours: true,
            format: 'DD-MM-YYYY HH:mm:[00]'
        });
        //
        $("#loader").hide();
        if ($("#dbProject").val() === 'pos_kasualcosmeticos') {
            //CARGA DE FAMLIAS EN FICHA DE PRODUCTO
            $("#idFamiliaNivel1").on('change', function () {
                $("#loader").show();
                params = {
                    service: 'getFamiliaForm',
                    table: 'familiaNivel2',
                    parametros: ' where idFamiliaNivel1=' + $(this).val() + ''
                };
                $("#idFamiliaNivel2,#idFamiliaNivel3").selectpicker("destroy");
                $("#idFamiliaNivel2,#idFamiliaNivel3").html('');
                $.post('controllers/adminController.php', params, function (data) {
                    $("#idFamiliaNivel2").append("<option value=''>[Seleccione...]</option>");
                    if (data !== null) {
                        $.each(data, function (key, val) {
                            $("#idFamiliaNivel2").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
                        });
                    }
                }, 'json').done(function () {
                    $("#idFamiliaNivel2").selectpicker().addClass('btn-sm');
                    $("#loader").hide();
                });
            });
            //
            $("#idFamiliaNivel2").on('change', function () {
                $("#loader").show();
                params = {
                    service: 'getFamiliaForm',
                    table: 'familiaNivel3',
                    parametros: ' where idFamiliaNivel2=' + $(this).val() + ''
                };
                $("#idFamiliaNivel3").selectpicker("destroy");
                $("#idFamiliaNivel3").html('');
                $.post('controllers/adminController.php', params, function (data) {
                    $("#idFamiliaNivel3").append("<option value=''>[Seleccione...]</option>");
                    if (data !== null) {
                        $.each(data, function (key, val) {
                            $("#idFamiliaNivel3").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
                        });
                    }
                }, 'json').done(function () {
                    $("#idFamiliaNivel3").selectpicker().addClass('btn-sm');
                    $("#loader").hide();
                });
            });
            //END CARGA DE FAMLIAS EN FICHA DE PRODUCTO
        }
        //
        $("#btnUpload").prop('disabled', true);
        $("#documentoAdjuntos").prop('disabled', true);
        $("#tabsDocumentosAdjuntos,#tabsPagosCuotas").hide();

        //
        $("#montoLiquidado").val(0).attr('disabled', true);
        $("#montoSinLiquidar").val(0).attr('disabled', true);
    });
}
//
function saveRecord(campos, table, btnNew, btnUpdate, btnDelete, modulo, opcion, camposRequeridos, names) {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var names = names.split(",");
    $.each(camposRequeridos.split(","), function (index, value) {
        if (!$("#" + value + "").val()) {
            flag = false;
            errorMsg += 'Campo ' + names[index] + ' es requerido\n';
        }
    });
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        var fields = campos.split(",");
        var params = {
            table: table,
            flag: 1,
            btnNew: btnNew,
            btnUpdate: btnUpdate,
            btnDelete: btnDelete,
            modulo: modulo,
            opcion: opcion,
        };
        for (var i = 0; i < fields.length; i++) {
            params[fields[i]] = $("#" + fields[i] + "").val();
        }
        $.post('views/dynamic/busquedas.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $("#modulo").html(modulo);
            $("#opcion").html(opcion);
            $('#dynamicTable .filters td').each(function () {
                var title = $(this).text();
                if (title !== 'No.' && title !== 'Item') {
                    $(this).html('<input type="text" class="form-control input-sm" placeholder="Buscar" style="width:100%;"/>');
                }
            });
            var t = $('#dynamicTable').DataTable({
                "order": [[1, "asc"]],
                "pageLength": 25,
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "views/dynamic/grid.php",
                    "type": "POST",
                    "data": function (d) {
                        d.table = 'vw_' + params.table;
                    }
                },
                "scrollX": true,
                "scrollY": (alto / 2) - 50,
                "scrollCollapse": true,
                "paging": true,
                "bFilter": true,
                //"pagingType": "full",
                "language": {
                    "processing": "Cargando informacion por favor espere un momento",
                    "search": "Buscar Registro&nbsp;",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "0 registros encontrados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "0 registros encontrados",
                    "infoFiltered": "(filtrados de _MAX_ registros totales)",
                    "oPaginate": {
                        "sPrevious": "Anterior",
                        "sNext": "Siguiente",
                        "sFirst": "Inicio",
                        "sLast": "Final"
                    }
                },
                "columnDefs": [
                    {"width": "auto", "targets": 0}
                ]
            });
            t.columns().eq(0).each(function (colIdx) {
                $('input', $('.filters td')[colIdx]).on('keyup change', function () {
                    t.column(colIdx).search(this.value).draw();
                });
            });
            $("#message").delay(2500).fadeOut('slow');
        }).done(function () {
            $("#modal1").modal('hide');
            $("#loader").hide();
        });
    }
}
//
function updateRecord(table, btnNew, btnUpdate, btnDelete, modulo, opcion) {
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
        $("#loader").show();
        params = {
            table: table,
            field: 'id',
            value: id,
            flag: 2,
            btnNew: btnNew,
            btnUpdate: btnUpdate,
            btnDelete: btnDelete,
            modulo: modulo,
            opcion: opcion
        };
        //var page = "forms.php";
        var page = "";
        switch (table) {
            case 'hrmEmpleados':
                page = "formsEmpleados.php";
                break;
            case 'clientes':
                page = "formsClientes.php";
                break;
            case 'productos':
                page = "formsProductos.php";
                break;
            default:
                page = "forms.php";
                break;
        }
        $.post('views/dynamic/' + page, params, function (respuesta) {
            switch (table) {
                case 'hrmEmpleados':
                    $('#page-container').html(respuesta);
                    $(".modal-dialog").addClass('modal-lg');
                    $('.modal-title').html('Ficha del Empleado');
                    break;
                case 'clientes':
                    $('#page-container').html(respuesta);
                    $(".modal-dialog").addClass('modal-lg');
                    $('.modal-title').html('Ficha del Cliente');
                    break;
                case 'productos':
                    $('#page-container').html(respuesta);
                    $(".modal-dialog").addClass('modal-lg');
                    $('.modal-title').html('Ficha del Producto');
                    break;
                default:
                    $('#controllers').html(respuesta);
                    $(".modal-dialog").removeClass('modal-lg');
                    $('.modal-title').html('Agregar ' + opcion);
                    $("#modal1").modal('show');
                    break;
            }
            //UPLOAD FOTO
            $("input[type='image']").click(function () {
                $("input[id='photo']").click();
            });
            $("#photo").on('change', function (event) {
                var output = document.getElementById('preview');
                output.src = URL.createObjectURL(event.target.files[0]);
            });
            //END UPLOAD FOTO
            $('.selectpicker').selectpicker();
            $('.bootstrap-select .btn').addClass('btn-sm');
        }).done(function () {
            $(".date").datetimepicker({
                format: 'DD-MM-YYYY',
                pickTime: false
            });
            $(".time").datetimepicker({
                pickDate: false,
                use24hours: true,
                format: 'HH:mm:[00]'
            });
            $(".timestamp").datetimepicker({
                use24hours: true,
                format: 'DD-MM-YYYY HH:mm:[00]'
            });
            $("#loader").hide();
            if ($("#dbProject").val() === 'pos_kasualcosmeticos') {
                //CARGA DE FAMLIAS EN FICHA DE PRODUCTO
                $("#idFamiliaNivel1").on('change', function () {
                    $("#loader").show();
                    params = {
                        service: 'getFamiliaForm',
                        table: 'familiaNivel2',
                        parametros: ' where idFamiliaNivel1=' + $(this).val() + ''
                    };
                    $("#idFamiliaNivel2,#idFamiliaNivel3").selectpicker("destroy");
                    $("#idFamiliaNivel2,#idFamiliaNivel3").html('');
                    $.post('controllers/adminController.php', params, function (data) {
                        $("#idFamiliaNivel2").append("<option value=''>[Seleccione...]</option>");
                        if (data !== null) {
                            $.each(data, function (key, val) {
                                $("#idFamiliaNivel2").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
                            });
                        }
                    }, 'json').done(function () {
                        $("#idFamiliaNivel2").selectpicker().addClass('btn-sm');
                        $("#loader").hide();
                    });
                });
                //
                $("#idFamiliaNivel2").on('change', function () {
                    $("#loader").show();
                    params = {
                        service: 'getFamiliaForm',
                        table: 'familiaNivel3',
                        parametros: ' where idFamiliaNivel2=' + $(this).val() + ''
                    };
                    $("#idFamiliaNivel3").selectpicker("destroy");
                    $("#idFamiliaNivel3").html('');
                    $.post('controllers/adminController.php', params, function (data) {
                        $("#idFamiliaNivel3").append("<option value=''>[Seleccione...]</option>");
                        if (data !== null) {
                            $.each(data, function (key, val) {
                                $("#idFamiliaNivel3").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
                            });
                        }
                    }, 'json').done(function () {
                        $("#idFamiliaNivel3").selectpicker().addClass('btn-sm');
                        $("#loader").hide();
                    });
                });
                //END CARGA DE FAMLIAS EN FICHA DE PRODUCTO
            }
            //
            $("#idTipoCuota").on('change', function () {
                if ($(this).val() === '1') {
                    $("#fechaFin").attr('disabled', true);
                    $("#noCuotas").val('0').attr('disabled', true);
                } else {
                    $("#fechaFin").attr('disabled', false);
                    $("#noCuotas").val('').attr('disabled', false);
                }
            });
            //
            cambioDePrecios();
        });
    }
}
//
function saveUpdateRecord(campos, table, campo, valor, btnNew, btnUpdate, btnDelete, modulo, opcion, camposRequeridos, camposRequeridosNombre) {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var names = camposRequeridosNombre.split(",");
    $.each(camposRequeridos.split(","), function (index, value) {
        if (!$("#" + value + "").val()) {
            flag = false;
            errorMsg += 'Campo ' + names[index] + ' es requerido\n';
        }
    });
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        $("#loader").show();
        var fields = campos.split(",");
        var params = {
            table: table,
            campo: campo,
            valor: valor,
            flag: 2,
            btnNew: btnNew,
            btnUpdate: btnUpdate,
            btnDelete: btnDelete,
            modulo: modulo,
            opcion: opcion,
        };
        for (var i = 0; i < fields.length; i++) {
            params[fields[i]] = $("#" + fields[i] + "").val();
        }
        //params += "table=" + table + "&campo=" + campo + "&valor=" + valor + "&flag=2&btnNew=" + btnNew + "&btnUpdate=" + btnUpdate + "&btnDelete=" + btnDelete + "&modulo=" + modulo + "&opcion=" + opcion + "";
        $.post('views/dynamic/busquedas.php', params, function (respuesta) {
            $('#page-container').html(respuesta);
            $("#modulo").html(modulo);
            $("#opcion").html(opcion);
            $('#dynamicTable .filters td').each(function () {
                var title = $(this).text();
                if (title !== 'No.' && title !== 'Item') {
                    $(this).html('<input type="text" class="form-control input-sm" placeholder="Buscar" style="width:100%;"/>');
                }
            });
            var t = $('#dynamicTable').DataTable({
                "order": [[1, "asc"]],
                "pageLength": 25,
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "views/dynamic/grid.php",
                    "type": "POST",
                    "data": function (d) {
                        d.table = 'vw_' + table;
                    }
                },
                "scrollX": true,
                "scrollY": (alto / 2) - 50,
                "scrollCollapse": true,
                "paging": true,
                "bFilter": true,
                //"pagingType": "full",
                "language": {
                    "processing": "Cargando informacion por favor espere un momento",
                    "search": "Buscar Registro&nbsp;",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "0 registros encontrados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "0 registros encontrados",
                    "infoFiltered": "(filtrados de _MAX_ registros totales)",
                    "oPaginate": {
                        "sPrevious": "Anterior",
                        "sNext": "Siguiente",
                        "sFirst": "Inicio",
                        "sLast": "Final"
                    }
                },
                "columnDefs": [
                    {"width": "auto", "targets": 0}
                ]
            });
            t.columns().eq(0).each(function (colIdx) {
                $('input', $('.filters td')[colIdx]).on('keyup change', function () {
                    t.column(colIdx).search(this.value).draw();
                });
            });
            $("#message").delay(2500).fadeOut('slow');
        }).done(function () {
            $("#modal1").modal('hide');
            $("#loader").hide();
        });
    }
}
//
function deleteRecord(table, btnNew, btnUpdate, btnDelete, modulo, opcion) {
    var id;
    $('.data').each(function () {
        if (this.checked) {
            id = $(this).val();
        }
    });
    if (id === undefined) {
        bootbox.alert('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Debe seleccionar un registro para realizar esta acción</div>');
    } else {
        bootbox.confirm('<br/><div class="alert alert-warning" role="alert"> <strong>Alerta!</strong> Esta seguro de eliminar el registro seleccionado?</div>', function (respuesta) {
            if (respuesta) {
                $("#loader").show();
                params = {
                    flag: 3,
                    id: id,
                    table: 'vw_' + table,
                    btnNew: btnNew,
                    btnUpdate: btnUpdate,
                    btnDelete: btnDelete,
                    modulo: modulo,
                    opcion: opcion
                };
                $.post('views/dynamic/busquedas.php', params, function (respuesta) {
                    $('#page-container').html(respuesta);
                    $("#modulo").html(modulo);
                    $("#opcion").html(opcion);
                    $('#dynamicTable .filters td').each(function () {
                        var title = $(this).text();
                        if (title !== 'No.' && title !== 'Item') {
                            $(this).html('<input type="text" class="form-control input-sm" placeholder="Buscar" style="width:100%;"/>');
                        }
                    });
                    var t = $('#dynamicTable').DataTable({
                        "order": [[1, "asc"]],
                        "pageLength": 25,
                        "pagingType": "full_numbers",
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": "views/dynamic/grid.php",
                            "type": "POST",
                            "data": function (d) {
                                d.table = 'vw_' + table;
                            }
                        },
                        "scrollX": true,
                        "scrollY": (alto / 2) - 50,
                        "scrollCollapse": true,
                        "paging": true,
                        "bFilter": true,
                        "pagingType": "full",
                        "language": {
                            "processing": "Cargando informacion por favor espere un momento",
                            "search": "Buscar Registro&nbsp;",
                            "lengthMenu": "Mostrar _MENU_ registros",
                            "zeroRecords": "0 registros encontrados",
                            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                            "infoEmpty": "0 registros encontrados",
                            "infoFiltered": "(filtrados de _MAX_ registros totales)",
                            "oPaginate": {
                                "sPrevious": "Anterior",
                                "sNext": "Siguiente",
                                "sFirst": "Inicio",
                                "sLast": "Final"
                            }
                        },
                        "columnDefs": [
                            {"width": "auto", "targets": 0}
                        ]
                    });
                    t.columns().every(function () {
                        var that = this;
                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                    $("#message").delay(2500).fadeOut('slow');
                }).done(function () {
                    $("#loader").hide();
                });
            } else {
            }
        });
    }
}
//******************************************************************************************
function AddRecordBusqueda(table, title) {
    params = {
        table: table,
        flag: 1,
        title: title
    };
    $.post('views/dynamic/formsBusquedas.php', params, function (respuesta) {
        $('#controllers').html(respuesta);
    }).done(function () {
        $("#modal2").modal('show');
        $(".modal-dialog").addClass('modal-md');
        $('.modal-title').html('Agregar ' + title);
        switch (table) {
            case 'proveedores':
                $("#nitP").val($("#nit").val());
                break;
        }
    });
}
//
function saveRecordBusqueda(campos, table, title, camposRequeridos, names) {
    var errorMsg = "Corrige los siguiente errores:\n";
    var flag = true;
    var names = names.split(",");
    $.each(camposRequeridos.split(","), function (index, value) {
        if (!$("#" + value + "").val()) {
            flag = false;
            errorMsg += 'Campo ' + names[index] + ' es requerido\n';
        }
    });
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var fields = campos.split(",");
        var params = "";
        for (var i = 0; i < fields.length; i++) {
            var field = fields[i] + "=" + $("#" + fields[i] + "").val() + "&";
            params += field;
        }
        params += "table=" + table + "&flag=1&title=" + title + "";
        $.post("views/dynamic/search.php?" + params + "", function (respuesta) {
            $('#controllers').html(respuesta);
            $("#message").fadeOut(3000);
            //
            $('#busquedas').dataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "views/dynamic/searchData.php",
                    "data": function (d) {
                        d.table = table;
                        d.idEmpresas = $("#idEmpresasC").val();
                    }
                },
                "language": {
                    "processing": "Cargando informacion por favor espere un momento",
                    "search": "Buscar Registro&nbsp;",
                    "lengthMenu": "Mostrar _MENU_ registros por pagina",
                    "zeroRecords": "0 registros encontrados",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "0 registros encontrados",
                    "infoFiltered": "(filtrados de _MAX_ registros totales)",
                    "oPaginate": {
                        "sPrevious": "Anterior",
                        "sNext": "Siguiente"
                    }
                },
                "scrollY": "250px",
                "scrollX": true,
                "scrollCollapse": true,
                "paging": true,
                "bFilter": true,
                "pagingType": "full",
                "language": {
                    "processing": "Cargando informacion por favor espere un momento",
                    "search": "Buscar Registro&nbsp;",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "0 registros encontrados",
                    "info": "Mostrando pagina _PAGE_ de _PAGES_",
                    "infoEmpty": "0 registros encontrados",
                    "infoFiltered": "(filtrados de _MAX_ registros totales)",
                    "oPaginate": {
                        "sPrevious": "Anterior",
                        "sNext": "Siguiente",
                        "sFirst": "Inicio",
                        "sLast": "Final"
                    }
                }
            });
        });
        $("#modal1").modal('show');
        $("#myModalLabel").html(title);
    }
}
//*******************************************************************************
function AddEmpresa(table, title) {
    $("#modal1").modal('show');
    params = {
        table: table,
        flag: 1,
        title: title
    };
    $.post('views/dynamic/formsEmpresas.php', params, function (respuesta) {
        $('#controllers').html(respuesta);
        $('.modal-title').html(title);
    });
}
//
function saveEmpresa(campos, table, title) {
    fields = campos.split(",");
    var dataForm = [];
    for (var i = 0; i < fields.length; i++) {
        var arr = {};
        arr[fields[i]] = $("#" + fields[i] + "").val();
        dataForm.push(arr);
    }
    params = {
        table: table,
        data: dataForm,
        title: title,
        service: 'save'
    };
    $.post('controllers/inventariosController.php', params).done(function (data) {
        location.reload();
    });
}
//*******************************************************************************
function upload() {
    if (!$('#my_file').val()) {
        alert('Seleccione una foto para agregar a la ficha');
    } else {
        var fsize = Math.round(parseFloat($('#my_file')[0].files[0].size) / 1048576 * 100) / 100;
        //obtiene el peso de la imagen en mb
        var ftype = $('#my_file')[0].files[0].type;
        // obtiene la extension de la imagen
        if (fsize > '4') {
            alert('Tamano maximo de imagen permitido 4 megas');
            return false;
        }
        if (ftype !== 'image/png' && ftype !== 'image/jpeg') {
            alert('Tipos de imagenes permitidas png o jpg');
            return false;
        }
        $("#btnUpload").addClass('disabled');
        $("#loader").show();
        var file_data = $('#my_file').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('id', $("#idPacientesUpload").val());
        $.ajax({
            url: 'views/dynamic/upload.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                var data = JSON.parse(response);
                $.each(data, function (key, val) {
                    if (val.message === 'uploadSuccess') {
                        alert('Foto agregada exitosamente');
                        $("#loader").fadeOut('slow').hide();
                        $("#btnUpload").removeClass('disabled');
                    } else {
                        alert('Error al agregar foto');
                    }
                });
            }
        });
    }
}
//
function templateDownload(modulo) {
    var nombrePlantilla = "";
    switch (modulo) {
        case 'vw_hrmHorasExtras':
            nombrePlantilla = "tpl_hrmHorasExtras.csv";
            break;
        case 'vw_hrmComisiones':
            nombrePlantilla = "tpl_hrmComisiones.csv";
            break;
        case 'vw_hrmOtrosPagosDescuentos':
            nombrePlantilla = "tpl_hrmOtrosPagosDescuentos.csv";
            break;
        case 'vw_hrmPrestamos':
            nombrePlantilla = "tpl_hrmPrestamos.csv";
            break;
        case 'vw_hrmAnticipos':
            nombrePlantilla = "tpl_hrmAnticipos.csv";
            break;
        case 'vw_hrmPoliticasPago':
            nombrePlantilla = "tpl_hrmPoliticasPago.csv";
            break;
        case 'pedidosDetalle':
            nombrePlantilla = "tpl_pedidosDetalle.csv";
            break;
    }
    window.location.href = 'views/plantillas/' + nombrePlantilla;
}
//
function uploadTemplate(modulo) {
    if (!$('#my_file').val()) {
        alert('Seleccione la plantilla para importar la información');
    } else {
        var ftype = $('#my_file')[0].files[0].type;
        console.log(ftype);
        // return false;
        //if (ftype !== 'text/csv') {
//        if (ftype !== 'application/vnd.ms-excel') {
//            alert('Archivo invalido, solo se permiten archivos CSV');
//            return false;
//        }
        var file_data = $('#my_file').prop('files')[0];
        var form_data = new FormData();
        form_data.append('service', 'cargarPlantilla');
        form_data.append('modulo', modulo);
        form_data.append('file', file_data);
        $.ajax({
            url: 'views/dynamic/uploadCSV.php',
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
                        alert('Información importada exitosamente');
                        $("#btnUpload").removeClass('disabled');
                        switch (modulo) {
                            case 'vw_hrmHorasExtras':
                                loadData('vw_hrmHorasExtras', 'Planilla', 'Ingreso de Horas Extras', 1, 1, 1);
                                break;
                            case 'vw_hrmComisiones':
                                loadData('vw_hrmComisiones', 'Planilla', 'Ingreso de Bono Puntos', 1, 1, 1);
                                break;
                            case 'vw_hrmOtrosPagosDescuentos':
                                loadData('vw_hrmOtrosPagosDescuentos', 'Planilla', 'Ingreso de Otros Pagos y Descuentos', 1, 1, 1);
                                break;
                            case 'vw_hrmPrestamos':
                                loadData('vw_hrmPrestamos', 'Planilla', 'Prestamos', 1, 1, 1);
                                break;
                            case 'vw_hrmAnticipos':
                                loadData('vw_hrmAnticipos', 'Planilla', 'Anticipos', 1, 1, 1);
                                break;
                            case 'vw_hrmPoliticasPago':
                                loadData('vw_hrmPoliticasPago', 'Planilla', 'Pago de Politicas', 1, 1, 1);
                                break;
                        }
                    } else {
                        alert('Error al importar información');
                    }
                });
            }
        });
    }
}
//
function uploadTemplatePedidos() {
    if (!$('#my_file').val()) {
        alert('Seleccione la plantilla para importar la información');
    } else {
        var ftype = $('#my_file')[0].files[0].type;
        console.log(ftype);
        // return false;
        //if (ftype !== 'text/csv') {
        if (ftype !== 'application/vnd.ms-excel') {
            alert('Archivo invalido, solo se permiten archivos CSV');
            return false;
        }
        var file_data = $('#my_file').prop('files')[0];
        var form_data = new FormData();
        form_data.append('service', 'cargarPedidosDetalle');
        form_data.append('ingresoA', $("#ingresoA").val());
        form_data.append('idPuntoIngreso', $("#idPuntoIngreso").val());
        form_data.append('valExistencias', accounting.unformat($("#valExistencias").val()));
        form_data.append('file', file_data);
        $.ajax({
            url: 'views/dynamic/uploadCSV.php',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (response) {
                alert('Información importada exitosamente');
                $("#btnUpload").removeClass('disabled');
                loadProductosPedido();
            }
        });
    }
}
//
function cambioDePrecios() {
    $('#precioCosto').on('keydown', function (e) {
        if (e.which == 13 || e.which === 9) {
            console.log($(this).val());
            generarPrecios(2);
        }
    });
    $('#margen').on('keydown', function (e) {
        if (e.which == 13 || e.which === 9) {
            generarPrecios(1);
        }
    });
    $('#precioPublico').on('keydown', function (e) {
        if (e.which == 13 || e.which === 9) {
            generarPrecios(2);
        }
    });
}
//
function generarPrecios(action) {
    var costo = accounting.unformat($("#precioCosto").val());
    var margen = accounting.unformat($("#margen").val());
    var precio = accounting.unformat($("#precioPublico").val());
    //
    var precioNuevo = accounting.unformat(costo + (costo * (margen / 100)));
    var margenNuevo = accounting.unformat(((((precio - costo) / precio) * 100) * 100) / 100);
    //
    switch (action) {
        case 1:
            //Obtener Precio Venta
            $("#precioPublico").val(accounting.formatNumber(precioNuevo, 2));
            break;
        case 2:
            //Obtener Margen
            $("#precioPublico").val(accounting.formatNumber(precio, 2));
            $("margen").val(accounting.formatNumber(margenNuevo, 2));
            break;
    }
}