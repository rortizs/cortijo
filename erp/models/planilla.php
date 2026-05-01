<?php

/**
 * Description of planilla
 *
 * @author Richard Sasvin
 */
require_once ("dbCon.php");
require_once ("general.php");

class Planilla extends General {
    
    
    /** METODO getDepartamentos
     * 
     */
    public function getDepartamentos($idEmpresas) {
        $this->resultado = null;
        $sql = "select * from hrmDepartamentos where idEmpresas=" . $idEmpresas . ";";
        //echo $sql;  
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }
    
    /** METODO getHrmPlanillas
     * 
     */
    public function getHrmPlanillas($idEmpresas) {
        $this->resultado = null;
        $sql = "select * from hrmPlanillas where idEmpresas=" . $idEmpresas . ";";
        //echo $sql;  
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getEmpleados
     * 
     */
    public function getEmpleados($idEmpresas, $periodo, $mes) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.id as idHrmEmpleados,
                    a.codigoEmpleado as codigo_empleado,
                    concat(a.primerNombre,' ',a.segundoNombre,' ',a.primerApellido,' ',a.segundoApellido,' ',a.apellidoCasada) as nombre_empleado,
                    a.salarioOrdinario as salario_ordinario,
                    a.bonificacionFija,
                    a.bonificacion,
                    (select ifnull(sum(monto),0) as monto from hrmComisiones where idHrmEmpleados=a.id and periodo='" . $periodo . "' and mes='" . $mes . "') as comisiones,
                    (select ifnull(sum(monto),0) as monto from hrmPrestamos where idHrmEmpleados=a.id and periodo='" . $periodo . "' and mes='" . $mes . "') as prestamos,
                    b.descripcion as departamento,
                    c.descripcion as puesto,
                    a.fechaIngreso as fecha_ingreso,
                    a.idEmpresas
                FROM
                    hrmEmpleados as a 
                    left join hrmDepartamentos as b on(a.idHrmDepartamentos=b.id)
                    left join hrmPuestos as c on(a.idHrmPuestos=c.id)
                WHERE
                    a.idEmpresas = " . $idEmpresas . " and a.idHrmStatusEmpleado=1;";
        //echo $sql;  
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getHrmConstructorPlanilla
     *
     */
    public function getHrmConstructorPlanilla($idHrmPlanillas, $idEmpresas) {
        $this->resultado = null;
        $sql = "select * from hrmConstructorPlanilla where idHrmPlanillas=" . $idHrmPlanillas . " and idEmpresas=" . $idEmpresas . " order by orden asc;";
        echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getHrmPlanillasGeneradas
     *
     */
    public function getHrmPlanillasGeneradas($periodo, $mes, $idHrmPlanillas, $idEmpresas) {
        $this->resultado = null;
        $sql = "select * from hrmPlanillasGeneradas where periodo='" . $periodo . "' and mes='" . $mes . "' and idHrmPlanillas=" . $idHrmPlanillas . " and idEmpresas=" . $idEmpresas . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getHrmDepartamentos
     *
     */
    public function getHrmDepartamentos($idEmpresa) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.*
                FROM
                    hrmDepartamentos as a inner join hrmEmpleados as b on(a.id=b.idHrmDepartamentos)
                where 
                    b.idEmpresas=" . $idEmpresa . " 
                    -- and idHrmDepartamentos=5
                group by
                    a.descripcion;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getHrmEmpleados
     *
     */
    public function getHrmEmpleados($params, $idEmpresas) {
        $this->resultado = null;
        $filter = "";
        if ($params['idHrmDepartamentos'] != "" && $params['idHrmDepartamentos'] != "*") {
            $filter .= " and b.id = " . $params['idHrmDepartamentos'] . "";
        }
        if ($params['idEmpleados'] != "" && $params['idEmpleados'] != "*") {
            $filter .= " and a.codigoEmpleado=" . $params['idEmpleados'] . " ";
        }
        $sql = "SELECT
                    a.id as idEmpleado,
                    a.codigoEmpleado,
                    concat(primerNombre,' ',segundoNombre,' ',primerApellido,' ',segundoApellido) as nombreEmpleado,
                    b.id,
                    b.descripcion as departamento,
                    a.idEmpresas,
                    date_format(a.fechaIngreso,'%d-%m-%Y') as fechaIngreso,
                    noTributario
                FROM
                    hrmEmpleados as a
                        inner join
                    hrmDepartamentos as b ON (a.idHrmDepartamentos = b.id)
                WHERE
                    a.idHrmStatusEmpleado=1 and a.idEmpresas=" . $idEmpresas . " " . $filter . "
                group by 
                    a.codigoEmpleado
                order by b.descripcion,primerNombre,primerApellido asc;";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO generacionPlanilla
     *
     */
    public function generacionPlanilla($params, $departamento, $idEmpresa) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.id as idEmpleado,
                    a.codigoEmpleado,
                    concat(primerNombre,' ',segundoNombre,' ',tercerNombre,' ',primerApellido,' ', segundoApellido,' ', apellidoCasada) as nombreCompleto,
                    date_format(a.fechaIngreso,'%d-%m-%Y') as fechaIngreso,
                    date_format(a.fechaEgreso,'%d-%m-%Y') as fechaEgreso,
                    b.id as idHrmDepartamentos,
                    b.descripcion as hrmDepartamentos,
                    d.id as idHrmPuestos,
                    d.descripcion as hrmPuestos,
                    (CASE c.idMetodoDias
                            WHEN 1 THEN 
                    (a.salarioOrdinario/30)
                            WHEN 2 THEN 
                    (a.salarioOrdinario/day(last_day('" . date("Y-m-d", strtotime($params['fechaFin'])) . "')))
                    END) as sueldoDiario,
                    ((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)) as diasTrabajados,
                    ifnull((SELECT
                            (CASE idHrmTipoEvento
                                WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                                WHEN 4 THEN dias
                                WHEN 6 THEN dias
                                WHEN 7 THEN dias
                            END) AS diasDesc
                        FROM
                            hrmEventos
                        WHERE
                            idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0) as diasDesc,    
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((a.salarioOrdinario/30)*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                                WHEN 2 THEN 
                        ((a.salarioOrdinario/day(last_day('" . date("Y-m-d", strtotime($params['fechaFin'])) . "')))*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                        END) as ordinario,
                (a.bonificacion*(c.pctPagoBonoAnt/100)) as bonif,        
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((a.bonificacion/30)*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                                WHEN 2 THEN 
                        ((a.bonificacion/day(last_day('" . date("Y-m-d", strtotime($params['fechaFin'])) . "')))*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                        END) as bonificacion,
                        (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((a.bonificacionFija/30)*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                                WHEN 2 THEN 
                        ((a.bonificacionFija/day(last_day('" . date("Y-m-d", strtotime($params['fechaFin'])) . "')))*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                        END) as bonificacionFija,
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ifnull((select sum(horasDiurnas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ifnull((select sum(horasDiurnas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasSimples,
                        (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((c.salarioDiarioHE/8)*1.5) * ifnull((select sum(horasDiurnas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ((c.salarioDiarioHE/8)*1.5) * ifnull((select sum(horasDiurnas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasExtSimples,
                        (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ifnull((select sum(horasNocturas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ifnull((select sum(horasNocturas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasDobles,
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((c.salarioDiarioHE/8)*2) * ifnull((select sum(horasNocturas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ((c.salarioDiarioHE/8)*2) * ifnull((select sum(horasNocturas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasExtDobles,
                        (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ifnull((select sum(horasMixtas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ifnull((select sum(horasMixtas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasMixtas,
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((c.salarioDiarioHE/8)*2) * ifnull((select sum(horasMixtas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ((c.salarioDiarioHE/8)*2) * ifnull((select sum(horasMixtas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasExtMixtas,
                    ifnull((select sum(valor) from hrmComisiones where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) as comisiones,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion='INCENTIVO' AND init_at = '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS bonoIncentivo,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion='CALIDAD' AND init_at = '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS bonoCalidad,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion='PRODUCTIVIDAD' AND init_at = '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS bonoProductividad,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion='FLOCKING' AND init_at = '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS bonoFlocking,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion not in('INCENTIVO','CALIDAD','PRODUCTIVIDAD','FLOCKING')AND init_at = '2018-04-01' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS otrosPagos,
                    ifnull((select sum(valor) from hrmAnticipos where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) as anticipos,
                    ifnull((select valor from hrmPrestamos where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) as prestamos,
                    ifnull((select sum(valor) from hrmOtrosPagosDescuentos where idHrmEmpleados=a.id and idHrmTipoPago=2 and init_at='" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) as otrosDescuentos,
                    c.seguroSocial,
                    deduccionSinComprobacion,
                    c.ingresoSujetosISR,
                    c.isr,
                    a.salarioOrdinario as salario,
                    a.bonificacion as bonifDecreto,
                    sum(a.salarioOrdinario+a.bonificacion) as salarioTotal,
                    c.salarioDiarioHE,
                    a.noTributario,
                    a.noSeguroSocial,
                    primerNombre,
                    segundoNombre,
                    tercerNombre,
                    primerApellido,
                    segundoApellido,
                    apellidoCasada,
                    f.abrev as condicionLaboral,
                    a.profesion,
                    month(a.fechaIngreso) as mesIngreso,
                    month(a.fechaEgreso) as mesEgreso,
                    year(a.fechaIngreso) as anoIngreso,
                    year(a.fechaEgreso) as anoEgreso,
                    ifnull((SELECT 
                            idHrmTipoEvento
                        FROM
                            hrmEventos
                        WHERE
                            idHrmEmpleados = a.id AND month(end_at) = '" . date("m", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento=1),0) as periodoVacaciones
                FROM
                    hrmEmpleados AS a
                        LEFT JOIN
                    hrmDepartamentos AS b ON (a.idHrmDepartamentos = b.id)
                        LEFT JOIN
                    hrmPuestos AS d ON (a.idHrmPuestos = d.id)
                        LEFT JOIN
                    paises as c ON(a.idPaises=c.id)
                        LEFT JOIN
                    hrmTipoContrato as f ON(a.idHrmTipoContrato=f.id)
                        LEFT JOIN
                    empresas as g ON(a.idEmpresas=g.id)
                WHERE
                    a.idHrmStatusEmpleado = 1 
                    AND b.descripcion = '" . $departamento . "' 
                    AND a.idEmpresas = " . $idEmpresa . " AND a.fechaIngreso<='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'
                GROUP BY
                    a.codigoEmpleado
                ORDER BY concat(primerNombre,' ',segundoNombre,' ',tercerNombre,' ',primerApellido,' ', segundoApellido,' ', apellidoCasada) ASC";
        //echo $sql . '<br/><br/>';
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO cargaInfoPlantilla
     * 
     */
    public function cargaInfoPlantilla($data, $modulo, $idEmpresa) {
        $response = "";
        $sql = "";
        switch ($modulo) {
            case 'vw_hrmHorasExtras':
                $number = count($data);
                $sql = "insert into hrmHorasExtras(idHrmEmpleados,init_at,end_at,horasDiurnas,horasNocturas,horasMixtas,idEmpresas) values";
                foreach ($data as $key => $value) {
                    if (($key + 1) !== $number) {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['init_at'] . "','" . $value['end_at'] . "','" . $value['horasDiurnas'] . "','" . $value['horasNocturas'] . "','" . $value['horasMixtas'] . "'," . $idEmpresa . "),";
                    } else {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['init_at'] . "','" . $value['end_at'] . "','" . $value['horasDiurnas'] . "','" . $value['horasNocturas'] . "','" . $value['horasMixtas'] . "'," . $idEmpresa . ");";
                    }
                }
                break;
            case 'vw_hrmComisiones':
                $number = count($data);
                $sql = "insert into hrmComisiones(idHrmEmpleados,periodo,mes,monto,observaciones,idEmpresas) values";
                foreach ($data as $key => $value) {
                    if (($key + 1) !== $number) {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['periodo'] . "','" . $value['mes'] . "','" . $value['valor'] . "','" . $value['observaciones'] . "'," . $idEmpresa . "),";
                    } else {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['periodo'] . "','" . $value['mes'] . "','" . $value['valor'] . "','" . $value['observaciones'] . "'," . $idEmpresa . ");";
                    }
                }
                break;
            case 'vw_hrmOtrosPagosDescuentos':
                $number = count($data);
                $sql = "insert into hrmOtrosPagosDescuentos(idHrmEmpleados,init_at,end_at,idHrmTipoPago,descripcion,valor,idEmpresas) values";
                foreach ($data as $key => $value) {
                    if (($key + 1) !== $number) {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['init_at'] . "','" . $value['end_at'] . "','" . $value['idHrmTipoPago'] . "','" . $value['descripcion'] . "','" . $value['valor'] . "'," . $idEmpresa . "),";
                    } else {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['init_at'] . "','" . $value['end_at'] . "','" . $value['idHrmTipoPago'] . "','" . $value['descripcion'] . "','" . $value['valor'] . "'," . $idEmpresa . ");";
                    }
                }
                break;
            case 'vw_hrmPrestamos':
                $number = count($data);
                $sql = "insert into hrmPrestamos(idHrmEmpleados,init_at,end_at,valor,idEmpresas) values";
                foreach ($data as $key => $value) {
                    if (($key + 1) !== $number) {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['init_at'] . "','" . $value['end_at'] . "','" . $value['valor'] . "'," . $idEmpresa . "),";
                    } else {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['init_at'] . "','" . $value['end_at'] . "','" . $value['valor'] . "'," . $idEmpresa . ");";
                    }
                }
                break;
            case 'vw_hrmAnticipos':
                $number = count($data);
                $sql = "insert into hrmAnticipos(idHrmEmpleados,init_at,end_at,valor,idEmpresas) values";
                foreach ($data as $key => $value) {
                    if (($key + 1) !== $number) {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['init_at'] . "','" . $value['end_at'] . "','" . $value['valor'] . "'," . $idEmpresa . "),";
                    } else {
                        $sql .= "(" . $value['idEmpleado'] . ",'" . $value['init_at'] . "','" . $value['end_at'] . "','" . $value['valor'] . "'," . $idEmpresa . ");";
                    }
                }
                break;
        }
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /**
     * 
     */
    public function dateFormat($string) {
        $divs = explode('/', $string);
        $day = $divs[0];
        $month = $divs[1];
        $year = $divs[2];
        $date = $year . '-' . $month . '-' . $day;
        return $date;
    }

    /** METODO horaMarcaje
     *
     */
    public function horaMarcaje($idUsuarios, $fecha, $idEmpresas) {
        //echo 'Empresa: '.$idEmpresas.'<br/>';
        $sql = "SELECT 
                a.id,
                a.codigoEmpleado,
                concat(a.primerNombre,' ',a.segundoNombre,' ',a.primerApellido,' ',a.segundoApellido) as nombreEmpleado,
                (SELECT 
                            c.descripcion
                    FROM
                            hrmTurnosEmpleados AS b
                                    LEFT JOIN
                            hrmTurnos as c on(b.idHrmTurnos=c.id)    
                    WHERE
                            init_at <= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND end_at >= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND idHrmEmpleados=a.id order by b.id desc limit 1) as turno,
                    (SELECT 
                            c.horaEntrada
                    FROM
                            hrmTurnosEmpleados AS b
                                    LEFT JOIN
                            hrmTurnos as c on(b.idHrmTurnos=c.id)    
                    WHERE
                            init_at <= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND end_at >= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND idHrmEmpleados=a.id order by b.id desc limit 1) as horaEntradaTurno,
                (SELECT 
                            c.horaSalida
                    FROM
                            hrmTurnosEmpleados AS b
                                    LEFT JOIN
                            hrmTurnos as c on(b.idHrmTurnos=c.id)    
                    WHERE
                            init_at <= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND end_at >= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND idHrmEmpleados=a.id order by b.id desc limit 1) as horaSalidaTurno,
                if((SELECT 
                            c.horaEntrada
                    FROM
                            hrmTurnosEmpleados AS b
                                    LEFT JOIN
                            hrmTurnos as c on(b.idHrmTurnos=c.id)    
                    WHERE
                            init_at <= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND end_at >= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND idHrmEmpleados=a.id order by b.id desc limit 1)='18:00:00',       
                (select time(created_at) from marcajes where idUsuarios=a.codigoEmpleado and date(created_at)='" . date("Y-m-d", strtotime($fecha)) . "' order by timestamp(created_at) desc limit 1),        
                (select time(created_at) from marcajes where idUsuarios=a.codigoEmpleado and date(created_at)='" . date("Y-m-d", strtotime($fecha)) . "' order by timestamp(created_at) asc limit 1)) as horaEntrada,
                if((SELECT 
                            c.horaEntrada
                    FROM
                            hrmTurnosEmpleados AS b
                                    LEFT JOIN
                            hrmTurnos as c on(b.idHrmTurnos=c.id)    
                    WHERE
                            init_at <= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND end_at >= '" . date("Y-m-d", strtotime($fecha)) . "'
                                    AND idHrmEmpleados=a.id order by b.id desc limit 1)='18:00:00',
                    (select time(created_at) from marcajes where idUsuarios=a.codigoEmpleado and date(created_at)=DATE_ADD('" . date("Y-m-d", strtotime($fecha)) . "',INTERVAL 1 DAY) order by timestamp(created_at) asc limit 1),
                (select time(created_at) from marcajes where idUsuarios=a.codigoEmpleado and date(created_at)='" . date("Y-m-d", strtotime($fecha)) . "' order by timestamp(created_at) desc limit 1))as horaSalida,
                DAYNAME('" . date("Y-m-d", strtotime($fecha)) . "') as dia
            FROM
                hrmEmpleados as a
            WHERE
                a.idEmpresas = " . $idEmpresas . " and 
                a.codigoEmpleado='" . $idUsuarios . "' and 
                a.idHrmStatusEmpleado=1";
        //echo $sql."<br/><br/>";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO HORAS TRABAJADAS
     *
     */
    public function getHorasTrabajadas($idUsuario, $fecha) {
        $arr = array();
        $horas = "";
        $sql = "SELECT 
                    time(created_at) as horaMarcaje
                FROM
                    marcajes
                WHERE
                    idUsuarios = " . $idUsuario . " AND date(created_at) = '" . $fecha . "' AND date(created_at) = '" . $fecha . "';";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            array_push($arr, $reg);
        }
        for ($i = 0; $i < count($arr); $i++) {
            $horas = $this->restarTiempos($arr[0]['horaMarcaje'], $arr[1]['horaMarcaje']);
        }
        return $horas;
    }

    public function restarTiempos($horaini, $horafin) {
        $horai = substr($horaini, 0, 2);
        $mini = substr($horaini, 3, 2);
        $segi = substr($horaini, 6, 2);
        $horaf = substr($horafin, 0, 2);
        $minf = substr($horafin, 3, 2);
        $segf = substr($horafin, 6, 2);
        $ini = ((($horai * 60) * 60) + ($mini * 60) + $segi);
        $fin = ((($horaf * 60) * 60) + ($minf * 60) + $segf);
        $dif = $fin - $ini;
        $difh = floor($dif / 3600);
        $difm = floor(($dif - ($difh * 3600)) / 60);
        $difs = $dif - ($difm * 60) - ($difh * 3600);
        if ($horafin != '') {
            return date("H", mktime($difh, $difm, $difs));
        } else {
            return '--';
        }
    }

    public function AddPlayTime($times) {
        // loop throught all the times
        foreach ($times as $time) {
            list($hour, $minute, $second) = explode(':', $time);
            $all_seconds += $hour * 3600;
            $all_seconds += $minute * 60;
            $all_seconds += $second;
        }
        $total_minutes = floor($all_seconds / 60);
        $seconds = $all_seconds % 60;
        $hours = floor($total_minutes / 60);
        $minutes = $total_minutes % 60;
        // returns the time already formatted
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /** METODO GET EMPLEADOS
     *
     */
    public function getDiasMarcajes($idUsuario, $month, $year) {
        $sql = "select count(*) as dias from(
                   SELECT 
                        if(count(date(created_at))=1,0,1) as dias
                    FROM
                        marcajes
                    WHERE
                        idUsuarios = '" . $idUsuario . "' and month(created_at)='" . $month . "' and year(created_at)='" . $year . "'
                    group by    
                        date(created_at)
                ) as marcajes";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg['dias'];
        }
    }

    public function getTipoMarcajes($idUsuario, $fecha) {
        $this->resultado = null;
        $sql = "SELECT
                    date(created_at) as fecha,
                    case if(count(date(created_at)) is null,0,count(date(created_at)))
                        when 0 then 1
                        when 1 then 2
                        when 2 then 3
                        when 3 then 3
                    end as tipoMarcaje
                FROM
                    marcajes
                WHERE
                    idUsuarios = '" . $idUsuario . "' and date(created_at)='" . $fecha . "'
                group by    
                    date(created_at)";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg['tipoMarcaje'];
        }
    }

    /** Metodo cerrarPlanilla
     * 
     */
    public function cerrarPlanilla($params, $idUsuarios, $idEmpresas) {
        $sql = "insert into hrmPlanillas values(null,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "','" . date("Y-m-d", strtotime($params['fechaFin'])) . "',,'" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "','" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'," . $idUsuarios . "," . $idEmpresas . ",'" . $this->timestamp . "');";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    public function dateDiff($start, $end) {
        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400);
    }

    public function saveCampoConstructorPlanilla($params) {
        $response = "";
        //VALIDACION QUE YA ESTE CREADO UN CAMPO EN EL MISMO NUMERO DE ORDEN
        $sql1 = "select id from hrmConstructorPlanilla where orden=" . $params['orden'] . " and idHrmPlanillas=" . $params['idHrmPlanilla'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
        $query1 = mysql_query($sql1, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query1);
        if ($reg['id'] === null) {
            $sql = "insert into hrmConstructorPlanilla
                values(null,'" . $params['nombreCampo'] . "','" . $params['valor'] . "'," . $params['idTipoValor'] . ",'" . $params['orden'] . "'," . $params['idTipoCampo'] . "," . ($params['idTipoOperacion'] ?: 0) . ",'" . ($params['valorMaximo'] ?: 0) . "'," . $params['idHrmPlanilla'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "',null);";
            $query = mysql_query($sql, dbCon::conPrincipal());
            if ($query == true) {
                if ($params['idTipoValor'] === '2') {
                    $this->insertOtrosPagosDescuentos($params);
                }
                $response[] = array('message' => 'success');
            } else {
                $response[] = array('message' => 'failed', 'error' => $sql);
            }
        } else {
            $response[] = array('message' => 'exists');
        }
        return $response;
    }

    public function insertOtrosPagosDescuentos($params) {
        //GET EMPLEADOS SEGUN EMPRESA
        $sql = "select id from hrmEmpleados where idEmpresas=" . $params['idEmpresas'] . " and idHrmStatusEmpleado=1;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        //INSERTAR EN TABLA hrmOtrosPagosDescuentos POR CADA EMPLEADO
        while ($reg = mysql_fetch_assoc($query)) {
            $sql2 = "insert into hrmOtrosPagosDescuentos values(null," . $reg['id'] . ",'" . $params['nombreCampo'] . "','" . $params['valor'] . "',1," . $params['idEmpresas'] . ",'" . $this->timestamp . "',null);";
            $query2 = mysql_query($sql2, dbCon::conPrincipal());
        }
    }

    public function deleteCampoConstructorPlanilla($params) {
        $sql = "delete from hrmConstructorPlanilla where id=" . $params['item'] . " and idHrmPlanillas=" . $params['idHrmPlanilla'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    public function consultarCierre($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    vw_hrmPlanillas
                WHERE
                    id=" . $params['idPlanilla'] . ";";
        //echo $sql;
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO generacionPlanillaBoletas
     *
     */
    public function generacionPlanillaBoletas($params, $idEmpleado, $idEmpresa) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.id as idEmpleado,
                    a.codigoEmpleado,
                    concat(primerNombre,' ',segundoNombre,' ',tercerNombre,' ',primerApellido,' ', segundoApellido,' ', apellidoCasada) as nombreCompleto,
                    date_format(a.fechaIngreso,'%d-%m-%Y') as fechaIngreso,
                    date_format(a.fechaEgreso,'%d-%m-%Y') as fechaEgreso,
                    b.id as idHrmDepartamentos,
                    b.descripcion as hrmDepartamentos,
                    d.id as idHrmPuestos,
                    d.descripcion as hrmPuestos,
                    (CASE c.idMetodoDias
                            WHEN 1 THEN 
                    (a.salarioOrdinario/30)
                            WHEN 2 THEN 
                    (a.salarioOrdinario/day(last_day('" . date("Y-m-d", strtotime($params['fechaFin'])) . "')))
                    END) as sueldoDiario,
                    ((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)) as diasTrabajados,
                    ifnull((SELECT
                            (CASE idHrmTipoEvento
                                WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                                WHEN 4 THEN dias
                                WHEN 6 THEN dias
                                WHEN 7 THEN dias
                            END) AS diasDesc
                        FROM
                            hrmEventos
                        WHERE
                            idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0) as diasDesc,    
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((a.salarioOrdinario/30)*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                                WHEN 2 THEN 
                        ((a.salarioOrdinario/day(last_day('" . date("Y-m-d", strtotime($params['fechaFin'])) . "')))*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                        END) as ordinario,
                (a.bonificacion*(c.pctPagoBonoAnt/100)) as bonif,        
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((a.bonificacion/30)*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                                WHEN 2 THEN 
                        ((a.bonificacion/day(last_day('" . date("Y-m-d", strtotime($params['fechaFin'])) . "')))*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                        END) as bonificacion,
                        (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((a.bonificacionFija/30)*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                                WHEN 2 THEN 
                        ((a.bonificacionFija/day(last_day('" . date("Y-m-d", strtotime($params['fechaFin'])) . "')))*((if(DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso) + 1>15,15,DATEDIFF('" . date("Y-m-d", strtotime($params['fechaFin'])) . "', a.fechaIngreso)+1))-ifnull((SELECT
                    (CASE idHrmTipoEvento
                        WHEN 2 THEN DATEDIFF(end_at, if(month(init_at)=month(date('" . date("Y-m-d", strtotime($params['fechaInicio'])) . "')),init_at,'" . date("Y-m-d", strtotime($params['fechaInicio'])) . "'))
                        WHEN 4 THEN dias
                        WHEN 6 THEN dias
                        WHEN 7 THEN dias
                    END) AS diasDesc
                FROM
                    hrmEventos
                WHERE
                    idHrmEmpleados=a.id and end_at>='" . date("Y-m-d", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento in (2, 4 , 6, 7)),0)))
                        END) as bonificacionFija,
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ifnull((select sum(horasDiurnas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ifnull((select sum(horasDiurnas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasSimples,
                        (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((c.salarioDiarioHE/8)*1.5) * ifnull((select sum(horasDiurnas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ((c.salarioDiarioHE/8)*1.5) * ifnull((select sum(horasDiurnas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasExtSimples,
                        (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ifnull((select sum(horasNocturas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ifnull((select sum(horasNocturas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasDobles,
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((c.salarioDiarioHE/8)*2) * ifnull((select sum(horasNocturas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ((c.salarioDiarioHE/8)*2) * ifnull((select sum(horasNocturas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasExtDobles,
                        (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ifnull((select sum(horasMixtas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ifnull((select sum(horasMixtas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasMixtas,
                    (CASE c.idMetodoDias
                                WHEN 1 THEN 
                        ((c.salarioDiarioHE/8)*2) * ifnull((select sum(horasMixtas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                                WHEN 2 THEN 
                        ((c.salarioDiarioHE/8)*2) * ifnull((select sum(horasMixtas) from hrmHorasExtras where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicioHE'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFinHE'])) . "'),0)
                        END) as horasExtMixtas,
                    ifnull((select sum(valor) from hrmComisiones where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) as comisiones,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion='INCENTIVO' AND init_at = '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS bonoIncentivo,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion='CALIDAD' AND init_at = '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS bonoCalidad,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion='PRODUCTIVIDAD' AND init_at = '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS bonoProductividad,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion='FLOCKING' AND init_at = '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS bonoFlocking,
                    IFNULL((SELECT SUM(valor) FROM hrmOtrosPagosDescuentos WHERE idHrmEmpleados = a.id AND idHrmTipoPago = 1 AND descripcion not in('INCENTIVO','CALIDAD','PRODUCTIVIDAD','FLOCKING')AND init_at = '2018-04-01' AND end_at = '" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) AS otrosPagos,
                    ifnull((select sum(valor) from hrmAnticipos where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) as anticipos,
                    ifnull((select valor from hrmPrestamos where idHrmEmpleados=a.id and init_at='" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) as prestamos,
                    ifnull((select sum(valor) from hrmOtrosPagosDescuentos where idHrmEmpleados=a.id and idHrmTipoPago=2 and init_at='" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' and end_at='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'),0) as otrosDescuentos,
                    c.seguroSocial,
                    deduccionSinComprobacion,
                    c.ingresoSujetosISR,
                    c.isr,
                    a.salarioOrdinario as salario,
                    a.bonificacion as bonifDecreto,
                    sum(a.salarioOrdinario+a.bonificacion) as salarioTotal,
                    c.salarioDiarioHE,
                    a.noTributario,
                    a.noSeguroSocial,
                    primerNombre,
                    segundoNombre,
                    tercerNombre,
                    primerApellido,
                    segundoApellido,
                    apellidoCasada,
                    f.abrev as condicionLaboral,
                    a.profesion,
                    month(a.fechaIngreso) as mesIngreso,
                    month(a.fechaEgreso) as mesEgreso,
                    year(a.fechaIngreso) as anoIngreso,
                    year(a.fechaEgreso) as anoEgreso,
                    ifnull((SELECT 
                            idHrmTipoEvento
                        FROM
                            hrmEventos
                        WHERE
                            idHrmEmpleados = a.id AND month(end_at) = '" . date("m", strtotime($params['fechaFin'])) . "' and idHrmTipoEvento=1),0) as periodoVacaciones
                FROM
                    hrmEmpleados AS a
                        LEFT JOIN
                    hrmDepartamentos AS b ON (a.idHrmDepartamentos = b.id)
                        LEFT JOIN
                    hrmPuestos AS d ON (a.idHrmPuestos = d.id)
                        LEFT JOIN
                    paises as c ON(a.idPaises=c.id)
                        LEFT JOIN
                    hrmTipoContrato as f ON(a.idHrmTipoContrato=f.id)
                        LEFT JOIN
                    empresas as g ON(a.idEmpresas=g.id)
                WHERE
                    a.idHrmStatusEmpleado = 1 
                    AND a.id = " . $idEmpleado . "
                    AND a.idEmpresas = " . $idEmpresa . " AND a.fechaIngreso<='" . date("Y-m-d", strtotime($params['fechaFin'])) . "'
                GROUP BY
                    a.codigoEmpleado
                ORDER BY concat(primerNombre,' ',segundoNombre,' ',tercerNombre,' ',primerApellido,' ', segundoApellido,' ', apellidoCasada) ASC";
        //echo $sql.'<br/><br/>';
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO getHrmDepartamentos
     *
     */
    public function getHrmDepartamentos2($idEmpresa) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    hrmDepartamentos
                WHERE 
                    idEmpresas=" . $idEmpresa . " 
                ORDER BY
                    descripcion ASC";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO subirExpediente
     * 
     */
    public function subirExpediente($params) {
        $sql = "insert into hrmEmpleadosExpediente values(null," . $params['idHrmEmpleados'] . ",'" . $params['descripcion'] . "','" . $params['file'] . "'," . $params['idEmpresa'] . ",'" . $this->timestamp . "');";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** METODO hrmEmpleadosExpediente
     *
     */
    public function hrmEmpleadosExpediente($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    hrmEmpleadosExpediente
                WHERE 
                    idHrmEmpleados=" . $params['idHrmEmpleados'] . " and idEmpresas=" . $params['idEmpresa'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO subirExpediente
     * 
     */
    public function eliminarExpediente($params) {
        $sql = "delete from hrmEmpleadosExpediente where id=" . $params['item'] . " and idHrmEmpleados=" . $params['idHrmEmpleados'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** METODO subirFotoFicha
     * 
     */
    public function subirFotoFicha($params) {
        $sql = "update hrmEmpleados set image='" . $params['file'] . "' where id=" . $params['idHrmEmpleados'] . " and idEmpresas=" . $params['idEmpresa'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** METODO subirFotoFichaCliente
     * 
     */
    public function subirFotoFichaCliente($params) {
        $sql = "update clientes set image='" . $params['file'] . "' where id=" . $params['idClientes'] . " and idEmpresas=" . $params['idEmpresa'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** METODO clientesDocumentosAdjuntos
     *
     */
    public function clientesDocumentosAdjuntos($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    clientesDocumentosAdjuntos
                WHERE 
                    idClientes=" . $params['idClientes'] . " and idEmpresas=" . $params['idEmpresa'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO subirClientesDocumentosAdjuntos
     * 
     */
    public function subirClientesDocumentosAdjuntos($params) {
        $sql = "insert into clientesDocumentosAdjuntos values(null," . $params['idClientes'] . ",'" . $params['descripcion'] . "','" . $params['file'] . "'," . $params['idEmpresa'] . ",'" . $this->timestamp . "');";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** METODO eliminarClientesDocumentosAdjuntos
     * 
     */
    public function eliminarClientesDocumentosAdjuntos($params) {
        $sql = "delete from clientesDocumentosAdjuntos where id=" . $params['item'] . " and idClientes=" . $params['idClientes'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** METODO subirFotoFichaProducto
     * 
     */
    public function subirFotoFichaProducto($params) {
        $sql = "update productos set image='" . $params['file'] . "' where id=" . $params['idProductos'] . " and idEmpresas=" . $params['idEmpresa'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** METODO getOtrosPagosDescuentosPorEmpleado
     *
     */
    public function getOtrosPagosDescuentosPorEmpleado($idHrmEmpleados, $nombrePago) {
        $sql = "select monto from hrmOtrosPagosDescuentos where idHrmEmpleados=" . $idHrmEmpleados . " and descripcion='" . $nombrePago . "' and idHrmStatusPago=1;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg['monto'];
        }
    }

    /** METODO saveSabanaPlanilla
     * 
     */
    public function saveSabanaPlanilla($params) {
        $response = "";
        //CONSULTAR SI YA EXISTE UN PLANILLA GUARDADA CON LOS PARAMETROS DE periodo,mes,idHrmPlanillas,idEmpresas
        $sql = "select status from hrmPlanillasGeneradas where periodo='" . $params['periodo'] . "' and mes='" . $params['mes'] . "' and idHrmPlanillas=" . $params['idHrmPlanillas'] . " and idEmpresas=" . $params['idEmpresas'] . " group by status;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        $reg = mysql_fetch_assoc($query);
        if ($reg['status'] === null || $reg['status'] === '1') {
            $flag = true;
            if ($reg['status'] === '1') {
                $sql2 = "delete from hrmPlanillasGeneradas where periodo='" . $params['periodo'] . "' and mes='" . $params['mes'] . "' and idHrmPlanillas=" . $params['idHrmPlanillas'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
                $query2 = mysql_query($sql2, dbCon::conPrincipal());
                if (!$query2) {
                    $flag = false;
                }
            }
            if ($flag === true) {
                $campos = "";
                for ($i = 1; $i <= intval($params['columnas']); $i++) {
                    if ($i === intval($params['columnas'])) {
                        $campos .= 'campo' . $i;
                    } else {
                        $campos .= 'campo' . $i . ',';
                    }
                }
                $sql = "insert into hrmPlanillasGeneradas (" . $campos . ",periodo,mes,idHrmPlanillas,idEmpresas,created_at,idUsuarios) values";
                foreach ($params['sabana'] as $key => $value) {
                    $sql .= "(";
                    for ($i = 1; $i <= intval($params['columnas']); $i++) {
                        if ($i === intval($params['columnas'])) {
                            $sql .= "'" . $value['campo' . $i] . "'";
                        } else {
                            $sql .= "'" . $value['campo' . $i] . "',";
                        }
                    }
                    $sql .= ",'" . $params['periodo'] . "','" . $params['mes'] . "'," . $params['idHrmPlanillas'] . "," . $params['idEmpresas'] . ",'" . $this->timestamp . "'," . $params['idUsuarios'] . "";
                    if (($key + 1) === count($params['sabana'])) {
                        $sql .= ")";
                    } else {
                        $sql .= "),";
                    }
                }
                $query = mysql_query($sql, dbCon::conPrincipal());
                if ($query == true) {
                    $response[] = array('message' => 'success');
                } else {
                    $response[] = array('message' => 'failed', 'error' => $sql);
                }
            } else {
                $response[] = array('message' => 'failed2');
            }
        } else if ($reg['status'] === '2') {
            $response[] = array('message' => 'autorizada');
        } else if ($reg['status'] === '3') {
            $response[] = array('message' => 'cerrada');
        }
        return $response;
    }

    /** METODO getCampoConstructorPlanilla
     *
     */
    public function getCampoConstructorPlanilla($params) {
        $this->resultado = null;
        $sql = "select * from hrmConstructorPlanilla where id=" . $params['item'] . " and idHrmPlanillas=" . $params['idHrmPlanilla'] . " and idEmpresas=" . $params['idEmpresas'] . ";";
        //echo $sql
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    public function updateCampoConstructorPlanilla($params) {
        $response = "";
        $sql = "update hrmConstructorPlanilla set nombreCampo='" . $params['nombreCampo'] . "',valor='" . $params['valor'] . "',idTipoValor=" . $params['idTipoValor'] . ",orden='" . $params['orden'] . "',idTipoCampo=" . $params['idTipoCampo'] . ",idTipoOperacion=" . ($params['idTipoOperacion'] ?: 0) . ",valorMaximo='" . ($params['valorMaximo'] ?: 0) . "',updated_at='" . $this->timestamp . "'"
                . " where id=" . $params['idHrmConstructorPlanilla'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            if ($params['idTipoValor'] === '2') {
                $this->updateOtrosPagosDescuentos($params);
            }
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    public function updateOtrosPagosDescuentos($params) {
        $sql2 = "update hrmOtrosPagosDescuentos set descripcion='" . $params['nombreCampo'] . "',monto='" . $params['valor'] . "',idHrmStatusPago=1,updated_at='" . $this->timestamp . "'"
                . " where upper(descripcion)='" . strtoupper($params['nombreCampoOld']) . "';";
        $query2 = mysql_query($sql2, dbCon::conPrincipal());
    }
    
    /** METODO getMarcajes
     *
     */
    public function getMarcajes($params) {
        $this->resultado = null;
        $sql = "SELECT 
                    *
                FROM
                    marcajesGSP
                WHERE
                    codigoEmpleado = " . $params['idEmpleados'] . "
                        AND DATE(fecha) BETWEEN '" . date("Y-m-d", strtotime($params['fechaInicio'])) . "' AND '" . date("Y-m-d", strtotime($params['fechaFin'])) . "' order by fecha asc;";
        $query = mysql_query($sql, dbCon::conPrincipal());
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** Metodo guardarMarcaje
     * 
     */
    public function guardarMarcaje($params) {
        $dia = date("D", strtotime($params['fecha']));
        $dayNew = "";
        switch ($dia) {
            case 'Mon':
                $dayNew = "Lunes";
                break;
            case 'Tue':
                $dayNew = "Martes";
                break;
            case 'Wed':
                $dayNew = "Miercoles";
                break;
            case 'Thu':
                $dayNew = "Jueves";
                break;
            case 'Fri':
                $dayNew = "Viernes";
                break;
            case 'Sat':
                $dayNew = "Sabado";
                break;
            case 'Sun':
                $dayNew = "Domingo";
                break;
        }
        $sql = "insert into marcajesGSP (codigoEmpleado,fecha,dia,horaEntrada,horaSalida,numeroHorasExtrasDiurnas,numeroHorasExtrasNocturas)"
                . " values('" . $params['idEmpleados'] . "','" . date("Y-m-d", strtotime($params['fecha'])) . "','" . $dayNew . "','" . $params['horaEntrada'] . "','" . $params['horaSalida'] . "','" . $params['numeroHorasExtrasDiurnas'] . "','" . $params['numeroHorasExtrasNocturas'] . "');";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }

    /** Metodo updateMarcaje
     * 
     */
    public function updateMarcaje($params) {
        $sql = "update marcajesGSP set horaEntrada='" . $params['horaEntrada'] . "',horaSalida='" . $params['horaSalida'] . "',numeroHorasExtrasDiurnas='" . $params['numeroHorasExtrasDiurnas'] . "',numeroHorasExtrasNocturas='" . $params['numeroHorasExtrasNocturas'] . "'"
                . " where id=" . $params['idMarcaje'] . ";";
        $query = mysql_query($sql, dbCon::conPrincipal());
        if ($query == true) {
            $response[] = array('message' => 'success');
        } else {
            $response[] = array('message' => 'failed', 'error' => $sql);
        }
        return $response;
    }
}
