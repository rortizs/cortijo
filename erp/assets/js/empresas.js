$(document).ready(function () {
    $('#tableEmpresas').DataTable({
        "scrollY": "220px",
        "scrollX": true,
        "scrollCollapse": true,
        "pagingType": "simple",
        "language": {
            "processing": "Cargando informacion por favor espere un momento",
            "search": "Buscar",
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
//
function ingresarEmpresa(idEmpresa, nombreEmpresa) {
    $.redirect("main.php", {idEmpresa: idEmpresa, nombreEmpresa: nombreEmpresa});
}