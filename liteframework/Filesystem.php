<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Filesystem
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Oct 2018
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\FilesystemInterface;

class Filesystem implements FilesystemInterface
{
	// @octal
	public $ownerWriteMode = 0644; // on delete

	// @array
	protected $errors = [];

	/**
	 * Directory scan
	 *
	 * @param  string $path
	 * @return array on success/false on failure
	*/
	public function scan(string $path) : array
	{
		if ($this->isDir($path) === false) {
			$this->errors[] = 'error_invalid_direcory_path';
			return [];
		}

		$files = scandir($path);
		if (is_array($files) === false) {
			$this->errors[] = 'error_direcory_scan';
			return [];
		}

		$i = 0;
		foreach ($files as $k => $fn) {
			if ($fn === '.' || $fn === '..') {
				unset($files[$k]);
				$i += 1;
			}

			if ($i == 2)
				break;
		}

		return array_merge($files);
	}

	/**
	 * Make directory
	 *
	 * @param  string $path
	 * @param  int $mode (octal)
	 * @return bool true on success/false on failure
	*/
	public function mkdir(string $path, $mode = 0755) : bool
	{
		if ($this->isDir($path) === true)
			return true;

		$rc = @mkdir($path, $mode, true);
		if ($rc === false) {
			$e = error_get_last();
			$this->errors[] = isset($e['message']) ? $e['message'] : 'error_mkdir';
			return false;
		}

		return true;
	}

	/**
	 * Read from file (get contents)
	 *
	 * @param  string $filename
	 * @param  mixed $content (reference)
	 * @return bool true on success/false on failure
	*/
	public function read(string $filename, &$content) : bool
	{
		if ($filename === '' || $this->isFile($filename) === false)
			return false;
		$content = file_get_contents($filename);
		return $content === false ? false : true;
	}

	/**
	 * Write to file (new file)
	 *
	 * @param  string $filename
	 * @param  string $content (reference)
	 * @param  int $mode (octal)
	 * @return bool true on success/false on failure
	*/
	public function write(string $filename, string& $content, $mode = 0644) : bool
	{
		if ($this->isFile($filename) === true) {
			if ($this->makeWritable($filename) === false)
				return false;
		}

		$fd = @fopen($filename, 'w');
		if ($fd === false) {
			$e = error_get_last();
			$this->errors[] = isset($e['message']) ? $e['message'] : 'error_fopen['.$filename.']';
			return false;
		}

		$rc = @fwrite($fd, $content);
		if ($rc === false) {
			fclose($fd);
			$e = error_get_last();
			$this->errors[] = isset($e['message']) ? $e['message'] : 'error_fwrite['.$filename.']';
			return false;
		}

		fclose($fd);
		return $this->chmod($filename, $mode);
	}

	/**
	 * Copy
	 *
	 * @param  string $originFile
	 * @param  string $targetFile
	 * @param  bool $overwrite
	 * @return bool true on success/false on failure
	*/
	public function copy(string $origin, string $target, bool $overwrite = false) : bool
	{
		return $this->copyFile($origin, $target, $overwrite);
	}

	/**
	 * Rename (directory|file)
	 *
	 * @param  string $from
	 * @param  string $to
	 * @return bool true on success/false on failure
	*/
	public function rename(string $from, string $to, bool $overwrite = false) : bool
	{
		if ($this->isReadable($from) === false) {
			$this->errors[] = sprintf('error_cannot_rename_target_not_readable', $from);
			return false;
		}

		if ($this->exists($to) === true) {
			if ($overwrite === false) {
				$this->errors[] = sprintf('error_cannot_rename_target_exists', $to);
				return false;
			}

			if ($this->isWritable($to) === false) {
				$rc = $this->makeWritable($to);
				if ($rc === false)
					return false;
			}
		}

		$rc = @rename($from, $to);
		if ($rc === false) {
			$e = error_get_last();
			$this->errors[] = isset($e['message']) ? $e['message'] : 'error_rename_file';
		}

		return $rc;
	}

	/**
	 * Remove (directory|file)
	 *
	 * @param  string $path
	 * @return bool true on success/false on failure
	*/
	public function remove(string $path) : bool
	{
		$rc = false;

		if ($this->isDir($path) === true) {
			$rc = $this->rrmdir($path);
		}
		else if ($this->isFile($path) === true) {
			if ($this->makeWritable($path) === false)
				return false;
			$rc = unlink($path);
		}

		return $rc;
	}

	/**
	 * Exists (directory|file)
	 *
	 * @param  string $filename
	 * @return bool true on success/false on failure
	*/
	public function exists(string $filename) : bool
	{
		return file_exists($filename) === true ? true : false;
	}

	/**
	 * Is file
	 *
	 * @param  string $filename
	 * @return bool true on success/false on failure
	*/
	public function isFile(string $filename) : bool
	{
		return is_file($filename) === true ? true : false;
	}

	/**
	 * Is directory
	 *
	 * @param  string $path
	 * @return bool true on success/false on failure
	*/
	public function isDir(string $path) : bool
	{
		return is_dir($path) === true ? true : false;
	}

	/**
	 * Is writable
	 *
	 * @param  string $path
	 * @return bool true on success/false on failure
	*/
	public function isWritable(string $path) : bool
	{
		return is_writable($path) === true ? true : false;
	}

	/**
	 * Is readable
	 *
	 * @param  string $path
	 * @return bool true on success/false on failure
	*/
	public function isReadable(string $path) : bool
	{
		return is_readable($path) === true ? true : false;
	}

	/**
	 * Get File Time
	 *
	 * @param  string file path
	 * @return int >0 on success/int -1 on failure
	*/
	public function filetime(string $filename) : int
	{
		if ($this->isFile($filename) === false)
			return -1;
		$time = filemtime($filename);
		if ($time === false) {
			$e = error_get_last();
			$this->errors[] = isset($e['message']) ? $e['message'] : 'error_filetime['.$filename.']';
			return -1;
		}
		return (int) $time;
	}

	/**
	 * Get file size
	 *
	 * @param  string file path
	 * @return int >=0 on success/int -1 on failure
	*/
	public function filesize(string $filename) : int
	{
		if ($this->isFile($filename) === false)
			return -1;
		$size = filesize($filename);
		if ($size === false) {
			$e = error_get_last();
			$this->errors[] = isset($e['message']) ? $e['message'] : 'error_filesize['.$filename.']';
			return -1;
		}
		return (int) $size;
	}

	/**
	 * Changes mode (directory|file)
	 *
	 * @param  string $path
	 * @param  int $mode (octal)
	 * @return bool true on success/false on failure
	*/
	public function chmod(string $path, $mode) : bool
	{
		$rc = chmod($path, $mode);
		return $rc;
	}

	/**
	 * Make Writable (directory|file)
	 *
	 * @param  string $path
	 * @return bool
	*/
	public function makeWritable($path) : bool
	{
		if ($this->isWritable($path) === true)
			return true;
		$rc = $this->chmod($path, $this->ownerWriteMode);
		return $rc === false ? false : true;
	}

	/**
	 * Make index.html
	 *
	 * @param  string (directory path) $dirPath
	 * @param  int $mode (octal)
	 * @return bool true on success/false on failure
	*/
	public function makeIndexHtml(string $dirPath, $mode = 0644) : bool
	{
		if ($this->isDir($dirPath) === false)
			return false;

		$filename = $dirPath.'/index.html';
		if ($this->isFile($filename) === true)
			return true;

		$content = '<!doctype html><html><head></head><body></body></html>';

		if ($this->write($filename, $content) === false)
			return false;

		if ($this->chmod($filename, $mode) === false)
			return false;

		unset($content);
		return true;
	}

	/**
	 * Get errors
	 *
	 * @return array
	*/
	public function errorInfo() : array
	{
		return $this->errors;
	}

	/**
	 * Reset errors info
	 *
	 * @return void
	*/
	public function errorReset() : void
	{
		$this->errors = [];
		return;
	}

	/**
	 * Copy file
	 *
	 * @param  string $originFile
	 * @param  string $targetFile
	 * @param  bool $overwrite
	 * @return bool true on success/false on failure
	*/
	protected function copyFile(string &$originFile, string &$targetFile, bool &$overwrite = false) : bool
	{
		if ($this->isFile($originFile) === false)
			return false;

		if ($overwrite === false && $this->isFile($targetFile) === true)
			return false;

		$rc = copy($originFile, $targetFile);
		if ($rc === false) {
			$e = error_get_last();
			$this->errors[] = isset($e['message']) ? $e['message'] : 'error_copy['.$originFile.']';
			return false;
		}

		return true;
	}

	/**
	 * Remove directory (sub-directories|files)
	 *
	 * @param  string $pathName
	 * @return bool
	*/
	protected function rrmdir(string $pathName) : bool
	{
		if ($this->isDir($pathName) === false)
			return false;

		if ($this->makeWritable($pathName) === false)
			return false;

		$items = scandir($pathName);
		foreach ($items as $item) {
			if ($item === '.' || $item === '..')
				continue;

			$path = $pathName.'/'.$item;
			if ($this->isDir($path) === true && is_link($path) === false) {
				$this->rrmdir($path);
			}
			else {
				if ($this->makeWritable($path) === false)
					return false;
				$rc = unlink($path);
				if ($rc === false)
					return false;
			}
		}

		$rc = rmdir($pathName);
		return $rc;
	}
}
