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
			"select id, name, code
			from user
			where code = :code";
		$res = $this->db->query($sql, [":code" => $params[0]]);
		if (isset($res[0])) {
			$join = file_get_contents(PATH_PUBLIC . '/join/index.html');

			$join = str_replace('[[name]]', $res[0]['name'], $join);
			$join = str_replace('[[id]]', $res[0]['id'], $join);
			$join = str_replace('[[code]]', $res[0]['code'], $join);

			/* TODO: 

				1 - Check if the user is verified + approved
				2 - Create country list on <datalist id="countries"></datalist> (e.g.: template/join.html)

			*/
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

	public function countries () {
		$sql =
		"select id, name, iso3, phonecode, native
			from country
			order by name asc";

		$res = $this->db->query($sql);

		if($res[0]) return $res;
		return false;
	}

}