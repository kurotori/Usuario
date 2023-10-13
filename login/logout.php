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
    
    //Pasamos los datos del objeto genérico a los objetos correspondientes
    $usuario->nombre = $objetoJson->usuario->nombre;

    $respuesta = cerrar_sesiones($usuario);//validSesion($usuario,$sesion);

    respuestaJSON($respuesta);

}
else {
    accesoInadecuado();
}

/** Funciones **/
function cerrar_sesiones(Usuario $usuario){
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
        $consulta="call cerrar_sesiones(?)";
        
        //Con el método 'prepare' de la conexión para declarar un objeto sentencia
        $sentencia = $bdd->conexion->prepare($consulta);
        
        //Declaramos variables para los términos de búsqueda
        $termino1 = $usuario->nombre;
        
        //Con el método bind_param del objeto sentencia, añadimos los términos a los parámetros de la consulta 
        $sentencia->bind_param("s",$termino1);
        //  bind_param requiere un string con caracteres que indique los tipos de los datos a agregar a los parámetros
        //      i - int, números enteros
        //      d - double, número con decimales
        //      s - string, textos, fechas, otros datos semejantes
        //      b - blob, paquetes de datos, que se envían en forma fragmentaria, en paquetes

        //Ejecutamos la sentencia con el método 'execute'
        $sentencia->execute();

        //Declaramos un objeto 'resultado' para contener las filas modificadas 
        $resultadoBD= $bdd->conexion->affected_rows;
        //print_r($resultadoBD);
        if ($resultadoBD >= 0) {
            $respuesta->datos = new stdClass; 
            $respuesta->estado="OK";
            $respuesta->datos->mensaje="Sesión Cerrada";
        }
        else {
            $respuesta->estado="ERROR";
            $respuesta->datos->mensaje="Ocurrió un error";
        }
                //$respuesta->datos->sesiones=$sesiones_validas;
    }
    
    $bdd->cerrarConexion();

    return $respuesta;
}



 ?>