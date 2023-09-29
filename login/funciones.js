const formDatosLogin = document.getElementById("datosLogin")
const btnLogin = document.getElementById("btnLogin")
const divEstado = document.getElementById("estado")

btnLogin.addEventListener("click",ejecutarLogin)


function ejecutarLogin() {


    let datos={
        nombre:formDatosLogin["nombreUsuario"].value,
        pass:null
    }
    
    let usuario = {
        usuario:datos
    }
    //console.log(usuario);
    
    enviarAlServidor(usuario)
    .then(res => {
        let estado = res.Respuesta.estado
        
        if (estado=="ERROR") {
            divEstado.innerText = estado + ": " + res.Respuesta.datos 

        } else if (estado=="OK") {

            console.log(res)
            let clave_pub = res.Respuesta.datos.clave_pub
            let password = formDatosLogin["passUsuario"].value
            passConClave= mezclarStrings(password,clave_pub)
            hashPass=generarHash(passConClave)
            console.log(hashPass)

            datos={
                nombre:formDatosLogin['nombreUsuario'].value,
                hash_contra:hashPass
            }
    
            usuario={
                usuario:datos
            }

            enviarAlServidor(usuario)
            .then(res=>{
                console.log(res.Respuesta)
            })


        }
    })

}



async function enviarAlServidor(usuario) {
    resultado = await fetch('http://localhost:3000/login/login.php', {
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
    return await fetch("https://api.ipify.org?format=json")
}

async function verIp() {
    return await obtenerIp.then(res => {console.log(res)})
}