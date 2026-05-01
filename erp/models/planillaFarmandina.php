<?php

/**
 * Description of planilla
 *
 * @author Richard Sasvin
 */
require_once ("dbCon.php");
require_once ("general.php");

class PlanillaFarmandina extends General {

    public function nominaCR($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.codigoEmpleado,
                    concat(a.primerNombre,' ',a.segundoNombre,' ',a.primerApellido,' ',a.segundoApellido) as empleado,
                    b.descripcion as cargo,
                    a.salarioOrdinario as salarioBase,
                    a.salarioOrdinario as salarioBruto,
                    ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0) AS comision,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario) as totalAPagar,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.2683 as CCSS,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.02 as INS,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.083333 as aguinaldo,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.0417 as vacaciones,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.0611 as cesantia,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.0833 as preaviso,
                    ifnull((SELECT sum(valor) FROM hrmOtrosPagosDescuentos WHERE idEmpresas = a.idEmpresas and idHrmEmpleados=a.id and descripcion in('DEPRECIACION') and idHrmTipoPago=3),0) as depreciacion
                FROM
                    hrmEmpleados as a left join hrmPuestos as b on(a.idHrmPuestos=b.id)
                WHERE
                    a.idEmpresas = " . $params['idEmpresas'] . " and a.idHrmStatusEmpleado=1;";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }
    
    public function nominaHN($params) {
        $this->resultado = null;
        $sql = "SELECT
                    a.codigoEmpleado,
                    concat(a.primerNombre,' ',a.segundoNombre,' ',a.primerApellido,' ',a.segundoApellido) as empleado,
                    b.descripcion as cargo,
                    a.salarioOrdinario as salarioBase,
                    ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0) AS variable,
                    ifnull((SELECT sum(valor) FROM hrmOtrosPagosDescuentos WHERE idEmpresas = a.idEmpresas and idHrmEmpleados=a.id and descripcion in('DEPRECIACION') and idHrmTipoPago=3),0) as depreciacion,
                    ifnull((select valor from hrmPrestamos WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id),0) as prestamos,
                    772.76 as IHSS,
                    ((a.salarioOrdinario-8882.3)*0.015) as RAP,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.01 as infop
                FROM
                    hrmEmpleados as a left join hrmPuestos as b on(a.idHrmPuestos=b.id)
                WHERE
                    a.idEmpresas = " . $params['idEmpresas'] . " and a.idHrmStatusEmpleado=1";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }
    
    public function nominaNC($params) {
        $this->resultado = null;
        $sql = "SELECT
                    a.codigoEmpleado,
                    concat(a.primerNombre,' ',a.segundoNombre,' ',a.primerApellido,' ',a.segundoApellido) as empleado,
                    b.descripcion as cargo,
                    a.salarioOrdinario as sueldo,
                    case a.aTrabajadoExtranjero  
                          when 'S' then (a.salarioOrdinario/2)  
                          when 'N' then (a.salarioOrdinario/2)
                          when 'I' then (a.salarioOrdinario)
                    end as sueldoLaSante,
                    ifnull((SELECT sum(valor) FROM hrmOtrosPagosDescuentos WHERE idEmpresas = a.idEmpresas and idHrmEmpleados=a.id and descripcion in('DEPRECIACION') and idHrmTipoPago=3),0) as depreciacion,
                    (ifnull((SELECT sum(valor) FROM hrmOtrosPagosDescuentos WHERE idEmpresas = a.idEmpresas and idHrmEmpleados=a.id and descripcion in('DEPRECIACION') and idHrmTipoPago=3),0)/2) as depreciacionLaSante,
                    ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0) AS comision,
                    (ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)/2) AS comisionLaSante,
                    ((ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.02) as inatec,
                    case a.aTrabajadoExtranjero  
                          when 'S' then (((ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.02)/2)
                          when 'N' then (((ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.02)/2)
                          when 'I' then (((ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.02))
                    end as inatecLaSante,
                    ((ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.19) as INSS,
                    case a.aTrabajadoExtranjero  
                          when 'S' then (((ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.19)/2)  
                          when 'N' then (((ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.19)/2)
                          when 'I' then (((ifnull((SELECT sum(valor) FROM hrmComisiones WHERE idEmpresas = a.idEmpresas and init_at='".date("Y-m-d", strtotime($params['fechaInicio']))."' and end_at='".date("Y-m-d", strtotime($params['fechaFin']))."' and idHrmEmpleados=a.id and observaciones in('COMISION','ARRASTRE','PREMIOS')),0)+a.salarioOrdinario)*0.19))
                    end as INSSLaSante
                FROM
                    hrmEmpleados as a left join hrmPuestos as b on(a.idHrmPuestos=b.id)
                WHERE
                    a.idEmpresas = " . $params['idEmpresas'] . " and a.idHrmStatusEmpleado=1";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }
}
