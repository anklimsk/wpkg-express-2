Language Codes Converter
========================

[![Latest Stable Version](http://img.shields.io/packagist/v/leodido/langcode-conv.svg?style=flat-square)](https://packagist.org/packages/leodido/langcode-conv) [![Build Status](https://img.shields.io/travis/leodido/langcode-conv.svg?style=flat-square)](https://travis-ci.org/leodido/langcode-conv) [![Coverage](http://img.shields.io/coveralls/leodido/langcode-conv.svg?style=flat-square)](https://coveralls.io/r/leodido/langcode-conv)

This library, based on [conversio library](https://github.com/leodido/conversio), is aimed to convert every existing language code to any format you want. No matter which format the input language code is.

Details
-------

The **available output formats** are:

1. `name`

    The international (often english) name of the language

2. `native`

    The language name written in native representation/s

3. `iso639-1`

    The ISO 639-1 (two-letters code) language representation
    
4. `iso639-2/t`

    The ISO 639-2/T (three-letters code for terminology applications) language representation

5. `iso639-2/b`

    The ISO 639-2/B (three-letters code, for bibliographic applications) language representation

6. `iso639-3`

    The ISO 639-3 (same as ISO 639-2/T except that for the macrolanguages) language representation

Currently **184 languages** are fully supported.

Examples
--------

First of all you need to create the conversion adapter and its options class.

```php
use Conversio\Conversion;
use Conversio\Adapter\LanguageCode;
use Conversio\Adapter\Options\LanguageCode;
// ...
$adapter = new LanguageCode();
$options = new LanguageCodeOptions();
```

Then, you can pass it to the `Conversion` class constructor (from [conversio library](https://github.com/leodido/conversio)):

```php
$converter = new Conversion($adapter);
$converter->setAdapterOptions($options);
```

Or, compactly:

```php
$converter = new Conversion(['adapter' => $adapter, 'options' => $options]);
```

Finally we need to specify the desired output format (see above the supported formats) of the conversion and perform it.

```php
$options->setOutput('native');
// ISO 639-1 => NATIVE
$converter->filter('it'); // italiano
```

Which ouputs, in this case `italiano`.

Have fun, try other language codes (e.g., `vie`, `tam`).

```php
// ISO 639-2/T => NATIVE
$converter->filter('vie'); // tiếng việt
$converter->filter('tam'); // தமிழ்
// ISO 639-3 => NATIVE
$converter->filter('yid + 2'); // ייִדיש
// ISO 639-3 => NAME
$options->setOutput('name');
$converter->filter('vie'); // vietnamese
$converter->filter('tam'); // tamil
$converter->filter('yid + 2'); // yiddish
```

Installation
------------

Add `leodido/langcode-conv` to your `composer.json`.

```json
{
   "require": {
       "leodido/langcode-conv": "v0.2.0"
   }
}
```

References
----------

- Language codes and schemes [reference](http://en.wikipedia.org/wiki/Language_code)
- The [ISO 639-1](http://en.wikipedia.org/wiki/ISO_639-1) standard
- The [ISO 639-2](http://en.wikipedia.org/wiki/ISO_639-2) standard
- The [ISO 639-3](http://en.wikipedia.org/wiki/ISO_639-3) standard
- [List](http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) of ISO 639-1 codes
- [List](http://en.wikipedia.org/wiki/List_of_ISO_639-2_codes) of ISO 639-2 codes
- [List](http://en.wikipedia.org/wiki/List_of_ISO_639-3_codes) of ISO 639-3 codes

---

[![Analytics](https://ga-beacon.appspot.com/UA-49657176-1/langcode-conv)](https://github.com/igrigorik/ga-beacon)