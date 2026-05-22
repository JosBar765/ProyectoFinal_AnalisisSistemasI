-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-05-2026 a las 01:35:44
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

--
-- Volcado de datos para la tabla `casoprueba`
--

INSERT INTO `casoprueba` (`id`, `id_fase_proyecto`, `titulo`, `descripcion`, `instrucciones`, `resultado_esperado`, `id_estado_caso_prueba`) VALUES
(1, 1, 'Login credenciales válidas', 'Verificar login con credenciales válidas.', '1. Ingresar correo válido.\r\n2. Ingresar contraseña válida.\r\n3. Presionar iniciar sesión.', 'El sistema permite acceso al dashboard.', 3),
(2, 1, 'Login contraseña incorrecta', 'Verificar login con contraseña incorrecta.', '1. Ingresar correo válido.\r\n2. Ingresar contraseña incorrecta.', 'El sistema muestra mensaje de error.', 3),
(3, 1, 'Bloqueo por múltiples intentos', 'Validar bloqueo temporal.', '1. Ingresar datos incorrectos repetidamente.', 'El sistema bloquea temporalmente.', 2),
(4, 2, 'Generar Factura A', 'Validar generación de factura.', '1. Completar datos.\r\n2. Generar factura.', 'Factura generada correctamente.', 2),
(5, 2, 'Validar IVA', 'El cálculo del IVA debe ser correcto.', '1. Agregar productos.\r\n2. Calcular factura.', 'IVA correcto.', 4),
(6, 2, 'Exportar PDF Factura', 'Validar exportación PDF.', '1. Generar factura.\r\n2. Exportar PDF.', 'PDF descargado correctamente.', 1),
(7, 3, 'Descarga PDF', 'Verificar descarga de reportes.', '1. Ir a reportes.\r\n2. Descargar PDF.', 'PDF descargado correctamente.', 1),
(8, 3, 'Filtro por fecha', 'Validar filtros de búsqueda.', '1. Seleccionar fechas.\r\n2. Filtrar.', 'Resultados correctos.', 1),
(9, 4, 'Registro usuario móvil', 'Validar registro desde app.', '1. Completar formulario.\n2. Crear cuenta.', 'Cuenta creada.', 3),
(10, 5, 'Agregar producto al carrito', 'Validar agregar productos.', '1. Seleccionar producto.\r\n2. Agregar.', 'Producto agregado.', 2),
(11, 6, 'Seguimiento GPS pedido', 'Validar tracking.', '1. Crear pedido.\n2. Ver mapa.', 'Tracking funcional.', 1),
(12, 7, 'Solicitud vacaciones', 'Validar solicitud.', '1. Completar formulario.\r\n2. Enviar.', 'Solicitud enviada.', 3),
(13, 8, 'Cálculo nómina', 'Validar cálculo salarial.', '1. Procesar nómina.', 'Cálculo correcto.', 3);

--
-- Volcado de datos para la tabla `consultor`
--

INSERT INTO `consultor` (`id`, `id_usuario`, `id_perfil_trabajo`) VALUES
(1, 3, 3),
(2, 4, 1),
(3, 5, 2),
(4, 6, 4);

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
(9, 3, 13, 'Completado', 'Cálculos correctos.', '2026-05-20 17:33:15');

--
-- Volcado de datos para la tabla `errorreporte`
--

INSERT INTO `errorreporte` (`id`, `id_ejecucion`, `titulo`, `descripcion`, `id_severidad_error`) VALUES
(1, 5, 'Calculo incorrecto de IVA', 'El sistema calcula 18% en lugar de 21%.', 4),
(2, 4, 'Botón generar factura no responde', 'El botón no ejecuta acción en Firefox.', 3),
(3, 7, 'Duplicación de productos en carrito', 'Se agregan productos duplicados.', 2);

--
-- Volcado de datos para la tabla `estadocasoprueba`
--

INSERT INTO `estadocasoprueba` (`id`, `estado`) VALUES
(1, 'Pendiente'),
(2, 'En Progreso'),
(3, 'Completado'),
(4, 'Fallido');

--
-- Volcado de datos para la tabla `estadoproyecto`
--

INSERT INTO `estadoproyecto` (`id`, `estado`) VALUES
(1, 'Activo'),
(2, 'En Pausa'),
(3, 'Finalizado');

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

--
-- Volcado de datos para la tabla `perfiltrabajo`
--

INSERT INTO `perfiltrabajo` (`id`, `perfil_trabajo`, `habilidades`) VALUES
(1, 'Especialista en pruebas funcionales y validación de flujos repetitivos.', 'UI/UX, Patrones repetitivos, Validación funcional'),
(2, 'Especialista en accesibilidad y experiencia de usuario.', 'Accesibilidad, Navegación simple, Flujos cortos'),
(3, 'Especialista en revisión visual y validación detallada.', 'Atención al detalle, Reportes visuales, QA manual'),
(4, 'Especialista en pruebas móviles.', 'Android, iOS, Responsive, Validación táctil');

--
-- Volcado de datos para la tabla `proyecto`
--

INSERT INTO `proyecto` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `id_estado_proyecto`) VALUES
(1, 'Sistema de Facturación Cliente XYZ', 'Pruebas funcionales y validación del sistema de facturación electrónica.', '2026-05-01', '2026-06-30', 1),
(2, 'App Móvil Delivery', 'Validación de flujos de compra y seguimiento de pedidos.', '2026-03-15', '2026-07-15', 2),
(3, 'Portal de Empleados ERP', 'Actualización de intranet y sistema de gestión interna.', '2026-01-01', '2026-04-30', 3);

--
-- Volcado de datos para la tabla `proyecto_consultor`
--

INSERT INTO `proyecto_consultor` (`id_proyecto`, `id_consultor`, `fecha_asignacion`) VALUES
(1, 1, '2026-05-20 17:33:15'),
(1, 2, '2026-05-20 17:33:15'),
(1, 3, '2026-05-20 17:33:15'),
(2, 2, '2026-05-20 17:33:15'),
(2, 4, '2026-05-20 17:33:15'),
(3, 3, '2026-05-20 17:33:15');

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`) VALUES
(1, 'admin'),
(2, 'supervisor'),
(3, 'consultor');

--
-- Volcado de datos para la tabla `severidaderror`
--

INSERT INTO `severidaderror` (`id`, `severidad_error`) VALUES
(1, 'BAJO'),
(2, 'MEDIO'),
(3, 'ALTO'),
(4, 'CRITICO');

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
