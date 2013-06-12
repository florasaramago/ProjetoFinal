<?php
class Model_Curl extends Core_Model
{
	public function curlRequest($url, $userAgent) 
	{
		//Initialize the cURL session
		$ch = curl_init();
			
		//Set the URL of the page or file to download
		curl_setopt($ch, CURLOPT_URL, $url);
			
		//Ask cURL to return the contents in a variable instead of simply echoing them to  the browser.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
		if($userAgent == "ios") {
			//Switch user agent to iPhone
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3');
		} else {
			//Switch user agent to Android
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.1.1; sv-se; GT-I9305N Build/JRO03C) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
		}
		
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		curl_setopt($ch, CURLOPT_TRANSFERTEXT, TRUE);
		
		//Execute the cURL session
		$contents = curl_exec ($ch);

		addslashes($contents);
		
		//Close cURL session
		curl_close ($ch);
		
		return $contents;
	}

	public function curlRequestForFiles($url) 
	{
		//Initialize the cURL session
		$ch = curl_init();
			
		//Set the URL of the page or file to download
		curl_setopt($ch, CURLOPT_URL, $url);

		//Ask cURL to return the contents in a variable instead of simply echoing them to  the browser.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		curl_setopt($ch, CURLOPT_TRANSFERTEXT, TRUE);
		
		//Execute the cURL session
		$contents = curl_exec ($ch);
		
		//Close cURL session
		curl_close ($ch);
		
		return $contents;
	}

	public function tryMobileVersion ($url)
	{
		$newUrl = substr($url, 0, 7) . 'm.' . substr($url, 7);
		$contents = self::curlRequest($newUrl, $userAgent);
		return $contents;
	}
	
	public function handleRedirect ($contents, $userAgent)
	{
		$l = '<p>The document has moved <a href="';
		$r = '">here';
		$il = strpos($contents,$l,0)+strlen($l);
		$ir = strpos($contents,$r,$il);
		$newUrl = substr($contents,$il,($ir-$il));
		$contents = self::curlRequest($newUrl, $userAgent);
		return $contents;
	}

	public function correctUrl ($url) 
	{
		if(substr($url, 0, 7) != 'http://' || substr($url, 0, 7) != 'https://') {
			$url = 'http://' . $url;
		}

		return $url;
	}
}