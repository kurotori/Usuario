<?php

    //CONFIGURACIÓN DE **P R U E B A S**
    function verCredenciales(){
        $usuarioBdD = "estudiante";
        $contraseniaBdD = "estudiante";
        $servidorBdD = "localhost:3306";
        $baseDeDatos = "usuarios";
        
        $credenciales = array($servidorBdD,$usuarioBdD,$contraseniaBdD,$baseDeDatos);
        return $credenciales; 
    }
   

 ?>