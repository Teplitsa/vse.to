<?php defined('SYSPATH') or die('No direct script access.');

class Model_Section extends Model
{
    const EVENT_ID = 1;
    
    /**
     * Get currently selected section for the specified request using the value of
     * corresponding parameter in the uri
     *
     * @param  Request $request (if NULL, current request will be used)
     * @return Model_Section
     */
    public static function current(Request $request = NULL)
    {
        if ($request === NULL)
        {
            $request = Request::current();
        }

        // cache?
        $section = $request->get_value('section');
        if ($section !== NULL)
            return $section;
        
        $section = new Model_Section();
        if (APP == 'BACKEND')
        {
            $id = (int) $request->param('cat_section_id');
            if ($id > 0)
            {
                $section->find_by_id_and_site_id($id, Model_Site::current()->id);
            }
        }
        elseif (APP == 'FRONTEND')
        {
            $section = Model::fly('Model_Section')->find(Model_Section::EVENT_ID);
        }
        
        $request->set_value('section', $section);
        return $section;
    }
    /**
     * Backup model before saving
     * @var boolean
     */
    public $backup = array('parent_id');

    /**
     * Find all active sections for given sectiongroup
     *
     * @param  integer $sectiongroup_id
     * @param  boolean $load_if_missed
     * @return ModelsTree_NestedSet
     */
    public function find_all_active_cached($sectiongroup_id, $load_if_missed = TRUE)
    {
        static $cache;

        if ( ! isset($cache[$sectiongroup_id]))
        {
            if ($load_if_missed)
            {
                $cache[$sectiongroup_id] = $this->find_all_by_active_and_sectiongroup_id(1, $sectiongroup_id, array(
                    'order_by' => 'caption',
                    'desc' => FALSE,

                    'columns' => array('id', 'sectiongroup_id', 'alias', 'lft', 'rgt', 'level', 'caption', 'products_count'),

                    'as_tree' => TRUE,

                    'with_sectiongroup_name' => TRUE,
                    'with_images' => TRUE,
                    //
                    //'with_active_products_count' => TRUE
                ));
            }
            else
            {
                return NULL;
            }
        }
        return $cache[$sectiongroup_id];
    }

    /**
     * Get frontend uri to the section
     */
    public function uri_frontend($with_base = FALSE)
    {
        if ($with_base) {
            $uri_template = URL::to('frontend/catalog/products', array(
                'sectiongroup_name' => '{{sectiongroup_name}}',
                'path'              => '{{path}}'
            ));
        } else {
            $uri_template = URL::uri_to('frontend/catalog/products', array(
                'sectiongroup_name' => '{{sectiongroup_name}}',
                'path'              => '{{path}}'
            ));                
        }
        
        return str_replace(
            array('{{sectiongroup_name}}', '{{path}}'),
            array($this->sectiongroup_name, $this->full_alias),
            $uri_template
        );
    }

    /**
     * Section is active by default
     *
     * @return boolean
     */
    public function default_section_active()
    {
        return TRUE;
    }

    /**
     * Make alias for section from it's caption
     *
     * @return string
     */
    public function make_alias()
    {
        $caption_alias = str_replace(' ', '_', strtolower(l10n::transliterate($this->caption)));
        $caption_alias = preg_replace('/[^a-z0-9_-]/', '', $caption_alias);

        $i = 0;
        $loop_prevention = 1000;
        do {

            if ($i > 0)
            {
                $alias = substr($caption_alias, 0, 30);
                $alias .= $i;
            }
            else
            {
                $alias = substr($caption_alias, 0, 31);
            }

            $i++;
            if ($this->level == 1)
            {
                $exists = $this->exists_another_by_alias_and_level($alias, 1);
            }
            else
            {
                $exists = $this->exists_another_by_alias_and_parent($alias, $this->parent);
            }
        }
        while ($exists && ($loop_prevention-- > 0));

        if ($loop_prevention <= 0)
            throw new Kohana_Exception ('Possible infinite loop in :method', array(':method' => __METHOD__));

        return $alias;
    }

    /**
     * Get full alias to this section by concatinating aliases of parent sectoins
     * 
     * @param  string $glue
     * @return string
     */
    public function get_full_alias($glue = '/')
    {
        if ( ! isset($this->_properties['full_alias']))
        {
            $sections = $this->find_all_active_cached($this->sectiongroup_id, FALSE);

            if ($sections !== NULL)
            {
                // Obtain parents from cached version of sections tree
                $parents = $sections->parents($this, TRUE);
            }
            else
            {
                // Get parents from database
                $parents = $this->get_parents(array('columns' => array('id', 'alias'), 'as_array' => TRUE));
            }

            $full_alias = '';
            foreach ($parents as $parent)
            {
                $full_alias .= $parent['alias'] . $glue;
            }
            $full_alias .= $this->alias;
            
            $this->_properties['full_alias'] = $full_alias;
        }
        return $this->_properties['full_alias'];
    }

    /**
     * Returns the first not empty description of section or it parents
     * 
     * @return string
     */
    public function get_full_description()
    {
        // get section description
        $section_description = '';

        foreach ($this->parents as $par)
        {
            if ($par->description != '')
            {                
                $section_description = $par->description;
                if ($par->image != '')
                {                    
                    $section_description = HTML::image('public/data/' . $par->image, array(
                        'width' => $par->image_width,
                        'height' => $par->image_height,
                        'alt' => $par->caption
                    )) . $section_description;
                }
            }
        }
        if ($section_description == '')
        {            
            $section_description = $this->description;
            if ($section_description != '')
            {
                if ($this->image != '')
                {
                    $section_description = HTML::image('public/data/' . $this->image, array(
                        'width' => $this->image_width,
                        'height' => $this->image_height,
                        'alt' => $this->caption
                    )) . $section_description;
                }
            }
        }
        return $section_description;
    }

    /**
     * Get sectiongroup name for this section
     *
     * @return string
     */
    public function get_sectiongroup_name()
    {
        if ( ! isset($this->_properties['sectiongroup_name']))
        {
            $sectiongroups = Model::fly('Model_SectionGroup')->find_all_cached();
            $this->_properties['sectiongroup_name'] = $sectiongroups[$this->sectiongroup_id]->name;
        }
        return $this->_properties['sectiongroup_name'];
    }

    /**
     * Finds a parent section id for this section
     *
     * @return Model_Section
     */
    public function get_parent_id()
    {
        if ( ! isset($this->_properties['parent_id']))
        {
            $this->_properties['parent_id'] = $this->parent->id;
        }
        return $this->_properties['parent_id'];
    }

    /**
     * Find a parent section
     *
     * @param  array $params
     * @return Model_Section
     */
    public function get_parent(array $params = NULL)
    {
        if ( ! isset($this->_properties['parent']))
        {
            if (isset($this->_properties['parent_id']))
            {
                $this->_properties['parent'] = new Model_Section;
                $this->_properties['parent']->find($this->_properties['parent_id'], $params);
            }
            else
            {
                $this->_properties['parent'] = $this->mapper()->find_parent($this, $params);
            }
        }
        return $this->_properties['parent'];
    }

    /**
     * Get all parents for this section
     *
     * @return Models
     */
    public function get_parents(array $params = NULL)
    {
        if ( ! isset($this->_properties['parents']))
        {
            $this->_properties['parents'] = $this->mapper()->find_all_parents($this, $params);
        }
        return $this->_properties['parents'];
    }

    /**
     * Construct full caption of this section - concatenated caption of all parent sections
     *
     * @param  string $delimeter
     * @return string
     */
    public function get_full_caption($delimeter = '&nbsp;&raquo;&nbsp;')
    {
        //@fixme: This can be accomplished by a single sql request with group_concat

        $parents = $this->get_parents(array(
            'order_by' => 'level', 'desc' => FALSE,
            'columns' => array('caption')
        ));

        $caption = '';
        foreach ($parents as $parent)
        {
            $caption .= HTML::chars($parent->caption) . $delimeter;
        }
        $caption .= HTML::chars($this->caption);

        return $caption;
    }

    /**
     * Get property-section infos
     *
     * @return Models
     */
    public function get_propertysections()
    {        
        if ( ! isset($this->_properties['propertysections']))
        {
            $this->_properties['propertysections'] =
                Model::fly('Model_PropertySection')->find_all_by_section($this, array('order_by' => 'position', 'desc' => FALSE));
        }

        return $this->_properties['propertysections'];
    }

    /**
     * Get property-section infos as array for form
     *
     * @return array
     */
    public function get_propsections()
    {
        if ( ! isset($this->_properties['propsections']))
        {
            $result = array();

            foreach ($this->propertysections as $propsection)
            {
                $result[$propsection->property_id]['active'] = $propsection->active;
                $result[$propsection->property_id]['filter'] = $propsection->filter;
                $result[$propsection->property_id]['sort']   = $propsection->sort;
            }

            $this->_properties['propsections'] = $result;
        }
        return $this->_properties['propsections'];
    }

    /**
     * Set property-section link info (usually from form - so we need to add 'section_id' field)
     *
     * @param array $propsections
     */
    public function set_propsections(array $propsections)
    {
        foreach ($propsections as $property_id => & $propsection)
        {
            if ( ! isset($propsection['property_id']))
            {
                $propsection['property_id'] = $property_id;
            }
        }

        $this->_properties['propsections'] = $propsections;
    }

    /**
     * Save section and link it to selected properties
     *
     * @param boolean $force_create
     * @param boolen  $link_properties
     * @param boolean $update_stats
     */
    public function save($force_create = FALSE, $link_properties = TRUE, $update_stats = TRUE)
    {
        // Create alias from caption
        $this->alias = $this->make_alias();

        parent::save($force_create);

        if ($link_properties)
        {
            // Link section to the properties
            Model::fly('Model_PropertySection')->link_section_to_properties($this, $this->propsections);
        }

        if ($this->parent_id !== $this->previous()->parent_id)
        {
            // Section is moved to the new parent, we need to relink all products in section
            Model::fly('Model_Product')->relink($this);
        }
        
        if ($update_stats)
        {
            $this->mapper()->update_activity(array($this->id));
            $this->mapper()->update_products_count(array($this->id, $this->parent_id, $this->previous()->parent_id));
        }
    }
     
    /**
     * Validate product deletion
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        // Create COMDI event
        if (Model_Product::COMDI === TRUE) {
              // Delete products from all subsections
              $subsections = $this->mapper()->find_all_with_subtree($this,array('as_list' => TRUE));
    
              $deleted = array();
              $not_deleted = array();
              
              foreach ($subsections as $subsection)
              {
                    $loop_prevention = 10000;
                    do {                        
                        $products = Model::fly('Model_Product')->find_all_by_section_id($subsection->id, array(
                            'batch' => 100
                        ));
                        foreach ($products as $product)
                        {
                            $bool = $product->validate_delete();
                            if ($bool === TRUE) {
                                $deleted[] = $product;
                            } else {
                                $not_deleted[] = $product;
                            }
                        }

                        $loop_prevention--;
                    }
                    while (count($products) && $loop_prevention > 0);

                    if ($loop_prevention <= 0)
                    {
                        throw new Kohana_Exception('Possibly an infinte loop while deleting section');
                    }                    
              }
              
              foreach ($not_deleted as $nd) {
                    $this->error('Не удается удалить COMDI-event для события '.$nd->caption.' ID = '.$nd->id);                  
              }
        }
        return parent::validate_delete($newvalues);
        /*
        // Create COMDI event
        if (Model_Product::COMDI === TRUE) {
              // Delete products from all subsections
              $subsections = $this->mapper()->find_all_subtree($this,array('as_list' => TRUE));

              $deleted = array();
              $repair_flag = FALSE;
              $stopper = FALSE;
              
              foreach ($subsections as $subsection)
              {
                    $loop_prevention = 10000;
                    $bool = true;
                    do {
                        $products = $this->find_all_by_section_id($section_id, array(
                            'columns' => array('id'),
                            'batch' => 100
                        ));

                        foreach ($products as $product)
                        {
                            $bool = $bool & $product->validate_delete();
                            if ($bool ===TRUE) {
                                $deleted[] = $product;
                            } else {
                                $repair_flag = TRUE;
                                $stopper = $product;
                                break;
                            }
                        }

                        $loop_prevention--;
                    }
                    while (count($products) && $loop_prevention > 0 && !$repair_flag);

                    if ($loop_prevention <= 0)
                    {
                        throw new Kohana_Exception('Possibly an infinte loop while deleting section');
                    }                    
                    if ($repair_flag) break;  
              }
              if ($repair_flag) {
                  foreach ($deleted as $del_product) {
                    $event_id = TaskManager::start('comdi_create', Task_Comdi_Base::mapping($del_product->values()));

                    if ($event_id === NULL) {
                        $this->error('Сервис COMDI временно не работает!');
                        return FALSE;
                    }

                    $del_product->event_id = $event_id;
                    $del_product->save();
                  }
                  if (empty($deleted)) {
                        $this->error('Сервис COMDI временно не работает!');
                        return FALSE;                      
                  } else {
                        $this->error('Не удается удалить COMDI-event для события '.$stopper->caption);
                        return FALSE;                      
                  }
              }
        }
        return parent::validate_delete($newvalues);*/
    }    
    /**
     * Delete section with all subsections and products
     */
    public function delete()
    {
        // Delete products from all subsections
        $subsections = $this->mapper()->find_all_subtree($this,array('as_list' => TRUE));
   
        foreach ($subsections as $subsection)
        {            
            Model::fly('Model_Product')->delete_all_by_section_id($subsection->id);

            // Delete section logo
            Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('section', $subsection->id);
            
            // Unlink products from section
            Model::fly('Model_Product')->unlink_all_from_section($subsection);            
        }

        // Delete products from this section
        Model::fly('Model_Product')->delete_all_by_section_id($this->id);

        // Delete section logo
        Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('section', $this->id);

        //@TODO: Unlink properties from section and all subsections

        // Unlink products from section
        Model::fly('Model_Product')->unlink_all_from_section($this);

        $parent_id = $this->parent_id;

        // Delete section and subsections
        $this->mapper()->delete_with_subtree($this);

        // Update products count for all parent sections
        if ( ! empty($parent_id))
        {
            $this->mapper()->update_products_count(array($parent_id));
        }
    }
}