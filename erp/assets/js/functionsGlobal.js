var today;
var dbProject = $("#dbProject").val();
var idEmpresas = $("#idEmpresas").val();
var idRoles = $("#idRolesUsuario").val();

var _dtLang = {
  paginate: { previous: "‹", next: "›" },
  info: "Mostrando _START_-_END_ de _TOTAL_",
  infoEmpty: "Sin registros",
  infoFiltered: "(filtrado de _MAX_)",
  lengthMenu: "_MENU_ por página",
  search: "Buscar:",
  zeroRecords: "Sin resultados",
  emptyTable: "Sin datos disponibles"
};

function initDT(selector, opts) {
  if ($.fn.DataTable.isDataTable(selector)) {
    $(selector).DataTable().destroy();
  }
  $(selector).DataTable($.extend({ language: _dtLang }, opts || {}));
}

$(document).ready(function () {
  today = moment().format("DD-MM-YYYY");
  //checkSessionAlive();
  $(".option").click(function (e) {
    e.preventDefault();
    if (
      $(".navbar-collapse").is(":visible") &&
      $(".navbar-toggle").is(":visible")
    ) {
      $(".navbar-collapse").collapse("toggle");
    }
  });
  resumenDocumentosOperados();
  if (dbProject === "erp_suplegt" || dbProject === "erp_suple") {
    $(".depositosDetalle").show();
    //
    resumenDepositos("resumenDepositosMayoreo", 1);
    resumenDepositos("resumenDepositosMenudeo", 2);
    //
    setInterval(function () {
      resumenDocumentosOperados();
      resumenDepositos("resumenDepositosMayoreo", 1);
      resumenDepositos("resumenDepositosMenudeo", 2);
    }, 60000);
    $.ajaxSetup({
      cache: false,
    });
  } else {
    $(".depositosDetalle").hide();
  }
  $("#fechaInicio,#fechaFin,#fechaInicioM,#fechaFinM").datetimepicker({
    pickTime: false,
    format: "DD-MM-YYYY",
  });
  ventasHoy("Hoy");
  ventasHoy("Ayer");
  ventasHoy("MesHoy");
  ventasHoy("MesAyer");
  comprasHoy("MesHoy");
  comprasHoy("MesAyer");
  utilidad("MesHoy");
  utilidad("MesAyer");
  resumenInventario();
  ultimaTransaccion();
  top10hoy();
  top10mes();
  ultimas10Transacciones();
  totalDtes();
  getDteSaldo();
  google.charts.setOnLoadCallback(getDtesPorMes);
  ultimos7dias();
  currentYear();
  ventasHoy("MesAnterior");
});
//
function checkSessionAlive() {
  setTimeout(function () {
    params = {
      service: "sessionAlive",
    };
    //
    $.post(
      "controllers/adminController.php",
      params,
      function (data) {
        $.each(data, function (key, val) {
          if (val.message === "empty") {
            alert("Su sesión a expirado");
            window.location = "logout.php?action=logout";
          }
          console.log(val.message);
        });
      },
      "json"
    ).done(function () {
      console.log(moment().format("MMMM Do YYYY, h:mm:ss a"));
      checkSessionAlive();
    });
  }, 60000 * 1);
}
//
function conComas(valor) {
  var nums = new Array();
  var simb = ",";
  //Éste es el separador
  valor = valor.toString();
  valor = valor.replace(/\D/g, "");
  //Ésta expresión regular solo permitira ingresar números
  nums = valor.split("");
  //Se vacia el valor en un arreglo
  var long = nums.length - 1;
  // Se saca la longitud del arreglo
  var patron = 3;
  //Indica cada cuanto se ponen las comas
  var prox = 2;
  // Indica en que lugar se debe insertar la siguiente coma
  var res = "";

  while (long > prox) {
    nums.splice(long - prox, 0, simb);
    //Se agrega la coma
    prox += patron;
    //Se incrementa la posición próxima para colocar la coma
  }

  for (var i = 0; i <= nums.length - 1; i++) {
    res += nums[i];
    //Se crea la nueva cadena para devolver el valor formateado
  }

  return res;
}
//
function loadEmpresa(container) {
  params = {
    service: "getEmpresas",
  };
  $("#" + container).html("");
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#" + container).append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        $("#" + container).append(
          "<option value='" + val.id + "'>" + val.nombreComercial + "</option>"
        );
      });
    },
    "json"
  );
  //loadSucursalesEmpresa();
}
//
function loadBodegasEmpresa(container, idEmpresaIngreso) {
  params = {
    service: "getBodegas",
    idEmpresaIngreso: idEmpresaIngreso,
  };
  $("#" + container).html("");
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#" + container).append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        $("#" + container).append(
          "<option value='" + val.id + "'>" + val.descripcion + "</option>"
        );
      });
    },
    "json"
  );
  //loadSucursalesEmpresa();
}
//
function loadSucursalesEmpresa(container, idEmpresaIngreso) {
  params = {
    service: "getSucursales",
    idEmpresaIngreso: idEmpresaIngreso,
  };
  $("#" + container).html("");
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#" + container).append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        $("#" + container).append(
          "<option value='" + val.id + "'>" + val.descripcion + "</option>"
        );
      });
    },
    "json"
  );
  //loadSucursalesEmpresa();
}
//
function loadDocumentos(tipoDocumento, correlativo) {
  params = {
    service: "getDocumentos",
    tipo: tipoDocumento,
  };
  //
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#tipoDocumento").append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        if (key === 0) {
          $("#tipoDocumento").append(
            "<option value='" +
              val.id +
              "' selected=''>" +
              val.prefijo +
              "</option>"
          );
        } else {
          $("#tipoDocumento").append(
            "<option value='" + val.id + "'>" + val.prefijo + "</option>"
          );
        }
      });
    },
    "json"
  ).done(function () {
    $("#tipoDocumento option").each(function () {
      if ($(this).val() === $("#idDocumentosCorrelativos").val()) {
        $(this).prop("selected", true);
      }
    });
    if (accounting.unformat(correlativo) === 0) {
      loadDocumentosCorrelativo();
    }
  });
}
//
function loadDocumentosCorrelativo() {
  params = {
    service: "getDocumentosCorrelativo",
    idDocumento: $("#tipoDocumento").val(),
  };
  //
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $.each(data, function (key, val) {
        if (val.serie === "") {
          $("#correlativo").val(val.correlativo);
          $("#availablePrint").val(val.availablePrint);
        } else {
          $("#correlativo").val(val.serie + "-" + val.correlativo);
          $("#availablePrint").val(val.availablePrint);
          $("#numeroItemsFactura").val(val.numeroItems);
          $("#idFormato").val(val.idFormatos);
        }
      });
    },
    "json"
  );
}

function loadDocumentosCorrelativoLax(tipoDocumento) {
  params = {
    service: "getDocumentos",
    tipo: tipoDocumento,
  };
  //
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#tipoDocumento").append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        params = {
          service: "getDocumentosCorrelativo",
          idDocumento: val.id,
        };

        $.post(
          "controllers/adminController.php",
          params,
          function (data2) {
            $.each(data2, function (key, val2) {
              if (val.prefijo == "FACTURA") {
                if (val.serie === "") {
                  $("#correlativoFactura").val(val2.correlativo);
                  $("#availablePrint").val(val2.availablePrint);
                  $("#idCorrelativoFactura").val(val.id);
                } else {
                  $("#correlativoFactura").val(
                    val2.serie + "-" + val2.correlativo
                  );
                  $("#availablePrint").val(val2.availablePrint);
                  $("#idCorrelativoFactura").val(val.id);
                }
              } else if (val.prefijo == "PAGARE") {
                if (val.serie === "") {
                  $("#correlativoPagare").val(val2.correlativo);
                  $("#availablePrint").val(val2.availablePrint);
                  $("#idCorrelativoPagare").val(val.id);
                } else {
                  $("#correlativoPagare").val(
                    val2.serie + "-" + val2.correlativo
                  );
                  $("#availablePrint").val(val2.availablePrint);
                  $("#idCorrelativoPagare").val(val.id);
                }
              }
            });
          },
          "json"
        );
      });
    },
    "json"
  );
}
//
function busqueda(table, title, modulo, item) {
  $("#controllers").html("");
  var ingresoA = $("#ingresoA").val();
  var idPuntoIngreso = $("#idPuntoIngreso").val();
  switch (modulo) {
    case "ventasPorVendedor":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "ventasPorProducto":
      ingresoA = 2;
      idPuntoIngreso = $("#idSucursales").val();
      break;
    case "historialCostos":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "proveedores":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "compras":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "consumoMateriales":
      ingresoA = 2;
      idPuntoIngreso = $("#idSucursales").val();
      break;
    case "cuentasContables":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "centrosCosto":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "partidaContable":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "proveedores":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "clientes":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "FAC":
      ingresoA = "2";
      idPuntoIngreso = $("#idSucursales").val();
      break;
    case "pedidos":
      ingresoA = $("#ingresoA").val();
      idPuntoIngreso = $("#idPuntoIngreso").val();
      break;
    case "pedidos2":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "cuentasBancarias":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "traslados":
      ingresoA = $("#salidaDe").val();
      idPuntoIngreso = $("#idPuntoSalida").val();
      break;
    case "ajustes":
      ingresoA = $("#ingresoA").val();
      idPuntoIngreso = $("#idPuntoIngreso").val();
      break;
    case "componentes":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "boletos":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "inventarioSeries":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "cotizaciones":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "importaciones":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "ordenesCompra":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "cajaChica":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "cajaChicaCompras":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "cajaChicaReintegros":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "factRecurrente":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
    case "ordenCompra":
      ingresoA = "0";
      idPuntoIngreso = "0";
      break;
  }
  var errorMsg = "Corrige los siguiente errores:\n";
  var flag = true;
  if (!ingresoA || !idPuntoIngreso) {
    flag = false;
    switch (modulo) {
      case "kardex":
        errorMsg +=
          "Debe de seleccionar los campos\nInventario De y Lugar para mostrar la información";
        break;
      case "ventasPorProducto":
        errorMsg +=
          "Debe de seleccionar una sucursal para mostrar la información";
        break;
      case "consumoMateriales":
        errorMsg +=
          "Debe de seleccionar una sucursal para mostrar la información";
        break;
      case "traslados":
        errorMsg +=
          "Debe de seleccionar Bodega de Salida para mostrar la información";
        break;
      case "ajustes":
        errorMsg +=
          'Debe de seleccionar las opciones "Inventario De" y "Lugar" para mostrar la información';
        break;
      case "FAC":
        errorMsg +=
          "Debe de seleccionar una sucursal para mostrar la información";
        break;
      case "compras":
        errorMsg +=
          "Debe de seleccionar una sucursal para mostrar la información";
        break;
    }
  }
  if (flag === false) {
    alert(errorMsg);
    return false;
  } else {
    params = {
      table: table,
      title: title,
      modulo: modulo,
    };
    $("#controllers").load("views/dynamic/search.php", params, function () {
      var selected = [];
      $("#busquedas").dataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "views/dynamic/searchData.php",
          type: "POST",
          data: function (d) {
            d.table = table;
            d.modulo = modulo;
            d.ingresoA = ingresoA;
            d.idPuntoIngreso = idPuntoIngreso;
            d.bodega = $("#idBodegas option:selected").text();
            d.item = item;
          },
        },
        language: {
          processing: "Cargando informacion por favor espere un momento",
          search: "Buscar Registro&nbsp;",
          lengthMenu: "Mostrar _MENU_ registros",
          zeroRecords: "0 registros encontrados",
          info: "Mostrando pagina _PAGE_ de _PAGES_",
          infoEmpty: "0 registros encontrados",
          infoFiltered: "(filtrados de _MAX_ registros totales)",
          oPaginate: {
            sPrevious: "Anterior",
            sNext: "Siguiente",
            sFirst: "Inicio",
            sLast: "Final",
          },
        },
        scrollX: true,
        scrollY: "250px",
        scrollCollapse: true,
        paging: true,
        bFilter: true,
        pagingType: "full",
        initComplete: function () {
          $("#busquedas_filter label input").focus();
          if (table === "inventario" && modulo === "Facturacion") {
            $("#busquedas_filter label input").val($("#codigo").val());
          }
        },
      });
    });
    $("#modal1").modal("show");
    $("#myModalLabel").html(title);
    if (
      table === "productos" ||
      table === "inventario" ||
      table === "vw_productos"
    ) {
      $(".modal-dialog").addClass("modal-lg");
    } else {
      $(".modal-dialog").removeClass("modal-lg");
    }
  }
  setTimeout(function () {
    console.log("search");
    $("#busquedas")
      .dataTable()
      .fnFilter($("#busquedas_filter label input").val());
  }, 2000);
}
//
function loadVendedores(modulo) {
  params = {
    service: "getVendedores",
    modulo: modulo,
  };
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#vendedores").append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        if ($("#idUsuariosPedidos").val() === val.id) {
          $("#vendedores").append(
            "<option value='" +
              val.id +
              "' selected=''>" +
              val.userName +
              "</option>"
          );
        } else {
          $("#vendedores").append(
            "<option value='" + val.id + "'>" + val.userName + "</option>"
          );
        }
      });
    },
    "json"
  );
}
//
function loadVendedoresLaxTravel(modulo) {
  params = {
    service: "getVendedoresLax",
    modulo: modulo,
  };
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#vendedores").append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        if ($("#idUsuariosPedidos").val() === val.codigoUsuario) {
          $("#vendedores").append(
            "<option value='" +
              val.codigoUsuario +
              "' selected=''>" +
              val.userName +
              "</option>"
          );
        } else {
          $("#vendedores").append(
            "<option value='" +
              val.codigoUsuario +
              "'>" +
              val.userName +
              "</option>"
          );
        }
      });
    },
    "json"
  );
}
//
function loadProveedoresLaxTravel(modulo) {
  params = {
    service: "getProveedoresLax",
    modulo: modulo,
  };
  $("#proveedores").html("");
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $.each(data, function (key, val) {
        if ($("#proveedores").val() === val.id) {
          $("#proveedores").append(
            "<option value='" +
              val.id +
              "' selected=''>" +
              val.descripcion +
              "</option>"
          );
        } else {
          $("#proveedores").append(
            "<option value='" + val.id + "'>" + val.descripcion + "</option>"
          );
        }
      });
    },
    "json"
  );
}
//
function loadProducto(
  idProducto,
  codigo,
  desc,
  existencia,
  precioPublico,
  tipoProducto,
  modulo,
  utilizaSerie
) {
  //console.log(idProducto, codigo, ' ', desc, ' ', existencia);
  $("#idProducto").val(idProducto);
  $("#codigo").val(codigo);
  $("#codigo2").val(codigo);
  $("#descProducto").val(desc);
  $("#descProducto2").val(desc);
  $("#existencia").val(existencia);
  if (modulo !== "compras" && modulo !== "ordenCompra") {
    $("#precioProducto").val(precioPublico);
  }
  $("#tipoProducto").val(tipoProducto);
  $("#utilizaSerie").val(utilizaSerie);
  $("#modal1").modal("hide");
  switch (modulo) {
    case "inventarioSeries":
      $("#serie").focus();
      break;
    case "compras":
      $("#precioProducto").val("");
      $("#descuentoProducto").focus();
      $("#descuentoProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#precioProducto").focus();
        }
      });
      $("#precioProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#cantidadProducto").focus();
        }
      });
      $("#cantidadProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          var totalProducto = accounting.unformat(
            $(this).val() * $("#precioProducto").val() -
              accounting.unformat($("#descuentoProducto").val())
          );
          $("#totalProducto")
            .val(accounting.formatNumber(totalProducto, 2))
            .focus();
        }
      });
      break;
    case "ventas":
      $("#cantidad").focus();
      break;
    case "ajustes":
      $("#cantidad").focus();
      break;
    case "importaciones":
      $("#precioProducto").val("");
      $("#cantidad").focus();
      $("#cantidad").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#peso").focus();
        }
      });
      $("#peso").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#arancel").focus();
        }
      });
      $("#arancel").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#precioProducto").focus();
        }
      });
      $("#precioProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          var cantidad = accounting.unformat($("#cantidad").val());
          var arancel = accounting.unformat($("#arancel").val()) / 100;
          var precioProducto = accounting.unformat($("#precioProducto").val());
          var arancelProducto = precioProducto * arancel;
          var precioProductoNew = precioProducto + arancelProducto;
          var totalProducto = precioProductoNew * cantidad;
          $("#totalProducto")
            .val(accounting.formatNumber(totalProducto, 2))
            .focus();
        }
      });
      break;
    case "traslados":
      $("#cantidad").focus();
      break;
    case "ordenCompra":
      $("#precioProducto").focus();
      $("#precioProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#cantidadProducto").focus();
          $("#cantidad").focus();
        }
      });
      //$("#monto").val(precioPublico).focus();
      break;
    default:
      $("#precioProducto").focus();
      $("#precioProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#cantidadProducto").focus();
          $("#cantidad").focus();
        }
      });
      $("#monto").val(precioPublico).focus();
      break;
  }
  if (
    modulo !== "compras" &&
    modulo !== "ordenCompra" &&
    modulo !== "importaciones" &&
    modulo !== "FAC"
  ) {
    var editarPrecio = accounting.unformat($("#editarPrecio").val());
    console.log(editarPrecio);
    if (editarPrecio === 0) {
      $("#precioProducto").attr("readonly", true);
      $("#cantidad").focus();
      $("#cantidadProducto").focus();
    } else {
      $("#precioProducto").attr("readonly", false);
    }
  }
  console.log(modulo);
  getPreciosProducto(idProducto);
}
//
function getPreciosProducto(idProducto) {
  $("#precioProducto").html();
  params = {
    service: "getPreciosProducto",
    idProducto: idProducto,
  };
  $.post(
    "controllers/cajaController.php",
    params,
    function (data) {
      var precios = ``;
      $.each(data, function (key, val) {
        precios += `<option value="${val.precioPublico}">${val.precioPublico}</option>`;
        precios += `<option value="${val.precio1}">${val.precio1}</option>`;
        precios += `<option value="${val.precio2}">${val.precio2}</option>`;
        precios += `<option value="${val.precio3}">${val.precio3}</option>`;
        precios += `<option value="${val.precio4}">${val.precio4}</option>`;
        precios += `<option value="${val.precio5}">${val.precio5}</option>`;
      });
      $("#precioProducto").html(precios);
    },
    "json"
  );
}
//
function loadProductoByCodigo() {
  var errorMsg = "Corrige los siguiente errores:\n";
  var ingresoA = $("#ingresoA").val() === undefined ? 2 : $("#ingresoA").val();
  var idPuntoIngreso =
    $("#idPuntoIngreso").val() === undefined
      ? $("#idSucursales").val()
      : $("#idPuntoIngreso").val();
  var codigo = $("#codigo").val().toUpperCase();
  var flag = true;
  if (!idPuntoIngreso) {
    flag = false;
    errorMsg += "Seleccione bodega para consulta de productos\n";
  }
  if (!codigo) {
    flag = false;
    errorMsg += "Ingrese codigo de producto a consultar\n";
  }
  if (flag === false) {
    alert(errorMsg);
    return false;
  } else {
    $("#loader").show();
    params = {
      service: "loadProductoByCodigo",
      codigo: codigo,
      ingresoA: ingresoA,
      idPuntoIngreso: idPuntoIngreso,
    };
    $.post(
      "controllers/inventariosController.php",
      params,
      function (data) {
        if (data === null) {
          alert(
            "Codigo de producto " +
              $("#codigo").val() +
              " no existente en el sistema o sin existencia"
          );
          clear();
        } else {
          $.each(data, function (key, val) {
            $("#idProducto").val(val.idProductos);
            $("#descProducto").val(val.descLarga);
            $("#existencia").val(val.saldo);
            $("#precioProducto").val(val.precioPublico);
            $("#tipoProducto").val(val.idTipoProductos);
            if (params.codigo === "SC" || params.codigo === "sc") {
              $("#descProducto").attr("readonly", false).focus();
            } else {
              $("#descProducto").attr("readonly", true);
              $("#precioProducto").focus();
            }
            getPreciosProducto(val.idProductos);
          });
        }
      },
      "json"
    ).done(function () {
      $("#loader").hide();
      $("#descProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#precioProducto").focus();
        }
      });
      $("#descuentoProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#precioProducto").focus();
        }
      });
      $("#precioProducto").on("keydown", function (e) {
        if (e.keyCode === 13) {
          $("#cantidad").focus();
          $("#cantidadProducto").focus();
        }
      });
      $("#cantidad").on("keydown", function (e) {
        if (e.keyCode === 13) {
          var totalProducto = accounting.unformat(
            $(this).val() * $("#precioProducto").val() -
              accounting.unformat($("#descuentoProducto").val())
          );
          $("#totalProducto")
            .val(accounting.formatNumber(totalProducto, 2))
            .focus();
        }
      });
      if (editarPrecio === 0) {
        $("#precioProducto").attr("readonly", true);
        $("#cantidad").focus();
      } else {
        $("#precioProducto").attr("readonly", false);
      }
    });
  }
}
//
function loadProductoSKU(modulo) {
  $("#loader").show();
  params = {
    service: "loadProductoSKU",
    codigo: $("#codigo").val(),
  };
  $.post(
    "controllers/inventariosController.php",
    params,
    function (data) {
      if (data === null) {
        alert(
          "Codigo de producto " +
            $("#codigo").val() +
            " no existente en el sistema"
        );
        clear();
      } else {
        $.each(data, function (key, val) {
          $("#idProducto").val(val.idProductos);
          $("#descProducto").val(val.descLarga);
          $("#tipoProducto").val(val.tipoProducto);
          $("#utilizaSerie").val(val.idUtilizaSerie);
          $("#tipoProducto").val(val.idTipoProductos);
          if (params.codigo === "SC" || params.codigo === "sc") {
            $("#descProducto").attr("readonly", false).focus();
          } else {
            $("#descProducto").attr("readonly", true);
            $("#precioproducto").focus();
          }
          switch (modulo) {
            case "compras":
              $("#descuentoProducto").focus();
              $("#precioProducto").val(val.costoFOB);
              $("#descuentoProducto").on("keydown", function (e) {
                if (e.keyCode === 13) {
                  $("#precioProducto").focus();
                }
              });
              $("#precioProducto").on("keydown", function (e) {
                if (e.keyCode === 13) {
                  $("#cantidadProducto").focus();
                }
              });
              $("#cantidadProducto").on("keydown", function (e) {
                if (e.keyCode === 13) {
                  var totalProducto = accounting.unformat(
                    $(this).val() * $("#precioProducto").val() -
                      accounting.unformat($("#descuentoProducto").val())
                  );
                  $("#totalProducto")
                    .val(accounting.formatNumber(totalProducto, 2))
                    .focus();
                }
              });
              break;
            case "ajustes":
              $("#cantidad").focus();
              break;
            default:
              $("#precioProducto").focus();
              $("#precioProducto").val(val.precioPublico);
              $("#precioProducto").on("keydown", function (e) {
                if (e.keyCode === 13) {
                  $("#cantidadProducto").focus();
                }
              });
              break;
          }
        });
      }
    },
    "json"
  ).done(function () {
    $("#loader").hide();
  });
}
//
function clear() {
  $("#codigo").val("");
  $("#idProducto").val("");
  $("#tipoProducto").val("");
  $("#utilizaSerie").val("");
  $("#descProducto").val("");
  $("#descuentoProducto").val("");
  $("#cantidadProducto").val("");
  $("#precioProducto").val("");
  $("#totalProducto").val("");
  $("#cantidad").val("");
  $("#arancel").val("");
  $("#utilizaSerie").val("");
  $("#cantidadProducto").val("");
  $("#existencia").val("");
  $("#monto").val("");
  $("#idTipoCuota").val("");
  $("#idPeriodoPago").val("");
  $("#fechaInicio").val("");
  $("#fechaFin").val("").attr("disabled", false);
  $("#noCuotas").val("").attr("disabled", false);
  $("#codigo").focus();
}
//
function loadEmisores() {
  params = {
    service: "getEmisores",
  };
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#idEmisores").append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        $("#idEmisores").append(
          "<option value='" + val.id + "'>" + val.descripcion + "</option>"
        );
      });
    },
    "json"
  );
}
//
function loadCajeros(modulo) {
  params = {
    service: "getCajeros",
    modulo: modulo,
  };
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#cajeros").append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        $("#cajeros").append(
          "<option value='" + val.id + "'>" + val.userName + "</option>"
        );
      });
    },
    "json"
  );
}
//
function cancelarModal() {
  $("#modal1").modal("hide");
  $(".modal-dialog").removeClass("modal-lg");
  $("#controllers").html("");
}
//
function loadNomenclatura(idNomenclaturaItem) {
  params = {
    service: "getNomenclaruta",
    idNomenclatura: idNomenclaturaItem,
  };
  $(".nomenclatura").html("");
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $(".nomenclatura").append("<option value=''>[Seleccione...]</option>");
      $.each(data, function (key, val) {
        $(".nomenclatura").append(
          "<option value='" +
            val.id +
            "'>" +
            val.cuenta +
            " - " +
            val.descripcion +
            "</option>"
        );
      });
    },
    "json"
  );
  //loadSucursalesEmpresa();
}
//
function loadClientesNit(nit, codCliente) {
  $("#loader").show();
  var dbProject = $("#dbProject").val();
  var txtObservaciones = "";
  switch (dbProject) {
    case "pos_lolascloset":
      if ($("#observaciones").val() === "") {
        txtObservaciones = "Hora de Entrega: ";
      }
      break;
    case "pos_vitalab":
      if ($("#observaciones").val() === "") {
        txtObservaciones = "Nombre de Paciente: ";
      }
      break;
    default:
      txtObservaciones = $("#observaciones").val();
      break;
  }
  var nitC = "";
  if (nit === undefined) {
    nitC = $("#nit").val();
  } else {
    nitC = nit;
  }
  if (
    (codCliente === undefined && nitC === "CF") ||
    nitC === "C/F" ||
    nitC === "cf" ||
    nitC === ""
  ) {
    $("#nit").val("CF");
    $("#nombre").val("Consumidor Final");
    $("#direccion").val("Ciudad");
    $("#telefono").val("N/A");
    $("#observaciones").val(txtObservaciones);
    $("#nombre").focus();
    $("#loader").hide();
  } else {
    params = {
      service: "getCliente",
      nit: nitC.replace(/-/g, ""),
      codCliente: codCliente,
    };
    $.post(
      "controllers/adminController.php",
      params,
      function (data) {
        if (data == null) {
          console.log("busca en sat");
          consultaNIT();
          return false;
        } else {
          $.each(data, function (key, val) {
            $("#idClientes").val(val.id);
            $("#nit").val(val.nitC);
            $("#codigoC").val(val.codigoC);
            $("#nombre").val(val.nombreF);
            $("#telefono").val(val.telefonoC);
            $("#direccion").val(val.direccionC);
            if (dbProject === "pos_vitalab") {
              $("#observaciones").val(txtObservaciones + " " + val.nombreC);
            } else {
              $("#observaciones").val(txtObservaciones);
            }
            $("#idTipoOperacion").focus();
            //$("#motivo").attr('readonly', true);
          });
          $("#nombre").focus();
        }
      },
      "json"
    ).done(function () {
      $("#modal1").modal("hide");
      $("#loader").hide();
    });
  }
}
//
function resumenDocumentosOperados() {
  params = {
    service: "resumenDocumentosOperados",
  };
  $(".nomenclatura").html("");
  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $.each(data, function (key, val) {
        switch (val.documento) {
          case "cotizaciones":
            $("#cotizaciones").html(
              accounting.formatNumber(val.documentosOperados, 0)
            );
            break;
          case "pedidos":
            $("#pedidos").html(
              accounting.formatNumber(val.documentosOperados, 0)
            );
            break;
          case "facturacion":
            $("#facturacion").html(
              accounting.formatNumber(val.documentosOperados, 0)
            );
            break;
          case "compras":
            $("#compras").html(
              accounting.formatNumber(val.documentosOperados, 0)
            );
            break;
          case "totalCotizado":
            $("#totalCotizado").html(
              accounting.formatNumber(val.documentosOperados, 2)
            );
            break;
          case "totalPedidos":
            $("#totalPedidos").html(
              accounting.formatNumber(val.documentosOperados, 2)
            );
            break;
          case "totalFacturado":
            $("#totalFacturado").html(
              accounting.formatNumber(val.documentosOperados, 2)
            );
            break;
          case "totalCompras":
            $("#totalCompras").html(
              accounting.formatNumber(val.documentosOperados, 2)
            );
            break;
          case "facturaciones":
            $("#facturaciones").html(
              accounting.formatNumber(val.documentosOperados, 2)
            );
        }
      });
    },
    "json"
  );
}
//
function resumenDepositos(target, idCentrosCosto) {
  var fechaInicio = $("#fechaInicio").val();
  var fechaFin = $("#fechaFin").val();
  if (idCentrosCosto === "1") {
    fechaInicio = $("#fechaInicioM").val();
    fechaFin = $("#fechaFinM").val();
  }
  params = {
    service: "resumenDepositos",
    fechaInicio: fechaInicio,
    fechaFin: fechaFin,
    idCentrosCosto: idCentrosCosto,
  };

  $.post(
    "controllers/adminController.php",
    params,
    function (data) {
      $("#" + target + " tbody").html("");
      $("#" + target + " #summary").html("");
      var datos = "";
      var summary = "";
      var total1 = 0;
      var total2 = 0;
      $.each(data, function (key, val) {
        total1 += accounting.unformat(val.numeroDepositos);
        total2 += accounting.unformat(val.montoDepositado);
        datos += "<tr>";
        datos += "<td>" + val.banco + "</td>";
        datos += "<td>" + val.numeroCuenta + "</td>";
        datos +=
          "<td class='text-right'>" +
          accounting.formatNumber(val.numeroDepositos, 0) +
          "</td>";
        datos +=
          "<td class='text-right'>" +
          accounting.formatNumber(val.montoDepositado, 2) +
          "</td>";
        datos += "</tr>";
      });
      summary += "<tr>";
      summary += "<td colspan='2'>TOTALES</td>";
      summary +=
        "<td class='text-right'>" +
        accounting.formatNumber(total1, 2) +
        "</td>";
      summary +=
        "<td class='text-right'>" +
        accounting.formatNumber(total2, 2) +
        "</td>";
      summary += "</tr>";
      $("#" + target + " tbody").html(datos);
      $("#" + target + " #summary").html(summary);
    },
    "json"
  );
}
//
function consultaNIT() {
  if ($("#nit").val().replace(/-/g, "") !== "CF") {
    params = {
      service: "consultaNIT",
      nit: $("#nit").val().replace(/-/g, ""),
    };
    $.post(
      "controllers/cajaController.php",
      params,
      function (data) {
        $.each(data, function (key, val) {
          switch (val.message) {
            case "success":
              $("#nombre").val(val.nombre);
              $("#direccion").val(val.direccion);
              break;
            default:
              alert(`${val.msj},\nintente ingresar el nit correctamente`);
              break;
          }
        });
      },
      "json"
    ).done(function () {
      $("#loader").hide();
    });
  }
}

//
function ventasHoy(option) {
  params = {
    service: "ventasHoy",
    option: option,
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      $.each(data, function (key, val) {
        $("#ventas" + option).html(`${accounting.formatNumber(val.total, 2)}`);
        $("#transacciones" + option).html(
          `${accounting.formatNumber(val.noTransacciones, 0)}`
        );
        $("#ventaPromedio" + option).html(
          `${accounting.formatNumber(val.ventaPromedio, 2)}`
        );
        $("#monto" + option).html(
          `Ultima transaccion: ${accounting.formatNumber(
            val.ultimaTransaccion,
            2
          )}`
        );
        $("#hora" + option).html(
          `Ultima transaccion: ${val.horaUltimaTransaccion}`
        );
      });
    },
    "json"
  ).done(function () {
    //
    if (
      accounting.unformat($("#ventasHoy").text()) <
      accounting.unformat($("#ventasAyer").text())
    ) {
      $("#salesIndicator").html(
        '<i class="fa fa-arrow-circle-down" style="color:red !important;"></i>'
      );
    } else {
      $("#salesIndicator").html(
        '<i class="fa fa-arrow-circle-up" style="color:green !important;"></i>'
      );
    }
    //
    if (
      accounting.unformat($("#transaccionesHoy").text()) <
      accounting.unformat($("#transaccionesAyer").text())
    ) {
      $("#salesIndicator2").html(
        '<i class="fa fa-arrow-circle-down" style="color:red !important;"></i>'
      );
    } else {
      $("#salesIndicator2").html(
        '<i class="fa fa-arrow-circle-up" style="color:green !important;"></i>'
      );
    }
    //
    if ($("#ventaPromedioHoy").text() < $("#ventaPromedioAyer").text()) {
      $("#salesIndicator3").html(
        '<i class="fa fa-arrow-circle-down" style="color:red !important;"></i>'
      );
    } else {
      $("#salesIndicator3").html(
        '<i class="fa fa-arrow-circle-up" style="color:green !important;"></i>'
      );
    }
    //
    var variacion =
      accounting.unformat($("#ventasMesHoy").text()) -
      accounting.unformat($("#ventasMesAyer").text());
    $("#variacion").html(`Variacion: ${accounting.formatNumber(variacion, 2)}`);
    if (
      accounting.unformat($("#ventasMesHoy").text()) <
      accounting.unformat($("#ventasMesAyer").text())
    ) {
      $("#salesIndicator4").html(
        '<i class="fa fa-arrow-circle-down" style="color:red !important;"></i>'
      );
    } else {
      $("#salesIndicator4").html(
        '<i class="fa fa-arrow-circle-up" style="color:green !important;"></i>'
      );
    }
  });
}
//
function ultimaTransaccion() {
  params = {
    service: "ultimaTransaccion",
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      if (!data) return;
      $.each(data, function (key, val) {
        $("#montoUltimaTransaccion").html(
          `Ultima transaccion: ${accounting.formatNumber(val.total, 2)}`
        );
        $("#horaUltimaTransaccion").html(`Ultima transaccion: ${val.hora}`);
        $("#montoUltimaHora").html(
          `Ultima hora: ${accounting.formatNumber(val.montoUltimaHora, 2)}`
        );
        $("#transaccionesUltimaHora").html(
          `Ultima hora: ${val.transaccionesUltimaHora}`
        );
        $("#promUltimaHora").html(`Ultima hora: ${val.promUltimaHora}`);
      });
    },
    "json"
  ).done(function () {});
}
//
function top10hoy() {
  $("#top10hoy tbody").html("");
  params = {
    service: "top10hoy",
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      var component = ``;
      if (!data) return;
      $.each(data, function (key, val) {
        component += `<tr>
                        <td>${key + 1}</td>
                        <td>${val.sku}</td>
                        <td>${val.descLarga}</td>
                        <td>${accounting.formatNumber(val.cantidad, 3)}</td>
                        <td>${accounting.formatNumber(val.total, 2)}</td>
                        </tr>`;
      });
      $("#top10hoy tbody").html(component);
      initDT('#top10hoy', { paging: false, order: [] });
    },
    "json"
  ).done(function () {});
}
//
function obtenerNombreMes(numeroMes) {
  const meses = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];

  return meses[numeroMes - 1];
}

function totalDtes() {
  $("#totalDtes tbody").html("");
  params = {
    service: "totalDtes",
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      var component = ``;
      var sumaTotal = 0;
      var sumaDiasFacturados = 0;
      var sumaFacturacionPorDia = 0;

      $.each(data, function (key, val) {
        sumaTotal += parseInt(val.total);
        sumaDiasFacturados += parseInt(val.diasFacturados);
        sumaFacturacionPorDia += parseInt(val.facturacionPorDia);

        component += `<tr>
                        <td>${key + 1}</td>
                        <td>${val.sucursal}</td>
                        <td>${val.periodo}</td>
                        <td>${obtenerNombreMes(val.mes)}</td>
                        <td>${val.total}</td>
                        <td>${val.diasFacturados}</td>
                        <td>${val.facturacionPorDia}</td>
                      </tr>`;
      });

      $("#totalDtes tbody").html(component);
      $("#totalDtesFootTotal").text(sumaTotal);
      $("#totalDtesFootDias").text(sumaDiasFacturados);
      $("#totalDtesFootFact").text(sumaFacturacionPorDia);
      $("#totalDtesFoot").show();
      initDT('#totalDtes', { pageLength: 10, order: [[2, 'desc'], [3, 'desc']] });
    },
    "json"
  ).done(function () {});
}

function getDteSaldo() {
  $.post(
    "controllers/adminController.php",
    { service: "getDteSaldo" },
    function (data) {
      if (!data) return;

      var colores = { ok: "#5cb85c", warning: "#f0ad4e", danger: "#d9534f" };
      var color = colores[data.alerta] || "#999";

      $("#dteEmitidos").text(accounting.formatNumber(data.dteEmitidos, 0));
      $("#dteComprados").text(accounting.formatNumber(data.dteComprados, 0));

      var meses = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
      var partes = data.fechaInicio.split("-");
      var nombreMes = meses[parseInt(partes[1]) - 1];
      $("#dteLabelPeriodo").text("DTE EMITIDOS · " + nombreMes + " " + partes[0]);

      var restantesTexto;
      if (data.dteRestantes < 0) {
        restantesTexto = "⚠ Excedido en " + Math.abs(data.dteRestantes) + " DTE";
      } else {
        restantesTexto = data.dteRestantes + " disponibles (" + data.porcentaje + "% usado)";
      }
      $("#dteRestantesLabel").text(restantesTexto).css("color", color);

      $(".mini-stat-icon.pink").css("background-color", color);
    },
    "json"
  );
}

function getDtesPorMes() {
  var anio = $("#dteAnioFiltro").val() || new Date().getFullYear();
  var meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',
               'Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  $("#tblDtesPorMesBody").html(
    '<tr><td colspan="4" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>'
  );
  $("#dteTituloGrafica").text(anio);
  $.post(
    "controllers/adminController.php",
    { service: "getDtesPorMes", anio: anio },
    function (data) {
      if (!data || data.length === 0) {
        $("#tblDtesPorMesBody").html('<tr><td colspan="4" class="text-center">Sin datos para ' + anio + '</td></tr>');
        initDT('#tblDtesPorMes', { paging: false, ordering: false });
        drawDteChart([], anio);
        return;
      }
      var html = "";
      var chartRows = [];
      var totalComprados = 0;
      var totalEmitidos  = 0;
      $.each(data, function (i, row) {
        var comprados = parseInt(row.dteComprados);
        var emitidos  = parseInt(row.dteEmitidos);
        totalComprados += comprados;
        totalEmitidos  += emitidos;
        var saldoHtml;
        if (comprados > 0) {
          var saldo = comprados - emitidos;
          var color = saldo < 0 ? '#d9534f' : (saldo < comprados * 0.2 ? '#f0ad4e' : '#5cb85c');
          saldoHtml = '<strong style="color:' + color + '">' + accounting.formatNumber(saldo, 0) + '</strong>';
        } else {
          saldoHtml = '<span class="text-muted">-</span>';
        }
        html += '<tr>' +
          '<td>' + meses[parseInt(row.mes)] + '</td>' +
          '<td class="text-right">' + (comprados > 0 ? accounting.formatNumber(comprados, 0) : '<span class="text-muted">-</span>') + '</td>' +
          '<td class="text-right">' + accounting.formatNumber(emitidos, 0) + '</td>' +
          '<td class="text-right">' + saldoHtml + '</td>' +
          '</tr>';
        chartRows.push([meses[parseInt(row.mes)], emitidos, comprados]);
      });
      $("#tblDtesPorMesBody").html(html);
      initDT('#tblDtesPorMes', { paging: false, ordering: false });
      var totalSaldo = totalComprados - totalEmitidos;
      var colorTotal = totalSaldo < 0 ? '#d9534f' : (totalSaldo < totalComprados * 0.2 ? '#f0ad4e' : '#5cb85c');
      $("#dteTotalComprados").text(accounting.formatNumber(totalComprados, 0));
      $("#dteTotalEmitidos").text(accounting.formatNumber(totalEmitidos, 0));
      if (totalComprados > 0) {
        $("#dteTotalSaldo").html('<strong style="color:' + colorTotal + '">' + accounting.formatNumber(totalSaldo, 0) + '</strong>');
      } else {
        $("#dteTotalSaldo").html('<span class="text-muted">-</span>');
      }
      drawDteChart(chartRows, anio);
    },
    "json"
  );
}

function drawDteChart(rows, anio) {
  if (!document.getElementById("chart_dte")) return;
  if (typeof google === "undefined" || typeof google.visualization === "undefined") return;
  var data = new google.visualization.DataTable();
  data.addColumn("string", "Mes");
  data.addColumn("number", "Emitidos");
  data.addColumn("number", "Comprados");
  data.addRows(rows);
  var options = {
    title: "",
    width: "100%",
    height: 300,
    colors: ["#337ab7", "#5cb85c"],
    legend: { position: "bottom" },
    vAxis: { minValue: 0, format: "#,##0" },
    hAxis: { textStyle: { fontSize: 11 } },
    chartArea: { width: "88%", height: "72%", left: 60 },
    bar: { groupWidth: "70%" }
  };
  var chart = new google.visualization.ColumnChart(document.getElementById("chart_dte"));
  chart.draw(data, options);
}

//
function top10mes() {
  $("#top10mes tbody").html("");
  params = {
    service: "top10mes",
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      var component = ``;
      $.each(data, function (key, val) {
        component += `<tr>
                        <td>${key + 1}</td>
                        <td>${val.sku}</td>
                        <td>${val.descLarga}</td>
                        <td>${accounting.formatNumber(val.cantidad, 3)}</td>
                        <td>${accounting.formatNumber(val.total, 2)}</td>
                        </tr>`;
      });
      $("#top10mes tbody").html(component);
      initDT('#top10mes', { paging: false, order: [] });
    },
    "json"
  ).done(function () {});
}
//
function ultimas10Transacciones() {
  $("#ultimas10Transacciones tbody").html("");
  params = {
    service: "ultimas10Transacciones",
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      var component = ``;
      if (!data) return;
      $.each(data, function (key, val) {
        component += `<tr>
                        <td>${key + 1}</td>
                        <td>${val.hora}</td>
                        <td>${accounting.formatNumber(val.total, 2)}</td>
                        </tr>`;
      });
      $("#ultimas10Transacciones tbody").html(component);
      initDT('#ultimas10Transacciones', { paging: false, order: [] });
    },
    "json"
  ).done(function () {});
}
//
function ultimos7dias() {
  $("#ultimos7dias tbody").html("");
  params = {
    service: "ultimos7dias",
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      var component = ``;
      $.each(data, function (key, val) {
        component += `<tr>
                        <td>${key + 1}</td>
                        <td>${val.fecha}</td>
                        <td>${accounting.formatNumber(val.total, 2)}</td>
                        </tr>`;
      });
      $("#ultimos7dias tbody").html(component);
      initDT('#ultimos7dias', { paging: false, ordering: false });
    },
    "json"
  ).done(function () {});
}
//
function currentYear() {
  $("#currentYear tbody").html("");
  params = {
    service: "currentYear",
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      var component = ``;
      $.each(data, function (key, val) {
        component += `<tr>
                        <td>${key + 1}</td>
                        <td>${val.mes}</td>
                        <td>${accounting.formatNumber(val.total, 2)}</td>
                        </tr>`;
      });
      $("#currentYear tbody").html(component);
      initDT('#currentYear', { paging: false, ordering: false });
    },
    "json"
  ).done(function () {});
}
//
function comprasHoy(option) {
  params = {
    service: "comprasHoy",
    option: option,
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      $.each(data, function (key, val) {
        $("#compras" + option).html(`${accounting.formatNumber(val.total, 2)}`);
      });
    },
    "json"
  ).done(function () {
    var variacion =
      accounting.unformat($("#comprasMesHoy").text()) -
      accounting.unformat($("#comprasMesAyer").text());
    $("#variacionCompras").html(
      `Variacion: ${accounting.formatNumber(variacion, 2)}`
    );
    if (
      accounting.unformat($("#comprasMesHoy").text()) <
      accounting.unformat($("#comprasMesAyer").text())
    ) {
      $("#salesIndicator5").html(
        '<i class="fa fa-arrow-circle-down" style="color:red !important;"></i>'
      );
    } else {
      $("#salesIndicator5").html(
        '<i class="fa fa-arrow-circle-up" style="color:green !important;"></i>'
      );
    }
  });
}
//
function utilidad(option) {
  params = {
    service: "utilidad",
    option: option,
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      $.each(data, function (key, val) {
        $("#utilidad" + option).html(
          `${accounting.formatNumber(val.utilidad, 2)}`
        );
        $("#margen" + option).html(`${val.margen}%`);
      });
    },
    "json"
  ).done(function () {
    var variacionU =
      accounting.unformat($("#utilidadMesHoy").text()) -
      accounting.unformat($("#utilidadMesAyer").text());
    var variacionM =
      accounting.unformat($("#margenMesHoy").text()) -
      accounting.unformat($("#margenMesAyer").text());
    $("#variacionUtilidad").html(
      `Variacion: ${accounting.formatNumber(variacionU, 2)}`
    );
    $("#variacionMargen").html(`Variacion: ${variacionM}%`);
    //UILIDAD
    if (
      accounting.unformat($("#utilidadMesHoy").text()) <
      accounting.unformat($("#utilidadMesAyer").text())
    ) {
      $("#salesIndicator6").html(
        '<i class="fa fa-arrow-circle-down" style="color:red !important;"></i>'
      );
    } else {
      $("#salesIndicator6").html(
        '<i class="fa fa-arrow-circle-up" style="color:green !important;"></i>'
      );
    }
    //MARGEN
    if (
      accounting.unformat($("#margenMesHoy").text()) <
      accounting.unformat($("#margenMesAyer").text())
    ) {
      $("#salesIndicator7").html(
        '<i class="fa fa-arrow-circle-down" style="color:red !important;"></i>'
      );
    } else {
      $("#salesIndicator7").html(
        '<i class="fa fa-arrow-circle-up" style="color:green !important;"></i>'
      );
    }
  });
}
//
function resumenInventario() {
  params = {
    service: "resumenInventario",
  };
  $.post(
    "controllers/reportesController.php",
    params,
    function (data) {
      $.each(data, function (key, val) {
        $("#inventarioCosto").html(`${accounting.formatNumber(val.costo, 2)}`);
        $("#inventarioVenta").html(`${accounting.formatNumber(val.venta, 2)}`);
      });
    },
    "json"
  ).done(function () {
    if (
      accounting.unformat($("#inventarioVenta").text()) <
      accounting.unformat($("#inventarioCosto").text())
    ) {
      $("#salesIndicator8").html(
        '<i class="fa fa-arrow-circle-down" style="color:red !important;"></i>'
      );
    } else {
      $("#salesIndicator8").html(
        '<i class="fa fa-arrow-circle-up" style="color:green !important;"></i>'
      );
    }
  });
}
