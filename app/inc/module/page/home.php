<?php

namespace Module\Page;

class Home extends BasePage {

	function __construct()
	{
		parent::__construct();
	}
	
	public function index($params, $queries) 
	{
		include_once PATH_PUBLIC . '/index.html';
		exit();
	}
}