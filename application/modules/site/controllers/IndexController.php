<?php
class IndexController extends Core_Controller
{
	public function indexAction()
	{
		$curlModel = new Model_Curl();
		$fileModel = new Model_File();

		if($this->_request->isPost()) {
			//Get and correct URL
			$url = $this->_request->getPost('url');
			$url = $curlModel->correctUrl($url);
			
			//Get contents from URL using cURL
			$contents = $curlModel->curlRequest($url);

			//Handle possible errors
			if(!$contents) {
				$contents = $curlModel->tryMobileVersion($url);
			} elseif (substr_count($contents, "<p>The document has moved <a href=")) {
				$contents = $curlModel->handleRedirect($contents);
			} else {
				//If there are no errors, get received HTML
				$html = str_get_html($contents);

				//Collect all JavaScript references
				foreach($html->find('script') as $element) {
					if($element->src) {
						$jsUrls[] = $element->src;
					}
				}

				//Collect all CSS references
				foreach($html->find('link[rel=stylesheet]') as $element) {
					if($element->href) {
						$cssUrls[] = $element->href;
					}
				}

				//Create copies of all CSS and JS files
				$javascripts = $fileModel->createFiles($jsUrls, $url);
				$css = $fileModel->createFiles($cssUrls, $url);

				//Replace original CSS references with respective copies
				if(!empty($css['sources'])) {
					$contents = $fileModel->replaceCssFiles($contents, $css['sources']);
				}

				//Replace original JavaScript references with respective copies
				if(!empty($javascripts['sources'])) {
					$contents = $fileModel->replaceJavascriptFiles($contents, $javascripts['sources']);
				}
				
				//Prevent iframe busting
				$contents = $fileModel->preventIframeBusting($contents);

				$this->view->javascripts = $javascripts['data'];
				$this->view->css = $css['data'];
				$this->view->contents = $contents;
				$this->view->post = 1;
			}
		} else {
			$this->view->post = 0;
		}
	}

	public function sessionKeyAction ()
	{
		if ($this->_request->isXmlHttpRequest()) {
			if($this->_request->isPost()) {
				$this->_helper->json->sendJson($_SESSION['key']);
			} else {
				exit;
			}
		} else {
			exit;
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

				$contentsString = $curlModel->curlRequestForFiles($fileName);

				$this->_helper->json->sendJson($contentsString);
			} else {
				exit;
			}
		} else {
			exit;
		}
	}

	public function addLibraryAction ()
	{
		if ($this->_request->isXmlHttpRequest()) {
			if($this->_request->isPost()) {
				$lib = $this->_request->getPost('lib');
				$html = $this->_request->getPost('html');

				$htmlDOM = new simple_html_dom();
				$htmlDOM->load($html, true, false);

				$e = $htmlDOM->find("body", 0);

				if($lib == 'jquery') {
					$e->outertext = $e->outertext . "\n<script type=\"text/javaScript\" src=\"http://code.jquery.com/jquery-1.9.1.js\" id=\"jquery\"></script>";
				} else if($lib == 'jquery-ui') {
					$e->outertext = $e->outertext . "\n<script type=\"text/javaScript\" src=\"http://code.jquery.com/ui/1.10.1/jquery-ui.js\" id=\"jquery-ui\"></script>";
				} else if($lib == 'jquery-mobile') {
					$e->outertext = $e->outertext . "\n<script type=\"text/javaScript\" src=\"http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js\" id=\"jquery-mobile\"></script>";
				}

				$this->_helper->json->sendJson($htmlDOM->outertext);
			} else {
				exit;
			}
		} else {
			exit;
		}
	}

	public function removeLibraryAction ()
	{
		if ($this->_request->isXmlHttpRequest()) {
			if($this->_request->isPost()) {
				$lib = $this->_request->getPost('lib');
				$html = $this->_request->getPost('html');

				$htmlDOM = new simple_html_dom();
				$htmlDOM->load($html, true, false);

				$e = $htmlDOM->find("script[id=$lib]", 0);
				$e->outertext = '';

				$this->_helper->json->sendJson($htmlDOM->outertext);
			} else {
				exit;
			}
		} else {
			exit;
		}
	}
}