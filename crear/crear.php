<?php

    include_once "funciones.php";
    include_once "../credenciales/bdd.php";
    include_once "../base/basededatos.php";
    include_once "../base/index.php";
    
    function crearUsuario(Usuario $usuario) {
        $resultado=new Respuesta;

        $chequeo=usuarioExiste($usuario);
        
        if ( ! $chequeo ) {
            $clave_pub=crearSal();
            
            $usuario->clave_pub="$clave_pub";

            $resultado=guardarUsuario($usuario);
        }
        else{
            $resultado->estado="ERROR";
            $resultado->datos="Ya existe";
        }
        return $resultado;
    }


    function usuarioExiste(Usuario $usuario) {
        $resultado=false;
        
        $bdd = new BaseDeDatos;

        $credenciales = verCredenciales();

        $bdd->iniciarConexion(
            $credenciales[0],
            $credenciales[1],
            $credenciales[2],
            $credenciales[3]
        );

        
        if ($bdd->estado == "OK") {
            
            //Si la conexión es correcta, declaramos la consulta con parámetros, indicados por los símbolos de pregunta ----------\/
            $consulta="select count(*) as conteo from usuarios.usuario where nombre like ?";

            //Con el método 'prepare' de la conexión para declarar un objeto sentencia
            $sentencia = $bdd->conexion->prepare($consulta);
            
            //Declaramos variables para los términos de búsqueda
            $termino = "%"."$usuario->nombre"."%";
            
            //Con el método bind_param del objeto sentencia, añadimos los términos a los parámetros de la consulta 
            $sentencia->bind_param("s",$termino);
            //  bind_param requiere un string con caracteres que indique los tipos de los datos a agregar a los parámetros
            //      i - int, números enteros
            //      d - double, número con decimales
            //      s - string, textos, fechas, otros datos semejantes
            //      b - blob, paquetes de datos, que se envían en forma fragmentaria, en paquetes

            //Ejecutamos la sentencia con el método 'execute'
            $sentencia->execute();

            //Declaramos un objeto 'resultado' para  
            $resultadoBD= $sentencia->get_result();

            if ($resultadoBD->num_rows > 0) {
                foreach($resultadoBD as $fila){
                    $cantUsuarios = $fila["conteo"];
                    if ($cantUsuarios>0) {
                        //echo("El usuario existe");
                        $resultado=true;
                    }
                    //else{
                      //  echo("El usuario no existe");
                    //}
                }
            }
            else{
                //$respuesta->datos = "No se encontraron resultados para la búsqueda";
            }
        }
        else {
            //$respuesta->estado=$basededatos->estado;
            //CAMBIAR ESTO PARA PRODUCCIÓN!!!!!!!
            //$respuesta->datos=$basededatos->mensaje;
        }
        
        $bdd->cerrarConexion();

        return $resultado;
    }



    function registroIncompleto(Usuario $usuario) {
        $resultado=false;
        
        $bdd = new BaseDeDatos;

        $credenciales = verCredenciales();

        $bdd->iniciarConexion(
            $credenciales[0],
            $credenciales[1],
            $credenciales[2],
            $credenciales[3]
        );

        
        if ($bdd->estado == "OK") {
            
            //Si la conexión es correcta, declaramos la consulta con parámetros, indicados por los símbolos de pregunta ----------\/
            $consulta="select count(*) as conteo from usuarios.usuario where nombre like ? and clave_priv is null";

            //Con el método 'prepare' de la conexión para declarar un objeto sentencia
            $sentencia = $bdd->conexion->prepare($consulta);
            
            //Declaramos variables para los términos de búsqueda
            $termino = "%"."$usuario->nombre"."%";
            
            //Con el método bind_param del objeto sentencia, añadimos los términos a los parámetros de la consulta 
            $sentencia->bind_param("s",$termino);
            //  bind_param requiere un string con caracteres que indique los tipos de los datos a agregar a los parámetros
            //      i - int, números enteros
            //      d - double, número con decimales
            //      s - string, textos, fechas, otros datos semejantes
            //      b - blob, paquetes de datos, que se envían en forma fragmentaria, en paquetes

            //Ejecutamos la sentencia con el método 'execute'
            $sentencia->execute();

            //Declaramos un objeto 'resultado' para  
            $resultadoBD= $sentencia->get_result();

            if ($resultadoBD->num_rows > 0) {
                foreach($resultadoBD as $fila){
                    $cantUsuarios = $fila["conteo"];
                    if ($cantUsuarios>0) {
                        //echo("El usuario existe");
                        $resultado=true;
                    }
                    //else{
                      //  echo("El usuario no existe");
                    //}
                }
            }
            else{
                //$respuesta->datos = "No se encontraron resultados para la búsqueda";
            }
        }
        else {
            //$respuesta->estado=$basededatos->estado;
            //CAMBIAR ESTO PARA PRODUCCIÓN!!!!!!!
            //$respuesta->datos=$basededatos->mensaje;
        }
        
        $bdd->cerrarConexion();

        return $resultado;
    }

    function guardarUsuario(Usuario $usuario){
        $resultado = new Respuesta;
        $bdd = new BaseDeDatos;

        $credenciales = verCredenciales();

        $bdd->iniciarConexion(
            $credenciales[0],
            $credenciales[1],
            $credenciales[2],
            $credenciales[3]
        );

        if ($bdd->estado == "OK") {
            $consulta = 
            "INSERT INTO usuario(nombre,clave_pub,clave_priv,hashContra)
            values (?,?,?,?)";
            $sentencia = $bdd->conexion->prepare($consulta);
            $nombre=$usuario->nombre;
            $clave_pub=$usuario->clave_pub;
            $clave_priv="null";
            $hashContra="null";

            $sentencia->bind_param("ssss",$nombre,
                                $clave_pub, $clave_priv, $hashContra);
            $res_sentencia=$sentencia->execute();

            if ( $sentencia->affected_rows>0 ) {
                $resultado->estado = "OK";
                $resultado->datos = new stdClass;

                $resultado->datos->mensaje = "usuario creado: fase 1";
                $resultado->datos->clave_pub = "$clave_pub";
            }
            else {
                $resultado->estado = "ERROR";
                $resultado->datos = "ocurrió algo:".$sentencia->error;
            }

        }
        return $resultado;
    }


    function completarRegistro(Usuario $usuario){
        $resultado = new Respuesta;
        $bdd = new BaseDeDatos;

        $credenciales = verCredenciales();

        $bdd->iniciarConexion(
            $credenciales[0],
            $credenciales[1],
            $credenciales[2],
            $credenciales[3]
        );

        if ($bdd->estado == "OK") {
            $consulta = 
            "UPDATE usuario set clave
            values (?,?,?,?)";
            $sentencia = $bdd->conexion->prepare($consulta);
            $nombre=$usuario->nombre;
            $clave_pub=$usuario->clave_pub;
            $clave_priv="null";
            $hashContra="null";

            $sentencia->bind_param("ssss",$nombre,
                                $clave_pub, $clave_priv, $hashContra);
            $res_sentencia=$sentencia->execute();

            if ( $sentencia->affected_rows>0 ) {
                $resultado->estado = "OK";
                $resultado->datos = new stdClass;

                $resultado->datos->mensaje = "usuario creado: fase 1";
                $resultado->datos->clave_pub = "$clave_pub";
            }
            else {
                $resultado->estado = "ERROR";
                $resultado->datos = "ocurrió algo:".$sentencia->error;
            }

        }
        return $resultado;
    }




/* --- EJECUCIÓN --- */


//Recepción de los datos diréctamente del input
$datos = file_get_contents('php://input');
//print_r($datos);
if ( ! empty($datos) ) {
        

    //Validación de los datos
    $datosValidados = validarPost($datos);

    //Decodificación de los datos: el string json se converte en un objeto genérico
    $objetoJson = json_decode("$datosValidados");
 
    //Creación de un objeto de clase Consulta para almacenar los datos específicos de la consulta
    $usuario = new Usuario;
    $usuario->nombre = $objetoJson->usuario->nombre;
    $usuario->hash_contra = $objetoJson->usuario->hash_contra;

   

    if (is_null($usuario->hash_contra)) {
        //print_r($usuario);
        $respuesta = crearUsuario($usuario);
        //print_r($respuesta);
    }
    //$datosConsulta->dato = $objetoJson->dato;

    //$respuesta=buscarLibro("$datosConsulta->dato");
    respuestaJSON($respuesta);
    
    //echo("$objetoJson->dato");
    //print_r($datosConsulta);
    //respuestaJSON($datosConsulta);
}
else {
    accesoInadecuado();
}



/*
$usuario->nombre="gomezete";

$prueba=crearUsuario($usuario);
print_r($prueba);
//usuarioExiste($usuario);
*/



 ?>