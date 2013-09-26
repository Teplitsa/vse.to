<?php defined('SYSPATH') or die('No direct script access.');

class Model_SectionGroup extends Model
{
    
    /**
     * Get selected section group for the specified request using the value of
     * corresponding parameter ("cat_sectiongroup_id") in the uri
     *
     * @param  Request $request (if NULL, current request will be used)
     * @return Model_SectionGroup
     */
    public static function current(Request $request = NULL)
    {
        if ($request === NULL)
        {
            $request = Request::current();
        }
        $site_id = (int) Model_Site::current()->id;

        // check cache
        $sectiongroup = $request->get_value('sectiongroup');

        if ($sectiongroup !== NULL)
            return $sectiongroup;

        $sectiongroup = new Model_SectionGroup();
        
        if (APP == 'BACKEND')
        {

            $id = (int) $request->param('cat_sectiongroup_id');
            if ($id > 0)
            {
                // id of section group is explicitly specified in the uri
                $sectiongroup->find_by_id_and_site_id($id, $site_id);
            }
            else
            {
                if (Model_Section::current()->id !== NULL)
                {
                    // Find sectiongroup by current section
                    $id = Model_Section::current()->sectiongroup_id;

                    $sectiongroup->find_by_id_and_site_id($id, $site_id);
                }
                else
                {
                    // By default, simply select the first sectiongroup available
                    $sectiongroup->find_by_site_id($site_id);
                }
            }

        }
        ////SROCHNO
        /*elseif (APP == 'FRONTEND')
        {
            $current_group_id = Auth::instance()->get_user()->group_id;
            
            switch ($current_group_id) {
                case Model_Group::SHOWMAN_GROUP_ID:
                    $section_group_type = self::TYPE_ANNOUNCE; 
                    break;
                case Model_Group::EDITOR_GROUP_ID:
                    $section_group_type = self::TYPE_ANNOUNCE; 
                    break;                    
                default:
                    $section_group_type = self::TYPE_EVENT; 
                    break;
            }
            
            $sectiongroup_name = $request->param('sectiongroup_name');
            
            if ($sectiongroup_name != '')
            {
                $sectiongroup->find_by_name($sectiongroup_name);
            } else {
                $sectiongroup_id = $request->param('sectiongroup_id');

                if ($sectiongroup_id != '')
                {
                    $sectiongroup->find($sectiongroup_id);
                } else {
                    // By default, simply select the first sectiongroup available
                    $sectiongroup->find_by_type($section_group_type);
                }
            }            
        }*/
        // save in cache
        $request->set_value('sectiongroup', $sectiongroup);
        return $sectiongroup;
    }

    /**
     * Get list of all section groups for current site (cached)
     * 
     * @return Models
     */
    public function find_all_cached()
    {
        static $cache;
        
        if ($cache == NULL)
        {
            $cache = $this->find_all_by_site_id(Model_Site::current()->id);
        }
        return $cache;
    }

    /**
     * Get frontend uri to the section group
     */
    public function uri_frontend()
    {
        static $uri_template;

        if ($uri_template === NULL)
        {
            $uri_template = URL::to('frontend/catalog/sections', array(
                'sectiongroup_name' => '{{sectiongroup_name}}'
            ));
        }
        return str_replace(
            '{{sectiongroup_name}}',
            $this->name,
            $uri_template
        );
    }

    /**
     * Validate creation/updation of sectiongroup
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        // Check that sectiongroup name is unque
        if ( ! isset($newvalues['name']))
        {
            $this->error('Вы не указали имя!', 'name');
            return FALSE;
        }

        if ($this->exists_another_by_name($newvalues['name']))
        {
            $this->error('Группа категорий с таким именем уже существует!', 'name');
            return FALSE;
        }

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
            $this->error('Группа категорий "' . HTML::chars($this->caption) . '" является системной. Её удаление запрещено!');
            return FALSE;
        }

        return TRUE;
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
    
    /**
     * Set system flag
     *
     * @param boolean $system
     */
    public function set_system($system)
    {
        // Prohibit setting system property for user
    }    
    
}