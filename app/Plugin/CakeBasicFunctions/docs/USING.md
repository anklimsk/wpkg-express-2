# Using this plugin

## Getting information about the current language of the UI and converting it

### Converting language code

```php
App::uses('Language', 'CakeTheme.Utility');

$language = new Language();
$result = $language->convertLangCode($lngCode, $outputFormat);
```

Where:
- `$lngCode` - Languge code for converting; 
- `$outputFormat` - Output format:
   * `name`: The international (often english) name of the language;
   * `native`: The language name written in native representations;
   * `iso639-1`: The ISO 639-1 (two-letters code) language representation;
   * `iso639-2/t`: The ISO 639-2/T (three-letters code for terminology applications) language representation;
   * `iso639-2/b`: The ISO 639-2/B (three-letters code, for bibliographic applications) language representation;
   * `iso639-3`: The ISO 639-3 (same as ISO 639-2/T except that for the macrolanguages) language representation.

