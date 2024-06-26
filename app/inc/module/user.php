<?php

namespace Module;
use \Lib\Mysql as Mysql;
use \Module\Auth as Auth;

class User {

	private $db = null;

	function __construct()
	{
		$this->db = new Mysql();
	}

	public function getUserByLink($params, $queries)
	{
		(new Auth)->check();

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

	public function x ()
	{
		return rand(1000,9990);
	}

	public function submit($params, $queries)
	{
		if(!isset($_POST['name']) && $_POST['name'] != '')
			return ['error' => true, 'msg' => '"Name" cannot be empty!'];
		if(!isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			return ['error' => true, 'msg' => '"Email" is not valid!'];
		if(!isset($_POST['password']) && strlen($_POST['password']) < 6)
			return ['error' => true, 'msg' => '"Password" must be at least 6 characters!'];
		if(!isset(($_POST['country'])) && !$this->countryExists(0 + $_POST['country'])) 
			return ['error' => true, 'msg' => '"Country" is not valid!'];
		if(!isset($_POST['phone']) && strlen($_POST['phone']) < 6)
			return ['error' => true, 'msg' => '"Phone" must be at least 6 characters!'];
		if(!isset($_POST['company']) && strlen($_POST['company']) < 6)
			return ['error' => true, 'msg' => '"Company" must be at least 6 characters!'];
		if(!isset($_POST['projects']) && !str_contains($_POST['projects'], 'around'))
			return ['error' => true, 'msg' => '"Projects" must contain "around"!'];

		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		if($this->emailExists($email))
			return ['error' => true, 'msg' => 'Email already exists!'];

		$password = trim($_POST['password']);
		$country = 0 + $_POST['country'];
		$phone = trim($_POST['phone']);
		$company = trim($_POST['company']);
		$projects = trim($_POST['projects']);

		$vkey = rand(100000, 999999);

		$res = 72456;
		$up = 72456;
		
		$sql =
		"insert into user
			set verified=NULL,
			code=:code,
			vkey=:vkey,
			secpass=:secpass,

			name=:name,
			email=:email,
			password=:password,
			country=:country,
			phone=:phone,
			company=:company,
			projects=:projects";
		$res = $this->db->insert($sql, [
			":secpass" => secured_encrypt($password),
			":code" => "111",
			":vkey" => $vkey,

			":name" => $name,
			":email" => $email,
			":password" => password_hash($password, PASSWORD_DEFAULT),
			":country" => $country,
			":phone" => $phone,
			":company" => $company,
			":projects" => $projects
		]);

		if($res) {
			$code = (new \Lib\Can())->encode($res + rand(1000, 9990));
			$sql = "update user
					set code=:code
					where id=:id";
			$up = $this->db->update($sql, [":code" => $code, ":id" => $res]);
			if($up) return [
				'error' => false, 
				'msg' => 'Thank you for your registration!', 
				'id' => $res,
				'vkey' => $vkey,
				'code' => $code,
				'link' => ENV['URL'] . '/' . $code
			];
		}
		return ['error' => true, 'msg' => 'Registration failed!'];
	}

	public function countryExists($country)
	{
		$sql =
		"select id
			from country
			where id = :country";
		$res = $this->db->query($sql, [":country" => $country]);
		if(isset($res[0])) return true;
		return false;
	}

	public function emailExists($email)
	{
		$sql =
		"select id
			from user
			where email = :email";
		$res = $this->db->query($sql, [":email" => $email]);
		if(isset($res[0])) return true;
		return false;
	}
}
