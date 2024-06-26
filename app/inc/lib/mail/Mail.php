<?php

namespace Lib\Mail;

include_once __DIR__ . '/PHPMailer.php';
include_once __DIR__ . '/Exception.php';
include_once __DIR__ . '/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail 
{

	private static $node = null;

	private $error = null;

	private $mail = null;

	private $from = null;

	function __construct()
	{
		$this->error = null;
		$this->from = ENV['MAIL_FROM'];
		$this->mail = new PHPMailer(true);

		$this->mail->isSMTP();                       			//Send using SMTP
		$this->mail->SMTPAuth = true;              				//Enable SMTP authentication
		$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;	//Enable implicit TLS encryption
		
		$this->mail->Host = ENV['MAIL_HOST']; 					//Set the SMTP server to send through
		$this->mail->Username = ENV['MAIL_USERNAME'];			//SMTP username
		$this->mail->Password = ENV['MAIL_PASSWORD'];			//SMTP password
		$this->mail->Port = ENV['MAIL_PORT']; 
		
		//TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
		//$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;        	//Enable verbose debug output

		if (!is_object(static::$node)) static::$node = $this;
	}

	public static function this()
	{
		if (is_object(static::$node)) return static::$node;
		return static::$node = new static();
	}

	public function send
	(
		$to = null, 
		$subject = null, 
		$body = null, 
		$altBody = null
	) {
		$this->mail->clearAllRecipients();
		$this->mail->clearAttachments();
		$this->mail->clearCustomHeaders();
		$this->error = null;

		$this->mail->setFrom($this->from);

		if (is_array($to)) foreach ($to as $t) $this->mail->addAddress($t ?? $this->from);
		else $this->mail->addAddress($to ?? $this->from);

		$this->mail->isHTML(true);
		$this->mail->Subject = $subject ?? 'Subject';
		$this->mail->Body = $body ?? 'Html Body';
		$this->mail->AltBody = $altBody ?? 'body';

		try {
			$this->mail->send();
			return true;
		} catch (Exception $e) {
			$this->error = $this->mail->ErrorInfo;
			return false;
		}
	}
}
