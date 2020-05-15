-- creacion de un usuario transmilenio
create user transmilenio identified by 123;
-- asignacion de permisos a usuario
grant all privileges to transmilenio;
----------------------------------------------------------------------------------------------
---- creacion de la tabla troncales 
create table troncales(
	id_troncal  	 		number(5)   	 	not null,
	nombre_troncal  		varchar2(50)		not null,
	letra_troncal			varchar2(2) 		not null,
	color_troncal			varchar2(50)		not null,
	activo_troncal 			varchar2(1)			not null,
    constraint tro_pk_id primary key (id_troncal)
);

alter table troncales add(
	constraint tro_ck_act check (activo_troncal in ('a' /*activo*/,'n'/*inactivo*/))
);

-- creacion de sequencia de troncales
create sequence troncales_seq start with 1 increment by 1 maxvalue 99999 minvalue 1;

-- creacion de trigger para autoincrementar el id de troncales
create or replace trigger tr_tro_autoid
before insert 
on troncales
for each row
begin
	select troncales_seq.nextval into :new.id_troncal from dual;
end;
----------------------------------------------------------------------------------------------
-- creacion de la tabla estaciones
create table estaciones(
	id_estacion  	 		number(5)   	 	not null,
	nombre_estacion  		varchar2(50)		not null,
	activo_estacion			varchar2(1)			not null,
    constraint est_pk_id primary key (id_estacion)
);

alter table estaciones add(
	constraint est_ck_act check (activo_estacion in ('a' /*activo*/,'n'/*inactivo*/))
);
-- creacion de sequencia de estaciones
create sequence estaciones_seq start with 1 increment by 1 maxvalue 99999 minvalue 1;

-- creacion de trigger para autoincrementar el id de estaciones
create or replace trigger tr_est_autoid
before insert 
on estaciones
for each row
begin
	select estaciones_seq.nextval into :new.id_estacion from dual;
end;
-----------------------------------------------------------------------------------------------
-- creacion de la entidad troncal_estacion
create table troncal_estacion(
	id_troncal_estacion		number(10)			not null,
	id_estacion  	 		number(5)   	 	not null,
	id_troncal  	 		number(5)   	 	not null,
	activo_troncal_estacion	varchar2(1)			not null,
	constraint troest_pk_id primary key (id_troncal_estacion)
);

-- creacion de los constraints establecidos para troncal_estacion
alter table troncal_estacion add (
    constraint troest_fk_ide foreign key (id_estacion) references estaciones (id_estacion),
	constraint troest_fk_idt foreign key (id_troncal) references troncales (id_troncal),
	constraint troest_ck_act check (activo_troncal_estacion in ('a' /*activo*/,'n'/*inactivo*/)),
	constraint troest_uni_tre unique (id_estacion,id_troncal)
);
-- creacion de sequencia de portales
create sequence troncal_estacion_seq start with 1 increment by 1 maxvalue 9999999999 minvalue 1;

-- creacion de trigger para autoincrementar el id de portales
create or replace trigger tst_por_autoid
before insert 
on troncal_estacion
for each row
begin
	select troncal_estacion_seq.nextval into :new.id_troncal_estacion from dual;
end;
------------------------------------------------------------------------------------------------
-- creacion de la tabla portales
create table portales(
	id_portal	  	 		number(5)   	 	not null,
	id_troncal  	 		number(5)   	 	not null,
	nombre_portal	  		varchar2(50)		not null,
	activo_portal			varchar2(1)			not null,
	constraint por_pk_id primary key (id_portal)
);
-- creacion de los  constraints establecidos para portales
alter table portales add (
	constraint por_fk_idt foreign key (id_troncal) references troncales (id_troncal),
	constraint por_ck_act check (activo_portal in ('a' /*activo*/,'n'/*inactivo*/))
);

-- creacion de sequencia de portales
create sequence portales_seq start with 1 increment by 1 maxvalue 99999 minvalue 1;

-- creacion de trigger para autoincrementar el id de portales
create or replace trigger tr_por_autoid
before insert 
on portales
for each row
begin
	select portales_seq.nextval into :new.id_portal from dual;
end;
-------------------------------------------------------------------------------------------------
-- creacion de la tabla plataformas
create table plataformas (
	id_plataforma			number(5)			not null,
	id_portal	  	 		number(5)   	 	not null,
	numero_plataforma  		number(2)   	 	not null,
	activo_plataforma		varchar2(1)			not null,
	constraint pla_pk_id primary key (id_plataforma)
);

--creacion de los constraints establecidos para plataformas 
alter table plataformas add (
	constraint pla_fk_idp foreign key (id_portal) references portales (id_portal),
	constraint pla_ck_act check (activo_plataforma in ('a' /*activo*/,'n'/*inactivo*/))
);

-- creacion de sequencia de plataformas
create sequence plataformas_seq start with 1 increment by 1 maxvalue 99999 minvalue 1;

-- creacion de trigger para autoincrementar el id de plataformas
create or replace trigger tr_pla_autoid
before insert 
on plataformas
for each row
begin
	select plataformas_seq.nextval into :new.id_plataforma from dual;
end;
---------------------------------------------------------------------------------------------------
--creacion de la tabla vagones 
create table vagones(
	id_vagon			number(5)			not null,
	id_plataforma		number(5)			null,
	id_troncal_estacion	number(10)          null,
	numero_vagon		number(2)			not null,
	activo_vagon		varchar2(1)			not null,
	constraint vag_pk_id primary key (id_vagon)
);

alter table vagones add(
	constraint vag_ck_act check (activo_vagon in ('a' /*activo*/,'n'/*inactivo*/))
);

-- creacion de sequencia de vagones
create sequence vagones_seq start with 1 increment by 1 maxvalue 99999 minvalue 1;

-- creacion de trigger para autoincrementar el id de vagones
create or replace trigger tr_vag_autoid
before insert 
on vagones
for each row
begin
	select vagones_seq.nextval into :new.id_vagon from dual;
end;
----------------------------------------------------------------------------------------------------
-- creacion de la tabla rutas
create table rutas(
	id_ruta				number(5)			not null,
	codigo_ruta			varchar2(5)			not null,
	activo_ruta			varchar2(1)			not null,
	constraint rut_pk_id primary key (id_ruta)
);

alter table rutas add(
	constraint rut_ck_act check (activo_ruta in ('a' /*activo*/,'n'/*inactivo*/))
);

-- creacion de sequencia de rutas
create sequence rutas_seq start with 1 increment by 1 maxvalue 99999 minvalue 1;

-- creacion de trigger para autoincrementar el id de rutas
create or replace trigger tr_rut_autoid
before insert 
on rutas
for each row
begin
	select rutas_seq.nextval into :new.id_ruta from dual;
end;
-----------------------------------------------------------------------------------------------------
-- creacion de la tabla paradas
create table paradas(
	id_vagon			number(5)			not null,
	id_ruta				number(5)			not null,
	orden				number(4)			not null,
	estado_parada		varchar2(1)			not null,
	constraint par_pk_id primary key (id_vagon,id_ruta)
);

-- creacion de los constraints establecidos para paradas
alter table paradas add (
    constraint par_fk_idv foreign key (id_vagon) references vagones (id_vagon),
	constraint par_fk_idr foreign key (id_ruta) references rutas (id_ruta),
	constraint par_ck_esp check (estado_parada in ('a'/*activa*/,'n'/*inactiva*/))
);
------------------------------------------------------------------------------------------------------
--creacion de la tabla tipo_bus
create table tipo_bus(
	id_tipo_bus			number(2)			not null,
	nombre_tipo			varchar2(50)		not null,
	color				varchar2(7)		not null,
	activo_tipo_bus		varchar2(1)			not null,
	constraint tpb_pk_id primary key (id_tipo_bus)
);

alter table tipo_bus add(
	constraint tib_ck_act check (activo_tipo_bus in ('a' /*activo*/,'n'/*inactivo*/))
);

-- creacion de sequencia de tipo bus
create sequence tipo_bus_seq start with 1 increment by 1 maxvalue 99 minvalue 1;

-- creacion de trigger para autoincrementar el id de tipo bus
create or replace trigger tr_tpb_autoid
before insert 
on tipo_bus
for each row
begin
	select tipo_bus_seq.nextval into :new.id_tipo_bus from dual;
end;
-------------------------------------------------------------------------------------------------------
-- creacion de la tabla buses
create table buses(
	id_bus				number(5)			not null,
	id_tipo_bus			number(2)			not null,
	placabus			varchar2(50)		not null,
	activo_bus			varchar2(1)			not null,
	constraint bus_pk_id primary key (id_bus)
);

-- creacion de los constraints de buses
alter table buses add (
    constraint bus_fk_idt foreign key (id_tipo_bus) references tipo_bus (id_tipo_bus),
	constraint bus_uni_ifp unique (placabus),
	constraint bus_ck_act check (activo_bus in ('a' /*activo*/,'n'/*inactivo*/))
);

-- creacion de sequencia de bus
create sequence bus_seq start with 1 increment by 1 maxvalue 99999 minvalue 1;

-- creacion de trigger para autoincrementar el id de bus
create or replace trigger tr_bus_autoid
before insert 
on buses
for each row
begin
	select bus_seq.nextval into :new.id_bus from dual;
end;
--------------------------------------------------------------------------------------------------------
-- creacion de la tabla horarios
create table horarios(
	id_horario			number(5)			not null,
	horario_inicio		timestamp			not null,
	horario_fin			timestamp			not null,
	dia					varchar2(10)		not null,
	activo_horario		varchar2(1)			not null,
	constraint hor_pk_id primary key (id_horario)
);

-- creacion de los constraints de horarios
alter table horarios add (
	constraint hor_uni_ifd unique (horario_inicio,horario_fin,dia),
	constraint hor_ck_act check (activo_horario in ('a' /*activo*/,'n'/*inactivo*/))
);
-- creacion de sequencia de horarios
create sequence horarios_seq start with 1 increment by 1 maxvalue 99999 minvalue 1;

-- creacion de trigger para autoincrementar el id de horarios
create or replace trigger tr_hor_autoid
before insert 
on horarios
for each row
begin
	select horarios_seq.nextval into :new.id_horario from dual;
end;
--------------------------------------------------------------------------------------------------------
-- creacion de la tabla asignacion_ruta_horario
create table asignacion_ruta_horario(
	id_asignacion_ruta		number(15)			not null,
	id_ruta					number(5)			not null,
	id_bus					number(5)			not null,
	id_horario				number(5)			not null,
	fecha_inicio_operacion	date				not null,
	fecha_fin_operacion		date				null,
	activo_asignacion		varchar2(1)			not null,
	constraint arh_pk_id primary key (id_asignacion_ruta)
);
-- creacion de los constraints establecidos para asignacion_ruta_horario
alter table asignacion_ruta_horario add (
    constraint arh_fk_idr foreign key (id_ruta) references rutas (id_ruta),
	constraint arh_fk_idb foreign key (id_bus) references buses (id_bus),
	constraint arh_fk_idh foreign key (id_horario) references horarios (id_horario),
	constraint arh_ck_act check (activo_asignacion in ('a' /*activo*/,'n'/*inactivo*/)),
	constraint arh_uni_ids unique (id_ruta,id_bus,id_horario,fecha_inicio_operacion)
);

-- creacion de sequencia de asignacion_ruta_horario
create sequence asignacion_ruta_hor_seq start with 1 increment by 1 maxvalue 999999999999999 minvalue 1;

-- creacion de trigger para autoincrementar el id de asignacion_ruta_horario
create or replace trigger tr_arh_autoid
before insert 
on asignacion_ruta_horario
for each row
begin
	select asignacion_ruta_hor_seq.nextval into :new.id_asignacion_ruta from dual;
end;
--------------------------------------------------------------------------------------------------------
-- creacion de la tabla Viaje_realizado
create table viaje_realizado(
	id_viaje				number(20)			not null,
	id_asignacion_ruta		number(15)			not null,
	fecha_inicio_viaje		timestamp			not null,
	fecha_fin_viaje			timestamp			null,
	constraint via_pk_id primary key (id_viaje)
);

-- creacion de los constraints establecidos para Viaje_realizado
alter table viaje_realizado add (
    constraint vjr_fk_ida foreign key (id_asignacion_ruta) references asignacion_ruta_horario (id_asignacion_ruta),
	constraint vjr_uni_ids unique (id_asignacion_ruta,fecha_inicio_viaje)
);

-- creacion de sequencia de Viaje_realizado
create sequence viaje_realizado_seq start with 1 increment by 1 maxvalue 999999999999999 minvalue 1;

-- creacion de trigger para autoincrementar el id de Viaje_realizado
create or replace trigger tr_viaje_autoid
before insert 
on viaje_realizado
for each row
begin
	select viaje_realizado_seq.nextval into :new.id_viaje from dual;
end;