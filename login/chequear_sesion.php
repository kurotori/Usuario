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

        //Creamos un nuevo objeto para contener los datos de la sesion y del usuario
        $usuario = new Usuario;
        $sesion = new Sesion;
        
        //Pasamos los datos del objeto genérico a los objetos correspondientes
        $usuario->nombre = $objetoJson->usuario->nombre;
        $
        
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


?>