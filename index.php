<?php
/**
 * frameduzPHP v7
 *
 * @Author  	: M. Hanif Afiatna <hanif.softdev@gmail.com>
 * @Since   	: version 7.0.0
 * @Date		: 04 Agustus 2020
 * @package 	: core system
 * @Description : 
 */
// Set default timezone
date_default_timezone_set('Asia/Jakarta');
// Set constant is where folder located
define('ROOT', dirname(__FILE__) . '/');
define('APP', dirname(__FILE__) . '/app/');
define('ASSETS', dirname(__FILE__) . '/assets/');
define('CONFIG', dirname(__FILE__) . '/config/');
define('COMP', dirname(__FILE__) . '/comp/');
define('LOGS', dirname(__FILE__) . '/logs/');
define('SYSTEM', dirname(__FILE__) . '/system/');
define('TEMPLATE', dirname(__FILE__) . '/template/');
define('UPLOAD', dirname(__FILE__) . '/upload/');
define('VENDOR', dirname(__FILE__) . '/vendor/');
define('COOKIE_EXP', (3600 * 24)); // 24 Jam / 1 Hari
// Set constant INDEX_FILE is the default file name
define('INDEX_FILE', basename(__FILE__));
// Set constant TIME_LOAD is the first time loader
define('TIME_LOAD', microtime(true));
define('MEMORY_LOAD', memory_get_usage());
// Running Application
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Load Composer Engine
require_once VENDOR . 'autoload.php';
require_once 'Frameduz.php';
?>