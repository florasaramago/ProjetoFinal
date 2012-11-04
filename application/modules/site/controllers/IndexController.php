<?php
class IndexController extends Core_Controller
{
	public function indexAction()
	{
		$curlModel = new Model_Curl();
		
		if($this->_request->isPost()) {
			$url = $this->_request->getPost('url');
			
			if(substr($url, 0, 7) != 'http://' || substr($url, 0, 7) != 'https://') {
				$url = 'http://' . $url;
			}
			
			$contents = $curlModel->curlRequest($url);
			addslashes($contents);

			if(!$contents) {
				$newUrl = substr($url, 0, 7) . 'm.' . substr($url, 7);
				$contents = $curlModel->curlRequest($newUrl);
			} elseif (substr_count($contents, "<p>The document has moved <a href=")) {
				$newUrl = $curlModel->handleRedirect($contents);
				$contents = $curlModel->curlRequest($newUrl);
			} else {
				$html = str_get_html($contents);
				foreach($html->find('script') as $element) {
					if($element->src) {
						$javascripts[] = $element->src;
					}
				}

				foreach($html->find('link[rel=stylesheet]') as $element) {
					$css[] = $element->href;
				}

				$this->view->javascripts = $javascripts;
				$this->view->css = $css;
				$this->view->contents = $contents;
			}
		}	
	}
	
}