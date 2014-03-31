<?php
	error_reporting(0);
	require '../PHPMailer/PHPMailerAutoload.php';
	require '../PHPMailer/class.phpmailer.php';
	//include "../include/EncryptionHelper.php";
	require_once("../include/config.php");

	class EmailController{


		public function __construct($first_name, $last_name, $email_address, $guid){
				
				$linkModel = new LinkModel();
				$linkModel->GUID = $guid;
				$linkModel->Action = LinkAction::INITIAL;
				$linkModel->Used = 0;

				$encryptionObject = new EncryptionHelper(DB_HOST, DB_NAME, DB_USER, DB_PASS);

				$encryptionLink = $encryptionObject->encryptObject($linkModel);

				$mail = new PHPMailer;
				$mail->isSMTP();                                      // Set mailer to use SMTP
				$mail->Host = SMTP_IP; 
				$mail->Port = SMTP_PORT;						 // Specify main and backup server;
				$mail->SMTPAuth = false;                              // Enable SMTP authentication
				$mail->Username = '';                            // SMTP username
				$mail->Password = ''; 

				$mail->From = 'mirko.simanic@devtechgroup.com';
				$mail->FromName = 'Mirko';
				$mail->addAddress($email_address, $first_name." ".$last_name);  // Add a recipient

				$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
				//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
				//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
				$mail->isHTML(true);                                  // Set email format to HTML

				$mail->Subject = 'Welcome to the Konteh';
				$mail->Body    = 	"Dear ".$first_name." ".$last_name.", <br/><br/> Welcome to the Konteh <br/><br/>
									To start please click at the following link: <br/> 
									<a target='_blank' href='http://challenge.devtechgroup.com/index.php?key=".$encryptionLink."'>http://challenge.devtechgroup.com/index.php?key=".$encryptionLink."</a><br/>
									<br/><br/><br/>
									Thank you,
									<br/>Devtech
									<br/>
									";
				$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

				//echo $mail->Body;

				if(!$mail->send()) {
				   echo 'Message could not be sent.';
				   echo 'Mailer Error: ' . $mail->ErrorInfo;
				   exit;
				}
				
				
		}


	}
	
	//$sendemail = new EmailController("Mirko", "Simanic", "mirko.simanic@devtechgroup.com", "{F695B391-1F2D-C6AC-CDD8-F8F26845F075}");


?>

