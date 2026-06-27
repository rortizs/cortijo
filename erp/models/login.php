<?php

/**
 * login.class
 * @author Jonathan Juarez
 * @version 1.0 20140127
 */
session_start();
require_once ("dbCon.php");
require_once ("general.php");

class Login extends General {

    /** METODO PARA VALIDAR CONF DE PROYECTO
     * 
     */
    public function validateConfProject($url) {
        $sql = "select * from connections where url='" . $url . "' and status=1";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conDynamic('instancias'));
        while ($reg = mysql_fetch_assoc($query)) {
            return $reg;
        }
    }

    /** METODO LOGIN USER
     *
     */
    public function loginUser($user, $pwd) {
        $sql = "SELECT 
                    a.*,b.descripcion,c.nombreComercial as empresa
                FROM
                    usuarios as a 
                        left join 
                    sucursales as b on(a.idSucursales=b.id) 
                        left join
                    empresas as c on(a.idEmpresas=c.id)    
                WHERE 
                    a.user='" . $user . "' and a.pwd='" . md5($pwd) . "';";
        $query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
        $reg = mysql_fetch_array($query);
        $_SESSION['idUsuarios'] = $reg['id'];
        $_SESSION['userName'] = $reg['userName'];
        $_SESSION['idEmpresas'] = $reg['idEmpresas'];
        $_SESSION['empresa'] = $reg['empresa'];
        $_SESSION['idRoles'] = $reg['idRoles'];
        $_SESSION['idSucursalesS'] = $reg['idSucursales'];
        $_SESSION['nombreSucursal'] = $reg['descripcion'];
        $_SESSION['maxDescuento'] = $reg['maxDescuento'];
        $_SESSION['companyName'] = $reg['empresa'];
        $_SESSION['tipoCaja'] = ($reg['idTipoCaja'] ?: 2);
    }

    public function loginApps($user, $pwd) {
        $this->resultado = null;
        $sql = "SELECT 
                    a.*,b.descripcion,c.nombreComercial as empresa
                FROM
                    usuarios as a 
                        left join 
                    sucursales as b on(a.idSucursales=b.id) 
                        left join
                    empresas as c on(a.idEmpresas=c.id)    
                WHERE 
                    a.user='" . $user . "' and a.pwd='" . md5($pwd) . "';";
        // echo $sql; 
        $query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
        if ($reg = mysql_fetch_array($query)) {
            $this->resultado = $reg;
            $_SESSION['idUsuarios'] = $reg['id'];
            $_SESSION['userName'] = $reg['userName'];
            $_SESSION['idEmpresas'] = $reg['idEmpresas'];
            $_SESSION['empresa'] = $reg['empresa'];
            $_SESSION['idRoles'] = $reg['idRoles'];
            $_SESSION['idSucursalesS'] = $reg['idSucursales'];
            $_SESSION['nombreSucursal'] = $reg['descripcion'];
            $_SESSION['maxDescuento'] = $reg['maxDescuento'];
            $_SESSION['companyName'] = $reg['empresa'];
        }
        return $this->resultado;
    }

    /** METODO PARA TRAER LISTADO DE EMPRESA SEGUN USUARIO
     * 
     */
    public function loadEmpresas($idUsuarios, $idRoles) {
        $filter = "empresas";
        if ($idRoles !== '1') {
            $filter = "usuariosEmpresas inner join empresas on(usuariosEmpresas.idEmpresas=empresas.id) where usuariosEmpresas.idUsuarios=" . $idUsuarios . "";
        }
        $this->resultado = null;
        $sql = "SELECT 
                    empresas.id,
                    empresas.nombreComercial,
                    empresas.razonSocial,
                    empresas.nit
                FROM
                " . $filter . ";";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO LOAD MODULOS
     *
     */
    public function loadModulos($idRoles) {
        $this->resultado = null;
        $sql = "SELECT 
                    modulos.descripcion as 'modulo'
                FROM
                    paginas 
                    inner join permisos on (paginas.id=permisos.idPaginas)
                    inner join modulos on (paginas.idModulos=modulos.id)
                    inner join roles on(permisos.idRoles=roles.id)
                WHERE
                    roles.id=" . $idRoles . " and modulos.status=1
                GROUP BY
                    modulos.descripcion
                ORDER BY modulos.orden asc;";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

    /** METODO LOAD PAGINAS
     *
     */
    public function loadPaginas($idRoles) {
        $this->resultado = null;
        $sql = "SELECT 
                    concat(funcion,'(',concat(if(parametros='','',concat(parametros)),concat(if(permisos.btnsNew='','',concat(',',permisos.btnsNew)),if(permisos.btnsUpdate='','',concat(',',permisos.btnsUpdate)),if(permisos.btnsDelete='','',concat(',',permisos.btnsDelete)))),')') as `function`,
                    titulo,
                    modulos.descripcion as 'modulo',
                    roles.descripcion
                FROM
                    paginas inner join permisos on (paginas.id=permisos.idPaginas)
                    inner join modulos on (paginas.idModulos=modulos.id)
                    inner join roles on(permisos.idRoles=roles.id)
                WHERE
                    roles.id=" . $idRoles . " and  paginas.status=1
                order by paginas.orden asc;";
        //echo $sql."<br/>";
        $query = mysql_query($sql, dbCon::conDynamic($_SESSION['dbProject']));
        while ($reg = mysql_fetch_assoc($query)) {
            $this->resultado[] = $reg;
        }
        return $this->resultado;
    }

}
