<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Locale info
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class l10n {

    /**
     * Locale information cache
     * @var array
     */
    protected static $_locale_info;

    /**
     * Obtain locale information
     *
     * @param string $field
     * @return string
     */
    protected static function get_locale_info($field)
    {
        if (self::$_locale_info === NULL)
        {
            self::$_locale_info = localeconv();
        }

        if (isset(self::$_locale_info[$field]))
        {
            return self::$_locale_info[$field];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Return decimal point character for current locale
     *
     * @return string
     */
    public static function decimal_point()
    {
        return self::get_locale_info('decimal_point');
    }

    /**
     * Make a correct plural form of word for given number
     *
     * @param  integer $number
     * @param  string $form1    1   'Товар'     1   'Рубль'
     * @param  string $form2    10  'Товаров'   10  'Рублей'
     * @param  string $form3    2   'Товара'    2   'Рубля'
     * @return string
     */
    public static function plural($number, $form1, $form2 = NULL, $form3 = NULL)
    {
        if ( ! isset($form2)) $form2 = $form1;
        if ( ! isset($form3)) $form3 = $form1;

        
        if ($number >= 11 && $number <= 19)
            return $form2;

        $last_digit = (int) $number % 10;

        if ($last_digit == 1)
            return $form1;

        if ($last_digit >= 2 && $last_digit <= 4)
            return $form3;

        return $form2;
    }

    /**
     * Convert a string to floating point
     * @TODO: more carefully handle locale-specific formatting (such as decimal and thousands separators)
     *
     * @param  string $value
     * @return float
     */
    public static function string_to_float($value)
    {
        if (is_int($value) || is_double($value))
            return $value;

        return (float) str_replace(array('.', ','), '.', (string) $value);
    }
    /**
     * Format a time/date
     * 
     * @param  string $format Formatted like for the strftime() function
     * @param  integer $timestamp
     * @return string
     */
    public static function date($format, $timestamp)
    {
        return strftime($format, $timestamp);
    }

    public static function rdate($param, $time=0) {
            if(intval($time)==0)$time=time();
            $MonthNames=array("Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
            $DayNames=array("Воскресение","Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота");

            if(strpos($param,'D')) {
                $param = str_replace('D',$DayNames[date('w',$time)],$param);            
            }
            if(strpos($param,'M')) {
                $param = str_replace('M',$MonthNames[date('n',$time)-1],$param);            
            }
            return date($param, $time);
    }    
    
    /**
     * Parse the given date and return the unix timestamp
     * The format of the date is specified in $format
     * Default values for time components, that are not present in format, can be specified
     * If not specified, the values defaults to the current time ($hour = time('H'), $minute = time('i'), ...)
     *
     * @param  string $format Formatted like for the strftime() function
     * @param  string $date
     * @param  int $hour
     * @param  int $minute
     * @param  int $second
     * @param  int $month
     * @param  int $day
     * @param  int $year
     *
     * @return integer|boolean The desired timestamp or FALSE on failure
     */
    public static function timestamp(
        $format, $date,
        $hour = NULL, $minute = NULL, $second = NULL, $month = NULL, $day = NULL, $year = NULL)
    {
        $regexp = self::date_format_regexp($format);

        if ( ! preg_match($regexp, $date, $matches))
            return FALSE;

        extract($matches);

        return mktime($hour, $minute, $second, $month, $day, $year);
    }
    /**
     * Convert date string from one format to another
     * @todo: a lot, a lot ...
     * 
     * @param  string $datetime
     * @param  string $format_from
     * @param  string $format_to
     * @return string
     */
    public static function datetime_convert($datetime, $format_from, $format_to)
    {
        $regexp = self::date_format_regexp($format_from);

        if ( ! preg_match($regexp, $datetime, $matches))
            return FALSE;

        extract($matches);

        if ( ! isset($hour))   $hour   = '00';
        if ( ! isset($minute)) $minute = '00';
        if ( ! isset($second)) $second = '00';
        if ( ! isset($month))  $month  = '00';
        if ( ! isset($day))    $day    = '00';
        if ( ! isset($year))   $year   = '0000';

        return str_replace(
            array('H',   'i',     's',     'm',    'd',  'Y'),
            array($hour, $minute, $second, $month, $day, $year),
            $format_to
        );
    }

    /**
     * Return localized version of date format
     * 
     * @param string $format Formatted like for strftime() function
     */
    public static function translate_datetime_format($format)
    {
        return str_replace(
            array('Y', 'm', 'd', 'H', 'i', 's'),
            array('гггг', 'мм', 'дд', 'чч', 'мм', 'сс'),
            $format
        );
    }

    /**
     * Build a regexp to parse date format
     * Format is expected to have special characters just like in strftime() function
     * @todo: excape more regexp special characters in format
     * 
     * @param  string $format
     * @return string
     */
    public static function date_format_regexp($format)
    {
        $regexp = strtr($format, array(
            'H' => '(?<hour>\d{2})',
            'i' => '(?<minute>\d{2})',
            's' => '(?<second>\d{2})',
            'd' => '(?<day>\d{2})',
            'm' => '(?<month>\d{2})',
            'Y' => '(?<year>\d{4})'
        ));

        $regexp = '/^\s*' . $regexp . '\s*$/i';

        return $regexp;
    }

    /**
     * @param  string $string
     * @return string
     */
    public static function transliterate($string)
    {
        return strtr($string, array(
            'а' => 'a', 'А' => 'A',
            'б' => 'b', 'Б' => 'B',
            'в' => 'v', 'В' => 'V',
            'г' => 'g', 'Г' => 'G',
            'д' => 'd', 'Д' => 'D',
            'е' => 'e', 'Е' => 'E',
            'ё' => 'jo', 'Ё' => 'JO',
            'ж' => 'zh', 'Ж' => 'ZH',
            'з' => 'z', 'З' => 'Z',
            'и' => 'i', 'И' => 'I',
            'й' => 'j', 'Й' => 'J',
            'к' => 'k', 'К' => 'K',
            'л' => 'l', 'Л' => 'L',
            'м' => 'm', 'М' => 'M',
            'н' => 'n', 'Н' => 'N',
            'о' => 'o', 'О' => 'O',
            'п' => 'p', 'П' => 'P',
            'р' => 'r', 'Р' => 'R',
            'с' => 's', 'С' => 'S',
            'т' => 't', 'Т' => 'T',
            'у' => 'u', 'У' => 'U',
            'ф' => 'f', 'Ф' => 'F',
            'х' => 'h', 'Х' => 'H',
            'ц' => 'c', 'Ц' => 'C',
            'ч' => 'ch', 'Ч' => 'CH',
            'ш' => 'sh', 'Ш' => 'SH',
            'щ' => 'w', 'Щ' => 'W',
            'ь' => '\'', 'Ь' => '\'',
            'ы' => 'y', 'Ы' => 'У',
            'ъ' => '#', 'Ъ' => '#',
            'э' => 'je', 'Э' => 'JE',
            'ю' => 'ju', 'Ю' => 'JU',
            'я' => 'ja', 'Я' => 'JA'
        ));
    }
}