<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\Adapter\Options;

use Conversio\Adapter\Options\OptionsMap;
use Conversio\Exception\DomainException;
use Conversio\Exception\InvalidArgumentException;
use Conversio\Exception\RuntimeException;
use ConversioTest\TestAsset\Adapter\Options\FakeOptionsMap;
use ConversioTest\TestAsset\Adapter\Options\WrongOptionsMap;

/**
 * Class OptionsMapTest
 */
class OptionsMapTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $optsMap = new FakeOptionsMap;
        $this->assertInstanceOf('Conversio\Adapter\Options\OptionsMap', $optsMap);
        $this->assertInstanceOf('Zend\Stdlib\AbstractOptions', $optsMap);
    }

    /**
     * @expectedException DomainException
     */
    public function testConstructWithoutValidConfigurationShouldThrowDomainException()
    {
        new OptionsMap();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidOptionsValueShouldThrowInvalidArgumentException()
    {
        $optsMap = new FakeOptionsMap;
        $optsMap->setNumber('string');
    }

    public function testSettersAndGetters()
    {
        $optsMap = new FakeOptionsMap(['number' => 1, 'string' => '1']);
        $this->assertInstanceOf('ConversioTest\TestAsset\Adapter\Options\FakeOptionsMap', $optsMap);
        $this->assertEquals(1, $optsMap->getNumber());
        $this->assertEquals('1', $optsMap->getString());
    }

    public function testToArray()
    {
        $optsMap = new FakeOptionsMap;
        $this->assertEmpty($optsMap->toArray());

        $optsMap->setNumber(3);
        $this->assertArrayHasKey('number', $optsMap->toArray());
        $this->assertArrayNotHasKey('string', $optsMap->toArray());
        $optsMap->setString('3');
        $this->assertArrayHasKey('string', $optsMap->toArray());
    }

    /**
     * @expectedException DomainException
     */
    public function testSetNonExistentOptionShouldThrowDomainException()
    {
        $optsMap = new WrongOptionsMap;
        $optsMap->setNotSet('try!');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetNonExistentOptionShouldThrowRuntimeException()
    {
        $optsMap = new WrongOptionsMap;
        $optsMap->getNotSet();
    }

    /**
     * @expectedException DomainException
     */
    public function testSetNonListOptionsShouldThrowDomainException()
    {
        $optsMap = new WrongOptionsMap;
        $optsMap->setString('value');
    }
}
