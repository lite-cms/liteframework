<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Dispatcher
 *
 * @modified : 28 Aug 2022
 * @created  : 28 Aug 2022
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\DispatcherInterface;
use LiteFramework\RouterInterface;

class Dispatcher implements DispatcherInterface
{
	// @bool - router $caseSensitive
	public $caseSensitive = false;

	// @object - RouterInterface
	protected $router;

	// @array
	protected $events = [];

	// @string
	protected $urlPath;

	// @mixed
	protected $callbackResult = null;

	/**
	 * Constructor
	 *
	 * @param object RouterInterface
	*/
	public function __construct(RouterInterface& $router, string $urlPath = '')
	{
		$this->router =& $router;
		$this->urlPath = $urlPath !== '' ? $urlPath : $_SERVER['REQUEST_URI'];
	}

	/**
	 * Has event
	 *
	 * @param string
	 * @return bool true on success/false on failure
	*/
	public function has(string $eventsName) : bool
	{
		return isset($this->events[$eventsName]) === true ? true : false;
	}

	/**
	 * Set event
	 *
	 * @param string
	 * @param mixed string class name|callback function
	 * @return void
	*/
	public function set(string $eventsName, $callback) : void
	{
		$this->events[$eventsName] = $callback;
		return;
	}

	/**
	 * Get event
	 *
	 * @param string
	 * @return mixed bool false|string class name|callback function
	*/
	public function get(string $eventsName)
	{
		if (isset($this->events[$eventsName]) === false)
			return false;

		return $this->events[$eventsName];
	}

	/**
	 * Remove event
	 *
	 * @param string
	 * @return bool true on success/false on failure
	*/
	public function remove(string $eventsName) : bool
	{
		if (isset($this->events[$eventsName]) === false)
			return false;
		unset($this->events[$eventsName]);
		return true;
	}

	/**
	 * Run
	 *
	 * @return bool true on success/false on failure
	*/
	public function run() : bool
	{
		$rc = false;

		foreach ($this->events as $eventName => $callback) {
			$rc = $this->router->matchRef($eventName, $this->urlPath, $this->caseSensitive);
			if ($rc === true) {
				$params = $this->router->getMatchParams();
				if (count($params) > 0)
					$params = array_values($params);
				$rc = $this->execute($eventName, $params);
				break;
			}
		}

		return $rc;
	}

	/**
	 * Get callback execute result
	 *
	 * @return mixed (default is null)
	*/
	public function getResult()
	{
		return $this->callbackResult;
	}

	/**
	 * Execute
	 *
	 * @return bool true on success/false on failure
	*/
	protected function execute(string& $eventName, array& $params) : bool
	{
		if (is_array($this->events[$eventName]) === true) {
			if (count($this->events[$eventName]) < 2)
				return false;
			return $this->executeClass($eventName, $params);
		}

		if (is_callable($this->events[$eventName]) === true)
			return $this->executeCallable($eventName, $params);

		return false;
	}

	/**
	 * Execute callable: functions or class static methods
	 *
	 * @return bool true
	*/
	protected function executeCallable(string& $eventName, array& $params) : bool
	{
		switch (count($params)) {
			case 0:
				$this->callbackResult = $this->events[$eventName]();
			break;
			case 1:
				$this->callbackResult = $this->events[$eventName]($params[0]);
			break;
			case 2:
				$this->callbackResult = $this->events[$eventName]($params[0], $params[1]);
			break;
			case 3:
				$this->callbackResult = $this->events[$eventName]($params[0], $params[1], $params[2]);
			break;
			case 4:
				$this->callbackResult = $this->events[$eventName]($params[0], $params[1], $params[2], $params[3]);
			break;
			case 5:
				$this->callbackResult = $this->events[$eventName]($params[0], $params[1], $params[2], $params[3], $params[4]);
			break;
			default:
				$this->callbackResult = $this->events[$eventName]($params);
		}

		return true;
	}

	/**
	 * Execute class
	 *
	 * @return bool true on success/false on failure
	*/
	protected function executeClass(string& $eventName, array& $params) : bool
	{
		if (class_exists($this->events[$eventName][0]) === false)
			return false;

		$class = new $this->events[$eventName][0]();

		if (method_exists($class, $this->events[$eventName][1]) === false)
			return false;

		$method =& $this->events[$eventName][1];

		switch (count($params)) {
			case 0:
				$this->callbackResult = $class->{$method}();
			break;
			case 1:
				$this->callbackResult = $class->{$method}($params[0]);
			break;
			case 2:
				$this->callbackResult = $class->{$method}($params[0], $params[1]);
			break;
			case 3:
				$this->callbackResult = $class->{$method}($params[0], $params[1], $params[2]);
			break;
			case 4:
				$this->callbackResult = $class->{$method}($params[0], $params[1], $params[2], $params[3]);
			break;
			case 5:
				$this->callbackResult = $class->{$method}($params[0], $params[1], $params[2], $params[3], $params[4]);
			break;
			default:
				$this->callbackResult = $class->{$method}($params);
		}

		return true;
	}
}
