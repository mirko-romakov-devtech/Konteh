<?php 
class LinkAction {
	const INITIAL = 0;
	const ACTIVATION = 1;
}

class LinkModel {
	public $GUID;
	public $Action;
	public $Used;
}

class EncryptionHelper {

	private $isEncryptionKey;

	/**
	 *
	 * @var PDO
	 */
	private $ioDatabaseHandler;
	private $isConnectionString = 'mysql:dbname=%s;host=%s';

	/**
	 *
	 * @param string $asDatabaseHost
	 * @param string $asDatabaseName
	 * @param string $asDatabaseUsername
	 * @param string $asDatabasePassword
	 *
	 * @return EncryptionHelper
	 */
	public function __construct($asDatabaseHost, $asDatabaseName, $asDatabaseUsername, $asDatabasePassword) {
		$this->ioDatabaseHandler = new PDO(sprintf($this->isConnectionString, $asDatabaseName, $asDatabaseHost), $asDatabaseUsername, $asDatabasePassword);
		$this->isEncryptionKey = $this->getEncryptionKey();
	}

	/**
	 * Decrypts provided string and returns LinkModel.
	 *
	 * @param string $asEncrypted
	 * @return LinkModel
	 */
	public function decryptObject($asEncrypted) {
		$lsDecrypted = $this->decrypt($asEncrypted);
		$loModel = new LinkModel();
			
		$loDecryptedArray = explode("|", $lsDecrypted);
			
		foreach ($loDecryptedArray as $toValue) {
			$toProperty = explode("=", $toValue);
			$loModel->$toProperty[0] = $toProperty[1];
		}
			
		return $loModel;
	}

	/**
	 * Encrypts provided LinkModel and returns encrypted string.
	 *
	 * @param LinkModel $aoModel
	 * @return string
	 */
	public function encryptObject(LinkModel $aoModel) {
		$lsModelString = "";
			
		foreach ($aoModel as $key=>$value) {
			$lsModelString .= $key."=".$value."|";
		}
		$lsModelString = rtrim($lsModelString, "|");
		$lsModelString = $this->encrypt($lsModelString);
			
		return $lsModelString;
			
	}

	private function encrypt($asString) {
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($isEncryptionKey), $asString, MCRYPT_MODE_CBC, md5(md5($isEncryptionKey))));
	}

	private function decrypt($asEncrypted) {
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($isEncryptionKey), base64_decode($asEncrypted), MCRYPT_MODE_CBC, md5(md5($isEncryptionKey))), "\0");
	}

	private function getEncryptionKey() {
		$loStatement = $this->ioDatabaseHandler->prepare("SELECT value FROM konteh.configuration WHERE `key` = ?");
		$loStatement->execute(array("encryption_key"));
		$loResult = $loStatement->fetchAll(PDO::FETCH_ASSOC);
			
		return base64_decode($loResult[0]['value']);
	}
}
?>