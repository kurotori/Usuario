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
        //print_r($objetoJson);
        //Creamos un nuevo objeto para contener los datos de la sesion y del usuario
        $usuario = new Usuario;
        $sesion = new Sesion;
        
        //Pasamos los datos del objeto genérico a los objetos correspondientes
        $usuario->nombre = $objetoJson->usuario->nombre;
        $sesion->id = $objetoJson->sesion->id;
        

        $respuesta = validarSesion($usuario,$sesion);

        respuestaJSON($respuesta);

    }
    else {
        accesoInadecuado();
    }

    /** Funciones **/
    function validarSesion(Usuario $usuario, Sesion $sesion){
        $respuesta = new Respuesta;

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
            $consulta="call validar_sesion(?,?)";
            
            //Con el método 'prepare' de la conexión para declarar un objeto sentencia
            $sentencia = $bdd->conexion->prepare($consulta);
            
            //Declaramos variables para los términos de búsqueda
            $termino1 = $usuario->nombre;
            $termino2 = $sesion->id;
            
            //Con el método bind_param del objeto sentencia, añadimos los términos a los parámetros de la consulta 
            $sentencia->bind_param("si",$termino1,$termino2);
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
                $respuesta->datos = new stdClass; 
                foreach($resultadoBD as $fila){
                    $sesiones_validas = $fila['cant_sesiones'];
                    
                    if ($sesiones_validas==1) {
                        $respuesta->estado="OK";
                        $respuesta->datos->mensaje="Sesión Válida: $termino2 de $termino1";
                    }
                    else {
                        $respuesta->estado="ERROR";
                        $respuesta->datos->mensaje="Sesión NO Válida";
                    }
                    //$respuesta->datos->sesiones=$sesiones_validas;
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

        return $respuesta;
    }

?>