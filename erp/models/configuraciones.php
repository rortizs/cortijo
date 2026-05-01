<?php

class configuraciones {

//FUNCION DE CONEXION
    public function nuevaConexion($nombreDB) { //funcion que recibe el nombre de la base de datos a conectar y devuelve la conexion
        $conexion = mysqli_connect('45.79.3.109', 'root', '10Br3nd!t@#102022', $nombreDB);
        //$conexion = mysqli_connect('192.168.1.19', 'root', 'c0rt23062022', $nombreDB);
        return $conexion;
    }

//FUNCION SELECT SCHEMA--------------------------
    public function selectBD($conexion) { //funcion que devuelve los nombres de las bases de datos en el esquema
        $bd = null;
        $query = "SELECT SCHEMA_NAME FROM SCHEMATA WHERE 
    SCHEMA_NAME NOT IN ('information_schema' , 'kairos_conf','mysql','performance_schema','phpmyadmin')";
        $result = $conexion->query($query);
        while ($row = $result->fetch_assoc()) {
            $bd[] = $row;
        }
        return $bd;
    }

//funcion que devuelve los nombres de las tablas de una base de datos;
    public function selectTable($conexion, $nombreBd) { //funcion que devuelve los nombres de las bases de datos en el esquema
        $table = null;
        $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA='$nombreBd'";
        $result = $conexion->query($query);
        while ($row = $result->fetch_assoc()) {
            $table[] = $row;

        }
        return $table;
    }

//funcion que devuelve los nombres de las columnas de una tabla
    public function selectColumn($conexion, $nombreTabla, $nombreBd) { //funcion que devuelve los nombres de las bases de datos en el esquema
        $column = null;
        $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_NAME ='$nombreTabla' AND TABLE_SCHEMA='$nombreBd'";
        $result = $conexion->query($query);
        while ($row = $result->fetch_assoc()) {
            $column[] = $row;
        }
        return $column;
    }

//MODIFICAR BASE DE DATOS POR MEDIO DE UN SCRIPT
    public function modificarDB($conexion) {
        $datos = null;
        if ($handle = opendir('./archivoSql')) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != '.' && $entry != '..') {
                    $dir = './archivoSql/' . $entry;
                    $sql = file_get_contents($dir);
                    mysqli_multi_query($conexion, $sql);

                    do {
                        $result = mysqli_store_result($conexion);

                        if (mysqli_errno($conexion) == 0) {
                            $datos[] = array(
                                "accion" => $sql,
                                "mensaje" => mysqli_affected_rows($conexion) . ' row(s) affected',
                                "archivo" => $entry,
                                "error" => 0
                            );
                        } else {
                            $datos[] = array(
                                "accion" => $sql,
                                "mensaje" => mysqli_errno($conexion) . ' - ' . mysqli_error($conexion),
                                "archivo" => $entry,
                                "error" => 1
                            );
                        }
                        mysqli_free_result($result);
                        /* print divider */
                        if (mysqli_more_results($conexion)) {
                            
                        }
                    } while (mysqli_next_result($conexion));
                }
                $conexion->query("COMMIT");
            }
        }
        return $datos;
    }

//COPIAR NOMENCLATURA--------------------------
    public function copiarNomenclatura($conexion, $empresa1, $empresa2) {
        $response = "";
        $queryNomenclatura = "SELECT * FROM nomenclatura WHERE idEmpresas=$empresa2";
        $result = $conexion->query($queryNomenclatura);
        if (mysqli_num_rows($result) > 0) {

            $queryDeleteNomenclatura = "DELETE FROM nomenclatura WHERE idEmpresas=$empresa2";
            $resultDeleteNomenclatura = $conexion->query($queryDeleteNomenclatura);
            if ($resultDeleteNomenclatura) {

                $queryInsert = "INSERT INTO nomenclatura(cuenta,nivel,descripcion,padre,idTipoCuentaContable,idTipoOperacionContable,idEmpresas)
                                SELECT
                                cuenta,nivel,descripcion,padre,idTipoCuentaContable,idTipoOperacionContable,$empresa2
                                FROM
                                nomenclatura
                                WHERE
                                idEmpresas = $empresa1";
                $resultInsert = $conexion->query($queryInsert);
                if ($resultInsert != TRUE) {
                    $response[] = array('message' => 'failed');
                    return $response;
                }
            } else {
                $response[] = array('message' => 'failed');
                return $response;
            }
        } else {


            $queryInsert = "INSERT INTO nomenclatura(cuenta,nivel,descripcion,padre,idTipoCuentaContable,idTipoOperacionContable,idEmpresas)
                                SELECT
                                cuenta,nivel,descripcion,padre,idTipoCuentaContable,idTipoOperacionContable,$empresa2
                                FROM
                                nomenclatura
                                WHERE
                                idEmpresas = $empresa1";
            $resultInsert = $conexion->query($queryInsert);
            if ($resultInsert == TRUE) {
                $resultadoQuerys = 1;
            } else {
                $response[] = array('message' => 'failed');
                return $response;
            }
        }
        $response[] = array('message' => 'success');

        return $response;
    }

//FUNCIONES USUARIOS Y EMPRESAS------------------
    public function asignarUsuarioEmpresa($conexion, $idUsuario, $arrayEmpresas) {
        $response = "";
        $resultQuerys = 0;
        $queryPermiso = "SELECT * FROM usuariosEmpresas WHERE idUsuarios=$idUsuario";
        $result = $conexion->query($queryPermiso);
        if (mysqli_num_rows($result) > 0) {
            $queryDeleteRol = "DELETE FROM usuariosEmpresas WHERE idUsuarios=$idUsuario";
            $resultDeleteRol = $conexion->query($queryDeleteRol);
            if ($resultDeleteRol) {
                foreach ($arrayEmpresas as $Empresa) {
                    $queryInsert = "INSERT INTO usuariosEmpresas(idUsuarios,idEmpresas)"
                            . "VALUES($idUsuario,$Empresa)";
                    $resultInsert = $conexion->query($queryInsert);
                    if ($resultInsert != TRUE) {
                        $response[] = array('message' => 'failed');
                        return $response;
                    }
                }
            } else {
                $response[] = array('message' => 'failed');
                return $response;
            }
        } else {

            foreach ($arrayEmpresas as $Empresa) {
                $nulo = null;
                $queryInsert = "INSERT INTO usuariosEmpresas(idUsuarios,idEmpresas)"
                        . "VALUES($idUsuario,$Empresa)";
                $resultInsert = $conexion->query($queryInsert);
                if ($resultInsert == TRUE) {
                    $resultadoQuerys = 1;
                } else {
                    $response[] = array('message' => 'failed');
                    return $response;
                }
            }
        }
        $response[] = array('message' => 'success');

        return $response;
    }

    public function getEmpresaUsuarios($conexion, $id_usuario) {
        $usuarios = null;
        $queryUsuario = "SELECT u.id as idUsuario ,u.userName as Nombre,e.idEmpresas as Empresa FROM usuarios AS u "
                . "INNER JOIN usuariosEmpresas AS e ON (u.id=e.idUsuarios) WHERE u.id=$id_usuario";
        $result = $conexion->query($queryUsuario);
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }

    public function getUsuarios($conexion) {
        $usuarios = null;
        $queryUsuario = "SELECT id,userName,idEmpresas FROM usuarios";
        $result = $conexion->query($queryUsuario);
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        return $usuarios;
    }

    public function getEmpresas($conexion) {
        $empresas = null;
        $queryUsuario = "SELECT id,nombreComercial FROM empresas";
        $result = $conexion->query($queryUsuario);
        while ($row = $result->fetch_assoc()) {
            $empresas[] = $row;
        }
        return $empresas;
    }

//-----FUNCIONES PERMISOS Y MODULOS---------------
    public function asignarPermisos($conexion, $idRol, $idModulo, $arrayModulo, $arrayNew, $arrayUpdate, $arrayDelete, $arrayPaginas) {
        $response = "";
        $arrayPosicion = 0;
        $Modulos = null;
        $queryPermiso = "SELECT * FROM permisos WHERE idRoles=$idRol and idPaginas IN (SELECT id FROM paginas WHERE idModulos=$idModulo)";
        $result = $conexion->query($queryPermiso);
        if (mysqli_num_rows($result) > 0) {
            $queryDeleteRol = "DELETE FROM permisos WHERE idRoles=$idRol and idPaginas IN (SELECT id FROM paginas WHERE idModulos=$idModulo)";
            $resultDeleteRol = $conexion->query($queryDeleteRol);
            if ($resultDeleteRol) {

                foreach ($arrayPaginas as $pagina) {
                    foreach ($arrayModulo as $modulo) {

                        if ($pagina == $modulo) {

                            if ($arrayNew[$arrayPosicion] == 2 || $arrayUpdate[$arrayPosicion] == 2 || $arrayDelete[$arrayPosicion] == 2) {
                                $queryInsert = "INSERT INTO permisos(idPaginas,idRoles)"
                                        . "VALUES($pagina,$idRol)";
                                $resultInsert = $conexion->query($queryInsert);
                                if ($resultInsert != TRUE) {
                                    $response[] = array('message' => 'failed');
                                    return;
                                }
                            } else {
                                $queryInsert = "INSERT INTO permisos(idPaginas,idRoles,btnsNew,btnsUpdate,btnsDelete) "
                                        . "VALUES($pagina,$idRol,$arrayNew[$arrayPosicion],$arrayUpdate[$arrayPosicion],$arrayDelete[$arrayPosicion])";
                                $resultInsert = $conexion->query($queryInsert);
                                if ($resultInsert != TRUE) {
                                    $response[] = array('message' => 'failed');
                                    return;
                                }
                            }
                        }
                    }
                    $arrayPosicion++;
                }
            } else {
                $response[] = array('message' => 'failed');
                return $response;
            }
        } else {
            foreach ($arrayPaginas as $pagina) {
                foreach ($arrayModulo as $modulo) {

                    if ($pagina == $modulo) {

                        if ($arrayNew[$arrayPosicion] == 2 || $arrayUpdate[$arrayPosicion] == 2 || $arrayDelete[$arrayPosicion] == 2) {
                            $queryInsert = "INSERT INTO permisos(idPaginas,idRoles)"
                                    . "VALUES($pagina,$idRol)";
                            $resultInsert = $conexion->query($queryInsert);
                            if ($resultInsert != TRUE) {
                                $response[] = array('message' => 'failed');
                                return;
                            }
                        } else {
                            $queryInsert = "INSERT INTO permisos(idPaginas,idRoles,btnsNew,btnsUpdate,btnsDelete) "
                                    . "VALUES($pagina,$idRol,$arrayNew[$arrayPosicion],$arrayUpdate[$arrayPosicion],$arrayDelete[$arrayPosicion])";
                            $resultInsert = $conexion->query($queryInsert);
                            if ($resultInsert != TRUE) {
                                $response[] = array('message' => 'failed');
                                return;
                            }
                        }
                    }
                }
                $arrayPosicion++;
            }
        }
        $response[] = array('message' => 'success');

        return $response;
    }

    public function getRoles($conexion) {
        $roles = null;
        $queryRol = "SELECT id,descripcion FROM roles";
        $result = $conexion->query($queryRol);
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
        return $roles;
    }

    public function getModulos($conexion) {
        $queryModulo = "SELECT id, descripcion FROM modulos";
        $result = $conexion->query($queryModulo);
        while ($row = $result->fetch_assoc()) {
           $modulos[] = array_map('utf8_encode',$row);      
        }
        return $modulos;
    }

    public function getPaginasModulo($conexion, $idModulo) {
        $result_array = null;
        $query = "SELECT 
   m.descripcion AS modulo,
   p.titulo AS pagina,
   p.id AS idPagina,
   p.funcion AS funcion
FROM
    paginas as p
       INNER JOIN
    modulos as m ON (p.idModulos = m.id)     
WHERE
    idModulos =$idModulo";
        $result = $conexion->query($query);
        while ($row = $result->fetch_assoc()) {

            $result_array[] = $row;
        }
        return $result_array;
    }

    public function getPaginasRol($conexion, $idModulo, $idRol) {
        $result_array = null;
        $query = "SELECT 
    modulos.descripcion AS descripcionModulo,
    paginas.id AS idPagina,
    paginas.titulo AS pagina,
    permisos.id AS idPermiso,
    permisos.btnsNew,
    permisos.btnsUpdate,
    permisos.btnsDelete
FROM
    paginas
        LEFT JOIN
    permisos ON (paginas.id = permisos.idPaginas)
        INNER JOIN
    modulos ON (paginas.idModulos = modulos.id)
        INNER JOIN
    roles ON (roles.id= permisos.idRoles)
WHERE
    idModulos =$idModulo and idRoles=$idRol";
        $result = $conexion->query($query);
        while ($row = $result->fetch_assoc()) {

            $result_array[] = $row;
        }
        return $result_array;
    }

}

//
?>
<?php


