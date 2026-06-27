<?php

/**
 * Description of admin
 *
 * @author jjuarez
 */
require_once ("dbCon.php");
require_once ("general.php");

class agenciasViajes extends General {

    /** METODO insertAgenciasViajes
     *
     */
    public function insertAgenciasViajes($params) {
        $response = "";
        $sql = "insert into agenciasViajes values(null,'" . $params['fileName'] . "','" . $params['gua'] . "','" . $params['reserva'] . "','" . $params['fecha'] . "','" . $params['codigoLineaArea'] . "','" . $params['lineaArea'] . "','" . $params['boleto'] . "','" . $params['pasajero'] . "','" . $params['nit'] . "','" . $params['nombre'] . "','" . $params['direccion'] . "','" . $params['montoDolares'] . "','" . $params['montoSinImpuestos'] . "','" . $params['totalImpuestos'] . "','" . $params['montoTotal'] . "','" . $params['tasaCambio'] . "','" . $params['itinerario'] . "','" . $params['compTarifa'] . "','" . $params['endosos'] . "','" . $params['formaPago'] . "','" . $params['codigoAuth'] . "','" . $params['estatusBoleto'] . "','" . $params['codVendedor'] . "','" . $params['codCliente'] . "','" . $params['codAgencia'] . "','1',null,null);";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //$this->insertAgenciasViajesDetalle(mysql_insert_id(), $params['impuestos'], $params['noPasajeros'], $params['tickets']);
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => $error);
        }
        return $response;
    }

    /** METODO insertAgenciasViajesDetalle
     * 
     */
    public function insertAgenciasViajesDetalle($idBoleto, $detalle, $noPasajeros, $boletos) {
        $tickets = explode(',', $boletos);
        for ($a = 0; $a < $noPasajeros; $a++) {
            if (count($detalle) != 0) {
                $numero = count($detalle);
                $i = 1;
                $sql = "insert into agenciasViajesDetalle values";
                foreach ($detalle as $key => $value) {
                    if ($i !== $numero) {
                        if ($key == 0) {
                            $sql .= "(null," . $idBoleto . ",'Costo Boleto No." . $tickets[$a] . "','" . $value . "'),";
                        } else {
                            $sql .= "(null," . $idBoleto . ",'Impuesto " . trim(preg_replace('/[0-9.]+/', '', str_replace(' ', '', $value))) . " Boleto No. " . $tickets[$a] . "','" . $value . "'),";
                        }
                    } else {
                        $sql .= "(null," . $idBoleto . ",'Impuesto " . trim(preg_replace('/[0-9.]+/', '', str_replace(' ', '', $value))) . " Boleto No. " . $tickets[$a] . "','" . $value . "')";
                    }
                    $i ++;
                }
                $query = mysql_query($sql, dbCon::conPrincipal());
                if ($query == true) {
                    error_log('insert true detalle boleto # ' . $idBoleto);
                } else {
                    error_log('error sql: ' . $sql);
                }
            }
        }
    }

    /** METODO RETURN DATE BY FILE AMADEUS
     * 
     */
    public function returnDateAmadeus($fileName) {
        $month = explode('.', $fileName);
        $d = substr($fileName, 1, 2);
        $m = "";
        switch ($month[1]) {
            case 'JAN':
                $m = "01";
                break;
            case 'FEB':
                $m = "02";
                break;
            case 'MAR':
                $m = "03";
                break;
            case 'APR':
                $m = "04";
                break;
            case 'MAY':
                $m = "05";
                break;
            case 'JUN':
                $m = "06";
                break;
            case 'JUL':
                $m = "07";
                break;
            case 'AUG':
                $m = "08";
                break;
            case 'SEP':
                $m = "09";
                break;
            case 'OCT':
                $m = "10";
                break;
            case 'NOV':
                $m = "11";
                break;
            case 'DEC':
                $m = "12";
                break;
        }
        $fecha = date('Y-' . $m . '-' . $d);
        return $fecha;
    }

    /** METODO GET BOLETO
     * 
     */
    public function getBoleto($params) {
        $where = "";
        if ($params['idBoleto']) {
            $where = " av.id= '" . $params['idBoleto'] . "'";
        } else {
            $where = "av.reserva='" . $params['noReserva'] . "' "
                    . "and (av.idVentas is null or av.idVentas=0) "
                    . "and not av.id in (select idProducto from ventasDetalle WHERE idVentas=0)";
        }
        $this->resultado = null;
        $sql = "select av.*,c.valorFee,c.id as idCliente, c.nitC as nitCliente, c.nombreC as nombreCliente from agenciasViajes av 
left join clientes c on c.codigo =  av.codCliente 
left join ventasDetalle vd on av.boleto= vd.idProducto
inner join empresas e on e.codAgencia= av.codAgencia where  " . $where;
         //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO GET DETALLE BOLETO
     *
     */
    public function getDetalleBoleto($params) {
        $this->resultado = null;
        $sql = "select av.*,SUBSTR(`av`.`codigoLineaArea`, 1, (LENGTH(`av`.`codigoLineaArea`) - 1)) as codigoLineaAerea2,v.fee as valorFee,c.id as idCliente,v.total,
                c.nombreC as nombreCliente, c.nitC as nitCliente, c.direccionC as direccionCliente
                from agenciasViajes av left join clientes c
                on c.codigo =  av.codCliente inner join empresas e on e.codAgencia= av.codAgencia 
                inner join ventasDetalle as v on v.idProducto= av.id
                where v.idVentas=0 and v.idUsuarios=" . $params['idUsuarios'] . " and v.idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO AGREGAR BOLETO AL DETALLE
     *
     */

    /** METODO AGREGAR BOLETO AL DETALLE
     *
     */
    public function agregarBoletoDetalle($params) {

        $sql = "INSERT INTO ventasDetalle(idventas,idProducto,fee,precio,total,idUsuarios,idSucursales,idEmpresas)"
                . " values('0','" . $params['noBoleto'] . "','" . $params['fee'] . "','" . $params['montoTotal'] . "','" . ($params['montoTotal'] + ($params['tasaCambio'] * $params['fee']))
                . "', '" . $params['idUsuarios'] . "', '" . $params['idSucursales'] . "', '" . $params['idEmpresas'] . "') ";
        echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed insert', 'Query' => $sql);
        }
        return $response;
    }

    /** METODO ELIMINAR BOLETO AL DETALLE
     *
     */
    public function eliminarBoletoDetalle($params) {
        $response = "";
        $sql = "delete from ventasDetalle "
                . "where idVentas=0 and idProducto=" . $params['boleto'] . " and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
        // echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'Query' => $sql);
        }
        return $response;
    }

    public function updateFeeDetalle($params) {
        $sql = "UPDATE ventasDetalle SET fee=" . $params['fee'] . ",total=" . ($params['montoTotal'] + ($params['tasaCambio'] * $params['fee'])) . " WHERE idProducto=" . $params['id'] . " and idVentas=0 and idUsuarios=" . $params['idUsuarios'] . " and idSucursales=" . $params['idSucursales'] . " and idEmpresas=" . $params['idEmpresas'] . "";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //$this->insertAgenciasViajesDetalle(mysql_insert_id(), $params['impuestos'], $params['noPasajeros'], $params['tickets']);
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => $error);
        }
        return $response;
    }

    public function updateAgenciasViajes($idFactura, $idEmpresas) {
        $sql = "UPDATE agenciasViajes SET idVentas=0 WHERE idVentas=" . $idFactura . "";
        //echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //$this->insertAgenciasViajesDetalle(mysql_insert_id(), $params['impuestos'], $params['noPasajeros'], $params['tickets']);
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => $error);
        }
        return $response;
    }

    public function updateBoleto($params) {
        $sql = "UPDATE agenciasViajes SET codVendedor=".$params['codVendedor'].", codCliente=".$params['codCliente']." WHERE id=" . $params['idBoleto'] . "";
       // echo $sql . "<br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            //$this->insertAgenciasViajesDetalle(mysql_insert_id(), $params['impuestos'], $params['noPasajeros'], $params['tickets']);
            $response[] = array('message' => 'success');
        } else {
            $error = mysql_error();
            $response[] = array('message' => $error);
        }
        return $response;
    }

}
