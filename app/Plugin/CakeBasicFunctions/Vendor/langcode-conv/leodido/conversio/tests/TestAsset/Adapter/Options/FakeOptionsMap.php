<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\TestAsset\Adapter\Options;

use Conversio\Adapter\Options\OptionsMap;

/**
 * Class FakeOptionsMap
 */
class FakeOptionsMap extends OptionsMap
{
    protected $config = [
        'number' => [1, 2, 3],
        'string' => ['1', '2', '3'],
    ];

    /**
     * @param $value
     * @return $this
     */
    public function setNumber($value)
    {
        $this->setOption('number', $value);
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->getOption('number');
    }

    /**
     * @param $value
     * @return $this
     */
    public function setString($value)
    {
        $this->setOption('string', $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->getOption('string');
    }
}
