<?php


    include_once "../base/usuario_general.php";
    include_once "../credenciales/bdd.php";
    include_once "../base/basededatos.php";
    include_once "../base/index.php";
    


    /* --- EJECUCIÓN --- */


//1 - Recepción de los datos diréctamente del input
$datos = file_get_contents('php://input');

//2 - Si los datos recibidos no son vacíos, procedemos a validarlos
if ( ! empty($datos) ) {
    //Validación de los datos
    $datosValidados = validarPost($datos);

    //Decodificación de los datos: convertimos el string json en un objeto de "clase genérica"
    $objetoJson = json_decode("$datosValidados");

    //Creamos un nuevo objeto para contener los datos del usuario
    $usuario = new Usuario;
    
    //Pasamos los datos del objeto genérico al objeto usuario
    $usuario->nombre = $objetoJson->usuario->nombre;
    
    //Chequeamos si el objeto contiene un hash de contraseña
    if (isset($objetoJson->usuario->hash_contra)) {
        $usuario->hash_contra = $objetoJson->usuario->hash_contra;
    }
    else {
        $usuario->hash_contra = null;
    }

    $respuesta = crearUsuario($usuario);

    respuestaJSON($respuesta);

}
else {
    accesoInadecuado();
}

/** FUNCIONES **/


    /**
     * Permite guiar el proceso de creación y registro de usuarios en el
     * sistema.
     * @param Usuario $usuario Un objeto de clase usuario con los datos de registro
     * @return $resultado Un objeto de clase Respuesta con los datos del registro o del error
     */
    function crearUsuario(Usuario $usuario) {
        $resultado=new Respuesta;

        $chequeo=usuarioExiste($usuario);
        
        if ( ! $chequeo ) {
            $clave_pub=crearSal();
            
            $usuario->clave_pub="$clave_pub";
            
            $resultado=guardarUsuario($usuario);
        }
        else{
            
            if (registroIncompleto($usuario)) {
                $hashContra_Base = $usuario->hash_contra;
                $usuario->clave_priv = crearSal();
                $hashContra = hashear($hashContra_Base,$usuario->clave_priv);
                $usuario->hash_contra = $hashContra;
                $resultado = completarRegistro($usuario);
            } else {
                $resultado->estado="ERROR";
                $resultado->datos=new stdClass;
                $resultado->datos->mensaje="Ya existe";
            }
            
        }
        return $resultado;
    }


    /**
     * Determina si el registro de un usuario es incompleto, para pasar a la fase 2 del registro del mismo.
     *
     * @param Usuario $usuario
     * @return void
     */
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
            $consulta="select count(*) as conteo from usuarios.usuario where nombre like ? and clave_priv='null'";

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


    /**
     * Guarda los datos de la fase 1 del registro de un usuario nuevo.
     * Se almacenan solo el nombre y se genera la clave pública.
     *
     * @param Usuario $usuario
     * @return void un objeto Respuesta con la clave pública del usuario. 
     */
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


    /**
     * Completa el registro del registro del usuario (fase 2)
     *
     * @param Usuario $usuario
     * @return void
     */
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
            "UPDATE usuario set clave_priv=?, hashContra=?
            where nombre=?";

            $sentencia = $bdd->conexion->prepare($consulta);
            $nombre=$usuario->nombre;
            $clave_priv=$usuario->clave_priv;
            $hashContra=$usuario->hash_contra;

            $sentencia->bind_param("sss", $clave_priv, $hashContra ,$nombre);
            $sentencia->execute();

            if ( $sentencia->affected_rows>0 ) {
                $resultado->estado = "OK";
                $resultado->datos = new stdClass;

                $resultado->datos->mensaje = "usuario creado: fase 2";
                
            }
            else {
                $resultado->estado = "ERROR";
                $resultado->datos = "ocurrió algo:".$sentencia->error;
            }

        }
        return $resultado;
    }








 ?>