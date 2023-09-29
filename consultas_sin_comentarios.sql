drop database if exists usuarios;
create schema usuarios;

create table usuarios.usuario(
    nombre varchar(25) not null unique,
    clave_pub varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default null,
    clave_priv varchar(100) default null,
    hashContra varchar(120) default null,
    primary key (nombre)
);

create table usuarios.sesion(
    id INT not null unique auto_increment primary key,
    fecha timestamp not null default CURRENT_TIMESTAMP,
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



delimiter //


drop procedure if exists usuarios.iniciar_sesion//

create procedure usuarios.iniciar_sesion(

    IN usuario_nom varchar(25)
)
BEGIN

    declare nueva_sesion_id int;

    insert into usuario.sesion() values();
   
    select last_insert_id() into nueva_sesion_id;
    
    insert into usuario.inicia(usuario_nombre, sesion_id)
    values(usuario_nom, nueva_sesion_id);


    select nueva_sesion_id as "id_sesion";
END//

delimiter ;
