<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Framework
 *
 * @modified : 31 Aug 2022
 * @created  : 30 Aug 2022
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\FrameworkInterface;
use LiteFramework\Request;
use LiteFramework\Response;
use LiteFramework\Url;
use LiteFramework\Router;
use LiteFramework\Dispatcher;

class Framework implements FrameworkInterface
{
	// @object
	public static $request = null;

	// @object
	public static $response = null;

	// @object
	public static $url = null;

	// @object
	public static $router = null;

	// @object
	public static $dispatcher = null;

	// @callback - custom error handler
	protected static $errorHandler_;

	/**
	 * Initialize
	 * Handles basic app.
	 *
	 * @return void
	*/
	public static function init(string $documentRoot, string $selfPath) : void
	{
		self::$request = new Request();
		self::$request->init();

		self::$response = new Response();
		self::$response->init();

		self::$url = new Url($documentRoot, $selfPath);
		self::$url->setUrl(self::$request->url());

		self::$router = new Router();
		self::$dispatcher = new Dispatcher(self::$router, self::$url->getPath());

		return;
	}

	/**
	 * Route
	 *
	 * @return void
	*/
	public static function route(string $pattern, $callback) : void
	{
		self::$dispatcher->set($pattern, $callback);
		return;
	}

	/**
	 * On error
	 *
	 * @return void
	*/
	public static function onError(callable $func) : void
	{
		self::$errorHandler_ = $func;
		return;
	}

	/**
	 * Run
	 *
	 * @return bool true on success/false on failure
	*/
	public static function run() : bool
	{
		$rc = self::$dispatcher->run();
		if ($rc === false)
			self::sendError(404);
		return $rc;
	}

	/**
	 * Error
	 *
	 * @return void
	*/
	protected static function sendError(int $statusCode) : void
	{
		if (isset(self::$response::$httpStatusCodes[$statusCode]) === false)
			$statusCode = 500;
		$statusMessage = self::$response::$httpStatusCodes[$statusCode];

		if (is_callable(self::$errorHandler_) === true) {
			call_user_func(self::$errorHandler_, $statusCode, $statusMessage);
			return;
		}

		self::$response->status($statusCode);
		self::$response->write('<html><head><title>Error '.$statusCode.'</title>');
		self::$response->write('</head><body>'."\n");
		self::$response->write('<h1>Error '.$statusCode.'</h1>'."\n");
		self::$response->write('<p>'.$statusMessage.'</p>'."\n");
		self::$response->write('</body></html>');
		self::$response->send();
		return;
	}
}
