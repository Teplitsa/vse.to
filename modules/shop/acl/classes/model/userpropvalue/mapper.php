<?php defined('SYSPATH') or die('No direct script access.');

class Model_UserPropValue_Mapper extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('user_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('userprop_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('value',       array('Type' => 'varchar(31)'));
    }
    /**
     * Update userprop values for given user
     * 
     * @param Model_User $user
     */
    public function update_values_for_user(Model_User $user)
    {        
        foreach ($user->userprops as $userprop)
        {
            $name = $userprop->name;
            $value = $user->__get($name);

            if ($value === NULL)
                continue;
            
            $where = DB::where('user_id', '=', (int) $user->id)
                ->and_where('userprop_id', '=', (int) $userprop->id);

            if ($this->exists($where))
            {
                $this->update(array('value' => $value), $where);
            }
            else
            {
                $this->insert(array(
                    'user_id'  => (int) $user->id,
                    'userprop_id' => (int) $userprop->id,
                    'value'       => $value
                ));
            }
        }
    }
}