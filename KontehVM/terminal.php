<?php
//error_reporting( E_ALL );
//ini_set('display_errors', 1);

	if(isset($_GET['key'])){
		checkKey($_GET['key']);
	} else {
		exit('VMKey Not Properly set');
	}

	function checkKey($asAuthKey){
		
		require_once('DatabaseHandler.php');
		require_once('EncryptionHelper.php');
		require_once('config.php');

		$loEncHelper = new EncryptionHelper(DB_HOST, DB_NAME, DB_USER, DB_PASS);
		$loDBHelper = DatabaseHandler::WithDefaultConfig();


		$a = $loEncHelper->decryptObject($asAuthKey);

		$b = $loDBHelper->getCandidateVNCDetails($a->GUID);
		if($_POST['username'] == $b['vnc_username'] and $_POST['password'] == $b['vnc_password']){
			$loDBHelper->logProgress($a->GUID, Tasks::SSHConnect);
			echo '<html>
					  <body>
					    <applet CODEBASE="."
					            ARCHIVE="jta26.jar"
					            CODE="de.mud.jta.Applet"
					            WIDTH=800 HEIGHT=600>
					            <param name="config" value="applet.conf">
					    </applet>
					  </body>
					</html>';
		}else {
			//var_dump($asAuthKey);
			echo '<html>
				<body>
					<form action="'.$_SERVER['PHP_SELF'].'?key='.$asAuthKey.'" method="POST">
						<input type="text" name="username"/>
						<input type="password" name="password"/>
						<input type="submit" value="Pokidaj me jako" />
					</form>
				</body>
				</html>';
		}
	}
?>

