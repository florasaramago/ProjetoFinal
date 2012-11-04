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

	protected function _initTranslate() {
		//Get previously selected language. If none, set pt_BR as default.
		$i18n = (isset($_COOKIE['i18n'])) ? $_COOKIE['i18n'] : 'pt_BR';
		define('I18N', $i18n);
		
		//Initialize locale and set language
		$locale = new Zend_Locale();
		$locale->setLocale($i18n);
		Zend_Registry::set('Zend_Locale', $locale);
		
		// Set up and load the translations (all of them!)
		$translate = new Zend_Translate('gettext',
										PATH_DATA . DS . 'i18n' . DS,
										$i18n,
										array('disableNotices' => true,
												'scan' => Zend_Translate::LOCALE_DIRECTORY));
		//$translate->setLocale($i18n); // Use this if you only want to load the translation matching current locale, experiment.
		     
		// Save it for later
		$registry = Zend_Registry::getInstance();
		$registry->set('Zend_Translate', $translate);
	}

	protected function _initDatabase( )
	{
		try{		
			$this->bootstrapMultiDb( );
	
			$multiDb = $this->getPluginResource( 'multidb' );
			$multiDb->init( );
			
			$db1Adapter = $multiDb->getDb( 'db1' );
	
			Zend_Registry::set( 'db1', $db1Adapter );
			Zend_Registry::set( 'multidb', $multiDb );
	
			// set this adapter as default for use with Zend_Db_Table
			Zend_Db_Table_Abstract::setDefaultAdapter( $db1Adapter );
	
			// Set profiler for development environment
			if ( APPLICATION_ENV == 'development') {
				$profiler = new Zend_Db_Profiler_Firebug( 'All DB Queries' );
				$profiler->setEnabled( true );
				$db1Adapter->setProfiler( $profiler );
			}
			
			return $db1Adapter;
		} catch(exception $e){
			if(APPLICATION_ENV == 'development') {
				echo $e->getMessage();
			}
			else {
				$e->getMessage();
			}
		}
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
	
	protected function _initCache( )
	{
	    try {
		    $frontendOptions = array(
							    	'automatic_serialization' => true
							    );
		    
		    $backendOptions  = array(
							    	'cache_dir' => CACHE_PATH
							    );
		    
		    $cache = Zend_Cache::factory('Core',
									    'File',
									    $frontendOptions,
									    $backendOptions);
		    
		    Zend_Registry::set('cache', $cache);
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

		//$view->setHelperPath( APPLICATION_PATH . DS . 'views'   . DS . 'helpers',  'View_Helper' );
		//$view->setScriptPath( PATH_MODULES     . DS . 'default' . DS . 'views ' . DS . 'scripts' );
		//$view->addScriptPath( APPLICATION_PATH . DS . 'views'   . DS . 'scripts');

		//$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		//$viewRenderer->setView($view);

		// $view->headTitle('Projeto Final');
		// $view->headTitle()->setSeparator(' :: ');
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
			//Zend_Controller_Action_HelperBroker::addPath( $path, ucfirst( $name ) . '_Helper' );
		}
	}
	

	protected function _initRouter()
	{
		// Acesso - Cadastro
		$route[] = array(
				        'alias'      => 'cadastro',
				        'module'     => 'default',
				        'controller' => 'auth',
                        'action'     => 'signup',
				        'language'   => 'pt_BR',
		);

		// Acesso - Entrar (formulario)
		$route[] = array(
				        'alias'      => 'entrar',
				        'module'     => 'default',
				        'controller' => 'auth',
                        'action'     => 'signin',
				        'language'   => 'pt_BR',
		);

		// Autenticação - Login
		$route[] = array(
				        'alias'      => 'login',
				        'module'     => 'default',
				        'controller' => 'auth',
                        'action'     => 'signin',
				        'language'   => 'pt_BR',
		);

		// Autenticação - Logout
		$route[] = array(
						'alias'      => 'signout',
						'module'     => 'default',
						'controller' => 'auth',
						'action'     => 'signout',
						'language'   => 'pt_BR',
		);


		// Definir a o objeto de roteamento
		$router = $this->frontController->getRouter( );

		// Iterar todas as entradas de rotas possíveis
		foreach ($route as $currentRoute) {
			 
			$data = array ( 'module'     => $currentRoute['module'],
       						'controller' => $currentRoute['controller'], 
        		            'action'     => $currentRoute['action'], 
			);

			$newRoute = new Zend_Controller_Router_Route( $currentRoute['alias'], $data );
				
			$router->addRoute( $currentRoute['alias'] , $newRoute );
		}
		
		// Specifying the "rest" module only as RESTful:
		$restRoute = new Zend_Rest_Route($this->frontController, array(), array('rest'));
		$router->addRoute('rest', $restRoute);
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