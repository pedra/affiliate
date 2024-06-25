<?php
include_once __DIR__ . "/inc/utils.php";
include_once __DIR__ . "/inc/start.php";

// ROUTER ----------------------------------------------------------------------
(new Lib\Router())
	// Page
	->get('/', '\Module\Page', 'home')
	// ->get('/join', '\Module\Page', 'join')
	->get('/profile', '\Module\Page', 'profile')
	->post('/profile', '\Module\Page', 'userData')


	
	// Auth
	->post('/login', '\Module\Auth', 'login')
	->post('/logout', '\Module\Auth', 'logout')
	
	->get('/(.*)', '\Module\Page', 'join')
	->run();