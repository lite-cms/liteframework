<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Flight
 * MIT License
 * Copyright (c) 2011 Mike Cao <mike@mikecao.com>
 * http://flightphp.com/
*/

/**
 * Response
 *
 * @modified : 26 Aug 2022
 * @created  : 12 Oct 2019
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\ResponseInterface;

class Response implements ResponseInterface
{
	// @bool
	public $sendContentLength = true;

	// @array
	public static $httpStatusCodes = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Moved Temporarily',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out',
		505 => 'HTTP Version not supported',
	];

	// @string
	public $bodyEncoding = '8bit';

	// @string
	protected $serverProtocol = '';

	// @int
	protected $statusCode = 200;

	// @array
	protected $headers = [];

	// @string
	protected $body = '';

	// @bool
	protected $sent = false;

	/**
	 * Init
	 *
	 * @return void
	*/
	public function init() : void
	{
		$this->getServerProtocol_();
		$this->contentType('text/html; charset=utf-8');
		return;
	}

	/**
	 * Set HTTP status code
	 *
	 * @param int
	 * @return bool true on success/false on failure
	*/
	public function status(int $code) : bool
	{
		if (array_key_exists($code, self::$httpStatusCodes) === false) {
			$this->statusCode = 500;
			return false;
		}
		$this->statusCode = $code;
		return true;
	}

	/**
	 * Get HTTP status code
	 *
	 * @return int
	*/
	public function getStatus() : int
	{
		return $this->statusCode;
	}

	/**
	 * Set the http header
	 *
	 * @param string
	 * @param string|int
	 * @return void
	*/
	public function header(string $key, $value) : void
	{
		$key = trim($key);
		$this->headers[$key] = $value;
		return;
	}

	/**
	 * Has header
	 *
	 * @param string
	 * @return bool
	*/
	public function hasHeader(string $key) : bool
	{
		return array_key_exists($key, $this->headers) === true ? true : false;
	}

	/**
	 * Get header value
	 *
	 * @param string
	 * @return string
	*/
	public function getHeader(string $key) : string
	{
		if ($this->hasHeader($key) === false)
			return '';
		return $this->headers[$key];
	}

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
						   bool $httponly = false) : bool
	{
		return setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
	}

	/**
	 * Set the HTTP content type
	 *
	 * @param string
	 * @return void
	*/
	public function contentType(string $value) : void
	{
		$this->headers['Content-Type'] = $value;
		return;
	}

	/**
	 * Sets caching headers for the response
	 *
	 * @param int $expires Expiration time (-1 === no cache)
	 *
	 * @return void
	*/
	public function cache(int $expires) : void
	{
		if ($expires < 0) {
			$this->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
			$this->headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0';
			$this->headers['Pragma'] = 'no-cache';
		}
		else {
			$this->headers['Expires'] = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
			$this->headers['Cache-Control'] = 'max-age=' . ($expires - time());
			if (isset($this->headers['Pragma']) && 'no-cache' == $this->headers['Pragma'])
				unset($this->headers['Pragma']);
		}

		return;
	}

	/**
	 * Redirect
	 *
	 * @param string
	 * @return void
	*/
	public function redirect(string $uri) : void
	{
		$this->statusCode = 301;
		$this->headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
		$this->headers['Pragma'] = 'no-cache';
		$this->headers['Location'] = $uri;
		return;
	}

	/**
	 * Write
	 *
	 * @param string|int
	 * @return void
	*/
	public function write($buffer) : void
	{
		$this->body .= $buffer;
		return;
	}

	/**
	 * Write JSON
	 *
	 * @param array
	 * @return void
	*/
	public function writeJson(array $data) : void
	{
		$this->body .= json_encode($data);
		return;
	}

	/**
	 * Gets the content length
	 *
	 * @return int
	*/
	public function getContentLength() : int
	{
		return mb_strlen($this->body, $this->bodyEncoding);
	}

	/**
	 * Sends HTTP headers
	 *
	 * @return void
	*/
	public function sendHeaders() : void
	{
		if (strpos(\PHP_SAPI, 'cgi') !== false) {
			header(sprintf('Status: %d %s', $this->statusCode, self::$httpStatusCodes[$this->statusCode]), true);
		}
		else {
			header(sprintf('%s %d %s', $this->serverProtocol, $this->statusCode, 
						   self::$httpStatusCodes[$this->statusCode]), true, $this->statusCode);
		}

		foreach ($this->headers as $key => $value)
			header($key . ': ' . $value);

		if ($this->sendContentLength === true) {
			$length = $this->getContentLength();
			header('Content-Length: ' . $length);
		}

		return;
	}

	/**
	 * Sends a HTTP response
	 *
	 * @return void
	*/
	public function send() : void
	{
		if (ob_get_length() > 0)
			ob_end_clean();

		if (headers_sent() === false)
			$this->sendHeaders();

		echo $this->body;

		$this->sent = true;

		return;
	}

	/**
	 * Gets whether response was sent
	 *
	 * @return bool
	*/
	public function sent() : bool
	{
		return $this->sent;
	}

	/**
	 * Clears the response
	 *
	 * @return void
	*/
	public function clear() : void
	{
		$this->statusCode = 200;
		$this->headers = [];
		$this->body = '';
		return;
	}

	/**
	 * Get server protocol
	 *
	 * @return void
	*/
	protected function getServerProtocol_() : void
	{
		$this->serverProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? (string) $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
		return;
	}
}
