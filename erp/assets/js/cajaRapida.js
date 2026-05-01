//GLOBALS
const host = "controllers/cajaRapidaController.php";
const hostImages = "/assets/images/productos/";
let page = location.href.split("/").slice(-1);
page = page[0].split('.');
let pagina = page[0];
var pathJasper = "views/jasper/";
//
$(document).ready(function () {
    if (pagina === 'cajaRapida') {
        getCategorias();
        loadProductosVenta();
        getDocumentoFacturacion();
//        $("#usuario").html(localStorage.nombreUsuario);
    }
});
//
function getCategorias() {
    params = {
        service: 'getCategorias',
    };
    $.post(host, params, function (data) {
        $("#categorias").html('');
        var component = "";
        $.each(data, function (key, val) {
            component += `<div class='col s6 box2' onclick='getProductosCategorias(${val.id});'><span class='middle-align'>${val.descripcion}</span></div>`;
        });
        $("#categorias").html(component);
    }, 'json');
}
//
function getProductosCategorias(idCategorias) {
    params = {
        service: 'getProductosCategorias',
        idCategorias: idCategorias
    };
    $.post(host, params, function (data) {
        $("#productos").html('');
        var component = ``;
        $.each(data, function (key, val) {
            console.log(val.image);
            var image = hostImages + val.image;
            if (!val.image) {
                //val.image='isotipo-cubix.jpeg';
                image = '/assets/images/isotipo-cubix.jpeg';
            }
            component += `<div class="col s4" onclick='agregarProductoVenta(${val.id},${accounting.unformat(val.existencia)});';>
                            <div class="card">
                                <div class="card-image">
                                    <button class="btn btn-danger btn-existencias">${accounting.unformat(val.existencia)}</button>
                                    <img src="${image}" class='responsive-img imgProducto center'>
                                </div>
                                <div class="card-content">
                                    <p class='truncate'>
                                        ${val.descLarga}<br/>
                                        ${val.sku}
                                    </p>
                                </div>
                                <div class="card-action">
                                    Q. ${val.precioPublico}
                                </div>
                            </div>
                        </div>`;
        });
        $("#productos").html(component);
    }, 'json');
}
//
function agregarProductoVenta(idProductos, existencia) {
    if (accounting.unformat(existencia) > 0) {
        params = {
            service: 'agregarProductoVenta',
            idProductos: idProductos,
            cantidad: 1
        };
        $.post(host, params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadProductosVenta();
                } else {
                    alert(`Error ${val.error}`);
                }
            });
        }, 'json');
    }
}
//
function loadProductosVenta() {
    $("#detalleCarrito,#resumenCarrito").html('');
    params = {
        service: 'loadProductosVenta',
    };
    $.post(host, params, function (data) {
        var detail = "";
        var summary = "";
        var total1 = 0;
        var total2 = 0;
        if (data === null) {
            detail += '<li>';
            detail += '<div class="row">';
            detail += '<div class="col s12 center-align">';
            detail += '0 registros encontrados en el carrito';
            detail += '</div>';
            detail += '</div>';
            detail += '</li>';
        } else {
            detail = `<li class="collection-header"><h5>Detalle de Venta</h5></li>`;
            $.each(data, function (key, val) {
                var Upc = "'" + val.Upc + "'";
                var Precio = "'" + accounting.unformat(val.precio) + "'";
                total1 += accounting.unformat(val.total);
                total2 += accounting.unformat(val.cantidad);
                detail += `<li class="collection-item" onclick="eliminarProductoVenta(${val.id},'${val.descLarga}');">`;
                detail += '<div class="row">';
                detail += '<div class="col s2 left-align">';
                detail += '<span>' + accounting.formatNumber(val.cantidad, 0) + 'x</span>';
                //detail += '<i class="mdi mdi-minus-circle red-text" onclick="quitarItemCarrito(' + Upc + ',' + accounting.formatNumber(val.cantidad, 0) + ',' + val.id + ');"></i>';
                detail += '</div>';
                detail += '<div class="col s6 left-align truncate">' + val.descLarga + '</div>';
                detail += '<div class="col s4 right-align">';
                detail += 'Q. ' + accounting.formatNumber(val.total, 2);
                //detail += '<i class="mdi mdi-plus-circle red-text" onclick="agregarItemCarrito(' + Upc + ', ' + Precio + ');"></i>';
                detail += '</div>';
                detail += '</div>';
                detail += '</li>';
            });
            summary += '<li class="collection-item">';
            summary += 'No. Productos:';
            summary += `<span class="totales"><input type="hidden" id="totalItems" value="${total2}"/>${accounting.formatNumber(total2, 0)}</span>`;
            summary += '</li>';
            summary += '<li class="collection-item">';
            summary += 'Total:';
            summary += `<span class="totales" style='font-size:2.5em !important;'><input type="hidden" id="total" value="${total1}"/>Q. ${accounting.formatNumber(total1, 2)}</span>`;
            summary += '</li>';

        }
        $("#detalleCarrito").append(detail);
        $("#resumenCarrito").append(summary);
    }, 'json').done(function () {
        $("#loader").hide();
    });
}
//
function cancelarVenta() {
    var r = confirm("¿Esta seguro de cancelar esta venta?");
    if (r == true) {
        params = {
            service: 'cancelarVenta',
        };
        $.post(host, params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadProductosVenta();
                } else {
                    alert(`Error ${val.Query}`);
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function eliminarProductoVenta(id, descLarga) {
    var r = confirm(`¿Esta seguro de eliminar el item ${descLarga} de esta venta?`);
    if (r == true) {
        params = {
            service: 'eliminarProductoVenta',
            id: id
        };
        $.post(host, params, function (data) {
            $.each(data, function (key, val) {
                if (val.message === 'success') {
                    loadProductosVenta();
                } else {
                    alert(`Error: ${val.Query}`);
                }
            });
        }, 'json');
    } else {
        return false;
    }
}
//
function getDatosVenta() {
    if ($("#totalItems").val() === undefined) {
        alert('No tiene articulos en esta venta');
    } else {
        $(".modal-content .row").html('');
        $("#modal-title").html('DATOS DE VENTA');
        $('#modal1').openModal();
        //
        let component = `<div class='col s4'>
                        <label>TOTAL VENTA</label><br/>
                        <input type='number' id="totalFactura" class='inputs'/>
                        </div>
                        <div class='col s4'>
                        <label>MONTO A RECIBIR</label><br/>
                        <input type='number' id="montoRecibir" class='inputs'/>
                        </div>
                        <div class='col s4'>
                        <label>TOTAL CAMBIO</label><br/>
                        <input type='number' id="totalCambio" class='inputs'/>
                        </div>
                        <div class='col s4'>
                        <label>NIT</label><br/>
                        <input type='text' id="nit" value="CF"/>
                        </div>
                        <div class='col s4'>
                        <label>NOMBRE</label><br/>
                        <input type='text' id="nombre" value="CONSUMIDOR FINAL"/>
                        </div>
                        <div class='col s4'>
                        <label>DIRECCION</label><br/>
                        <input type='text' id="direccion" value="CIUDAD"/>
                        </div>
                        `;
        //
        $(".modal-content .row").html(component);
        $("#totalFactura").val(accounting.formatNumber($("#total").val(), 2)).attr('disabled', true);
        $("#montoRecibir").focus();
        $("#totalCambio").attr('disabled', true);
        $("#montoRecibir").keyup(function () {
            var totalCambio = 0;
            totalCambio = accounting.unformat($("#montoRecibir").val()) - accounting.unformat($("#totalFactura").val());
            $("#totalCambio").val(accounting.formatNumber(totalCambio, 2));
        });
        $("#montoRecibir").on('keypress', function (e) {
            if (e.keyCode == 13) {
                finalizarVenta();
            }
        });
        $
    }
}
//
function cerrarModal() {
    $("#modal1").closeModal();
}
//
function finalizarVenta() {
    var flag = true;
    var errorMsg = "Corrige los siguiente errores:\n";
    if (accounting.unformat($("#montoRecibir").val()) === 0) {
        flag = false;
        errorMsg += ('Debe ingresar el monto a recibir');
    }
    if (accounting.unformat($("#totalCambio").val()) < 0) {
        flag = false;
        errorMsg += ('El monto a recibir es menor al total de la venta');
    }
    if (flag === false) {
        alert(errorMsg);
        return false;
    } else {
        var total = accounting.unformat($("#totalFactura").val());
        var iva = accounting.unformat((total / 1.12 * 0.12 * 100) / 100);
        var subTotal = accounting.unformat((total - iva));
        params = {
            service: 'finalizarVenta',
            total: total,
            nombre: $("#nombre").val().toUpperCase(),
            nit: $("#nit").val(),
            direccion: $("#direccion").val(),
            subtotal: subTotal,
            descuentoM: 0,
            descuentoP: 0,
            anticipo: 0,
            saldo: total,
            iva: iva,
            tasaCambio: 0,
            totalDolares: 0,
            tipoVenta: 1,
            idDocumento: $("#idDocumento").val(),
            serie: $("#serie").val(),
            correlativo: $("#correlativo").val()
        };
        $.post(host, params, function (data) {
            $.each(data, function (key, val) {
                switch (val.message) {
                    case 'success':
                        var url = pathJasper + "formatoFactura.php?idVenta=" + val.idVenta + "&modulo=cajaRapida";
                        window.open(url);
                        location.reload();
                        break;
                    case 'docExists':
                        alert('Correlativo de documento ya existe en el sistema');
                        break;
                    default:
                        alert('Error cerrar venta');
                        break;
                }
            });
        }, 'json');
    }
}
//
function getDocumentoFacturacion() {
    params = {
        service: 'getDocumentoFacturacion',
    };
    $.post(host, params, function (data) {
        if (data !== null) {
            $.each(data, function (key, val) {
                $("#idDocumento").val(val.id);
                $("#serie").val(val.serie);
                $("#correlativo").val(val.correlativo);
            });
        }
    }, 'json');
}
//
function openCorteCaja() {
    $(".modal-content .row").html('');
    $("#modal-title").html('CORTE DE CAJA');
    $('#modal1').openModal();
    let component = `
                <div class='col s6'>
                    Efectivo en Caja
                </div>
                <div class='col s6'>
                    Resumen
                </div>
                `;
    $(".modal-content .row").html(component);
}