<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Response Tests
 *
 * ./vendor/bin/phpunit tests/ResponseTest.php
 *
 * @modified : 26 Aug 2022
 * @created  : 25 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Response;

final class ResponseTest extends TestCase
{
	public function testBasic() : void
	{
		$res = new Response();
		$res->init();

		$this->assertEquals(
			'OK',
			$res::$httpStatusCodes[200]
		);

		$this->assertEquals(
			true,
			method_exists($res, 'redirect')
		);

		$this->assertEquals(
			true,
			method_exists($res, 'clear')
		);

		$this->assertEquals(
			true,
			method_exists($res, 'sendHeaders')
		);

		$this->assertEquals(
			true,
			method_exists($res, 'send')
		);

		$res->header('X-Key', 'X-Value');

		$this->assertEquals(
			true,
			$res->hasHeader('X-Key')
		);

		$this->assertEquals(
			false,
			$res->hasHeader('X-Key-2')
		);

		$this->assertEquals(
			'X-Value',
			$res->getHeader('X-Key')
		);

		$this->assertEquals(
			'',
			$res->getHeader('X-Key-2')
		);

		$this->assertEquals(
			'text/html; charset=utf-8',
			$res->getHeader('Content-Type')
		);

		$this->assertEquals(
			200,
			$res->getStatus()
		);

		$this->assertEquals(
			false,
			$res->status(1024)
		);

		$this->assertEquals(
			500,
			$res->getStatus()
		);

		$this->assertEquals(
			true,
			$res->status(404)
		);

		$this->assertEquals(
			404,
			$res->getStatus()
		);

		$res->write('hello-world');

		$this->assertEquals(
			11,
			$res->getContentLength()
		);

		$res->clear();

		$res->write('سلام');

		$this->assertEquals(
			8,
			$res->getContentLength()
		);

		$this->assertEquals(
			false,
			$res->sent()
		);

		return;
	}
}
