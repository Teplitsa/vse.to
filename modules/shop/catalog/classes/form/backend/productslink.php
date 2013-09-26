<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_ProductsLink extends Form_Backend
{
    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "lb100px w500px");
        $this->layout = 'wide';


        $options = array(
            'link' => 'Привязать',
            'link_main' => 'Привязать как основной',
            'unlink' => 'Отвязать'
        );
        $element = new Form_Element_RadioSelect('mode', $options, array('label' => 'Действие'));
        $element->default_value = 'link';
        $this->add_component($element);

        // ----- Section_id        
        $sectiongroups = Model::fly('Model_SectionGroup')->find_all_by_site_id(Model_Site::current()->id, array('columns' => array('id', 'caption')));

        foreach ($sectiongroups as $sectiongroup)
        {
            $sections = Model::fly('Model_Section')->find_all_by_sectiongroup_id($sectiongroup->id, array(
                'order_by' => 'lft',
                'desc' => FALSE,
                'columns' => array('id', 'rgt', 'lft', 'level', 'caption')
            ));
            
            $options = array(0 => '---');
            foreach ($sections as $section)
            {
                $options[$section->id] = str_repeat('&nbsp;', ($section->level - 1) * 3) . Text::limit_chars($section->caption, 30);
            }

            $element = new Form_Element_Select('section_ids[' . $sectiongroup->id . ']', $options, array('label' => $sectiongroup->caption, 'required' => TRUE));
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options), array(
                    Form_Validator_InArray::NOT_FOUND => 'Вы не указали ' . $sectiongroup->caption
                )));
            $this->add_component($element);
        }

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => 'Назад'),
                    array('class' => 'button_cancel')
                ));

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Выполнить'),
                    array('class' => 'button_accept')
                ));

    }
}
