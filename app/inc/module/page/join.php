<?php

namespace Module\Page;

class Join extends BasePage {

	function __construct()
	{
		parent::__construct();
	}

	public function index ($params, $queries)
	{
		$sql =
			"select id, name, code
			from user
			where code = :code";
		$res = $this->db->query($sql, [":code" => $params[0]]);
		if (isset($res[0])) {

			$name = $res[0]['name'];
			$id = $res[0]['id'];
			$code = $res[0]['code'];

			include_once PATH_TEMPLATE . '/page/join.php';

			/* TODO: 

				1 - Check if the user is verified + approved
				2 - Create country list on <datalist id="countries"></datalist> (e.g.: template/join.html)

			*/
			exit;
		}

		goToHome();
	}

	public function verified(){
		include_once PATH_TEMPLATE . '/page/verified.php';
		exit;
	}
}