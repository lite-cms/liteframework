<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * URL Tests
 *
 * ./vendor/bin/phpunit tests/UrlTest.php
 *
 * @modified : 03 Sep 2022
 * @created  : 30 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Url;

final class UrlTest extends TestCase
{
	public function test1() : void
	{
		$url = new Url('/home/user/public', '/home/user/public');
		$this->assertEquals('8bit', $url->mbEncoding);
		$this->assertIsArray($url->filterChars);
		$this->assertEquals('/home/user/public', $url->getDocumentRootPath());
		$this->assertEquals('/home/user/public', $url->getSelfPath());

		$this->assertEquals('', $url->getPath());
		$this->assertEquals('', $url->get());
		$this->assertEquals('', $url);

		$url->setDocumentRootPath('\\home//path\\');
		$this->assertEquals('/home//path', $url->getDocumentRootPath());

		$url->setSelfPath('\\home//app/path\\');
		$this->assertEquals('/home//app/path', $url->getSelfPath());

		$url->setDocumentRootPath('/home/user/public');
		$url->setSelfPath('/home/user/public');
		$url->setUrl('/');
		$this->assertEquals('/', $url->getPath());
		$this->assertEquals('', $url->getBasePath());
		$this->assertEquals([''], $url->getPathArray());

		$url->setDocumentRootPath('/home/user/public');
		$url->setSelfPath('/home/user/public');
		$url->setUrl('///test///');
		$this->assertEquals('/test/', $url->getPath());
		$this->assertEquals('', $url->getBasePath());
		$this->assertEquals(['test'], $url->getPathArray());

		$url->setDocumentRootPath('/home/user/public/');
		$url->setSelfPath('/home/user/public/app///');
		$url->setUrl('/app/');
		$this->assertEquals('/', $url->getPath());
		$this->assertEquals('/app', $url->getBasePath());
		$this->assertEquals([''], $url->getPathArray());

		$url->setUrl('/app/test/about');
		$this->assertEquals('/test/about', $url->getPath());
		$this->assertEquals('/app', $url->getBasePath());

		$url->setDocumentRootPath('C:\web\public');
		$url->setSelfPath('C:\web\public\site\web\dir\\');
		$url->setUrl('/site/web/dir/about');
		$this->assertEquals('/about', $url->getPath());
		$this->assertEquals('/site/web/dir', $url->getBasePath());
		$this->assertEquals(['about'], $url->getPathArray());

		$url->setDocumentRootPath('/home/user/public');
		$url->setSelfPath('/home/user/public/app/test/web-site');
		$url->setUrl('/app/test/web-site/blog/post/123?q=s&p=1&#top');
		$this->assertEquals('/blog/post/123', $url->getPath());
		$this->assertEquals('/blog/post/123', $url->get());
		$this->assertEquals('/blog/post/123', $url);
		$this->assertEquals('q=s&p=1&', $url->getQuery());
		$this->assertEquals('top', $url->getFragment());
		$this->assertEquals('/app/test/web-site', $url->getBasePath());
		$this->assertEquals(['blog', 'post', '123'], $url->getPathArray());

		$url->setDocumentRootPath('/home/user/public/');
		$url->setSelfPath('/home/user/public/site/');
		$url->set('/site');
		$this->assertEquals($url->get(), '/');
		$this->assertEquals($url->getBasePath(), '/site');

		$url->setBasePath('/test-path');
		$this->assertEquals($url->getBasePath(), '/test-path');

		return;
	}

	public function test2() : void
	{
		$url = new Url('/home/user/public', '/home/user/public');

		$url->setUrl('/app/test/web-\'site/blog////pos"t/1\\23?q=s&p=1&#top');
		$this->assertEquals(
			'/app/test/web-site/blog/post/123',
			$url->getPath()
		);

		$url->setUrl('•°$�llmeG.G%$°•');
		$this->assertEquals(
			'•°$�llmeG.G%$°•',
			$url->getPath()
		);
		$this->assertEquals(
			['•°$�llmeG.G%$°•'],
			$url->getPathArray()
		);

		$url->set('//path\\/\\/\\"/url?#');
		$this->assertEquals(
			'/path/url',
			$url->getPath()
		);

		$url->set('刘纪l君a哈哈');
		$this->assertEquals(
			'刘纪l君a哈哈',
			$url->getPath()
		);

		$url->set('/تست/سلام/ي');
		$this->assertEquals(
			'/تست/سلام/ي',
			$url->getPath()
		);
		$this->assertEquals(
			['تست', 'سلام', 'ي'],
			$url->getArray()
		);

		return;
	}

	public function test3() : void
	{
		$url = new Url();
		$url->set('?#');
		$this->assertEquals('', $url->getPath());
		$this->assertEquals('', $url->get());
		$this->assertEquals('', $url);
		$this->assertEquals('', $url->getBasePath());
		$this->assertEquals('', $url->getDocumentRootPath());
		$this->assertEquals('', $url->getSelfPath());
		$this->assertEquals([''], $url->getPathArray());

		return;
	}

	public function test4() : void
	{
		$url = new Url();
		$url->setDocumentRootPath('/')->setSelfPath('/')->set('/index.html?s');
		$this->assertEquals('/index.html', $url->getPath());
		$this->assertEquals('/index.html', $url->get());
		$this->assertEquals('/index.html', $url);
		$this->assertEquals('', $url->getBasePath());
		$this->assertEquals('', $url->getDocumentRootPath());
		$this->assertEquals('', $url->getSelfPath());
		$this->assertEquals(['index.html'], $url->getPathArray());

		return;
	}
}
