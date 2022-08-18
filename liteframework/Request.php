<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Request
 *
 * @modified : 18 Aug 2022
 * @created  : 11 Oct 2019
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\RequestInterface;

class Request implements RequestInterface
{
	// Http headers : maximum key length @int
	public $httpHeaderMaxKeyLen = 64;

	// Http headers : maximum value length @int
	public $httpHeaderMaxValueLen = 300;

	// Http headers : maximum size (count) @int
	public $httpHeaderMaxSize = 40;

	// Proxy forwarded ip address @array
	public $forwardedIpAddr = [
		'HTTP_CLIENT_IP',
		'HTTP_CF_CONNECTING_IP',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'HTTP_X_CLIENT_IP',
		'HTTP_X_REAL_IP',
	];

	// @array
	protected $_server;

	// HTTP headers : array
	protected $headers = [];

	// Client IP @string
	protected $ip = null;

	// @string
	protected $remoteIp = null;

	// Proxy IP @string
	protected $proxyIp = null;

	// Port @int
	protected $port = null;

	// @string
	protected $scheme = null;

	// @string
	protected $method = null;

	// Url path @string
	protected $urlPath = null;

	// Url query string @string
	protected $queryString = null;

	// Http Accept @array
	protected $httpAccept = null;

	// Is XMLRequest @bool
	protected $xmlRequest = null;

	// User agent @string
	protected $useragent = null;

	// Request is valid @bool
	protected $requestHasError = false;

	/**
	 * Init
	 *
	 * @return void
	*/
	public function init() {
		$this->createFromGlobalServer();
		$this->getHttpHeaders();
		return;
	}

	/**
	 * Get the client IP address
	 *
	 * 1 - forwardedIpAddr
	 * 2 - server[REMOTE_ADDR]
	 *
	 * @return string
	*/
	public function ip() : string
	{
		if ($this->ip !== null)
			return $this->ip;

		$proxyIp = $this->proxyIp();
		if ($proxyIp !== '') {
			$this->ip = $proxyIp;
			return $this->ip;
		}

		$this->ip = $this->remoteIp();
		return $this->ip;
	}

	/**
	 * Get remote address
	 *
	 * @return string
	*/
	public function remoteIp() : string
	{
		if ($this->remoteIp !== null)
			return $this->remoteIp;

		if (isset($this->_server['REMOTE_ADDR']) === false || 
			$this->isValidIP($this->_server['REMOTE_ADDR']) === false) {
			$this->remoteIp = '';
			return $this->remoteIp;
		}

		$this->remoteIp = (string) $this->_server['REMOTE_ADDR'];

		return $this->remoteIp;
	}

	/**
	 * Get the proxy server IP
	 *
	 * @return string
	*/
	public function proxyIp() : string
	{
		if ($this->proxyIp !== null)
			return $this->proxyIp;

		foreach ($this->forwardedIpAddr as $key) {
			if (isset($this->_server[$key]) === true && 
				$this->isValidIP($this->_server[$key]) === true) {
				$this->proxyIp = $this->_server[$key];
				break;
			}
		}

		if ($this->proxyIp === null)
			$this->proxyIp = '';

		return $this->proxyIp;
	}

	/**
	 * Get the request port
	 *
	 * @return int
	*/
	public function port() : int
	{
		if ($this->port !== null)
			return $this->port;

		if (isset($this->_server['SERVER_PORT']) === false) {
			$this->port = 0;
			return $this->port;
		}

		$this->port = (int) $this->_server['SERVER_PORT'];
		return $this->port;
	}

	/**
	 * Get the request scheme
	 *
	 * @return string
	*/
	public function scheme() : string
	{
		if ($this->scheme !== null)
			return $this->scheme;

		if (isset($this->_server['REQUEST_SCHEME']) === true) {
			$this->scheme = strtoupper($this->_server['REQUEST_SCHEME']);
			return $this->scheme;
		}

		if (isset($this->_server['HTTPS']) === true) {
			$this->scheme = 'HTTPS';
			return $this->scheme;
		}

		if (isset($this->_server['SERVER_PROTOCOL']) === false) {
			$this->scheme = '';
			return $this->scheme;
		}

		$this->scheme = stripos($this->_server['SERVER_PROTOCOL'], 'HTTPS') === 0 ? 'HTTPS' : 'HTTP';

		return $this->scheme;
	}

	/**
	 * Is secure?
	 *
	 * @return bool
	*/
	public function secure() : bool
	{
		if ($this->scheme === null)
			$this->scheme();

		return $this->scheme === 'HTTPS' ? true : false;
	}

	/**
	 * Get the request method
	 *
	 * @return string (GET|POST|PUT|DELETE...)
	*/
	public function method() : string
	{
		if ($this->method !== null)
			return $this->method;

		if (isset($this->_server['REQUEST_METHOD']) === false) {
			$this->method = '';
			return $this->method;
		}

		$this->method = strtoupper($this->_server['REQUEST_METHOD']);

		return $this->method;
	}

	/**
	 * Get the user-agent
	 *
	 * @return string
	*/
	public function useragent() : string
	{
		if ($this->useragent !== null)
			return $this->useragent;

		if (isset($this->_server['HTTP_USER_AGENT']) === false) {
			$this->useragent = '';
			return $this->useragent;
		}

		$this->useragent = (string) $this->_server['HTTP_USER_AGENT'];

		return $this->useragent;
	}

	/**
	 * Get the request url path
	 *
	 * @return string
	*/
	public function url() : string
	{
		if ($this->urlPath !== null)
			return $this->urlPath;

		$url = urldecode($this->_server['REQUEST_URI']);
		if (empty($url) === true || $url === '/') {
			$this->urlPath = '/';
			return $this->urlPath;
		}

		$surl = '';
		$lastCharIsSlash = false;
		for ($i=0; $i<strlen($url); ++$i) {
			if ($lastCharIsSlash === true) {
				if ($url[$i] === '/')
					continue;
				$lastCharIsSlash = false;
			}
			else if ($url[$i] === '/') {
				$lastCharIsSlash = true;
			}

			if ($url[$i] === '?' || $url[$i] === '#')
				break;

			$surl .= $url[$i];
		}

		$this->urlPath = $surl;

		return $this->urlPath;
	}

	/**
	 * Get query string
	 *
	 * @param string
	*/
	public function queryString() : string
	{
		if ($this->queryString !== null)
			return $this->queryString;

		$qs = '';
		$isQs = false;
		$url = $this->_server['REQUEST_URI'];
		for ($i=0; $i<strlen($url); ++$i) {
			if ($url[$i] === '?') {
				$isQs = true;
				continue;
			}
			else if ($url[$i] === '#') {
				break;
			}

			if (true === $isQs)
				$qs .= $url[$i];
		}

		$this->queryString = str_replace('&amp;', '&', $qs);
		return $this->queryString;
	}

	/**
	 * Get the request host name
	 *
	 * @return string
	*/
	public function host() : string
	{
		$host = isset($this->_server['HTTP_HOST']) ? (string) $this->_server['HTTP_HOST'] : '';
		return $host;
	}

	/**
	 * Get HTTP accept parameters
	 *
	 * @return array
	*/
	public function accept() : array
	{
		if ($this->httpAccept !== null)
			return $this->httpAccept;

		if (isset($this->_server['HTTP_ACCEPT']) === false)
			return $this->httpAccept = [];

		$this->httpAccept = explode(',', (string) $this->_server['HTTP_ACCEPT']);
		if (is_array($this->httpAccept) === false || count($this->httpAccept) === 0)
			return $this->httpAccept = [];

		return $this->httpAccept;
	}

	/**
	 * Is Xml-Http-Request?
	 *
	 * @return bool true on yes/bool false on no
	*/
	public function xmlRequest() : bool
	{
		if ($this->xmlRequest !== null)
			return $this->xmlRequest;

		if (isset($this->_server['HTTP_X_REQUESTED_WITH']) === false || 
		   'xmlhttprequest' !== strtolower($this->_server['HTTP_X_REQUESTED_WITH'])) {
			$this->xmlRequest = false;
		}
		else {
			$this->xmlRequest = true;
		}

		return $this->xmlRequest;
	}

	/**
	 * Is ajax request (alias of xmlRequest)
	 *
	 * @return bool true on yes/bool false on no
	*/
	public function ajax() : bool
	{
		return $this->xmlRequest();
	}

	/**
	 * Get Header
	*/
	public function header(string $key) : string
	{
		$key = strtoupper($key);
		return isset($this->headers[$key]) ? (string) $this->headers[$key] : '';
	}

	/**
	 * Get headers
	 *
	 * @return array
	*/
	public function headers() : array
	{
		return $this->headers;
	}

	/**
	 * Get cookie value
	 *
	 * @param string
	 * @param mixed
	 * @return mixed on success/bool false on failure
	*/
	public function cookie(string $key, $defaultValue = false)
	{
		return getValue($_COOKIE, $key, $defaultValue);
	}

	/**
	 * GET parameter value
	 *
	 * @param string
	 * @param mixed
	 * @return string on success/bool false on failure
	*/
	public function get(string $key, $defaultValue = false)
	{
		return getValue($_GET, $key, $defaultValue);
	}

	/**
	 * POST parameters value
	 *
	 * @param string
	 * @param mixed
	 * @return string on success/bool false on failure
	*/
	public function post(string $key, $defaultValue = false)
	{
		return getValue($_POST, $key, $defaultValue);
	}

	/**
	 * Set value
	 *
	 * @param string
	 * @param mixed
	 * @return bool true on success/false on failure
	*/
	public function set(string $key, $value) : bool
	{
		switch (strtolower($key)) {
			case 'ip':
				$this->ip = (string) $value;
			break;
			case 'remoteip':
				$this->remoteIp = (string) $value;
			break;
			case 'proxyip':
				$this->proxyIp = (string) $value;
			break;
			case 'port':
				$this->port = (int) $value;
			break;
			case 'scheme':
				$this->scheme = strtoupper((string) $value);
			break;
			case 'method':
				$this->method = strtoupper((string) $value);
			break;
			case 'urlpath':
			case 'url':
				$this->urlPath = (string) $value;
			break;
			case 'querystring':
				$this->queryString = (string) $value;
			break;
			case 'xmlrequest':
			case 'ajax':
				$this->xmlRequest = $value === true ? true : false;
			break;
			case 'useragent':
				$this->useragent = (string) $value;
			break;
			case 'accept':
				$this->httpAccept = is_array($value) === false ? [$value] : $value;
			break;
			case 'haserror':
				$this->requestHasError = $value === true ? true : false;
			break;
			default:
				return false;
		}

		return true;
	}

	/**
	 * Request has error?
	 *
	 * @return bool true on yes/bool false on no
	*/
	public function hasError() : bool
	{
		return $this->requestHasError;
	}

	/**
	 * IP address validation
	 *
	 * @param string
	 * @param mixed
	 * return bool true on success/false on failure
	*/
	public function isValidIP(&$ip, $flags = false) : bool
	{
		if (is_string($ip) === false)
			return false;

		return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false ? true : false;
	}

	/**
	 * Create from $_server
	 *
	 * @return void
	*/
	protected function createFromGlobalServer()
	{
		if (isset($_SERVER) === false || is_array($_SERVER) === false)
			$_SERVER = [];

		$this->_server =& $_SERVER;

		$serverItems = [
			// 'key' => 'default value' //
			'REQUEST_URI'  => '',
			'QUERY_STRING' => '',
		];

		foreach ($serverItems as $key => $defaultValue) {
			if (isset($this->_server[$key]) === false) {
				$this->_server[$key] = $defaultValue;
				continue;
			}

			if (is_array($this->_server[$key]) === true || is_object($this->_server[$key]) === true) {
				$this->_server[$key] = $defaultValue;
				continue;
			}

			$this->_server[$key] = (string) $this->_server[$key];
		}

		return;
	}

	/**
	 * Get Http Headers
	 *
	 * @return void
	*/
	protected function getHttpHeaders()
	{
		$i = 0;
		foreach ($this->_server as $key => $value) {
			if (substr($key, 0, 5) != 'HTTP_')
				continue;

			$i += 1;

			if ($i > $this->httpHeaderMaxSize) {
				$this->requestHasError = true;
				unset($this->_server[$key]);
				continue;
			}

			if ($this->httpHeaderValidator($key) === false) {
				$this->requestHasError = true;
				unset($this->_server[$key]);
				continue;
			}

			if (is_array($value) || is_object($value) || strlen($value) > $this->httpHeaderMaxValueLen) {
				$this->requestHasError = true;
				unset($this->_server[$key]);
				continue;
			}

			$this->headers[strtoupper($key)] = (string) $value;
		}

		return;
	}

	/**
	 * Http headers - validator
	 *
	 * @return bool true on success/bool false on failure
	*/
	protected function httpHeaderValidator(&$key) : bool
	{
		if (is_string($key) === false)
			return false;

		if (strlen($key) > $this->httpHeaderMaxKeyLen)
			return false;

		return preg_match("/^[a-zA-Z0-9-_]+$/", $key) !== 1 ? false : true;
	}
}
