<?php
/**
 * Reporte de Asistencia Mensual
 */
session_start();
require_once ("../../models/login.php");
require_once ("../../models/planilla.php");
$login = new Login();
$planilla = new Planilla();
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];
$idDepartamentos = $_POST['idDepartamentos'];
$getDepartamentos = $planilla->getDepartamentos($_SESSION['idEmpresa']);

//
function lastDayMonth($mes) {
    $month = $mes;
    $year = date('Y');
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('d', mktime(0, 0, 0, $month, $day, $year));
}

//
function addZero($number) {
    $numero = "";
    if (strlen((string) $number) == 1) {
        $numero = "0" . $number;
    } else {
        $numero = $number;
    }
    return $numero;
}

function monthName($number) {
    $name = "";
    switch ($number) {
        case 1 :
            $name = 'Enero';
            break;
        case 2 :
            $name = 'Febrero';
            break;
        case 3 :
            $name = 'Marzo';
            break;
        case 4 :
            $name = 'Abril';
            break;
        case 5 :
            $name = 'Mayo';
            break;
        case 6 :
            $name = 'Junio';
            break;
        case 7 :
            $name = 'Julio';
            break;
        case 8 :
            $name = 'Agosto';
            break;
        case 9 :
            $name = 'Septiembre';
            break;
        case 10 :
            $name = 'Octube';
            break;
        case 11 :
            $name = 'Noviembre';
            break;
        case 12 :
            $name = 'Diciembre';
            break;
    }
    return $name;
}
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Parametros
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <label>Mes</label>
                        <select class="form-control input-sm" id="month">
                            <option value="" selected="">[Seleccione...]</option>
                            <?php
                            for ($i = 1; $i <= 12; $i++) {
                                if (addZero($i) == $_REQUEST['month']) {
                                    ?>
                                    <option value="<?= addZero($i); ?>" selected=""><?= monthName($i); ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= addZero($i); ?>"><?= monthName($i); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label>Año</label>
                        <input type="text" id="year" class="form-control input-sm" value="<?= $_REQUEST['year'] ?: date('Y'); ?>"/>
                    </div>
                    <div class="col-lg-3">
                        <label>Departamento</label>
                        <select class="form-control input-sm" id="idDepartamentos" onchange="loadEmpleados();">
                            <option value="" selected="">[Seleccione...]</option>
                            <?php
                            if ($idDepartamentos == '*') {
                                ?>
                                <option value="*" selected="">Todos los Departamentos</option>
                                <?php
                            } else {
                                ?>
                                <option value="*">Todos los Departamentos</option>
                                <?php
                            }
                            ?>
                            <?php
                            foreach ($getDepartamentos as $key => $value) {
                                if ($value['id'] == $idDepartamentos) {
                                    ?>
                                    <option value="<?= $value['id']; ?>" selected=""><?= strtoupper($value['descripcion']); ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?= $value['id']; ?>"><?= strtoupper($value['descripcion']); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label>Empleados</label>
                        <select class="form-control input-sm" id="idEmpleados">
                            <option value="*">Todos los empleados</option>
                        </select>
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-primary btn-sm" onclick="generarReporteAsistenciasDetalle();">
                            <span class="glyphicon glyphicon-list-alt"></span> Generar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <center>
        <h4>
            Reporte Detalle Asistencia Mensual<br/>
            <small>
                Mes de Reporte: <?= monthName($_REQUEST['month']); ?> - <?= $_REQUEST['year']; ?><br/>
                Departamento(s): <?= $_REQUEST['txtDepartamentos']; ?><br/>
                Empleado(s): <?= $_REQUEST['txtEmpleados']; ?>
            </small>
        </h4>
        <?php
        $lastDayMonth = lastDayMonth($_REQUEST['month']);
        if ($_REQUEST['month'] != '' && $_REQUEST['year'] != '') {
            $params['idHrmDepartamentos'] = $_REQUEST['idDepartamentos'];
            $params['idEmpleados'] = $_REQUEST['idEmpleados'];
            $getEmpleados = $planilla->getHrmEmpleados($params, $_SESSION['idEmpresa']);
            ?>
            <table class="table table-bordered table-striped table-fixed" style="width: 98% !important;">
                <thead>
                    <tr class="info">
                        <td colspan="3"></td>
                        <td class="text-center" colspan="<?= $lastDayMonth; ?>">
                            Mes <?= monthName($_REQUEST['month']); ?>-<?= $_REQUEST['year']; ?>
                        </td>
                    </tr>
                    <tr class="info">
                        <td>Empleado</td>
                        <td>Departamento</td>
                        <td align="center">Dias<br/>Trabajo</td>
                        <?php
                        for ($i = 1; $i <= $lastDayMonth; $i++) {
                            ?>
                            <td><?= $i; ?></td>
                            <?php
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($a = 0; $a < count($getEmpleados); $a++) {
                        $getDiasMarcajesT = $planilla->getDiasMarcajes($getEmpleados[$a]['codigoEmpleado'], $_REQUEST['month'], $_REQUEST['year']);
                        ?>
                        <tr>
                            <td><?= $getEmpleados[$a]['codigoEmpleado']; ?> - <?= strtoupper($getEmpleados[$a]['nombreEmpleado']); ?></td>
                            <td><?= strtoupper($getEmpleados[$a]['departamento']); ?></td>
                            <td><?= $getDiasMarcajesT; ?></td>
                            <?php
                            for ($i = 1; $i <= $lastDayMonth; $i++) {
                                $getTipoMarcajes = $planilla->getTipoMarcajes($getEmpleados[$a]['codigoEmpleado'], date($_REQUEST['year'] . '-' . $_REQUEST['month'] . '-' . addZero($i)));
                                $style = "";
                                switch ($getTipoMarcajes) {
                                    case '2':
                                        $style = '<i class="fa fa-check" style="color:blue !important;" aria-hidden="true"></i>';
                                        break;
                                    case '3':
                                        $style = '<i class="fa fa-check" style="color:green !important;" aria-hidden="true"></i>';
                                        break;
                                    default:
                                        $style = '<i class="fa fa-times" style="color:red !important;" aria-hidden="true"></i>';
                                        break;
                                }
                                ?>
                                <td><?= $style; ?></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </center>
</div>