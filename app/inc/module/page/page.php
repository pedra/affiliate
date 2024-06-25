<?php

namespace Module;
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

	public function join($params, $queries)
	{
		$sql =
			"select id, name
			from user
			where code = :link";
		$res = $this->db->query($sql, [":link" => $params[0]]);
		if (isset($res[0])) {		
			$join = file_get_contents(PATH_PUBLIC . '/join/index.html');
			$join = str_replace('</body>', '<input type="hidden" id="aft-link" value="' . $params[0] . '"/></body>', $join);
			exit($join);
		}

		goToHome();
	}

	public function profile($params, $queries)
	{
		(new Auth)->check();	
	
		include_once PATH_PUBLIC . '/profile/index.html';
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

	
}