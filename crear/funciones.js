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
    console.log(usuario);

    fetch('http://localhost:3000/crear/index.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(usuario)
    }).then(res => res.json())
    .then(res => console.log(res));
}