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

/**
 * Class ConvertNothing
 */
class ConvertNothing implements ConversionAlgorithmInterface
{
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
        return 'ConvertNothing';
    }
}
