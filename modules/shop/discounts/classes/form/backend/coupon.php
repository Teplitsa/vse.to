<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Coupon extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // HTML class
        $this->attribute('class', 'w600px');
        $this->layout = 'wide';

        // ----- code
        $element = new Form_Element_Input('code',
            array('label' => 'Код купона', 'required' => TRUE),
            array('maxlength' => 8)
        );
        $element
            ->add_filter(new Form_Filter_TrimCrop(8))
            ->add_validator(new Form_Validator_StringLength(8, 8))
            ->add_validator(new Form_Validator_Alnum(NULL, TRUE, FALSE, FALSE));
        $this->add_component($element);

        // ----- discount
        $element = new Form_Element_Float('discount',
            array('label' => 'Скидка', 'required' => TRUE, 'layout' => 'wide')
        );
        $element
            ->add_filter(new Form_Filter_Trim())
            ->add_validator(new Form_Validator_Float(0, NULL, FALSE));
        $this->add_component($element);

        $e = new Form_Element_RadioSelect('discount_type',
            array('percent' => '%', 'sum' => 'руб'),
            array('label' => '', 'render' => FALSE, 'layout' => 'inline')
        );
        $this->add_component($e);

        $element->append = $e->render();

        // ----- Valid date range
        $fieldset = new Form_Fieldset('valid',
            array('label' => 'Срок действия', 'required' => TRUE, 'layout' => 'wide')
        );
        $fieldset->config_entry = 'fieldset_inline';
        $this->add_component($fieldset);

            // ----- valid_after
            $element = new Form_Element_SimpleDate('date_from', array('label' => '', 'layout' => 'fieldset_inline'));
            $element->value_format = Model_Coupon::$date_as_timestamp ? 'timestamp' : 'date';
            $element
                ->add_filter(new Form_Filter_Date())
                ->add_validator(new Form_Validator_Date());            
            $element->append = ' - ';
            $fieldset->add_component($element);

            // ----- valid_before
            $element = new Form_Element_SimpleDate('date_to', array('label' => '', 'layout' => 'fieldset_inline'));
            $element->value_format = Model_Coupon::$date_as_timestamp ? 'timestamp' : 'date';
            $element
                ->add_filter(new Form_Filter_Date())
                ->add_validator(new Form_Validator_Date());
            $fieldset->add_component($element);


        // ----- multiple
        $options = array(0 => 'один раз', 1 => 'не ограничено');

        $element = new Form_Element_RadioSelect('multiple', $options,  array('label' => 'Количество использований'));
        $element
            ->add_validator(new Form_Validator_InArray(array_keys($options)));
        $this->add_component($element);

        // ----- User_id
        // Store user id in hidden field
        $element = new Form_Element_Hidden('user_id');
        $this->add_component($element);

        if ($element->value !== FALSE)
        {
            $user_id = (int) $element->value;
        }
        else
        {
            $user_id = (int) $this->model()->user_id;
        }

        // ----- User name
        // User for this order
        $user = new Model_User();
        $user->find($user_id);
        
        $user_name = ($user->id !== NULL) ? $user->name : '--- для всех пользователей ---';

        $element = new Form_Element_Input('user_name',
                array('label' => 'Пользователь', 'disabled' => TRUE, 'layout' => 'wide'),
                array('class' => 'w250px')
        );
        $element->value = $user_name;
        $this->add_component($element);

        // Button to select user
        $button = new Form_Element_LinkButton('select_user_button',
                array('label' => 'Выбрать', 'render' => FALSE),
                array('class' => 'button_select_user open_window')
        );
        $button->url   = URL::to('backend/acl', array('action' => 'user_select'), TRUE);
        $this->add_component($button);

        $element->append = '&nbsp;&nbsp;' . $button->render();

        // ----- sites
        $sites = Model::fly('Model_Site')->find_all();

        $options = array();
        foreach ($sites as $site)
        {
            $options[$site->id] = $site->caption;
        }

        $element = new Form_Element_CheckSelect('sites', $options,  array('label' => 'Магазины'));
        $element
            ->add_validator(new Form_Validator_CheckSelect(array_keys($options)));
        $this->add_component($element);

        // ----- description
        $element = new Form_Element_Textarea('description', array('label' => 'Описание'));
        $element->add_filter(new Form_Filter_Crop(1024));
        $this->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => 'Отменить'),
                    array('class' => 'button_cancel')
                ));

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));
    }
}
