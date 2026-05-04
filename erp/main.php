<?php

/**
 * ERP
 * main page.
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
session_start();
$release_version = date('Y.m.d.H.i.s');
if ($_SESSION['userName'] != '') {
    $_SESSION['nombreEmpresa'] = $_POST['nombreEmpresa'] ?: $_SESSION['nombreEmpresa'];
    $_SESSION['idEmpresa'] = $_POST['idEmpresa'] ?: $_SESSION['idEmpresa'];
    require_once "models/config.php";
    require_once "models/login.php";
    $login = new Login();
    $modulos = $login->loadModulos($_SESSION['idRoles']);
    $paginas = $login->loadPaginas($_SESSION['idRoles']);

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="DIGICOM ERP POS CRM Ecommerce Guatemala">
        <meta name="author" content="">
        <link rel="icon" type="image/png" sizes="50x50" href="assets/images/logo_cubix.png">
        <title><?= Config::$systemName; ?></title>
        <!--Core CSS -->
        <link href="assets/bs3/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/style.css?v=<?= $release_version; ?>" rel="stylesheet">
        <link href="assets/css/style-responsive.css?v=<?= $release_version; ?>" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
        <!--/Core CSS -->
        <link rel='stylesheet' href="https://cdn.materialdesignicons.com/3.8.95/css/materialdesignicons.min.css" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
        <link href="assets/css/main.css?v=<?= $release_version; ?>" rel="stylesheet" />
    </head>

    <body>
        <input type="hidden" id="dbProject" value="<?= $_SESSION['dbProject']; ?>" />
        <input type="hidden" id="idEmpresas" value="<?= $_SESSION['idEmpresa']; ?>" />
        <input type="hidden" id="idRolesUsuario" value="<?= $_SESSION['idRoles']; ?>" />
        <div id="loader" style="display: none;">
            <div class="child-loader">
                <img src="/assets/images/ajax-loader_1.gif">
            </div>
        </div>
        <section id="container">
            <!--header start-->
            <header class="header fixed-top clearfix">
                <!--logo start-->
                <div class="brand">
                    <a href="./" class="logo">
                        <img src="assets/images/logo_digicom.png" alt="" style="width: 50% !important;">
                    </a>
                    <div class="sidebar-toggle-box">
                        <div class="fa fa-bars"></div>
                    </div>
                </div>
                <div class="nav notify-row" id="top_menu">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item" id="modulo">Inicio</li>
                            <li class="breadcrumb-item" id="opcion">Dashboard</li>
                        </ol>
                    </nav>
                </div>
                <div class="top-nav clearfix">
                    <!--search & user info start-->
                    <ul class="nav pull-right top-menu">
                        <!-- user login dropdown start-->
                        <li class="dropdown">
                            <a data-toggle="dropdown" class="dropdown-toggle icon-user" href="#">
                                <!--<img alt="" src="images/avatar1_small.jpg">-->
                                <i class="mdi mdi-office-building"></i>
                                <span class="username hidden-xs">Empresa: <?= $_SESSION['nombreEmpresa']; ?></span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu extended logout">
                                <li><a href="logout.php?action=cambiarEmpresa"><i class="mdi mdi-shuffle-variant"></i>Cambiar de Empresa</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a data-toggle="dropdown" class="dropdown-toggle icon-user" href="#">
                                <!--<img alt="" src="images/avatar1_small.jpg">-->
                                <i class="mdi mdi-account"></i>
                                <span class="username hidden-xs"><?= $_SESSION['userName']; ?></span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu extended logout">
                                <li><a href="logout.php?action=logout"><i class="mdi mdi-power"></i> Cerrar sesión</a></li>
                            </ul>
                        </li>
                        <!-- user login dropdown end -->
                    </ul>
                    <!--search & user info end-->
                </div>
            </header>
            <!--header end-->
            <aside>
                <div id="sidebar" class="nav-collapse">
                    <!-- sidebar menu start-->
                    <div class="leftside-navigation">
                        <ul class="sidebar-menu" id="nav-accordion">
                            <?php
                            foreach ($modulos as $key => $value) {
                            ?>
                                <li class="sub-menu">
                                    <a href="javascript:;">
                                        <i class="mdi mdi-view-grid"></i>
                                        <span><?= $value['modulo']; ?></span>
                                    </a>
                                    <ul class="sub">
                                        <?php
                                        foreach ($paginas as $key => $values) {
                                            if ($values['modulo'] == $value['modulo']) {
                                        ?>
                                                <li><a onclick="<?= $values['function']; ?>;"><i class="mdi mdi-menu-right"></i><?= $values['titulo']; ?></a></li>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <!-- sidebar menu end-->
                </div>
            </aside>
            <!--sidebar end-->
            <!--main content start original-->
            <section id="main-content">
                <section class="wrapper" id="page-container">
                    <!-- page start-->
                    <!--<div class="row">
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Ventas<br/>
                                                <small id="montoUltimaTransaccion">ultima transaccion: 0.00</small><br/>
                                                <small id="montoUltimaHora">ultima hora: 0.00</small>
                                            </h4>
                                            <div class="mini-stat-info">
                                                <h4>Hoy: <span id="ventasHoy"></span><span id="salesIndicator"></span></h4>
                                                <h4>Ayer: <span id="ventasAyer"></span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Transacciones<br/>
                                                <small id="horaUltimaTransaccion">ultima transaccion: 00:00:00</small><br/>
                                                <small id="transaccionesUltimaHora">ultima hora: 0</small>
                                            </h4>
                                            <div class="mini-stat-info">
                                                <h4>Hoy: <span id="transaccionesHoy"></span><span id="salesIndicator2"></span></h4>
                                                <h4>Ayer: <span id="transaccionesAyer"></span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Venta Promedio<br/>
                                                <small>ventas / transacciones</small><br/>
                                                <small id="promUltimaHora">ultima hora: 0</small>
                                            </h4>
                                            <div class="mini-stat-info">
                                                <h4>Hoy: <span id="ventaPromedioHoy"></span><span id="salesIndicator3"></span></h4>
                                                <h4>Ayer: <span id="ventaPromedioAyer"></span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Ventas Mensual<br/>
                                                <small>vs mismo dia mes anterior</small><br/>
                                                <small id="variacion">variacion: </small><br/>
                                            </h4>
                                            <div class="mini-stat-info">
                                                <h4><?= date('d-m-Y'); ?>: <span id="ventasMesHoy"></span><span id="salesIndicator4"></span></h4>
                                                <h4><?= date("d-m-Y", strtotime("-1 months")); ?>: <span id="ventasMesAyer"></span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Compras y Gastos<br/>
                                                <small>vs mismo dia mes anterior</small><br/>
                                                <small id="variacionCompras">variacion: </small><br/>
                                            </h4>
                                            <div class="mini-stat-info">
                                                <h4><?= date('d-m-Y'); ?>: <span id="comprasMesHoy"></span><span id="salesIndicator5"></span></h4>
                                                <h4><?= date("d-m-Y", strtotime("-1 months")); ?>: <span id="comprasMesAyer"></span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Utilidad<br/>
                                                <small>vs mismo dia mes anterior</small><br/>
                                                <small id="variacionUtilidad">variacion: </small><br/>
                                            </h4>
                                            <div class="mini-stat-info">
                                                <h4><?= date('d-m-Y'); ?>: <span id="utilidadMesHoy"></span><span id="salesIndicator6"></span></h4>
                                                <h4><?= date("d-m-Y", strtotime("-1 months")); ?>: <span id="utilidadMesAyer"></span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Margen de Ganancia<br/>
                                                <small>vs mismo dia mes anterior</small><br/>
                                                <small id="variacionMargen">variacion: </small><br/>
                                            </h4>
                                            <div class="mini-stat-info">
                                                <h4><?= date('d-m-Y'); ?>: <span id="margenMesHoy"></span><span id="salesIndicator7"></span></h4>
                                                <h4><?= date("d-m-Y", strtotime("-1 months")); ?>: <span id="margenMesAyer"></span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Resumen de Inventario<br/>
                                                <small>inventario al costo y precio venta</small>
                                            </h4>
                                            <div class="mini-stat-info">
                                                <h4>Precio Venta: <span id="inventarioVenta"></span><span id="salesIndicator8"></span></h4>
                                                <h4>Costo: <span id="inventarioCosto"></span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear">&nbsp;</div>
                                    <div class="col-md-4">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Ultimas 10 transacciones
                                            </h4>
                                            <table class="table" id="ultimas10Transacciones">
                                                <thead>
                                                    <tr>
                                                        <td>No.</td>
                                                        <td>Hora</td>
                                                        <td>Monto</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Ultimos 7 dias
                                            </h4>
                                            <table class="table" id="ultimos7dias">
                                                <thead>
                                                    <tr>
                                                        <td>No.</td>
                                                        <td>Fecha</td>
                                                        <td>Monto</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Transacciones <?= date('Y'); ?>
                                            </h4>
                                            <table class="table" id="currentYear">
                                                <thead>
                                                    <tr>
                                                        <td>No.</td>
                                                        <td>Mes</td>
                                                        <td>Monto</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Punto de Equilibrio<br/>
                                                <small>
                                                    Gastos Fijos: 0.00<br/>
                                                    Utilidad Acumulada: 0.00<br/>
                                                    %: Cumplimiento: 0.00<br/>
                                                    Utilidad Operativa: 0.00<br/>
                                                    Dia Punto Equilibrio: <br/>
                                                </small>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Creditos<br/>
                                                <small>
                                                    Hoy: 0.00<br/>
                                                    Mes: 0.00<br/>
                                                    Recuperacion mensual: 0.00<br/>
                                                    % Recuperacion mensual: 0.00<br/>
                                                </small>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Compras y Gastos<br/>
                                                <small>
                                                    Compras de Inventario: 0.00<br/>
                                                    Gastos:                0.00<br/>
                                                </small>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Utilidad y Margen<br/>
                                                <small>
                                                    Utilidad: 0.00<br/>
                                                    Margen:   0.00<br/>
                                                </small>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="clear">&nbsp;</div>
                                    <div class="col-md-6">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Vales y Cupones<br/>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <td>Documento</td>
                                                            <td>Cantidad</td>
                                                            <td>Total</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Resumen por forma de pago<br/>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <td>Descripcion</td>
                                                            <td>Cantidad</td>
                                                            <td>Total</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>EFECTIVO</td>
                                                        </tr>
                                                        <tr>
                                                            <td>POS VISANET</td>
                                                        </tr>
                                                        <tr>
                                                            <td>POS BAC</td>
                                                        </tr>
                                                        <tr>
                                                            <td>POS VERSATEC</td>
                                                        </tr>
                                                        <tr>
                                                            <td>POS LUMINET</td>
                                                        </tr>
                                                        <tr>
                                                            <td>CHEQUES</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="clear">&nbsp;</div>
                                    <div class="col-md-6">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Top 10 productos mas vendidos hoy
                                            </h4>
                                            <table class="table" id="top10hoy">
                                                <thead>
                                                    <tr>
                                                        <td>No.</td>
                                                        <td>Codigo</td>
                                                        <td>Descripcion</td>
                                                        <td>Cantidad</td>
                                                        <td>Monto</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mini-stat clearfix text-center kpis">
                                            <h4>
                                                Top 10 productos mas vendidos mensual
                                            </h4>
                                            <table class="table" id="top10mes">
                                                <thead>
                                                    <tr>
                                                        <td>No.</td>
                                                        <td>Codigo</td>
                                                        <td>Descripcion</td>
                                                        <td>Cantidad</td>
                                                        <td>Monto</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--mini statistics start-->


                    <div class="row">
                        <div class="col-md-3">
                            <div class="mini-stat clearfix">
                                <span class="mini-stat-icon orange"><i class="fa fa-shopping-basket"></i></span>
                                <div class="mini-stat-info">
                                    <div class="clearfix">&nbsp;</div>
                                    <span id="cotizaciones">0</span>
                                    No. Ventas
                                    <h5><?php $date = date('d-m-y h:i:s');
                                        echo ($date); ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat clearfix">
                                <span class="mini-stat-icon tar"><i class="fa fa-shopping-cart"></i></span>
                                <div class="mini-stat-info">
                                    <div class="clearfix">&nbsp;</div>
                                    <span id="pedidos">0</span>
                                    No. Pedidos
                                    <h5><?php $date = date('d-m-y h:i:s');
                                        echo ($date); ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat clearfix">
                                <span class="mini-stat-icon pink"><i class="fa fa-money"></i></span>
                                <div class="mini-stat-info">
                                    <div class="clearfix">&nbsp;</div>
                                    <span id="dteEmitidos">0</span> / <span id="dteComprados">0</span>
                                    <span id="dteLabelPeriodo">No. DTE EMITIDOS</span>
                                    <br><small id="dteRestantesLabel" style="font-weight:bold;font-size:11px;">Cargando...</small>

                                    <h5><?php $date = date('d-m-y h:i:s');
                                        echo ($date); ?></h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mini-stat clearfix">
                                <span class="mini-stat-icon green"><i class="fa fa-money"></i></span>
                                <div class="mini-stat-info">
                                    <div class="clearfix">&nbsp;</div>
                                    <span id="compras">0</span>
                                    No. Compras
                                    <h5><?php $date = date('d-m-y h:i:s');
                                        echo ($date); ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mini-stat clearfix">
                                <span class="mini-stat-icon orange"><i class="fa fa-money"></i></span>
                                <div class="mini-stat-info">
                                    <div class="clearfix">&nbsp;</div>
                                    <span id="totalCotizado">0</span>
                                    Total Ventas
                                    <h5><?php $date = date('d-m-y h:i:s');
                                        echo ($date); ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat clearfix">
                                <span class="mini-stat-icon tar"><i class="fa fa-money"></i></span>
                                <div class="mini-stat-info">
                                    <div class="clearfix">&nbsp;</div>
                                    <span id="totalPedidos">0</span>
                                    Total Pedidos
                                    <h5><?php $date = date('d-m-y h:i:s');
                                        echo ($date); ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat clearfix">
                                <span class="mini-stat-icon pink"><i class="fa fa-money"></i></span>
                                <div class="mini-stat-info">
                                    <div class="clearfix">&nbsp;</div>
                                    <span id="totalFacturado">0</span>
                                    Total Facturado
                                    <h5><?php $date = date('d-m-y h:i:s');
                                        echo ($date); ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mini-stat clearfix">
                                <span class="mini-stat-icon green"><i class="fa fa-money"></i></span>
                                <div class="mini-stat-info">
                                    <div class="clearfix">&nbsp;</div>
                                    <span id="totalCompras">0</span>
                                    <label>Total Compras</label>
                                    <h5><?php $date = date('d-m-y h:i:s');
                                        echo ($date); ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 depositosDetalle">
                            <div class="mini-stat clearfix">
                                <div class="mini-stat-info">
                                    <table class="table table-bordered table-striped" id="resumenDepositosMenudeo">
                                        <thead>
                                            <tr>
                                                <td colspan="4" class="text-center">RESUMEN DEPOSITOS MENUDEO</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input type="text" class="form-control input-sm" id="fechaInicio" value="<?= date('d-m-Y'); ?>" onKeydown="javascript: if 			                                                                                                (event.keyCode == 13)
                                                                                                resumenDepositos('resumenDepositosMenudeo', 2);" />
                                                </td>
                                                <td colspan="2">
                                                    <input type="text" class="form-control input-sm" id="fechaFin" value="<?= date('d-m-Y'); ?>" onKeydown="javascript: if                                                                                                               (event.keyCode == 13)
                                                                                                resumenDepositos('resumenDepositosMenudeo', 2);" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>BANCO</td>
                                                <td>CUENTA</td>
                                                <td>NUMERO DEPOSITOS</td>
                                                <td>TOTAL DEPOSITOS</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <thead id="summary">
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 depositosDetalle">
                            <div class="mini-stat clearfix">
                                <div class="mini-stat-info">
                                    <table class="table table-bordered table-striped" id="resumenDepositosMayoreo">
                                        <thead>
                                            <tr>
                                                <td colspan="4" class="text-center">RESUMEN DEPOSITOS MAYOREO</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <input type="text" class="form-control input-sm" id="fechaInicioM" value="<?= date('d-m-Y'); ?>" onKeydown="javascript: if                                                                                                           (event.keyCode == 13)
                                                                                                resumenDepositos('resumenDepositosMayoreo', 1);" />
                                                </td>
                                                <td colspan="2">
                                                    <input type="text" class="form-control input-sm" id="fechaFinM" value="<?= date('d-m-Y'); ?>" onKeydown="javascript: if                                                                                                             (event.keyCode == 13)
                                                                                                resumenDepositos('resumenDepositosMayoreo', 1);" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>BANCO</td>
                                                <td>CUENTA</td>
                                                <td>NUMERO DEPOSITOS</td>
                                                <td>TOTAL DEPOSITOS</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <thead id="summary">
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="clear">&nbsp;</div>


                        <div class="col-md-6">
                            <div class="mini-stat clearfix text-center kpis">
                                <h4>
                                    Total DTE Emitidos
                                </h4>
                                <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed" id="totalDtes">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Sucursal</th>
                                            <th>Periodo</th>
                                            <th>Mes</th>
                                            <th>Total</th>
                                            <th>Días facturados</th>
                                            <th>Fact. por día</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr id="totalDtesFoot" style="display:none;">
                                            <th colspan="4">Total</th>
                                            <th id="totalDtesFootTotal"></th>
                                            <th id="totalDtesFootDias"></th>
                                            <th id="totalDtesFootFact"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--mini statistics end-->

                    <div class="col-sm-6">
                        <section class="panel">
                            <header class="panel-heading">
                                Top 10 Productos más Vendidos
                            </header>
                            <div class="panel-body">
                                <div class="row table-responsive">
                                    <div id="chart_div1"></div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-sm-6">
                        <section class="panel">
                            <header class="panel-heading">
                                Ventas por Hora
                            </header>
                            <div class="panel-body">
                                <div class="row table-responsive">
                                    <div id="chart_div3"></div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-sm-6">
                        <section class="panel">
                            <header class="panel-heading">
                                VENTAS POR MES (SUCURSALES)
                            </header>
                            <div class="panel-body">
                                <div class="row table-responsive">
                                    <div id="chart_div4"></div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-sm-6">
                        <section class="panel">
                            <header class="panel-heading">
                                Grafica 2
                            </header>
                            <div class="panel-body">
                                <div class="row table-responsive">
                                    <div id="chart_div2"></div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- VENTAS POR MES - GRÁFICA DE BARRAS -->
                    <div class="col-sm-12">
                        <section class="panel">
                            <header class="panel-heading">
                                <i class="fa fa-bar-chart"></i> Ventas por Mes &nbsp;<span id="ventasMesTitulo" class="text-muted" style="font-size:13px;"></span>
                                <div class="pull-right" style="margin-top:-4px;">
                                    <select class="form-control input-sm" id="ventasMesAnio" onchange="ventasPorMes();" style="display:inline-block;width:90px;">
                                        <?php
                                        $anioActual = (int) date('Y');
                                        for ($a = $anioActual; $a >= 2022; $a--) {
                                            $sel = ($a == $anioActual) ? 'selected' : '';
                                            echo "<option value=\"$a\" $sel>$a</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </header>
                            <div class="panel-body">
                                <div id="chart_ventas_mes" style="height:350px;"></div>
                            </div>
                        </section>
                    </div>

                    <!-- DTEs POR MES - TABLA -->
                    <div class="col-sm-12">
                        <section class="panel">
                            <header class="panel-heading">
                                <i class="fa fa-file-text-o"></i> DTEs Utilizados por Mes
                                <div class="pull-right" style="margin-top:-4px;">
                                    <select class="form-control input-sm" id="dteAnioFiltro" onchange="getDtesPorMes();" style="display:inline-block;width:90px;">
                                        <?php
                                        $anioActual = (int) date('Y');
                                        for ($a = $anioActual; $a >= 2022; $a--) {
                                            $sel = ($a == $anioActual) ? 'selected' : '';
                                            echo "<option value=\"$a\" $sel>$a</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </header>
                            <div class="panel-body table-responsive">
                                <table class="table table-striped table-bordered table-condensed" id="tblDtesPorMes">
                                    <thead>
                                        <tr>
                                            <th>Mes</th>
                                            <th class="text-right">DTEs comprados</th>
                                            <th class="text-right">DTEs emitidos</th>
                                            <th class="text-right">Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tblDtesPorMesBody">
                                        <tr><td colspan="4" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-right" id="dteTotalComprados">-</th>
                                            <th class="text-right" id="dteTotalEmitidos">-</th>
                                            <th class="text-right" id="dteTotalSaldo">-</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </section>
                    </div>

                    <!-- DTEs POR MES - GRÁFICA -->
                    <div class="col-sm-12">
                        <section class="panel">
                            <header class="panel-heading">
                                <i class="fa fa-bar-chart"></i> Gráfica DTEs por Mes &nbsp;<span id="dteTituloGrafica" class="text-muted" style="font-size:13px;"></span>
                            </header>
                            <div class="panel-body">
                                <div id="chart_dte" style="height:320px;"></div>
                            </div>
                        </section>
                    </div>

                </section>
            </section>
            <!--main content end-->
            <!--right sidebar start-->
            <div class="right-sidebar">
                <div class="right-stat-bar">
                    <ul class="right-side-accordion">
                        <li class="widget-collapsible">
                            <ul class="widget-container">
                                <li>
                                    <div class="prog-row side-mini-stat clearfix">
                                        <div class="side-mini-graph">
                                            <div class="target-sell">
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <!--right sidebar end-->
        </section>
        <!-- CONTROLLER DIALOG-->
        <div class="modal fade" id="modal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"></h4>
                    </div>
                    <div class="modal-body" id="controllers">
                    </div>
                </div>
            </div>
        </div>
        <!-- /CONTROLLER DIALOG -->
        <!--Core js-->
        <script src="assets/js/jquery.js"></script>
        <script src="assets/bs3/js/bootstrap.min.js"></script>
        <script src="https://use.fontawesome.com/e63724d01b.js"></script>
        <script src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="assets/js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>
        <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/js/bootstrap-datetimepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
        <script src="assets/js/accounting.min.js"></script>
        <script src="assets/js/jquery.redirect.js" type="text/javascript"></script>
        <script src="assets/js/json2.js"></script>
        <script src="assets/js/jstorage.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="assets/js/dashboardInit.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/scripts.js"></script>
        <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
        <!--/Core js-->
        <script src="assets/js/configuraciones.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/functionsGlobal.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/dynamic.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/flujosBovedas.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/inventarios.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/reportes.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/planilla.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/contabilidad.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/forms.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/formsMesas.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/flujosBovedas.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/facturacion.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/bancos.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/cotizaciones.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/pedidos.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/marcajes.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/cxc.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/vales.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/planillasFarmandina.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/planillasGSP.js?v=<?= $release_version; ?>"></script>
        <script src="assets/js/planillas.js?v=<?= $release_version; ?>"></script>
    </body>

    </html>
<?php
} else {
    header("location:login.php");
}
?>