<?php

namespace Module\Page;
use \Lib\Mysql as Mysql;
use \Module\Auth as Auth;

class Page {

	private $db = null;

	function __construct()
	{
		$this->db = new Mysql();
	}

	public function home($params, $queries) 
	{
		include_once PATH_PUBLIC . '/index.html';
		exit();
	}

	public function profile($params, $queries)
	{
		(new Auth)->check();	
	
		include_once PATH_TEMPLATE . '/page/profile.php';
		exit();
	}

	public function userData($params, $queries)
	{
		//(new Auth)->check();
		if(!isset($_POST['link'])) return false;

		$link = $_POST['link'];
		$sql = 
		"select id, name
			from user
			where code = :link";
		$res = $this->db->query($sql, [":link" => $link]);
		if(isset($res[0])) return $res[0];
		return false;
	}

	public function check($params, $queries)
	{
		$code = $params[0] ?? false;
		if(!$code) return goToHome();

		$sql =
		"select id
			from user
			where verification_link = :code";
		$res = $this->db->query($sql, [":code" => $code]);
		if(isset($res[0]) && $res[0]['id']) {
			$sql =
			"update user
				set verification_link = null,
					verified = now()
				where id = :id";
			$this->db->query($sql, [":id" => $res[0]['id']]);
			
			include_once PATH_TEMPLATE . '/page/check.php';
			exit;
		}

		goToHome();
	}

	
}