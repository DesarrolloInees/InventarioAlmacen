<?php
// app/config/config_global.php

if (!defined('ENTRADA_PRINCIPAL')) die("Acceso denegado.");

/* =========================================================
    1. VARIABLES DE ENTORNO Y SISTEMA
========================================================= */
define('URL_SISTEMA', 'http://localhost/InventarioAlmacen/'); // Cambiar cuando subas al server
define('ZONA_HORARIA', 'America/Bogota');

/* =========================================================
    2. CONFIGURACIÓN DE ALERTAS DE INVENTARIO (SMTP)
========================================================= */
define('ALERTAS_SMTP_HOST', 'smtp.gmail.com');
define('ALERTAS_SMTP_USER', 'ineesmensajesautomaticos@gmail.com');
define('ALERTAS_SMTP_PASS', 'bhoh svdq qvfl rxwy');
define('ALERTAS_SMTP_PORT', 465);
define('ALERTAS_SMTP_SECURE', 'ssl'); 

define('ALERTAS_REMITENTE_EMAIL', 'ineesmensajesautomaticos@gmail.com');
define('ALERTAS_REMITENTE_NOMBRE', 'Alertas I-Nexis Almacén');
define('ALERTAS_CORREO_DESTINO', 'supervisorsat@inees.co; laboratorio@inees.co');

/* =========================================================
    3. CONFIGURACIÓN DE WHATSAPP (CALLMEBOT)
========================================================= */
define('ALERTAS_WHATSAPP_PHONE', ''); 
define('ALERTAS_WHATSAPP_APIKEY', '');

/* =========================================================
    4. REGLAS DE NEGOCIO DEL ALMACÉN
========================================================= */
define('ALERTAS_UMBRAL_STOCK_CRITICO', 10); 
define('ALERTAS_MODO_PRUEBA', true); 
define('ALERTAS_CORREO_PRUEBAS', 'aquilesbedoya37@gmail.com');