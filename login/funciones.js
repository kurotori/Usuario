const formDatosLogin = document.getElementById("datosLogin")
const btnLogin = document.getElementById("btnLogin")
const btnLogout = document.getElementById("btnLogout")
const divEstado = document.getElementById("estado")

const divInicioSesion = document.getElementById("inicioSesion")
const divCerrarSesion = document.getElementById("cerrarSesion")



btnLogin.addEventListener("click",ejecutarLogin)
btnLogout.addEventListener("click",cerrarSesion)

/**
 * Ejecuta una función una vez que se carga toda la página
 */
document.addEventListener("DOMContentLoaded", function(event) { 
    verificarLogin()
  });
/** */  

/**
 * Ejecuta el proceso de inicio de sesión
 */
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
                    location.reload()
                }
            })
        }
    })
}

/**
 * Verifica si existe una sesión en las cookies y valida las mismas con el servidor
 */
async function verificarLogin() {
    respuesta = await validarSesion()
    .then(()=>{
        //console.log(verCookie("usuario").length)
        if((verCookie("usuario").length > 0)&&(verCookie("sesion").length > 0)) {
            divCerrarSesion.style.display="block"
            divInicioSesion.style.display="none"
        } else {
            divCerrarSesion.style.display="none"
            divInicioSesion.style.display="block"
        }
    }
    )
}



async function obtenerIp() {
    respuesta=await fetch("https://api.ipify.org?format=json")
    .then(res => res.json())
    .then(res => {return res})
}

async function verIp() {
    return obtenerIp()
}

