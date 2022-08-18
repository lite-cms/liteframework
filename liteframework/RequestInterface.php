<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Request Interface
 *
 * @modified : 18 Aug 2022
 * @created  : 11 Oct 2019
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface RequestInterface
{
	/**
	 * Init
	 *
	 * @return void
	*/
	public function init();

	/**
	 * Get the client IP address
	 *
	 * 1 - forwardedIpAddr
	 * OR
	 * 2 - server[REMOTE_ADDR]
	 *
	 * @return string
	*/
	public function ip() : string;

	/**
	 * Get remote address
	 *
	 * @return string
	*/
	public function remoteIp() : string;

	/**
	 * Get the proxy server IP
	 *
	 * @return string
	*/
	public function proxyIp() : string;

	/**
	 * Get the request port
	 *
	 * @return int
	*/
	public function port() : int;

	/**
	 * Get the request's scheme
	 *
	 * @return string
	*/
	public function scheme() : string;

	/**
	 * Is secure? (HTTPS)
	*/
	public function secure() : bool;

	/**
	 * Get the request method
	 *
	 * @return string (GET|POST|PUT|DELETE...)
	*/
	public function method() : string;

	/**
	 * Get the request url path
	 *
	 * @return string
	*/
	public function url() : string;

	/**
	 * Get query string
	 *
	 * @param string
	*/
	public function queryString() : string;

	/**
	 * Get the request host name
	 *
	 * @return string
	*/
	public function host() : string;

	/**
	 * Get the request user-agent
	 *
	 * @return string
	*/
	public function useragent() : string;

	/**
	 * Get HTTP accept parameters
	 *
	 * @return array
	*/
	public function accept() : array;

	/**
	 * Is Xml-Http-Request?
	 *
	 * @return bool true on yes/bool false on no
	*/
	public function xmlRequest() : bool;

	/**
	 * Is ajax request (alias of xmlRequest)
	 *
	 * @return bool true on yes/bool false on no
	*/
	public function ajax() : bool;

	/**
	 * Get Header
	*/
	public function header(string $key) : string;

	/**
	 * Get headers
	 *
	 * @return array
	*/
	public function headers() : array;

	/**
	 * Get cookie
	 *
	 * @param string
	 * @param mixed
	 * @return mixed on success/bool false on failure
	*/
	public function cookie(string $key, $defaultValue = false);

	/**
	 * GET parameter value
	 *
	 * @param string
	 * @param mixed
	 * @return string on success/bool false on failure
	*/
	public function get(string $key, $defaultValue = false);

	/**
	 * POST parameters value
	 *
	 * @param string
	 * @param mixed
	 * @return string on success/bool false on failure
	*/
	public function post(string $key, $defaultValue = false);

	/**
	 * Set value
	 *
	 * @param string
	 * @param mixed
	 * @return bool true on success/false on failure
	*/
	public function set(string $key, $value) : bool;

	/**
	 * Request has error?
	 *
	 * @return bool true on yes/bool false on no
	*/
	public function hasError() : bool;

	/**
	 * IP address validation
	 *
	 * @param string
	 * @param mixed
	 * return bool true on success/false on failure
	*/
	public function isValidIP(&$ip, $flags = false) : bool;
}
