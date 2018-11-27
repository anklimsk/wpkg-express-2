<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace Conversio;

use Traversable;
use Zend\Filter\AbstractFilter;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class Conversion
 */
class Conversion extends AbstractFilter
{
    /**
     * @var ConversionAlgorithmInterface
     */
    protected $adapter;

    /**
     * @var AbstractOptions
     */
    protected $adapterOptions = [];

    /**
     * Class constructor
     *
     * @param null|array|string|Traversable|ConversionAlgorithmInterface $params Adapter and its options to set (opt.)
     */
    public function __construct($params = null)
    {
        if ($params instanceof Traversable) {
            $params = ArrayUtils::iteratorToArray($params);
        }
        switch(true) {
            case is_string($params):
            case $params instanceof ConversionAlgorithmInterface:
                $this->setAdapter($params);
                break;
            case is_array($params):
                $this->setOptions($params);
                break;
            default:
                break;
        }
    }

    /**
     * Sets conversion adapter
     *
     * @param  string|ConversionAlgorithmInterface $adapter Adapter to use
     * @return $this
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function setAdapter($adapter)
    {
        if (!is_string($adapter) && !$adapter instanceof ConversionAlgorithmInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" expects a string or an instance of ConversionAlgorithmInterface; received "%s"',
                __METHOD__,
                is_object($adapter) ? get_class($adapter) : gettype($adapter)
            ));
        }
        if (is_string($adapter)) {
            if (!class_exists($adapter)) {
                throw new Exception\RuntimeException(sprintf(
                    '"%s" unable to load adapter; class "%s" not found',
                    __METHOD__,
                    $adapter
                ));
            }
            $adapter = new $adapter();
            if (!$adapter instanceof ConversionAlgorithmInterface) {
                throw new Exception\InvalidArgumentException(sprintf(
                    '"%s" expects a string representing an instance of ConversionAlgorithmInterface; received "%s"',
                    __METHOD__,
                    is_object($adapter) ? get_class($adapter) : gettype($adapter)
                ));
            }
        }
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Returns the current adapter
     *
     * @return ConversionAlgorithmInterface
     * @throws Exception\RuntimeException
     */
    public function getAdapter()
    {
        if (!$this->adapter) {
            throw new Exception\RuntimeException(sprintf(
                '"%s" unable to load adapter; adapter not found',
                __METHOD__
            ));
        }
        if (method_exists($this->adapter, 'setOptions')) {
            $this->adapter->setOptions($this->getAdapterOptions());
        }

        return $this->adapter;
    }

    /**
     * Retrieve adapter name
     *
     * @return string
     */
    public function getAdapterName()
    {
        return $this->getAdapter()->getName();
    }

    /**
     * Set adapter options
     *
     * @param array|AbstractOptions $options
     * @return $this
     */
    public function setAdapterOptions($options)
    {
        if (!is_array($options) && !$options instanceof AbstractOptions) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" expects an array or a valid instance of "%s"; received "%s"',
                __METHOD__,
                'Zend\Stdlib\AbstractOptions',
                is_object($options) ? get_class($options) : gettype($options)
            ));
        }
        $this->adapterOptions = $options;
        $this->options = $options;

        return $this;
    }

    /**
     * @return AbstractOptions
     */
    public function getAdapterOptions()
    {
        if (is_array($this->adapterOptions)) {
            $optClass = $this->getAbstractOptions();
            $this->adapterOptions = $optClass->setFromArray($this->adapterOptions);
            return $this->adapterOptions;
        }
        $wantedOptionsClass = self::getOptionsFullQualifiedClassName($this->adapter);
        if (get_class($this->adapterOptions) !== $wantedOptionsClass) {
            throw new Exception\DomainException(sprintf(
                '"%s" expects that options set are an array or a valid "%s" instance; received "%s"',
                __METHOD__,
                $wantedOptionsClass,
                get_class($this->adapterOptions)
            ));
        }
        $this->options = $this->adapterOptions->toArray();
        return $this->adapterOptions;
    }

    /**
     * Instantiate and retrieve the options of the current adapter
     *
     * It also checks that an appropriate options class exists.
     *
     * @return AbstractOptions
     */
    protected function getAbstractOptions()
    {
        $optClass = self::getOptionsFullQualifiedClassName($this->adapter);
        // Does the option class exist?
        if (!class_exists($optClass)) {
            throw new Exception\DomainException(
                sprintf(
                    '"%s" expects that an options class ("%s") for the current adapter exists',
                    __METHOD__,
                    $optClass
                )
            );
        }
        $opts = new $optClass();
        if (!$opts instanceof AbstractOptions) {
            throw new Exception\DomainException(sprintf(
                '"%s" expects the options class to resolve to a valid "%s" instance; received "%s"',
                __METHOD__,
                'Zend\Stdlib\AbstractOptions',
                $optClass
            ));
        }
        return $opts;
    }

    /**
     * Set filter options
     *
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException if options is not an array or Traversable
     * @return $this
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" expects an array or Traversable; received "%s"',
                __METHOD__,
                is_object($options) ? get_class($options) : gettype($options)
            ));
        }
        foreach ($options as $key => $value) {
            if ($key == 'options') {
                $key = 'adapterOptions';
            }
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Get individual or all options from underlying adapter options object
     *
     * @param  string|null $option
     * @return mixed|array
     */
    public function getOptions($option = null)
    {
        $this->getAdapterOptions();
        if ($option !== null) {
            if (!isset($this->options[$option])) {
                throw new Exception\RuntimeException(sprintf(
                    'Options "%s" not found',
                    $option
                ));
            }
            return $this->options[$option];
        }
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        return $this->getAdapter()->convert($value);
    }

    /**
     * Recreate the full qualified class name of options class for the supplied adapter
     *
     * @param ConversionAlgorithmInterface|null $adapter
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public static function getOptionsFullQualifiedClassName($adapter)
    {
        if (!$adapter) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" unable to load adapter; adapter not found',
                __METHOD__
            ));
        }
        if (!$adapter instanceof ConversionAlgorithmInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" expects an instance of ConversionAlgorithmInterface; received "%s"',
                __METHOD__,
                is_object($adapter) ? get_class($adapter) : gettype($adapter)
            ));
        }

        $adapterClass = get_class($adapter);
        $namespace = substr($adapterClass, 0, strrpos($adapterClass, '\\'));
        return $namespace . '\\Options\\' . $adapter->getName() . 'Options';
    }
}
