<?php

namespace Module\Page;
use \Module\Auth as Auth;

class Profile extends BasePage {

	function __construct()
	{
		parent::__construct();
	}

	public function index($params, $queries)
	{
		$user = (new Auth)->check();
		$id = $user['id'];
		$name = $user['name'];
		$aftTable = "No Affiliates founded!";

		$res = $this->db->query(
			"select 
				u.id uid,
				u.level level,
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
			left join country c on c.id = u.country"
		);

		if (isset($res[0])) {
			$aftTable = "<table><tr><th>Name</th><th>Code</th><th>Status</th><th>Action</th></tr>";

			foreach ($res as $key => $value) {
				//if($value['level'] >= 20)
				$password = $_SESSION['user_level'] >= 20 ? secured_decrypt($value['secpass']) : false;

				$projects = explode(',', $value['projects']);
				$pjt = '';
				foreach ($projects as $v) {
					$pjt .= "<span>$v</span>";
				}

				$apvToggle = $value['approved'] == null ? 'off' : 'on';
				$apvClass = $value['approved'] == null ? 'off' : 'on';
				$apvStatus = $value['approved'] == null ? '0' : '1';
				$apvText = $value['approved'] == null ? 'Disapproved' : 'Approved';

				if ($value['verified'] == null) {
					$apvToggle = 'off';
					$apvClass = 'disabled';
					$apvStatus = '0';
					$apvText = 'Unverified';
				}

				$aftTable .= "<tr>
            	<td>{$value['name']}</td>
            	<td>{$value['code']}</td>
            	<td>
					<div class=\"control\">
						<div>$apvText</div>
						<span class=\"material-symbols-outlined $apvClass\" data-status=\"$apvStatus\">toggle_$apvToggle</span>
					</div>
				</td>
            	<td><span class=\"material-symbols-outlined aft-plus\">add</span></td>
				<tr class=\"data\">
					<td colspan=\"4\">
						<div class=\"data-content\">
							<div class=\"top\">
								<div><label>Phone:</label>{$value['phonecode']} {$value['phone']}</div>
								<div><label>Email:</label>{$value['email']}</div>
								<div><label>Company:</label>{$value['company']}</div>
								<div><label>Country:</label>{$value['country']}</div>" .
					($password !== false ? "<div><label>Password:</label>$password</div>" : "")
					. "
							</div>				
							<div class=\"project\">$pjt</div>
						</div>
					</td>
				</tr>";
			}
			$aftTable .= "</table>";
		}

		include_once PATH_TEMPLATE . '/page/profile.php';
		exit();
	}
}