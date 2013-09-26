<?php defined('SYSPATH') or die('No direct script access.');

class DbTable_TaskValues extends DbTable
{
    public function init()
    {
        parent::init();

        $this->add_column('task',  array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
        $this->add_column('key',   array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
        $this->add_column('value', array('Type' => 'text'));
    }

    /**
     * Set a value for task
     *
     * @param string $task
     * @param string $key
     * @param mixed $value
     */
    public function set_value($task, $key, $value)
    {
        $value = serialize($value);
        
        if ($this->exists(DB::where('task', '=', $task)->and_where('key', '=', $key)))
        {
            // Update existing value
            $this->update(array('value' => $value), DB::where('task', '=', $task)->and_where('key', '=', $key));
        }
        else
        {
            // Insert new value
            $this->insert(array(
                'task'  => $task,
                'key'   => $key,
                'value' => $value
            ));
        }
    }

    /**
     * Get value for task
     *
     * @param string $task
     * @param string $key
     */
    public function get_value($task, $key)
    {
        $result = $this->select_row(DB::where('task', '=', $task)->and_where('key', '=', $key));
        if (count($result))
        {
            $value = @unserialize($result['value']);
        }
        else
        {
            $value = NULL;
        }
        return $value;
    }

    /**
     * Unset the value for task
     *
     * @param string $task
     * @param string $key
     */
    public function unset_value($task, $key)
    {
        $this->delete_rows(DB::where('task', '=', $task)->and_where('key', '=', $key));
    }
}