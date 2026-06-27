<?php
/**
 * POS / Seleccione de empresas
 * @author Jonathan Juarez
 * @version 1.0 20160805
 */
session_start();
$release_version = date('Y.m.d.H.i.s');
if ($_SESSION['userName'] != '') {
    require_once ("models/config.php");
    require_once ("models/login.php");
    $login = new Login();
    $loadEmpresas = $login->loadEmpresas($_SESSION['idUsuarios'], $_SESSION['idRoles']);
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="DIGICOM.COM.GT ERP POS CRM Ecommerce Guatemala">
            <meta name="author" content="Richard Ortiz richard.ortiz@digicom.com.gt">
            <link rel="icon" type="image/png" sizes="50x50" href="assets/images/logo_cubix.png">
            <title><?= Config::$systemName ?></title>
            <!--Core CSS -->
            <link href="assets/bs3/css/bootstrap.min.css" rel="stylesheet">
            <link href="assets/css/bootstrap-reset.css" rel="stylesheet">
            <link href="assets/css/style.css" rel="stylesheet">
            <link href="assets/css/style-responsive.css" rel="stylesheet" />
            <link href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" >
            <!--/Core CSS -->
            <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
            <link href="assets/css/empresas.css?v=<?= $release_version; ?>" rel="stylesheet"/>
        </head>
        <body>
            <div class="empresas">
                <div class="row">
                    <div class="col-lg-6 logoCubix text-center">
                        <img src="assets/images/LogoCortijo_login.png" class="img-responsive">
                        <br/>
                        <?php
                        if ($_SESSION['idRoles'] == '1') {
                            ?>
                            <button class="btn btn-primary btn-sm" onclick="AddEmpresa('empresas', 'Nueva Empresa');">
                                Crear Empresa&nbsp;<i class="fa fa-plus"></i>
                            </button>
                            <?php
                        }
                        ?>
                        <button class="btn btn-primary btn-sm" onclick="window.location = 'logout.php?action=logout'">
                            Salir del Sistema&nbsp;<i class="fa fa-power-off"></i>
                        </button>
                    </div>
                    <div class="col-lg-6 listadoEmpresas text-center">
                        <h1>Listado de Empresas</h1>
                        <table id="tableEmpresas" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr class="info text-uppercase" style="font-weight: bold;">
                                    <td><i class="fa fa-building"></i>&nbsp;Empresa</td>
                                    <td><i class="fa fa-hashtag"></i>&nbsp;NIT</td>
                                    <td>INGRESAR</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($loadEmpresas); $i++) {
                                    ?>
                                    <tr>
                                        <td><?= $loadEmpresas[$i]['nombreComercial']; ?></td>
                                        <td><?= $loadEmpresas[$i]['nit']; ?></td>
                                        <td align="center"> 
                                            <button class="btn btn-primary btn-xs" onclick="ingresarEmpresa('<?= $loadEmpresas[$i]['id']; ?>', '<?= $loadEmpresas[$i]['nombreComercial']; ?>');">
                                                <i class="fa fa-sign-in" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- CONTROLLER DIALOG-->
            <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
            <script src="assets/bs3/js/bootstrap.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js" type="text/javascript"></script>
            <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
            <script src="https://use.fontawesome.com/e63724d01b.js"></script>
            <script src="assets/js/dynamic.js?v=<?= $release_version; ?>" type="text/javascript"></script>
            <script src="assets/js/jquery.redirect.js?v=<?= $release_version; ?>" type="text/javascript"></script>
            <script src="assets/js/empresas.js?v=<?= $release_version; ?>"></script>
        </body>
    </html>
    <?php
} else {
    header("location:login.php");
}
?>