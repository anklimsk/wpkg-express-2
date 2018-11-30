<?php
/**
 * Copyright (c) 2014 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Streams\Tests;

class PartialWrapper extends \Icewind\Streams\NullWrapper {
	/**
	 * Wraps a stream with the provided callbacks
	 *
	 * @param resource $source
	 * @return resource
	 *
	 * @throws \BadMethodCallException
	 */
	public static function wrap($source) {
		$context = stream_context_create(array(
			'null' => array(
				'source' => $source)
		));
		return self::wrapSource($source, $context, 'partial', '\Icewind\Streams\Tests\PartialWrapper');
	}

	public function stream_read($count) {
		$count = min($count, 2); // return as most 2 bytes
		return parent::stream_read($count);
	}

	public function stream_write($data) {
		$data = substr($data, 0, 2); //write as most 2 bytes
		return parent::stream_write($data);
	}
}

class FailWrapper extends \Icewind\Streams\NullWrapper {
	/**
	 * Wraps a stream with the provided callbacks
	 *
	 * @param resource $source
	 * @return resource
	 *
	 * @throws \BadMethodCallException
	 */
	public static function wrap($source) {
		$context = stream_context_create(array(
			'null' => array(
				'source' => $source)
		));
		return self::wrapSource($source, $context, 'fail', '\Icewind\Streams\Tests\FailWrapper');
	}

	public function stream_read($count) {
		return false;
	}

	public function stream_write($data) {
		return false;
	}
}

class RetryWrapperTest extends WrapperTest {

	/**
	 * @param resource $source
	 * @return resource
	 */
	protected function wrapSource($source) {
		return \Icewind\Streams\RetryWrapper::wrap(PartialWrapper::wrap($source));
	}

	public function testReadDir() {
		$this->markTestSkipped('directories not supported');
	}

	public function testRewindDir() {
		$this->markTestSkipped('directories not supported');
	}

	public function testFailedRead() {
		$source = fopen('data://text/plain,foo', 'r');
		$wrapped = \Icewind\Streams\RetryWrapper::wrap(FailWrapper::wrap($source));
		$this->assertEquals('', fread($wrapped, 10));
	}

	public function testFailedWrite() {
		$source = fopen('php://temp', 'w');
		$wrapped = \Icewind\Streams\RetryWrapper::wrap(FailWrapper::wrap($source));
		fwrite($wrapped, 'foo');
	}
}
