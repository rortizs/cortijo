<?php
/* CXC - Reporte de Abonos
 * 
 */
session_start();
require_once("../../models/planilla.php");
$planilla = new Planilla();
$getEmpleados = $planilla->getEmpleados($_SESSION['idEmpresa'], $_REQUEST['periodo'], $_REQUEST['mes']);
$getHrmConstructorPlanilla = $planilla->getHrmConstructorPlanilla($_REQUEST['idHrmPlanillas'], $_SESSION['idEmpresa']);
$operadores = array(
    "+", "-", "*", "/"
);
?>
<section class="panel">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table class="table-bordered table-condensed table-hover" id="myTable">
                    <thead>
                        <tr>
                            <?php
                            foreach ($getHrmConstructorPlanilla as $key => $value) {
                                ?>
                                <td><?= $value['nombreCampo']; ?></td>
                                <?php
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($getEmpleados as $key => $value1) {
                            $contador = $key;
                            ?>
                            <tr>
                                <?php
                                foreach ($getHrmConstructorPlanilla as $key => $value2) {
                                    switch ($value2['idTipoCampo']) {
                                        case '1':
                                            ?>
                                            <td class="sabana-<?= $contador; ?>"><?= $value1[$value2['valor']]; ?></td>
                                            <?php
                                            break;
                                        case '2':
                                            ?>
                                            <td class="text-right sabana-<?= $contador; ?> data-<?= $key; ?> dataL<?= $value2['idTipoOperacion']; ?>-<?= $contador; ?>">
                                                <?php
                                                if ($value2['idTipoValor'] === '1') {
                                                    echo number_format($value1[$value2['valor']] ?: $value2['valor'], 2);
                                                } else {
                                                    $valor = $planilla->getOtrosPagosDescuentosPorEmpleado($value1['idHrmEmpleados'], $value2['nombreCampo']);
                                                    echo number_format(($valor ?: '0.00'), 2);
                                                }
                                                ?>
                                            </td>
                                            <?php
                                            break;
                                        case '3':
                                            ?>
                                            <td class="text-right sabana-<?= $contador; ?> data-<?= $key; ?> dataL<?= $value2['idTipoOperacion']; ?>-<?= $contador; ?>">
                                                <?php
                                                $formula = "";
                                                if ($value2['idTipoValor'] === '1') {
                                                    $formula = $value2['valor'];
                                                } else {
                                                    $valor = $planilla->getOtrosPagosDescuentosPorEmpleado($value1['idHrmEmpleados'], $value2['nombreCampo']);
                                                    $formula = $valor;
                                                }
                                                $formulaFinal = 0;
                                                foreach ($getHrmConstructorPlanilla as $key => $value3) {
                                                    if ($value2['valor'] !== $value3['valor']) {
                                                        //echo $value3['valor'] . '<br/>';
                                                        $find = strpos($value2['valor'], $value3['valor']);
                                                        if ($find !== false) {
                                                            //echo 'si esta ' . $value3['valor'] . ' valor: ' . ($value1[$value3['valor']] ?: $value3['valor']) . ' <br/>';
                                                            $formula = str_replace($value3['valor'], floatval(($value1[$value3['valor']] ?: $value3['valor'])), $formula);
                                                        }
                                                    }
                                                }
                                                $formulas = explode(')', str_replace('(', '', $formula));
                                                foreach ($formulas as $formulas2) {
                                                    //echo $formulas2 . '<br/>';
                                                    foreach ($operadores as $operador) {
                                                        $find = strpos($formulas2, $operador);
                                                        if ($find !== false) {
                                                            $formula2 = explode($operador, $formulas2);
                                                            foreach ($formula2 as $keyO => $operaciones) {
                                                                if ($operaciones) {
                                                                    //$formula += floatval($operaciones);
                                                                    switch ($operador) {
                                                                        case '+':
                                                                            $formula += floatval($operaciones);
                                                                            break;
                                                                        case '-':
                                                                            if ($keyO == 0) {
                                                                                $formula += floatval($operaciones);
                                                                            } else {
                                                                                $formula -= floatval($operaciones);
                                                                            }
                                                                            break;
                                                                        case '*':
                                                                            if ($keyO == 0) {
                                                                                $formula += floatval($operaciones);
                                                                            } else {
                                                                                $formula = $formula * $operaciones;
                                                                            }
                                                                            break;
                                                                        case '/':
                                                                            if ($keyO == 0) {
                                                                                $formula += floatval($operaciones);
                                                                            } else {
                                                                                $formula = $formula / $operaciones;
                                                                            }
                                                                            break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                if (intval($value2['valorMaximo']) <= 0) {
                                                    echo number_format(($formula ?: 0), 2);
                                                } else if (floatval($formula) >= floatval($value2['valorMaximo'])) {
                                                    echo number_format($value2['valorMaximo'], 2);
                                                } else {
                                                    echo number_format(($formula ?: 0), 2);
                                                }
                                                ?>
                                            </td>
                                            <?php
                                            break;
                                        case '4':
                                            ?>
                                            <td class="text-right sabana-<?= $contador; ?> data-<?= $key; ?> dataL<?= $value2['idTipoOperacion']; ?>-<?= $contador; ?> resultL<?= $value2['idTipoOperacion']; ?>-<?= $contador; ?> dataLR-<?= $contador; ?>"></td>
                                            <?php
                                            break;
                                        case '5':
                                            ?>
                                            <td class="text-right sabana-<?= $contador; ?> data-<?= $key; ?> dataL<?= $value2['idTipoOperacion']; ?>-<?= $contador; ?> resultL<?= $value2['idTipoOperacion']; ?>-<?= $contador; ?> dataLR-<?= $contador; ?>"></td>
                                            <?php
                                            break;
                                        case '6':
                                            ?>
                                            <td class="text-right sabana-<?= $contador; ?> data-<?= $key; ?> resultLR-<?= $contador; ?>"></td>
                                            <?php
                                            break;
                                    }
                                }
                                ?>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <thead>
                        <tr>
                            <?php
                            foreach ($getHrmConstructorPlanilla as $key => $value) {
                                if ($value['idTipoCampo'] !== '1') {
                                    ?>
                                    <td class="result-<?= $key; ?> text-right">0.00</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>&nbsp;</td>
                                    <?php
                                }
                                ?>
                                <?php
                            }
                            ?>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>