$(document).ready(function(){
   //loadPermisos(); 
});
//
function loadRolesYModulos() {
    loadRoles();
    loadModulos();
}

function loadRoles() {
    params = {
        service: 'getRoles',
        nombreBD: $("#nombreBD").val()
    };
    $("#roles").html('');
    $.post('../controllers/controller.php', params, function (data) {
        $("#roles").append("<option value=''>Seleccione...</option>");
        $.each(data, function (key, val) {
            if ($("#rolSelect").val() == val.id) {
                $("#roles").append("<option selected value='" + val.id + "'>" + val.descripcion + "</option>");
            } else {
                $("#roles").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }
        });
    }, 'json');
}


function loadModulos() {
    params = {
        service: 'getModulos',
        nombreBD: $("#nombreBD").val()
    };
    $("#modulos").html('');
    $.post('../controllers/controller.php', params, function (data) {
        $("#modulos").append("<option value=''>Seleccione...</option>");
        $.each(data, function (key, val) {
            if ($("#moduloSelect").val() == val.id) {
                $("#modulos").append("<option selected value='" + val.id + "'>" + val.descripcion + "</option>");
            } else {
                $("#modulos").append("<option value='" + val.id + "'>" + val.descripcion + "</option>");
            }

        });
    }, 'json');
}

function loadPaginas() {
    $("#detalle").html('');
    datos = "";
    params = {
        service: 'getPaginaModulos',
        nombreBD: $("#nombreBD").val(),
        modulo: $("#modulos").val(),
    };
    params2 = {
        service: 'getPaginaRoles',
        nombreBD: $("#nombreBD").val(),
        rol: $("#roles").val()
    };
    $.post('../controllers/controller.php', params, function (data) {
        $.each(data, function (key, valModulo) {
            igual = 0;
            botonNew = 2;
            botonUpdate = 2;
            botonDelete = 2;
            $.post('../controllers/controller.php', params2, function (data2) {
                $.each(data2, function (key, valRol) {
                    if (valModulo.idPagina == valRol.idPagina) {
                        igual = 1;
                        console.log("--");                  
                        botonNew = valRol.btnsNew;
                        botonUpdate = valRol.btnsUpdate;
                        botonDelete = valRol.btnsDelete;
                    }
                });
            }, 'json');
 
            if (igual == 0) {
                datos += '<tr><td>' + valModulo.pagina + '</td>';
                datos += '<div class="checkbox"> <input type="hidden" class="checkitem" name="Paginas[]"  value="' + valModulo.idPaginas + '"';
                datos += ' <td>' + valModulo.pagina + '</td>';
                datos += '<div class="checkbox">';
                datos += '<input type="hidden" class="checkitem" name="Paginas[]"  value="' + valModulo.idPagina + '">';
                datos += '<td><input type="checkbox" class="checkitem"  name="agregarPermisos[]"  value="' + valModulo.Pagina + '"checked></td>';
                if (botonNew == 1 && valModulo.funcion == 'loadData') {
                    datos += '< td > < input type = "hidden" ID = "Bnew" name = "botonNew[]" value = "1" > < input type = "checkbox" class = "checkitem_N"  checked  onchange = "this.previousSibling.value = 1 - this.previousSibling.value" > < /td>';
                } else if (botonNew == 0 && valModulo.funcion == 'loadData') {
                    datos += '< td > < input type = "hidden" ID = "Bnew" name = "botonNew[]" value = "0" > < input type = "checkbox" class = "checkitem_N"  onchange = "this.previousSibling.value = 1 - this.previousSibling.value" >< /td>';
                } else {
                    datos += '< td > < input type = "hidden"  name = "botonNew[]" value = "2" > < /td>';
                }
                if (botonUpdate == 1 && valModulo.funcion == 'loadData') {
                    datos += '<td><input type="hidden" ID="Bupdate" name="botonUpdate[]" value="1"><input type="checkbox" class="checkitem_U"  checked onchange="this.previousSibling.value = 1 - this.previousSibling.value"> </td>';
                } else if (botonUpdate == 0 && valModulo['funcion'] == 'loadData') {
                    datos += ' <td><input type="hidden" ID="Bupdate" name="botonUpdate[]" value="0"><input type="checkbox"  class="checkitem_U" onchange="this.previousSibling.value = 1 - this.previousSibling.value"></td>';
                } else {
                    datos += ' <td>  <input type="hidden" name="botonUpdate[]" value="2">    </td>'
                }

                if (botonDelete == 1 && valModulo.funcion == 'loadData') {
                    datos += '<td><input type="hidden" ID="Bdelete" name="botonDelete[]" value="1"><input type="checkbox" class="checkitem_D"  checked="" onchange="this.previousSibling.value = 1 - this.previousSibling.value"></td>';
                } else if (botonDelete == 0 && valModulo.funcion == 'loadData') {
                    datos += '<td><input type="hidden" ID="Bdelete" name="botonDelete[]" value="0"><input type="checkbox" class="checkitem_D" onchange="this.previousSibling.value = 1 - this.previousSibling.value">  </td>';
                } else {
                    datos += '<td><input type="hidden" name="botonDelete[]" value="2">    </td>';
                }
                datos += '< /div> < /tr>';

            } else {
                datos += '<tr><td>  "' + valModulo.pagina + '" </td>';
                datos += '<div class="checkbox">';
                datos += '<input type="hidden" class="checkitem" name="Paginas[]"  value="' + valModulo.idPagina + '">';
                datos += '<td><input type="checkbox" class="checkitem" name="agregarPermisos[]"  value="' + valorModulo.idPagina + '"></td>';

                if (valModulo.funcion == 'loadData') {
                    datos += ' <td><input type="hidden" ID="Bnew" name="botonNew[]" value="0"><input type="checkbox" class="checkitem_N"  onchange="this.previousSibling.value = 1 - this.previousSibling.value"></td>';

                    datos += ' <td><input type="hidden" ID="Bupdate" name="botonUpdate[]" value="0"><input type="checkbox" class="checkitem_U"  onchange="this.previousSibling.value = 1 - this.previousSibling.value"></td>';

                    datos += '<td><input type="hidden" ID="Bdelete" name="botonDelete[]" value="0"><input type="checkbox" class="checkitem_D" onchange="this.previousSibling.value = 1 - this.previousSibling.value"></td>';

                } else {
                    datos += '<td> <input type="hidden" name="botonNew[]" value="2"></td>';
                    datos += '<td> <input type="hidden" name="botonUpdate[]" value="2"></td>';
                    datos += '<td> <input type="hidden" name="botonDelete[]" value="2"></td>';
                }

            }
            datos += '</div></tr>';

        });
        $("#detalle").append(datos);
    }, 'json');
}
//
function loadPermisos() {
    $.post('views/configuraciones/permisos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Configuraciones');
        $("#opcion").html('Permisos');
    });
}
//
function loadComprasDte(btnsNew, btnsUpdate, btnsDelete) {
    $.post('views/configuraciones/comprasDte.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Configuraciones');
        $("#opcion").html('Compras DTE');
    });
}
//
function getComprasDte() {
    $("#tblComprasDteBody").html('<tr><td colspan="6" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
    $.post('controllers/adminController.php', { service: 'getComprasDte' }, function (data) {
        var meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        var html = '';
        if (!data || data.length === 0) {
            html = '<tr><td colspan="6" class="text-center">Sin registros</td></tr>';
        } else {
            $.each(data, function (i, row) {
                var saldo     = parseInt(row.saldo);
                var emitidos  = parseInt(row.dteEmitidos);
                var colorSaldo = saldo < 0 ? '#d9534f' : (saldo < row.dteComprados * 0.2 ? '#f0ad4e' : '#5cb85c');
                var saldoLabel = saldo < 0
                    ? '<strong style="color:' + colorSaldo + '">&#9888; ' + saldo + '</strong>'
                    : '<strong style="color:' + colorSaldo + '">' + saldo + '</strong>';

                html += '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + row.periodo + '</td>' +
                    '<td>' + meses[parseInt(row.mes)] + '</td>' +
                    '<td class="text-right">' + accounting.formatNumber(row.dteComprados, 0) + '</td>' +
                    '<td class="text-right">' + accounting.formatNumber(emitidos, 0) + '</td>' +
                    '<td class="text-right">' + saldoLabel + '</td>' +
                    '<td>' + row.fechaRegistro + '</td>' +
                    '<td class="text-center">' +
                        '<button class="btn btn-danger btn-xs" onclick="eliminarComprasDte(' + row.id + ');">' +
                            '<i class="fa fa-trash"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>';
            });
        }
        $("#tblComprasDteBody").html(html);
    }, 'json');
}
//
function guardarComprasDte() {
    var anio      = $("#dteAnio").val();
    var mes       = $("#dteMes").val();
    var comprados = $("#dteComprados").val();

    if (!comprados || parseInt(comprados) < 1) {
        alert('Ingresá la cantidad de DTEs comprados.');
        return;
    }

    $.post('controllers/adminController.php', {
        service:      'guardarComprasDte',
        periodo:      anio,
        mes:          mes,
        dteComprados: comprados
    }, function (data) {
        if (data && data.success) {
            $("#dteComprados").val('');
            getComprasDte();
            getDteSaldo();
        } else {
            alert('Error al guardar. Intentá de nuevo.');
        }
    }, 'json');
}
//
function eliminarComprasDte(id) {
    if (!confirm('¿Eliminar este registro? Esta acción no se puede deshacer.')) return;
    $.post('controllers/adminController.php', {
        service: 'eliminarComprasDte',
        id:      id
    }, function (data) {
        if (data && data.success) {
            getComprasDte();
            getDteSaldo();
        } else {
            alert('Error al eliminar.');
        }
    }, 'json');
}
//
function getPermisos() {
    params = {
        service: 'getPermisos',
        idNivel: $("#idNivel").val(),
        idModulos: $("#idModulos").val()
    };
    $("#loader").show();
    $.post('controllers/configuracionesController.php', params, function (data) {
        $("#permisos tbody").html('');
        var datos = "";
        $.each(data, function (key, val) {
            var status1 = '';
            var status2 = '';
            var status3 = '';
            var status4 = '';
            var status5 = '';
            if (val.idPermisos !== '0') {
                status1 = 'checked';
            }
            if (val.btnsNew !== '0') {
                status2 = 'checked';
            }
            if (val.btnsUpdate !== '0') {
                status3 = 'checked';
            }
            if (val.btnsDelete !== '0') {
                status4 = 'checked';
            }
            if (val.btnsExport !== '0') {
                status5 = 'checked';
            }
            datos += "<tr>";
            datos += "<td>" + val.titulo + "</td>";
            datos += "<td class='text-center'><input type='checkbox' class='checkitem checkitem1' value='" + val.idPermisos + "' " + status1 + " data-id='1' data-value='" + val.idPaginas + "'/></td>";
            if (val.funcion === 'loadData') {
                datos += "<td class='text-center'><input type='checkbox' class='checkitem checkitem2 btnsNew-" + val.idPaginas + "' value='" + val.idPermisos + "' " + status2 + " data-id='2'/></td>";
                datos += "<td class='text-center'><input type='checkbox' class='checkitem checkitem3 btnsUpdate-" + val.idPaginas + "' value='" + val.idPermisos + "' " + status3 + " data-id='3'/></td>";
                datos += "<td class='text-center'><input type='checkbox' class='checkitem checkitem4 btnsDelete-" + val.idPaginas + "' value='" + val.idPermisos + "' " + status4 + " data-id='4'/></td>";
                datos += "<td class='text-center'><input type='checkbox' class='checkitem checkitem5 btnsExport-" + val.idPaginas + "' value='" + val.idPermisos + "' " + status5 + " data-id='5'/></td>";
            } else {
                datos += "<td colspan='4'></td>";
            }
            datos += "<td class='text-center'><input type='text' class='checkitem1 form-control input-sm ordenKPIS-" + val.idPaginas + "' value='" + accounting.unformat(val.ordenKPIS) + "'/></td>";
            datos += "</tr>";
        });
        $("#permisos tbody").html(datos);
    }, 'json').done(function () {
        $("#loader").hide();
        $(".checkall").change(function () {
            if ($(this).prop("checked") == false) {
                $(".checkitem" + $(this).data('id') + "").prop("checked", false).val(0);
            } else {
                $(".checkitem" + $(this).data('id') + "").prop("checked", true).val(1);
            }
        });
        $(".checkitem").change(function () {
            if ($(this).prop("checked") == false) {
                $(".checkall" + $(this).data('id') + "").prop("checked", false);
                $(this).val(0);
            } else {
                $(".checkall" + $(this).data('id') + "").prop("checked", true);
                $(this).val(1);
            }
        });
    });
}
//
function guardarPermisos() {
    var permisos = [];
    $(".checkitem1").each(function () {
        if ($(this).val() !== '0') {
            if (accounting.unformat($(this).data("value")) !== 0) {
                var arr = {};
                arr['idPaginas'] = $(this).data("value");
                arr['btnsNew'] = accounting.unformat(($(".btnsNew-" + $(this).data('value') + "").val() === undefined ? 0 : $(".btnsNew-" + $(this).data('value') + "").val()));
                arr['btnsUpdate'] = accounting.unformat(($(".btnsUpdate-" + $(this).data('value') + "").val() === undefined ? 0 : $(".btnsUpdate-" + $(this).data('value') + "").val()));
                arr['btnsDelete'] = accounting.unformat(($(".btnsDelete-" + $(this).data('value') + "").val() === undefined ? 0 : $(".btnsDelete-" + $(this).data('value') + "").val()));
                arr['btnsExport'] = accounting.unformat(($(".btnsExport-" + $(this).data('value') + "").val() === undefined ? 0 : $(".btnsExport-" + $(this).data('value') + "").val()));
                arr['ordenKPIS'] = accounting.unformat(($(".ordenKPIS-" + $(this).data('value') + "").val() === undefined ? 0 : $(".ordenKPIS-" + $(this).data('value') + "").val()));
                permisos.push(arr);
            }
        }
    });
    params = {
        service: 'guardarPermisos',
        permisos: permisos,
        idNivel: $("#idNivel").val(),
        idModulos: $("#idModulos").val()
    };
    //$("#loader").show();
    $.post('controllers/configuracionesController.php', params, function (data) {
        $.each(data, function (key, val) {
            if (val.message === 'success') {
                alert('Permisos asignados exitosamente');
            } else {
                console.log(val.Query);
                alert('Error al asignar permisos, comuniquese con el administrador del sistema');
            }
        });
    }, 'json').done(function () {
        $("#loader").hide();
    });
}