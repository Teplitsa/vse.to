<?php defined('SYSPATH') or die('No direct script access.');

class Model_LinkValue_Mapper extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('user_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('link_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('value', array('Type' => 'varchar(63)'));
    }
    /**
     * Update link values for given user
     * 
     * @param Model_User $user
     */
    public function update_values_for_user(Model_User $user)
    {        
        foreach ($user->links as $link)
        {
            $name = $link->name;
            $value = $user->__get($name);

            if ($value === NULL)
                continue;
            
            $where = DB::where('user_id', '=', (int) $user->id)
                ->and_where('link_id', '=', (int) $link->id);

            if ($this->exists($where))
            {
                $this->update(array('value' => $value), $where);
            }
            else
            {
                $this->insert(array(
                    'user_id' => (int) $user->id,
                    'link_id' => (int) $link->id,
                    'value'   => $value
                ));
            }
        }
    }
}