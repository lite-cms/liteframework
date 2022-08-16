<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Filesystem Tests
 *
 * ./vendor/bin/phpunit tests/FilesystemTest.php
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Filesystem;

final class FilesystemTest extends TestCase
{
	private $fs;

	protected function setUp() : void
	{
		$this->fs = new Filesystem();

		$this->assertEquals(
			0644,
			$this->fs->ownerWriteMode
		);

		return;
	}

	public function testFilesystem1() : void
	{
		$appDirPath = basename(LITEF_PATH);
		$appFilePath = LITEF_PATH.'/LiteFramework.php';

		$this->assertDirectoryExists($appDirPath);

		$allFiles = $this->fs->scan($appFilePath);
		$this->assertGreaterThan(count($allFiles), 5);

		$this->assertEquals(
			false,
			$this->fs->isDir('/nothing_')
		);

		$this->assertEquals(
			true,
			$this->fs->isDir($appDirPath)
		);

		$this->assertEquals(
			false,
			$this->fs->exists('/nothing_')
		);

		$this->assertEquals(
			true,
			$this->fs->exists($appDirPath)
		);

		$this->assertEquals(
			false,
			$this->fs->isDir('')
		);

		$this->assertEquals(
			false,
			$this->fs->isFile('')
		);

		$this->assertEquals(
			false,
			$this->fs->isFile('/nothing_.php')
		);

		$this->assertEquals(
			true,
			$this->fs->isFile($appFilePath)
		);

		$this->assertEquals(
			true,
			$this->fs->exists($appFilePath)
		);

		return;
	}

	public function testFilesystem2() : void
	{
		$testDirBase  = __DIR__.'/_test';
		$testDir       = $testDirBase.'/test';
		$testDirRename = $testDirBase.'/test-rename';

		$this->assertEquals(
			true,
			$this->fs->mkdir($testDir)
		);

		$this->assertDirectoryExists($testDir);

		$this->assertEquals(
			true,
			$this->fs->mkdir($testDir)
		);

		$this->assertEquals(
			false,
			$this->fs->mkdir('http://litecms.ir')
		);

		$this->assertEquals(
			true,
			$this->fs->isDir($testDir)
		);

		$this->assertEquals(
			true,
			$this->fs->isReadable($testDir)
		);

		$this->assertEquals(
			true,
			$this->fs->isWritable($testDir)
		);

		$this->assertEquals(
			true,
			$this->fs->makeIndexHtml($testDir, 0444)
		);

		$this->assertEquals(
			true,
			$this->fs->exists($testDir.'/index.html')
		);

		$this->assertFileExists($testDir.'/index.html');

		$this->assertEquals(
			true,
			$this->fs->isReadable($testDir.'/index.html')
		);

		$this->assertEquals(
			false,
			$this->fs->isWritable($testDir.'/index.html')
		);

		$this->assertEquals(
			true,
			$this->fs->makeIndexHtml($testDir)
		);

		$this->assertEquals(
			true,
			$this->fs->rename($testDir, $testDirRename)
		);

		$this->assertDirectoryExists($testDirRename);

		$this->assertEquals(
			true,
			$this->fs->remove($testDirRename)
		);

		$this->assertDirectoryNotExists($testDirRename);

		return;
	}

	public function testFilesystem3() : void
	{
		$testDirBase = __DIR__.'/_test';
		$testFile = $testDirBase.'/write-test.php';
		$appFilePath = LITEF_PATH.'/LiteFramework.php';
		$contents = null;

		$this->assertEquals(
			true,
			$this->fs->read($appFilePath, $contents)
		);

		$this->assertIsString($contents);

		$this->assertEquals(
			false,
			$this->fs->read('nothing_.php', $contents)
		);

		$this->assertEquals(
			true,
			$this->fs->mkdir($testDirBase)
		);

		$this->assertIsString($contents);

		$this->assertEquals(
			true,
			$this->fs->write($testFile, $contents, 0444)
		);

		$this->assertEquals(
			true,
			$this->fs->isReadable($testFile)
		);

		$this->assertEquals(
			false,
			$this->fs->isWritable($testFile)
		);

		$this->assertEquals(
			true,
			$this->fs->chmod($testFile, 0644)
		);

		$this->assertEquals(
			true,
			$this->fs->isWritable($testFile)
		);

		$this->assertEquals(
			true,
			$this->fs->chmod($testFile, 0444)
		);

		$filetime = $this->fs->filetime($testFile);
		$this->assertLessThan($filetime, 0);

		$filetime = $this->fs->filetime('');
		$this->assertSame($filetime, -1);

		$filesize = $this->fs->filesize($testFile);
		$this->assertLessThan($filesize, 0);

		$filesize = $this->fs->filesize('');
		$this->assertSame($filesize, -1);

		$contents = 123;
		$contents = strval($contents);

		$this->assertEquals(
			true,
			$this->fs->write($testFile, $contents)
		);

		$this->assertFileExists($testFile);

		$this->assertEquals(
			false,
			$this->fs->write($testDirBase.'/$#\\.php', $contents)
		);

		$contents = null;

		$this->assertEquals(
			true,
			$this->fs->read($testFile, $contents)
		);

		$this->assertSame('123', $contents);

		$this->assertEquals(
			true,
			$this->fs->remove($testFile)
		);

		$this->assertEquals(
			true,
			$this->fs->write($testDirBase."/test.txt", $contents, 0444)
		);

		$this->assertEquals(
			true,
			$this->fs->copy($testDirBase.'/test.txt', $testDirBase.'/test2.txt')
		);

		$contentsCopy = null;

		$this->assertEquals(
			true,
			$this->fs->read($testDirBase.'/test2.txt', $contentsCopy)
		);

		$this->assertSame($contents, $contentsCopy);

		$this->assertEquals(
			true,
			$this->fs->rename($testDirBase.'/test2.txt', $testDirBase.'/test3.txt')
		);

		$this->assertEquals(
			true,
			$this->fs->isFile($testDirBase.'/test3.txt')
		);

		$this->assertFileExists($testDirBase.'/test3.txt');

		$this->assertFileIsReadable($testDirBase.'/test3.txt');

		$this->assertEquals(
			false,
			$this->fs->rename($testDirBase.'/test3.txt', $testDirBase.'/test.txt', false)
		);

		$this->assertEquals(
			true,
			$this->fs->chmod($testDirBase.'/test.txt', 0444)
		);

		$this->assertFileNotIsWritable($testDirBase.'/test.txt');

		$this->assertEquals(
			true,
			$this->fs->rename($testDirBase.'/test3.txt', $testDirBase.'/test.txt', true)
		);

		$this->assertEquals(
			false,
			$this->fs->rename($testDirBase.'/test10.txt', $testDirBase.'/test11.txt', true)
		);

		$this->assertEquals(
			false,
			$this->fs->rename($testDirBase.'/http://10.txt', $testDirBase.'/test11.txt', true)
		);

		$this->assertEquals(
			true,
			$this->fs->remove($testDirBase)
		);

		$this->assertDirectoryNotExists($testDirBase);

		$errors = $this->fs->errorInfo();
		$this->assertLessThan(count($errors), 0);

		$this->fs->errorReset();

		$errors = $this->fs->errorInfo();
		$this->assertSame(count($errors), 0);

		return;
	}
}
