<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_error_handler('exceptions_error_handler');

header("Access-Control-Allow-Origin: *");
// header("Content-Security-Policy: connect-src *; default-src *");

define('PATH_ROOT', dirname(dirname(__FILE__)));
define('PATH_INC', PATH_ROOT . '/inc');
define('PATH_PUBLIC', PATH_ROOT . '/public');
define('PATH_TEMPLATE', PATH_ROOT . '/template');
define('ENV', parse_ini_file(dirname(PATH_ROOT) . "/.env"));

// Load static content ---------------------------------------------------------
static_content();

// AUTO LOADER -----------------------------------------------------------------
spl_autoload_register(function ($class) {
	$path = PATH_INC . '/' . strtolower(str_replace('\\', '/', $class) . '.php');
	if (file_exists($path)) include_once $path;
});

// CACHE & SESSION -------------------------------------------------------------
ob_start("ob_gzhandler");
session_name("plm45022");
session_start();