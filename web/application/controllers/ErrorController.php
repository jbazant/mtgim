<?php

require_once('Baz/Controller/Action.php');

class ErrorController extends Baz_Controller_Action
{

    /**
     * Error action
     * @throws Zend_Controller_Response_Exception
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Stránka nenalezena';
                break;

            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Chyba aplikace';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request = $errors->request;
    }


    /**
     * Log helper
     * @return bool
     */
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');

        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }

        $log = $bootstrap->getResource('Log');

        return $log;
    }
}
