<?php
 echo realpath(__DIR__ . '/../../web/application/');
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../../web/application/'))
;

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', 'testing')
;

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/* Zend_Application */
require_once 'Zend/Application.php';
require_once 'ControllerTestCase.php';
