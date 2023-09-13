drop database if exists usuarios;
create schema usuarios;

create table usuarios.usuario(
    nombre varchar(25) not null unique,
    clave_pub varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default null,
    clave_priv varchar(100) default null,
    hashContra varchar(120) default null,
    primary key (nombre)
);


/*select count(*) from usuario where nombre="???????"*/