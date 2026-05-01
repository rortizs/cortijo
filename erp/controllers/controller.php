<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
date_default_timezone_set("America/Guatemala");
require_once ('../models/configuraciones.php');
$configuraciones = new configuraciones();
$service = $_REQUEST['service'];
$nombreBD = $_REQUEST['nombreBD'];
 
switch ($service) {
    case 'getRoles':
        $conexion = $configuraciones->nuevaConexion($nombreBD);
        $data = $configuraciones->getRoles($conexion);
        echo json_encode($data);
        break;
     case 'getModulos':
        $conexion = $configuraciones->nuevaConexion($nombreBD);
        $data = $configuraciones->getModulos($conexion);
        echo json_encode($data);
        break;
   
}