<?php

session_start();
require_once("../../models/agenciasViajes.php");
$agenciasViajes = new agenciasViajes();
if ($handle = opendir('./air/')) {
    /* This is the correct way to loop over the directory. */
    echo 'Hora Inicio: ' . date('H:i:s') . "<br/>";
    while (false !== ($entry = readdir($handle))) {
        if ($entry != '.' && $entry != '..') {
            $fileName = $entry;
            $file = new SplFileObject("./air/" . $fileName);
            //Parametros de busqueda
            $agencia = 'APGUA 502 2332-8811 - TRASVIAJES - A';
            $agencia2 = 'APGUA 502 23616838 - LAX TRAVEL - A';
            $agencia3 = 'APGUA 502 2332-8811 - TRASVIAJES - A//E';
            $param1 = "MUC1A"; //RESERVA
            $param2 = "T-K"; //BOLETOS
            $param3 = "I-"; //PASAJEROS
            $param4 = "A-";  //CODIGO Y NOMBRE LINEA AREA
            $param5 = "RIZNIT:"; //NIT
            $param6 = "RIZNOMBRE CONTRIBUYENTE:"; //NOMBRE DE FACTURACION
            $param7 = "RIZDIRECCION:"; //DIRECCION FACTURACION
            $param8 = "K-FUSD"; //CARGOS
            $param9 = "H-"; //ITINERARIO
            $param10 = "M-"; //ITINERARIO PARTE 2
            $param11 = "O-"; //ITINERARIO PARTE 3
            $param12 = "Q-"; //COMPOSICION DE TARIFA
            $param13 = "FE"; //ENDOSOS
            $param14 = "FP"; //FORMA DE PAGO
            $param15 = "FT"; //CODIGO AUTH
            $param16 = "KFTF"; //IMPUESTOS
            $param17 = "TK"; //ESTATUS BOLETO
            $param18 = "RIIVE"; //CODIGO VENDEDOR
            $param19 = "RIICL"; //CODIGO CLIENTE
            $param20 = "RIIAG"; //CODIGO AGENCIA
            $boletos = "";
            $pasajeros = "";
            $itinerario = "";
            $itinerarioP2 = "";
            $itinerarioP3 = "";
            $itinerarioF = "";
            $params = array();
            $impuestos = array();
            $linecount = 0;
            //EMPIEZA PROCESO DOCUMENTO
            while (!$file->eof()) {
                $line = $file->fgets();
                //RESERVA
                $find = strpos($line, $param1);
                if ($find !== false) {
                    //$line . "<br/>";
                    $linea = explode(';', str_replace($param1, '', $line));
                    foreach ($linea as $key => $value) {
                        if ($key == 0) {
                            $_POST['reserva'] = trim($value);
                        }
                        if ($key == 2) {
                            $_POST['gua'] = trim($value);
                        }
                    }
                }
                //BOLETOS
                $find = strpos($line, $param2);
                if ($find !== false) {
                    //substr(str_replace($param2, '', $line), 4, strlen(str_replace($param2, '', $line))) . "<br/>";
                    $boletos .= substr(str_replace($param2, '', $line), 4, strlen(str_replace($param2, '', $line))) . ",";
                }
                //PASAJEROS
                $find = strpos($line, $param3);
                if ($find !== false) {
                    $linea = explode(';', $line);
                    foreach ($linea as $key => $value) {
                        if (strlen($value) !== 0 && strlen($value) !== 2 && $key != 0 && $value != $agencia && $value != $agencia2 && $value != $agencia3) {
                            $pasajeros .= str_replace('/', ' ', substr($value, 2, strlen($value))) . ",";
                        }
                    }
                }
                //CODIGO Y NOMBRE LINEA AREA
                $find = strpos($line, $param4);
                if ($find !== false) {
                    if (substr($line, 0, 2) == 'A-') {
                        $linea = explode(';', $line);
                        $_POST['codigoLineaArea'] = trim($linea[1]);
                        $_POST['lineaArea'] = trim(str_replace('A-', '', $linea[0]));
                    }
                }
                //NIT
                $find = strpos($line, $param5);
                if ($find !== false) {
                    $linea = explode(';', $line);
                    $_POST['nit'] = trim(str_replace($param5, '', $linea[0]));
                }
                //NOMBRE DE FACTURACION
                $find = strpos($line, $param6);
                if ($find !== false) {
                    $linea = explode(';', $line);
                    $_POST['nombre'] = trim(str_replace($param6, '', $linea[0]));
                }
                //DIRECCION FACTURACION
                $find = strpos($line, $param7);
                if ($find !== false) {
                    $linea = explode(';', $line);
                    $_POST['direccion'] = trim(str_replace($param7, '', $linea[0]));
                }
                //CARGOS
                $find = strpos($line, $param8);
                if ($find !== false) {
                    $linea = explode(';', $line);
                    $_POST['montoDolares'] = trim(str_replace('K-FUSD', '', $linea[0]));
                    $_POST['montoSinImpuestos'] = trim(str_replace('GTQ', '', $linea[1]));
                    $_POST['montoTotal'] = trim(str_replace('GTQ', '', $linea[12]));
                    $_POST['totalImpuestos'] = trim(($_POST['montoTotal'] - $_POST['montoSinImpuestos']));
                    $_POST['tasaCambio'] = trim($linea[13]);
                }
                //ITINERARIO
                $find = strpos($line, $param9);
                if ($find !== false) {
                    $linecount++;
                    $linea = explode(';', $line);
                    $linea2 = explode(' ', $linea[5]);
                    //$linecount . ' ' . $line . "<br/>";
                    $itinerario .= $linecount . ";" . substr($linea[1], 3, strlen($linea[1])) . ";" . $linea2[0] . ' ' . $linea2[4] . ";" . $linea2[5] . ";" . $linea2[7] . ";" . substr($linea[6], 0, 2) . ";" . $linea[13] . ";";
                    // str_replace(';', ' ', str_replace('H-', '', trim($line))) . ",";
                }
                //ITINERARIO PARTE 2
                $find = strpos($line, $param10);
                if ($find !== false) {
                    $itinerarioP2 = explode(';', str_replace($param10, '', $line));
                }
                //ITINERARIO PARTE 3
                $find = strpos($line, $param11);
                if ($find !== false) {
                    $itinerarioP3 = explode(';', str_replace($param11, '', $line));
                }
                //COMPOSICION DE TARIFA
                $find = strpos($line, $param12);
                if ($find !== false) {
                    if (substr($line, 0, 2) == $param12) {
                        $_POST['compTarifa'] = trim($line);
                    }
                }
                //ENDOSOS
                $find = strpos($line, $param13);
                if ($find !== false) {
                    if (substr($line, 0, 2) == $param13) {
                        $_POST['endosos'] = trim($line);
                    }
                }
                //FORMA DE PAGO
                $find = strpos($line, $param14);
                if ($find !== false) {
                    $linea = explode(';', $line);
                    $_POST['formaPago'] = trim(str_replace($param14, '', $linea[0]));
                }
                //CODIGO AUTH
                $find = strpos($line, $param15);
                if ($find !== false) {
                    $linea = explode(';', $line);
                    $_POST['codigoAuth'] = trim(str_replace($param15, '', $linea[0]));
                }
                //IMPUESTOS
                $find = strpos($line, $param16);
                if ($find !== false) {
                    $linea = explode(';', $line);
                    foreach ($linea as $key => $value) {
                        if (strlen($value) !== 0 && strlen($value) !== 2 && $value != 'KFTF') {
                            array_push($impuestos, trim(str_replace('GTQ', '', $value)));
                        }
                    }
                }
                //ESTATUS DEL BOLETO
                $find = strpos($line, $param17);
                if ($find !== false) {
                    if (substr($line, 0, 2) == $param17) {
                        $_POST['estatusBoleto'] = trim(str_replace($param17, '', $line));
                        $_POST['fecha'] = trim(substr($_POST['estatusBoleto'], 2, 5) . date('Y'));
                    }
                }
                //CODIGO VENDEDOR
                $find = strpos($line, $param18);
                if ($find !== false) {
                    $_POST['codVendedor'] = trim(str_replace($param18, '', $line));
                }
                //CODIGO CLIENTE
                $find = strpos($line, $param19);
                if ($find !== false) {
                    $_POST['codCliente'] = trim(str_replace($param19, '', $line));
                }
                //CODIGO AGENCIA
                $find = strpos($line, $param20);
                if ($find !== false) {
                    $_POST['codAgencia'] = trim(str_replace($param20, '', $line));
                }
            }
            //END PROCESO DOCUMENTO
            $itinerario = explode(';', substr($itinerario, 0, -1));
            foreach ($itinerario as $key => $value) {
                $line = explode(';', $value);
                $itinerarioF .= $line[0] . " " . $line[1] . " " . $line[2] . " " . $line[3] . " " . $line[4] . " " . $line[5] . " " . $itinerarioP2[$key] . " " . str_replace('XX', '', $itinerarioP3[$key]) . " " . $line[6] . ";";
            }
            $boletos = explode(',', substr($boletos, 0, -1));
            $pasajeros = explode(',', substr($pasajeros, 0, -1));
            //$_POST['fecha'] = $agenciasViajes->returnDateAmadeus($fileName);
            $_POST['fileName'] = trim($fileName);
            $_POST['itinerario'] = trim(substr($itinerarioF, 0, -1));
            foreach ($boletos as $key => $value) {
                //    $_POST['fileName'] . "<br/>";
                //    $_POST['gua'] . "<br/>";
                //    $_POST['reserva'] . "<br/>";
                //    $_POST['codigoLineaArea'] . "<br/>";
                //    $_POST['lineaArea'] . "<br/>";
                $_POST['boleto'] = trim($value);
                $_POST['pasajero'] = trim(str_replace('/r/n', '', $pasajeros[$key]));
                //    $_POST['nit'] . "<br/>";
                //    $_POST['nombre'] . "<br/>";
                //    $_POST['direccion'] . "<br/>";
                //    $_POST['montoDolares'] . "<br/>";
                //    $_POST['montoSinImpuestos'] . "<br/>";
                //    $_POST['totalImpuestos'] . "<br/>";
                //    $_POST['montoTotal'] . "<br/>";
                //    $_POST['tasaCambio'] . "<br/>";
                //    $_POST['itinerario'] . "<br/>";
                //    $_POST['compTarifa'] . "<br/>";
                //    $_POST['endosos'] . "<br/>";
                //    $_POST['formaPago'] . "<br/>";
                //    $_POST['codigoAuth'] . "<br/>";
                //    $_POST['estatusBoleto'] . "<br/>";
                //'TAXES:' . $_POST['totalImpuestos'] . "<br/>";
                foreach ($impuestos as $key => $value) {
                    $value . "<br/>";
                }
                //echo '<hr/>';
                $proces = $agenciasViajes->insertAgenciasViajes($_POST);
                echo json_encode($proces) . "<br/>";
            }
        }
    }
    closedir($handle);
    echo '<br/>';
    echo 'Hora Fin: ' . date('H:i:s');
}