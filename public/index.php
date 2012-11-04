<?php
set_time_limit(60);
// Benchmark
$bmTime   = microtime(true);
$bmMemory = memory_get_usage();

// Define directory separator alias
define('DS', DIRECTORY_SEPARATOR);

// Including config file
require_once('..' . DS . 'configs'   . DS . 'application.php');

// Set include path to include files in our library directory on which the zend framework is located
set_include_path( '.' . PATH_SEPARATOR . ZEND_FW
					  // . PATH_SEPARATOR . APPLICATION_PATH . DS . 'models'
					  // . PATH_SEPARATOR . APPLICATION_PATH . DS . 'core'
					  . PATH_SEPARATOR . '.' );


// require_once 'Zend/Application.php';
require_once('..' . DS . 'library'  . DS . 'Zend' . DS . 'Application.php');

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, BASE_PATH . DS . 'configs' . DS . 'application.ini');
Zend_Registry::set('config', $application);

require_once APPLICATION_PATH . '/core/Controller.php';
require_once APPLICATION_PATH . '/core/Model.php';
require_once LIBRARY_PATH . '/SimpleHtmlDom.php';

// COMPACTA GZIP E ADICIONA EM CACHE
$pathinfo  = pathinfo($_SERVER['PHP_SELF']);
$extension = $pathinfo['extension'];
$basename  = $pathinfo['basename'];
 
if($extension == 'php'){
	header('Accept-Encoding: gzip, deflate');
	header('X-Compression: gzip');
	//header('Content-Encoding: gzip');
	header('Content-type: text/html');
	header('Cache-Control: must-revalidate');
	$offset = 60 * 60 ;
	$ExpStr = 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
	header($ExpStr);
}

try {
	$application->bootstrap()
	            ->run();
	
} catch (exception $e) {
	if (APPLICATION_ENV == 'development') {
		echo $e->getMessage();
		exit;
	}
}


/**
 * Load benchmark
 */
register_shutdown_function('__shutdown');
function __shutdown() {
	global $bmTime, $bmMemory;
	$bmEndTime   = microtime(true);
	$bmEndMemory = memory_get_usage();

	$benchmark              = array();
	$benchmark['memmory']   = number_format(( $bmEndMemory - $bmMemory) / 1024) . 'Kb';
	$benchmark['load_time'] = number_format(( $bmEndTime - $bmTime), 4, ',', '.') . 's';
	
	if (APPLICATION_ENV == 'development') {
		/*$profiler = new My_Profiler($benchmark);
		$profiler->getProfiler(); */
	}
}