<?php
/**
 * Language code conversions
 *
 * @link        https://github.com/leodido/langcode-conv
 * @copyright   Copyright (c) 2014, Leo Di Donato
 * @license     http://opensource.org/licenses/ISC      ISC license
 */
namespace Conversio\Adapter;

use Conversio\Adapter\Options\LanguageCodeOptions;
use Conversio\Exception;

/**
 * Class LanguageCode
 */
class LanguageCode extends AbstractOptionsEnabledAdapter
{
    /**
     * TODO: add language family
     *
     * @var array
     */
    protected $languageCode = [
        'ab' =>
            [
                'name' => 'abkhaz',
                'native' => 'аҧсуа бызшәа, аҧсшәа',
                'iso639-1' => 'ab',
                'iso639-2/t' => 'abk',
                'iso639-2/b' => 'abk',
                'iso639-3' => 'abk',
            ],
        'aa' =>
            [
                'name' => 'afar',
                'native' => 'afaraf',
                'iso639-1' => 'aa',
                'iso639-2/t' => 'aar',
                'iso639-2/b' => 'aar',
                'iso639-3' => 'aar',
            ],
        'af' =>
            [
                'name' => 'afrikaans',
                'native' => 'afrikaans',
                'iso639-1' => 'af',
                'iso639-2/t' => 'afr',
                'iso639-2/b' => 'afr',
                'iso639-3' => 'afr',
            ],
        'ak' =>
            [
                'name' => 'akan',
                'native' => 'akan',
                'iso639-1' => 'ak',
                'iso639-2/t' => 'aka',
                'iso639-2/b' => 'aka',
                'iso639-3' => 'aka + 2',
            ],
        'sq' =>
            [
                'name' => 'albanian',
                'native' => 'shqip',
                'iso639-1' => 'sq',
                'iso639-2/t' => 'sqi',
                'iso639-2/b' => 'alb',
                'iso639-3' => 'sqi + 4',
            ],
        'am' =>
            [
                'name' => 'amharic',
                'native' => 'አማርኛ',
                'iso639-1' => 'am',
                'iso639-2/t' => 'amh',
                'iso639-2/b' => 'amh',
                'iso639-3' => 'amh',
            ],
        'ar' =>
            [
                'name' => 'arabic',
                'native' => 'العربية',
                'iso639-1' => 'ar',
                'iso639-2/t' => 'ara',
                'iso639-2/b' => 'ara',
                'iso639-3' => 'ara + 30',
            ],
        'an' =>
            [
                'name' => 'aragonese',
                'native' => 'aragonés',
                'iso639-1' => 'an',
                'iso639-2/t' => 'arg',
                'iso639-2/b' => 'arg',
                'iso639-3' => 'arg',
            ],
        'hy' =>
            [
                'name' => 'armenian',
                'native' => 'հայերեն',
                'iso639-1' => 'hy',
                'iso639-2/t' => 'hye',
                'iso639-2/b' => 'arm',
                'iso639-3' => 'hye',
            ],
        'as' =>
            [
                'name' => 'assamese',
                'native' => 'অসমীয়া',
                'iso639-1' => 'as',
                'iso639-2/t' => 'asm',
                'iso639-2/b' => 'asm',
                'iso639-3' => 'asm',
            ],
        'av' =>
            [
                'name' => 'avaric',
                'native' => 'авар мацӏ, магӏарул мацӏ',
                'iso639-1' => 'av',
                'iso639-2/t' => 'ava',
                'iso639-2/b' => 'ava',
                'iso639-3' => 'ava',
            ],
        'ae' =>
            [
                'name' => 'avestan',
                'native' => 'avesta',
                'iso639-1' => 'ae',
                'iso639-2/t' => 'ave',
                'iso639-2/b' => 'ave',
                'iso639-3' => 'ave',
            ],
        'ay' =>
            [
                'name' => 'aymara',
                'native' => 'aymar aru',
                'iso639-1' => 'ay',
                'iso639-2/t' => 'aym',
                'iso639-2/b' => 'aym',
                'iso639-3' => 'aym + 2',
            ],
        'az' =>
            [
                'name' => 'azerbaijani',
                'native' => 'azərbaycan dili',
                'iso639-1' => 'az',
                'iso639-2/t' => 'aze',
                'iso639-2/b' => 'aze',
                'iso639-3' => 'aze + 2',
            ],
        'bm' =>
            [
                'name' => 'bambara',
                'native' => 'bamanankan',
                'iso639-1' => 'bm',
                'iso639-2/t' => 'bam',
                'iso639-2/b' => 'bam',
                'iso639-3' => 'bam',
            ],
        'ba' =>
            [
                'name' => 'bashkir',
                'native' => 'башҡорт теле',
                'iso639-1' => 'ba',
                'iso639-2/t' => 'bak',
                'iso639-2/b' => 'bak',
                'iso639-3' => 'bak',
            ],
        'eu' =>
            [
                'name' => 'basque',
                'native' => 'euskara, euskera',
                'iso639-1' => 'eu',
                'iso639-2/t' => 'eus',
                'iso639-2/b' => 'baq',
                'iso639-3' => 'eus',
            ],
        'be' =>
            [
                'name' => 'belarusian',
                'native' => 'беларуская мова',
                'iso639-1' => 'be',
                'iso639-2/t' => 'bel',
                'iso639-2/b' => 'bel',
                'iso639-3' => 'bel',
            ],
        'bn' =>
            [
                'name' => 'bengali, bangla',
                'native' => 'বাংলা',
                'iso639-1' => 'bn',
                'iso639-2/t' => 'ben',
                'iso639-2/b' => 'ben',
                'iso639-3' => 'ben',
            ],
        'bh' =>
            [
                'name' => 'bihari',
                'native' => 'भोजपुरी',
                'iso639-1' => 'bh',
                'iso639-2/t' => 'bih',
                'iso639-2/b' => 'bih',
                'iso639-3' => 'bih'
            ],
        'bi' =>
            [
                'name' => 'bislama',
                'native' => 'bislama',
                'iso639-1' => 'bi',
                'iso639-2/t' => 'bis',
                'iso639-2/b' => 'bis',
                'iso639-3' => 'bis',
            ],
        'bs' =>
            [
                'name' => 'bosnian',
                'native' => 'bosanski jezik',
                'iso639-1' => 'bs',
                'iso639-2/t' => 'bos',
                'iso639-2/b' => 'bos',
                'iso639-3' => 'bos',
            ],
        'br' =>
            [
                'name' => 'breton',
                'native' => 'brezhoneg',
                'iso639-1' => 'br',
                'iso639-2/t' => 'bre',
                'iso639-2/b' => 'bre',
                'iso639-3' => 'bre',
            ],
        'bg' =>
            [
                'name' => 'bulgarian',
                'native' => 'български език',
                'iso639-1' => 'bg',
                'iso639-2/t' => 'bul',
                'iso639-2/b' => 'bul',
                'iso639-3' => 'bul',
            ],
        'my' =>
            [
                'name' => 'burmese',
                'native' => 'ဗမာစာ',
                'iso639-1' => 'my',
                'iso639-2/t' => 'mya',
                'iso639-2/b' => 'bur',
                'iso639-3' => 'mya',
            ],
        'ca' =>
            [
                'name' => 'catalan, valencian',
                'native' => 'català, valencià',
                'iso639-1' => 'ca',
                'iso639-2/t' => 'cat',
                'iso639-2/b' => 'cat',
                'iso639-3' => 'cat',
            ],
        'ch' =>
            [
                'name' => 'chamorro',
                'native' => 'chamoru',
                'iso639-1' => 'ch',
                'iso639-2/t' => 'cha',
                'iso639-2/b' => 'cha',
                'iso639-3' => 'cha',
            ],
        'ce' =>
            [
                'name' => 'chechen',
                'native' => 'нохчийн мотт',
                'iso639-1' => 'ce',
                'iso639-2/t' => 'che',
                'iso639-2/b' => 'che',
                'iso639-3' => 'che',
            ],
        'ny' =>
            [
                'name' => 'chichewa, chewa, nyanja',
                'native' => 'chicheŵa, chinyanja',
                'iso639-1' => 'ny',
                'iso639-2/t' => 'nya',
                'iso639-2/b' => 'nya',
                'iso639-3' => 'nya',
            ],
        'zh' =>
            [
                'name' => 'chinese',
                'native' => '中文 (zhōngwén), 汉语, 漢語',
                'iso639-1' => 'zh',
                'iso639-2/t' => 'zho',
                'iso639-2/b' => 'chi',
                'iso639-3' => 'zho + 13',
            ],
        'cv' =>
            [
                'name' => 'chuvash',
                'native' => 'чӑваш чӗлхи',
                'iso639-1' => 'cv',
                'iso639-2/t' => 'chv',
                'iso639-2/b' => 'chv',
                'iso639-3' => 'chv',
            ],
        'kw' =>
            [
                'name' => 'cornish',
                'native' => 'kernewek',
                'iso639-1' => 'kw',
                'iso639-2/t' => 'cor',
                'iso639-2/b' => 'cor',
                'iso639-3' => 'cor',
            ],
        'co' =>
            [
                'name' => 'corsican',
                'native' => 'corsu, lingua corsa',
                'iso639-1' => 'co',
                'iso639-2/t' => 'cos',
                'iso639-2/b' => 'cos',
                'iso639-3' => 'cos',
            ],
        'cr' =>
            [
                'name' => 'cree',
                'native' => 'ᓀᐦᐃᔭᐍᐏᐣ',
                'iso639-1' => 'cr',
                'iso639-2/t' => 'cre',
                'iso639-2/b' => 'cre',
                'iso639-3' => 'cre + 6',
            ],
        'hr' =>
            [
                'name' => 'croatian',
                'native' => 'hrvatski jezik',
                'iso639-1' => 'hr',
                'iso639-2/t' => 'hrv',
                'iso639-2/b' => 'hrv',
                'iso639-3' => 'hrv',
            ],
        'cs' =>
            [
                'name' => 'czech',
                'native' => 'čeština, český jazyk',
                'iso639-1' => 'cs',
                'iso639-2/t' => 'ces',
                'iso639-2/b' => 'cze',
                'iso639-3' => 'ces',
            ],
        'da' =>
            [
                'name' => 'danish',
                'native' => 'dansk',
                'iso639-1' => 'da',
                'iso639-2/t' => 'dan',
                'iso639-2/b' => 'dan',
                'iso639-3' => 'dan',
            ],
        'dv' =>
            [
                'name' => 'divehi, dhivehi, maldivian',
                'native' => 'ދިވެހި',
                'iso639-1' => 'dv',
                'iso639-2/t' => 'div',
                'iso639-2/b' => 'div',
                'iso639-3' => 'div',
            ],
        'nl' =>
            [
                'name' => 'dutch',
                'native' => 'nederlands, vlaams',
                'iso639-1' => 'nl',
                'iso639-2/t' => 'nld',
                'iso639-2/b' => 'dut',
                'iso639-3' => 'nld',
            ],
        'dz' =>
            [
                'name' => 'dzongkha',
                'native' => 'རྫོང་ཁ',
                'iso639-1' => 'dz',
                'iso639-2/t' => 'dzo',
                'iso639-2/b' => 'dzo',
                'iso639-3' => 'dzo',
            ],
        'en' =>
            [
                'name' => 'english',
                'native' => 'english',
                'iso639-1' => 'en',
                'iso639-2/t' => 'eng',
                'iso639-2/b' => 'eng',
                'iso639-3' => 'eng',
            ],
        'eo' =>
            [
                'name' => 'esperanto',
                'native' => 'esperanto',
                'iso639-1' => 'eo',
                'iso639-2/t' => 'epo',
                'iso639-2/b' => 'epo',
                'iso639-3' => 'epo',
            ],
        'et' =>
            [
                'name' => 'estonian',
                'native' => 'eesti, eesti keel',
                'iso639-1' => 'et',
                'iso639-2/t' => 'est',
                'iso639-2/b' => 'est',
                'iso639-3' => 'est + 2',
            ],
        'ee' =>
            [
                'name' => 'ewe',
                'native' => 'eʋegbe',
                'iso639-1' => 'ee',
                'iso639-2/t' => 'ewe',
                'iso639-2/b' => 'ewe',
                'iso639-3' => 'ewe',
            ],
        'fo' =>
            [
                'name' => 'faroese',
                'native' => 'føroyskt',
                'iso639-1' => 'fo',
                'iso639-2/t' => 'fao',
                'iso639-2/b' => 'fao',
                'iso639-3' => 'fao',
            ],
        'fj' =>
            [
                'name' => 'fijian',
                'native' => 'vosa vakaviti',
                'iso639-1' => 'fj',
                'iso639-2/t' => 'fij',
                'iso639-2/b' => 'fij',
                'iso639-3' => 'fij',
            ],
        'fi' =>
            [
                'name' => 'finnish',
                'native' => 'suomi, suomen kieli',
                'iso639-1' => 'fi',
                'iso639-2/t' => 'fin',
                'iso639-2/b' => 'fin',
                'iso639-3' => 'fin',
            ],
        'fr' =>
            [
                'name' => 'french',
                'native' => 'français, langue française',
                'iso639-1' => 'fr',
                'iso639-2/t' => 'fra',
                'iso639-2/b' => 'fre',
                'iso639-3' => 'fra',
            ],
        'ff' =>
            [
                'name' => 'fula, fulah, pulaar, pular',
                'native' => 'fulfulde, pulaar, pular',
                'iso639-1' => 'ff',
                'iso639-2/t' => 'ful',
                'iso639-2/b' => 'ful',
                'iso639-3' => 'ful + 9',
            ],
        'gl' =>
            [
                'name' => 'galician',
                'native' => 'galego',
                'iso639-1' => 'gl',
                'iso639-2/t' => 'glg',
                'iso639-2/b' => 'glg',
                'iso639-3' => 'glg',
            ],
        'ka' =>
            [
                'name' => 'georgian',
                'native' => 'ქართული',
                'iso639-1' => 'ka',
                'iso639-2/t' => 'kat',
                'iso639-2/b' => 'geo',
                'iso639-3' => 'kat',
            ],
        'de' =>
            [
                'name' => 'german',
                'native' => 'deutsch',
                'iso639-1' => 'de',
                'iso639-2/t' => 'deu',
                'iso639-2/b' => 'ger',
                'iso639-3' => 'deu',
            ],
        'el' =>
            [
                'name' => 'greek (modern)',
                'native' => 'ελληνικά',
                'iso639-1' => 'el',
                'iso639-2/t' => 'ell',
                'iso639-2/b' => 'gre',
                'iso639-3' => 'ell',
            ],
        'gn' =>
            [
                'name' => 'guaraní',
                'native' => 'avañe\'ẽ',
                'iso639-1' => 'gn',
                'iso639-2/t' => 'grn',
                'iso639-2/b' => 'grn',
                'iso639-3' => 'grn + 5',
            ],
        'gu' =>
            [
                'name' => 'gujarati',
                'native' => 'ગુજરાતી',
                'iso639-1' => 'gu',
                'iso639-2/t' => 'guj',
                'iso639-2/b' => 'guj',
                'iso639-3' => 'guj',
            ],
        'ht' =>
            [
                'name' => 'haitian, haitian creole',
                'native' => 'kreyòl ayisyen',
                'iso639-1' => 'ht',
                'iso639-2/t' => 'hat',
                'iso639-2/b' => 'hat',
                'iso639-3' => 'hat',
            ],
        'ha' =>
            [
                'name' => 'hausa',
                'native' => '(hausa) هَوُسَ',
                'iso639-1' => 'ha',
                'iso639-2/t' => 'hau',
                'iso639-2/b' => 'hau',
                'iso639-3' => 'hau',
            ],
        'he' =>
            [
                'name' => 'hebrew (modern)',
                'native' => 'עברית',
                'iso639-1' => 'he',
                'iso639-2/t' => 'heb',
                'iso639-2/b' => 'heb',
                'iso639-3' => 'heb',
            ],
        'hz' =>
            [
                'name' => 'herero',
                'native' => 'otjiherero',
                'iso639-1' => 'hz',
                'iso639-2/t' => 'her',
                'iso639-2/b' => 'her',
                'iso639-3' => 'her',
            ],
        'hi' =>
            [
                'name' => 'hindi',
                'native' => 'हिन्दी, हिंदी',
                'iso639-1' => 'hi',
                'iso639-2/t' => 'hin',
                'iso639-2/b' => 'hin',
                'iso639-3' => 'hin',
            ],
        'ho' =>
            [
                'name' => 'hiri motu',
                'native' => 'hiri motu',
                'iso639-1' => 'ho',
                'iso639-2/t' => 'hmo',
                'iso639-2/b' => 'hmo',
                'iso639-3' => 'hmo',
            ],
        'hu' =>
            [
                'name' => 'hungarian',
                'native' => 'magyar',
                'iso639-1' => 'hu',
                'iso639-2/t' => 'hun',
                'iso639-2/b' => 'hun',
                'iso639-3' => 'hun',
            ],
        'ia' =>
            [
                'name' => 'interlingua',
                'native' => 'interlingua',
                'iso639-1' => 'ia',
                'iso639-2/t' => 'ina',
                'iso639-2/b' => 'ina',
                'iso639-3' => 'ina',
            ],
        'id' =>
            [
                'name' => 'indonesian',
                'native' => 'bahasa indonesia',
                'iso639-1' => 'id',
                'iso639-2/t' => 'ind',
                'iso639-2/b' => 'ind',
                'iso639-3' => 'ind',
            ],
        'ie' =>
            [
                'name' => 'interlingue',
                'native' => 'originally called occidental; then interlingue after WWII',
                'iso639-1' => 'ie',
                'iso639-2/t' => 'ile',
                'iso639-2/b' => 'ile',
                'iso639-3' => 'ile',
            ],
        'ga' =>
            [
                'name' => 'irish',
                'native' => 'gaeilge',
                'iso639-1' => 'ga',
                'iso639-2/t' => 'gle',
                'iso639-2/b' => 'gle',
                'iso639-3' => 'gle',
            ],
        'ig' =>
            [
                'name' => 'igbo',
                'native' => 'asụsụ igbo',
                'iso639-1' => 'ig',
                'iso639-2/t' => 'ibo',
                'iso639-2/b' => 'ibo',
                'iso639-3' => 'ibo',
            ],
        'ik' =>
            [
                'name' => 'inupiaq',
                'native' => 'iñupiaq, iñupiatun',
                'iso639-1' => 'ik',
                'iso639-2/t' => 'ipk',
                'iso639-2/b' => 'ipk',
                'iso639-3' => 'ipk + 2',
            ],
        'io' =>
            [
                'name' => 'ido',
                'native' => 'ido',
                'iso639-1' => 'io',
                'iso639-2/t' => 'ido',
                'iso639-2/b' => 'ido',
                'iso639-3' => 'ido',
            ],
        'is' =>
            [
                'name' => 'icelandic',
                'native' => 'íslenska',
                'iso639-1' => 'is',
                'iso639-2/t' => 'isl',
                'iso639-2/b' => 'ice',
                'iso639-3' => 'isl',
            ],
        'it' =>
            [
                'name' => 'italian',
                'native' => 'italiano',
                'iso639-1' => 'it',
                'iso639-2/t' => 'ita',
                'iso639-2/b' => 'ita',
                'iso639-3' => 'ita',
            ],
        'iu' =>
            [
                'name' => 'inuktitut',
                'native' => 'ᐃᓄᒃᑎᑐᑦ',
                'iso639-1' => 'iu',
                'iso639-2/t' => 'iku',
                'iso639-2/b' => 'iku',
                'iso639-3' => 'iku + 2',
            ],
        'ja' =>
            [
                'name' => 'japanese',
                'native' => '日本語 (にほんご)',
                'iso639-1' => 'ja',
                'iso639-2/t' => 'jpn',
                'iso639-2/b' => 'jpn',
                'iso639-3' => 'jpn',
            ],
        'jv' =>
            [
                'name' => 'javanese',
                'native' => 'basa jawa',
                'iso639-1' => 'jv',
                'iso639-2/t' => 'jav',
                'iso639-2/b' => 'jav',
                'iso639-3' => 'jav',
            ],
        'kl' =>
            [
                'name' => 'kalaallisut, greenlandic',
                'native' => 'kalaallisut, kalaallit oqaasii',
                'iso639-1' => 'kl',
                'iso639-2/t' => 'kal',
                'iso639-2/b' => 'kal',
                'iso639-3' => 'kal',
            ],
        'kn' =>
            [
                'name' => 'kannada',
                'native' => 'ಕನ್ನಡ',
                'iso639-1' => 'kn',
                'iso639-2/t' => 'kan',
                'iso639-2/b' => 'kan',
                'iso639-3' => 'kan',
            ],
        'kr' =>
            [
                'name' => 'kanuri',
                'native' => 'kanuri',
                'iso639-1' => 'kr',
                'iso639-2/t' => 'kau',
                'iso639-2/b' => 'kau',
                'iso639-3' => 'kau + 3',
            ],
        'ks' =>
            [
                'name' => 'kashmiri',
                'native' => 'कश्मीरी, كشميري‎',
                'iso639-1' => 'ks',
                'iso639-2/t' => 'kas',
                'iso639-2/b' => 'kas',
                'iso639-3' => 'kas',
            ],
        'kk' =>
            [
                'name' => 'kazakh',
                'native' => 'қазақ тілі',
                'iso639-1' => 'kk',
                'iso639-2/t' => 'kaz',
                'iso639-2/b' => 'kaz',
                'iso639-3' => 'kaz',
            ],
        'km' =>
            [
                'name' => 'khmer',
                'native' => 'ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ',
                'iso639-1' => 'km',
                'iso639-2/t' => 'khm',
                'iso639-2/b' => 'khm',
                'iso639-3' => 'khm',
            ],
        'ki' =>
            [
                'name' => 'kikuyu, gikuyu',
                'native' => 'gĩkũyũ',
                'iso639-1' => 'ki',
                'iso639-2/t' => 'kik',
                'iso639-2/b' => 'kik',
                'iso639-3' => 'kik',
            ],
        'rw' =>
            [
                'name' => 'kinyarwanda',
                'native' => 'ikinyarwanda',
                'iso639-1' => 'rw',
                'iso639-2/t' => 'kin',
                'iso639-2/b' => 'kin',
                'iso639-3' => 'kin',
            ],
        'ky' =>
            [
                'name' => 'kyrgyz',
                'native' => 'кыргызча, кыргыз тили',
                'iso639-1' => 'ky',
                'iso639-2/t' => 'kir',
                'iso639-2/b' => 'kir',
                'iso639-3' => 'kir',
            ],
        'kv' =>
            [
                'name' => 'komi',
                'native' => 'коми кыв',
                'iso639-1' => 'kv',
                'iso639-2/t' => 'kom',
                'iso639-2/b' => 'kom',
                'iso639-3' => 'kom + 2',
            ],
        'kg' =>
            [
                'name' => 'kongo',
                'native' => 'kikongo',
                'iso639-1' => 'kg',
                'iso639-2/t' => 'kon',
                'iso639-2/b' => 'kon',
                'iso639-3' => 'kon + 3',
            ],
        'ko' =>
            [
                'name' => 'korean',
                'native' => '한국어, 조선어',
                'iso639-1' => 'ko',
                'iso639-2/t' => 'kor',
                'iso639-2/b' => 'kor',
                'iso639-3' => 'kor',
            ],
        'ku' =>
            [
                'name' => 'kurdish',
                'native' => 'kurdî, كوردی‎',
                'iso639-1' => 'ku',
                'iso639-2/t' => 'kur',
                'iso639-2/b' => 'kur',
                'iso639-3' => 'kur + 3',
            ],
        'kj' =>
            [
                'name' => 'kwanyama, kuanyama',
                'native' => 'kuanyama',
                'iso639-1' => 'kj',
                'iso639-2/t' => 'kua',
                'iso639-2/b' => 'kua',
                'iso639-3' => 'kua',
            ],
        'la' =>
            [
                'name' => 'latin',
                'native' => 'latine, lingua latina',
                'iso639-1' => 'la',
                'iso639-2/t' => 'lat',
                'iso639-2/b' => 'lat',
                'iso639-3' => 'lat',
            ],
        'lb' =>
            [
                'name' => 'luxembourgish, letzeburgesch',
                'native' => 'lëtzebuergesch',
                'iso639-1' => 'lb',
                'iso639-2/t' => 'ltz',
                'iso639-2/b' => 'ltz',
                'iso639-3' => 'ltz',
            ],
        'lg' =>
            [
                'name' => 'ganda',
                'native' => 'luganda',
                'iso639-1' => 'lg',
                'iso639-2/t' => 'lug',
                'iso639-2/b' => 'lug',
                'iso639-3' => 'lug',
            ],
        'li' =>
            [
                'name' => 'limburgish, limburgan, limburger',
                'native' => 'limburgs',
                'iso639-1' => 'li',
                'iso639-2/t' => 'lim',
                'iso639-2/b' => 'lim',
                'iso639-3' => 'lim',
            ],
        'ln' =>
            [
                'name' => 'lingala',
                'native' => 'lingála',
                'iso639-1' => 'ln',
                'iso639-2/t' => 'lin',
                'iso639-2/b' => 'lin',
                'iso639-3' => 'lin',
            ],
        'lo' =>
            [
                'name' => 'lao',
                'native' => 'ພາສາລາວ',
                'iso639-1' => 'lo',
                'iso639-2/t' => 'lao',
                'iso639-2/b' => 'lao',
                'iso639-3' => 'lao',
            ],
        'lt' =>
            [
                'name' => 'lithuanian',
                'native' => 'lietuvių kalba',
                'iso639-1' => 'lt',
                'iso639-2/t' => 'lit',
                'iso639-2/b' => 'lit',
                'iso639-3' => 'lit',
            ],
        'lu' =>
            [
                'name' => 'luba-katanga',
                'native' => 'tshiluba',
                'iso639-1' => 'lu',
                'iso639-2/t' => 'lub',
                'iso639-2/b' => 'lub',
                'iso639-3' => 'lub',
            ],
        'lv' =>
            [
                'name' => 'latvian',
                'native' => 'latviešu valoda',
                'iso639-1' => 'lv',
                'iso639-2/t' => 'lav',
                'iso639-2/b' => 'lav',
                'iso639-3' => 'lav + 2',
            ],
        'gv' =>
            [
                'name' => 'manx',
                'native' => 'gaelg, gailck',
                'iso639-1' => 'gv',
                'iso639-2/t' => 'glv',
                'iso639-2/b' => 'glv',
                'iso639-3' => 'glv',
            ],
        'mk' =>
            [
                'name' => 'macedonian',
                'native' => 'македонски јазик',
                'iso639-1' => 'mk',
                'iso639-2/t' => 'mkd',
                'iso639-2/b' => 'mac',
                'iso639-3' => 'mkd',
            ],
        'mg' =>
            [
                'name' => 'malagasy',
                'native' => 'fiteny malagasy',
                'iso639-1' => 'mg',
                'iso639-2/t' => 'mlg',
                'iso639-2/b' => 'mlg',
                'iso639-3' => 'mlg + 10',
            ],
        'ms' =>
            [
                'name' => 'malay',
                'native' => 'bahasa melayu, بهاس ملايو‎',
                'iso639-1' => 'ms',
                'iso639-2/t' => 'msa',
                'iso639-2/b' => 'may',
                'iso639-3' => 'msa + 13',
            ],
        'ml' =>
            [
                'name' => 'malayalam',
                'native' => 'മലയാളം',
                'iso639-1' => 'ml',
                'iso639-2/t' => 'mal',
                'iso639-2/b' => 'mal',
                'iso639-3' => 'mal',
            ],
        'mt' =>
            [
                'name' => 'maltese',
                'native' => 'malti',
                'iso639-1' => 'mt',
                'iso639-2/t' => 'mlt',
                'iso639-2/b' => 'mlt',
                'iso639-3' => 'mlt',
            ],
        'mi' =>
            [
                'name' => 'māori',
                'native' => 'te reo māori',
                'iso639-1' => 'mi',
                'iso639-2/t' => 'mri',
                'iso639-2/b' => 'mao',
                'iso639-3' => 'mri',
            ],
        'mr' =>
            [
                'name' => 'marathi (marāṭhī)',
                'native' => 'मराठी',
                'iso639-1' => 'mr',
                'iso639-2/t' => 'mar',
                'iso639-2/b' => 'mar',
                'iso639-3' => 'mar',
            ],
        'mh' =>
            [
                'name' => 'marshallese',
                'native' => 'kajin m̧ajeļ',
                'iso639-1' => 'mh',
                'iso639-2/t' => 'mah',
                'iso639-2/b' => 'mah',
                'iso639-3' => 'mah',
            ],
        'mn' =>
            [
                'name' => 'mongolian',
                'native' => 'монгол',
                'iso639-1' => 'mn',
                'iso639-2/t' => 'mon',
                'iso639-2/b' => 'mon',
                'iso639-3' => 'mon + 2',
            ],
        'na' =>
            [
                'name' => 'nauru',
                'native' => 'ekakairũ naoero',
                'iso639-1' => 'na',
                'iso639-2/t' => 'nau',
                'iso639-2/b' => 'nau',
                'iso639-3' => 'nau',
            ],
        'nv' =>
            [
                'name' => 'navajo, navaho',
                'native' => 'diné bizaad, dinékʼehǰí',
                'iso639-1' => 'nv',
                'iso639-2/t' => 'nav',
                'iso639-2/b' => 'nav',
                'iso639-3' => 'nav',
            ],
        'nd' =>
            [
                'name' => 'northern ndebele',
                'native' => 'isindebele',
                'iso639-1' => 'nd',
                'iso639-2/t' => 'nde',
                'iso639-2/b' => 'nde',
                'iso639-3' => 'nde',
            ],
        'ne' =>
            [
                'name' => 'nepali',
                'native' => 'नेपाली',
                'iso639-1' => 'ne',
                'iso639-2/t' => 'nep',
                'iso639-2/b' => 'nep',
                'iso639-3' => 'nep',
            ],
        'ng' =>
            [
                'name' => 'ndonga',
                'native' => 'owambo',
                'iso639-1' => 'ng',
                'iso639-2/t' => 'ndo',
                'iso639-2/b' => 'ndo',
                'iso639-3' => 'ndo',
            ],
        'nb' =>
            [
                'name' => 'norwegian bokmål',
                'native' => 'norsk bokmål',
                'iso639-1' => 'nb',
                'iso639-2/t' => 'nob',
                'iso639-2/b' => 'nob',
                'iso639-3' => 'nob',
            ],
        'nn' =>
            [
                'name' => 'norwegian nynorsk',
                'native' => 'norsk nynorsk',
                'iso639-1' => 'nn',
                'iso639-2/t' => 'nno',
                'iso639-2/b' => 'nno',
                'iso639-3' => 'nno',
            ],
        'no' =>
            [
                'name' => 'norwegian',
                'native' => 'norsk',
                'iso639-1' => 'no',
                'iso639-2/t' => 'nor',
                'iso639-2/b' => 'nor',
                'iso639-3' => 'nor + 2',
            ],
        'ii' =>
            [
                'name' => 'nuosu',
                'native' => 'ꆈꌠ꒿ nuosuhxop',
                'iso639-1' => 'ii',
                'iso639-2/t' => 'iii',
                'iso639-2/b' => 'iii',
                'iso639-3' => 'iii',
            ],
        'nr' =>
            [
                'name' => 'southern ndebele',
                'native' => 'isindebele',
                'iso639-1' => 'nr',
                'iso639-2/t' => 'nbl',
                'iso639-2/b' => 'nbl',
                'iso639-3' => 'nbl',
            ],
        'oc' =>
            [
                'name' => 'occitan',
                'native' => 'occitan, lenga d\'òc',
                'iso639-1' => 'oc',
                'iso639-2/t' => 'oci',
                'iso639-2/b' => 'oci',
                'iso639-3' => 'oci',
            ],
        'oj' =>
            [
                'name' => 'ojibwe, ojibwa',
                'native' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ',
                'iso639-1' => 'oj',
                'iso639-2/t' => 'oji',
                'iso639-2/b' => 'oji',
                'iso639-3' => 'oji + 7',
            ],
        'cu' =>
            [
                'name' => 'old church slavonic, church slavonic, old bulgarian',
                'native' => 'ѩзыкъ словѣньскъ',
                'iso639-1' => 'cu',
                'iso639-2/t' => 'chu',
                'iso639-2/b' => 'chu',
                'iso639-3' => 'chu',
            ],
        'om' =>
            [
                'name' => 'oromo',
                'native' => 'afaan oromoo',
                'iso639-1' => 'om',
                'iso639-2/t' => 'orm',
                'iso639-2/b' => 'orm',
                'iso639-3' => 'orm + 4',
            ],
        'or' =>
            [
                'name' => 'oriya',
                'native' => 'ଓଡ଼ିଆ',
                'iso639-1' => 'or',
                'iso639-2/t' => 'ori',
                'iso639-2/b' => 'ori',
                'iso639-3' => 'ori',
            ],
        'os' =>
            [
                'name' => 'ossetian, ossetic',
                'native' => 'ирон æвзаг',
                'iso639-1' => 'os',
                'iso639-2/t' => 'oss',
                'iso639-2/b' => 'oss',
                'iso639-3' => 'oss',
            ],
        'pa' =>
            [
                'name' => 'panjabi, punjabi',
                'native' => 'ਪੰਜਾਬੀ, پنجابی‎',
                'iso639-1' => 'pa',
                'iso639-2/t' => 'pan',
                'iso639-2/b' => 'pan',
                'iso639-3' => 'pan',
            ],
        'pi' =>
            [
                'name' => 'pāli',
                'native' => 'पाऴि',
                'iso639-1' => 'pi',
                'iso639-2/t' => 'pli',
                'iso639-2/b' => 'pli',
                'iso639-3' => 'pli',
            ],
        'fa' =>
            [
                'name' => 'persian (farsi)',
                'native' => 'فارسی',
                'iso639-1' => 'fa',
                'iso639-2/t' => 'fas',
                'iso639-2/b' => 'per',
                'iso639-3' => 'fas + 2',
            ],
        'pl' =>
            [
                'name' => 'polish',
                'native' => 'język polski, polszczyzna',
                'iso639-1' => 'pl',
                'iso639-2/t' => 'pol',
                'iso639-2/b' => 'pol',
                'iso639-3' => 'pol',
            ],
        'ps' =>
            [
                'name' => 'pashto, pushto',
                'native' => 'پښتو',
                'iso639-1' => 'ps',
                'iso639-2/t' => 'pus',
                'iso639-2/b' => 'pus',
                'iso639-3' => 'pus + 3',
            ],
        'pt' =>
            [
                'name' => 'portuguese',
                'native' => 'português',
                'iso639-1' => 'pt',
                'iso639-2/t' => 'por',
                'iso639-2/b' => 'por',
                'iso639-3' => 'por',
            ],
        'qu' =>
            [
                'name' => 'quechua',
                'native' => 'runa simi, kichwa',
                'iso639-1' => 'qu',
                'iso639-2/t' => 'que',
                'iso639-2/b' => 'que',
                'iso639-3' => 'que + 44',
            ],
        'rm' =>
            [
                'name' => 'romansh',
                'native' => 'rumantsch grischun',
                'iso639-1' => 'rm',
                'iso639-2/t' => 'roh',
                'iso639-2/b' => 'roh',
                'iso639-3' => 'roh',
            ],
        'rn' =>
            [
                'name' => 'kirundi',
                'native' => 'ikirundi',
                'iso639-1' => 'rn',
                'iso639-2/t' => 'run',
                'iso639-2/b' => 'run',
                'iso639-3' => 'run',
            ],
        'ro' =>
            [
                'name' => 'romanian',
                'native' => 'limba română',
                'iso639-1' => 'ro',
                'iso639-2/t' => 'ron',
                'iso639-2/b' => 'rum',
                'iso639-3' => 'ron',
            ],
        'ru' =>
            [
                'name' => 'russian',
                'native' => 'русский язык',
                'iso639-1' => 'ru',
                'iso639-2/t' => 'rus',
                'iso639-2/b' => 'rus',
                'iso639-3' => 'rus',
            ],
        'sa' =>
            [
                'name' => 'sanskrit (saṁskṛta)',
                'native' => 'संस्कृतम्',
                'iso639-1' => 'sa',
                'iso639-2/t' => 'san',
                'iso639-2/b' => 'san',
                'iso639-3' => 'san',
            ],
        'sc' =>
            [
                'name' => 'sardinian',
                'native' => 'sardu',
                'iso639-1' => 'sc',
                'iso639-2/t' => 'srd',
                'iso639-2/b' => 'srd',
                'iso639-3' => 'srd + 4',
            ],
        'sd' =>
            [
                'name' => 'sindhi',
                'native' => 'सिन्धी, سنڌي، سندھی‎',
                'iso639-1' => 'sd',
                'iso639-2/t' => 'snd',
                'iso639-2/b' => 'snd',
                'iso639-3' => 'snd',
            ],
        'se' =>
            [
                'name' => 'northern sami',
                'native' => 'davvisámegiella',
                'iso639-1' => 'se',
                'iso639-2/t' => 'sme',
                'iso639-2/b' => 'sme',
                'iso639-3' => 'sme',
            ],
        'sm' =>
            [
                'name' => 'samoan',
                'native' => 'gagana fa\'a samoa',
                'iso639-1' => 'sm',
                'iso639-2/t' => 'smo',
                'iso639-2/b' => 'smo',
                'iso639-3' => 'smo',
            ],
        'sg' =>
            [
                'name' => 'sango',
                'native' => 'yângâ tî sängö',
                'iso639-1' => 'sg',
                'iso639-2/t' => 'sag',
                'iso639-2/b' => 'sag',
                'iso639-3' => 'sag',
            ],
        'sr' =>
            [
                'name' => 'serbian',
                'native' => 'српски језик',
                'iso639-1' => 'sr',
                'iso639-2/t' => 'srp',
                'iso639-2/b' => 'srp',
                'iso639-3' => 'srp',
            ],
        'gd' =>
            [
                'name' => 'scottish gaelic, gaelic',
                'native' => 'gàidhlig',
                'iso639-1' => 'gd',
                'iso639-2/t' => 'gla',
                'iso639-2/b' => 'gla',
                'iso639-3' => 'gla',
            ],
        'sn' =>
            [
                'name' => 'shona',
                'native' => 'chishona',
                'iso639-1' => 'sn',
                'iso639-2/t' => 'sna',
                'iso639-2/b' => 'sna',
                'iso639-3' => 'sna',
            ],
        'si' =>
            [
                'name' => 'sinhala, sinhalese',
                'native' => 'සිංහල',
                'iso639-1' => 'si',
                'iso639-2/t' => 'sin',
                'iso639-2/b' => 'sin',
                'iso639-3' => 'sin',
            ],
        'sk' =>
            [
                'name' => 'slovak',
                'native' => 'slovenčina, slovenský jazyk',
                'iso639-1' => 'sk',
                'iso639-2/t' => 'slk',
                'iso639-2/b' => 'slo',
                'iso639-3' => 'slk',
            ],
        'sl' =>
            [
                'name' => 'slovene',
                'native' => 'slovenski jezik, slovenščina',
                'iso639-1' => 'sl',
                'iso639-2/t' => 'slv',
                'iso639-2/b' => 'slv',
                'iso639-3' => 'slv',
            ],
        'so' =>
            [
                'name' => 'somali',
                'native' => 'soomaaliga, af soomaali',
                'iso639-1' => 'so',
                'iso639-2/t' => 'som',
                'iso639-2/b' => 'som',
                'iso639-3' => 'som',
            ],
        'st' =>
            [
                'name' => 'southern sotho',
                'native' => 'sesotho',
                'iso639-1' => 'st',
                'iso639-2/t' => 'sot',
                'iso639-2/b' => 'sot',
                'iso639-3' => 'sot',
            ],
        'es' =>
            [
                'name' => 'spanish, castilian',
                'native' => 'español, castellano',
                'iso639-1' => 'es',
                'iso639-2/t' => 'spa',
                'iso639-2/b' => 'spa',
                'iso639-3' => 'spa',
            ],
        'su' =>
            [
                'name' => 'sundanese',
                'native' => 'basa sunda',
                'iso639-1' => 'su',
                'iso639-2/t' => 'sun',
                'iso639-2/b' => 'sun',
                'iso639-3' => 'sun',
            ],
        'sw' =>
            [
                'name' => 'swahili',
                'native' => 'kiswahili',
                'iso639-1' => 'sw',
                'iso639-2/t' => 'swa',
                'iso639-2/b' => 'swa',
                'iso639-3' => 'swa + 2',
            ],
        'ss' =>
            [
                'name' => 'swati',
                'native' => 'siswati',
                'iso639-1' => 'ss',
                'iso639-2/t' => 'ssw',
                'iso639-2/b' => 'ssw',
                'iso639-3' => 'ssw',
            ],
        'sv' =>
            [
                'name' => 'swedish',
                'native' => 'svenska',
                'iso639-1' => 'sv',
                'iso639-2/t' => 'swe',
                'iso639-2/b' => 'swe',
                'iso639-3' => 'swe',
            ],
        'ta' =>
            [
                'name' => 'tamil',
                'native' => 'தமிழ்',
                'iso639-1' => 'ta',
                'iso639-2/t' => 'tam',
                'iso639-2/b' => 'tam',
                'iso639-3' => 'tam',
            ],
        'te' =>
            [
                'name' => 'telugu',
                'native' => 'తెలుగు',
                'iso639-1' => 'te',
                'iso639-2/t' => 'tel',
                'iso639-2/b' => 'tel',
                'iso639-3' => 'tel',
            ],
        'tg' =>
            [
                'name' => 'tajik',
                'native' => 'тоҷикӣ, toğikī, تاجیکی‎',
                'iso639-1' => 'tg',
                'iso639-2/t' => 'tgk',
                'iso639-2/b' => 'tgk',
                'iso639-3' => 'tgk',
            ],
        'th' =>
            [
                'name' => 'thai',
                'native' => 'ไทย',
                'iso639-1' => 'th',
                'iso639-2/t' => 'tha',
                'iso639-2/b' => 'tha',
                'iso639-3' => 'tha',
            ],
        'ti' =>
            [
                'name' => 'tigrinya',
                'native' => 'ትግርኛ',
                'iso639-1' => 'ti',
                'iso639-2/t' => 'tir',
                'iso639-2/b' => 'tir',
                'iso639-3' => 'tir',
            ],
        'bo' =>
            [
                'name' => 'tibetan standard, tibetan, central',
                'native' => 'བོད་ཡིག',
                'iso639-1' => 'bo',
                'iso639-2/t' => 'bod',
                'iso639-2/b' => 'tib',
                'iso639-3' => 'bod',
            ],
        'tk' =>
            [
                'name' => 'turkmen',
                'native' => 'türkmen, түркмен',
                'iso639-1' => 'tk',
                'iso639-2/t' => 'tuk',
                'iso639-2/b' => 'tuk',
                'iso639-3' => 'tuk',
            ],
        'tl' =>
            [
                'name' => 'tagalog',
                'native' => 'wikang tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔',
                'iso639-1' => 'tl',
                'iso639-2/t' => 'tgl',
                'iso639-2/b' => 'tgl',
                'iso639-3' => 'tgl',
            ],
        'tn' =>
            [
                'name' => 'tswana',
                'native' => 'setswana',
                'iso639-1' => 'tn',
                'iso639-2/t' => 'tsn',
                'iso639-2/b' => 'tsn',
                'iso639-3' => 'tsn',
            ],
        'to' =>
            [
                'name' => 'tonga', // tonga islands
                'native' => 'faka tonga',
                'iso639-1' => 'to',
                'iso639-2/t' => 'ton',
                'iso639-2/b' => 'ton',
                'iso639-3' => 'ton',
            ],
        'tr' =>
            [
                'name' => 'turkish',
                'native' => 'türkçe',
                'iso639-1' => 'tr',
                'iso639-2/t' => 'tur',
                'iso639-2/b' => 'tur',
                'iso639-3' => 'tur',
            ],
        'ts' =>
            [
                'name' => 'tsonga',
                'native' => 'xitsonga',
                'iso639-1' => 'ts',
                'iso639-2/t' => 'tso',
                'iso639-2/b' => 'tso',
                'iso639-3' => 'tso',
            ],
        'tt' =>
            [
                'name' => 'tatar',
                'native' => 'татар теле, tatar tele',
                'iso639-1' => 'tt',
                'iso639-2/t' => 'tat',
                'iso639-2/b' => 'tat',
                'iso639-3' => 'tat',
            ],
        'tw' =>
            [
                'name' => 'twi',
                'native' => 'twi',
                'iso639-1' => 'tw',
                'iso639-2/t' => 'twi',
                'iso639-2/b' => 'twi',
                'iso639-3' => 'twi',
            ],
        'ty' =>
            [
                'name' => 'tahitian',
                'native' => 'reo tahiti',
                'iso639-1' => 'ty',
                'iso639-2/t' => 'tah',
                'iso639-2/b' => 'tah',
                'iso639-3' => 'tah',
            ],
        'ug' =>
            [
                'name' => 'uyghur, uighur',
                'native' => 'uyƣurqə, ئۇيغۇرچە‎',
                'iso639-1' => 'ug',
                'iso639-2/t' => 'uig',
                'iso639-2/b' => 'uig',
                'iso639-3' => 'uig',
            ],
        'uk' =>
            [
                'name' => 'ukrainian',
                'native' => 'українська мова',
                'iso639-1' => 'uk',
                'iso639-2/t' => 'ukr',
                'iso639-2/b' => 'ukr',
                'iso639-3' => 'ukr',
            ],
        'ur' =>
            [
                'name' => 'urdu',
                'native' => 'اردو',
                'iso639-1' => 'ur',
                'iso639-2/t' => 'urd',
                'iso639-2/b' => 'urd',
                'iso639-3' => 'urd',
            ],
        'uz' =>
            [
                'name' => 'uzbek',
                'native' => 'o‘zbek, ўзбек, أۇزبېك‎',
                'iso639-1' => 'uz',
                'iso639-2/t' => 'uzb',
                'iso639-2/b' => 'uzb',
                'iso639-3' => 'uzb + 2',
            ],
        've' =>
            [
                'name' => 'venda',
                'native' => 'tshivenḓa',
                'iso639-1' => 've',
                'iso639-2/t' => 'ven',
                'iso639-2/b' => 'ven',
                'iso639-3' => 'ven',
            ],
        'vi' =>
            [
                'name' => 'vietnamese',
                'native' => 'tiếng việt',
                'iso639-1' => 'vi',
                'iso639-2/t' => 'vie',
                'iso639-2/b' => 'vie',
                'iso639-3' => 'vie',
            ],
        'vo' =>
            [
                'name' => 'volapük',
                'native' => 'volapük',
                'iso639-1' => 'vo',
                'iso639-2/t' => 'vol',
                'iso639-2/b' => 'vol',
                'iso639-3' => 'vol',
            ],
        'wa' =>
            [
                'name' => 'walloon',
                'native' => 'walon',
                'iso639-1' => 'wa',
                'iso639-2/t' => 'wln',
                'iso639-2/b' => 'wln',
                'iso639-3' => 'wln',
            ],
        'cy' =>
            [
                'name' => 'welsh',
                'native' => 'cymraeg',
                'iso639-1' => 'cy',
                'iso639-2/t' => 'cym',
                'iso639-2/b' => 'wel',
                'iso639-3' => 'cym',
            ],
        'wo' =>
            [
                'name' => 'wolof',
                'native' => 'wollof',
                'iso639-1' => 'wo',
                'iso639-2/t' => 'wol',
                'iso639-2/b' => 'wol',
                'iso639-3' => 'wol',
            ],
        'fy' =>
            [
                'name' => 'western frisian',
                'native' => 'frysk',
                'iso639-1' => 'fy',
                'iso639-2/t' => 'fry',
                'iso639-2/b' => 'fry',
                'iso639-3' => 'fry',
            ],
        'xh' =>
            [
                'name' => 'xhosa',
                'native' => 'isixhosa',
                'iso639-1' => 'xh',
                'iso639-2/t' => 'xho',
                'iso639-2/b' => 'xho',
                'iso639-3' => 'xho',
            ],
        'yi' =>
            [
                'name' => 'yiddish',
                'native' => 'ייִדיש',
                'iso639-1' => 'yi',
                'iso639-2/t' => 'yid',
                'iso639-2/b' => 'yid',
                'iso639-3' => 'yid + 2',
            ],
        'yo' =>
            [
                'name' => 'yoruba',
                'native' => 'yorùbá',
                'iso639-1' => 'yo',
                'iso639-2/t' => 'yor',
                'iso639-2/b' => 'yor',
                'iso639-3' => 'yor',
            ],
        'za' =>
            [
                'name' => 'zhuang, chuang',
                'native' => 'saɯ cueŋƅ, saw cuengh',
                'iso639-1' => 'za',
                'iso639-2/t' => 'zha',
                'iso639-2/b' => 'zha',
                'iso639-3' => 'zha + 16',
            ],
        'zu' =>
            [
                'name' => 'zulu',
                'native' => 'isizulu',
                'iso639-1' => 'zu',
                'iso639-2/t' => 'zul',
                'iso639-2/b' => 'zul',
                'iso639-3' => 'zul',
            ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'LanguageCode';
    }

    /**
     * {@inheritdoc}
     */
    public function convert($value)
    {
        if (!is_string($value)) {
            return null;
        }
        // Search $value
        $index = false;
        foreach ($this->languageCode as $key => $langs) {
            foreach ($langs as $lang) {
                if ($value === $lang) {
                    $index = $key;
                    break 2;
                }
            }
        }
        if (!$index) {
            return null;
        }
        /** @var $opts LanguageCodeOptions */
        $opts = $this->getOptions();

        return $this->languageCode[$index][$opts->getOutput()];
    }
}
