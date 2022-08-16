<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Random Tests
 *
 * ./vendor/bin/phpunit tests/RandomTest.php
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Random;

final class RandomTest extends TestCase
{
	private $random;

	protected function setUp() : void
	{
		$this->random = new Random();

		return;
	}

	public function testRandom1() : void
	{
		for ($i=0; $i<100; ++$i)
		{
			$bytes = $this->random->bytes(256);
			$this->assertIsString($bytes);
			$this->assertSame(strlen($bytes), 512);

			$bytes = $this->random->bytes(0);
			$this->assertIsString($bytes);
			$this->assertSame(strlen($bytes), 64);

			$bytes = $this->random->bytes(32, true);
			$this->assertIsString($bytes);
			$this->assertSame(strlen($bytes), 64);

			$bytes = $this->random->bytes(32, false);
			$this->assertIsString($bytes);
			$this->assertSame(strlen($bytes), 32);

			$rand = $this->random->int(10, 99);
			$this->assertIsInt($rand);
			$this->assertGreaterThan(9, $rand);
			$this->assertLessThan(100, $rand);
		}

		return;
	}
}
