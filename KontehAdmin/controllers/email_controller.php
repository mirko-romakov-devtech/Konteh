<?php
	error_reporting(0);
	require '../PHPMailer/PHPMailerAutoload.php';
	require '../PHPMailer/class.phpmailer.php';
	//include "../include/EncryptionHelper.php";
	require_once("../include/config.php");

	class EmailController{

		private $mail;
		

		public function __construct(){

				$this->mail = new PHPMailer;
				$this->mail->isSMTP();                                      // Set mailer to use SMTP
				$this->mail->Host = SMTP_IP; 
				$this->mail->Port = SMTP_PORT;						 // Specify main and backup server;
				$this->mail->SMTPAuth = false;                              // Enable SMTP authentication
				$this->mail->Username = '';                            // SMTP username
				$this->mail->Password = ''; 
				$this->mail->From = SENDER_EMAIL;
				$this->mail->FromName = SENDER_NAME;
		}
		
		public function send($data){

				
				$guid = $data[0];
				$email_address = $data[1];
				$first_name = $data[2];
				$last_name = $data[3];
				
				$linkModel = new LinkModel();
				$linkModel->GUID = $guid;
				$linkModel->Action = LinkAction::INITIAL;
				$linkModel->Used = 0;
				
				$encryptionObject = new EncryptionHelper(DB_HOST, DB_NAME, DB_USER, DB_PASS);
				
				$encryptionLink = $encryptionObject->encryptObject($linkModel);
				
				$this->mail->addAddress($email_address, $first_name." ".$last_name);  // Add a recipient

				$this->mail->WordWrap = 50;                                 // Set word wrap to 50 characters
				//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
				//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
				$this->mail->isHTML(true);                                  // Set email format to HTML

				$this->mail->Subject = EMAIL_SUBJECT;
				$this->mail->Body    = 	"Dear ".$first_name." ".$last_name.", <br/><br/> Welcome to the Konteh <br/><br/>
									To start please click at the following link: <br/> 
									<a target='_blank' href='http://challenge.devtechgroup.com/index.php?key=".$encryptionLink."'>http://challenge.devtechgroup.com/index.php?key=".$encryptionLink."</a><br/>
									<br/><br/><br/>
									Thank you,
									<br/>Devtech
									<br/>
									";
				$this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

				//echo $this->mail->Body;

				if(!$mail->send()) {
				   echo 'Message could not be sent.';
				   echo 'Mailer Error: ' . $mail->ErrorInfo;
				   exit;
				}
				
				
		}


	}
	//$data = array("{F695B391-1F2D-C6AC-CDD8-F8F26845F075}", "mirko.simanic@devtechgroup.com", "Mirko", "Simanic");
	//$sendemail = new EmailController();
	//$sendemail->send($data);


?>

