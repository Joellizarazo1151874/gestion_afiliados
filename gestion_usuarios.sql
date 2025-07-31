-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-07-2025 a las 01:19:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_usuarios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `usuario`, `password`, `nombre`, `email`, `fecha_creacion`) VALUES
(1, 'admin', '$2y$10$Rr3DoC91D1UoESoa8CqOWeA/qpzCs/7PFQ0R0UuMoW4xLL69LpWf.', 'Administrador', 'endersonlizarazo3@gmail.com', '2025-07-29 21:56:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_solicitudes`
--

CREATE TABLE `documentos_solicitudes` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `tipo_documento` varchar(100) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `fecha_carga` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('Pendiente','Aprobado','Rechazado') DEFAULT 'Pendiente',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_usuarios`
--

CREATE TABLE `documentos_usuarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_documento` varchar(100) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `fecha_carga` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('Pendiente','Aprobado','Rechazado') DEFAULT 'Pendiente',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_afiliacion`
--

CREATE TABLE `solicitudes_afiliacion` (
  `id` int(11) NOT NULL,
  `numero_solicitud` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('Masculino','Femenino','Otro') NOT NULL,
  `tipo_discapacidad` varchar(100) NOT NULL,
  `porcentaje_discapacidad` int(11) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `codigo_postal` varchar(10) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada') DEFAULT 'Pendiente',
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `revisado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_documentos`
--

CREATE TABLE `tipos_documentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `obligatorio` tinyint(1) DEFAULT 1,
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_documentos`
--

INSERT INTO `tipos_documentos` (`id`, `nombre`, `descripcion`, `obligatorio`, `orden`, `activo`) VALUES
(1, 'Foto tipo carnet', 'Foto tipo carnet del Asociado', 1, 1, 1),
(2, 'Cédula del Asociado', 'Fotocopia de cédula del Asociado', 1, 2, 1),
(3, 'Foto cuerpo entero', 'Foto cuerpo entero del Asociado', 1, 3, 1),
(4, 'Cédula representante', 'Fotocopia de cédula del representante legal', 0, 4, 1),
(5, 'Hoja de Vida', 'Hoja de Vida del Asociado o Representante legal', 1, 5, 1),
(6, 'Certificado de Discapacidad', 'Certificado de Discapacidad', 1, 6, 1),
(7, 'Historia médica', 'Fotocopia de la historia médica en los casos aplicables', 0, 7, 1),
(8, 'Caracterización', 'Fotocopia de la Caracterización', 1, 8, 1),
(9, 'SISBEN', 'Fotocopia del SISBEN', 1, 9, 1),
(10, 'FOSYGA', 'Fotocopia del FOSYGA', 1, 10, 1),
(11, 'Renuncia anterior', 'Fotocopia de la renuncia en caso de haber pertenecido a otra organización', 0, 11, 1),
(12, 'Declaración de estatutos', 'Declaración de conocimiento de los estatutos', 1, 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `numero_asociado` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` enum('Masculino','Femenino','Otro') NOT NULL,
  `tipo_discapacidad` varchar(100) NOT NULL,
  `porcentaje_discapacidad` int(11) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `fecha_afiliacion` date NOT NULL,
  `estado` enum('Activo','Inactivo','Suspendido') DEFAULT 'Activo',
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `documentos_solicitudes`
--
ALTER TABLE `documentos_solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_documentos_solicitud` (`solicitud_id`),
  ADD KEY `idx_documentos_tipo_solicitud` (`tipo_documento`);

--
-- Indices de la tabla `documentos_usuarios`
--
ALTER TABLE `documentos_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_documentos_usuario` (`usuario_id`),
  ADD KEY `idx_documentos_tipo` (`tipo_documento`),
  ADD KEY `idx_documentos_estado` (`estado`);

--
-- Indices de la tabla `solicitudes_afiliacion`
--
ALTER TABLE `solicitudes_afiliacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_solicitud` (`numero_solicitud`),
  ADD KEY `revisado_por` (`revisado_por`),
  ADD KEY `idx_solicitudes_estado` (`estado`),
  ADD KEY `idx_solicitudes_fecha` (`fecha_solicitud`),
  ADD KEY `idx_solicitudes_email` (`email`);

--
-- Indices de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_asociado` (`numero_asociado`),
  ADD KEY `idx_numero_asociado` (`numero_asociado`),
  ADD KEY `idx_nombre_apellidos` (`nombre`,`apellidos`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_afiliacion` (`fecha_afiliacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `documentos_solicitudes`
--
ALTER TABLE `documentos_solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `documentos_usuarios`
--
ALTER TABLE `documentos_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes_afiliacion`
--
ALTER TABLE `solicitudes_afiliacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `documentos_solicitudes`
--
ALTER TABLE `documentos_solicitudes`
  ADD CONSTRAINT `documentos_solicitudes_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_afiliacion` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `documentos_usuarios`
--
ALTER TABLE `documentos_usuarios`
  ADD CONSTRAINT `documentos_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_afiliacion`
--
ALTER TABLE `solicitudes_afiliacion`
  ADD CONSTRAINT `solicitudes_afiliacion_ibfk_1` FOREIGN KEY (`revisado_por`) REFERENCES `administradores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
