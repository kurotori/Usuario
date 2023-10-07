const formDatosLogin = document.getElementById("datosLogin")
const btnLogin = document.getElementById("btnLogin")
const divEstado = document.getElementById("estado")

const urlLogin = 'http://localhost:3000/login/login.php'
const urlValidarSesion = 'http://localhost:3000/login/validar_sesion.php'


btnLogin.addEventListener("click",ejecutarLogin)


function ejecutarLogin() {
    divEstado.innerText=""

    const datos={
        nombre:formDatosLogin["nombreUsuario"].value,
        pass:null
    }
    
    const usuario = {
        usuario:datos
    }
    //console.log(usuario);
    
    enviarAlServidor(usuario,urlLogin)
    .then(res => {
        let estado = res.Respuesta.estado
        
        if (estado=="ERROR") {
            divEstado.innerText = estado + ": " + res.Respuesta.datos 

        } else if (estado=="OK") {

            //console.log(res)
            let clave_pub = res.Respuesta.datos.clave_pub
            let password = formDatosLogin["passUsuario"].value

            passConClave= mezclarStrings(password,clave_pub)
            hashPass=generarHash(passConClave)
            
            //console.log(hashPass)

            datos.nombre=formDatosLogin['nombreUsuario'].value
            datos.hash_contra=hashPass
            
            usuario.usuario=datos

            enviarAlServidor(usuario,urlLogin )
            .then(res=>{
                console.log(res.Respuesta)
                if (res.Respuesta.estado=="ERROR") {
                    divEstado.innerText = res.Respuesta.estado + ": " + res.Respuesta.datos.mensaje
                } 
                else if(res.Respuesta.estado=="OK") {
                    crearCookie("usuario",datos.nombre,30)
                    crearCookie("sesion",res.Respuesta.datos.id_sesion,30)
                    divEstado.innerText = "Hola, "+datos.nombre
                }
            })


        }
    })

}

function validarSesion() {
    
}


async function enviarAlServidor(usuario,url) {
    resultado = await fetch(url, { //'http://localhost:3000/login/login.php', {
    method: 'POST',
    headers: {
        'Accept': 'application/json, text/plain, */*',
        'Content-Type': 'application/json'
        },
    body: JSON.stringify(usuario)
    })
    .then(res => res.json())
    return await resultado
}

async function obtenerIp() {
    respuesta=await fetch("https://api.ipify.org?format=json")
    .then(res => res.json())
    .then(res => {return res})
}

async function verIp() {
    return obtenerIp()
}

