<?php

namespace Module;
use \Lib\Mysql as Mysql;
use \Lib\Mail\Mail as Mail;
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
		if(!isset($_POST['affiliate']) && $_POST['affiliate'] == '')
			return ['error' => true, 'msg' => 'This affiliate is not enabled!<br>Check if the <b>link</b> is correct or contact us.'];
		
		$affiliate = trim($_POST['affiliate']);
		if(!$this->userExists($affiliate))
			return ['error' => true, 'msg' => 'This affiliate is not enabled!<br>Check if the <b>link</b> is correct or contact us.'];

		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		if($this->emailExists($email))
			return ['error' => true, 'msg' => 'Email already exists!'];

		$password = trim($_POST['password']);
		$country = 0 + $_POST['country'];
		$phone = trim($_POST['phone']);
		$company = trim($_POST['company']);
		$projects = trim($_POST['projects']);

		$verification_key = rand(100000, 999999);
		$verification_link = uniqid();

		$res = 72456;
		$up = 72456;
		
		$sql =
		"insert into user
			set affiliate=:affiliate,
			created=NOW(),
			verified=NULL,
			approved=NULL,
			code=:code,
			verification_key=:verification_key,
			verification_link=:verification_link,
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
			":verification_key" => $verification_key,
			":verification_link" => $verification_link,

			":affiliate" => $affiliate,
			":name" => $name,
			":email" => $email,
			":password" => password_hash($password, PASSWORD_DEFAULT),
			":country" => $country,
			":phone" => $phone,
			":company" => $company,
			":projects" => $projects
		]);

		if($res) {
			$can = new \Lib\Can();
			$code = $can->encode($res) . $can->encode(rand(1000, 9990));
			$sql = "update user
					set code=:code
					where id=:id";
			$up = $this->db->update($sql, [
				":code" => $code, 
				":id" => $res
			]);
			
			if($up) {
				
				$link = ENV['SHORT_URL'] . '/' . $link;
				$vlink = ENV['URL'] . '/check/' . $verification_link;
				$this->sendMailVerification($email, $verification_key, $vlink);
				
				return [
					'error' => false, 
					'msg' => 'Thank you for your registration!', 
					'id' => $res,
					'verification_key' => $verification_key,
					'code' => $code,
					'link' => $link,
					'vlink' => $vlink
				];
			}
		}
		return ['error' => true, 'msg' => 'Registration failed!'];
	}

	public function userExists($user)
	{
		$sql =
		"select id
			from user
			where id = :user
			and verified is not null
			and approved is not null";
		$res = $this->db->query($sql, [":user" => $user]);
		if(isset($res[0])) return true;
		return false;
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
		// DEVELOP ONLY - BEGIN
		if ($email == 'email@.email.com') return false;
		// DEVELOP ONLY - END

		$sql =
		"select id
			from user
			where email = :email";
		$res = $this->db->query($sql, [":email" => $email]);
		if(isset($res[0])) return true;
		return false;
	}

	public function sendMailVerification ($email, $verification_key, $link)
	{
		$mail = new Mail();
		include PATH_INC.'/template/mail/verify.php';

		// DEVELOP ONLY - BEGIN
		$to = [
			'paulo.rocha@outlook.com',
			'ahcordesign@gmail.com'
		];
		if ($email != 'email@.email.com') array_push($to, $email);
		// DEVELOP ONLY - END

		return $mail->send(
			$to,
			$subject,			
			$body,
			$altBody
		);
	}

	/* TODO: verifyEmail() 👇
		
		1 - Force user to type password, before.

	*/

	public function verifyEmail ($params, $queries)
	{
		if(!isset($_POST['verification_key'])) return false;

		$verification_key = $_POST['verification_key'];
		$sql =
		"select id, verified
			from user
			where verification_key = :verification_key";
		$res = $this->db->query($sql, [":verification_key" => $verification_key]);
		if(isset($res[0])) {
			if($res[0]['verified'] == NULL) {
				$sql =
				"update user
					set verified=NOW()
					where verification_key=:verification_key";
						$this->db->update($sql, [":verification_key" => $verification_key]);
				return ['error' => false, 'msg' => '']];
			}
		}
		return false;
	}

	public function login ($params, $queries)
	{
		if(!isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			return ['error' => true, 'msg' => '"Email" is not valid!'];
		if(!isset($_POST['password']) && strlen($_POST['password']) < 6)
			return ['error' => true, 'msg' => '"Password" must be at least 6 characters!'];

		$email = trim($_POST['email']);
		$password = trim($_POST['password']);

		$sql =
		"select id, password, verified, approved
			from user
			where email = :email";
				$res = $this->db->query($sql, [":email" => $email]);
		if(isset($res[0])) {
			if($res[0]['verified'] != NULL && $res[0]['approved'] != NULL) {
				if(password_verify($password, $res[0]['password'])) {
					(new Auth)->set($res[0]['id']);
					return ['error' => false, 'msg' => 'Welcome!'];
				}
				return ['error' => true, 'msg' => 'Wrong password!'];
			}
			return ['error' => true, 'msg' => 'This account is not verified or approved!'];
		}
		return ['error' => true, 'msg' => 'Email not found!'];
	}

	public function forgotPassword ($params, $queries)
	{
		if(!isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			return ['error' => true, 'msg' => '"Email" is not valid!'];

		$email = trim($_POST['email']);

		$sql =
		"select id, name
			from user
			where email = :email";
				$res = $this->db->query($sql, [":email" => $email]);
		if(isset($res[0])) {
			$verification_key = rand(100000, 999999);
			$sql =
			"update user
				set verification_key=:verification_key
				where id=:id";
						$this->db->update($sql, [
					":verification_key" => $verification_key,
					":id" => $res[0]['id']
				]);

			$link = ENV['SHORT_URL'] . '/' . $res[0]['id'] . '/' . $verification_key;
			$this->sendMailForgotPassword($email, $res[0]['name'], $link);

			return ['error' => false, 'msg' => 'Check your email!'];
		}
		return ['error' => true, 'msg' => 'Email not found!'];
	}

	// public function sendMailForgotPassword ($email, $name, $link)
	// {
	// 	$mail = new Mail();
	// 	include PATH_INC.'/template/mail/forgot.php';

	// 	// DEVELOP ONLY - BEGIN
	// 	$to = [
	// 			'XXXXXXXXXXXXXXXXXXXXXXX',
	// 			'XXXXXXXXXXXXXXXXXXXXX'
	// 		];
	// 			if ($email != 'XXXXXXXXXXXXXXXX') array_push($to, $email);
	// 				// DEVELOP ONLY - END

	// 				return $mail->send(
	// 					$to,
	// 					$subject,
	// 					$body,
	// 					$altBody
	// 				);
	// 				9				

								
	// }
}
