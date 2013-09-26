<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Filter datetime values
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Filter_DateTimeSimple extends Form_Filter
{
    /**
     * @param string $value
     */
    public function filter($value)
    {
        $value = trim((string) $value);
        
        $format = $this->get_form_element()->format;
        if ($value != '' && $format !== NULL)
        {
            // Extract special characters form format - we are interested in order in which
            // these characters appear
            if (preg_match_all('/(Y|m|d|H|i|s)/', $format, $special_chars))
            {
                $special_chars = array_values($special_chars[0]);

                // Extract numbers from value
                $values = preg_split('/[^\d]+/i', $value, NULL, PREG_SPLIT_NO_EMPTY);

                $day    = '00';
                $month  = '00';
                $year   = '0000';
                $second = '00';
                $minute = '00';
                $hour   = '00';

                // Ther order of numbers is expected to be the same, as the order of special chars
                foreach ($special_chars as $i => $special_char)
                {
                    if ( ! isset($values[$i]))
                        break;

                    switch ($special_char)
                    {
                        case 'd':
                            $day = (int) $values[$i];
                            $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                            break;

                        case 'm':
                            $month = (int) $values[$i];
                            $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                            break;

                        case 'Y':
                            $year = (int) $values[$i];
                            if ($year < 100)
                            {
                                $year = $year + ($year < 70 ? 2000 : 1900);
                            }
                            $year = str_pad($year, 4, '0', STR_PAD_LEFT);
                            break;
                            
                        case 's':
                            $second = (int) $values[$i];
                            $second = str_pad($second, 2, '0', STR_PAD_LEFT);
                            break;

                        case 'i':
                            $minute = (int) $values[$i];
                            $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);

                        case 'H':
                            $hour = (int) $values[$i];
                            $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                    }
                }
                
                $value = str_replace(
                    array('d', 'm', 'Y', 's', 'i', 'H'),
                    array($day, $month, $year, $second, $minute, $hour),
                    $format
                );
            }
        }

        return $value;
    }
}