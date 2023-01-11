<?php
$twentythree = str_split("ABCDEFGHJKLMNPQRSTVWXYZ",1);
$errorMessages = array(
	'sortcode' => 
		array(	'tooLong' => 'The Sort Code is too long. It needs to be 6 digits.',
				'tooShort' => 'The Sort Code is too short. It needs to be 6 digits.',
				'empty' => 'The Sort Code is empty. It needs to be 6 digits.',
				'doesnotexist' => 'No sortcode value has been declared.'
				),
	'account' => 
		array(	'tooLong' => 'The Account Code is too long. It needs to be 8 digits, or 9 digits if hasNineDigits is selected.',
				'tooShort' => 'The Account Code is too short. It needs to be 8 digits, or 9 digits if hasNineDigits is selected.',
				'tooShortNineDigits' => 'The Account Code is too short. You have selected that this account has 9 digits, but it contains fewer than that number.',
				'empty' => 'The Account Code is empty. It needs to be 8 digits',
				'doesnotexist' => 'No account value has been declared.')
	);
$code = new bankcode();
if (isset($_GET['sortcode'])) $code->sortcode = $_GET['sortcode'];
if (isset($_GET['hasNineDigits']) && !empty($_GET['hasNineDigits'])) $code->hasNineDigits = $_GET['hasNineDigits'];
if (isset($_GET['account'])) $code->account = $_GET['account'];
echo json_encode($code());

class bankcode {
	private $sortcode = NULL;
	private $account = NULL;
	private $checksum = NULL;
	private $checksumPhonetic = NULL;
	public $hasNineDigits = NULL;
	private $errorLog = array();
	
	function __set($string,$value) {
	global $errorMessages;
		switch ($string) {
			case "sortcode": 
				$getSortcode = preg_replace("@\D@","",$value);
				if (strlen($getSortcode)== 6) $this->sortcode = (string) $getSortcode;
					elseif (strlen($getSortcode)>6) $this->errorLog[] = $errorMessages['sortcode']['tooLong'];
					elseif (strlen($getSortcode)<6 && strlen($getSortcode)>0) $this->errorLog[] = $errorMessages['sortcode']['tooShort'];
					else $this->errorLog[] = $errorMessages['sortcode']['empty'];
				break;
			case "account": 
				$accountLength = (int) ($this->hasNineDigits) ? 9 : 8;
				$getAccount = preg_replace("@\D@","",$value);
				if (strlen($getAccount)== $accountLength) $this->account = (string) sprintf("%0".$accountLength."s",$getAccount);
					elseif (strlen($getAccount)>$accountLength) $this->errorLog[] = $errorMessages['account']['tooLong'];
					elseif (strlen($getAccount)<9 && $this->hasNineDigits) $this->errorLog[] = $errorMessages['account']['tooShortNineDigits'];
					elseif (strlen($getAccount)<8 && strlen($getAccount)>0) $this->errorLog[] = $errorMessages['account']['tooShort'];
					else $this->errorLog[] = $errorMessages['account']['empty'];
				break;
			}	
		}
		
	private function createChecksum() {
		global $twentythree;
		$input = str_split((string) $this->sortcode.$this->account,1);

		// LEFT AND RIGHT
		$odd = 0;
		$even = 0;	
		foreach($input as $key => $value) ($key&1) ? $odd+=(int) $value : $even+=(int) $value;

		// MIDDLE
		$a=0;
		$b=0;
		foreach($input as $key => $value) {
			if($key&1) $a+=$value;
				else $b+=$value;
			}
		$middle = (int) abs ($a-$b);

		// BUILD CHECKSUM
		$this->checksum = sprintf("%s%s%s%s",
			$twentythree[(int)(abs (($odd*3)-$even) % 23)],
			((int)$middle % 10),
			$twentythree[(int)(abs (($even*3)-$odd) % 23)],
			($this->hasNineDigits) ? "9" : "");

		// BUILD PHONETIC VERSION
		$temp = file_get_contents("http://localhost/getPhoneticAlphabet.php?".urlencode($this->checksum));
		$temp2=json_decode($temp,1);
		if (!isset($temp2['error']) && isset($temp2['output'])) $this->checksumPhonetic = (string) $temp2['output'];
		}

				
	function __invoke($string = NULL) {
		global $errorMessages;
		if (!isset($this->sortcode)) $this->errorLog[] = $errorMessages['sortcode']['doesnotexist']; 
		if (!isset($this->account)) $this->errorLog[] = $errorMessages['account']['doesnotexist'];
	
		if (count($this->errorLog)!=0) {
			return $this->errorLog;
			}
		else {
			self::createChecksum();
			$return = array (
				'display' => array('sortcode' => implode("-",str_split($this->sortcode,2)),
									'account' => implode(" ",str_split($this->account,2)),
									'checksumPhonetic' => $this->checksumPhonetic),
				'sortcode' => $this->sortcode,
				'account' => $this->account,
				'checksum' => $this->checksum,
				'hasNineDigits' => $this->hasNineDigits
				);
			return $return;				
			}
		}
	
	}

?>