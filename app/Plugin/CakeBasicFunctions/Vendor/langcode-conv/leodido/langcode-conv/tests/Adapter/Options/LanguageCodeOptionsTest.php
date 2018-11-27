<?php
/**
 * Language code conversions
 *
 * @link        https://github.com/leodido/langcode-conv
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\Adapter\Options;

use Conversio\Adapter\Options\LanguageCodeOptions;

/**
 * Class LanguageCodeOptionsTest
 */
class LanguageCodeOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $opts = new LanguageCodeOptions;
        $this->assertInstanceOf('Conversio\Adapter\Options\LanguageCodeOptions', $opts);
    }

    public function testSetAndGetOutput()
    {
        $opts = new LanguageCodeOptions();
        $this->assertInstanceOf('Conversio\Adapter\Options\LanguageCodeOptions', $opts->setOutput('native'));
        $this->assertEquals('native', $opts->getOutput());
    }

    /**
     * @expectedException \Conversio\Exception\InvalidArgumentException
     */
    public function testSetNotSupportedOutputShouldThrowInvalidArgumentException()
    {
        $opts = new LanguageCodeOptions();
        $opts->setOutput('giargianese');
    }
}
