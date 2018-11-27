<?php
/**
 * Language code conversions
 *
 * @link        https://github.com/leodido/langcode-conv
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\Adapter;

use Conversio\Adapter\LanguageCode;
use Conversio\Adapter\Options\LanguageCodeOptions;
use Conversio\Conversion;

/**
 * Class LanguageCodeTest
 */
class LanguageCodeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $adapter = new LanguageCode;
        $converter = new Conversion($adapter);
        $this->assertInternalType('string', $adapter->getName());
        $this->assertEquals($adapter->getName(), $converter->getAdapterName());
    }

    public function testConvert()
    {
        $opts = new LanguageCodeOptions;
        $adapter = new LanguageCode;
        $adapter->setOptions($opts);

        // Not string input
        $this->assertNull($adapter->convert(1));

        // Inexistent language code
        $opts->setOutput('name');
        $res = $adapter->convert('inexistent');
        $this->assertNull($res);

        $opts->setOutput('native');
        $res = $adapter->convert('it');
        $this->assertEquals('italiano', $res);
        $this->assertInternalType('string', $res);
    }
}
