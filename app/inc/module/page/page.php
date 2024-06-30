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
		$user = (new Auth)->check();
		$id = $user['id'];
		$name = $user['name'];

		$procards = ''; //"<table><tr>
			// <th>Name</th>
			// <th>Code</th>
			// <th>Phone</th>
			// <th>Email</th>
			// <th>Company</th>
			// <th>Country</th>
			// <th>Projects</th>
			// <th>Verified</th>
			// <th>Approved</th>
			// <td>Password<th>
			// </tr>";

		$res = $this->db->query(
			"select 
				u.id uid,
				u.verified verified,
				u.approved approved,
				u.name name,
				u.code code,
				u.phone phone,
				u.email email,
				u.company company,
				c.name country,
				c.phonecode phonecode,
				u.projects projects,
				u.secpass secpass

			from user u
			left join country c on c.id = u.country");

		if(isset($res[0])) {
			foreach ($res as $key => $value) {
				$password = secured_decrypt($value['secpass']);				

				$projects = explode(',', $value['projects']);
				$pjt = '';
				foreach ($projects as $v) {
					$pjt .= "<span>$v</span>";
				}

				$apvToggle = $value['approved'] == null ? 'off' : 'on';
				$apvClass = $value['approved'] == null ? 'off' : 'on';
				$apvStatus = $value['approved'] == null ? '0' : '1';
				$apvText = $value['approved'] == null ? 'Disapproved' : 'Approved';

				if($value['verified'] == null) {
					$apvToggle = 'off';
					$apvClass = 'disabled';
					$apvStatus = '0';
					$apvText = 'Waiting for verification';
				}	



				//$procards .= "{$value} - ";

			$procards .= "
				<div class=\"pro-card\">
					<div class=\"pro-card-header\">
						<h2>{$value['name']}</h2>
						<div class=\"code\"><label>Code:</label>{$value['code']}</div>
					</div>
					<div class=\"pro-card-content\">
						<div class=\"div\">
							<div class=\"phone\"><label>Phone:</label>{$value['phonecode']} {$value['phone']}</div>
							<div class=\"email\">
								<label>Email:</label>{$value['email']}
							</div>
						</div>
						<div class=\"div\">
							<div class=\"company\"><label>Company:</label>{$value['company']}</div>
							<div class=\"country\"><label>Country:</label>{$value['country']}</div>
							<div class=\"phone\"><label>Password:</label>$password</div>
						</div>
					</div>

					<div class=\"project\">$pjt</div>

					<div class=\"control\">
						<div>$apvText</div>
							<span class=\"material-symbols-outlined $apvClass\" data-status=\"$apvStatus\">toggle_$apvToggle</span>
					</div>
				</div>";
				
			// }	

			//$procards .= "</table>";
			}
		}
	
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
		// e($params, true);
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
}