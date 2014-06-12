<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));

// Define application environment
if (in_array($_SERVER['SERVER_NAME'], array('www.mtgim.cz', 'mtgim.cz'))) {
    defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'production');
}
else {
    defined('APPLICATION_ENV')
        || define('APPLICATION_ENV',
            getenv('APPLICATION_ENV')
            ? getenv('APPLICATION_ENV')
            : 'development'
        )
    ;
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

try {
    $application
        ->bootstrap()
        ->run()
    ;
} catch (Exception $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}
