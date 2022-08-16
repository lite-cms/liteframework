<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Router Tests
 *
 * ./vendor/bin/phpunit tests/RouterTest.php
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Router;

final class RouterTest extends TestCase
{
	private $router;

	protected function setUp() : void
	{
		$this->router = new Router();

		return;
	}

	public function testMatch() : void
	{
		$this->assertEquals(
			true,
			$this->router->match('/', '/')
		);

		$this->assertEquals(
			false,
			$this->router->match('/', '//')
		);

		$this->assertEquals(
			false,
			$this->router->match('/', '/hello')
		);

		$this->assertEquals(
			true,
			$this->router->match('/hello', '/Hello')
		);

		$this->assertEquals(
			false,
			$this->router->match('/hello', '/Hello', true)
		);

		$this->assertEquals(
			true,
			$this->router->match('/a/@name', '/a/php')
		);
		$this->assertEquals(
			['name' => 'php'],
			$this->router->getMatchParams()
		);

		$this->assertEquals(
			false,
			$this->router->match('/a/@name', '/a/php/c++')
		);

		$this->assertEquals(
			true,
			$this->router->match('/news/[0-9]+', '/news/1234')
		);
		$this->assertEquals(
			[],
			$this->router->getMatchParams()
		);

		$this->assertEquals(
			false,
			$this->router->match('/news/[0-9]+', '/news/12a4')
		);

		$this->assertEquals(
			true,
			$this->router->match('/@name/@id:[0-9]{3}', '/ali/123')
		);
		$this->assertEquals(
			['name' => 'ali', 'id' => '123'],
			$this->router->getMatchParams()
		);

		$this->assertEquals(
			false,
			$this->router->match('/@name/@id:[0-9]{3}', '/ali/1234')
		);

		$this->assertEquals(
			true,
			$this->router->match('/blog(/@year(/@month(/@day)))', '/blog/2020/11/25')
		);
		$this->assertEquals(
			['year' => '2020', 'month' => '11', 'day' => '25'],
			$this->router->getMatchParams()
		);

		$this->assertEquals(
			true,
			$this->router->match('/blog(/@year(/@month(/@day)))', '/blog/2020/11')
		);
		$this->assertEquals(
			['year' => '2020', 'month' => '11', 'day' => null],
			$this->router->getMatchParams()
		);

		$this->assertEquals(
			true,
			$this->router->match('/blog(/@year(/@month(/@day)))', '/blog/2020')
		);
		$this->assertEquals(
			['year' => '2020', 'month' => null, 'day' => null],
			$this->router->getMatchParams()
		);

		$this->assertEquals(
			true,
			$this->router->match('/blog(/@year(/@month(/@day)))', '/blog')
		);
		$this->assertEquals(
			['year' => null, 'month' => null, 'day' => null],
			$this->router->getMatchParams()
		);

		$this->assertEquals(
			true,
			$this->router->match('/blog/*', '/blog/2020/11/25')
		);
		$this->assertEquals(
			['2020/11/25'],
			$this->router->getMatchParams()
		);

		$this->assertEquals(
			true,
			$this->router->match('*', '/blog/2020/11/25')
		);

		$this->assertEquals(
			true,
			$this->router->match('*', '')
		);

		return;
	}

	public function testMatchRef() : void
	{
		$pattern = '/hello/@name';
		$url = '/hello/world';
		$caseSensitiveTrue = true;
		$caseSensitiveFalse = false;

		$this->assertEquals(
			true,
			$this->router->matchRef($pattern, $url)
		);

		$this->assertEquals(
			true,
			$this->router->matchRef($pattern, $url, $caseSensitiveFalse)
		);

		$this->assertEquals(
			true,
			$this->router->matchRef($pattern, $url, $caseSensitiveTrue)
		);

		return;
	}

	public function testMake() : void
	{
		$this->assertEquals(
			'/route/1000/user/any',
			$this->router->make('/route/@id/user/@name', [1000, 'any'])
		);

		$this->assertEquals(
			'/route/1000/user/',
			$this->router->make('/route/@id/user/@name', ['1000', null])
		);

		$this->assertEquals(
			'/user/',
			$this->router->make('/user/@name', [false])
		);

		$this->assertEquals(
			'/user/1',
			$this->router->make('/user/@name', [true])
		);

		$this->assertEquals(
			'/path/10/20/30',
			$this->router->make('/path/*', [10, 20, 30])
		);

		$this->assertEquals(
			'/path/10',
			$this->router->make('/path/@firstItem', [10, 20, 30])
		);

		$this->assertEquals(
			'/path/10/20/30',
			$this->router->make('/path/@firstItem/*', [10, 20, 30])
		);

		$this->assertEquals(
			'/path/10/20/30',
			$this->router->make('/path/@firstItem/@secondItem/*', [10, 20, 30])
		);

		$this->assertEquals(
			'/ali/a13',
			$this->router->make('/@name/@id:[0-9]', ['ali', 'a13'])
		);

		for ($i=0; $i<1000; ++$i) {
			$this->assertEquals(
				'/Value/'.$i.'/ok',
				$this->router->make('/@value/@id:[0-9]+/ok', ['Value', $i])
			);
		}

		$this->assertEquals(
			'/?id=1&p=2',
			$this->router->make('/?id=1&p=2')
		);

		return;
	}
}
