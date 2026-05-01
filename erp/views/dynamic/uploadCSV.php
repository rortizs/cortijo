<?php

session_start();
require_once ("../../models/planilla.php");
require_once ("../../models/admin.php");
require_once ("../../models/inventarios.php");
require_once ("../../models/caja.php");
$planilla = new Planilla();
$admin = new Admin();
$inventarios = new Inventarios();
$caja = new Caja();
switch ($_REQUEST['service']) {
    case 'cargarPlantilla' :
        $row = 1;
        $response = "";
        if (($handle = fopen($_FILES['file']['tmp_name'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                if ($row != 1) {
                    //get Id Empleado
                    $params['idEmpleados'] = $data[0];
                    $idEmpleado = $planilla->getHrmEmpleados($params, $_SESSION['idEmpresa']);
                    if ($idEmpleado[0]['idEmpleado'] != '') {
                        switch ($_REQUEST['modulo']) {
                            case 'vw_hrmHorasExtras':
                                $response[] = array(
                                    'idEmpleado' => $idEmpleado[0]['idEmpleado'],
                                    'codigo' => $data[0],
                                    'init_at' => $planilla->dateFormat($data[1]),
                                    'end_at' => $planilla->dateFormat($data[2]),
                                    'horasDiurnas' => $data[3],
                                    'horasNocturas' => $data[4],
                                    'horasMixtas' => $data[5]
                                );
                                break;
                            case 'vw_hrmComisiones':
                                $response[] = array(
                                    'idEmpleado' => $idEmpleado[0]['idEmpleado'],
                                    'codigo' => $data[0],
                                    'periodo' => $data[1],
                                    'mes' => $data[2],
                                    'valor' => $data[3],
                                    'observaciones' => $data[4]
                                );
                                break;
                            case 'vw_hrmOtrosPagosDescuentos':
                                $response[] = array(
                                    'idEmpleado' => $idEmpleado[0]['idEmpleado'],
                                    'codigo' => $data[0],
                                    'init_at' => $planilla->dateFormat($data[1]),
                                    'end_at' => $planilla->dateFormat($data[2]),
                                    'idHrmTipoPago' => $data[3],
                                    'descripcion' => $data[4],
                                    'valor' => $data[5]
                                );
                                break;
                            case 'vw_hrmPrestamos':
                                $response[] = array(
                                    'idEmpleado' => $idEmpleado[0]['idEmpleado'],
                                    'codigo' => $data[0],
                                    'init_at' => $planilla->dateFormat($data[1]),
                                    'end_at' => $planilla->dateFormat($data[2]),
                                    'valor' => $data[3]
                                );
                                break;
                            case 'vw_hrmAnticipos':
                                $response[] = array(
                                    'idEmpleado' => $idEmpleado[0]['idEmpleado'],
                                    'codigo' => $data[0],
                                    'init_at' => $planilla->dateFormat($data[1]),
                                    'end_at' => $planilla->dateFormat($data[2]),
                                    'valor' => $data[3]
                                );
                                break;
                            case 'vw_hrmPoliticasPago':
                                $getComisionPolitica = $planilla->getComisionPolitica($idEmpleado[0]['idEmpleado'], $data[3]);
                                $response[] = array(
                                    'idEmpleado' => $idEmpleado[0]['idEmpleado'],
                                    'codigo' => $data[0],
                                    'init_at' => $planilla->dateFormat($data[1]),
                                    'end_at' => $planilla->dateFormat($data[2]),
                                    'cantidad' => $data[3],
                                    'valor' => $getComisionPolitica
                                );
                                break;
                        }
                    }
                }
                $row++;
            }
            fclose($handle);
            $cargaInfoPlantilla = $planilla->cargaInfoPlantilla($response, $_REQUEST['modulo'], $_SESSION['idEmpresa']);
            echo json_encode($cargaInfoPlantilla);
        }
        break;
    case 'cargarPedidosDetalle' :
        $row = 1;
        if (($handle = fopen($_FILES['file']['tmp_name'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                if ($row != 1) {
                    $params['codigo'] = trim($data[0]);
                    $params['ingresoA'] = $_REQUEST['ingresoA'];
                    $params['idPuntoIngreso'] = $_REQUEST['idPuntoIngreso'];
                    $getProducto = $inventarios->loadProductoByCodigo($params, $_SESSION['idEmpresa']);
                    if ($getProducto[0]['idProductos'] != '') {
                        $params2['idUsuarios'] = $_SESSION['idUsuarios'];
                        $params2['idSucursales'] = $_SESSION['idSucursalesS'];
                        $params2['idEmpresas'] = $_SESSION['idEmpresa'] ?: $_SESSION['idEmpresas'];
                        $params2['tipoProducto'] = $getProducto[0]['idTipoProductos'];
                        $params2['idProducto'] = $getProducto[0]['idProductos'];
                        $params2['cantidad'] = trim($data[2]);
                        $params2['precio'] = $getProducto[0]['precioPublico'];
                        $params2['total'] = (trim($data[2]) * $getProducto[0]['precioPublico']);
                        $params2['ingresoA'] = $_REQUEST['ingresoA'];
                        $params2['idPuntoIngreso'] = $_REQUEST['idPuntoIngreso'];
                        $params2['valExistencias'] = $_REQUEST['valExistencias'];
                        $params2['sku'] = $getProducto[0]['sku'];
                        $process = $caja->agregarProductoPedido($params2);
                    }
                }
                $row++;
            }
            fclose($handle);
        }
        break;
}
