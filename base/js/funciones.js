



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