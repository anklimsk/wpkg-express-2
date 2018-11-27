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
use ConversioTest\TestAsset\Adapter\Options\WrongAdapterOptions;

/**
 * Class WrongAdapter
 */
class WrongAdapter implements ConversionAlgorithmInterface
{
    /**
     * @var WrongAdapterOptions
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
        return 'WrongAdapter';
    }

    /**
     * @param WrongAdapterOptions $options
     * @return $this
     */
    public function setOptions(WrongAdapterOptions $options)
    {
        $this->options = $options;
        return $this;
    }
}
