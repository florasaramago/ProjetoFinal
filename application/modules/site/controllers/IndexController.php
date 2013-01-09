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
					if($element->href) {
						$cssUrls[] = $element->href;
					}
				}

				$javascripts = $fileModel->createFiles($jsUrls, $url);
				$css = $fileModel->createFiles($cssUrls, $url);

				$contents = $fileModel->replaceJavascriptFiles($contents, $javascripts['sources']);
				$contents = $fileModel->replaceCssFiles($contents, $css['sources']);

				$this->view->javascripts = $javascripts['data'];
				$this->view->css = $css['data'];
				$this->view->contents = $contents;
			}
		}	
	}

	public function updateFileAction ()
	{
		if ($this->_request->isXmlHttpRequest()) {
			if($this->_request->isPost()) {
				$fileModel = new Model_File();

				$fileName = $this->_request->getPost('file');
				$content = $this->_request->getPost('text');

				$this->_helper->json->sendJson($fileModel->updateFile($fileName, $content));
			} else {
				exit;
			}
		} else {
			exit;
		}
	}

	public function changeSubTabAction ()
	{
		if ($this->_request->isXmlHttpRequest()) {
			if($this->_request->isPost()) {
				$curlModel = new Model_Curl();
				$fileName = $this->_request->getPost('file');

				$contentsString = $curlModel->curlRequestForFiles('http://projetofinal.dev' . $fileName);

				$this->_helper->json->sendJson($contentsString);
			} else {
				exit;
			}
		}
		else {
			exit;
		}
	}
	
}