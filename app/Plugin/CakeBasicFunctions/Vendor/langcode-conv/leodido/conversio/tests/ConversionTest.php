<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest;

use Conversio\Conversion;
use ConversioTest\TestAsset\Adapter\FakeAdapter;
use ConversioTest\TestAsset\Adapter\Options\FakeAdapterOptions;

/**
 * Class ConversionTest
 */
class ConversionTest extends \PHPUnit_Framework_TestCase
{
    const CLASSNAME = 'Conversio\Conversion';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mock;

    public function setUp()
    {
        $this->mock = $this->getMockBuilder(self::CLASSNAME)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructor()
    {
        // array input
        $input = [];
        $this->mock->expects($this->at(0))
             ->method('setOptions')
             ->with($this->equalTo($input));

        $class = new \ReflectionClass(self::CLASSNAME);
        $ctor = $class->getConstructor();

        $ctor->invoke($this->mock, $input);

        // traversable input
        $input = new \ArrayIterator([]);
        $this->mock->expects($this->at(0))
            ->method('setOptions')
            ->with($this->equalTo($input->getArrayCopy()));

        $ctor->invoke($this->mock, $input);

        // string input
        $input = 'adapter';
        $this->mock->expects($this->at(0))
            ->method('setAdapter')
            ->with($this->equalTo($input));

        $ctor->invoke($this->mock, $input);

        // adapter input
        $input = $this->getMockForAbstractClass('Conversio\ConversionAlgorithmInterface');
        $this->mock->expects($this->at(0))
            ->method('setAdapter')
            ->with($this->equalTo($input));

        $ctor->invoke($this->mock, $input);

        // null input
        $input = null;
        $this->mock->expects($this->never())
            ->method('setAdapter')
            ->with($this->equalTo($input));
        $this->mock->expects($this->never())
            ->method('setOptions')
            ->with($this->equalTo($input));

        $ctor->invoke($this->mock, $input);
    }

    public function testGetOptionsFQCNWithNotAConversionAlgorithmInterfaceShouldThrowInvalidArgumentException()
    {
        $this->setExpectedException('Conversio\Exception\InvalidArgumentException');
        Conversion::getOptionsFullQualifiedClassName(new \stdClass());
    }

    public function testGetAdapterNotSetShouldThrowRuntimeException()
    {
        $filter = new Conversion();
        $this->setExpectedException('Conversio\Exception\RuntimeException');
        $filter->getAdapter();
    }

    public function testSetInvalidTypeAdapterShouldThrowInvalidArgumentException()
    {
        $filter = new Conversion();
        $this->setExpectedException('Conversio\Exception\InvalidArgumentException');
        $filter->setAdapter(new \stdClass());
    }

    public function testSetNonExistentAdapterShouldThrowRuntimeException()
    {
        $filter = new Conversion();
        $this->setExpectedException('Conversio\Exception\RuntimeException');
        $filter->setAdapter('Conversio\Phantom\NonExistentAdapter');

        $this->setExpectedException('Conversio\Exception\RuntimeException');
        $filter->getAdapter();
    }

    public function testSetInvalidAdapterClassShouldThrowInvalidArgumentException()
    {
        $filter = new Conversion();
        $this->setExpectedException('Conversio\Exception\InvalidArgumentException');
        $filter->setAdapter('\ArrayIterator');

        $this->setExpectedException('Conversio\Exception\RuntimeException');
        $filter->getAdapter();
    }

    public function testSetAdapter()
    {
        $adapterClassName = 'ConversioTest\TestAsset\Adapter\ConvertNothing';
        $filter = new Conversion();

        // string param
        $filter->setAdapter($adapterClassName);
        $this->assertInstanceOf($adapterClassName, $filter->getAdapter());
        $this->assertEquals($filter->getAdapter()->getName(), $filter->getAdapterName());

        // instance param
        /** @var $adapterInstance \ConversioTest\TestAsset\Adapter\ConvertNothing */
        $adapterInstance = new $adapterClassName();
        $filter->setAdapter($adapterInstance);
        $this->assertInstanceOf($adapterClassName, $filter->getAdapter());
        $this->assertEquals($adapterInstance->getName(), $filter->getAdapterName());
    }

    public function testSetInvalidTypeOptionsShouldThrowInvalidArgumentException()
    {
        $filter = new Conversion();
        $this->setExpectedException('Conversio\Exception\InvalidArgumentException');
        $filter->setOptions('invalidoptions');
    }

    public function testSetOptions()
    {
        $onlyOpts = [
            'options' => ['prop1' => 1, 'prop2' => 2]
        ];

        $this->mock->expects($this->at(0))
            ->method('setAdapterOptions')
            ->with($this->equalTo($onlyOpts['options']));
        $class = new \ReflectionClass(self::CLASSNAME);
        $setOptsMethod = $class->getMethod('setOptions');

        $setOptsMethod->invoke($this->mock, $onlyOpts);

        $opts = [
            'adapter' => 'ConversioTest\TestAsset\Adapter\ConvertNothing',
            'options' => ['prop1' => 1, 'prop2' => 2],
        ];

        $this->mock->expects($this->at(0))
            ->method('setAdapter')
            ->with($this->equalTo($opts['adapter']));
        $this->mock->expects($this->at(1))
            ->method('setAdapterOptions')
            ->with($this->equalTo($opts['options']));
        $class = new \ReflectionClass(self::CLASSNAME);
        $setOptsMethod = $class->getMethod('setOptions');

        $setOptsMethod->invoke($this->mock, $opts);

        $opts = [
            'options' => ['prop1' => 1, 'prop2' => 2],
            'adapter' => 'ConversioTest\TestAsset\Adapter\ConvertNothing',
        ];

        $this->mock->expects($this->at(0))
            ->method('setAdapterOptions')
            ->with($this->equalTo($opts['options']));
        $this->mock->expects($this->at(1))
            ->method('setAdapter')
            ->with($this->equalTo($opts['adapter']));
        $class = new \ReflectionClass(self::CLASSNAME);
        $setOptsMethod = $class->getMethod('setOptions');

        $setOptsMethod->invoke($this->mock, $opts);
    }

    public function testGetAdapterOptionsWhenAdapterHasNotBeenSpecifiedShouldThrowRuntimeException()
    {
        $onlyOpts = [
            'options' => ['opt1' => 'O1', 'opt2' => 'O2'],
        ];
        $filter = new Conversion($onlyOpts);
        $this->setExpectedException('Conversio\Exception\InvalidArgumentException');
        $filter->getAdapterOptions();
    }

    public function testGetAdapterOptionsWhenAdapterAbstractOptionsClassDoesNotExistShouldThrowDomainException()
    {
        $onlyOpts = [
            'options' => ['opt1' => 'O1', 'opt2' => 'O2'],
            'adapter' => 'ConversioTest\TestAsset\Adapter\ConvertNothing',
        ];
        $filter = new Conversion($onlyOpts);
        $this->setExpectedException(
            'Conversio\Exception\DomainException',
            sprintf(
                '%s::getAbstractOptions" expects that an options class ("%s") for the current adapter exists',
                self::CLASSNAME,
                'ConversioTest\TestAsset\Adapter\Options\ConvertNothingOptions'
            )
        );
        $filter->getAdapterOptions();
    }

    public function testGetAdapterOptionsWhenAdapterOptionsAreNotAbstractOptionsShouldThrowDomainException()
    {
        $onlyOpts = [
            'options' => ['opt1' => 'O1', 'opt2' => 'O2'],
            'adapter' => 'ConversioTest\TestAsset\Adapter\WrongAdapter',
        ];
        $filter = new Conversion($onlyOpts);
        $this->setExpectedException(
            'Conversio\Exception\DomainException',
            sprintf(
                '%s::getAbstractOptions" expects the options class to resolve to a valid "%s" instance; received "%s"',
                self::CLASSNAME,
                'Zend\Stdlib\AbstractOptions',
                'ConversioTest\TestAsset\Adapter\Options\WrongAdapterOptions'
            )
        );
        $filter->getAdapterOptions();
    }

    public function testGetAdapterOptionsWhenNotMachingAdapterOptionsWasSetShouldThrowDomainException()
    {
        /** @var $mockOptions \Zend\Stdlib\AbstractOptions */
        $mockOptions = $this->getMockForAbstractClass('\Zend\Stdlib\AbstractOptions');
        $filter = new Conversion();
        $filter->setAdapter(new FakeAdapter());
        $filter->setAdapterOptions($mockOptions);
        $this->setExpectedException('Conversio\Exception\DomainException');
        $filter->getAdapterOptions();
    }

    public function testGetAdapterOptionsAndOptions()
    {
        $fakeAdapterOpts = new FakeAdapterOptions();
        $filter = new Conversion();
        $filter->setAdapter(new FakeAdapter());
        $filter->setAdapterOptions($fakeAdapterOpts);
        $this->assertInstanceOf(get_class($fakeAdapterOpts), $filter->getAdapterOptions());
        $this->assertEquals($fakeAdapterOpts, $filter->getAdapterOptions());
        $this->assertEquals($fakeAdapterOpts->toArray(), $filter->getOptions());

        $params = [
            'adapterOptions' => ['fake' => 'fake'],
            'adapter' => 'ConversioTest\TestAsset\Adapter\FakeAdapter',
        ];
        $filter = new Conversion($params);
        $this->assertInstanceOf(get_class($fakeAdapterOpts), $filter->getAdapterOptions());
        $this->assertEquals(new FakeAdapterOptions($params['adapterOptions']), $filter->getAdapterOptions());
        $this->assertEquals($params['adapterOptions'], $filter->getOptions());


        $filter = new Conversion();
        $filter->setAdapter(new FakeAdapter());
        $this->assertEmpty($filter->getOptions());
        $this->assertEquals($fakeAdapterOpts, $filter->getAdapterOptions());
        $this->assertEquals($fakeAdapterOpts->toArray(), $filter->getOptions());

        $this->assertEquals($fakeAdapterOpts->getFake(), $filter->getOptions('fake'));

        $this->setExpectedException('Conversio\Exception\RuntimeException');
        $filter->getOptions('not_exists');
    }

    public function testSetAdapterOptionsWithInvalidTypeInputShouldThrowInvalidArgumentException()
    {
        $filter = new Conversion();
        $this->setExpectedException(
            'Conversio\Exception\InvalidArgumentException',
            sprintf(
                '"%s::setAdapterOptions" expects an array or a valid instance of "%s"; received "%s"',
                self::CLASSNAME,
                'Zend\Stdlib\AbstractOptions',
                'string'
            )
        );
        $filter->setAdapterOptions('invalid');
    }

    public function testFilter()
    {
        // Filtering a non string return the non strig input
        $notAString = [];
        $filter = new Conversion();
        $this->assertEquals($notAString, $filter->filter($notAString));

        // Filtering
        $input = 'string';

        $adapterMock = $this->getMock('Conversio\ConversionAlgorithmInterface');

        $this->mock->expects($this->at(0))
                   ->method('getAdapter')
                   ->willReturn($adapterMock);

        $adapterMock->expects($this->at(0))
                    ->method('convert')
                    ->with($this->equalTo($input));

        $class = new \ReflectionClass(self::CLASSNAME);
        $filterMethod = $class->getMethod('filter');
        $filterMethod->invoke($this->mock, $input);
    }


    public function testGetAdapterThatHaveOptions()
    {
        $adapterOpts = new FakeAdapterOptions(['fake' => 'ABC']);
        $adapter = new FakeAdapter();

        $filter = new Conversion();
        $filter->setAdapter($adapter);
        $filter->setAdapterOptions($adapterOpts);

        $this->assertSame($adapter, $filter->getAdapter());
        $this->assertSame($adapterOpts, $adapter->getOptions());
    }
}
