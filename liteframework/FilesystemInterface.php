<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Filesystem interface
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Oct 2018
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface FilesystemInterface
{
	/**
	 * Directory scan
	 *
	 * @param  string $path
	 * @return array
	*/
	public function scan(string $path) : array;

	/**
	 * Make directory
	 *
	 * @param  string $path
	 * @param  octal $mode
	 * @return bool true on success/false on failure
	*/
	public function mkdir(string $path, $mode = 0755) : bool;

	/**
	 * Read from file (get contents)
	 *
	 * @param  string $filename
	 * @param  mixed $content (reference)
	 * @return bool true on success/false on failure
	*/
	public function read(string $filename, &$content) : bool;

	/**
	 * Write to file (new file)
	 *
	 * @param  string $filename
	 * @param  string $content (reference)
	 * @param  octal $mode
	 * @return bool true on success/false on failure
	*/
	public function write(string $filename, string& $content, $mode = 0644) : bool;

	/**
	 * Copy
	 *
	 * @param  string $originFile
	 * @param  string $targetFile
	 * @param  bool $overwrite
	 * @return bool true on success/false on failure
	*/
	public function copy(string $originFile, string $targetFile, bool $overwrite = false) : bool;

	/**
	 * Rename
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  bool $overwrite
	 * @return bool true on success/false on failure
	*/
	public function rename(string $from, string $to, bool $overwrite = false) : bool;

	/**
	 * Remove (directory|file)
	 *
	 * @param  string $filename
	 * @return bool true on success/false on failure
	*/
	public function remove(string $path) : bool;

	/**
	 * Exists (directory|file)
	 *
	 * @param  string $filename
	 * @return bool true on success/false on failure
	*/
	public function exists(string $filename) : bool;

	/**
	 * Is file
	 *
	 * @param  string $filename
	 * @return bool true on success/false on failure
	*/
	public function isFile(string $filename) : bool;

	/**
	 * Is directory
	 *
	 * @param  string $path
	 * @return bool true on success/false on failure
	*/
	public function isDir(string $path) : bool;

	/**
	 * Is writable
	 *
	 * @param  string $path
	 * @return bool true on success/false on failure
	*/
	public function isWritable(string $path) : bool;

	/**
	 * Is readable
	 *
	 * @param  string $path
	 * @return bool true on success/false on failure
	*/
	public function isReadable(string $path) : bool;

	/**
	 * Get file time
	 *
	 * @param  string file path
	 * @return int >0 on success/int -1 on failure
	*/
	public function filetime(string $filename) : int;

	/**
	 * Get file size
	 *
	 * @param  string file path
	 * @return int >=0 on success/int -1 on failure
	*/
	public function filesize(string $filename) : int;

	/**
	 * Changes mode (directory|file)
	 *
	 * @param  string $path
	 * @param  octal $mode
	 * @return bool true on success/false on failure
	*/
	public function chmod(string $path, $mode) : bool;

	/**
	 * Make writable (directory|file)
	 *
	 * @param  string $path
	 * @return bool
	*/
	public function makeWritable($path) : bool;

	/**
	 * Create index.html
	 *
	 * @param  string (directory path) $path
	 * @param  octal $mode
	 * @return bool true on success/false on failure
	*/
	public function makeIndexHtml(string $dirPath, $mode = 0644) : bool;

	/**
	 * Get errors
	 *
	 * @return mixed
	*/
	public function errorInfo() : array;

	/**
	 * Reset errors info
	 *
	 * @return void
	*/
	public function errorReset() : void;
}
