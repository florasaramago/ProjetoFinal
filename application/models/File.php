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
			$path = TEMP_PATH . '/' . Zend_Session::getId() . '/' . $host;

			if(!is_dir($path)) {
				mkdir($path);
			}

			foreach($urls as $id => $url) {
				$fileName = substr(strrchr($urls[$id], '/'), 1);
				$filePath = $path . '/'. $fileName;
				$handle = fopen($filePath, "w");
				fwrite($handle, $curlModel->curlRequestForFiles($urls[$id]));
				$data[$fileName] = file($filePath);
				$sources[$url] = '/temp/' . Zend_Session::getId() . '/' . $host . '/' . $fileName;
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
}