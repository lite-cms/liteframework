<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Request Tests
 *
 * ./vendor/bin/phpunit tests/RequestTest.php
 *
 * @modified : 30 Aug 2022
 * @created  : 17 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Request;

final class RequestTest extends TestCase
{
	public function testBasic() : void
	{
		$req = new Request();
		$req->init();

		$this->assertEquals(64,  $req->httpHeaderMaxKeyLen);
		$this->assertEquals(300, $req->httpHeaderMaxValueLen);
		$this->assertEquals(40,  $req->httpHeaderMaxSize);
		$this->assertIsArray($req->forwardedIpAddr);

		$this->assertIsArray($req->headers());

		$this->assertEquals(
			'',
			$req->header('header-key')
		);

		$this->assertEquals(
			'',
			$req->ip()
		);

		$this->assertEquals(
			0,
			$req->port()
		);

		$this->assertEquals(
			'',
			$req->host()
		);

		$this->assertEquals(
			'',
			$req->useragent()
		);

		$this->assertEquals(
			false,
			$req->ajax()
		);

		$this->assertEquals(
			'/',
			$req->url()
		);

		$this->assertEquals(
			'',
			$req->method()
		);

		$this->assertEquals(
			'',
			$req->scheme()
		);

		$this->assertEquals(
			false,
			$req->secure()
		);

		$this->assertEquals(
			[],
			$req->accept()
		);

		$this->assertEquals(
			false,
			$req->set('var', '')
		);

		$this->assertEquals(
			false,
			$req->get('var')
		);

		$this->assertEquals(
			false,
			$req->post('var')
		);

		$this->assertEquals(
			false,
			$req->cookie('var')
		);

		$this->assertEquals(
			false,
			$req->hasError()
		);

		return;
	}

	public function testIPPort() : void
	{
		$_SERVER['REMOTE_ADDR'] = '127.0.0.2';
		$_SERVER['SERVER_PORT'] = 80;

		$req = new Request();
		$req->init();

		$this->assertEquals(
			'127.0.0.2',
			$req->ip()
		);

		$this->assertEquals(
			true,
			$req->set('IP', '122.22.22.23')
		);

		$this->assertEquals(
			true,
			$req->set('ip', '122.22.22.22')
		);

		$this->assertEquals(
			'122.22.22.22',
			$req->ip()
		);

		$this->assertEquals(
			'127.0.0.2',
			$req->remoteIp()
		);

		$this->assertEquals(
			80,
			$req->port()
		);

		$this->assertEquals(
			true,
			$req->set('RemoteIP', '0.0.0.0')
		);

		$this->assertEquals(
			'0.0.0.0',
			$req->remoteIp()
		);

		$this->assertEquals(
			true,
			$req->set('proxyIP', '0.0.0.1')
		);

		$this->assertEquals(
			'0.0.0.1',
			$req->proxyIp()
		);

		$this->assertEquals(
			false,
			$req->set('proxIP', '0.0.0.1')
		);

		$ip = '122.22.22.a2';
		$this->assertEquals(
			false,
			$req->isValidIP($ip)
		);

		$ip = '122.22..22.22';
		$this->assertEquals(
			false,
			$req->isValidIP($ip)
		);

		$ip = '122.42.256.22';
		$this->assertEquals(
			false,
			$req->isValidIP($ip)
		);

		$ip = '255.255.255.255';
		$this->assertEquals(
			true,
			$req->isValidIP($ip)
		);

		$ip = '0.0.0.0';
		$this->assertEquals(
			true,
			$req->isValidIP($ip)
		);

		return;
	}

	public function testIPProxy() : void
	{
		$_SERVER['REMOTE_ADDR'] = '127.0.0.10';
		$_SERVER['HTTP_CF_CONNECTING_IP'] = '127.0.0.5';

		$req = new Request();
		$req->init();

		$this->assertEquals(
			'127.0.0.5',
			$req->ip()
		);

		$this->assertEquals(
			'127.0.0.10',
			$req->remoteIp()
		);

		$this->assertEquals(
			'127.0.0.5',
			$req->proxyIp()
		);

		return;
	}

	public function testIPProxy2() : void
	{
		unset($_SERVER['HTTP_CF_CONNECTING_IP']);

		$_SERVER['REMOTE_ADDR'] = '127.0.0.10';
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.5';

		$req = new Request();
		$req->init();

		$this->assertEquals(
			'127.0.0.5',
			$req->ip()
		);

		$this->assertEquals(
			'127.0.0.10',
			$req->remoteIp()
		);

		$this->assertEquals(
			'127.0.0.5',
			$req->proxyIp()
		);

		return;
	}

	public function testScheme() : void
	{
		$_SERVER['REQUEST_SCHEME'] = 'https';

		$req = new Request();
		$req->init();

		$this->assertEquals(
			'HTTPS',
			$req->scheme()
		);

		$this->assertEquals(
			true,
			$req->secure()
		);

		$this->assertEquals(
			true,
			$req->set('Scheme', 'http')
		);

		$this->assertEquals(
			'HTTP',
			$req->scheme()
		);

		$this->assertEquals(
			false,
			$req->secure()
		);

		return;
	}

	public function testUri1() : void
	{
		$_SERVER['REQUEST_URI'] = '/web/blog/post/123?q=keyword&sort=asc';

		$req = new Request();
		$req->init();

		$this->assertEquals(
			'/web/blog/post/123?q=keyword&sort=asc',
			$req->url()
		);

		$this->assertEquals(
			true,
			$req->set('url', '/hello/world')
		);

		$this->assertEquals(
			'/hello/world',
			$req->url()
		);

		return;
	}

	public function testUri2() : void
	{
		$_SERVER['REQUEST_URI'] = '//index/////سلام/';

		$req = new Request();
		$req->init();

		$this->assertEquals(
			'//index/////سلام/',
			$req->url()
		);

		return;
	}

	public function testUri3() : void
	{
		$_SERVER['REQUEST_URI'] = '/index/تست/تست#x';

		$req = new Request();
		$req->init();

		$this->assertEquals(
			'/index/تست/تست#x',
			$req->url()
		);

		return;
	}

	public function testMethod1() : void
	{
		$req = new Request();
		$req->init();

		$this->assertEquals(
			'',
			$req->method()
		);

		return;
	}

	public function testMethod2() : void
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$req = new Request();
		$req->init();

		$this->assertEquals(
			'GET',
			$req->method()
		);

		$this->assertEquals(
			true,
			$req->set('Method', 'DELETE')
		);

		$this->assertEquals(
			'DELETE',
			$req->method()
		);

		return;
	}

	public function testAjax() : void
	{
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHTTPREQUEST';

		$req = new Request();
		$req->init();

		$this->assertEquals(
			true,
			$req->xmlRequest()
		);

		$this->assertEquals(
			true,
			$req->ajax()
		);

		$this->assertEquals(
			true,
			$req->set('AJAX', false)
		);

		$this->assertEquals(
			false,
			$req->ajax()
		);

		return;
	}

	public function testAccept() : void
	{
		$_SERVER['HTTP_ACCEPT'] = 'text/html,image/*';

		$req = new Request();
		$req->init();

		$result = $req->accept();
		$this->assertIsArray($result);
		$this->assertSame(count($result), 2);
		$this->assertEquals($result[0], 'text/html');
		$this->assertEquals($result[1], 'image/*');

		$this->assertEquals(
			true,
			$req->set('Accept', ['*/*'])
		);

		$result = $req->accept();
		$this->assertIsArray($result);
		$this->assertSame(count($result), 1);
		$this->assertEquals($result[0], '*/*');

		return;
	}

	public function testUseragnet() : void
	{
		$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0';
		$_SERVER['HTTP_USER_AGENT'] = $agent;

		$req = new Request();
		$req->init();

		$this->assertEquals(
			$agent,
			$req->useragent()
		);

		$this->assertEquals(
			true,
			$req->set('Useragent', 'Mozilla')
		);

		$this->assertEquals(
			'Mozilla',
			$req->useragent()
		);

		return;
	}

	public function testHeaders1() : void
	{
		$_SERVER['HTTP_X_HEADER'] = 'x-value';

		$req = new Request();
		$req->init();

		$result = $req->headers();
		$this->assertIsArray($result);
		$this->assertEquals($result['HTTP_X_HEADER'], 'x-value');

		return;
	}

	public function testHeaders2() : void
	{
		$_SERVER['HTTP_X_HEADER_*'] = 'x-value';

		$req = new Request();
		$req->init();

		$result = $req->headers();
		$this->assertIsArray($result);
		$this->assertArrayNotHasKey('HTTP_X_HEADER_*', $result);

		return;
	}

	public function testData() : void
	{
		$_GET['get_key']   = 'g-value';
		$_POST['post_key'] = 'p-value';
		$_COOKIE['cookie_key'] = 'c-value';

		$req = new Request();
		$req->init();

		$result = $req->get('get_key');
		$this->assertEquals($result, 'g-value');

		$result = $req->post('post_key');
		$this->assertEquals($result, 'p-value');

		$result = $req->cookie('cookie_key');
		$this->assertEquals($result, 'c-value');

		$result = $req->get('get_key_n');
		$this->assertEquals($result, false);

		$result = $req->get('get_key_n', 1010);
		$this->assertEquals($result, 1010);

		$result = $req->post('post_key_n', '123');
		$this->assertEquals($result, '123');

		$result = $req->cookie('cookie_key_n', true);
		$this->assertEquals($result, true);

		return;
	}
}
