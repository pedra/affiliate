<?php

namespace Module\Page;

use \Lib\Mysql as Mysql;
use \Module\Auth as Auth;

class BasePage
{

	protected $db = null;

	function __construct()
	{
		$this->db = new Mysql();
	}

	public function userData($params, $queries)
	{
		//(new Auth)->check();
		if (!isset($_POST['link']))
			return false;

		$link = $_POST['link'];
		$sql =
			"select id, name
			from user
			where code = :link";
		$res = $this->db->query($sql, [":link" => $link]);
		if (isset($res[0]))
			return $res[0];
		return false;
	}


	public function notFound($params, $queries)
	{
		include_once PATH_TEMPLATE . '/page/page404.php';
		exit();
	}

	public function goToHome()
	{
		goToHome();
		exit();
	}

	public function countries()
	{
		$sql =
			"select id, name, iso3, phonecode, native
			from country
			order by name asc";

		$res = $this->db->query($sql);

		if ($res[0])
			return $res;
		return false;
	}
}