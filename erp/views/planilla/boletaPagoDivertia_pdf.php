<?php
session_start();
require_once("../../models/NumberToLetterConverter.class.php");
require_once("../../models/planilla.php");
require_once ("../../models/admin.php");
$converter = new NumberToLetterConverter();
$planilla = new Planilla();
$admin = new Admin();
$cantidad = "0.00";
$number = explode(".", $cantidad);
$toWord = $converter->to_word($number[0]);
$params1['idEmpresas'] = $_SESSION['idEmpresa'];
$getEmpresa = $admin->getEmpresas($params1);
$getHrmDepartamentos = $planilla->getHrmDepartamentos($_SESSION['idEmpresa']);
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
            .empresa {
                font-size: 14px !important;
                font-weight: bold;
                text-transform: uppercase;
            }
            .header{
                font-weight: bold;
                text-transform: uppercase;
            }
            .uppercase{
                text-transform: uppercase;
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
                .empresa {
                    font-size: 14px !important;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .header{
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .uppercase{
                    text-transform: uppercase;
                }
            }
        </style>
    </head>
    <body>
        <?php
        foreach ($getHrmDepartamentos as $key => $value) {
            $tipoPlanilla = "";
            $periodo = "";
            $observaciones = "";
            $_REQUEST['fechaInicio'] = "";
            $_REQUEST['fechaFin'] = "";
            $cols = "16";
            switch ($_REQUEST['tipoPlanilla']) {
                case '1':
                    $tipoPlanilla = $_REQUEST['tipoPlanillaTXT'];
                    $periodo = "Del 01 al 15 " . $_REQUEST['monthTXT'] . " - " . $_REQUEST['yearTXT'];
                    $observaciones = $_REQUEST['observaciones'];
                    $_REQUEST['fechaInicio'] = date('01-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                    $_REQUEST['fechaFin'] = date('15-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                    $cols = "17";
                    break;
                case '2':
                    $tipoPlanilla = $_REQUEST['tipoPlanillaTXT'];
                    $periodo = "Del 01 al 15 " . $_REQUEST['monthTXT'] . " - " . $_REQUEST['yearTXT'];
                    $observaciones = $_REQUEST['observaciones'];
                    $_REQUEST['fechaInicio'] = date('01-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                    $_REQUEST['fechaFin'] = date('15-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                    break;
                case '3':
                    $tipoPlanilla = $_REQUEST['tipoPlanillaTXT'];
                    $periodo = "Del 16 al 30 " . $_REQUEST['monthTXT'] . " - " . $_REQUEST['yearTXT'];
                    $observaciones = $_REQUEST['observaciones'];
                    $_REQUEST['fechaInicio'] = date('16-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                    $_REQUEST['fechaFin'] = date('30-' . $_REQUEST['month'] . '-' . $_REQUEST['yearTXT'] . '');
                    $cols = "25";
                    break;
            }
            $generacionPlanilla = $planilla->generacionPlanilla($_REQUEST, $value['descripcion'], $_SESSION['idEmpresa']);
            foreach ($generacionPlanilla as $key2 => $value2) {
                ?>
                <table width="100%">
                    <tr>
                        <td align="center" class="empresa"><?= $getEmpresa[0]['nombreComercial']; ?></td>
                        <td class="empresa" align="right"><?= $getEmpresa[0]['nit']; ?></td>
                    </tr>
                    <tr>
                        <td align="center">RECIBO DE PAGO DE SUELDOS</td>
                    </tr>
                </table>
                <div style="border: 1px solid #000; padding: 5px !important; border-radius: 5px !important;">
                    <table width="100%">
                        <tr>
                            <td class="header">Empleado:</td>
                            <td>-</td>
                            <td align="right" class="header">NIT Empleado:</td>
                            <td align="right">-</td>
                        </tr>
                        <tr>
                            <td class="header">Puesto:</td>
                            <td>-</td>
                            <td class="header">Departamento:</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td class="header">Recibi de:</td>
                            <td colspan="3">-</td>
                        </tr>
                        <tr>
                            <td class="header">La cantidad de:</td>
                            <td colspan="3">-</td>
                        </tr>
                        <tr>
                            <td class="header">Concepto:</td>
                            <td>-</td>
                            <td colspan="2">d-m-Y al d-m-Y</td>
                        </tr>
                    </table>
                </div>
                <br/>
                <table width="100%">
                    <thead>
                        <tr>
                            <td class="header">Concepto</td>
                            <td class="header" align="right">ingresos</td>
                            <td class="header" align="right">descuentos</td>
                            <td class="header" align="right">cantidad</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="uppercase">Ordinario</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">bonificación incentivo</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">total de horas simples</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">total de horas dobles</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">vacaciones</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">otros ingresos no afectos</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">iggs laboral</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">i.s.r</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">anticipos</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="uppercase">otros descuentos</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td align="right">0</td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="header" align="center">totales</td>
                            <td align="right">0.00</td>
                            <td align="right">0.00</td>
                            <td class="header" align="center">liquido a <br/>recibir</td>
                            <td class="header" align="right"><?= number_format($cantidad, 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" class="header">Total en letras: <?= $toWord; ?> con <?= $number[1] . '/100'; ?> ctvs.</td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center" class="header">
                                Revisado<br/>
                                Admitivo-financiero<br/>
                                Autorizado segun nómina No. {YYYYMM}<br/>
                                Depositado en Cuenta Depósito Monetario<br/>
                                No. {Cuenta}
                            </td>
                            <td colspan="2" align="center" class="header">
                                total<br/>
                                recibido en<br/>
                                el mes 
                            </td>
                            <td>
                                0.00
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="3" style="border-bottom: 1px solid #000;">(F:)</td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" class="header">
                                <br/>
                                * Bonificación incentivo según decreto 78-89 en Art 1-2,se establece como pago conforme a una evaluación que se realiza a los
                                empleados de manera mensual.
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div style="page-break-after:always;"></div>
                <?php
            }
        }
        ?>
        <script type="text/javascript">
            this.print();
        </script>
    </body>
</html>