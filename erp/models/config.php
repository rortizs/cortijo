<?php

/**
 * Description of labels
 *
 * @author Richard Sasvin
 */
class Config
{

  public static $systemName = "ERP - DIGICOM.COM.GT";
  public static $dbD = "information_schema";
  public static $host = "45.79.3.109";
  public static $userDB = "root";
  public static $pwdDB = "";    // credencial movida fuera del repo — leer de variable de entorno o archivo externo

  public static function redirectTohttps()
  {
    if ($_SERVER['HTTPS'] != "on") {
      $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      header("Location:$redirect");
    }
  }
}
