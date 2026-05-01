<?php

/**
 * PROCESO DE CIERRE
 * 1. MAYOR
 * 2. BALANCE DE SALDOS
 * 3. ESTADO DE RESULTADOS
 * 4. BALANCE GENERAL
 */
session_start();
require_once("../../models/contabilidad.php");
$conta = new Contabilidad();
// 3. ESTADO DE RESULTADOS
$_REQUEST['tipo'] = 1;
//$_REQUEST['periodo'] = '2017';
//$_REQUEST['mes'] = '08';
$estadoResultados = $conta->getBalanceGeneralEstadoResultados($_REQUEST, $_SESSION['idEmpresa']);
$saldos = [];
$params = array();
for ($a = 0; $a < count($estadoResultados); $a++) {
    array_push($saldos, $estadoResultados[$a]['saldo']);
}
for ($i = 0; $i < count($estadoResultados); $i++) {
    $saldo = $i;
    for ($j = $i + 1; $j < count($estadoResultados); $j++) {
        if ($estadoResultados[$i]['padre'] == $estadoResultados[$j]['padre']) {
            $estadoResultados[$i]['saldo'] = ($estadoResultados[$i]['saldo'] + $estadoResultados[$j]['saldo']) * -1;
            $estadoResultados[$j]['saldo'] = 0;
        }
    }
    if ($estadoResultados[$i]['saldo'] != 0) {
        $getCuenta = $conta->getCuenta($estadoResultados[$i]['padre'], $_SESSION['idEmpresa']);
        $getCuenta2 = $conta->getCuenta($getCuenta['padre'], $_SESSION['idEmpresa']);
        $getCuenta3 = $conta->getCuenta($getCuenta2['padre'], $_SESSION['idEmpresa']);
        if ($getCuenta2['padre'] !== '') {
            if ($getCuenta2['padre'] == '1') {
                $total1 += $estadoResultados[$i]['saldo'];
            } else {
                $total2 += $estadoResultados[$i]['saldo'];
            }
            $paramsValue = array("cuenta" => $getCuenta2['padre'],
                "cuentaContable" => strtoupper($getCuenta3['cuentaContable']),
                "saldo4" => 0.00,
                "saldo3" => 0.00,
                "saldo2" => 0.00,
                "saldo1" => ($estadoResultados[$i]['saldo'] > 0 ? $estadoResultados[$i]['saldo'] : ($estadoResultados[$i]['saldo'] * -1)));
            array_push($params, $paramsValue);
        }
        if ($getCuenta['padre'] !== '') {
            $paramsValue = array("cuenta" => $getCuenta['padre'],
                "cuentaContable" => strtoupper($getCuenta2['cuentaContable']),
                "saldo4" => 0.00,
                "saldo3" => 0.00,
                "saldo2" => ($estadoResultados[$i]['saldo'] > 0 ? $estadoResultados[$i]['saldo'] : ($estadoResultados[$i]['saldo'] * -1)),
                "saldo1" => 0.00);
            array_push($params, $paramsValue);
        }
        $paramsValue = array("cuenta" => $estadoResultados[$i]['padre'],
            "cuentaContable" => strtoupper($getCuenta['cuentaContable']),
            "saldo4" => 0.00,
            "saldo3" => ($estadoResultados[$i]['saldo'] > 0 ? $estadoResultados[$i]['saldo'] : ($estadoResultados[$i]['saldo'] * -1)),
            "saldo2" => 0.00,
            "saldo1" => 0.00);
        array_push($params, $paramsValue);
    }
    $paramsValue = array("cuenta" => $estadoResultados[$i]['cuenta'],
        "cuentaContable" => strtoupper($estadoResultados[$i]['cuentaContable']),
        "saldo4" => ($saldos[$i] > 0 ? $saldos[$i] : ($saldos[$i] * -1)),
        "saldo3" => 0.00,
        "saldo2" => 0.00,
        "saldo1" => 0.00);
    array_push($params, $paramsValue);
}
$insertEstadoResultados = $conta->insertEstadoResultados($params, $_REQUEST['periodo'], $_REQUEST['mes'], $_SESSION['idEmpresa']);
echo json_encode($insertEstadoResultados);
echo '<hr/>';
// 4. BALANCE GENERAL
$_REQUEST['tipo'] = 2;
$_REQUEST['periodo'] = '2017';
$_REQUEST['mes'] = '08';
$balanceGeneral = $conta->getBalanceGeneralEstadoResultados($_REQUEST, $_SESSION['idEmpresa']);
$saldos = [];
$params = array();
for ($a = 0; $a < count($balanceGeneral); $a++) {
    array_push($saldos, $balanceGeneral[$a]['saldo']);
}
for ($i = 0; $i < count($balanceGeneral); $i++) {
    $saldo = $i;
    for ($j = $i + 1; $j < count($balanceGeneral); $j++) {
        if ($balanceGeneral[$i]['padre'] == $balanceGeneral[$j]['padre']) {
            $balanceGeneral[$i]['saldo'] = ($balanceGeneral[$i]['saldo'] + $balanceGeneral[$j]['saldo']) * -1;
            $balanceGeneral[$j]['saldo'] = 0;
        }
    }
    if ($balanceGeneral[$i]['saldo'] != 0) {
        $getCuenta = $conta->getCuenta($balanceGeneral[$i]['padre'], $_SESSION['idEmpresa']);
        $getCuenta2 = $conta->getCuenta($getCuenta['padre'], $_SESSION['idEmpresa']);
        $getCuenta3 = $conta->getCuenta($getCuenta2['padre'], $_SESSION['idEmpresa']);
        if ($getCuenta2['padre'] !== '') {
            if ($getCuenta2['padre'] == '1') {
                $total1 += $balanceGeneral[$i]['saldo'];
            } else {
                $total2 += $balanceGeneral[$i]['saldo'];
            }
            $paramsValue = array("cuenta" => $getCuenta2['padre'],
                "cuentaContable" => strtoupper($getCuenta3['cuentaContable']),
                "saldo4" => 0.00,
                "saldo3" => 0.00,
                "saldo2" => 0.00,
                "saldo1" => ($balanceGeneral[$i]['saldo'] > 0 ? $balanceGeneral[$i]['saldo'] : ($balanceGeneral[$i]['saldo'] * -1)));
            array_push($params, $paramsValue);
        }
        if ($getCuenta['padre'] !== '') {
            $paramsValue = array("cuenta" => $getCuenta['padre'],
                "cuentaContable" => strtoupper($getCuenta2['cuentaContable']),
                "saldo4" => 0.00,
                "saldo3" => 0.00,
                "saldo2" => ($balanceGeneral[$i]['saldo'] > 0 ? $balanceGeneral[$i]['saldo'] : ($balanceGeneral[$i]['saldo'] * -1)),
                "saldo1" => 0.00);
            array_push($params, $paramsValue);
        }
        $paramsValue = array("cuenta" => $balanceGeneral[$i]['padre'],
            "cuentaContable" => strtoupper($getCuenta['cuentaContable']),
            "saldo4" => 0.00,
            "saldo3" => ($balanceGeneral[$i]['saldo'] > 0 ? $balanceGeneral[$i]['saldo'] : ($balanceGeneral[$i]['saldo'] * -1)),
            "saldo2" => 0.00,
            "saldo1" => 0.00);
        array_push($params, $paramsValue);
    }
    $paramsValue = array("cuenta" => $balanceGeneral[$i]['cuenta'],
        "cuentaContable" => strtoupper($balanceGeneral[$i]['cuentaContable']),
        "saldo4" => ($saldos[$i] > 0 ? $saldos[$i] : ($saldos[$i] * -1)),
        "saldo3" => 0.00,
        "saldo2" => 0.00,
        "saldo1" => 0.00);
    array_push($params, $paramsValue);
}
$insertBalanceGeneral = $conta->insertBalanceGeneral($params, $_REQUEST['periodo'], $_REQUEST['mes'], $_SESSION['idEmpresa']);
echo json_encode($insertBalanceGeneral);
?>