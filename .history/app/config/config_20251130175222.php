<?php
// app/config/config.php

// Nombre de la app (solo para títulos, etc.)
define('APP_NAME', 'Mesa de Ayuda UTP');

// URL base del proyecto (ajusta según tu carpeta en WAMP)
// Ejemplo si el proyecto está en: C:\wamp64\www\mesa-ayuda\public
// y lo abres como: http://localhost/mesa-ayuda/public
define('APP_URL', 'http://localhost/mesa-ayuda/public');

// Configuración de zona horaria
date_default_timezone_set('America/Panama');

// (Opcional) Datos de BD por si luego quieres usarlos en Database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mesa_ayuda');
define('DB_USER', 'root');
define('DB_PASS', '');
