--
-- Base de datos: `mesa_ayuda`
--

-- --------------------------------------------------------
-- Tabla: colaboradores (perfil de estudiante)
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
  PRIMARY KEY (`id`),
  -- Un estudiante/colaborador no puede repetirse por identificación o email
  UNIQUE KEY `uq_colaboradores_identificacion` (`identificacion`),
  UNIQUE KEY `uq_colaboradores_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: encuestas_satisfaccion
-- --------------------------------------------------------

DROP TABLE IF EXISTS `encuestas_satisfaccion`;
CREATE TABLE IF NOT EXISTS `encuestas_satisfaccion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  -- Requisitos: atendido satisfactoriamente, conforme, inconforme, no respondido
  `nivel` enum('ATENDIDO_SATISFACTORIAMENTE','CONFORME','INCONFORME','NO_RESPONDIDO') NOT NULL,
  `comentario` text,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  -- Solo una encuesta por ticket
  UNIQUE KEY `uq_encuesta_ticket` (`ticket_id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Tabla: tipos_ticket
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tipos_ticket`;
CREATE TABLE IF NOT EXISTS `tipos_ticket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  -- Esto te permite dividir en Soporte / Académico para el punto 4
  `categoria` enum('TECNICO','ACADEMICO') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  -- Un correo no se puede repetir entre usuarios
  UNIQUE KEY `uq_usuarios_email` (`email`),
  KEY `rol_id` (`rol_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Vista: vw_tickets_resumen (tiempo de respuesta)
-- --------------------------------------------------------

DROP VIEW IF EXISTS `vw_tickets_resumen`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_tickets_resumen` AS
SELECT
    `t`.`id` AS `id`,
    `t`.`titulo` AS `titulo`,
    `t`.`estado` AS `estado`,
    `t`.`prioridad` AS `prioridad`,
    `t`.`fecha_creacion` AS `fecha_creacion`,
    `t`.`fecha_respuesta` AS `fecha_respuesta`,
    TIMESTAMPDIFF(MINUTE, `t`.`fecha_creacion`, `t`.`fecha_respuesta`) AS `minutos_respuesta`
FROM `tickets` AS `t`;

COMMIT;
