<?php
session_start();
require_once("../../models/planilla.php");
require_once ("../../models/admin.php");
$planilla = new Planilla();
$admin = new Admin();
$params1['idEmpresas'] = $_SESSION['idEmpresa'];
$getEmpresa = $admin->getEmpresas($params1);
$params2['idEmpleados'] = '*';
$getEmpleados = $planilla->getHrmEmpleados($params2, $_SESSION['idEmpresa']);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Boleta de Pago</title>
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
            }
            table {
                border-collapse: collapse;
                font-size: 0.6em !important;
            }
            .top {
                position:relative !important;
                width:100%;
                top: 0%;
                height: 48% !important;
            }
            .bottom {
                border-top: 1px #000 dashed;
                position:relative !important;
                width:100%;
                height: 48% !important;
                padding-top: 30px !important;
            }
            .detalle{
                position: absolute;
                top: 50%; left: 50%;
                transform: translate(-50%,-50%);
                width: 100% !important;
            }
            .firmaTop{
                position: absolute !important; 
                bottom: 15px !important;
                width: 100% !important;
            }
            .firmaBottom{
                position: absolute !important; 
                bottom: 0 !important;
                width: 100% !important;
            }
            .tipo{
                text-transform: uppercase; 
                font-weight: bold;
                text-align: right !important;
            }
            .firmaEmpleado{
                border-top: 1px solid #000;
                font-weight: bold; 
                text-transform: uppercase; 
                text-align: center;
                font-style: italic !important;
                width: 50% !important;
            }
            .header{
                background-color: #CCC !important;
                -webkit-print-color-adjust: exact; 
                width: 50% !important; 
                font-weight: bold; 
                text-transform: uppercase;
                text-align: center;
            }
            .header-left{
                background-color: #CCC !important;
                -webkit-print-color-adjust: exact;
                font-weight: bold; 
                text-transform: uppercase;
                text-align: left;
            }
            .header-right{
                background-color: #CCC !important;
                -webkit-print-color-adjust: exact;
                font-weight: bold; 
                text-transform: uppercase;
                text-align: right;
            }
            /**/
            @media print {
                body {
                    font-family: Arial, Helvetica, sans-serif;
                }
                table {
                    border-collapse: collapse;
                    font-size: 0.6em !important;
                }
                .top {
                    position:relative !important;
                    width:100%;
                    top: 0%;
                    height: 48% !important;
                }
                .bottom {
                    border-top: 1px #000 dashed;
                    position:relative !important;
                    width:100%;
                    height: 48% !important;
                    padding-top: 30px !important;
                }
                .detalle{
                    position: absolute;
                    top: 50%; left: 50%;
                    transform: translate(-50%,-50%);
                    width: 100% !important;
                }
                .firmaTop{
                    position: absolute !important; 
                    bottom: 15px !important;
                    width: 100% !important;
                }
                .firmaBottom{
                    position: absolute !important; 
                    bottom: 0 !important;
                    width: 100% !important;
                }
                .tipo{
                    text-transform: uppercase; 
                    font-weight: bold;
                    text-align: right !important;
                }
                .firmaEmpleado{
                    border-top: 1px solid #000;
                    font-weight: bold; 
                    text-transform: uppercase; 
                    text-align: center;
                    font-style: italic !important;
                    width: 50% !important;
                }
                .header{
                    background-color: #CCC !important;
                    -webkit-print-color-adjust: exact; 
                    width: 50% !important; 
                    font-weight: bold; 
                    text-transform: uppercase;
                    text-align: center;
                }
                .header-left{
                    background-color: #CCC !important;
                    -webkit-print-color-adjust: exact;
                    font-weight: bold; 
                    text-transform: uppercase;
                    text-align: left;
                }
                .header-right{
                    background-color: #CCC !important;
                    -webkit-print-color-adjust: exact;
                    font-weight: bold; 
                    text-transform: uppercase;
                    text-align: right;
                }
            }
        </style>
    </head>
    <body>
        <?php
        for ($a = 0; $a < count($getEmpleados); $a++) {
            $generacionPlanilla = $planilla->generacionPlanillaBoletas($_REQUEST, $getEmpleados[$a]['idEmpleado'], $_SESSION['idEmpresa']);
            $totalPagado = ($generacionPlanilla[0]['ordinario'] + $generacionPlanilla[0]['bonificacion'] + $generacionPlanilla[0]['bonificacionFija'] + ($generacionPlanilla[0]['horasSimples'] * ($generacionPlanilla[0]['salarioDiarioHE'] / 8) * 1.5) + ($generacionPlanilla[0]['horasDobles'] * ($generacionPlanilla[0]['salarioDiarioHE'] / 8) * 2) + $generacionPlanilla[0]['horasExtMixtas'] + $generacionPlanilla[0]['comisiones'] + $generacionPlanilla[0]['bonoIncentivo'] + $generacionPlanilla[0]['bonoCalidad'] + $generacionPlanilla[0]['bonoProductividad'] + $generacionPlanilla[0]['bonoFlocking'] + $generacionPlanilla[0]['otrosPagos']);            
            $totalPagado2 = ($generacionPlanilla[0]['ordinario'] + ($generacionPlanilla[0]['horasSimples'] * ($generacionPlanilla[0]['salarioDiarioHE'] / 8) * 1.5) + ($generacionPlanilla[0]['horasDobles'] * ($generacionPlanilla[0]['salarioDiarioHE'] / 8) * 2) + $generacionPlanilla[0]['horasExtMixtas']);
            $totalPagado3 = ($generacionPlanilla[0]['bonificacion'] + $generacionPlanilla[0]['bonificacionFija'] + $generacionPlanilla[0]['comisiones'] + $generacionPlanilla[0]['bonoIncentivo'] + $generacionPlanilla[0]['bonoCalidad'] + $generacionPlanilla[0]['bonoProductividad'] + $generacionPlanilla[0]['bonoFlocking'] + $generacionPlanilla[0]['otrosPagos']);
            $seguroSocial = (($totalPagado2) * $generacionPlanilla[0]['seguroSocial']) / 100;
            $isr = 0;
            $isrFormula = ($totalPagado * 24) + ($generacionPlanilla[0]['salarioOrdinario'] * 2) - (($seguroSocial * 24) + $generacionPlanilla[0]['deduccionSinComprobacion'] + ($generacionPlanilla[0]['salarioOrdinario'] * 2));
            if ($isrFormula <= $generacionPlanilla[0]['ingresoSujetosISR']) {
                $isr = (((($isrFormula * $generacionPlanilla[0]['isr']) / 100) / 12) / (30 / $_REQUEST['dias']));
            }
            if ($isr < 1) {
                $isr = 0;
            }
            for ($i = 0; $i < 2; $i++) {
                $tipo = "";
                $divider = "";
                $firma = "";
                if ($i == 0) {
                    $tipo = "Original";
                    $divider = "class='top'";
                    $firma = "class='firmaTop'";
                } else {
                    $tipo = "Copia";
                    $divider = "class='bottom'";
                    $firma = "class='firmaBottom'";
                }
                ?>
                <div <?= $divider; ?>>
                    <table width="100%">
                        <tr>
                            <td><b>Empresa:</b> <?= $getEmpresa[0]['nombreComercial']; ?></td>
                            <td align="right"><b>Cod. Empleado:</b> <?= $getEmpleados[$a]['codigoEmpleado']; ?></td>
                        </tr>
                        <tr>
                            <td><b>Nit. Empresa:</b> <?= $getEmpresa[0]['nit']; ?></td>
                            <td align="right"><b>Período de Pago:</b> <?= $_REQUEST['fechaInicio']; ?> al <?= $_REQUEST['fechaFin']; ?></td>
                        </tr>
                        <tr>
                            <td><b>Nombre:</b> <?= $getEmpleados[$a]['nombreEmpleado']; ?></td>
                            <td align="right"><b>Dias Trabajados:</b> <?= $_REQUEST['dias']; ?></td>
                        </tr>
                        <tr>
                            <td><b>Puesto:</b> - </td>
                            <td align="right"><b>Dias Vacaciones:</b> 0</td>
                        </tr>
                        <tr>
                            <td><b>Departamento:</b> <?= $getEmpleados[$a]['departamento']; ?></td>
                            <td align="right"><b>Nit Empleado:</b> <?= $getEmpleados[$a]['noTributario']; ?></td>
                        </tr>
                        <tr>
                            <td><b>Fecha Ingreso:</b> <?= $getEmpleados[$a]['fechaIngreso']; ?></td>
                            <td></td>
                        </tr>
                    </table>
                    <div class="detalle">
                        <table width="100%" border="1">
                            <tr>
                                <td colspan="4" class="header">Ingresos</td>
                                <td colspan="2" class="header">Descuentos</td>
                            </tr>
                            <tr>
                                <td class="header-left">Descripcion</td>
                                <td class="header-left">Horas</td>
                                <td class="header-left">Valor</td>
                                <td class="header-left">Total</td>
                                <td class="header-left">Descripcion</td>
                                <td class="header-left">Valor</td>
                            </tr>
                            <tr>
                                <td style="text-transform: uppercase;">Sueldo Ordinario</td>
                                <td style="text-transform: uppercase;"></td>
                                <td style="text-transform: uppercase;"></td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format($generacionPlanilla[0]['ordinario'], 2); ?></td>
                                <td style="text-transform: uppercase;">IGSS</td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format($seguroSocial, 2); ?></td>
                            </tr>
                            <tr>
                                <td style="text-transform: uppercase;">bonificacion decreto</td>
                                <td style="text-transform: uppercase; text-align: center;"></td>
                                <td style="text-transform: uppercase; text-align: right;"></td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format($totalPagado3, 2); ?></td>
                                <td style="text-transform: uppercase;">ISR</td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format($isr, 2); ?></td>
                            </tr>
                            <tr>
                                <td style="text-transform: uppercase;">Hrs. Extras Diurnas</td>
                                <td style="text-transform: uppercase; text-align: center;"><?= number_format($generacionPlanilla[0]['horasSimples'], 2); ?></td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format((($generacionPlanilla[0]['salarioDiarioHE'] / 8) * 1.5), 2); ?></td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format(($generacionPlanilla[0]['horasSimples'] * ($generacionPlanilla[0]['salarioDiarioHE'] / 8) * 1.5), 2); ?></td>
                                <td style="text-transform: uppercase;">Prestamos</td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format($generacionPlanilla[0]['prestamos'], 2); ?></td>
                            </tr>
                            <tr>
                                <td style="text-transform: uppercase;">Hrs. Extras Nocturnas</td>
                                <td style="text-transform: uppercase; text-align: center;"><?= number_format($generacionPlanilla[0]['horasDobles'], 2); ?></td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format((($generacionPlanilla[0]['salarioDiarioHE'] / 8) * 2), 2); ?></td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format(($generacionPlanilla[0]['horasDobles'] * ($generacionPlanilla[0]['salarioDiarioHE'] / 8) * 2), 2); ?></td>
                                <td style="text-transform: uppercase;">Anticipos</td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format($generacionPlanilla[0]['anticipos'], 2); ?></td>
                            </tr>
                            <tr>
                                <td style="text-transform: uppercase;"></td>
                                <td style="text-transform: uppercase;"></td>
                                <td style="text-transform: uppercase;"></td>
                                <td style="text-transform: uppercase;"></td>
                                <td style="text-transform: uppercase;">Otros Descuentos</td>
                                <td style="text-transform: uppercase; text-align: right;"><?= number_format($generacionPlanilla[0]['otrosDescuentos'], 2); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="header-left">Total</td>
                                <td class="header-right"><?= number_format($totalPagado, 2); ?></td>
                                <td class="header-left">Total</td>
                                <td class="header-right"><?= number_format(($seguroSocial + $isr + $generacionPlanilla[0]['prestamos'] + $generacionPlanilla[0]['anticipos'] + $generacionPlanilla[0]['otrosDescuentos']), 2); ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="header">Liquido a Recibir</td>
                                <td class="header-right"><?= number_format(($totalPagado - ($seguroSocial + $isr + $generacionPlanilla[0]['prestamos'] + $generacionPlanilla[0]['anticipos'] + $generacionPlanilla[0]['otrosDescuentos'])), 2); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div <?= $firma; ?>>
                        <table width="100%">
                            <tr>
                                <td class="firmaEmpleado">Firma Empleado</td>
                                <td class="tipo"><?= $tipo; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }
            ?>
            <div style="page-break-after:always;"></div>
            <?php
        }
        ?>
        <script type="text/javascript">
            this.print();
        </script>
    </body>
</html>