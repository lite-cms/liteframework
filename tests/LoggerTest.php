<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Logger Tests
 *
 * ./vendor/bin/phpunit tests/LoggerTest.php
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Logger;

final class LoggerTest extends TestCase
{
	private $logger;

	private $loggerFilePath = __DIR__.'/logs.log';

	protected function setUp() : void
	{
		$this->logger = new Logger($this->loggerFilePath);

		$this->assertIsObject($this->logger->monolog);

		return;
	}

	public function testLogger1() : void
	{
		$this->logger->emergency('emergency_log_01', ['file' => __FILE__]);

		$this->logger->alert('alert_log_01', ['file' => __FILE__]);

		$this->logger->critical('critical_log_01', ['file' => __FILE__]);

		$this->logger->error('error_log_01', ['file' => __FILE__]);

		$this->logger->warning('warning_log_01', ['file' => __FILE__]);

		$this->logger->notice('notice_log_01', ['file' => __FILE__]);

		$this->logger->info('info_log_01', ['file' => __FILE__]);

		$this->logger->debug('debug_log_01', ['file' => __FILE__]);

		$this->logger->log('debug', 'debug_log_02', ['file' => __FILE__]);

		$this->assertSame($this->logger->getLogPath(), $this->loggerFilePath);

		$this->assertFileExists($this->loggerFilePath);

		@unlink($this->loggerFilePath);

		return;
	}
}
