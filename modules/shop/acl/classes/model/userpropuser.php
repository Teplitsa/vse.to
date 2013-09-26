<?php defined('SYSPATH') or die('No direct script access.');

class Model_UserPropUser extends Model
{
    /**
     * Get "active" userprop
     * 
     * @return boolean
     */
    public function get_active()
    {        
        if ($this->system)
        {
            // System userprops are always active
            return 1;
        }
        elseif (isset($this->_properties['active']))
        {
            return $this->_properties['active'];
        }
        else
        {
            return 0; // default value
        }
    }

    /**
     * Set "active" userprop
     *
     * @param <type> $value
     */
    public function set_active($value)
    {
        // System userprops are always active
        if ($this->system)
        {
            $value = 1;
        }

        $this->_properties['active'] = $value;
    }

    /**
     * Link given userprop to user
     * 
     * @param Model_Userprop $userprop
     * @param array $userpropusers
     */
    public function link_userprop_to_users(Model_UserProp $userprop, array $userpropusers)
    {
        // Delete all info
        $this->delete_all_by_userprop_id($userprop->id);
        
        $userpropuser = Model::fly('Model_UserPropUser');
        foreach ($userpropusers as $values)
        {
            $userpropuser->values($values);
            $userpropuser->userprop_id = $userprop->id;
            $userpropuser->save();
        }
    }

    /**
     * Link given user to userprops
     *
     * @param Model_User $user
     * @param array $userpropusers
     */
    public function link_user_to_userprops(Model_Group $user, array $userpropusers)
    {
        // Delete all info
        $this->delete_all_by_user_id($user->id);

        $userpropuser = Model::fly('Model_UserPropUser');
        foreach ($userpropusers as $values)
        {
            $userpropuser->values($values);
            $userpropuser->user_id = $user->id;
            $userpropuser->save();
        }
    }
}