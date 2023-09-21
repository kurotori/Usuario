<?php 
include_once "../credenciales/bdd.php";
include_once "basededatos.php";



/**
 * Permite crear una sal de 100 caracteres.
 */
function crearSal(){
    $sal="";
    $caracteres="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $tope=rand(80,100);
    for ($i=0; $i < $tope; $i++) { 
        $num=rand(0,strlen($caracteres)-1);
        $letra=$caracteres[$num];
        $sal="$sal"."$letra";
    }
    return $sal;
}

/**
 * Permite hashear un dato utilizando una sal alfanumérica
 * @param $dato El dato a hashear
 * @param $sal La sal a utilizar para el hasheo
 */
function hashear($dato,$sal){
    $texto="$dato"."$sal";
    $limiteOp = SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE;
    $limiteMem = SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE;
    $hash=sodium_crypto_pwhash_str($texto,$limiteOp,$limiteMem);
    return $hash;
}


/**
     * Chequea si el usuario ya existe en el sistema.
     *
     * @param Usuario $usuario
     * @return void
     */
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
            $bdd->cerrarConexion();
        }
        else {
            //$respuesta->estado=$basededatos->estado;
            //CAMBIAR ESTO PARA PRODUCCIÓN!!!!!!!
            //$respuesta->datos=$basededatos->mensaje;
            
        }
        
        

        return $resultado;
    }
?>