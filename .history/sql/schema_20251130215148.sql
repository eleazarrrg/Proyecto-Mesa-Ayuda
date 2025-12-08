-- --------------------------------------------------------
-- Base de datos: mesa_ayuda
-- (Créala antes si no existe: CREATE DATABASE mesa_ayuda;)
-- --------------------------------------------------------

-- Usar la base de datos
USE mesa_ayuda;

-- --------------------------------------------------------
-- Tabla: colaboradores
-- --------------------------------------------------------

DROP TABLE IF EXISTS `colaboradores`;
CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `identificacion` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `tipo` enum('ESTUDIANTE','COLABORADOR') NOT NULL,
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `colaboradores`
(`id`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`,
 `sexo`, `fecha_nacimiento`, `foto_perfil`, `identificacion`, `email`,
 `telefono`, `tipo`, `creado_en`)
VALUES
(1, 'Juan', 'Felipe', 'Zhu', '', NULL, NULL, NULL,
 '8-1010-701', 'felipezhu3@gmail.com', '65791336',
 'ESTUDIANTE', '2025-11-30 19:46:07');

-- --------------------------------------------------------
-- Tabla: encuestas_satisfaccion
-- --------------------------------------------------------

DROP TABLE IF EXISTS `encuestas_satisfaccion`;
CREATE TABLE IF NOT EXISTS `encuestas_satisfaccion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `nivel` enum('CONFORME','INCONFORME','NO_RESPONDIDO') NOT NULL,
  `comentario` text,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=MyISAM
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: roles
-- --------------------------------------------------------

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `alcance` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `roles` (`id`, `nombre`, `alcance`, `descripcion`) VALUES
(1, 'admin', NULL, 'Acceso total al sistema'),
(2, 'agente', NULL, 'Atiende y gestiona tickets'),
(3, 'colaborador', NULL, 'Crea tickets y consulta estado');

-- --------------------------------------------------------
-- Tabla: tickets
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `tipo_ticket_id` int NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `estado` enum('EN_PROCESO','EN_ESPERA','CULMINADA') NOT NULL DEFAULT 'EN_ESPERA',
  `prioridad` enum('BAJA','MEDIA','ALTA') DEFAULT 'MEDIA',
  `creado_por_usuario_id` int DEFAULT NULL,
  `ip_origen` varchar(45) NOT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_asignacion` datetime DEFAULT NULL,
  `agente_id` int DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `solucion` text,
  PRIMARY KEY (`id`),
  KEY `colaborador_id` (`colaborador_id`),
  KEY `tipo_ticket_id` (`tipo_ticket_id`),
  KEY `creado_por_usuario_id` (`creado_por_usuario_id`),
  KEY `agente_id` (`agente_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `tickets`
(`id`, `colaborador_id`, `tipo_ticket_id`, `titulo`, `descripcion`,
 `estado`, `prioridad`, `creado_por_usuario_id`, `ip_origen`,
 `fecha_creacion`, `fecha_asignacion`, `agente_id`, `fecha_respuesta`,
 `fecha_cierre`, `solucion`)
VALUES
(1, 1, 4, 'Primer ticket', 'Intento de primer ticket',
 'EN_ESPERA', 'BAJA', 1, '::1',
 '2025-11-30 19:46:54', NULL, 2,
 '2025-11-30 20:50:22', NULL, 'Nada');

-- --------------------------------------------------------
-- Tabla: tipos_ticket
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tipos_ticket`;
CREATE TABLE IF NOT EXISTS `tipos_ticket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `categoria` enum('TECNICO','ACADEMICO') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `tipos_ticket` (`id`, `nombre`, `categoria`) VALUES
(1, 'Incidente Informático', 'TECNICO'),
(2, 'Falla de Sistema', 'TECNICO'),
(3, 'Reclamo de Nota', 'ACADEMICO'),
(4, 'Créditos Oficiales', 'ACADEMICO');

-- --------------------------------------------------------
-- Tabla: usuarios
-- --------------------------------------------------------

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rol_id` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `creado_en` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `rol_id` (`rol_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuarios`
(`id`, `username`, `password_hash`, `nombre`, `email`,
 `rol_id`, `activo`, `creado_en`)
VALUES
(1, 'JuanZhu3', '$2y$12$rq.U5YX3LpCRTAj9/1MOtuJGhSzIsiLHKu0ZhME8x26.Eb1jDDS9e',
    'Juan Zhu', 'felipezhu3@gmail.com', 1, 1, '2025-11-30 18:48:07'),
(2, 'Rafael', '$2y$12$qI0jajtoRiYcAh11FEYt2uy1peJS8VLDsSZ5Y0yrxf0iaOXMGRof.',
    'Rafael Gomez', 'rafael@gmail.com', 2, 1, '2025-11-30 19:38:58'),
(3, 'Isa', '$2y$12$UUle9rfCFUETeowxMgv9S.Z3LD4z.ztSb.GIwpjTZZUiuF09w8O.e',
    'Isabella Castro', 'isa@gmail.com', 3, 1, '2025-11-30 19:40:35');

-- --------------------------------------------------------
-- Vista: vw_tickets_resumen
-- --------------------------------------------------------

DROP VIEW IF EXISTS `vw_tickets_resumen`;

CREATE ALGORITHM=UNDEFINED
SQL SECURITY DEFINER
VIEW `vw_tickets_resumen` AS
  SELECT
    `t`.`id` AS `id`,
    `t`.`titulo` AS `titulo`,
    `t`.`estado` AS `estado`,
    `t`.`prioridad` AS `prioridad`,
    `t`.`fecha_creacion` AS `fecha_creacion`,
    `t`.`fecha_respuesta` AS `fecha_respuesta`,
    TIMESTAMPDIFF(MINUTE, `t`.`fecha_creacion`, `t`.`fecha_respuesta`)
      AS `minutos_respuesta`
  FROM `tickets` AS `t`;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
