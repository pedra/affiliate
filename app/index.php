<?php
include_once __DIR__ . "/inc/utils.php";
include_once __DIR__ . "/inc/start.php";

// Redirects if host is fd2e.com
if ($_SERVER['HTTP_HOST'] == 'fd2e.com') {
	goToUrl(ENV['URL'] . '/c' . $_SERVER['REQUEST_URI']);
	exit;
}

// ROUTER ----------------------------------------------------------------------
(new Lib\Router())
	// Page
	->get('/', '\Module\Page\Home', 'index')
	->get('/profile', '\Module\Page\Profile', 'index')

	
	// Check the link sent to the email in the affiliate registration.
	->get('/v/(.*)', '\Module\Page\Check', 'index')
	->get('/v', '\Module\Page\Check', 'goToHome')

	// DEBUG
	->get('/verified', '\Module\Page\Join', 'verified')
	// DEBUG



	// APIs
	->get('/a/countries', '\Module\Page\Join', 'countries')

	// Add affiliate
	->post('/a/submit', '\Module\User', 'submit')
	->post('/a/verify', '\Module\User', 'verify')

	// User
	->post('/profile', '\Module\User', 'getUserByLink')
	
	// Auth
	->post('/login', '\Module\Auth', 'login')
	->post('/logout', '\Module\Auth', 'logout')
	
	// Join by affiliate link
	->get('/c/(.*)', '\Module\Page\Join', 'index')
	
	// Show "home" page if no route is found
	->get('(.*)', '\Module\Page\Home', 'notFound')
	->run();