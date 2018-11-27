Conversio
=========

[![Latest Stable Version](http://img.shields.io/packagist/v/leodido/conversio.svg?style=flat-square)](https://packagist.org/packages/leodido/conversio) [![Build Status](https://img.shields.io/travis/leodido/conversio.svg?style=flat-square)](https://travis-ci.org/leodido/conversio) [![Coverage](http://img.shields.io/coveralls/leodido/conversio.svg?style=flat-square)](https://coveralls.io/r/leodido/conversio)

Conversio is a PHP library that provides a simple infrastructure to create your own converters and to perform any conversion.

Explaination
------------

The entry point for **conversion** is the class `Conversion`, that acts as a **filter** (i.e., `Zend\Filter\AbstractFilter`).

To implement a conversion you have to create an **adapter** (that will be passed to `Conversion` class) that describes its process.

The adapters must implement the `ConversionAlgorithmInterface` interface.

Furthermore, adapters can have **options** too, in the form of a `Zend\Stdlib\AbstractOptions` subclass. Conversio library requires only that the options class of each adapter is called with the name of the adapter followed by the suffix "Options" (e.g., `LanguageCodeOptions` is the option class of `LanguageCode` adapter class).

In this case your adapter can extend `AbstractOptionsEnabledAdapter` abstract class to take advantage of its options related methods.

The `OptionsMap` class is an utility class aimed to create a option class starting from a configuration hash that describes the options (by name) and their admitted values.

Installation
------------

Add `leodido/conversio` to your `composer.json`.

```json
{
   "require": {
       "leodido/conversio": "v0.2.0"
   }
}
```

Usage
-----

**WIP**

Converters
----------

Here will be listed the converters created using Conversio library.

- [LangCode](https://github.com/leodido/langcode-conv)

---

[![Analytics](https://ga-beacon.appspot.com/UA-49657176-1/conversio)](https://github.com/igrigorik/ga-beacon)
