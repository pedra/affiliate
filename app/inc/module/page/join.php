<?php

namespace Module\Page;
use \Lib\Mysql as Mysql;
use \Module\Auth as Auth;

class Join {

	private $db = null;

	function __construct()
	{
		$this->db = new Mysql();
	}

	public function index ($params, $queries)
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

	public function searchCountry($params, $queries)
	{
		if(!$params[0]) return false;
		$query = $params[0];

		$sql =
		"select id, name, iso3, phonecode, native
			from country
			where name like concat('%', :q, '%')
			or native  like concat('%', :q, '%')";

		$res = $this->db->query($sql, [":q" => $query]);
		
		if($res[0]) return $res[0];
		return false;
	}

}