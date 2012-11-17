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
		try {
			$router = $this->frontController->getRouter();
			$request =  new Zend_Controller_Request_Http();
			$router->route($request);
			
			if($request->getModuleName() != 'site' || $request->getControllerName() == 'auth') {
				// Config Zend_Session
				$config = Zend_Registry::get('config')->toArray();
				//Zend_Session::setOptions($config);
				Zend_Session::setOptions( array (
		        		'name'                => $config['name'],  // Own name
		        		'cookie_httponly'     => true,             // XSS hardening
		                'gc_probability'      => $config['gc_probability'], 
		                'gc_divisor'          => $config['gc_divisor'], 
		        		'gc_maxlifetime'      => $config['gc_maxlifetime'],
		                'remember_me_seconds' => $config['remember_me_seconds'],
				) );				
				
				
				// Create your Zend_Session_SaveHandler_DbTable
				$configHandler = array(
				    'name'           => 'session',
				    'primary'        => 'id',
				    'modifiedColumn' => 'modified',
				    'dataColumn'     => 'data',
				    'lifetimeColumn' => 'lifetime',
					'useridColumn'	 => 'user_id'
				);
				
				$saveHandler = new My_Session_SaveHandler_DbTable($configHandler);
				$saveHandler->setLifetime( $config['gc_maxlifetime'] );
				
				// Set the save handler for Zend_Session
				Zend_Session::setSaveHandler($saveHandler);
			
				// Start Session
				Zend_Session::start();
			}
		} catch (Exception $e) {
			if(APPLICATION_ENV == 'development') {
				echo $e->getMessage();
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