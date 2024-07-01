<?php

namespace Module\Page;

class Home extends BasePage {

	function __construct()
	{
		parent::__construct();
	}

	public function index($params, $queries)
	{
		$code = $params[0] ?? false;
		if(!$code) return goToHome();

		$sql =
		"select id, verification_link
			from user
			where verification_link = :code";
		$res = $this->db->query($sql, [":code" => $code]);
		if(isset($res[0]) && $res[0]['id']) {			
			include_once PATH_TEMPLATE . '/page/check.php';
			exit;
		}

		goToHome();
	}
}