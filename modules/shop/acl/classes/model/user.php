<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model
{
    const LINKS_LENGTH = 36;
    
    static $users = array();
    
    public static function users($group_id)
    {
        if (!isset(self::$users[$group_id])) {
            self::$users[$group_id] =array();
            $results = Model::fly('Model_User')->find_all_by_group_id($group_id,array(
                'order_by'=> 'last_name',
                'columns'=>array('id','last_name')
            ));
            foreach ($results as $result) {
                self::$users[$group_id][$result->id] = $result->name;
            }
        }
        return self::$users[$group_id];
    } 
    
    /**
     * Get userlinks for the user
     */
	 
	public function update_props(array $newvals) {
		$this->email = $newvals['email'];
		$this->first_name = $newvals['first_name'];
		$this->last_name = $newvals['last_name'];
		$this->organizer_name = $newvals['organizer_name'];
		$this->town_id = $newvals['town_id'];
		$this->info = $newvals['info'];
		$this->set_password($newvals['password']);
	}    
    /**
     * Get currently authorized user (FRONTEND)
     *
     * @param  Request $request (if NULL, current request will be used)
     * @return Model_User
     */
    public static function current(Request $request = NULL)
    {
        if ($request === NULL)
        {
            $request = Request::current();            
        }
        // cache?
        $user = $request->param('user',NULL);
        if ($user !== NULL)
            return $user;
    
        
        $user = Auth::instance()->get_user();
        
        $request->set_value('user', $user);

        return $user;
    }    
    /**
     * Does this user have the requested privilege?
     * 
     * @param  string $privilege 
     * @return boolean
     */
    public function granted($privilege)
    {
        return $this->group->granted($privilege);
    }
    /**
     * Proxy privileges from Model_Group
     * 
     * @return Models
     */    
    public function get_privileges() {
        return $this->get_group()->privileges;
    }

    /**
     * Proxy privileges_granted from Model_Group
     * 
     * @return Models
     */    
    public function get_privileges_granted() {
        return $this->get_group()->privileges_granted;
    }    
    /**
     * User is not system by default
     *
     * @return boolean
     */
    public function default_system()
    {
        return FALSE;
    }

    public function default_notify()
    {
        return FALSE;
    }    
    
    /**
     * Returns default group id for user
     *
     * @return integer
     */
    public function default_group_id()
    {
        return 0;
    }

    /**
     * Get active[!] userprops for this user
     */
    public function get_userprops()
    {  
        if ( ! isset($this->_properties['userprops']))
        {
            if ($this->id !== NULL) {
                $userprops = Model::fly('Model_UserProp')->find_all_by_user_id_and_active_and_system(
                    $this->id, 1, 0, 
                    array('order_by' => 'position', 'desc' => FALSE)
                );
            } else {
                $userprops = new Models('Model_UserProp',array());
            }
            $this->_properties['userprops'] = $userprops;
        } 
        return $this->_properties['userprops'];
    }  

    /**
     * Get userlinks for the user
     */
    public function get_links()
    {  
        if ( ! isset($this->_properties['links']))
        {
            $userlinks = Model::fly('Model_Link')->find_all( 
                    array('order_by' => 'position', 'desc' => FALSE)
                );
            
            $this->_properties['links'] = $userlinks;
        } 
        return $this->_properties['links'];
    }  

    /**
     * Set password for user and regenerate hash
     *
     * @param string $value
     */
    public function set_password($value)
    {
        $this->password = $value;
        $this->hash = Auth::instance()->calculate_hash($value);
    }
    
    /**
     * Get group for this user
     *
     * @return Model_Group
     */
    public function get_group()
    {
        if ( ! isset($this->_properties['group']))
        {
            $group = new Model_Group();

            if ($this->group_id != 0)
            {
                $group->find($this->group_id);
            }

            $this->_properties['group'] = $group;
        }

        return $this->_properties['group'];
    }

    /**
     * Set system flag
     *
     * @param boolean $system
     */
    public function set_system($system)
    {
        // Prohibit setting system property for user
    }

    /**
     * Return user name
     * 
     * @return string
     */
    public function get_name()
    {
        $name = '';
        if ($this->first_name) $name.=$this->first_name;
        if ($this->last_name) $name.=' '.$this->last_name;
        
        return $name;
    }

    /**
     * Generates a new token for this user with the type given
     * 
     * @param  integer $type
     * @return Model_Token
     */
    public function generate_token($type = Model_Token::TYPE_LOGIN)
    {
        $token = new Model_Token();
        $token->type = Model_Token::TYPE_LOGIN;
        $token->user_id = $this->id;

        $token->save();

        return $token;
    }

	/**
	 * Complete the login for a user by incrementing the logins and saving login timestamp
	 */
	public function complete_login()
	{
        //@TODO:
        /*
		// Update the number of logins
		$this->logins += 1;

		// Set the last login date
		$this->last_login = time();

		// Save the user
		$this->save();
         */
	}

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------
    /**
     * Validate user creation
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_create(array $newvalues)
    {
        return $this->validate_update($newvalues);
    }
    
    /**
     * Validates user update
     *
     * @param array $newvalues
     * @return boolean
     */
    public function validate_update(array $newvalues)
    {
        
        if (!isset($newvalues['organizer_id']) || $newvalues['organizer_id'] == NULL) {
            $this->error('Указана несуществующая организация!');
            return FALSE;
        }
        return (
                $this->validate_email($newvalues)
            AND $this->validate_group_id($newvalues)
        );
        
    }

    /**
     * Validate user email
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_email(array $newvalues)
    {
        if ( ! isset($newvalues['email']))
        {
            $this->error('Вы не указали e-mail!', 'email');
            return FALSE;
        }

        if ($this->exists_another_by_email($newvalues['email']))
        {
            $this->error('Пользователь с таким e-mail уже существует!', 'email');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Prohibit group changes for system users
     *
     * @param array   $newvalues New values for model
     * @return boolean
     */
    public function validate_group_id(array $newvalues)
    {
        if ( ! isset($newvalues['group_id']))
        {
            if ( ! isset($this->group_id))
            {
                $this->error('Не указана группа!', 'group_id');
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }

        if ($this->system && (int)$newvalues['group_id'] !== $this->group_id)
        {
            $this->error('Для системного пользователя нельзя сменить группу!', 'group_id');
            return FALSE;
        }

       //@FIXME: check that group exists here, not in form?
        return TRUE;
    }

    /**
     * Prohibit deletion of system users
     *
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        if ($this->system)
        {
            $this->error('Пользователь "' . HTML::chars($this->name) . '" является системным. Его удаление запрещено!');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Get userprop-user infos
     *
     * @return Models
     */
    public function get_userpropusers()
    {        
        if ( ! isset($this->_properties['userpropusers']))
        {
            $this->_properties['userpropusers'] =
                Model::fly('Model_UserPropUser')->find_all_by_user($this, array('order_by' => 'position', 'desc' => FALSE));
        }

        return $this->_properties['userpropusers'];
    }

    /**
     * Get userprop-user infos as array for form
     *
     * @return array
     */
    public function get_userpropus()
    {
        if ( ! isset($this->_properties['userpropus']))
        {
            $result = array();

            foreach ($this->userpropusers as $userpropuser)
            {
                if ($userpropuser->userprop_id !== NULL) 
                        $result[$userpropuser->userprop_id]['userprop_id'] = $userpropuser->userprop_id; 
                $result[$userpropuser->userprop_id]['active'] = $userpropuser->active;
            }
            $this->_properties['userpropus'] = $result;
        }
        return $this->_properties['userpropus'];
    }

    /**
     * Set userprop-user link info (usually from form - so we need to add 'user_id' field)
     *
     * @param array $userpropusers
     */
    public function set_userpropus(array $userpropusers)
    {
        foreach ($userpropusers as $userprop_id => & $userpropuser)
        {
            if ( ! isset($userpropuser['userprop_id']))
            {
                $userpropuser['userprop_id'] = $userprop_id;
            }
        }

        $this->_properties['userpropus'] = $userpropusers;
    }
    
    public function get_organizer() {
        if ( ! isset($this->_properties['organizer']))
        {
            $organizer = new Model_Organizer();
            $organizer->find((int) $this->organizer_id);
            if (!$organizer->id) $organizer->name = $this->organizer_name;
            $this->_properties['organizer'] = $organizer;
        }
        return $this->_properties['organizer'];        
    }

    public function get_town() {
        if ( ! isset($this->_properties['town']))
        {
            $town = new Model_Town();
            $town->find((int) $this->town_id);
            $this->_properties['town'] = $town;
        }
        return $this->_properties['town'];        
    }
    
    public function get_tag_items() {
        if ( ! isset($this->_properties['tag_items']))
        {
            $tags = array();
            if ($this->id) {
                $tags = Model::fly('Model_Tag')->find_all_by(array(
                    'owner_type' => 'user',
                    'owner_id' => $this->id
                ), array('order_by' => 'weight'));
            }
            $this->_properties['tag_items'] = $tags;
        }
        return $this->_properties['tag_items'];        
    }
    
    public function get_tags() {
        if ( ! isset($this->_properties['tags']))
        {
            $tag_str = '';
            $tag_arr = array();

            foreach ($this->tag_items as $tag_item) {
                $tag_arr[] = $tag_item->name;
            }
            $this->_properties['tags'] = implode(Model_Tag::TAGS_DELIMITER,$tag_arr);
        }
        return $this->_properties['tags'];
    }
    
    public function save($create = FALSE,$update_userprops = TRUE, $update_userlinks = TRUE)
    {
        parent::save($create);

        if ($this->file['name']) {
            // Delete product images
            Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('user', $this->id);
            
            $image = new Model_Image();
            $image->file = $this->file;
            $image->owner_type = 'user';
            $image->owner_id = $this->id;
            $image->config = 'user';
            $image->save();
        }
        
        Model::fly('Model_Tag')->save_all($this->tags, 'user',$this->id);

        if ($update_userprops)
        {
            // Link user to the userprops
            Model::fly('Model_UserPropUser_Mapper')->link_user_to_userprops($this, $this->userpropus);
            // Update values for additional properties
            Model_Mapper::factory('Model_UserPropValue_Mapper')->update_values_for_user($this);
        }        

        if ($update_userlinks)
        {
            // Update links values
            Model_Mapper::factory('Model_LinkValue_Mapper')->update_values_for_user($this);
        }        
        
    }
    
    private function recovery_link_gen()
    {
        return 'http://vse.to/'.URL::uri_to('frontend/acl',array('action'=>'newpas','hash' => Auth::instance()->hash(time())));
    }
    /**
     * Set notification to client that his question has been answered
     */
    public function password_recovery()
    {
        try {
            $this->recovery_link = $this->recovery_link_gen();

            // Site settings
            $settings = Model_Site::current()->settings;
            $email_from   = isset($settings['email']['from'])      ? $settings['email']['from']      : '';
            $email_sender = isset($settings['email']['sender'])    ? $settings['email']['sender']    : '';
            $signature    = isset($settings['email']['signature']) ? $settings['email']['signature'] : '';

            if ($email_sender != '')
            {
                $email_from = array($email_from => $email_sender);
            }

            // FAQ settings
            $config = Modules::load_config('acl_' . Model_Site::current()->id, 'acl');
            $subject = isset($config['email']['client']['subject'])  ? $config['email']['client']['subject']  : '';
            $body    = isset($config['email']['client']['body'])     ? $config['email']['client']['body']     : '';


            if ($this->email != '')
            {
                $twig = Twig::instance('string');
                
                // Values for the templates
                $values = $this->values();
                $values['site'] = Model_Site::canonize_url(URL::site('', TRUE));

                // Subject
                $subject = $twig->loadTemplate($subject)->render($values);
                
                // Body (with optional signature)
                $body = $twig->loadTemplate($body)->render($values);
                if ($signature != '')
                {
                    $body .= "\n\n\n" . $signature;
                }


                // Init mailer
                SwiftMailer::init();
                $transport = Swift_MailTransport::newInstance();
                $mailer = Swift_Mailer::newInstance($transport);

                $message = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($email_from)
                    ->setTo($this->email)
                    ->setBody($body);
                
                // Send message
                $mailer->send($message);
            }
        }
        catch (Exception $e)
        {
            if (Kohana::$environment !== Kohana::PRODUCTION)
            {
                throw $e;
            } else {
                return FALSE;
            }
        }
        $this->save(FALSE,FALSE,FALSE);
        return TRUE;
    }
    
    public function delete()
    {
        // Delete userprop values for this user
        Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('user', $this->id);

        // Delete userprop values for this user
        Model_Mapper::factory('Model_UserPropValue_Mapper')->delete_all_by_user_id($this, $this->id);

        // Delete links values for this user
        Model_Mapper::factory('Model_LinkValue_Mapper')->delete_all_by_user_id($this, $this->id);
        
        // Delete user itself
        parent::delete();
    }     
    
    public function image($size = NULL) {
        $image_info = array();
        $image = Model::fly('Model_Image')->find_by_owner_type_and_owner_id('user', $this->id, array(
            'order_by' => 'position',
            'desc'     => FALSE
        ));
        if ($size) {
            $field_image = 'image'.$size;
            $field_width = 'width'.$size;
            $field_height = 'height'.$size;

            $image_info['image'] = $image->$field_image;
            $image_info['width'] = $image->$field_width;
            $image_info['height'] = $image->$field_height;
        }
        return $image_info;
    }        
}
