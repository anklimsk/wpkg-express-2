<?php
/**
 * Language code conversions
 *
 * @link        https://github.com/leodido/langcode-conv
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace Conversio\Adapter\Options;

/**
 * Class LanguageCodeOptions
 */
class LanguageCodeOptions extends OptionsMap
{
    protected $config = [
        'output' => ['name', 'native', 'iso639-1', 'iso639-2/t', 'iso639-2/b', 'iso639-3']
    ];

    /**
     * Set output option
     *
     * @param $output
     * @return $this
     */
    public function setOutput($output)
    {
        $this->setOption('output', $output);
        return $this;
    }

    /**
     * Retrieve output option
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->getOption('output');
    }
}
