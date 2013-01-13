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
			$sessionPath = TEMP_PATH . '/' . Zend_Session::getId();

			if(!is_dir($sessionPath)) {
				mkdir($sessionPath, 0777);
			}

			$hostPath = $sessionPath . '/' . $host;

			if(!is_dir($hostPath)) {
				mkdir($hostPath, 0777);
			}

			foreach($urls as $id => $url) {
				$fileName = substr(strrchr($urls[$id], '/'), 1);
				$filePath = '/temp/' . Zend_Session::getId() . '/' . $host . '/' . $fileName;
				$handle = fopen($hostPath . '/'. $fileName, "w");
				fwrite($handle, $curlModel->curlRequestForFiles($urls[$id]));
				$data[$fileName][0] = $filePath;
				$data[$fileName][1] = file($hostPath . '/'. $fileName);
				$sources[$url] = $filePath;
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
					$contents = str_replace($element->src, BASE_URL . $source, $contents);
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
					$contents = str_replace($element->href, BASE_URL . $source, $contents);
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
		$handle = fopen($fileName, "w");
		fwrite($handle, $newContents);
		fclose($handle);
	}
}