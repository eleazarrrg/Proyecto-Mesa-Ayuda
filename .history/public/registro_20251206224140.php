<?php
// public/registro.php
session_start();

require_once __DIR__ . '/../app/core/Validator.php';
require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/models/Estudiante.php';

$usuarioModel    = new Usuario();
$estudianteModel= new Estudiante();

$errores = [];
$mensaje_exito = '';

// ID del rol ESTUDIANTE (ajusta si tu tabla roles es distinta)
$ROL_ESTUDIANTE_ID = 3;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos personales (estudiantes)
    $primer_nombre    = Validator::sanitizeString($_POST['primer_nombre'] ?? '');
    $segundo_nombre   = Validator::sanitizeString($_POST['segundo_nombre'] ?? '');
    $primer_apellido  = Validator::sanitizeString($_POST['primer_apellido'] ?? '');
    $segundo_apellido = Validator::sanitizeString($_POST['segundo_apellido'] ?? '');
    $sexo             = Validator::sanitizeString($_POST['sexo'] ?? '');
    $identificacion   = Validator::sanitizeString($_POST['identificacion'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $email            = Validator::sanitizeEmail($_POST['email'] ?? '');
    $telefono         = Validator::sanitizeString($_POST['telefono'] ?? '');
    $tipo             = 'ESTUDIANTE';

    // Datos de acceso (usuarios)
    $username         = Validator::sanitizeString($_POST['username'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password2        = $_POST['password2'] ?? '';

    // Validaciones básicas
    if (!Validator::required($primer_nombre))   $errores[] = "El primer nombre es obligatorio.";
    if (!Validator::required($primer_apellido)) $errores[] = "El primer apellido es obligatorio.";
    if (!Validator::required($sexo))            $errores[] = "Debe seleccionar el sexo.";
    if (!Validator::required($identificacion))  $errores[] = "La identificación es obligatoria.";
    if (!Validator::required($fecha_nacimiento))$errores[] = "La fecha de nacimiento es obligatoria.";
    if (!Validator::required($email))           $errores[] = "El email es obligatorio.";
    if (!Validator::isEmail($email))            $errores[] = "El email no tiene un formato válido.";

    if (!Validator::required($username))        $errores[] = "El nombre de usuario es obligatorio.";
    if (!Validator::required($password) || !Validator::required($password2)) {
        $errores[] = "La contraseña y su confirmación son obligatorias.";
    } else {
        if ($password !== $password2) {
            $errores[] = "Las contraseñas no coinciden.";
        }
        if (strlen($password) < 8) {
            $errores[] = "La contraseña debe tener al menos 8 caracteres.";
        }
    }

    // Validaciones de duplicados
    if ($usuarioModel->obtenerPorUsername($username)) {
        $errores[] = "El nombre de usuario ya está en uso.";
    }
    if ($usuarioModel->obtenerPorEmail($email)) {
        $errores[] = "El email ya está registrado.";
    }

    // (Opcional) validación simple de fecha
    if ($fecha_nacimiento !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_nacimiento)) {
        $errores[] = "La fecha de nacimiento debe estar en formato YYYY-MM-DD.";
    }

    // Manejo de foto (opcional). Por simplicidad, solo guardamos el nombre del archivo.
    $foto_perfil = null;
    if (!empty($_FILES['foto_perfil']['name'])) {
        $nombreArchivo = basename($_FILES['foto_perfil']['name']);
        $destino = __DIR__ . '/../assets/uploads/' . $nombreArchivo;

        // Crea la carpeta si no existe
        if (!is_dir(__DIR__ . '/../assets/uploads/')) {
            mkdir(__DIR__ . '/../assets/uploads/', 0777, true);
        }

        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $destino)) {
            $foto_perfil = 'assets/uploads/' . $nombreArchivo;
        } else {
            $errores[] = "No se pudo guardar la foto de perfil.";
        }
    }

    if (empty($errores)) {
        // 1) Crear registro en estudiantes (perfil)
        $colData = [
            'primer_nombre'    => $primer_nombre,
            'segundo_nombre'   => $segundo_nombre,
            'primer_apellido'  => $primer_apellido,
            'segundo_apellido' => $segundo_apellido,
            'sexo'             => $sexo,
            'fecha_nacimiento' => $fecha_nacimiento,
            'foto_perfil'      => $foto_perfil,
            'identificacion'   => $identificacion,
            'email'            => $email,
            'telefono'         => $telefono,
            'tipo'             => $tipo,
        ];

        if (!$estudianteModel->crear($colData)) {
            $errores[] = "Error al registrar el perfil del estudiante.";
        } else {
            // 2) Crear usuario de sistema (rol ESTUDIANTE)
            $nombreCompleto = trim($primer_nombre . ' ' . $primer_apellido);
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userData = [
                'nombre'        => $nombreCompleto,
                'username'      => $username,
                'email'         => $email,
                'password_hash' => $hash,
                'rol_id'        => $ROL_ESTUDIANTE_ID,
                'activo'        => 1,
            ];

            if ($usuarioModel->crear($userData)) {
                $mensaje_exito = "Registro completado correctamente. Ya puedes iniciar sesión.";
            } else {
                $errores[] = "Error al crear el usuario para iniciar sesión.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Estudiante</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Registro de Estudiante</h1>

    <p><a href="index.php">Volver al login</a></p>

    <?php if (!empty($errores)): ?>
        <ul style="color:red;">
            <?php foreach ($errores as $e): ?>
                <li><?= htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($mensaje_exito): ?>
        <p style="color:green;"><?= htmlspecialchars($mensaje_exito); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <h3>Datos personales</h3>
        <label>Primer nombre:</label><br>
        <input type="text" name="primer_nombre" value="<?= htmlspecialchars($primer_nombre ?? ''); ?>"><br><br>

        <label>Segundo nombre:</label><br>
        <input type="text" name="segundo_nombre" value="<?= htmlspecialchars($segundo_nombre ?? ''); ?>"><br><br>

        <label>Primer apellido:</label><br>
        <input type="text" name="primer_apellido" value="<?= htmlspecialchars($primer_apellido ?? ''); ?>"><br><br>

        <label>Segundo apellido:</label><br>
        <input type="text" name="segundo_apellido" value="<?= htmlspecialchars($segundo_apellido ?? ''); ?>"><br><br>

        <label>Sexo:</label><br>
        <select name="sexo">
            <option value="">-- Selecciona --</option>
            <option value="M" <?= (isset($sexo) && $sexo == 'M') ? 'selected' : ''; ?>>Masculino</option>
            <option value="F" <?= (isset($sexo) && $sexo == 'F') ? 'selected' : ''; ?>>Femenino</option>
        </select><br><br>

        <label>Identificación:</label><br>
        <input type="text" name="identificacion" value="<?= htmlspecialchars($identificacion ?? ''); ?>"><br><br>

        <label>Fecha de nacimiento:</label><br>
        <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($fecha_nacimiento ?? ''); ?>"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? ''); ?>"><br><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?= htmlspecialchars($telefono ?? ''); ?>"><br><br>

        <label>Foto de perfil (opcional):</label><br>
        <input type="file" name="foto_perfil" accept="image/*"><br><br>

        <h3>Datos de acceso</h3>
        <label>Usuario:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($username ?? ''); ?>"><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password"><br><br>

        <label>Repetir contraseña:</label><br>
        <input type="password" name="password2"><br><br>

        <button type="submit">Registrarme</button>
    </form>
</body>
</html>
