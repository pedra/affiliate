<?php

namespace Module;
use \Lib\Mysql as Mysql;

class Auth {

	private $db = null;

	function __construct()
	{
		$this->db = new Mysql();
	}

	public function check ()
	{
		if (!isset($_SESSION['user'])) goTo404();

		$id = 0 + $_SESSION['user'];

		$res = $this->db->query(
			" select id from user where id = :id",
			[":id" => $id]
		);
		if (!isset($res[0])) goTo404();

		return $id;
	}

	public function register ($params, $queries)
	{
		if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password']))
			return false;

		$name = $_POST['name'];
		$email = $_POST['email'];
		$password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
		$secpass = secured_encrypt(trim($_POST['password']));

		$sql = "
			insert into user
			(name, email, secpass, password)
			values
			(:name, :email, :secpass, :password)
		";
		$res = $this->db->query($sql, [
			":name" => $name, 
			":email" => $email,
			":secpass" => $secpass,
			":password" => $password
		]);
		return $res;
	}

	public function login ($params, $queries)
	{
		if(!isset($_POST['email']) || !isset($_POST['password']))
			return false;

		$email = $_POST['email'];
		$password = trim($_POST['password']);

		$sql = "
			select id, name, password
			from user
			where email = :email";
		$res = $this->db->query($sql, [":email" => $email]);
		if(isset($res[0]) && password_verify($password, $res[0]['password'])) {
			$user = ["name" => $res[0]["name"], "id" => $res[0]["id"]];
			$_SESSION['user'] = $user["id"];
			return $user;
		}
		return false;
	}

	public function test ($params, $queries)
	{
		$sql = 
		"select 
			u.id uid,
			u.name name,
			u.email email,
			u.verified verified,
			u.approved approved,
			u.code code,
			u.vkey vkey,
			u.phone phone, 
			u.company company,
			u.project project,
			c.name as country_name,
			c.iso3 country_sigla,
			c.native native,
			c.phonecode phonecode,
			c.emojiU emojiU,
			c.emoji emoji
		
		from user u
		left join country c on u.country = c.id";

		$res = $this->db->query($sql);

		e($res, true);
	}

}