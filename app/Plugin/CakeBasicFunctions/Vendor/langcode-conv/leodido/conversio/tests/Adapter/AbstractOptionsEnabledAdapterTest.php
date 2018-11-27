<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\Adapter;

use Conversio\Adapter\AbstractOptionsEnabledAdapter;
use Conversio\Exception\DomainException;
use Conversio\Exception\RuntimeException;
use ConversioTest\TestAsset\Adapter\AdapterWithOptions;
use ConversioTest\TestAsset\Adapter\Options\AdapterWithOptionsOptions;
use Zend\Stdlib\AbstractOptions;

/**
 * Class AbstractOptionsEnabledAdapterTest
 */
class AbstractOptionsEnabledAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractOptionsEnabledAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new AdapterWithOptions();
    }

    /**
     * @expectedException DomainException
     */
    public function testSetNotValidOptionsShouldThrowDomainException()
    {
        /** @var $abstractOptsMock AbstractOptions */
        $abstractOptsMock = $this->getMockForAbstractClass('Zend\Stdlib\AbstractOptions');
        $this->adapter->setOptions($abstractOptsMock);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetNotSetOptionsShouldThrowRuntimeException()
    {
        $this->adapter->getOptions();
    }
    
    public function testOptions()
    {
        $opts = new AdapterWithOptionsOptions(['opt1' => 1, 'opt2' => 2]);
        $this->assertInstanceOf(
            'ConversioTest\TestAsset\Adapter\AdapterWithOptions',
            $this->adapter->setOptions($opts)
        );
        $options = $this->adapter->getOptions();
        $this->assertInstanceOf('ConversioTest\TestAsset\Adapter\Options\AdapterWithOptionsOptions', $options);
        $this->assertAttributeEquals(1, 'opt1', $options);
        $this->assertAttributeEquals(2, 'opt2', $options);
        $options = $options->toArray();
        $this->assertArrayHasKey('opt1', $options);
        $this->assertArrayHasKey('opt2', $options);
    }
}
