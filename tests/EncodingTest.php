<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Encoding Tests
 *
 * ./vendor/bin/phpunit tests/EncodingTest.php
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Encoding;

final class EncodingTest extends TestCase
{
	private $encoding;

	protected function setUp() : void
	{
		$this->encoding = new Encoding();

		return;
	}

	public function testEncoding() : void
	{
		$this->assertEquals(
			'سلام',
			$this->encoding->toUTF8('سلام')
		);

		$this->assertEquals(
			'Fédération Camerounaise de Football',
			$this->encoding->fixUTF8('FÃÂ©dération Camerounaise de Football')
		);

		$this->assertEquals(
			false,
			$this->encoding->isAscii('سلام')
		);

		$this->assertEquals(
			true,
			$this->encoding->isAscii('hello')
		);

		$this->assertEquals(
			8,
			$this->encoding->strlen('سلام')
		);

		$this->assertEquals(
			4,
			$this->encoding->strlen('سلام', 'utf8')
		);

		$this->assertEquals(
			4,
			$this->encoding->strlen('سلام', 'UTF8')
		);

		$this->assertEquals(
			5,
			$this->encoding->strlen('hello')
		);

		return;
	}

	public function testEncodingRef() : void
	{
		$farsiValue = 'دلارام';
		$fixValue = 'FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football';
		$fixValueResult = 'Fédération Camerounaise de Football';

		$this->assertEquals(
			'دلارام',
			$this->encoding->toUTF8Ref($farsiValue)
		);

		$this->assertEquals(
			$fixValueResult,
			$this->encoding->fixUTF8Ref($fixValue)
		);

		$this->assertEquals(
			false,
			$this->encoding->isAsciiRef($farsiValue)
		);

		$this->assertEquals(
			12,
			$this->encoding->strlenRef($farsiValue)
		);

		$this->assertEquals(
			6,
			$this->encoding->strlenRef($farsiValue, 'utf8')
		);

		return;
	}
}
