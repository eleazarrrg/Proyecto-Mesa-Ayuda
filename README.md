# Proyecto Mesa de Ayuda 🧩🖥️

<p align="center">
  <img src="https://img.freepik.com/fotos-premium/pantalla-ayuda-sitio-web-proporciona-respuestas-preguntas-frecuentes-facilidad_31965-520321.jpg" alt="Pantalla principal del sistema Mesa de Ayuda" width="800">
</p>

> Proyecto final de la asignatura **Ingeniería Web (1SF132) – II Semestre 2025**  
> **Universidad Tecnológica de Panamá – Facultad de Ingeniería**  
> **Profesora:** Ing. Irina Fong  

---

## 📌 Descripción general

**Mesa de Ayuda** es una aplicación web desarrollada como proyecto final de la materia **Ingeniería Web**, cuyo objetivo principal es centralizar y gestionar las solicitudes de soporte técnico de una organización.

El sistema permite registrar, asignar, atender y dar seguimiento a incidencias de forma organizada, proporcionando a los usuarios y al personal de soporte una herramienta sencilla, accesible y segura.

Este repositorio contiene el código fuente del sistema descrito en el documento PDF entregado como informe final del proyecto.

---

## 🎯 Objetivos del proyecto

- Diseñar y desarrollar una **aplicación web funcional** para la gestión de tickets de soporte.
- Aplicar los conceptos de **análisis, diseño y desarrollo web** vistos en clase.
- Implementar **buenas prácticas** de programación en PHP y manejo de base de datos.
- Proporcionar a la organización una herramienta que:
  - Reduzca el tiempo de respuesta.
  - Mantenga un historial de incidencias.
  - Mejore la comunicación entre usuarios y personal técnico.

---

## 🧑‍💻 Roles del sistema

Según el análisis presentado en el informe del proyecto, el sistema está pensado para trabajar con, al menos, los siguientes perfiles:

- **Administrador**
  - Gestiona usuarios del sistema.
  - Configura categorías, prioridades y estados de los tickets.
  - Visualiza reportes generales.

- **Técnico / Soporte**
  - Recibe tickets asignados.
  - Actualiza el estado de las solicitudes.
  - Registra notas, comentarios y soluciones aplicadas.

- **Usuario final**
  - Crea nuevas solicitudes de soporte.
  - Consulta el estado de sus incidencias.
  - Recibe notificaciones sobre cambios y cierres de tickets.

Los nombres exactos de los roles y permisos pueden consultarse en el documento del proyecto y en el script de base de datos correspondiente.

---

## ⚙️ Funcionalidades principales

Entre las funciones descritas y desarrolladas en el proyecto se encuentran:

- Autenticación de usuarios (inicio y cierre de sesión).
- Registro de nuevos tickets o solicitudes de soporte.
- Asignación de tickets a personal de soporte.
- Gestión de estados (por ejemplo: _pendiente, en proceso, resuelto, cerrado_).
- Gestión de prioridades y categorías de incidencias.
- Listado y filtrado de tickets por:
  - Usuario
  - Estado
  - Prioridad
  - Fecha
- Visualización del historial y detalle de cada ticket.
- Validaciones básicas de formularios (campos requeridos, formatos, coincidencia de contraseñas, etc.).
- Módulo de administración de usuarios y parámetros del sistema.

> Para un detalle más exhaustivo de los casos de uso, diagramas y requisitos, consultar el PDF del proyecto.

---

## 🛠️ Tecnologías utilizadas

El desarrollo del sistema se basa en el stack típico de aplicaciones web con PHP:

- **Lenguajes**
  - PHP (lógica de servidor)
  - HTML5
  - CSS3
  - JavaScript

- **Frameworks / Librerías**
  - Bootstrap (diseño responsivo y componentes visuales)
  - (Opcional) Librerías JS para mejoras de interfaz (alertas, validaciones, etc.)

- **Base de datos**
  - MySQL / MariaDB

- **Servidor web**
  - WAMP / XAMPP o similar (Apache + PHP + MySQL)

---

## 🧩 Arquitectura general

La aplicación está organizada bajo una estructura clásica de proyecto PHP:

- Separación básica entre **presentación** (vistas), **lógica de negocio** (controladores) y **acceso a datos**.
- Uso de formularios HTML para la interacción con el usuario.
- Consultas SQL para la gestión de la información de usuarios y tickets.
- Validación de datos en el lado del servidor y, en algunos casos, en el lado del cliente.

Los diagramas de casos de uso, clases, secuencia y otros modelos UML se encuentran descritos en el informe en PDF.

---

## 📂 Estructura recomendada del proyecto

> Los nombres de carpetas pueden variar según tu implementación real. Ajusta esta sección si es necesario.

```text
mesa-ayuda/
├── docs/
│   ├── ProyectoFinal-Ing-Web.pdf      # Informe del proyecto (versión entregada)
│   └── mesa-ayuda-banner.png          # Imagen principal usada en este README
├── sql/
│   └── mesa_ayuda.sql                 # Script de creación de la base de datos
├── src/                               # Código fuente principal
│   ├── config/                        # Configuración (conexión a BD, constantes, etc.)
│   ├── controllers/                   # Lógica de negocio
│   ├── models/                        # Clases de acceso a datos
│   ├── views/                         # Vistas y plantillas
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   └── index.php                      # Punto de entrada de la aplicación
└── README.md
```

---

## 🚀 Puesta en marcha en local

### 1. Requisitos previos

- PHP 7.4+ o 8.x  
- MySQL / MariaDB  
- Servidor web (Apache) – WAMP, XAMPP, Laragon u otro  
- Git (opcional pero recomendado)

### 2. Clonar el repositorio

En tu carpeta de proyectos (por ejemplo, el directorio `www` de WAMP):

```bash
git clone https://github.com/eleazarrrg/Proyecto-Mesa-Ayuda.git
cd Proyecto-Mesa-Ayuda
```

> Ajusta el nombre de la carpeta si el repositorio tiene otro nombre.

### 3. Configurar la base de datos

1. Crear una base de datos en MySQL, por ejemplo:

```sql
CREATE DATABASE mesa_ayuda CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

2. Importar el script SQL del proyecto (por ejemplo, `sql/mesa_ayuda.sql`) desde phpMyAdmin o la consola de MySQL.

3. Configurar el archivo de conexión (por ejemplo, `src/config/db.php`) con tus credenciales:

```php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "mesa_ayuda";
```

### 4. Configurar el servidor

- Si usas WAMP/XAMPP, coloca el proyecto dentro de la carpeta:
  - `C:\wamp64\www\mesa-ayuda` o
  - `C:\xampp\htdocs\mesa-ayuda`
- Inicia Apache y MySQL.
- Abre en el navegador:

```text
http://localhost/mesa-ayuda/
```

---

## 🧪 Pruebas y validaciones

En el documento del proyecto se describen diversos **casos de uso** y **escenarios de prueba**, tanto exitosos como fallidos, por ejemplo:

- Registro de nuevo usuario con:
  - Contraseñas que no coinciden.
  - Campos obligatorios vacíos.
  - Correos duplicados.
- Creación de ticket con:
  - Campos incompletos.
  - Categorías o prioridades inválidas.
- Inicio de sesión con:
  - Usuario no registrado.
  - Contraseña incorrecta.
  - Usuario deshabilitado.

Estos casos de prueba se utilizaron para verificar que el sistema maneja correctamente los errores y muestra mensajes adecuados al usuario.

---

## 📈 Posibles mejoras

Algunas extensiones futuras propuestas en el informe o recomendadas para versiones posteriores:

- Envío de notificaciones por correo al crear o actualizar tickets.
- Dashboard con métricas (tiempos promedio de respuesta, tickets abiertos/cerrados, etc.).
- Exportación de reportes a PDF/Excel.
- Buscador avanzado de tickets.
- Historial detallado de acciones por ticket.
- Implementación de control de acceso más robusto (roles y permisos más granulares).
- Mejoras en seguridad:
  - Hash seguro de contraseñas.
  - Protección contra inyección SQL.
  - Filtros y sanitización de datos de entrada.

---

## 👥 Equipo de trabajo

Este proyecto fue desarrollado como parte del curso:

- **Asignatura:** Ingeniería Web (1SF132) – II Semestre 2025  
- **Universidad:** Universidad Tecnológica de Panamá  
- **Profesora:** Ing. Irina Fong  

> Los nombres de los integrantes del equipo pueden añadirse en esta sección, tal como aparecen en la portada del informe en PDF.

---

## 📎 Relación con el documento del proyecto

El archivo PDF incluido en la carpeta `docs/` (por ejemplo, `ProyectoFinal-Ing-Web.pdf`) contiene:

- Planteamiento del problema y justificación.
- Objetivos general y específicos.
- Análisis de requerimientos.
- Diagramas (casos de uso, clases, secuencia, etc.).
- Diseño de la base de datos.
- Detalle de la implementación.
- Pruebas realizadas y resultados.
- Conclusiones y recomendaciones.

Este README resume y complementa ese documento, sirviendo como guía rápida para cualquier persona que desee **instalar, revisar o continuar el desarrollo** del sistema Mesa de Ayuda.

---

## 📜 Licencia

Este proyecto se desarrolló con fines **académicos**.  
Si deseas reutilizar parte del código o adaptarlo, se recomienda citar el trabajo original y la asignatura en la que fue elaborado.

