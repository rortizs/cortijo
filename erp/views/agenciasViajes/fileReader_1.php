<?php

/**
 * @version 1.0
 * Decoder File Amadeus
 */
session_start();
require_once("../../models/agenciasViajes.php");
$agenciasViajes = new agenciasViajes();
$agencia = 'APGUA 502 2332-8811 - TRASVIAJES - A';
$agencia2 = 'APGUA 502 23616838 - LAX TRAVEL - A';
$agencia3 = 'APGUA 502 2332-8811 - TRASVIAJES - A//E';
//-
if ($handle = opendir('./amadeus/AIR/')) {
    /* This is the correct way to loop over the directory. */
    echo 'Hora Inicio: ' . date('H:i:s') . "<br/>";
    while (false !== ($entry = readdir($handle))) {
        if ($entry != '.' && $entry != '..') {
            //echo $entry . "<br/>";
            //READ FILE
            $fileName = $entry;
            $file = new SplFileObject("./amadeus/AIR/" . $fileName);
            $param1 = "A-";  //CODIGO Y NOMBRE LINEA AREA
            $param2 = "T-";  //TICKETS
            $param3 = "AMD"; //NUMERO DE PASAJEROS
            $param4 = "I-"; //NOMBRES DE PASAJEROS
            $param5 = "RIZNIT:"; //NIT
            $param6 = "RIZNOMBRE CONTRIBUYENTE:"; //NOMBRE DE FACTURACION
            $param7 = "RIZDIRECCION:"; //DIRECCION FACTURACION
            $param8 = "K-FUSD"; //CARGOS
            $param9 = "KFTF"; //IMPUESTOS
            $param10 = "1A"; //NUMERO DE RESERVA
            $param11 = "H-"; //ITINERARIO
            $param12 = "FP"; //ITINERARIO
            $numeroPasajeros = 0;
            $tickets = "";
            $pasajeros = "";
            $itinerario = "";
            $impuestos = array();
            while (!$file->eof()) {
                $line = $file->fgets();
                //FORMA DE PAGO
                $find = strpos($line, $param12);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    $_POST['formaPago'] = str_replace('FP', '', $linea[0]);
                }
                //ITINERARIO
                $find = strpos($line, $param11);
                if ($find !== false) {
                    $itinerario .= str_replace('#', ' ', str_replace('H-', '', trim($line))) . ",";
                }
                //RESERVA
                $find = strpos($line, $param10);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    $_POST['reserva'] = str_replace('1A', '', $linea[0]);
                }
                //CODIGO Y NOMBRE LINEA AREA
                $find = strpos($line, $param1);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    $_POST['codigoLineaArea'] = $linea[1];
                    $_POST['lineaArea'] = str_replace('A-', '', $linea[0]);
                }
                //TICKETS
                $find = strpos($line, $param2);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    foreach ($linea as $key => $value) {
                        $tickets .= str_replace('T-K', '', trim($value)) . ",";
                    }
                }
                //NOMBRES DE PASAJEROS
                $find = strpos($line, $param4);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    foreach ($linea as $key => $value) {
                        if (strlen($value) !== 0 && strlen($value) !== 2 && $key != 0 && $value != $agencia && $value != $agencia2 && $value != $agencia3) {
                            //echo $key . '-' . strlen($value) . '-' . $value . "<br/>";
                            $pasajeros .= $value . ",";
                            $numeroPasajeros++;
                        }
                    }
                }
                //NIT
                $find = strpos($line, $param5);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    $_POST['nit'] = str_replace($param5, '', $linea[0]);
                }
                //NOMBRE DE FACTURACION
                $find = strpos($line, $param6);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    $_POST['nombre'] = str_replace($param6, '', $linea[0]);
                }
                //DIRECCION FACTURACION
                $find = strpos($line, $param7);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    $_POST['direccion'] = str_replace($param7, '', $linea[0]);
                }
                //CARGOS
                $find = strpos($line, $param8);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    $_POST['montoDolares'] = str_replace('K-FUSD', '', $linea[0]);
                    $_POST['montoSinImpuestos'] = str_replace('GTQ', '', $linea[1]);
                    $_POST['montoTotal'] = str_replace('GTQ', '', $linea[12]);
                    $_POST['tasaCambio'] = $linea[13];
                    array_push($impuestos, str_replace('GTQ', '', $linea[1]));
                }
                //IMPUESTOS
                $find = strpos($line, $param9);
                if ($find !== false) {
                    $linea = explode('#', $line);
                    foreach ($linea as $key => $value) {
                        if (strlen($value) !== 0 && strlen($value) !== 2 && $value != 'KFTF') {
                            array_push($impuestos, str_replace('GTQ', '', $value));
                        }
                    }
                }
            }
            $file = null;
            $_POST['itinerario'] = substr($itinerario, 0, -1);
            $_POST['tickets'] = substr($tickets, 0, -1);
            $_POST['noPasajeros'] = $numeroPasajeros;
            $_POST['pasajeros'] = substr($pasajeros, 0, -1);
            $_POST['fileName'] = $fileName;
            $_POST['impuestos'] = $impuestos;
            $_POST['from'] = 'amadeus';
            $_POST['fecha'] = $agenciasViajes->returnDateAmadeus($fileName);
            $proces = $agenciasViajes->insertAgenciasViajes($_POST);
            echo json_encode($proces) . "<br/>";
            //echo json_encode($_POST['impuestos']);
            //END READ FILE
        }
    }
    closedir($handle);
    echo '<br/>';
    echo 'Hora Fin: ' . date('H:i:s');
}

