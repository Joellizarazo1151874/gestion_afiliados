-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-07-2025 a las 18:18:06
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
(1, 'admin', '$2y$10$Rr3DoC91D1UoESoa8CqOWeA/qpzCs/7PFQ0R0UuMoW4xLL69LpWf.', 'Administrador', 'familiaunidaporladiscapacidad@gmail.com', '2025-07-29 21:56:08');

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

--
-- Volcado de datos para la tabla `documentos_solicitudes`
--

INSERT INTO `documentos_solicitudes` (`id`, `solicitud_id`, `tipo_documento`, `nombre_archivo`, `ruta_archivo`, `fecha_carga`, `estado`, `observaciones`) VALUES
(170, 28, 'foto_carnet', '688b8d59b5293_foto_carnet.jpg', 'uploads/documentos_solicitudes/688b8d59b5293_foto_carnet.jpg', '2025-07-31 15:35:53', 'Pendiente', NULL),
(171, 28, 'cedula_asociado', '688b8d59b5d67_cedula_asociado.jpg', 'uploads/documentos_solicitudes/688b8d59b5d67_cedula_asociado.jpg', '2025-07-31 15:35:53', 'Pendiente', NULL),
(172, 28, 'foto_cuerpo', '688b8d59b6706_foto_cuerpo.jpg', 'uploads/documentos_solicitudes/688b8d59b6706_foto_cuerpo.jpg', '2025-07-31 15:35:53', 'Pendiente', NULL),
(173, 28, 'hoja_vida', '688b8d59b6e83_hoja_vida.docx', 'uploads/documentos_solicitudes/688b8d59b6e83_hoja_vida.docx', '2025-07-31 15:35:53', 'Pendiente', NULL),
(174, 28, 'certificado_discapacidad', '688b8d59b7654_certificado_discapacidad.jpg', 'uploads/documentos_solicitudes/688b8d59b7654_certificado_discapacidad.jpg', '2025-07-31 15:35:53', 'Pendiente', NULL),
(175, 28, 'caracterizacion', '688b8d59b7e88_caracterizacion.jpg', 'uploads/documentos_solicitudes/688b8d59b7e88_caracterizacion.jpg', '2025-07-31 15:35:53', 'Pendiente', NULL),
(176, 28, 'sisben', '688b8d59b85af_sisben.png', 'uploads/documentos_solicitudes/688b8d59b85af_sisben.png', '2025-07-31 15:35:53', 'Pendiente', NULL),
(177, 28, 'fosyga', '688b8d59b8cfe_fosyga.jpg', 'uploads/documentos_solicitudes/688b8d59b8cfe_fosyga.jpg', '2025-07-31 15:35:53', 'Pendiente', NULL),
(178, 28, 'declaracion_estatutos', '688b8d59b95a9_declaracion_estatutos.docx', 'uploads/documentos_solicitudes/688b8d59b95a9_declaracion_estatutos.docx', '2025-07-31 15:35:53', 'Pendiente', NULL),
(179, 29, 'foto_carnet', '688b8e7c6766d_foto_carnet.jpg', 'uploads/documentos_solicitudes/688b8e7c6766d_foto_carnet.jpg', '2025-07-31 15:40:44', 'Pendiente', NULL),
(180, 29, 'cedula_asociado', '688b8e7c67ee5_cedula_asociado.png', 'uploads/documentos_solicitudes/688b8e7c67ee5_cedula_asociado.png', '2025-07-31 15:40:44', 'Pendiente', NULL),
(181, 29, 'foto_cuerpo', '688b8e7c686d7_foto_cuerpo.jpg', 'uploads/documentos_solicitudes/688b8e7c686d7_foto_cuerpo.jpg', '2025-07-31 15:40:44', 'Pendiente', NULL),
(182, 29, 'hoja_vida', '688b8e7c68f41_hoja_vida.docx', 'uploads/documentos_solicitudes/688b8e7c68f41_hoja_vida.docx', '2025-07-31 15:40:44', 'Pendiente', NULL),
(183, 29, 'certificado_discapacidad', '688b8e7c696a3_certificado_discapacidad.jpg', 'uploads/documentos_solicitudes/688b8e7c696a3_certificado_discapacidad.jpg', '2025-07-31 15:40:44', 'Pendiente', NULL),
(184, 29, 'historia_medica', '688b8e7c69e45_historia_medica.jpg', 'uploads/documentos_solicitudes/688b8e7c69e45_historia_medica.jpg', '2025-07-31 15:40:44', 'Pendiente', NULL),
(185, 29, 'caracterizacion', '688b8e7c6a937_caracterizacion.jpg', 'uploads/documentos_solicitudes/688b8e7c6a937_caracterizacion.jpg', '2025-07-31 15:40:44', 'Pendiente', NULL),
(186, 29, 'sisben', '688b8e7c6b163_sisben.jpg', 'uploads/documentos_solicitudes/688b8e7c6b163_sisben.jpg', '2025-07-31 15:40:44', 'Pendiente', NULL),
(187, 29, 'fosyga', '688b8e7c6b959_fosyga.jpg', 'uploads/documentos_solicitudes/688b8e7c6b959_fosyga.jpg', '2025-07-31 15:40:44', 'Pendiente', NULL),
(188, 29, 'declaracion_estatutos', '688b8e7c6c1ba_declaracion_estatutos.docx', 'uploads/documentos_solicitudes/688b8e7c6c1ba_declaracion_estatutos.docx', '2025-07-31 15:40:44', 'Pendiente', NULL),
(189, 30, 'foto_carnet', '688b90b053ab7_foto_carnet.jpg', 'uploads/documentos_solicitudes/688b90b053ab7_foto_carnet.jpg', '2025-07-31 15:50:08', 'Pendiente', NULL),
(190, 30, 'cedula_asociado', '688b90b054420_cedula_asociado.jpg', 'uploads/documentos_solicitudes/688b90b054420_cedula_asociado.jpg', '2025-07-31 15:50:08', 'Pendiente', NULL),
(191, 30, 'foto_cuerpo', '688b90b054cfb_foto_cuerpo.jpg', 'uploads/documentos_solicitudes/688b90b054cfb_foto_cuerpo.jpg', '2025-07-31 15:50:08', 'Pendiente', NULL),
(192, 30, 'hoja_vida', '688b90b055607_hoja_vida.docx', 'uploads/documentos_solicitudes/688b90b055607_hoja_vida.docx', '2025-07-31 15:50:08', 'Pendiente', NULL),
(193, 30, 'certificado_discapacidad', '688b90b055e38_certificado_discapacidad.jpg', 'uploads/documentos_solicitudes/688b90b055e38_certificado_discapacidad.jpg', '2025-07-31 15:50:08', 'Pendiente', NULL),
(194, 30, 'caracterizacion', '688b90b0566f7_caracterizacion.jpg', 'uploads/documentos_solicitudes/688b90b0566f7_caracterizacion.jpg', '2025-07-31 15:50:08', 'Pendiente', NULL),
(195, 30, 'sisben', '688b90b056f80_sisben.png', 'uploads/documentos_solicitudes/688b90b056f80_sisben.png', '2025-07-31 15:50:08', 'Pendiente', NULL),
(196, 30, 'fosyga', '688b90b0577c7_fosyga.jpg', 'uploads/documentos_solicitudes/688b90b0577c7_fosyga.jpg', '2025-07-31 15:50:08', 'Pendiente', NULL),
(197, 30, 'declaracion_estatutos', '688b90b057f36_declaracion_estatutos.docx', 'uploads/documentos_solicitudes/688b90b057f36_declaracion_estatutos.docx', '2025-07-31 15:50:08', 'Pendiente', NULL);

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

--
-- Volcado de datos para la tabla `solicitudes_afiliacion`
--

INSERT INTO `solicitudes_afiliacion` (`id`, `numero_solicitud`, `nombre`, `apellidos`, `fecha_nacimiento`, `genero`, `tipo_discapacidad`, `porcentaje_discapacidad`, `telefono`, `email`, `direccion`, `ciudad`, `codigo_postal`, `observaciones`, `estado`, `fecha_solicitud`, `fecha_revision`, `motivo_rechazo`, `revisado_por`) VALUES
(28, 'SOL000001', 'Joel', 'Lizarazo', '2025-07-16', 'Masculino', 'Discapacidad del habla', 34, '3209939817', 'endersonjoellg@ufps.edu.co', 'calle 8 # 14 - 56 Barrio gramalote / Villa del rosario', 'Villa Del Rosario', '541030', '', 'Aprobada', '2025-07-31 15:35:53', '2025-07-31 15:36:08', NULL, 1),
(29, 'SOL000002', 'Joel2', 'Lizarazo2', '2025-07-08', 'Masculino', 'Discapacidad del habla', 2, '3209939817', 'endersonlizarazo3@gmail.com', 'calle 8 # 14 - 56 Barrio gramalote / Villa del rosario', 'Villa Del Rosario', '541030', '', 'Aprobada', '2025-07-31 15:40:44', '2025-07-31 15:41:00', NULL, 1),
(30, 'SOL000003', 'Joel', 'Lizarazo', '2025-07-14', 'Masculino', 'Discapacidad intelectual', 2, '3209939817', 'endersonlizarazo6@gmail.com', 'calle 8 # 14 - 56 Barrio gramalote / Villa del rosario', 'Villa Del Rosario', '541030', '', 'Rechazada', '2025-07-31 15:50:08', '2025-07-31 15:50:39', 'Documentos incompletos o erroneos', 1);

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
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `numero_asociado`, `nombre`, `apellidos`, `fecha_nacimiento`, `genero`, `tipo_discapacidad`, `porcentaje_discapacidad`, `telefono`, `email`, `direccion`, `ciudad`, `codigo_postal`, `fecha_afiliacion`, `estado`, `observaciones`, `fecha_registro`, `fecha_actualizacion`) VALUES
(30, 'ASO000001', 'Joel', 'Lizarazo', '2025-07-16', 'Masculino', 'Discapacidad del habla', 34, '3209939817', 'endersonjoellg@ufps.edu.co', 'calle 8 # 14 - 56 Barrio gramalote / Villa del rosario', 'Villa Del Rosario', '541030', '2025-07-31', 'Activo', '', '2025-07-31 15:36:08', '2025-07-31 15:36:08'),
(31, 'ASO000002', 'Joel2', 'Lizarazo2', '2025-07-08', 'Masculino', 'Discapacidad del habla', 2, '3209939817', 'endersonlizarazo3@gmail.com', 'calle 8 # 14 - 56 Barrio gramalote / Villa del rosario', 'Villa Del Rosario', '541030', '2025-07-31', 'Activo', '', '2025-07-31 15:41:00', '2025-07-31 15:41:00');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT de la tabla `documentos_usuarios`
--
ALTER TABLE `documentos_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT de la tabla `solicitudes_afiliacion`
--
ALTER TABLE `solicitudes_afiliacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `tipos_documentos`
--
ALTER TABLE `tipos_documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
