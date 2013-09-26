<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A custom form element. Element value is used as element contnet
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Custom extends Form_Element
{
    /**
     * Use element value as it's contents
     *
     * @return string
     */
    public function render_input()
    {
        return $this->value;
    }
}
