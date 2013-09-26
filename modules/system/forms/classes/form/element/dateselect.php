<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form date element.
 * Rendered as three input elements for day, month and year
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_DateSelect extends Form_Element_Date {

    /**
     * Standart template to render element as table row
     *
     * @param string $type
     */
    public function default_template($type = 'element')
    {
        switch ($type)
        {
            case 'element':
                return
                    '<tr>'
                  . '   <td class="label">$(label)</td>'
                  . '   <td class="date">$(input)$(comment)</td>'
                  . '   <td class="errors">$(errors)</td>'
                  . '</tr>';

            default:
                return parent::default_template($type);
        }
    }

    /**
     * Renders date element as three selects for day, month and year
     *
     * @return string
     */
    public function render_input()
    {
        // Highlight inputs if there are errors
        if ($this->has_errors()) {
            $class = ' error';
        } else {
            $class = '';
        }

        $days   = array();
        $months = array();
        $years  = array();

        $year_max = (int)date('Y');

        for ($i = 1; $i < 32; $days[$i] = $i, $i++);
        for ($i = 1; $i < 13; $months[$i] = $i, $i++);
        for ($i = $year_max; $i > $year_max - 100; $years[$i] = $i, $i--);
        return
            Form_Helper::select(
                $this->name.'[]',
                $days,
                $this->get_day(),
                array(
                    'type' => 'text',
                    'class'=>'text day' . $class,
                    'maxlength' => 2
                )
            )
          . Form_Helper::select(
                $this->name.'[]',
                $months,
                $this->get_month(),
                array(
                    'type' => 'text',
                    'class'=>'text month' . $class,
                    'maxlength' => 2
                )
            )
          . Form_Helper::select(
                $this->name.'[]',
                $years,
                $this->get_year(),
                array(
                    'type' => 'text',
                    'class'=>'text year' . $class,
                    'maxlength' => 4
                )
            );
    }
}
