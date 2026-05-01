<?php

/** adminController
 *  @author Richard Sasvin
 * 
 */
session_start();
date_default_timezone_set("America/Guatemala");
require_once("../models/admin.php");
require_once("../models/dynamic.php");
$admin = new Admin();
$dynamic = new Dynamic();
$service = $_REQUEST['service'];

class ControladorAdmins{

    /*==========================
    MOSTRAR CUENTA DTE REALIZADOS
    ============================*/
    static public function ctrTotalFactura($idEmpresas){

        $respuesta = Admins::getTotalFacturacion($idEmpresas);
        return $respuesta;

    }

    /*===========================

    =============================*/
    static public function ctrTotalFacturas($idEmpresas){

        $respuesta = Admins = getTotalVentas($idEmpresas);
        return $respuesta;

    }
}