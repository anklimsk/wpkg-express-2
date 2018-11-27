<?php
/**
 * Language code conversions
 *
 * @link        https://github.com/leodido/langcode-conv
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace ConversioTest\Integration;

use Conversio\Adapter\Options\LanguageCodeOptions;
use Conversio\Conversion;

/**
 * Class IntegrationTest
 * @group integration
 */
class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $languageData
     * @param string $inputFormat
     * @param string $outputFormat
     * @dataProvider languageCodeConversionsProvider
     */
    public function testConversions($languageData, $inputFormat, $outputFormat)
    {
        $opts = new LanguageCodeOptions();
        $converter = new Conversion([
            'adapter' => 'Conversio\Adapter\LanguageCode',
            'options' => $opts
        ]);

        $opts->setOutput($outputFormat);
        $this->assertEquals($opts->getOutput(), $outputFormat);
        $this->assertEquals($languageData[$opts->getOutput()], $converter->filter($languageData[$inputFormat]));
    }

    /**
     * @return array
     */
    public function languageCodeConversionsProvider()
    {
        // Native is ignored because exists different languages natively called with the same name
        $formats = ['name',/* 'native',*/ 'iso639-1', 'iso639-2/t', 'iso639-2/b', 'iso639-3'];
        // Retrieve all the language codes data
        $class = new \ReflectionClass('Conversio\Adapter\LanguageCode');
        $property = $class->getProperty('languageCode');
        $property->setAccessible(true);
        $instance = $class->newInstance();
        $languageCode = $property->getValue($instance);
        // Create data matrix
        $data = [];
        foreach ($formats as $key => $inputFormat) {
            $otherFormats = $formats;
            unset($otherFormats[$key]);
            foreach ($otherFormats as $outputFormat) {
                foreach ($languageCode as $language) {
                    $data[] = [$language, $inputFormat, $outputFormat];
                }
            }
        }

        return $data;
    }
}
