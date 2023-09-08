create table usuario(
    nombre varchar(25) not null unique primary key,
    clave_pub varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci default null,
    clave_priv varchar(100) default null,
    hashContra varchar(120) default null
);


select count(*) from usuario where nombre="???????";