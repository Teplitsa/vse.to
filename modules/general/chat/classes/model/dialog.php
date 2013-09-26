<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dialog extends Model
{
    
    public function get_opponent() {
        $current_user = Auth::instance()->get_user();
        $opponent_id = ($current_user->id == $this->sender_id) ?$this->receiver_id : $this->sender_id;        
        return Model::fly('Model_User')->find($opponent_id);
    }
    
    /**
     * Get frontend uri to the product
     */
    public function uri_frontend()
    {
        static $uri_template;

        if ($uri_template === NULL)
        {
            $uri_template = URL::uri_to('frontend/messages', array(
                'dialog_id'    =>  $this->id 
            ));
        }

        return $uri_template;
    }
    
    /**
     * Default active for dialog
     * 
     * @return bool
     */
    public function default_sender_active()
    {
        return TRUE;
    }

    /**
     * Default active for dialog
     * 
     * @return bool
     */
    public function default_receiver_active()
    {
        return TRUE;
    }
    
    /**
     * Default site id for dialog
     * 
     * @return id
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * Default sender id for dialog
     * 
     * @return id
     */
    public function default_sender_id()
    {
        return Auth::instance()->get_user()->id;
    }
           
    /**
     * Delete dialog and all messages from it
     */
    public function delete()
    {
        // Delete all messages from dialog
        Model::fly('Model_Message')->delete_all_by_dialog_id($this->id);
        
        // Delete group itself
        parent::delete();
    }    
}
