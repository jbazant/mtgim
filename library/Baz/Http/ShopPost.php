<?php

abstract class Baz_Http_ShopPost
{
	/**
	 * Pole s vysledky
	 * @var array
	 */
	private $_data = array();
	
	/**
	 * 
	 * @var Zend_Http_Client
	 */
	private $_client;
	
	/**
	 * Url obchodu
	 * @var string
	 */
	protected $_url;
	
	/**
	 * Priznak v jakem kodovani prijde odpoved
	 * Pokud je false znamena to, ze odpoved se nebude dekodovat
	 * a jiz je v UTF-8
	 * @var string|false
	 */
	protected $_encodeFrom = false;
	
	/**
	 * Metoda, ktera bude pouzita k dotazu na server
	 * @var unknown_type
	 */
	protected $_method = Zend_Http_Client::POST;

    /** @var string $_foilTope F|R|A foil/normal/oboji */
    protected $_foilType = 'A';

    /**
     * Nastavi pozdadovany typ karty foil/normal/oboji
     * @param string $val F|R|A
     * @throws Exception
     */
    public function setFoilType($val) {
        if (!in_array($val, array('A', 'R', 'F'))) {
            throw new Exception('Invalid foil type');
        }
        else {
            $this->_foilType = $val;
        }
    }
	
	/**
	 * Konstruktor, vytvori default Zend_Http_Client a nastvi metodu podle $this->_method
     * @param string $clientname Class name of http client used
	 */
	public function __construct($clientname = 'Zend_Http_Client') {

		$this->_client = new $clientname($this->_getUrl());
		$this->_client->setMethod($this->_method);
	} 
	
	/**
	 * Provede dotaz do obchodu na zadanou kartu a rozparsuje vysledek
	 * @param string $cardName
	 * @return bool
	 */
	public function doCardRequest($cardName)
	{
		if (Zend_Http_Client::POST === $this->_method) {
			$this->_client->setParameterPost($this->_getParams($cardName));
		} else {
			$this->_client->setParameterGet($this->_getParams($cardName));
		}
		
		//TODO check result state
		
		//get body
		$rawData = $this->_client->request()->getBody();
		
		//apply iconv if needed
		if ($this->_encodeFrom) {
			$rawData = iconv($this->_encodeFrom, 'utf-8', $rawData);
		}
		
		//process data
		return $this->_processData($rawData);
	}
	
	/**
	 * Http client getter
	 * @return Zend_Http_Client
	 */
	public function getHttpClient()
	{
		return $this->_client;
	}
	
	/**
	 * Data getter
	 * @return array
	 */
	public function getData()
	{
		return $this->_data;
	}
	
	/**
	 * Data setter
	 * @param array $data
	 */
	protected function _setData($data)
	{
		$this->_data = $data;
	}
	
	/**
	 * Url getter
	 * @return string
	 */
	protected function _getUrl()
	{
		return $this->_url;
	}
	
	/**
	 * pomocna funkce pro odrezavani tagu
	 * @param string $item
	 * @return string $item bez prvniho tagu
	 */
	protected function _cut($item) {
    	return trim(substr($item, (strpos($item, '>') + 1)));
    }
    
    /**
     * Vraci parametry pozadavku, pro zadane cardname
     * @param string $cardName
     * @return array
     */
	abstract protected function _getParams($cardName);
	
	/**
	 * Zpracuje data a vraci priznak uspechu
	 * @param string $rowData
	 * @return bool
	 */
	abstract protected function _processData($rowData);

} 