const formDatosLogin = document.getElementById("datosLogin")
const btnLogin = document.getElementById("btnLogin")
const divEstado = document.getElementById("estado")

btnLogin.addEventListener("click",ejecutarLogin)


function ejecutarLogin() {


    const datos={
        nombre:formDatosLogin["nombreUsuario"],
        pass:null
    }

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
            divEstado.innerText
        } else {
            
        }
    })

}