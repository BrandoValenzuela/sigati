<?php
/**
 * SigaTI - Sistema de Gestión de Activos de TI
 * Configuración General del Sistema
 */

// Evitar acceso directo al archivo por seguridad
if (count(get_included_files()) === 1) exit("Acceso denegado.");

// Definición de la ruta raíz del proyecto
define('BASE_URL', 'http://localhost/sigati/');

// Nombre oficial del sistema para las vistas
define('APP_NAME', 'SigaTI - Instituto Sebastián Cabot');

// Configuración de la zona horaria institucional
date_default_timezone_set('America/Mexico_City');