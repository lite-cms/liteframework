<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Dictionary Tests
 *
 * ./vendor/bin/phpunit tests/DictionaryTest.php
 *
 * @modified : 02 Sep 2022
 * @created  : 01 Sep 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Dictionary;

final class DictionaryTest extends TestCase
{
	protected $langPath1 = __DIR__.'/data/languages1';
	protected $langPath2 = __DIR__.'/data/languages2';

	public function test1() : void
	{
		$dic = new Dictionary('en', $this->langPath1);

		$this->assertEquals('Hello', $dic->get('test', 'hello'));
		$this->assertEquals('World', $dic->get('test', 'world'));
		$this->assertEquals('', $dic->get('test', 'Hello'));
		$this->assertEquals('', $dic->get('test', ''));
		$this->assertEquals('', $dic->get('test2', 'hello'));
		$this->assertEquals(
			'email address mail@example.com',
			$dic->get('test', 'email', ['mail@example.com'])
		);
		$this->assertEquals(
			'id 10',
			$dic->get('test', 'num', ['10', 10])
		);
		$this->assertEquals(
			'email address mail@example.com',
			$dic->get('test', 'email', ['mail@example.com', '123'])
		);
		$this->assertEquals('en', $dic->getLanguage());
		$this->assertEquals('en', $dic);
		$result = $dic->getDir();
		$this->assertIsArray($result);
		$this->assertEquals($this->langPath1, $result[0]);

		return;
	}

	public function test2() : void
	{
		$dic = new Dictionary('en', $this->langPath1);
		$dic->addDir($this->langPath2);
		$this->assertEquals('Hello 2', $dic->get('test', 'hello'));

		return;
	}

	public function test3() : void
	{
		$dic = new Dictionary('en', $this->langPath1);
		$this->assertEquals('Hello', $dic->get('test', 'hello'));
		$dic->addDir($this->langPath2);
		$this->assertEquals('Hello', $dic->get('test', 'hello'));

		return;
	}

	public function test4() : void
	{
		$dic = new Dictionary('fa', $this->langPath1);
		$this->assertEquals('سلام', $dic->get('test', 'hello'));
		$this->assertEquals('دنیا', $dic->get('test', 'world'));
		$this->assertEquals(
			'ادرس ایمیل mail@example.com',
			$dic->get('test', 'email', ['mail@example.com'])
		);

		return;
	}

	public function test5() : void
	{
		$dic = new Dictionary('fa', $this->langPath1);
		$dic->addDir($this->langPath2);
		$this->assertEquals('value_fa_1', $dic->get('test', 'key_1'));
		$this->assertEquals('value_fa_2', $dic->get('test', 'key_2'));

		return;
	}
}
