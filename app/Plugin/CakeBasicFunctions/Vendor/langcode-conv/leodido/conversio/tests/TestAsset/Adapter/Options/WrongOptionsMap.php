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
 * Class WrongOptionsMap
 */
class WrongOptionsMap extends OptionsMap
{
    protected $config = [
        'number' => [1, 2, 3],
        'string' => 'this value is not a list',
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
    public function setNotSet($value)
    {
        $this->setOption('not_set', $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNotSet()
    {
        return $this->getOption('not_set');
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
