
const urlValidarSesion = 'http://localhost:3000/login/validar_sesion.php'


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

/**
 * Permite combinar de forma intercalada los caracteres de dos strings
 * @param {*} string1 
 * @param {*} string2 
 */
function mezclarStrings(string1, string2) {
    let long=0
    let resultado = ""
    let complemento = ""
    if (string1.length > string2.length) {
        long = string2.length
        complemento = string1.slice(long)
    } else {
        long = string1.length
        complemento = string2.slice(long)
    }

    for (let index = 0; index < long; index++) {
        resultado += string1[index]+string2[index]
    } 
    //console.log(resultado)
    resultado+=complemento
    return resultado
}


/**
 * Crea una cookie con el nombre, valor, y caducidad especificados
 * @param {0} nombre nombre de la cookie
 * @param {*} valor valor asignado a la cookie
 * @param {*} caducidad_minutos tiempo en minutos en que la cookie caducará
 */
function crearCookie(nombre, valor, caducidad_minutos) {
    const d = new Date();
    d.setTime(d.getTime() + (caducidad_minutos*60*1000));
    let expira = "expires="+ d.toUTCString();
    document.cookie = nombre + "=" + valor + ";" + expira + ";path=/;SameSite=None; Secure";
  }


  /**
   * Permite obtener el contenido de una cookie mediante su nombre
   * @param {*} nombre el nombre de la cookie
   * @returns el valor de la cookie
   */
  function verCookie(nombre) {
    let nombreC = nombre + "=";
    let cookieDecodificada = decodeURIComponent(document.cookie);
    let listaCookies = cookieDecodificada.split(';');

    for(let i = 0; i <listaCookies.length; i++) {
      let c = listaCookies[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(nombreC) == 0) {
        return c.substring(nombreC.length, c.length);
      }
    }
    return "";
  }

  /**
   * Fuerza la caducidad de una cookie
   * @param {*} nombre 
   */
  function borrarCookie(nombre) {
    let valor = verCookie(nombre)
    document.cookie = nombre + "="+valor+";expires='Thu, 01 Jan 1970 00:00:00 UTC';path=/;SameSite=None; Secure";
  }

  /**
   * Valida la sesion con el servidor
   * @returns la respuesta del servidor 
   */
  async function validarSesion() {
    const usuario={
        nombre:verCookie("usuario")
    }
    
    const sesion = {
        id:verCookie("sesion")
    }

    const datos={
        sesion:sesion,
        usuario:usuario
    }

    enviarAlServidor(datos,urlValidarSesion)
    .then(res=>{
      //console.log(res)
      if (res.Respuesta.estado == "OK") {
        crearCookie("usuario",usuario.nombre,30)
        crearCookie("sesion",sesion.id,30)
        
      } else {
        borrarCookie("usuario")
        borrarCookie("sesion")
        
      }
      //return res
    })
}

async function cerrarSesion(params) {
  const usuario={
    nombre:verCookie("usuario")
  }

  const datos={
    usuario:usuario
  }

  enviarAlServidor(datos,urlValidarSesion)
  .then(res=>{
    //console.log(res)
    if (res.Respuesta.estado == "OK") {
      crearCookie("usuario",usuario.nombre,30)
      crearCookie("sesion",sesion.id,30)
      
    } else {
      borrarCookie("usuario")
      borrarCookie("sesion")
      
    }
  //return res
})
}


async function enviarAlServidor(datos,url) {
    resultado = await fetch(url, { //'http://localhost:3000/login/login.php', {
    method: 'POST',
    headers: {
        'Accept': 'application/json, text/plain, */*',
        'Content-Type': 'application/json'
        },
    body: JSON.stringify(datos)
    })
    .then(res => res.json())
    return await resultado
}