const formRegistro = document.getElementById("datosUsuario");
const btnEnviar = document.getElementById("btnEnviar");

btnEnviar.addEventListener("click",prepararDatos)

function prepararDatos() {
    enviarDatos(formRegistro)
}

function enviarDatos(formulario) {

    let datos = {
        nombre:formulario['nombreUsuario'].value
    }

    let usuario = {
        usuario:datos
    }
    //console.log(usuario);

    fetch('http://localhost:3000/crear/crear.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(usuario)
    }).then(res => res.json())
    .then(res => {
        clave_pub=res.Respuesta.datos.clave_pub
        password=formulario['passUsuario'];
        passConClave=password+'-'+clave_pub
        hashPass=generarHash(passConClave)

        datos={
            nombre:formulario['nombreUsuario'].value,
            hash_contra:hashPass
        }

        usuario={
            usuario:datos
        }

        fetch('http://localhost:3000/crear/crear.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(usuario)
        }).then(res => res.json()).then(res => console.log(res))
    });
}


function name(params) {
    
}




/**
 * Permite generar un hash con la contraseña proporcionada por el usuario
 * @param {*} password La contraseña del usuario.
 * @returns El hash de la contraseña proporcionada.
 */
function generarHash(password) {
    //1 - Creamos un objeto de hasheo
    var hasheador = new jsSHA("SHA-512", "TEXT", {numRounds: 1});
    //2 - Agregamos el password al objeto de hasheo
    hasheador.update(password);
    //3 - Obtenemos el hash en formato hexadecimal...
    var hash = hasheador.getHash("HEX");
    // ...y devolvemos el mismo.
    return hash;
}