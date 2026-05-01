<!DOCTYPE html>
<?php
session_start();
?>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <!--CSS-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" media="screen,projection" />
        <link rel='stylesheet' href="https://cdn.materialdesignicons.com/3.8.95/css/materialdesignicons.min.css" type="text/css">
        <link type="text/css" rel="stylesheet" href="assets/css/cajaRapida.css" media="screen,projection" />
        <!--/CSS-->
    </head>

    <body>
        <input type="hidden" id="dbProject" value="<?= $_SESSION['dbProject']; ?>"/>
        <input type="hidden" id="idDocumento" />
        <input type="hidden" id="serie" />
        <input type="hidden" id="correlativo" />
        <div class="navbar-fixed">
            <nav>
                <div class="nav-wrapper">
                    <div class="row">
                        <div class="col s2">
                            <img src="assets/images/logo_digicom.png" class="logoNav"/>
                        </div>
                        <div class="col s4">
                            <!-- <a href="#!" class="breadcrumb" id="deptoName">Departamento</a>
                            <a href="#!" class="breadcrumb" id="catName">Categoria</a>
                            <a href="#!" class="breadcrumb" id="subCatName">Sub Categoria</a> -->
                        </div>
                        <div class="col s6">
                            <ul class="right hide-on-med-and-down">
                                <li id="usuario">USUARIO: <?= $_SESSION['userName']; ?></li>
                                <li><a class="waves-effect waves-light btn" onclick="openCorteCaja();">Corte Caja</a></li>
                                <li><a class="waves-effect waves-light btn red" onclick="cancelarVenta();"><i class="material-icons">delete</i></a></li>
                                <li><a class="waves-effect waves-light btn red" href="logout.php?action=logout"><i class="material-icons">power_settings_new</i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col s3 box">
                    <div class="row" id="categorias">
                    </div>
                </div>
                <div class="col s6 box">
                    <div class="row" id="productos">
                    </div>
                </div>
                <div class="col s3 box center-align">
                    <div class="row">
                        <div class="col s12 detalleCarritos">
                            <ul class="collection" id="detalleCarrito">
                            </ul>
                        </div>
                        <div class="col s12 resumenCarrito">
                            <h5>Resumen de Venta</h5>
                            <ul class="collection" id="resumenCarrito">
                            </ul>
                            <a class="waves-effect waves-light btn green btns2 middle-align" onclick="getDatosVenta();">
                                Finalizar
                            </a>
                            <!-- <br/>
                            <a class="waves-effect waves-light btn red btn3 btns" onclick="cancelarVenta();"><i class="mdi mdi-trash-can"></i> Cancelar</a> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL-->
        <div id="modal1" class="modal">
            <div class="modal-header">
                <h5 id="modal-title"></h5>
                <i class="mdi mdi-close-circle-outline modal-close"></i>
            </div>
            <div class="modal-content">
                <div class="row">
                </div>
            </div>
        </div>
        <!-- /MODAL-->
        <!--JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/accounting.js/0.4.1/accounting.min.js"></script>
        <script src="assets/js/cajaRapida.js"></script>
        <!--/JS-->
    </body>

</html>