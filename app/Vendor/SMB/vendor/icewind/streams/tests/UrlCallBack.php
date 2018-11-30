<?php
/**
 * Copyright (c) 2014 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Streams\Tests;

class UrlCallBack extends \PHPUnit_Framework_TestCase {
	protected $tempDirs = array();

	protected function getTempDir() {
		$dir = sys_get_temp_dir() . '/streams_' . uniqid();
		mkdir($dir);
		$this->tempDirs[] = $dir;
		return $dir;
	}

	public function tearDown() {
		foreach ($this->tempDirs as $dir) {
			$this->rmdir($dir);
		}
	}

	protected function rmdir($path) {
		$directory = new \RecursiveDirectoryIterator($path);
		$iterator = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::CHILD_FIRST);
		/**
		 * @var \SplFileInfo $file
		 */
		foreach ($iterator as $file) {
			if (in_array($file->getBasename(), array('.', '..'))) {
				continue;
			} elseif ($file->isDir()) {
				rmdir($file->getPathname());
			} elseif ($file->isFile() || $file->isLink()) {
				unlink($file->getPathname());
			}
		}
	}

	public function testFOpenCallBack() {
		$called = false;
		$callback = function () use (&$called) {
			$called = true;
		};
		$path = \Icewind\Streams\UrlCallBack::wrap('php://temp', $callback);
		$fh = fopen($path, 'r');
		fclose($fh);
		$this->assertTrue($called);
	}

	public function testOpenDirCallBack() {
		$called = false;
		$callback = function () use (&$called) {
			$called = true;
		};
		$path = \Icewind\Streams\UrlCallBack::wrap($this->getTempDir(), null, $callback);
		$fh = opendir($path);
		closedir($fh);
		$this->assertTrue($called);
	}

	public function testMKDirCallBack() {
		$called = false;
		$callback = function () use (&$called) {
			$called = true;
		};
		$dir = $this->getTempDir() . '/test';
		$path = \Icewind\Streams\UrlCallBack::wrap($dir, null, null, $callback);
		mkdir($path);
		$this->assertTrue(file_exists($dir));
		$this->assertTrue($called);
	}

	public function testRMDirCallBack() {
		$called = false;
		$callback = function () use (&$called) {
			$called = true;
		};
		$dir = $this->getTempDir() . '/test';
		mkdir($dir);
		$path = \Icewind\Streams\UrlCallBack::wrap($dir, null, null, null, null, $callback);
		rmdir($path);
		$this->assertFalse(file_exists($dir));
		$this->assertTrue($called);
	}

	public function testRenameCallBack() {
		$called = false;
		$callback = function () use (&$called) {
			$called = true;
		};
		$source = $this->getTempDir() . '/test';
		touch($source);
		$path = \Icewind\Streams\UrlCallBack::wrap($source, null, null, null, $callback);
		$target = $path->wrapPath($source . '_rename');
		rename($path, $target);
		$this->assertTrue(file_exists($source . '_rename'));
		$this->assertTrue($called);
	}

	public function testUnlinkCallBack() {
		$called = false;
		$callback = function () use (&$called) {
			$called = true;
		};
		$file = $this->getTempDir() . '/test';
		touch($file);
		$path = \Icewind\Streams\UrlCallBack::wrap($file, null, null, null, null, null, $callback);
		unlink($path);
		$this->assertFalse(file_exists($file));
		$this->assertTrue($called);
	}

	public function testStatCallBack() {
		$called = false;
		$callback = function () use (&$called) {
			$called = true;
		};
		$file = $this->getTempDir() . '/test';
		touch($file);
		$path = \Icewind\Streams\UrlCallBack::wrap($file, null, null, null, null, null, null, $callback);
		try {
			stat($path);
		} catch (\Exception $e) {
			$this->markTestSkipped('url_stat doesn\'t receive the context parameter, see php bug 50526');
		}
		$this->assertTrue($called);
	}

	public function testMKDirRecursive() {
		$dir = $this->getTempDir() . '/test/sad';
		$path = \Icewind\Streams\UrlCallBack::wrap($dir);
		mkdir($path, 0700, true);
		$this->assertTrue(file_exists($dir));
	}
}
