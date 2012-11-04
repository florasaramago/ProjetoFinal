<?php
class Core_Controller extends Zend_Controller_Action
{
	public function preDispatch()
	{
		// Constraints
		define('REQUEST_MODULE'    , $this->_request->getModuleName());
		define('REQUEST_CONTROLLER', $this->_request->getControllerName());
		define('REQUEST_ACTION'    , $this->_request->getActionName());
		 
		if ( $this->_request->isPost( ) ) {
			$this->view->urlVars = array_diff($this->_request->getParams(), $_POST);
		} else {
			$this->view->urlVars = $this->_request->getParams();
		}
		
		// Setup layout
		$layout = Zend_Layout::getMvcInstance();
		if(is_dir(APPLICATION_PATH . '/modules/' . $this->_request->getModuleName() . '/layouts')) {
			$layout->setLayoutPath(APPLICATION_PATH . '/modules/' . $this->_request->getModuleName() . '/layouts');
		}
		else {
			$layout->setLayoutPath(APPLICATION_PATH . '/layouts');
		}
	}
}

/**
 * Debug
 */
function _d($data, $exit = true)
{
	if (APPLICATION_ENV == 'production') {
		return true;
	}
	
	Zend_Debug::dump($data, $label = null, $echo = true);

	if ($exit) {
		exit;
	}
}


/**
 * Get domain from URL
 */
function _domain($url)
{
	$parts  = parse_url(trim($url));
	$domain = (isset($parts['host'])) ? $parts['host'] : $parts['path'];
	
	if(substr($domain, 0, 4) === 'www.') {
		$domain = substr($domain, 4);
	}
	if (substr_count($domain, '/') > 0) {
		$pos = strpos($domain, '/');
		$domain = substr($domain, 0, $pos);
	}
	
	return $domain;
}
?>