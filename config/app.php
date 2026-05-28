<?php

/*
| CONFIGURACIÓN GENERAL DE LA APLICACIÓN : BASE_URL guarda la ruta base del proyecto.
| se crea constante global para no rescribir la ruta base en 
| controladores y formularios.
*/

define('BASE_URL', '/Impulso_Fitness');

/*
 | CONFIGURACIÓN DE CORREO (SMTP)
 | Modifica estos valores con las credenciales de tu proveedor SMTP.
 | Si no deseas usar SMTP, deja MAILER_SMTP_ENABLED en false y el sistema usará mail().
*/
define('MAILER_SMTP_ENABLED', true);
define('MAILER_HOST', 'smtp.office365.com');
define('MAILER_PORT', 587);
define('MAILER_USERNAME', 'angel_jesus_urias@hotmail.com');
define('MAILER_PASSWORD', 'Kaleigh_Preciado1929');
define('MAILER_SMTP_SECURE', 'tls'); // 'tls' o 'ssl'
define('MAILER_FROM', 'angel_jesus_urias@hotmail.com');
define('MAILER_FROM_NAME', 'Impulso Fitness');
