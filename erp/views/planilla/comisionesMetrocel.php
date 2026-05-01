<?php

session_start();
header('Content-Type: text/html; charset=utf-8');
require_once("../../models/librerias/excel/simplexlsx.class.php");
require_once("../../models/planilla.php");
$planilla = new Planilla();
$data = SimpleXLSX::parse('sabanaComisionesOctubreEscuintla2.xlsx');
if ($data) {
    foreach ($data->rows() as $key => $value) {
        if ($key !== 0) {
            if ($value['12'] !== 'sin asignar' && $value['12'] !== '') {
                $codCoordinador = "";
                $coordinador = "";
                $codVendedor = "";
                $vendedor = "";
                $tipoComision = "";
                $status = "";
                $semana = "";
                $fecha = "";
                $idHrmPlanillas = "";
                switch ($_REQUEST['planilla']) {
                    case '1':
                        $codCoordinador = strtoupper($value['11']);
                        $coordinador = strtoupper($value['12']);
                        $codVendedor = strtoupper($value['14']);
                        $vendedor = strtoupper($value['13']);
                        $tipoComision = number_format($value['19'], 2);
                        $status = $value['16'];
                        $semana = $value['0'];
                        $fecha = $value['2'];
                        $idHrmPlanillas = $_REQUEST['planilla'];
                        break;
                    case '2':
                        $codCoordinador = strtoupper($value['12']);
                        $coordinador = strtoupper($value['13']);
                        $codVendedor = strtoupper($value['15']);
                        $vendedor = strtoupper($value['21']);
                        $tipoComision = number_format($value['24'], 2);
                        $status = $value['22'];
                        $semana = 1;
                        $fecha = $value['0'];
                        $idHrmPlanillas = $_REQUEST['planilla'];
                        break;
                }
                //echo $key . " - " . $coordinador . " - " . $vendedor . "-" . $tipoComision . "-" . $status . "-" . $semana . "-" . $fecha . "-" . $planilla . "<br/>";
                $cargaComisiones = $planilla->cargaComisiones($codCoordinador, $coordinador, $codVendedor, $vendedor, $tipoComision, $status, $semana, $fecha, $idHrmPlanillas);
                echo json_encode($cargaComisiones) . "<br/>";
            }
        }
    }
    //echo $contador;
} else {
    echo SimpleXLSX::parse_error();
}
?>