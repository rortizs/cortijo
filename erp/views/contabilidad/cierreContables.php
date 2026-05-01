<?php
/* CONTABILIDAD - DIARIO
 * 
 */
session_start();
require_once("../../models/admin.php");
require_once("../../models/contabilidad.php");
$admin = new Admin();
$conta = new Contabilidad();
$getAnos = $admin->getAnos();
$getMeses = $admin->getMeses();
?>
<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
        <section class="panel">
            <header class="panel-heading">
                Filtros Proceso de Cierre
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <label>Periodo</label>
                        <select id="periodo" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getAnos as $key => $value) {
                                if ($value['descripcion'] == $_POST['year'] ? : date('Y')) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label>Mes</label>
                        <select id="mes" class="form-control input-sm">
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($getMeses as $key => $value) {
                                if ($value['number'] == $_POST['month']) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= $value['descripcion']; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= $value['descripcion']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        &nbsp;<br/>
                        <button class="btn btn-info btn-sm" onclick="procesarCierreContable();">
                            <i class="fa fa-check"></i> Procesar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
if ($_REQUEST['periodo'] != '' && $_REQUEST['mes'] != '') {
    // 3. ESTADO DE RESULTADOS
    $_REQUEST['tipo'] = 1;
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
                if($getCuenta2['nivel']=='1'){
                $paramsValue = array("cuenta" => $getCuenta['padre'],
                    "cuentaContable" => strtoupper($getCuenta2['cuentaContable']),
                    "saldo4" => 0.00,
                    "saldo3" => 0.00,
                    "saldo2" => 0.00,
                    "saldo1" => ($estadoResultados[$i]['saldo'] > 0 ? $estadoResultados[$i]['saldo'] : ($estadoResultados[$i]['saldo'] * -1))   );
                array_push($params, $paramsValue);
                }
                else{                
                $paramsValue = array("cuenta" => $getCuenta['padre'],
                    "cuentaContable" => strtoupper($getCuenta2['cuentaContable']),
                    "saldo4" => 0.00,
                    "saldo3" => 0.00,
                    "saldo2" => ($estadoResultados[$i]['saldo'] > 0 ? $estadoResultados[$i]['saldo'] : ($estadoResultados[$i]['saldo'] * -1)),
                    "saldo1" => 0.00);
                array_push($params, $paramsValue);       
                }
            }
              if($getCuenta['nivel']=='2'){
            $paramsValue = array("cuenta" => $estadoResultados[$i]['padre'],
                "cuentaContable" => strtoupper($getCuenta['cuentaContable']),
                "saldo4" => 0.00,
                "saldo3" => 0,00,
                "saldo2" => ($estadoResultados[$i]['saldo'] > 0 ? $estadoResultados[$i]['saldo'] : ($estadoResultados[$i]['saldo'] * -1)),
                "saldo1" => 0.00);
            array_push($params, $paramsValue);
              }
              else {
            $paramsValue = array("cuenta" => $estadoResultados[$i]['padre'],
                "cuentaContable" => strtoupper($getCuenta['cuentaContable']),
                "saldo4" => 0.00,
                "saldo3" => ($estadoResultados[$i]['saldo'] > 0 ? $estadoResultados[$i]['saldo'] : ($estadoResultados[$i]['saldo'] * -1)),
                "saldo2" => 0.00,
                "saldo1" => 0.00);
            array_push($params, $paramsValue);
              }
        }
        if($estadoResultados[$i]['nivel']=='3')
        {
        $paramsValue = array("cuenta" => $estadoResultados[$i]['cuenta'],
            "cuentaContable" => strtoupper($estadoResultados[$i]['cuentaContable']),
            "saldo4" => 0.00,
            "saldo3" => ($saldos[$i] > 0 ? $saldos[$i] : ($saldos[$i] * -1)),
            "saldo2" => 0.00,
            "saldo1" => 0.00);
        array_push($params, $paramsValue);
        }
        else if($estadoResultados[$i]['nivel']=='2')
        {
        $paramsValue = array("cuenta" => $estadoResultados[$i]['cuenta'],
            "cuentaContable" => strtoupper($estadoResultados[$i]['cuentaContable']),
            "saldo4" => 0.00,
            "saldo3" => 0.00,
            "saldo2" => ($saldos[$i] > 0 ? $saldos[$i] : ($saldos[$i] * -1)),
            "saldo1" => 0.00);
        array_push($params, $paramsValue);
        }
        else
        {
        $paramsValue = array("cuenta" => $estadoResultados[$i]['cuenta'],
            "cuentaContable" => strtoupper($estadoResultados[$i]['cuentaContable']),
            "saldo4" => ($saldos[$i] > 0 ? $saldos[$i] : ($saldos[$i] * -1)),
            "saldo3" => 0.00,
            "saldo2" => 0.00,
            "saldo1" => 0.00);
        array_push($params, $paramsValue);
        }
    }
    $insertEstadoResultados = $conta->insertEstadoResultados($params, $_REQUEST['periodo'], $_REQUEST['mes'], $_SESSION['idEmpresa'], $_REQUEST['mesTxt']);
    echo json_encode($insertEstadoResultados);
    echo '<hr/>';
// 4. BALANCE GENERAL
    $_REQUEST['tipo'] = 2;
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
                if ($getCuenta2['nivel'] == '1') {
                    $paramsValue = array("cuenta" => $getCuenta['padre'],
                        "cuentaContable" => strtoupper($getCuenta2['cuentaContable']),
                        "saldo4" => 0.00,
                        "saldo3" => 0.00,
                        "saldo2" => 0.00,
                        "saldo1" => ($balanceGeneral[$i]['saldo'] > 0 ? $balanceGeneral[$i]['saldo'] : ($balanceGeneral[$i]['saldo'] * -1)));
                    array_push($params, $paramsValue);
                } else {
                    $paramsValue = array("cuenta" => $getCuenta['padre'],
                        "cuentaContable" => strtoupper($getCuenta2['cuentaContable']),
                        "saldo4" => 0.00,
                        "saldo3" => 0.00,
                        "saldo2" => ($balanceGeneral[$i]['saldo'] > 0 ? $balanceGeneral[$i]['saldo'] : ($balanceGeneral[$i]['saldo'] * -1)),
                        "saldo1" => 0.00);
                    array_push($params, $paramsValue);
                }
            }

            if ($getCuenta['nivel'] == '2') {
                echo $getCuenta['nivel'];
                $paramsValue = array("cuenta" => $balanceGeneral[$i]['padre'],
                    "cuentaContable" => strtoupper($getCuenta['cuentaContable']),
                    "saldo4" => 0.00,
                    "saldo3" => 0.00,
                    "saldo2" => ($balanceGeneral[$i]['saldo'] > 0 ? $balanceGeneral[$i]['saldo'] : ($balanceGeneral[$i]['saldo'] * -1)),
                    "saldo1" => 0.00);
                array_push($params, $paramsValue);
            } else {
                $paramsValue = array("cuenta" => $balanceGeneral[$i]['padre'],
                    "cuentaContable" => strtoupper($getCuenta['cuentaContable']),
                    "saldo4" => 0.00,
                    "saldo3" => ($balanceGeneral[$i]['saldo'] > 0 ? $balanceGeneral[$i]['saldo'] : ($balanceGeneral[$i]['saldo'] * -1)),
                    "saldo2" => 0.00,
                    "saldo1" => 0.00);
                array_push($params, $paramsValue);
            }
        }
        
        if($balanceGeneral[$i]['nivel']==3){
        $paramsValue = array("cuenta" => $balanceGeneral[$i]['cuenta'],
            "cuentaContable" => strtoupper($balanceGeneral[$i]['cuentaContable']),
            "saldo4" => 0.00,
            "saldo3" => ($saldos[$i] > 0 ? $saldos[$i] : ($saldos[$i] * -1)),
            "saldo2" => 0.00,
            "saldo1" => 0.00);
        array_push($params, $paramsValue);
        }
        else if($balanceGeneral[$i]['nivel']==2){
        $paramsValue = array("cuenta" => $balanceGeneral[$i]['cuenta'],
            "cuentaContable" => strtoupper($balanceGeneral[$i]['cuentaContable']),
            "saldo4" => 0.00,
            "saldo3" => 0.00,
            "saldo2" => ($saldos[$i] > 0 ? $saldos[$i] : ($saldos[$i] * -1)),
            "saldo1" => 0.00);
        array_push($params, $paramsValue);
        }
        else{
        $paramsValue = array("cuenta" => $balanceGeneral[$i]['cuenta'],
            "cuentaContable" => strtoupper($balanceGeneral[$i]['cuentaContable']),
            "saldo4" => ($saldos[$i] > 0 ? $saldos[$i] : ($saldos[$i] * -1)),
            "saldo3" => 0.00,
            "saldo2" => 0.00,
            "saldo1" => 0.00);
        array_push($params, $paramsValue);
        }
    }
}


$insertBalanceGeneral = $conta->insertBalanceGeneral($params, $_REQUEST['periodo'], $_REQUEST['mes'], $_SESSION['idEmpresa'], $_REQUEST['mesTxt']);
echo json_encode($insertBalanceGeneral);
?>
