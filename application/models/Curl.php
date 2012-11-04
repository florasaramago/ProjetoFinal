<?php
class Model_Curl extends Core_Model
{
	public function curlRequest($url) 
	{
		//Initialize the cURL session
		$ch = curl_init();
			
		//Set the URL of the page or file to download
		curl_setopt($ch, CURLOPT_URL, $url);
			
		//Ask cURL to return the contents in a variable instead of simply echoing them to  the browser.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
		//Switch user agent to iPhone
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3');
			
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		
		curl_setopt($ch, CURLOPT_TRANSFERTEXT, TRUE);
		
		//Execute the cURL session
		$contents = curl_exec ($ch);
		
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
	
	public function handleRedirect ($contents)
	{
		$l = '<p>The document has moved <a href="';
		$r = '">here';
		$il = strpos($contents,$l,0)+strlen($l);
		$ir = strpos($contents,$r,$il);
		return substr($contents,$il,($ir-$il));
	}
}