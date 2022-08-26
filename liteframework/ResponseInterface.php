<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Response Interface
 *
 * @modified : 26 Aug 2022
 * @created  : 12 Oct 2019
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface ResponseInterface
{
	/**
	 * Init
	 *
	 * @return void
	*/
	public function init() : void;

	/**
	 * Set HTTP status code
	 *
	 * @param int
	 * @return bool true on success/false on failure
	*/
	public function status(int $code) : bool;

	/**
	 * Get HTTP status code
	 *
	 * @return int
	*/
	public function getStatus() : int;

	/**
	 * Set header
	 *
	 * @param string
	 * @param string|int
	 * @return void
	*/
	public function header(string $key, $value) : void;

	/**
	 * Has header
	 *
	 * @param string
	 * @return bool
	*/
	public function hasHeader(string $key) : bool;

	/**
	 * Get header value
	 *
	 * @param string
	 * @return string
	*/
	public function getHeader(string $key) : string;

	/**
	 * Set cookie
	 *
	 * @param string
	 * @param string
	 * @param int
	 * @param string
	 * @param string
	 * @param bool
	 * @param bool
	 * @return bool true on success/false on failure
	*/
	public function cookie(string $name, string $value, int $expires = 0, 
						   string $path = '', string $domain = '', bool $secure = false, 
						   bool $httponly = false) : bool;

	/**
	 * Set the http content-type
	 *
	 * @param string
	 * @return void
	*/
	public function contentType(string $value) : void;

	/**
	 * Sets caching headers for the response
	 *
	 * @param int $expires Expiration time (-1 === no cache)
	 *
	 * @return void
	*/
	public function cache(int $expires) : void;

	/**
	 * Redirect
	 *
	 * @param string
	 * @return void
	*/
	public function redirect(string $uri) : void;

	/**
	 * Write
	 *
	 * @param string|int
	 * @return void
	*/
	public function write($buffer) : void;

	/**
	 * Write - JSON
	 *
	 * @param array
	 * @return void
	*/
	public function writeJson(array $data) : void;

	/**
	 * Gets the content length
	 *
	 * @return int
	*/
	public function getContentLength() : int;

	/**
	 * Clears the response
	 *
	 * @return void
	*/
	public function clear() : void;

	/**
	 * Sends HTTP headers
	 *
	 * @return void
	*/
	public function sendHeaders() : void;

	/**
	 * Sends a HTTP response.
	 *
	 * @return void
	*/
	public function send() : void;

	/**
	 * Gets whether response was sent
	 *
	 * @return bool
	*/
	public function sent() : bool;
}
