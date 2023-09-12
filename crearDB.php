<?php 

    include_once "./base/basededatos.php";
    //print_r( count($_POST));
    $estado = "";
    $archivos = glob("./*.sql");
    
    


    if (count($_POST) < 1) {
        $estado = "Esperando datos..."; 
    }
    else {
        $estado="Ejecutando...";

        $servidor=$_POST["txtServidor"];
        $usuario=$_POST["txtUsuario"];
        $pass=$_POST["txtPass"];
        $archivo=$_POST["archivoSQL"];

        $archivoAbierto = fopen($archivo, 'r');
        $contenido="";
        if ($archivoAbierto) {
            $contenido = fread($archivoAbierto, filesize($archivo));
            fclose($archivoAbierto);
        }

        $bdd = new BaseDeDatos;
        $bdd->iniciarConexion($servidor,$usuario,$pass,null);
        $sentencia=$bdd->conexion->prepare($contenido);
        $bdd->conexion->execute();


        echo("$contenido");
    }



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crearDB</title>
</head>

<style>
    html,body{
        width: 100%;
        height: 100%;
        margin: 0px;
        background-color: #166ff5;
        font-family: Arial, Helvetica, sans-serif;
    }

    .columna{
        height: 100%;
    }

    .fila{
        width: 100%;
    }

    #contenido{
        position: relative;
        width: calc((100% / 3)*2);
        left: calc(100% / 6);
        right: auto;
    }

    .titulos{
        background-color: #d2f7d7;
        text-align: center;
        border-radius: 15px;
    }

    .titulos h2{
        text-align: left;
        margin-left: 5%;
    }

    .contenidos{
        background-color: white;
        border-radius: 15px;
    }

    .contenidos p{
        position: relative;
        left: 15%;
        
    }

    #uno table{
        width: 50%;
        margin-left: 20%;
    }

    #uno table input{
        width: 100%;
    }
</style>

<body>
    <div id="contenido" class="columna">
        <div class="fila titulos" id="titulo" >
            <h1>Creador de Bases de Datos</h1>
        </div>
        <div class="fila titulos">
            <h2>Estado:</h2>
        </div>
        <div class="fila contenidos" id="estado">
            <p>
                <?php echo("$estado") ?>
            </p>
        </div>
        <div class="fila titulos">
            <h2>Datos del Servidor:</h2>
        </div>
        <form action="" method="post">
            <div class="fila contenidos" id="uno">
                <table>
                    <tr>
                        <td>
                            <label for="txtServidor">IP/Ubicación del Servidor:</label>
                        </td>
                        <td>
                            <input type="text" name="txtServidor" id="txtServidor">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="txtUsuario">Usuario del Servidor:</label>
                        </td>
                        <td>
                            <input type="text" name="txtUsuario" id="txtUsuario">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="txtPass">Contraseña:</label>
                        </td>
                        <td>
                            <input type="password" name="txtPass" id="txtPass">
                        </td>
                    </tr>
                </table>
            </div>

            <div class="fila titulos">
                <h2>Archivo SQL para crear la Base de Datos:</h2>
            </div>
           
            <div class="fila contenidos" id="dos">
                <p>
                    <?php
                        foreach ($archivos as $archivo) {
                            if (is_file("./$archivo")) {
                                //echo('<p>');
                                echo("<input type='radio' name='archivoSQL' id='$archivo' value='$archivo'> ");
                                echo("<label for='$archivo'>$archivo</label>");
                                //echo('</p>');
                                echo('<br>');
                                
                            }
                        } 
                    ?>
                 </p>
            </div>

            <div class="fila contenidos">
                <input type="submit" value="Ejecutar">
            </div>
            
        </form>
        

    </div>
</body>
</html>