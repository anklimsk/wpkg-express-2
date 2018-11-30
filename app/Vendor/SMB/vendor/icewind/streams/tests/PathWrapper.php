<?php
/**
 * Copyright (c) 2016 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Streams\Tests;

class PathWrapper extends \PHPUnit_Framework_TestCase {
	private function getDataStream($data) {
		$stream = fopen('php://temp', 'w+');
		fwrite($stream, $data);
		rewind($stream);
		return $stream;
	}

	public function testFileGetContents() {
		$data = 'foobar';
		$stream = $this->getDataStream($data);
		$path = \Icewind\Streams\PathWrapper::getPath($stream);
		$this->assertEquals($data, file_get_contents($path));
	}
}
