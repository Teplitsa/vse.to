<?php defined('SYSPATH') or die('No direct script access.');

class Form_BackendRes extends Form_Backend
{
    /**
     *@todo 
     * 
     * This definition copy the definition in Controller_BackendRES
     */
    protected $_role_class = 'Model_User';
    protected $_user_id = 'user_id';
    
    /**
     * Add javascripts
     */
    public function render_js()
    {
        parent::render_js();
        // ----- Install javascripts
        $js =
            "function on_user_select(user){"
        .   " var f = jforms['$this->name'];"
        .   " if (user['id'])"
        .   "{ f.get_element('$this->_user_id').set_value(user['id']);"
        .   "f.get_element('user_name').set_value(user['user_name']);}"
        .   "else { f.get_element('$this->_user_id').set_value(0);"
        .   "f.get_element('user_name').set_value('');}}";
        $layout = Layout::instance();

        $layout->add_script($js, TRUE);
        
        $script = '';
        $component = $this->find_component('access_towns');
        if ($component !== FALSE)
        {
            $script .= "var towns_fieldset_ids='" . $component->id . "';\n";
        }        

        $component = $this->find_component('access_organizers');
        if ($component !== FALSE)
        {
            $script .= "var organizers_fieldset_ids='" . $component->id . "';\n";
        } 

        $component = $this->find_component('access_users');
        if ($component !== FALSE)
        {
            $script .= "var users_fieldset_ids='" . $component->id . "';\n";
        }
        
        $layout->add_script($script, TRUE);

        Layout::instance()->add_script(Modules::uri('area') . '/public/js/backend/towns_form.js');
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/backend/organizers_form.js');
        Layout::instance()->add_script(Modules::uri('acl') . '/public/js/backend/users_form.js');
        
        //Layout::instance()->add_script(Modules::uri('backend') . '/public/js/roleuser.js');        
        
    } 
}