<?php defined('SYSPATH') or die('No direct script access.');

class Model_PrivilegeValue_Mapper extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('group_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('privilege_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('value',       array('Type' => 'varchar(31)'));
    }
    /**
     * Update privilege values for given group
     * 
     * @param Model_Group $group
     */
    public function update_values_for_group(Model_Group $group)
    {   
        foreach ($group->privileges as $privilege)
        {
            $name = $privilege->name;
            $value = $group->__get($name);

            if ($value === NULL)
                continue;
            
            $where = DB::where('group_id', '=', (int) $group->id)
                ->and_where('privilege_id', '=', (int) $privilege->id);

            if ($this->exists($where))
            {
                $this->update(array('value' => $value), $where);
            }
            else
            {
                $this->insert(array(
                    'group_id'  => (int) $group->id,
                    'privilege_id' => (int) $privilege->id,
                    'value'       => $value
                ));
            }
        }
    }
}