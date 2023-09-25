const formDatosLogin = document.getElementById("datosLogin")
const btnLogin = document.getElementById("btnLogin")
const divEstado = document.getElementById("estado")

btnLogin.addEventListener("click",ejecutarLogin)


function ejecutarLogin() {


    const datos={
        nombre:formDatosLogin["nombreUsuario"].value,
        pass:null
    }
    console.log(datos)
    let usuario = {
        usuario:datos
    }
    //console.log(usuario);
    
    fetch('http://localhost:3000/login/login.php', {
        method: 'POST',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(usuario)
    })
    .then(res => res.json())
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

            datos={
                nombre:formDatosLogin['nombreUsuario'].value,
                hash_contra:hashPass
            }
    
            usuario={
                usuario:datos
            }


        }
    })

}