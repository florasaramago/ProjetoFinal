<?php
class Model_File extends Core_Model
{
	public function createFiles ($urls, $hostUrl) 
	{
		if($urls) {
			$curlModel = new Model_Curl();
			$data = array();
			$sources = array();

			$host = self::getHostFromUrl($hostUrl);

			$key = $_SESSION['key'];
			
			$sessionPath = TEMP_PATH . '/' . $key;

			if(!is_dir($sessionPath)) {
				mkdir($sessionPath, 0777);
			}

			$hostPath = $sessionPath . '/' . $host;

			if(!is_dir($hostPath)) {
				mkdir($hostPath, 0777);
			}

			foreach($urls as $id => $url) {
				$fileName = substr(strrchr($urls[$id], '/'), 1);
				$filePath = '/temp/' . $key . '/' . $host . '/' . $fileName;
				$handle = fopen($hostPath . '/'. $fileName, "w");
				$fileContents = $curlModel->curlRequestForFiles($urls[$id]);
				if(strpos($fileName, ".css")) {
					$hostParse = parse_url($url);
					$assetsHost = $hostParse['host'];
					$fileContents = str_replace("../", "http://".$assetsHost."/", $fileContents);
				}
				if(substr(strrchr($fileName, "."), 1) == "js") {
					$fileContents = self::preventIframeBusting($fileContents);
				}
				fwrite($handle, $fileContents);
				$data[$fileName][0] = $host . '/' . $fileName;
				$data[$fileName][1] = file($hostPath . '/'. $fileName);
				$sources[$url] = '/' . $host . '/' . $fileName;
				fclose($handle);
			}

			return array('data' => $data, 'sources' => $sources);
		}

		return null;
	}

	public function replaceJavascriptFiles($contents, $sources)
	{
		$html = str_get_html($contents);
		foreach($html->find('script') as $element) {
			foreach($sources as $id => $source) {
				if($element->src == $id) {
					$contents = str_replace($element->src, $source, $contents);
				}
			}
		}
		return $contents;
	}

	public function putBackJsFiles($contents, $sources) 
	{
		$html = str_get_html($contents);
		foreach($html->find('script') as $element) {
			foreach($sources as $id => $source) {
				if($element->href == $source) {
					$contents = str_replace($element->src, $id, $contents);
				}
			}
		}
		return $contents;
	}

	public function replaceCssFiles($contents, $sources)
	{
		$html = str_get_html($contents);
		foreach($html->find('link[rel=stylesheet]') as $element) {
			foreach($sources as $id => $source) {
				if($element->href == $id) {
					$contents = str_replace($element->href, $source, $contents);
				}
			}
		}
		return $contents;
	}

	public function putBackCssFiles($contents, $sources) 
	{
		$html = str_get_html($contents);
		foreach($html->find('link[rel=stylesheet]') as $element) {
			foreach($sources as $id => $source) {
				if($element->href == $source) {
					$contents = str_replace($element->href, $id, $contents);
				}
			}
		}
		return $contents;
	}

	public function getHostFromUrl($url)
	{
		$parse = parse_url($url);
		$host = $parse['host'];
		$pieces = explode('.', $host);
		if($pieces[0] == 'm' || $pieces[0] == 'www') {
			return $pieces[1];
		} else {
			return $pieces[0];
		}
	}

	public function updateFile ($fileName, $newContents)
	{
		$handle = fopen($_SERVER['DOCUMENT_ROOT'] . $fileName, "w");
		if($handle) {
			fwrite($handle, $newContents);
			fclose($handle);
			return true;
		} else {
			return false;
		}
		
	}

	public function preventIframeBusting ($code)
	{
		if(substr_count($code, "top.location")) {
			return str_replace("top.location", "", $code);
		} else {
			return $code;
		}
	}

	public function createZip($files = array(), $destination = '', $overwrite = false) {
		$valid_files = array();
		if(is_array($files)) {
			foreach($files as $file) {
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}

		if(count($valid_files)) {
			$curlModel = New Model_Curl();
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) === true) {
				foreach($valid_files as $file) {
					$tmp = explode($_SESSION['key'], $file);
					$fileName = $tmp[1];
					$zip->addFromString($fileName, $curlModel->curlRequestForFiles(PATH_PUBLIC.'/temp/'.$_SESSION['key'].$fileName));	
				}
				$zip->close();
				$zip = new ZipArchive;
				if ($zip->open($destination) === TRUE) {
					$tmp = explode('/', $fileName);
					$currentSite = $tmp[1];
					//_d('/'.$currentSite.'/default.txt'.' '.'/'.$currentSite.'/default.html');
				    $zip->renameName('/'.$currentSite.'/default.txt', '/'.$currentSite.'/default.html');
				    $zip->close();
				}
				return true;
			} else {
				return false;
			}	
		} else {
			return false;
		}
	}
}