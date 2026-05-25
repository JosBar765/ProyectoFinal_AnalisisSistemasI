-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-05-2026 a las 06:08:03
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `specialisterne`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `casoprueba`
--

CREATE TABLE `casoprueba` (
  `id` int(11) NOT NULL,
  `id_fase_proyecto` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `instrucciones` text DEFAULT NULL,
  `resultado_esperado` text DEFAULT NULL,
  `id_estado_caso_prueba` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `casoprueba`
--

INSERT INTO `casoprueba` (`id`, `id_fase_proyecto`, `titulo`, `descripcion`, `instrucciones`, `resultado_esperado`, `id_estado_caso_prueba`) VALUES
(1, 1, 'Login credenciales válidas', 'Verificar login con credenciales válidas.', '1. Ingresar correo válido.\r\n2. Ingresar contraseña válida.\r\n3. Presionar iniciar sesión.', 'El sistema permite acceso al dashboard.', 3),
(2, 1, 'Login contraseña incorrecta', 'Verificar login con contraseña incorrecta.', '1. Ingresar correo válido.\r\n2. Ingresar contraseña incorrecta.', 'El sistema muestra mensaje de error.', 3),
(3, 1, 'Bloqueo por múltiples intentos', 'Validar bloqueo temporal.', '1. Ingresar datos incorrectos repetidamente.', 'El sistema bloquea temporalmente.', 2),
(4, 2, 'Generar Factura A', 'Validar generación de factura.', '1. Completar datos.\r\n2. Generar factura.', 'Factura generada correctamente.', 1),
(5, 2, 'Validar IVA', 'El cálculo del IVA debe ser correcto.', '1. Agregar productos.\r\n2. Calcular factura.', 'IVA correcto.', 4),
(6, 2, 'Exportar PDF Factura', 'Validar exportación PDF.', '1. Generar factura.\n2. Exportar PDF.', 'PDF descargado correctamente.', 3),
(7, 3, 'Descarga PDF', 'Verificar descarga de reportes.', '1. Ir a reportes.\r\n2. Descargar PDF.', 'PDF descargado correctamente.', 1),
(8, 3, 'Filtro por fecha', 'Validar filtros de búsqueda.', '1. Seleccionar fechas.\r\n2. Filtrar.', 'Resultados correctos.', 1),
(9, 4, 'Registro usuario móvil', 'Validar registro desde app.', '1. Completar formulario.\n2. Crear cuenta.', 'Cuenta creada.', 3),
(10, 5, 'Agregar producto al carrito', 'Validar agregar productos.', '1. Seleccionar producto.\r\n2. Agregar.', 'Producto agregado.', 1),
(11, 6, 'Seguimiento GPS pedido', 'Validar tracking.', '1. Crear pedido.\n2. Ver mapa.', 'Tracking funcional.', 1),
(12, 7, 'Solicitud vacaciones', 'Validar solicitud.', '1. Completar formulario.\r\n2. Enviar.', 'Solicitud enviada.', 3),
(13, 8, 'Cálculo nómina', 'Validar cálculo salarial.', '1. Procesar nómina.', 'Cálculo correcto.', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultor`
--

CREATE TABLE `consultor` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_perfil_trabajo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consultor`
--

INSERT INTO `consultor` (`id`, `id_usuario`, `id_perfil_trabajo`) VALUES
(1, 3, 3),
(2, 4, 1),
(3, 5, 2),
(4, 6, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ejecucionprueba`
--

CREATE TABLE `ejecucionprueba` (
  `id` int(11) NOT NULL,
  `id_consultor` int(11) NOT NULL,
  `id_caso_prueba` int(11) NOT NULL,
  `resultado` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_ejecucion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ejecucionprueba`
--

INSERT INTO `ejecucionprueba` (`id`, `id_consultor`, `id_caso_prueba`, `resultado`, `observaciones`, `fecha_ejecucion`) VALUES
(1, 1, 1, 'Completado', 'Flujo correcto.', '2026-05-20 17:33:15'),
(2, 1, 2, 'Completado', 'Mensaje mostrado correctamente.', '2026-05-20 17:33:15'),
(3, 2, 3, 'En Progreso', 'Pendiente validar tiempos.', '2026-05-20 17:33:15'),
(4, 2, 4, 'En Progreso', 'Se detectaron inconsistencias.', '2026-05-20 17:33:15'),
(5, 3, 5, 'Fallido', 'IVA calculado incorrectamente.', '2026-05-20 17:33:15'),
(6, 4, 9, 'Completado', 'Registro exitoso.', '2026-05-20 17:33:15'),
(7, 4, 10, 'En Progreso', 'Validando múltiples productos.', '2026-05-20 17:33:15'),
(8, 3, 12, 'Completado', 'Solicitud creada.', '2026-05-20 17:33:15'),
(9, 3, 13, 'Completado', 'Cálculos correctos.', '2026-05-20 17:33:15'),
(13, 2, 6, 'Pendiente', 'descarga dos veces el pdf', '2026-05-24 21:39:07'),
(16, 2, 3, 'Fallido', 'fdfsfdsfsdf', '2026-05-24 21:57:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `errorreporte`
--

CREATE TABLE `errorreporte` (
  `id` int(11) NOT NULL,
  `id_ejecucion` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `id_severidad_error` int(11) NOT NULL,
  `estado` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `errorreporte`
--

INSERT INTO `errorreporte` (`id`, `id_ejecucion`, `titulo`, `descripcion`, `id_severidad_error`, `estado`) VALUES
(1, 5, 'Calculo incorrecto de IVA', 'El sistema calcula 18% en lugar de 21%.', 4, 0),
(2, 4, 'Botón generar factura no responde', 'El botón no ejecuta acción en Firefox.', 3, 0),
(3, 7, 'Duplicación de productos en carrito', 'Se agregan productos duplicados.', 2, 0),
(4, 16, 'crash del programa', 'fdfsfdsfsdf', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadocasoprueba`
--

CREATE TABLE `estadocasoprueba` (
  `id` int(11) NOT NULL,
  `estado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estadocasoprueba`
--

INSERT INTO `estadocasoprueba` (`id`, `estado`) VALUES
(1, 'Pendiente'),
(2, 'En Progreso'),
(3, 'Completado'),
(4, 'Fallido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadoproyecto`
--

CREATE TABLE `estadoproyecto` (
  `id` int(11) NOT NULL,
  `estado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estadoproyecto`
--

INSERT INTO `estadoproyecto` (`id`, `estado`) VALUES
(1, 'Activo'),
(2, 'En Pausa'),
(3, 'Finalizado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faseproyecto`
--

CREATE TABLE `faseproyecto` (
  `id` int(11) NOT NULL,
  `id_proyecto` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `faseproyecto`
--

INSERT INTO `faseproyecto` (`id`, `id_proyecto`, `nombre`, `descripcion`) VALUES
(1, 1, 'Módulo Login', 'Pruebas relacionadas con autenticación.'),
(2, 1, 'Módulo Facturación', 'Pruebas de generación de facturas.'),
(3, 1, 'Módulo Reportes', 'Pruebas de exportación y reportes.'),
(4, 2, 'Registro y Login', 'Autenticación móvil.'),
(5, 2, 'Carrito de Compras', 'Validación de pedidos.'),
(6, 2, 'Seguimiento en Tiempo Real', 'Tracking de pedidos.'),
(7, 3, 'Gestión de Vacaciones', 'Solicitudes internas.'),
(8, 3, 'Gestión de Nóminas', 'Cálculos salariales.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenerrorreporte`
--

CREATE TABLE `imagenerrorreporte` (
  `id` int(11) NOT NULL,
  `id_error` int(11) NOT NULL,
  `imagen` text DEFAULT NULL,
  `fecha_subida` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `imagenerrorreporte`
--

INSERT INTO `imagenerrorreporte` (`id`, `id_error`, `imagen`, `fecha_subida`) VALUES
(1, 4, 'https://res.cloudinary.com/deugepkpf/image/upload/v1779681371/capturas/4/1.jpg', '2026-05-24 21:57:04'),
(2, 4, 'https://res.cloudinary.com/deugepkpf/image/upload/v1779681373/capturas/4/2.jpg', '2026-05-24 21:57:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `manualprueba`
--

CREATE TABLE `manualprueba` (
  `id` int(11) NOT NULL,
  `id_proyecto` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `fecha_subida` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiltrabajo`
--

CREATE TABLE `perfiltrabajo` (
  `id` int(11) NOT NULL,
  `perfil_trabajo` text DEFAULT NULL,
  `habilidades` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perfiltrabajo`
--

INSERT INTO `perfiltrabajo` (`id`, `perfil_trabajo`, `habilidades`) VALUES
(1, 'Especialista en pruebas funcionales y validación de flujos repetitivos.', 'UI/UX, Patrones repetitivos, Validación funcional'),
(2, 'Especialista en accesibilidad y experiencia de usuario.', 'Accesibilidad, Navegación simple, Flujos cortos'),
(3, 'Especialista en revisión visual y validación detallada.', 'Atención al detalle, Reportes visuales, QA manual'),
(4, 'Especialista en pruebas móviles.', 'Android, iOS, Responsive, Validación táctil');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto`
--

CREATE TABLE `proyecto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `id_estado_proyecto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyecto`
--

INSERT INTO `proyecto` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `id_estado_proyecto`) VALUES
(1, 'Sistema de Facturación Cliente XYZ', 'Pruebas funcionales y validación del sistema de facturación electrónica.', '2026-05-01', '2026-06-30', 1),
(2, 'App Móvil Delivery', 'Validación de flujos de compra y seguimiento de pedidos.', '2026-03-15', '2026-07-15', 1),
(3, 'Portal de Empleados ERP', 'Actualización de intranet y sistema de gestión interna.', '2026-01-01', '2026-04-30', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_consultor`
--

CREATE TABLE `proyecto_consultor` (
  `id_proyecto` int(11) NOT NULL,
  `id_consultor` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyecto_consultor`
--

INSERT INTO `proyecto_consultor` (`id_proyecto`, `id_consultor`, `fecha_asignacion`) VALUES
(1, 1, '2026-05-20 17:33:15'),
(1, 2, '2026-05-20 17:33:15'),
(1, 3, '2026-05-20 17:33:15'),
(2, 2, '2026-05-20 17:33:15'),
(2, 4, '2026-05-20 17:33:15'),
(3, 2, '2026-05-24 10:40:22'),
(3, 3, '2026-05-20 17:33:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`) VALUES
(1, 'admin'),
(2, 'supervisor'),
(3, 'consultor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `severidaderror`
--

CREATE TABLE `severidaderror` (
  `id` int(11) NOT NULL,
  `severidad_error` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `severidaderror`
--

INSERT INTO `severidaderror` (`id`, `severidad_error`) VALUES
(1, 'BAJO'),
(2, 'MEDIO'),
(3, 'ALTO'),
(4, 'CRITICO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `contrasenia` varchar(255) DEFAULT NULL,
  `id_rol` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `apellido`, `correo`, `contrasenia`, `id_rol`, `estado`, `fecha_creacion`) VALUES
(1, 'admin', 'admin', 'admin@specialisterne.com', 'admin', 1, 1, '2026-05-20 15:58:38'),
(2, 'Josue', 'Barrios', 'supervisor@specialisterne.com', 'supervisor', 2, 1, '2026-05-20 16:30:40'),
(3, 'Maria', 'Garcia', 'maria.garcia@specialisterne.com', '1234', 3, 1, '2026-05-20 17:33:14'),
(4, 'Carlos', 'Lopez', 'carlos.lopez@specialisterne.com', '1234', 3, 1, '2026-05-20 17:33:14'),
(5, 'Ana', 'Rodriguez', 'ana.rodriguez@specialisterne.com', '1234', 3, 1, '2026-05-20 17:33:14'),
(6, 'Diego', 'Hernandez', 'diego.hernandez@specialisterne.com', '1234', 3, 1, '2026-05-20 17:33:14');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `casoprueba`
--
ALTER TABLE `casoprueba`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_CasoPrueba_FaseProyecto` (`id_fase_proyecto`),
  ADD KEY `FK_CasoPrueba_EstadoCasoPrueba` (`id_estado_caso_prueba`);

--
-- Indices de la tabla `consultor`
--
ALTER TABLE `consultor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Consultor_Usuario` (`id_usuario`),
  ADD KEY `FK_Consultor_PerfilTrabajo` (`id_perfil_trabajo`);

--
-- Indices de la tabla `ejecucionprueba`
--
ALTER TABLE `ejecucionprueba`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_EjecucionPrueba_CasoPrueba` (`id_caso_prueba`),
  ADD KEY `FK_EjecucionPrueba_Consultor` (`id_consultor`);

--
-- Indices de la tabla `errorreporte`
--
ALTER TABLE `errorreporte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ErrorReporte_EjecucionPrueba` (`id_ejecucion`),
  ADD KEY `FK_ErrorReporte_SeveridadError` (`id_severidad_error`);

--
-- Indices de la tabla `estadocasoprueba`
--
ALTER TABLE `estadocasoprueba`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estadoproyecto`
--
ALTER TABLE `estadoproyecto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `faseproyecto`
--
ALTER TABLE `faseproyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_FaseProyecto_Proyecto` (`id_proyecto`);

--
-- Indices de la tabla `imagenerrorreporte`
--
ALTER TABLE `imagenerrorreporte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ImagenErrorReporte_ErrorReporte` (`id_error`);

--
-- Indices de la tabla `manualprueba`
--
ALTER TABLE `manualprueba`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ManualPrueba_Proyecto` (`id_proyecto`);

--
-- Indices de la tabla `perfiltrabajo`
--
ALTER TABLE `perfiltrabajo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Proyecto_EstadoProyecto` (`id_estado_proyecto`);

--
-- Indices de la tabla `proyecto_consultor`
--
ALTER TABLE `proyecto_consultor`
  ADD PRIMARY KEY (`id_proyecto`,`id_consultor`),
  ADD KEY `FK_ProyectoConsultor_Consultor` (`id_consultor`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `severidaderror`
--
ALTER TABLE `severidaderror`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `FK_Usuario_Rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `casoprueba`
--
ALTER TABLE `casoprueba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `consultor`
--
ALTER TABLE `consultor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ejecucionprueba`
--
ALTER TABLE `ejecucionprueba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `errorreporte`
--
ALTER TABLE `errorreporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estadocasoprueba`
--
ALTER TABLE `estadocasoprueba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `estadoproyecto`
--
ALTER TABLE `estadoproyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `faseproyecto`
--
ALTER TABLE `faseproyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `imagenerrorreporte`
--
ALTER TABLE `imagenerrorreporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `manualprueba`
--
ALTER TABLE `manualprueba`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `perfiltrabajo`
--
ALTER TABLE `perfiltrabajo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `severidaderror`
--
ALTER TABLE `severidaderror`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `casoprueba`
--
ALTER TABLE `casoprueba`
  ADD CONSTRAINT `FK_CasoPrueba_EstadoCasoPrueba` FOREIGN KEY (`id_estado_caso_prueba`) REFERENCES `estadocasoprueba` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_CasoPrueba_FaseProyecto` FOREIGN KEY (`id_fase_proyecto`) REFERENCES `faseproyecto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `consultor`
--
ALTER TABLE `consultor`
  ADD CONSTRAINT `FK_Consultor_PerfilTrabajo` FOREIGN KEY (`id_perfil_trabajo`) REFERENCES `perfiltrabajo` (`id`),
  ADD CONSTRAINT `FK_Consultor_Usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `ejecucionprueba`
--
ALTER TABLE `ejecucionprueba`
  ADD CONSTRAINT `FK_EjecucionPrueba_CasoPrueba` FOREIGN KEY (`id_caso_prueba`) REFERENCES `casoprueba` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EjecucionPrueba_Consultor` FOREIGN KEY (`id_consultor`) REFERENCES `consultor` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `errorreporte`
--
ALTER TABLE `errorreporte`
  ADD CONSTRAINT `FK_ErrorReporte_EjecucionPrueba` FOREIGN KEY (`id_ejecucion`) REFERENCES `ejecucionprueba` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_ErrorReporte_SeveridadError` FOREIGN KEY (`id_severidad_error`) REFERENCES `severidaderror` (`id`);

--
-- Filtros para la tabla `faseproyecto`
--
ALTER TABLE `faseproyecto`
  ADD CONSTRAINT `FK_FaseProyecto_Proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyecto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `imagenerrorreporte`
--
ALTER TABLE `imagenerrorreporte`
  ADD CONSTRAINT `FK_ImagenErrorReporte_ErrorReporte` FOREIGN KEY (`id_error`) REFERENCES `errorreporte` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `manualprueba`
--
ALTER TABLE `manualprueba`
  ADD CONSTRAINT `FK_ManualPrueba_Proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyecto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD CONSTRAINT `FK_Proyecto_EstadoProyecto` FOREIGN KEY (`id_estado_proyecto`) REFERENCES `estadoproyecto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyecto_consultor`
--
ALTER TABLE `proyecto_consultor`
  ADD CONSTRAINT `FK_ProyectoConsultor_Consultor` FOREIGN KEY (`id_consultor`) REFERENCES `consultor` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_ProyectoConsultor_Proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyecto` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_Usuario_Rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
