<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_SearchProducts extends Form_Backend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
        $this->attribute('class', "w500px");
        $this->layout = 'wide';

        // ----- search_text
        $element = new Form_Element_Input('search_text',
            array('label' => 'Поиск'),
            array('maxlength' => 255, 'class' => 'w200px')
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(255));
        $this->add_component($element);

        // ----- Submit button
        $button = new Form_Backend_Element_Submit('submit',
            array('label' => 'Найти', 'render' => FALSE),
            array('class' => 'button_accept')
        );
        $this->add_component($button);

        $element->append = '&nbsp;' . $button->render();

        // ----- advanced search options
        $fieldset = new Form_Fieldset('advanced', array('label' => 'Расширенный поиск'));
        $this->add_component($fieldset);

            // ----- active
            $options = array(-1 => 'не важно', 1 => 'да', 0 => 'нет');
            $element = new Form_Element_RadioSelect('active', $options,
                array('label' => 'Активный:')
            );
            $element->default_value = -1;
            $element
                ->add_validator(new Form_Validator_InArray(array_keys($options)));
            $element->config_entry = 'checkselect_inline';
            $fieldset->add_component($element);

            // ----- presence in product lists
            /*
            $fieldset2 = new Form_Fieldset('plists_fieldset', array('label' => 'Наличие в списках товаров'), array('class' => 'lb200px'));
            $fieldset->add_component($fieldset2);

                $options = array(-1 => 'не важно', 1 => 'да', 0 => 'нет');

                $plists = Model::fly('Model_PList')->find_all_by_site_id(Model_Site::current()->id,
                    array('columns' => array('id', 'caption'), 'order_by' => 'id', 'desc' => FALSE)
                );
                foreach ($plists as $plist)
                {
                    $element = new Form_Element_RadioSelect('plists[' . $plist->id . ']', $options,
                        array('label' => $plist->caption)
                    );
                    $element->default_value = -1;
                    $element
                        ->add_validator(new Form_Validator_InArray(array_keys($options)));
                    $element->config_entry = 'checkselect_inline';
                    $fieldset2->add_component($element);
                }
             * 
             */
    }    
}
