<?php
// Para crear la contraseña hash
echo password_hash('12345678', PASSWORD_DEFAULT);

/*
Para ingresar un admin en la base de datos

INSERT INTO usuarios (username, password_hash, nombre, email, rol_id, activo)
VALUES (
  'admin',
  'PON TU HASH AQUÍ',
  'Administrador General',
  'admin@utp.ac.pa',
  1,
  1
);

*/