<?php

/**
 * Interface Mtgim_Api_Adapter_Interface
 */
interface Mtgim_Api_Adapter_Interface {

    /**
     * Method which servers all Mtgim Api Calls
     * @param string $method
     * @param array $params
     * @return array
     */
    public function callMethod($method, $params);
}