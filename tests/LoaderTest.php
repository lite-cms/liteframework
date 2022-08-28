<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Loader Tests
 *
 * ./vendor/bin/phpunit tests/LoaderTest.php
 *
 * @modified : 28 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Loader;

final class LoaderTest extends TestCase
{
	public function test1() : void
	{
		$myVar = 'hello';

		Loader::set('myVar', $myVar);

		$myVar = 'hello1';

		$this->assertEquals(
			'hello',
			Loader::get('myVar')
		);

		$this->assertEquals(
			false,
			Loader::get('myvar')
		);

		$myVar = 'persian';

		Loader::setRef('myVar', $myVar);

		$this->assertEquals(
			'persian',
			Loader::getRef('myVar')
		);

		$myVar = 'persianicon';

		$this->assertEquals(
			'persianicon',
			Loader::getRef('myVar')
		);

		unset($myVar);

		$this->assertEquals(
			'persianicon',
			Loader::getRef('myVar')
		);

		$myVar = null;

		$this->assertEquals(
			'persianicon',
			Loader::getRef('myVar')
		);

		$this->assertEquals(
			'persianicon',
			Loader::get('myVar')
		);

		$this->assertEquals(
			true,
			Loader::remove('myVar')
		);

		$this->assertEquals(
			false,
			Loader::remove('myVar')
		);

		$var = 'hello';
		Loader::setRef('var', $var);

		$this->assertEquals(
			true,
			Loader::remove('var')
		);

		$this->assertEquals('hello', $var);

		$this->assertEquals(
			false,
			Loader::remove('var')
		);

		return;
	}
}
