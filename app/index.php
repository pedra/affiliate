<?php
include_once __DIR__ . "/inc/utils.php";
include_once __DIR__ . "/inc/start.php";

// ROUTER ----------------------------------------------------------------------
(new Lib\Router())
	// Page
	->get('/', '\Module\Page', 'home')
	->get('/profile', '\Module\Page', 'profile')

	// APIs
	->get('/a/country/(.*)', '\Module\Page\Join', 'searchCountry')
	->post('/profile', '\Module\User', 'getUserByLink')



	
	// Auth
	->post('/login', '\Module\Auth', 'login')
	->post('/logout', '\Module\Auth', 'logout')
	
	->get('/(.*)', '\Module\Page\Join', 'index')
	->run();