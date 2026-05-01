<?php

session_start();
$action = $_REQUEST['action'];
switch ($action) {
    case 'logout':
        session_destroy();
        header("location:login.php");
        break;
    case 'cambiarEmpresa':
        header("location:empresas.php");
        break;
}
?>
