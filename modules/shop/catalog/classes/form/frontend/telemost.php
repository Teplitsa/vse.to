<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Telemost extends Form_Frontend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Set HTML class
         $this->view_script = 'frontend/forms/telemost';
        
        $userTownId = Model_User::current()->town->id;
        $places_arr = array();
         if ($userTownId) {
            $places = Model::fly('Model_Place')->find_all_by_town_id(Model_User::current()->town->id);
            foreach ($places as $place) {
                $places_arr[$place->id] = $place->name;
            }
         }
         
         
        $element = new Form_Element_Select('place_id',
            $places_arr,
            array('label' => 'Площадка','required' => TRUE),
            array('placeholder' => 'Выберите площадку')
        );
        
        $element->add_validator(new Form_Validator_InArray(array_keys($places_arr)));
        $this->add_component($element);        
       
        // control hidden field
        $control_element = new Form_Element_Hidden('product_alias');
        $control_element->value = $this->model()->alias;
        
        $this->add_component($control_element);
        
        $button_text = 'Отправить заявку';
        if ($this->model()->price->gt(new Money())) {
            // ----- Сумма
            $price_elem = new Form_Element_Text('price', array('label' => 'Стоимость лицензии'),array('placeholder' => "Стоимость лицензии"));
            $price_elem->value = $this->model()->price;
            $this->add_component($price_elem);
            // ----- Telephone
            $phone_elem = new Form_Element_Input('telephone', array('label' => 'Номер телефона пользователя Visa QIWI Wallet'),array('placeholder' => "Номер телефона пользователя Visa QIWI Wallet"));
            $phone_elem
                ->add_validator(new Form_Validator_Integer())
                ->add_validator(new Form_Validator_NotEmptyString());
            $this->add_component($phone_elem);
            
            $button_text = 'Оплатить лицензию';
        }
        
        // ----- Description
        $this->add_component(new Form_Element_Textarea('info', array('label' => 'Дополнительная информация'),array('placeholder' => "Дополнтельная информация")));        
        
        // ----- Form buttons
        $button = new Form_Element_Button('submit_request',
                array('label' => $button_text),
                array('class' => 'button button-modal')
        );
        $this->add_component($button);        

    }
    /**
     * Add javascripts
     */
    public function render_js()
    {
        parent::render_js();
        // ----- Install javascripts
        

        Layout::instance()->add_script(Modules::uri('catalog') . '/public/js/frontend/telemostplace.js');        
        
    }    
}
