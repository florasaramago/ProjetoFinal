<?php

###############################################################################
#                               SERVER CONFIGS                                #
###############################################################################

ini_set( 'default_charset', 'UTF-8' );

mb_internal_encoding('UTF-8');

date_default_timezone_set('America/Sao_Paulo');


###############################################################################
#                               SERVER CONSTRAINTS                            #
###############################################################################

// Define application environment (development, staging, production)
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));


###############################################################################
#                               SERVER PATHS                                  #
###############################################################################

switch( APPLICATION_ENV ) {
	case 'production':
		// Application Paths
		define('PATH_PUBLIC', 'http://florasaramago.com');
		
		break;
			
	case 'development':
		error_reporting( E_ALL );
		ini_set('display_errors', 1 );
		ini_set('error_reporting',E_ALL^E_NOTICE);

		// Base Paths
		define('BASE_PATH'       , '/Library/WebServer/Documents/ProjetoFinal');
		define('BASE_PUBLIC'     , BASE_PATH . DS . 'public');
		define('BASE_URL'        , 'http://projetofinal.dev');
		define('TEMP_PATH'		 , BASE_PUBLIC . DS . 'tmp');
		//define('CACHE_PATH'		 , BASE_PUBLIC . DS . 'cache');

		// Application Paths
		define('PATH_PUBLIC', 'http://projetofinal.dev');
			
		break;
		
}

// Application domain [for cookies use]
//define( 'BASE_DOMAIN', substr( BASE_URL, ( strpos( BASE_URL , '.' )  + 1 ) ) );
define( 'BASE_DOMAIN', substr( BASE_URL, 7 ) );


###############################################################################
#                                  APP PATHS                                  #
###############################################################################
define('APPLICATION_PATH', BASE_PATH        . DS . 'application');
define('PATH_DATA'       , APPLICATION_PATH . DS . 'data');
define('PATH_MODULES'    , APPLICATION_PATH . DS . 'modules');
define('LIBRARY_PATH'    , BASE_PATH . DS . 'library');
define('ZEND_FW'		 , LIBRARY_PATH);


if (isset($_SERVER['SERVER_NAME'])) {
	define('URL_SELF'  , 'http://' . $_SERVER['SERVER_NAME']);
} elseif (isset($_SERVER['REDIRECT_URL'])) {
	define('URL_SELF'  , 'http://' . $_SERVER['REDIRECT_URL']);
} else {
	define('URL_SELF'  , URL_ROOT);
}

?>