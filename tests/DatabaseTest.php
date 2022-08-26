<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Database Tests
 *
 * ./vendor/bin/phpunit tests/DatabaseTest.php
 *
 * @modified : 26 Aug 2022
 * @created  : 26 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Database;

final class DatabaseTest extends TestCase
{
	public function testDb1() : void
	{
		$config = [
			'drive' => 'sqlite',
			'database' => __DIR__.'/test-db.db',
		];

		$db = new Database();

		$this->assertEquals(
			true,
			$db->setConfig($config)
		);

		$this->assertEquals(
			true,
			$db->connect()
		);

		return;
	}
}
