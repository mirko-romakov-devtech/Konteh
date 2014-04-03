<?php
$ssc = $_POST['supersecretkey'];
$guid = $_POST['guid'];
var_dump($guid);
die();
if(!isset($ssc, $guid)){
        echo "Dalje neces moci!";
        die();
}

$ssc = $_POST['supersecretkey'];
$key = "supersecretkey";

for ($i = 0; $i < 14; $i++) {
        $key = md5($key);
}

if($ssc != $key){
        echo "SUPER SECRET KEY IS INVALID!";
        die();
}

require_once 'config.php';
require_once 'EncryptionHelper.php';
require_once 'DatabaseHandler.php';


$path = '/home/contexuser/home/testcontex/';
if(file_exists($path.$guid."/victory.txt")){
        echo "LIAR LIAR PANTS ON FIRE!";
        die();
}

mkdir($path.$guid);

try {
        $encryptor = new EncryptionHelper(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $linkModel = new LinkModel();
        $linkModel->Action = LinkAction::ACTIVATION;
        $linkModel->GUID = $guid;
        $linkModel->Used = 0;
        $kobaja = $encryptor->encryptObject($linkModel);

        $dbHandler = DatabaseHandler::WithDefaultConfig();
        $dbHandler->insertActivation($linkModel, urlencode($kobaja));

        $name = $path.$guid."/victory.txt";
        $handle = fopen($name, "w");
        fwrite($handle, "http://challenge.devtechgroup.com/youwin.php?key=".urlencode($kobaja).PHP_EOL);
        fclose($handle);

} catch (Exception $ex) {
        echo "Error occured: ".$ex->getMessage();
}
echo "Bravo!";