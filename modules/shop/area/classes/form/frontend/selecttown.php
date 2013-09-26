<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_SelectTown extends Form_Frontend {

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Render using view
        $this->view_script = 'frontend/forms/selecttown';
        
        // ----- town                
        $towns = Model_Town::towns();
        $element = new Form_Element_Select('name',
            $towns,
            array('label' => '','layout'=>'wide')    
        );
                
        $this->add_component($element);
        
        $button = new Form_Frontend_Element_Submit('submit',
                array('label' => 'Выбрать'),
                array('class' => 'button_select')
        );
        $this->add_component($button);        
    }
}
