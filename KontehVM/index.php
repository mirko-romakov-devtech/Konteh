<?php
$ssc = $_POST['supersecretkey'];
$guid = $_POST['guid'];

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

$path = '/home/testuser/home/usertest/';
if(file_exists($path.$guid."/victory.txt")){
        echo "LIAR LIAR PANTS ON FIRE!";
        die();
}

mkdir($path.$guid);

$name = $path.$guid."/victory.txt";
$handle = fopen($name, "w");
fwrite($handle, "jpuiiii\n");
fclose($handle);

echo "Bravo!";
