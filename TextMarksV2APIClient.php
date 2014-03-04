<?php
/**
 * TextMarks V2 API Client Library (PHP). v2.60d.
 * ---------------------------------------------------------------------------
 *
 * TextMarks provides a text-messaging platform you can integrate into
 * your own applications to send and receive text messages to individual
 * users or groups of users.
 *
 * For full online documentation, visit:
 *   http://www.textmarks.com/api/
 *   http://www.textmarks.com/
 *
 * The HTTP API that this library integrates with is NOT REQUIRED.
 * You can do all kinds of wonderful things without this API and without
 * writing any code at all.  However if you wish to automate and integrate
 * TextMarks more deeply into your applications, this API may be useful.
 *
 * This optional PHP client library provides one way to integrate with
 * the platform's HTTP API from your PHP applications.
 *
 * This library requires:
 *  - PHP 5.1 or greater.
 *  - libCURL (normally included with PHP).
 *
 * ---------------------------------------------------------------------------
 * @author Dan Kamins [d k a m i n s A.T t e x t m a r k s D.O.T c o m]
 * @package tmAPIClient
 * ---------------------------------------------------------------------------
 * Copyright (c) 2009, TextMarks Inc. All rights reserved.
 * ---------------------------------------------------------------------------
 *
 * THIS PROGRAM IS PROVIDED ON AN "AS IS" BASIS, WITHOUT WARRANTIES OR
 * CONDITIONS OF ANY KIND, EITHER EXPRESS OR IMPLIED INCLUDING, WITHOUT
 * LIMITATION, ANY WARRANTIES OR CONDITIONS OF TITLE, NON-INFRINGEMENT,
 * MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * RECIPIENT IS SOLELY RESPONSIBLE FOR DETERMINING THE APPROPRIATENESS
 * OF USING AND DISTRIBUTING THE PROGRAM AND ASSUMES ALL RISKS ASSOCIATED
 * WITH ITS EXERCISE OF RIGHTS UNDER THIS AGREEMENT, INCLUDING BUT NOT
 * LIMITED TO THE RISKS AND COSTS OF PROGRAM ERRORS, COMPLIANCE WITH
 * APPLICABLE LAWS, DAMAGE TO OR LOSS OF DATA, PROGRAMS OR EQUIPMENT,
 * AND UNAVAILABILITY OR INTERRUPTION OF OPERATIONS.
 *
 * NEITHER RECIPIENT NOR ANY CONTRIBUTORS SHALL HAVE ANY LIABILITY FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING WITHOUT LIMITATION LOST PROFITS), HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OR
 * DISTRIBUTION OF THE PROGRAM OR THE EXERCISE OF ANY RIGHTS GRANTED
 * HEREUNDER, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
 */
/** */

// ---------------------------------------------------------------------------

/*
 * For PHP prior to 5.2 (which introduced native JSON support),
 * we include a free JSON library and bind it to json_decode.
 */
if (!function_exists('json_decode')) {
	require_once('JSON.php');
	function json_decode($json) {
		$svc = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		return $svc->decode($json);
	}
}



// ---------------------------------------------------------------------------



/**
 * Exception subclass used by TextMarksAPIClient.
 */
class TextMarksV2APIClientException extends Exception
{
}

/**
 * Exception subclass used by TextMarksV2APIClient for transport-level errors.
 */
class TextMarksV2APIClientTransportException extends TextMarksV2APIClientException
{
}



// ---------------------------------------------------------------------------



/**
 * TextMarksV2APIClient - construct and call().
 */
class TextMarksV2APIClient
{
	// Public constants:
	const HTTP_GET          = 'GET';
	const HTTP_POST         = 'POST';
	
	// -----------------------------------------------------------------------
	
	// Configuration:
	const API_URL_BASE      = 'http://php1.api2.textmarks.com';

	// -----------------------------------------------------------------------

	/**
	 * Create TextMarksV2APIClient around indicated authentication info (optional).
	 *
	 * @param string  $sApiKey   API Key ( register at https://www.textmarks.com/manage/account/profile/api_keys/ ). (NULL for none).
	 * @param string  $sAuthUser Phone# or TextMarks username to authenticate to API with. (NULL for none).
	 * @param string  $sAuthPass TextMarks Password associated with sAuthUser. (NULL for none).
	 */
	public function TextMarksV2APIClient( $sApiKey = NULL, $sAuthUser = NULL, $sAuthPass = NULL ) 
	{
		$this->m_sApiKey    = $sApiKey;
		$this->m_sAuthUser  = $sAuthUser;
		$this->m_sAuthPass  = $sAuthPass;
	}

	/**
	 * Public method to call API.
	 *
	 * The API Key and auth params are automatically added if present.
	 *
	 * @param string              $sPackageName    Package name.
	 * @param string              $sMethodName     Method name.
	 * @param map(string,string)  $mssParams       Params for method.
	 * @param string              $sHttpMethod     'GET' or 'POST'.
	 * @return Decoded (from JSON) response.
	 * @throws Exception on error.
	 */
	public function call( $sPackageName, $sMethodName, $mssParams, $sHttpMethod = self::HTTP_POST ) 
	{
		return $this->_callJsonApiMethod($sPackageName, $sMethodName, $mssParams, $sHttpMethod);
	}


	// -----------------------------------------------------------------------
	

	/**
	 * Execute HTTP request (post params to API endpoint) and return string response.
	 *
	 * @param string              $sUrl           URL to request (method endpoint).
	 * @param map(string,string)  $mssParams      Request params.
	 * @param string              $sHttpMethod   'GET' or 'POST'.
	 * @return string Response (usually JSON string).
	 * @throws TextMarksV2APIClientTransportException on error.
	 */
	protected function _rawHttpCall( $sUrl, $mssParams, $sHttpMethod = self::HTTP_POST )
	{
		// Convert param map to encoded form (to post):
		$sPostData = "";
		foreach ($mssParams as $sK => $sV) {
			$sPostData .= "&" . urlencode($sK) . "=" . urlencode($sV);
		}

		// Prep curl:
		$ch = curl_init();
		$arsHeaders = array();
		
		if ($sHttpMethod == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $sUrl);
			$arsHeaders[] = "Content-Type: application/x-www-form-urlencoded";
			curl_setopt($ch, CURLOPT_POSTFIELDS, $sPostData);
		} else {
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
			curl_setopt($ch, CURLOPT_URL, $sUrl . "?" . $sPostData);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arsHeaders);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // (response as string, not output)

		// Make request (synchronous):
		$sResponse = curl_exec($ch);

		// Check for transport-level errors:
		$iCurlErrNo     = curl_errno($ch);
		$iHttpRespCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($iCurlErrNo != 0) {
			curl_close($ch);
			throw new TextMarksV2APIClientTransportException("TextMarksV2APIClient ($sUrl) saw CURL error #$iCurlErrNo: " . curl_error($ch), curl_error($ch));
		}
		if ($iHttpRespCode != 200) {
			curl_close($ch);
			throw new TextMarksV2APIClientTransportException("TextMarksV2APIClient ($sUrl) saw non-200 HTTP response #$iHttpRespCode.", -1);
		}

		// No obvious transport-level errors. Return response:
		return $sResponse;
	}

	/**
	 * Execute API call and return decoded JSON response.
	 *
	 * The API Key and auth params are automatically added.
	 *
	 * @param string              $sPackageName    Package name.
	 * @param string              $sMethodName     Method name.
	 * @param map(string,string)  $mssParams       Params for method.
	 * @param string              $sHttpMethod     'GET' or 'POST'.
	 * @return Decoded (from JSON) response.
	 * @throws Exception on error.
	 */
	protected function _callJsonApiMethod( $sPackageName, $sMethodName, $mssParams, $sHttpMethod = self::HTTP_POST )
	{
		// Prep:
		$mssParamsFull = $mssParams; // (copy array to keep original clean)
		if ($this->m_sApiKey !== NULL)   { $mssParamsFull['api_key']   = $this->m_sApiKey; }
		if ($this->m_sAuthUser !== NULL) { $mssParamsFull['auth_user'] = $this->m_sAuthUser; }
		if ($this->m_sAuthPass !== NULL) { $mssParamsFull['auth_pass'] = $this->m_sAuthPass; }
		$sUrl = self::API_URL_BASE . '/' . $sPackageName . '/' . $sMethodName . '/';

		// Make actual HTTP call:
		$sResp = $this->_rawHttpCall( $sUrl, $mssParamsFull, $sHttpMethod );

		// Parse JSON response:
		$oDecoded = json_decode($sResp, TRUE);

		// Check API response code:
		$iResCode = (int) $oDecoded['head']['rescode'];
		$sResMsg  = $oDecoded['head']['resmsg'];
		if ($iResCode != 0) {
			throw new TextMarksV2APIClientException("TextMarksV2APIClient.call($sPackageName.$sMethodName) got API error #$iResCode: $sResMsg", $iResCode);
		}
		
		return $oDecoded;
	}

	// -----------------------------------------------------------------------

	protected $m_sApiKey;
	protected $m_sAuthUser;
	protected $m_sAuthPass;
}



// ---------------------------------------------------------------------------



// ---------------------------------------------------------------------------


/**
 * Example code to demonstrate how you might call the API.
 */
function exampleTextMarksUsage()
{
	try
	{
		// Most basic echo test:
		echo "Echo test...\n";
		$tmapi = new TextMarksV2APIClient();
		$resp = $tmapi->call('Test', 'echo', array(
			'str' => "Hello world"
			));
		print_r($resp);

		// Check a keyword status:
		echo "Keyword status test...\n";
		$sMyApiKey        = 'MyAPIKEY_12345678';
		$sKeyword         = 'MYKEYWORD';
		$tmapi = new TextMarksV2APIClient($sMyApiKey);
		$resp = $tmapi->call('Anybody', 'keyword_status', array(
			'keyword' => $sKeyword
			));
		print_r($resp);
		echo "Keyword Status Code: " . $resp['body']['status'] . "\n";

		// Invite a user to join a TextMark group:
		echo "Invite a user to join a TextMark group test...\n";
		$sMyApiKey        = 'MyAPIKEY_12345678';
		$sKeyword         = 'MYKEYWORD';
		$sPhone           = '4155551212';
		$tmapi = new TextMarksV2APIClient($sMyApiKey);
		$resp = $tmapi->call('Anybody', 'invite_to_group', array(
			'tm' => $sKeyword,
			'user' => $sPhone
			));
		print_r($resp);

		// Broadcast a message to a TextMark group:
		echo "Broadcasting a message to a TextMark group test...\n";
		$sMyApiKey        = 'MyAPIKEY_12345678';
		$sMyTextMarksUser = 'mytmuser'; // (or my TextMarks phone#)
		$sMyTextMarksPass = 'mytmp@$$word';
		$sKeyword         = 'MYKEYWORD';
		$sMessage         = "This is an alert sent from the PHP API Client. Did it work?";
		$tmapi = new TextMarksV2APIClient($sMyApiKey, $sMyTextMarksUser, $sMyTextMarksPass);
		$resp = $tmapi->call('GroupLeader', 'broadcast_message', array(
			'tm' => $sKeyword,
			'msg' => $sMessage
			));
		print_r($resp);
	}
	catch (Exception $e)
	{
		echo "Whoops... Exception caught!\n";
		echo "Error code: " . $e->getCode() . "\n";
		echo "Exception: " . $e . "\n";
	}
}
//exampleTextMarksUsage();
?>