<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\TestAsset\Adapter;

use Conversio\ConversionAlgorithmInterface;
use ConversioTest\TestAsset\Adapter\Options\FakeAdapterOptions;

/**
 * Class FakeAdapter
 */
class FakeAdapter implements ConversionAlgorithmInterface
{
    /**
     * @var FakeAdapterOptions
     */
    protected $options = null;

    /**
     * {@inheritdoc}
     */
    public function convert($value)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'FakeAdapter';
    }

    /**
     * @param FakeAdapterOptions $options
     * @return $this
     */
    public function setOptions(FakeAdapterOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return FakeAdapterOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
}
