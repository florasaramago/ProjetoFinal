<?php
class IndexController extends Core_Controller
{
	public function indexAction()
	{
		$curlModel = new Model_Curl();
		$fileModel = new Model_File();
		
		if($this->_request->isPost()) {
			$url = $this->_request->getPost('url');
			$url = $curlModel->correctUrl($url);
			
			$contents = $curlModel->curlRequest($url);

			if(!$contents) {
				$contents = $curlModel->tryMobileVersion($url);
			} elseif (substr_count($contents, "<p>The document has moved <a href=")) {
				$contents = $curlModel->handleRedirect($contents);
			} else {
				$html = str_get_html($contents);
				foreach($html->find('script') as $element) {
					if($element->src) {
						$jsUrls[] = $element->src;
					}
				}

				foreach($html->find('link[rel=stylesheet]') as $element) {
					$cssUrls[] = $element->href;
				}

				$javascripts = $fileModel->createFiles($jsUrls);
				$css = $fileModel->createFiles($cssUrls);

				$this->view->javascripts = $javascripts;
				$this->view->css = $css;
				$this->view->contents = $contents;
			}
		}	
	}
	
}