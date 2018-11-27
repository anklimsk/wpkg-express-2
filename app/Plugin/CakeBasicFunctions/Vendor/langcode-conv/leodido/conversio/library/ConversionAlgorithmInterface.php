<?php
/**
 * Conversio
 *
 * @link        https://github.com/leodido/conversio
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace Conversio;

/**
 * Interface ConversionAlgorithmInterface
 *
 * @author leodido <leodidonato@gmail.com>
 */
interface ConversionAlgorithmInterface
{
    /**
     * Convert $value with the defined settings
     *
     * @param  string $value Data to decompress
     * @return string The converted data
     */
    public function convert($value);

    /**
     * Return the conversion adapter name
     *
     * @return string
     */
    public function getName();
}
