<?php

//pega endereço relativo para links
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/');
//codificação usada
define('UNICODE_CHARSET', 'utf8');
//definição do caminho de procura do sistema
define('SYSTEM_PATH', 'system/');
//definição do caminho do framework
define('LIBRARY_PATH', 'library/');

//inclusão do arquivo do framework
require LIBRARY_PATH . 'GrehFW/GrehFW.class.php';

//GrehFw Start
$fw = new GrehFW();