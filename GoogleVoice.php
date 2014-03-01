<?php

class GoogleVoice {
	// Google account credentials.
	private $_login;
	private $_pass;

	// Special string that Google requires in our POST requests.
	private $_rnr_se;

	// File handle for PHP-Curl.
	private $_ch;

	// The location of our cookies.
	private $_cookieFile;

	// Are we logged in already?
	private $_loggedIn = FALSE;

	public function __construct($login, $pass) {
		$this->_login = $login;
		$this->_pass = $pass;
		$this->_cookieFile = '/tmp/gvCookies.txt';

		$this->_ch = curl_init();
		curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $this->_cookieFile);
		curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->_ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
	}
	
	private function _logIn() {
		global $conf;

		if($this->_loggedIn)
			return TRUE;

		// Fetch the Google Voice login page.
		curl_setopt($this->_ch, CURLOPT_URL, 'https://accounts.google.com/ServiceLogin?service=grandcentral&passive=1209600&continue=https://www.google.com/voice&followup=https://www.google.com/voice&ltmpl=open');
		$html = curl_exec($this->_ch);

		// Parse the returned webpage for the "GALX" token, needed for POST requests.
		if (preg_match('/name="GALX"\s*type="hidden"\s*value="([^"]+)"/', $html, $match))
			$GALX = $match[1];
		else
			throw new Exception('Could not parse for GALX token');

		// Send HTTP POST service login request.
		curl_setopt($this->_ch, CURLOPT_URL, 'https://accounts.google.com/ServiceLoginAuth');
		curl_setopt($this->_ch, CURLOPT_POST, TRUE);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, array(
			'Email' => $this->_login,
			'GALX' => $GALX,
			'Passwd' => $this->_pass,
			'continue' => 'https://www.google.com/voice',
			'followup' => 'https://www.google.com/voice',
			'service' => 'grandcentral',
			'signIn' => 'Sign in'));
		$html = curl_exec($this->_ch);

		// Test if the service login was successful.
		if(preg_match('/name="_rnr_se" (.*) value="(.*)"/', $html, $matches)) {
			$this->_rnr_se = $matches[2];
			$this->_loggedIn = TRUE;
		}
		else {
			throw new Exception("Could not log in to Google Voice with username: " . $this->_login);
		}
	}

	/**
	 * Send an SMS to $number containing $message.
	 * @param $number The 10-digit phone number to send the message to (formatted with parens and hyphens or none).
	 * @param $message The message to send within the SMS.
	 */
	public function sendSMS($number, $message) {
		// Login to the service if not already done.
		$this->_logIn();

		// Send HTTP POST request.
		curl_setopt($this->_ch, CURLOPT_URL, 'https://www.google.com/voice/sms/send/');
		curl_setopt($this->_ch, CURLOPT_POST, TRUE);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, array(
			'_rnr_se' => $this->_rnr_se,
			'phoneNumber' => '+1'.$number,
			'text' => $message
			));
		curl_exec($this->_ch);
	}
	
	public function dom_dump($obj) {
		if ($classname = get_class($obj)) {
			$retval = "Instance of $classname, node list: \n";
			switch (true) {
				case ($obj instanceof DOMDocument):
					$retval .= "XPath: {$obj->getNodePath()}\n".$obj->saveXML($obj);
					break;
				case ($obj instanceof DOMElement):
					$retval .= "XPath: {$obj->getNodePath()}\n".$obj->ownerDocument->saveXML($obj);
					break;
				case ($obj instanceof DOMAttr):
					$retval .= "XPath: {$obj->getNodePath()}\n".$obj->ownerDocument->saveXML($obj);
					break;
				case ($obj instanceof DOMNodeList):
					for ($i = 0; $i < $obj->length; $i++) {
						$retval .= "Item #$i, XPath: {$obj->item($i)->getNodePath()}\n"."{$obj->item($i)->ownerDocument->saveXML($obj->item($i))}\n";
					}
					break;
				default:
					return "Instance of unknown class";
			}
		}
		else {
			return 'no elements...';
		}
		return htmlspecialchars($retval);
	}
}

?>

