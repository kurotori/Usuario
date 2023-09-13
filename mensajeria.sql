create table usuario(
    nombre varchar(20) unique not null,
    nombreP varchar(30),
    apellidoP varchar(50),
    primary key(nombre)
)

create table mensajeria(
    mensaje varchar(255),
    ID int not null unique auto_increment,
    usuario_nombre_E varchar(20),
    usuario_nombre_R varchar(20),
    primary key(ID)  
)

insert into mensajeria
(mensaje, usuario_nombre_E, usuario_nombre_R)
values
(?,?,?);

select 
mensaje,
(select concat_ws(" ",nombreP, apellidoP) 
from usuario where nombre = usuario_nombre_E) as remitente
from mensajeria
where usuario_nombre_R = "fulano";