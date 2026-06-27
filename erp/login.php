<?php
/**
 * Login Page
 * @author Jonathan Juarez
 * @version 1.0 20141123
 */
session_start();
require_once ("models/config.php");
require_once ("models/login.php");
$login = new Login();
// VALIDAR CONF DB PROYECTO
$url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
$validateConfProject = $login->validateConfProject($url);
if ($validateConfProject['id'] === null) {
    echo '<div id="loader"><div class="child-loader error">Instancia: ' . $url . '<br/>status no configurado o bloqueado<br/>Comuniquese con el administrador del sistema</div></div>';
} else {
    $_SESSION['dbProject'] = $validateConfProject['dbname'];
}
if ($validateConfProject['ssl'] === '1') {
    Config::redirectTohttps();
}
//
if ($_SESSION['userName'] != '') {
    switch ($_SESSION['idRoles']) {
        case 1:
            header("location: main.php");
            break;
        case 3:
            if ($_SESSION['tipoCaja'] === '1') {
                header("location: cajaRapida.php");
            } else {
                header("location: caja.php");
            }
            break;
        case 12:
            header("location: caja.php");
            break;
        default:
            header("location: empresas.php");
            break;
    }
} else {
    if (isset($_POST['usuario']) && isset($_POST['password'])) {
        $loginUser = $login->loginUser($_POST['usuario'], $_POST['password']);
        switch ($_SESSION['idRoles']) {
            case 3:
                if ($_SESSION['tipoCaja'] === '1') {
                    header("location: cajaRapida.php");
                } else {
                    header("location: caja.php");
                }
                break;
            case 12:
                header("location: caja.php");
                break;
            default:
                header("location: empresas.php");
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="DIGICOM.COM.GT  ERP POS CRM Ecommerce Guatemala">
        <meta name="author" content="info@digicom.com.gt">
        <link rel="icon" type="image/png" sizes="50x50" href="assets/images/logo_cubix.png">
        <title><?= Config::$systemName ?></title>
        <!--Core CSS -->
        <link href="assets/bs3/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">
        <link href="assets/css/style-responsive.css" rel="stylesheet" />
        <link rel='stylesheet' href="https://cdn.materialdesignicons.com/3.8.95/css/materialdesignicons.min.css" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
        <!--/Core CSS -->
        <style>
            .bg-login{
                background-color: #185ca3 !important;
            }
            .login {
                width: 330px !important;
                height: 330px !important;
                background-color: #FFF !important;
                position: absolute;
                top:0;
                bottom: 0;
                left: 0;
                right: 0;
                margin: auto;
                border:2px solid #28B4E9 !important;
                border-radius: 10px !important;
                padding: 10px !important;
            }
            .input-group-addon{
                background-color: #1A3973 !important;
                color: #FFF !important;
                border:1px solid #28B4E9 !important;
            }
            .login .btn-primary{
                background-color: #1A3973 !important;
            }
            .error{
                background-color: #FF0000 !important;
                color: #FFF !important;
            }
        </style>
    </head>
    <body class="bg-login">
        <div class="login">
            <form method="post" action="login.php" autocomplete="off">
                <div class="row">
                    <div class="col-lg-12" style="margin-bottom: 20px !important;">
                        <img src="assets/images/LogoCortijo_login.png" class="img-responsive">
                    </div>
                    <div class="col-lg-12" style="margin-bottom: 20px !important;">
                        <div class="input-group" style="margin-bottom: 5px !important;">
                            <span class="input-group-addon"><span class="mdi mdi-account"></span></span>
                            <input type="text" class="form-control" placeholder="Usuario" name="usuario">
                        </div>
                        <div class="input-group" style="margin-bottom: 5px !important;">
                            <span class="input-group-addon"><span class="mdi mdi-lock"></span></span>
                            <input type="password" class="form-control" placeholder="Contraseña" name="password" id="password">
                            <span class="input-group-addon" id="checkPassword">
                                <input type="hidden" id="checkPwd" value="0">
                                <span class="mdi mdi-eye-off"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12 text-center">
                        <button class="btn btn-lg btn-block btn-primary btn-sm" type="submit">
                            INGRESAR <i class="mdi mdi-login"></i>
                        </button>
                        <br/>
                        <a href="http://www.digicom.com.gt" target="_blank" style="color:#FFF !important;">www.digicom.com.gt</a>
                    </div>
                </div>
            </form>
        </div>
        <!--Core js-->
        <script src="assets/js/jquery.js"></script>
        <script src="assets/bs3/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                $('.form-control').val('');
                $('#checkPassword').click(function () {
                    if ($("#checkPwd").val() === '0') {
                        $("#checkPwd").val('1');
                        $('#password').get(0).type = 'text';
                        $('#checkPassword span').removeClass('mdi-eye-off').addClass('mdi-eye');
                    } else {
                        $("#checkPwd").val('0');
                        $('#password').get(0).type = 'password';
                        $('#checkPassword span').removeClass('mdi-eye').addClass('mdi-eye-off');
                    }
                });
            });
        </script>
        <!--/Core js-->
    </body>
</html>
