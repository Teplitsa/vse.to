<?php defined('SYSPATH') or die('No direct script access.');

class Model_Token_Mapper extends Model_Mapper {

    public function init()
    {
        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('user_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('type', array('Type' => 'tinyint unsigned', 'Key' => 'INDEX'));
        $this->add_column('token', array('Type' => 'char(32)'));
        $this->add_column('expires_at', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        // Peform cleanup from time to time
        if (mt_rand(0, 100) > 80)
        {
            $this->cleanup();
        }
    }

    /**
     * Delete expired tokens
     */
    public function cleanup()
    {
        $this->delete_rows(DB::where('expires_at', '<=', time()));
    }

    /**
     * Finds a NOT EXPIRED token by criteria
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Model
     */
    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $condition = $this->_prepare_condition($condition);
        
        $condition->and_where('expires_at', '>', time());

        return parent::find_by($model, $condition, $params, $query);
    }

    /**
     * Save the token and generate unique token string for new tokens
     * 
     * @return integer
     */
    public function save(Model $token, $force_create = FALSE)
    {
        if ( ! isset($token->id))
        {
            // A new token here - generate new unique token string
            $this->lock();

            $loop_prevention = 0;
            do {
                $tk = md5(mt_rand() . mt_rand() . mt_rand() . mt_rand());
                $loop_prevention++;
            }
            while ($this->exists(DB::where('token', '=', $tk)) && $loop_prevention < 100 );

            if ($loop_prevention >= 100)
            {
                throw new Kohana_Exception('Infinite loop while generate unique token string');
            }
            
            $token->token = $tk;
            
            parent::save($token);

            $this->unlock();
        }
        else
        {
            parent::save($token);
        }

        return $token->id;
    }
}