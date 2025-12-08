CREATE DATABASE IF NOT EXISTS mesa_ayuda
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mesa_ayuda;

-- ======================
-- ROLES
-- ======================
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,        -- admin, agente, colaborador
  descripcion VARCHAR(255) NULL
);

INSERT INTO roles (nombre, descripcion) VALUES
('admin', 'Acceso total al sistema'),
('agente', 'Atiende y gestiona tickets'),
('colaborador', 'Crea tickets y consulta estado');

-- ======================
-- USUARIOS (AGENTES/ADMINS)
-- ======================
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  rol_id INT NOT NULL,
  activo TINYINT(1) DEFAULT 1,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Usuario admin inicial (password: admin123, la cambiarás luego en código)
-- OJO: esto es solo placeholder, en producción deberías generar el hash vía PHP.
-- Puedes dejar esta inserción comentada y luego la generamos con PHP.
/*
INSERT INTO usuarios (username, password_hash, nombre, email, rol_id)
VALUES ('admin', '<AQUI_HASH>', 'Administrador', 'admin@utp.local', 1);
*/

-- ======================
-- COLABORADORES / ESTUDIANTES
-- ======================
CREATE TABLE colaboradores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  primer_nombre VARCHAR(50) NOT NULL,
  segundo_nombre VARCHAR(50) NULL,
  primer_apellido VARCHAR(50) NOT NULL,
  segundo_apellido VARCHAR(50) NULL,
  identificacion VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  telefono VARCHAR(30) NULL,
  tipo ENUM('ESTUDIANTE','COLABORADOR') NOT NULL,
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ======================
-- TIPOS DE TICKET
-- ======================
CREATE TABLE tipos_ticket (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  categoria ENUM('TECNICO','ACADEMICO') NOT NULL
);

INSERT INTO tipos_ticket (nombre, categoria) VALUES
('Incidente Informático', 'TECNICO'),
('Falla de Sistema', 'TECNICO'),
('Reclamo de Nota', 'ACADEMICO'),
('Créditos Oficiales', 'ACADEMICO');

-- ======================
-- TICKETS
-- ======================
CREATE TABLE tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  colaborador_id INT NOT NULL,
  tipo_ticket_id INT NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  descripcion TEXT NOT NULL,
  estado ENUM('ABIERTO','EN_PROCESO','CERRADO') DEFAULT 'ABIERTO',
  prioridad ENUM('BAJA','MEDIA','ALTA') DEFAULT 'MEDIA',
  creado_por_usuario_id INT NULL,
  ip_origen VARCHAR(45) NOT NULL,
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  fecha_asignacion DATETIME NULL,
  agente_id INT NULL,
  fecha_respuesta DATETIME NULL,
  fecha_cierre DATETIME NULL,
  solucion TEXT NULL,
  FOREIGN KEY (colaborador_id) REFERENCES colaboradores(id),
  FOREIGN KEY (tipo_ticket_id) REFERENCES tipos_ticket(id),
  FOREIGN KEY (creado_por_usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (agente_id) REFERENCES usuarios(id)
);

-- ======================
-- ENCUESTA DE SATISFACCION
-- ======================
CREATE TABLE encuestas_satisfaccion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  nivel ENUM('CONFORME','INCONFORME','NO_RESPONDIDO') NOT NULL,
  comentario TEXT NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id)
);

-- ======================
-- VISTA SIMPLE PARA REPORTES (OPCIONAL)
-- ======================
-- Vista para ver tickets con tiempo de respuesta en minutos
CREATE OR REPLACE VIEW vw_tickets_resumen AS
SELECT
  t.id,
  t.titulo,
  t.estado,
  t.prioridad,
  t.fecha_creacion,
  t.fecha_respuesta,
  TIMESTAMPDIFF(MINUTE, t.fecha_creacion, t.fecha_respuesta) AS minutos_respuesta
FROM tickets t;
