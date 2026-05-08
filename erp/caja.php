<?php
/** MODULO DE CAJA
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
$release_version = date('Y.m.d.H.i.s');

require_once("models/config.php");
if (!empty($_SESSION['userName']) && !empty($_SESSION['idRoles'])) {
    $_SESSION['nombreEmpresa'] = $_SESSION['companyName'];
    $_SESSION['idEmpresa'] = $_SESSION['idEmpresas'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="DIGICOM ERP POS CRM Guatemala">
            <meta name="author" content="Richard Ortiz richard.ortiz@digicom.com.gt">
            <link rel="icon" type="image/png" sizes="50x50" href="assets/images/logo_cubix.png">
            <title><?= Config::$systemName; ?></title>
            <link href="assets/bs3/css/bootstrap.min.css" rel="stylesheet">
            <link href="assets/css/bootstrap-reset.css" rel="stylesheet">
            <link href="assets/css/style.css" rel="stylesheet">
            <link href="assets/css/style-responsive.css" rel="stylesheet" />
            <link href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" >
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
            <link href="assets/css/main.css?v=<?= $release_version; ?>" rel="stylesheet"/>
            <link href="assets/css/caja.css?v=<?= $release_version; ?>" rel="stylesheet"/>
        </head>
        <body class="full-width">
            <section id="container" class="hr-menu">
                <input type="hidden" id="dbProject" value="<?= $_SESSION['dbProject']; ?>"/>
                <input type="hidden" id="idEmpresas" value="<?= $_SESSION['idEmpresa']; ?>"/>
                <input type="hidden" id="idSucursales" value="<?= $_SESSION['idSucursalesS']; ?>"/>
                <input type="hidden" id="idRoles" value="<?= $_SESSION['idRoles']; ?>"/>
                <input type="hidden" id="idUsuarios" value="<?= $_SESSION['idUsuarios']; ?>"/>
                <!--header start-->
                <header class="header fixed-top">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle hr-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="fa fa-bars"></span>
                        </button>
                        <!--logo start-->
                        <div class="brand ">
                            <a class="logo" onclick="loadModulo();">
                                <img src="assets/images/logo_digicom.png" alt="" style="width: 50% !important;">
                            </a>
                        </div>
                        <div class="horizontal-menu navbar-collapse collapse">'
                            <ul class="nav navbar-nav">
                                <li class="dropdown">
                                    <a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" href="#">Menu <b class=" fa fa-angle-down"></b></a>
                                    <ul class="dropdown-menu">

                                        <?php
                                        switch ($_SESSION['idRoles']) {
                                            case 12:
                                                ?>
                                                <li onclick="loadModuloCotizaciones();"><a><i class="fa fa-shopping-cart"></i> Cotizaciones</a></li>
                                                <li onclick="loadModuloPedidos();"><a><i class="fa fa-shopping-cart"></i> Pedidos</a></li>
                                                <?php if ($_SESSION['dbProject'] == 'pos_togasjulissa') { ?>
                                                    <li onclick="loadTomaMedidas(0,'get');"><a><i class="fa fa-user"></i>Toma Medidas</a></li>
                                                    <li onclick="loadConsultaTomaMedidas();"><a><i class="fa fa-list"></i> Consulta de Toma de Medidas</a></li>

                                                <?php } ?>
                                                <li onclick="loadConsultaCotizaciones();"><a><i class="fa fa-list"></i> Consulta de Cotizaciones</a></li>
                                                <li onclick="loadConsultaPedidos();"><a><i class="fa fa-list"></i> Consulta de Pedidos</a></li>
                                                <li onclick="loadConsultaExistencias();"><a><i class="fa fa-list"></i> Consulta de Existencias</a></li>
                                                <?php if ($_SESSION['dbProject'] == 'erp_laxTravelTopacio') { ?>
                                                    <li onclick="loadConsultaBoletos();"><a><i class="fa fa-shopping-plane"></i> Consulta de Boletos</a></li>
                                                <?php } ?>
                                                <?php
                                                break;
                                            case 18:
                                                ?>
                                                <li onclick="loadModuloCotizaciones();"><a><i class="fa fa-shopping-cart"></i> Cotizaciones</a></li>
                                                <li onclick="loadModuloPedidos();"><a><i class="fa fa-shopping-cart"></i> Pedidos</a></li>
                                                <li onclick="loadModuloFacturacion();"><a><i class="fa fa-shopping-cart"></i> Caja</a></li>
                                                <li onclick="loadModuloRecibos();"><a><i class="fa fa-shopping-cart"></i> Recibos</a></li>
                                                <li onclick="openVale();"><a><i class="fa fa-sticky-note"></i> Vales</a></li>
                                                <?php if ($_SESSION['dbProject'] == 'pos_togasjulissa') { ?>
                                                    <li onclick="loadTomaMedidas(0,'get');"><a><i class="fa fa-user"></i>Toma Medidas</a></li>
                                                    <li onclick="loadConsultaTomaMedidas();"><a><i class="fa fa-list"></i> Consulta de Toma de Medidas</a></li>

                                                <?php } ?>
                                                <li onclick="loadConsultaCotizaciones();"><a><i class="fa fa-list"></i> Consulta de Cotizaciones</a></li>
                                                <li onclick="loadConsultaPedidos();"><a><i class="fa fa-list"></i> Consulta de Pedidos</a></li>
                                                <li onclick="loadConsultaFacturas();"><a><i class="fa fa-list"></i> Consulta de Facturas</a></li>
                                                <?php if ($_SESSION['dbProject'] == 'erp_laxTravelTopacio') { ?>
                                                    <li onclick="loadConsultaBoletos();"><a><i class="fa fa-shopping-plane"></i> Consulta de Boletos</a></li>
                                                <?php } ?>
                                                <li onclick="loadConsultaVales();"><a><i class="fa fa-list"></i> Consulta de Vales</a></li>
                                                <li onclick="loadConsultaExistencias();"><a><i class="fa fa-list"></i> Consulta de Existencias</a></li>
                                                <li onclick="openCorteCaja();"><a><i class="fa fa-money"></i> Corte de Caja</a></li>
                                                <?php
                                                break;
                                            default:
                                                ?>
                                                <li onclick="loadModuloCotizaciones();"><a><i class="fa fa-shopping-cart"></i> Cotizaciones</a></li>
                                                <li onclick="loadModuloPedidos();"><a><i class="fa fa-shopping-cart"></i> Pedidos</a></li>
                                                <li onclick="loadModuloFacturacion();"><a><i class="fa fa-shopping-cart"></i> Caja</a></li>
                                                <li onclick="loadModuloRecibos();"><a><i class="fa fa-shopping-cart"></i> Recibos</a></li>
                                                <li onclick="openVale();"><a><i class="fa fa-sticky-note"></i> Vales</a></li>
                                                <?php if ($_SESSION['dbProject'] == 'pos_togasjulissa') { ?>
                                                    <li onclick="loadTomaMedidas(0,'get');"><a><i class="fa fa-user"></i>Toma Medidas</a></li>
                                                    <li onclick="loadConsultaTomaMedidas();"><a><i class="fa fa-list"></i> Consulta de Toma de Medidas</a></li>

                                                <?php } ?>
                                                <li onclick="loadConsultaCotizaciones();"><a><i class="fa fa-list"></i> Consulta de Cotizaciones</a></li>
                                                <li onclick="loadConsultaPedidos();"><a><i class="fa fa-list"></i> Consulta de Pedidos</a></li>
                                                <li onclick="loadConsultaFacturas();"><a><i class="fa fa-list"></i> Consulta de Facturas</a></li>
                                                <?php if ($_SESSION['dbProject'] == 'erp_laxTravelTopacio') { ?>
                                                    <li onclick="loadConsultaBoletos();"><a><i class="fa fa-ticket"></i> Consulta de Boletos</a></li>
                                                <?php } ?>
                                                <li onclick="loadConsultaVales();"><a><i class="fa fa-list"></i> Consulta de Vales</a></li>
                                                <li onclick="loadConsultaExistencias();"><a><i class="fa fa-list"></i> Consulta de Existencias</a></li>
                                                <li onclick="openCorteCaja();"><a><i class="fa fa-money"></i> Corte de Caja</a></li>
                                                <?php
                                                break;
                                        }
                                        ?>
                                        <li><a href="logout.php?action=logout"><i class="fa fa-power-off"></i> Cerrar Sesión</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="nav notify-row" id="top_menu">
                            <ul class="breadcrumb">
                                <li>Caja</li>
                                <li id="opcion"></li>
                            </ul>
                        </div>
                        <!--logo end-->
                        <div class="top-nav clearfix hidden-xs hidden-sm">
                            <ul class="breadcrumb pull-right">
                                <li>EMPRESA: <?= $_SESSION['nombreEmpresa']; ?></li>
                                <li>SUCURSAL: <?= $_SESSION['nombreSucursal']; ?></li>
                                <li>USUARIO: <?= $_SESSION['userName']; ?></li>
                            </ul>
                        </div>
                    </div>
                </header>
                <!--header end-->
                <!--sidebar start-->
                <!--sidebar end-->
                <!--main content start-->
                <section id="main-content">
                    <input type="hidden" id="idRoles" value="<?= $_SESSION['idRoles']; ?>"/>
                    <div id="loader" style="display: none;">
                        <div class="child-loader">
                            <img src="./assets/images/ajax-loader_1.gif">
                        </div>
                    </div>
                    <section class="wrapper" id="page-container" style="margin-top: 75px !important;">
                        <!-- page start-->
                        <!-- page end-->
                    </section>
                </section>
                <!--main content end-->
            </section>
            <!--MODALES -->
            <div class="modal fade" id="modal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog vertical-align-center">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel"></h4>
                        </div>
                        <div class="modal-body" id="controllers">
                        </div>
                    </div>
                </div>
            </div>
            <!--/MODALES-->
            <!--Core js-->
            <script src="assets/js/jquery.js"></script>
            <script src="assets/bs3/js/bootstrap.min.js"></script>
            <script src="https://use.fontawesome.com/e63724d01b.js"></script>
            <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js" type="text/javascript"></script>
            <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/js/bootstrap-datetimepicker.min.js"></script>
            <script src="assets/js/json2.js"></script>
            <script src="assets/js/jstorage.js"></script>
            <script src="assets/js/accounting.min.js"></script>
            <script src="assets/js/functionsGlobal.js?v=<?= $release_version; ?>"></script>
            <script src="assets/js/dynamic.js?v=<?= $release_version; ?>"></script>
            <script src="assets/js/caja.js?v=<?= $release_version; ?>"></script>
            <script src="assets/js/jquery.redirect.js" type="text/javascript"></script>
        </body>
    </html>
    <?php
} else {
    header("location:login.php");
}
?>