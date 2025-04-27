<?php

namespace Cart2cart;

class Api
{
  const USER_INFO_URL = 'api.get.user.info';
  const BRIDGE_DOWNLOAD_URL = 'api.bridge.download/';

  private static $_instance = null;
  private $_apiUrl = null;
  private $_timeOut = 10;
  private $_client = null;

  protected function __construct($url, $timeOut)
  {
    $this->_apiUrl = $url;
    $this->_timeOut = $timeOut;

    $this->_init();
  }

  protected function __clone() {}

  private function _init()
  {
    $headers = array(
      'Accept: application/json',
      'content-type: application/json',
      'Accept-Encoding: identity',
    );

    $this->_client = curl_init();
    curl_setopt ($this->_client, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt ($this->_client, CURLOPT_HTTPHEADER, $headers);
    curl_setopt ($this->_client, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');

    curl_setopt ($this->_client, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt ($this->_client, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt ($this->_client, CURLFTPAUTH_SSL, true);
    curl_setopt ($this->_client, CURLOPT_TIMEOUT, $this->_timeOut);
    curl_setopt ($this->_client, CURLOPT_RETURNTRANSFER, true);
  }

  public static function getInstance($url, $timeOut = 10)
  {
    if (self::$_instance === null) {
      self::$_instance = new self($url, $timeOut);
    }

    return self::$_instance;
  }

  public function request($path, $params = array(), $method = 'GET')
  {
    curl_setopt($this->_client, CURLOPT_CUSTOMREQUEST, $method);

    if ($method == 'POST') {
      curl_setopt($this->_client, CURLOPT_POSTFIELDS, json_encode($params));
      curl_setopt($this->_client, CURLOPT_URL, $this->_apiUrl . $path);
    } else {
      curl_setopt($this->_client, CURLOPT_URL, $this->_apiUrl . $path . (empty($params) ? '' : ('?' . http_build_query($params))));
    }

    return @json_decode(curl_exec($this->_client), true);
  }
}