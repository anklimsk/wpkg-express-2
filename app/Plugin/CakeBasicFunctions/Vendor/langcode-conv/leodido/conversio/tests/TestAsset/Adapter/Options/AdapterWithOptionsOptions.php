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
 * Class AdapterWithOptionsOptions
 */
class AdapterWithOptionsOptions extends AbstractOptions
{
    protected $opt1;

    protected $opt2;

    /**
     * @return mixed
     */
    public function getOpt2()
    {
        return $this->opt2;
    }

    /**
     * @param mixed $opt2
     */
    public function setOpt2($opt2)
    {
        $this->opt2 = $opt2;
    }

    /**
     * @return mixed
     */
    public function getOpt1()
    {
        return $this->opt1;
    }

    /**
     * @param mixed $opt1
     */
    public function setOpt1($opt1)
    {
        $this->opt1 = $opt1;
    }
}
