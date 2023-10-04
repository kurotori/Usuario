<?php 
    include_once "../base/basededatos.php";
    include_once "../base/index.php";
    include_once "../base/usuario_general.php";

    /** EJECUCIÓN**/

    //1 - Recepción de los datos diréctamente del input
    $datos = file_get_contents('php://input');

    //2 - Si los datos recibidos NO son vacíos, procedemos a validarlos
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

        $respuesta = login($usuario);

        respuestaJSON($respuesta);

    }
    else {
        accesoInadecuado();
    }

    /** Funciones **/


    /**
     * Permite iniciar el proceso de login
     *
     * @param Usuario $usuario
     * @return void
     */
    function login(Usuario $usuario){
        $respuesta = new Respuesta;

        if (usuarioExiste($usuario)) {
            $respuesta->estado="OK";
            $datosUsuario = buscarDatosUsuario($usuario);

            $respuesta->datos=new stdClass;

            if ($usuario->hash_contra==null) {
        
                $respuesta->datos->clave_pub = $datosUsuario->clave_pub;
            }
            else {
                //$usuario->hash_contra;  -->Viene del frontend
                //$datosUsuario->clave_priv  --> de BDD
                //$datosUsuario->hash_contra --> de BDD

                $hash_contra_usuario = hashear($usuario->hash_contra, $datosUsuario->clave_priv);
                //echo($hash_contra_usuario);
                ////$respuesta->datos->cp=$datosUsuario->clave_priv;
                //$respuesta->datos->hash_contra_usuario = $hash_contra_usuario;
                //$respuesta->datos->hash_contra = $datosUsuario->hash_contra;
                
                //$respuesta->datos->hc_enviado=$usuario->hash_contra;

                if ( strstr($datosUsuario->hash_contra,$hash_contra_usuario) ) {
                    $respuesta->estado="OK";
                    //$respuesta->datos->R="Contraseña Correcta";
                    $respuesta->datos->id_sesion = loguearUsuario($usuario);
                } else {
                    $respuesta->estado="ERROR";
                    $respuesta->datos-> mensaje="Contraseña Incorrecta";
                }
                
            }

        } else {
            $respuesta->estado="ERROR";
            $respuesta->datos="El usuario no existe";
        }

        return $respuesta;
    }


    /**
     * Permite obtener los datos 
     *
     * @param Usuario $usuario
     * @return void
     */
    function buscarDatosUsuario(Usuario $usuario) {
        $datosUsuario=new Usuario;
        
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
            $consulta="select clave_pub,clave_priv,hashContra from usuarios.usuario where nombre=?";
            
            //Con el método 'prepare' de la conexión para declarar un objeto sentencia
            $sentencia = $bdd->conexion->prepare($consulta);
            
            //Declaramos variables para los términos de búsqueda
            $termino = $usuario->nombre;
            
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
                    $datosUsuario->clave_pub = $fila["clave_pub"];
                    $datosUsuario->clave_priv = $fila["clave_priv"];
                    $datosUsuario->hash_contra = $fila["hashContra"];
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

        return $datosUsuario;
    }



    /**
     * Crea una sesión, la asocia al nombre de usuario y retorna el ID de dicha sesión
     */
    function loguearUsuario(Usuario $usuario){
        $respuesta=0;
        
        $bdd = new BaseDeDatos;

        $credenciales = verCredenciales();

        $bdd->iniciarConexion(
            $credenciales[0],
            $credenciales[1],
            $credenciales[2],
            $credenciales[3]
        );

        
        if ($bdd->estado == "OK") {
            $consulta="call iniciar_sesion(?)";
            $sentencia = $bdd->conexion->prepare($consulta);
            $termino = $usuario->nombre;
            $sentencia->bind_param("s",$termino);

            $sentencia->execute();

            //Declaramos un objeto 'resultado' para  
            $resultadoBD= $sentencia->get_result();

            if ($resultadoBD->num_rows > 0) {
                foreach($resultadoBD as $fila){
                    $respuesta = $fila["id_sesion"];
                }
            }
        }
        
        $bdd->cerrarConexion();

        return $respuesta;

    }
 ?>