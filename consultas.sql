    drop database if exists usuarios;
    create schema usuarios;

    create table usuarios.usuario(
        nombre varchar(25) not null unique,
        clave_pub varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default null,
        clave_priv varchar(100) default null,
        hashContra varchar(200) default null,
        primary key (nombre)
    );

    create table usuarios.sesion(
        id INT not null unique auto_increment primary key,
        fecha timestamp not null default CURRENT_TIMESTAMP,
        fecha_cad timestamp not null default date_add(current_timestamp, interval 30 minute),
        estado enum('abierta','cerrada') not null default 'abierta'
    );

    create table usuarios.inicia(
        sesion_id INT not null unique primary key,
        usuario_nombre varchar(25) not null,
        ip varchar(40)
    );


    alter table usuarios.inicia
    add constraint fk_usuario_inicia
    foreign key (usuario_nombre)
    references usuario(nombre)
    on update cascade
    on delete no action;

    alter table usuarios.inicia
    add constraint fk_inicia_sesion
    foreign key (sesion_id)
    references sesion(id)
    on update cascade
    on delete no action
    ;


    /* 1 - Cambio el caracter delimitador de consultas */
    delimiter //

    /* 2 - OPCIONAL PERO RECOMENDADO: 
        Borro el procedimiento si ya existe */
    drop procedure if exists usuarios.iniciar_sesion//

    /* 3 - Creo el procedimiento indicando su nombre... */
    create procedure usuarios.iniciar_sesion(
        /* ...y sus datos de entrada (parámetros) */
        IN usuario_nom varchar(25)
    )
    BEGIN
        /* 4 - Al comienzo del procedimiento declaro cualquier variable
        inicial que precise para la ejecución del mismo*/
        declare nueva_sesion_id int;
        call cerrar_sesiones(usuario_nom);

        /* 5 - Ingreso las consultas del procedimiento,
        vinculándolas a los parámetros*/
        insert into usuarios.sesion() values();
        
        /* 6 - Para pasar datos a una variable auxilar puedo usar el
        comando "select [valor/variable/expresión] into variable" 
        o el comando "set variable = [valor/variable/expresión]"*/    
        select last_insert_id() into nueva_sesion_id;
        
        insert into usuarios.inicia(usuario_nombre, sesion_id)
        values(usuario_nom, nueva_sesion_id);

        /* 7 - Para entregar datos tras la ejecución, puedo utilizar
        el comando "select" al final del procedimiento */
        select nueva_sesion_id as "id_sesion";
    END//

    /* 8 - Tras la declaración del procedimiento, restauro el delimitador */
    delimiter ;


    /*select count(*) from usuario where nombre="???????"*/

    delimiter //

    drop procedure if exists usuarios.cerrar_sesiones//

    create procedure usuarios.cerrar_sesiones(
        /* ...y sus datos de entrada (parámetros) */
        IN usuario_nom varchar(25)
    )
    BEGIN
        update usuarios.sesion
        set estado='cerrada'
        where id in
        (select sesion_id from usuarios.inicia
        where usuario_nombre=usuario_nom);
        
    END//

    delimiter ;