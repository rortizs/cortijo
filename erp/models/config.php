<?php

/**
 * Description of labels
 *
 * @author Richard Sasvin
 */
class Config {

    public static $systemName = "ERP - DIGICOM.COM.GT";
    public static $dbD = "information_schema";
	public static $host = "45.79.3.109";
    //public static $host = "192.168.1.19";
    public static $userDB = "root";
    //public static $pwdDB = "R!c@rd0#2020.1"; esta clave ya no funciona
    public static $pwdDB = "10Br3nd!t@#102022";    //clave correcta
    //public static $pwdDB = "c0rt23062022"; //clave local

    public static function redirectTohttps() {
        if ($_SERVER['HTTPS'] != "on") {
            $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location:$redirect");
        }
    }

}
