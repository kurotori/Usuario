<?php 

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
?>