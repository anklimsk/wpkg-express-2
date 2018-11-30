<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Streams\Tests;

class DirectoryFilter extends \PHPUnit_Framework_TestCase {
	public function testFilterAcceptAll() {
		$this->filter(array('a', 'b', 'c'),
			function () {
				return true;
			},
			array('a', 'b', 'c')
		);
	}

	public function testFilterRejectAll() {
		$this->filter(array('a', 'b', 'c'),
			function () {
				return false;
			},
			array()
		);
	}

	public function testFilterRejectLong() {
		$this->filter(array('a', 'bb', 'c'),
			function ($file) {
				return strlen($file) < 2;
			},
			array('a', 'c')
		);
	}

	private function filter(array $files, callable $filter, array $expected) {
		$source = \Icewind\Streams\IteratorDirectory::wrap($files);
		$filtered = \Icewind\Streams\DirectoryFilter::wrap($source, $filter);
		$result = array();
		while (($file = readdir($filtered)) !== false) {
			$result[] = $file;
		}
		$this->assertEquals($expected, $result);
	}
}
