<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\TestAsset\Adapter;

use Conversio\Adapter\AbstractOptionsEnabledAdapter;

/**
 * Class AdapterWithOptions
 */
class AdapterWithOptions extends AbstractOptionsEnabledAdapter
{
    /**
     * {@inheritdoc}
     */
    public function convert($value)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'AdapterWithOptions';
    }
}
