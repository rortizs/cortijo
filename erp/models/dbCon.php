<?php

if (!isset($_SESSION)) {
    session_start();
}
require_once('config.php');

/**
 * CLASS dbCon
 */
class dbCon {

    /** CONEXION PRINCIPAL 
     *
     *
     */
    public static function conPrincipal() {
        $conexion = mysql_connect(Config::$host, Config::$userDB, Config::$pwdDB);
        mysql_query("SET NAMES 'utf8'");
        mysql_select_db($_SESSION['dbProject']);
        if ($conexion == true) {
            return $conexion;
        } else {
            echo "conexion fallida -> PRINCIPAL";
        }
    }

    /** conexion dynamic
     * 
     */
    public static function conDynamic($db) {
        $conexion = mysql_connect(Config::$host, Config::$userDB, Config::$pwdDB);
        mysql_query("SET NAMES 'utf8'");
        mysql_select_db($db);
        if ($conexion == true) {
            return $conexion;
        } else {
            //return $conexion;
            echo "conexion fallida Information_schema";
        }
    }

}
