<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Logger
 *
 * @modified : 16 Aug 2022
 * @created  : 01 Feb 2022
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\LoggerInterface;
use Monolog\Logger as Monologger;
use Monolog\Handler\StreamHandler;

class Logger implements LoggerInterface
{
	// @object - Monolog class
	public $monolog = null;

	// @string
	protected $logsFilename = '';

	/**
	 * Constructor
	 *
	 * @param string
	*/
	public function __construct(string $logsFilename)
	{
		$this->logsFilename = $logsFilename;
		$this->monolog = new Monologger('');
		$this->monolog->pushHandler(new StreamHandler($this->logsFilename));
	}

	/**
	 * Get log filename
	 *
	 * @return string
	*/
	public function getLogPath() : string
	{
		return $this->logsFilename;
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function emergency($message, array $context = [])
	{
		$this->monolog->emergency($message, $context);
		return;
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function alert($message, array $context = [])
	{
		$this->monolog->alert($message, $context);
		return;
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function critical($message, array $context = [])
	{
		$this->monolog->critical($message, $context);
		return;
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function error($message, array $context = [])
	{
		$this->monolog->error($message, $context);
		return;
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function warning($message, array $context = [])
	{
		$this->monolog->warning($message, $context);
		return;
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function notice($message, array $context = [])
	{
		$this->monolog->notice($message, $context);
		return;
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function info($message, array $context = [])
	{
		$this->monolog->info($message, $context);
		return;
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function debug($message, array $context = [])
	{
		$this->monolog->debug($message, $context);
		return;
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return void
	*/
	public function log($level, $message, array $context = [])
	{
		$this->monolog->log($level, $message, $context);
		return;
	}
}
