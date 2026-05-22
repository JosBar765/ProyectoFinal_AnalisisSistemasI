CREATE DATABASE `Specialisterne`;

USE `Specialisterne`;

CREATE TABLE `Rol` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255)
);

CREATE TABLE `EstadoProyecto` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `estado` varchar(255)
);

CREATE TABLE `EstadoCasoPrueba` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `estado` varchar(255)
);

CREATE TABLE `SeveridadError` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `severidad_error` varchar(255)
);

CREATE TABLE `Usuario` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255),
  `apellido` varchar(255),
  `correo` varchar(255) UNIQUE,
  `contrasenia` varchar(255),
  `id_rol` int NOT NULL,
  `estado` boolean,
  `fecha_creacion` datetime
);

CREATE TABLE `PerfilTrabajo` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `perfil_trabajo` text,
  `habilidades` text
);

CREATE TABLE `Consultor` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_perfil_trabajo` int NOT NULL
);

CREATE TABLE `Proyecto` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nombre` varchar(255),
  `descripcion` text,
  `fecha_inicio` date,
  `fecha_fin` date,
  `id_estado_proyecto` int NOT NULL
);

CREATE TABLE `FaseProyecto` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_proyecto` int NOT NULL,
  `nombre` varchar(255),
  `descripcion` text
);

CREATE TABLE `ManualPrueba` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_proyecto` int NOT NULL,
  `titulo` varchar(255),
  `descripcion` text,
  `url` text,
  `fecha_subida` datetime
);

CREATE TABLE `CasoPrueba` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_fase_proyecto` int NOT NULL,
  `titulo` varchar(255),
  `descripcion` text,
  `instrucciones` text,
  `resultado_esperado` text,
  `id_estado_caso_prueba` int NOT NULL
);

CREATE TABLE `Proyecto_Consultor` (
  `id_proyecto` int,
  `id_consultor` int,
  `fecha_asignacion` datetime,
  PRIMARY KEY (`id_proyecto`, `id_consultor`)
);

CREATE TABLE `EjecucionPrueba` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_consultor` int NOT NULL,
  `id_caso_prueba` int NOT NULL,
  `resultado` varchar(255),
  `observaciones` text,
  `fecha_ejecucion` datetime
);

CREATE TABLE `ErrorReporte` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_ejecucion` int NOT NULL,
  `titulo` varchar(255),
  `descripcion` text,
  `id_severidad_error` int NOT NULL
);

CREATE TABLE `ImagenErrorReporte` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `id_error` int NOT NULL,
  `imagen` text,
  `fecha_subida` datetime
);

ALTER TABLE `Usuario` ADD CONSTRAINT `FK_Usuario_Rol` FOREIGN KEY (`id_rol`) REFERENCES `Rol` (`id`);

ALTER TABLE `Consultor` ADD CONSTRAINT `FK_Consultor_Usuario` FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id`);

ALTER TABLE `Consultor` ADD CONSTRAINT `FK_Consultor_PerfilTrabajo` FOREIGN KEY (`id_perfil_trabajo`) REFERENCES `PerfilTrabajo` (`id`);

ALTER TABLE `Proyecto` ADD CONSTRAINT `FK_Proyecto_EstadoProyecto` FOREIGN KEY (`id_estado_proyecto`) REFERENCES `EstadoProyecto` (`id`) ON DELETE CASCADE;

ALTER TABLE `FaseProyecto` ADD CONSTRAINT `FK_FaseProyecto_Proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `Proyecto` (`id`) ON DELETE CASCADE;

ALTER TABLE `ManualPrueba` ADD CONSTRAINT `FK_ManualPrueba_Proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `Proyecto` (`id`) ON DELETE CASCADE;

ALTER TABLE `CasoPrueba` ADD CONSTRAINT `FK_CasoPrueba_FaseProyecto` FOREIGN KEY (`id_fase_proyecto`) REFERENCES `FaseProyecto` (`id`) ON DELETE CASCADE;

ALTER TABLE `CasoPrueba` ADD CONSTRAINT `FK_CasoPrueba_EstadoCasoPrueba` FOREIGN KEY (`id_estado_caso_prueba`) REFERENCES `EstadoCasoPrueba` (`id`) ON DELETE CASCADE;

ALTER TABLE `Proyecto_Consultor` ADD CONSTRAINT `FK_ProyectoConsultor_Proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `Proyecto` (`id`) ON DELETE CASCADE;

ALTER TABLE `Proyecto_Consultor` ADD CONSTRAINT `FK_ProyectoConsultor_Consultor` FOREIGN KEY (`id_consultor`) REFERENCES `Consultor` (`id`) ON DELETE CASCADE;

ALTER TABLE `EjecucionPrueba` ADD CONSTRAINT `FK_EjecucionPrueba_CasoPrueba` FOREIGN KEY (`id_caso_prueba`) REFERENCES `CasoPrueba` (`id`) ON DELETE CASCADE;

ALTER TABLE `EjecucionPrueba` ADD CONSTRAINT `FK_EjecucionPrueba_Consultor` FOREIGN KEY (`id_consultor`) REFERENCES `Consultor` (`id`) ON DELETE CASCADE;

ALTER TABLE `ErrorReporte` ADD CONSTRAINT `FK_ErrorReporte_EjecucionPrueba` FOREIGN KEY (`id_ejecucion`) REFERENCES `EjecucionPrueba` (`id`) ON DELETE CASCADE;

ALTER TABLE `ErrorReporte` ADD CONSTRAINT `FK_ErrorReporte_SeveridadError` FOREIGN KEY (`id_severidad_error`) REFERENCES `SeveridadError` (`id`);

ALTER TABLE `ImagenErrorReporte` ADD CONSTRAINT `FK_ImagenErrorReporte_ErrorReporte` FOREIGN KEY (`id_error`) REFERENCES `ErrorReporte` (`id`) ON DELETE CASCADE;
