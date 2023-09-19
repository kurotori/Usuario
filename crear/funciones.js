//Obtener los elementos requeridos del html
const formRegistro = document.getElementById("datosUsuario");
const btnEnviar = document.getElementById("btnEnviar");
const divEstado = document.getElementById("estado");
//const divDatos = document.getElementById("datos");
//const listaP = document.getElementById("listaP")

//Se agrega un eventListener
btnEnviar.addEventListener("click",prepararDatos)

//Ejecuta la función de envío de datos
function prepararDatos() {
    enviarDatos(formRegistro)
}

/**
 * Envía los datos del formulario mediante una solicitud al servidor
 * @param {*} formulario 
 */
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
    })
    .then(res => res.json())
    .then(res => {
        
        clave_pub=res.Respuesta.datos.clave_pub
        password=formulario['passUsuario'];
        passConClave= mezclarStrings(password,clave_pub)//password+'-'+clave_pub
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
        })
        .then(res => res.json())
        .then(res => {
            console.log(res)
            const pEstado = document.createElement("p")
            pEstado.innerHTML=res.Respuesta.estado+": "+res.Respuesta.datos.mensaje
            divEstado.appendChild(pEstado)
        })
    });
}


/**
 * Esta función contiene elementos de ejemplo para el desarrollo de funciones similares
 * @param {*} formulario 
 */
function enviarDatos2(formulario) {

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
    })
    .then(res => res.json())
    .then(res => {
        let listado = res.Respuesta.datos
        listado.forEach(producto => {
            const pProducto = document.createElement("li")
            const imgProd = document.createElement("img")
            const divImagen = 
            imgProd.src = "ruta/fija/"+producto.imagen
            pProducto.classList.add("nombreClase")
            
            pProducto.innerText = producto.producto + " - $"+ producto.precio
            divDatos.appendChild(listaP)
        });
    })
}