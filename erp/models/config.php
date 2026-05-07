<?php

/**
 * Description of labels
 *
 * @author Richard Sasvin
 */

// Credenciales cargadas desde archivo externo fuera del webroot
if (file_exists('/etc/erp-cortijo.conf.php')) {
  require_once '/etc/erp-cortijo.conf.php';
}

class Config
{

  public static $systemName = "ERP - DIGICOM.COM.GT";
  public static $dbD = "information_schema";
  public static $host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
  public static $userDB = defined('DB_USER') ? DB_USER : '';
  public static $pwdDB = defined('DB_PASS') ? DB_PASS : '';

  public static function redirectTohttps()
  {
    if ($_SERVER['HTTPS'] != "on") {
      $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      header("Location:$redirect");
    }
  }
}
