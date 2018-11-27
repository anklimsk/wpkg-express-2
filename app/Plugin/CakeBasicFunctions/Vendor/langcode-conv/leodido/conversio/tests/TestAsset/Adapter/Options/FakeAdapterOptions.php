<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\TestAsset\Adapter\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class FakeAdapterOptions
 */
class FakeAdapterOptions extends AbstractOptions
{
    /**
     * @var string
     */
    protected $fake = 'AAA';

    /**
     * @return string
     */
    public function getFake()
    {
        return $this->fake;
    }

    /**
     * @param string $fake
     */
    public function setFake($fake)
    {
        $this->fake = $fake;
    }

}
