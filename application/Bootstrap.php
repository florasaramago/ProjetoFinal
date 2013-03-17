<?php
function __autoload($class) {
	require str_replace('_', '/', $class) . '.php';
}
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected $_pluginResources = array(
		'modules' => array()
	);

	protected function _initAutoload( )
	{
		$moduleLoader = new Zend_Application_Module_Autoloader( array (
				'namespace' => '', 
				'basePath' => APPLICATION_PATH 
		) );
		return $moduleLoader;
	}

	protected function _initConfig( )
	{
		// load configuration
		$config = new Zend_Config_Ini( BASE_PATH . DS . 'configs' . DS . 'application.ini', APPLICATION_ENV );

		// registry settings
		Zend_Registry::set( 'config', $config );
	}


	public function _initController()
	{
		//Zend_Controller_Action_HelperBroker::addPrefix( 'Helper' ); // My Custom Helpers
	}

	protected function _initRequest()
	{
		// Ensure front controller instance is present, and fetch it
		$this->bootstrap( 'frontController' );
		$front = $this->frontController;
		
		// Initialize the request object
		$request = new Zend_Controller_Request_Http( );
		$request->setBaseUrl( '/' );

		// Add it to the front controller
		$front->setRequest( $request );
		
		// Bootstrap will store this value in the 'request' key of its container
		return $request;
	}
	
	protected function _initSession( )
	{
		Zend_Session::start();

		if(!Zend_Registry::isRegistered('session')) {
			$sessionPath = TEMP_PATH . '/' . Zend_Session::getId();

			if(!is_dir($sessionPath)) {
				if(mkdir($sessionPath, 0777)) {
					$userPath = $sessionPath . '/user';

					if(!is_dir($userPath)) {
						if(mkdir($userPath, 0777)) {
							$cssHandle = fopen($userPath . '/default.css', "w");
							$jsHandle = fopen($userPath . '/default.js', "w");

							$ns = new Zend_Session_Namespace('defaultFiles');
							Zend_Registry::set('session', $ns);
							$ns->cssHandle = $cssHandle;
							$ns->jsHandle = $jsHandle;
						}
					}
				}
			}
		}
	}

	protected function _initLayout( )
	{
		Zend_Layout::startMvc( array (
				'layout'     => 'layout',
				'contentKey' => 'content' 
				) );
	}

	/**
	 * Initialize view
	 *
	 * @return void
	 */
	protected function _initView( )
	{
		$layout = Zend_Layout::getMvcInstance();
		$view   = $layout->getView();
	}

	protected function _initHelpers( )
	{
		$frontController = Zend_Controller_Front::getInstance( );
		$frontController->addModuleDirectory( APPLICATION_PATH . DS . 'modules' );
		$modules = $frontController->getControllerDirectory( );

		foreach ( $modules as $name => $path ) {
			$autoloader = new Zend_Application_Module_Autoloader( array (
					'namespace' => ucfirst( $name ) . '_', 
					'basePath' => APPLICATION_PATH . DS . 'modules' . DS . $name 
			) );
		}
	}
	
	protected function _bootstrap($resource = null)
	{
		try {
			parent::_bootstrap($resource);
		} catch (Exception $e) {
			parent::_bootstrap('frontController');
			$front = $this->getResource('frontController');
			//$front->registerPlugin(new Zend_Controller_Plugin_BootstrapError($e));
		}
	}
}

?>