<?php

define('ENV_PATH', '../keys.env');

function loadEnv($path)
{
    if (!file_exists($path)) {
        echo "No existe el archivo {$path}\n";
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (strpos(trim($line), '#') === 0) continue;

        // Buscar el signo =
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);

            // Limpiamos espacios y comillas accidentales
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            $_ENV[$name] = $value;
            // Cargar en el sistema por si acaso
            putenv("$name=$value");
        }
    }
    return true;
}

loadEnv(ENV_PATH);
