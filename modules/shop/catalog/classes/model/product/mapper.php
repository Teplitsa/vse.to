<?php defined('SYSPATH') or die('No direct script access.');

class Model_Product_Mapper extends Model_Mapper_Resource {

    public function init()
    {
        parent::init();

        // for event
        
        $this->add_column('id',          array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        
        $this->add_column('event_id',    array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('event_uri',    array('Type' => 'varchar(127)'));
        
        $this->add_column('alias',       array('Type' => 'varchar(63)', 'Key' => 'INDEX'));

        $this->add_column('section_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        
        $this->add_column('web_import_id', array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
        
        $this->add_column('lecturer_id', array('Type' => 'int unsigned'));
        
        $this->add_column('lecturer_name', array('Type' => 'varchar(127)'));

        $this->add_column('organizer_id', array('Type' => 'int unsigned'));

        $this->add_column('organizer_name', array('Type' => 'varchar(127)'));
        
        $this->add_column('place_id', array('Type' => 'int unsigned'));

        $this->add_column('place_name', array('Type' => 'varchar(127)'));        

        $this->add_column('theme', array('Type' => 'varchar(127)'));

        $this->add_column('format', array('Type' => 'varchar(127)'));
        
        $this->add_column('caption',     array('Type' => 'varchar(255)'));

        $this->add_column('description', array('Type' => 'text'));
                
        $this->add_column('datetime', array('Type' => 'datetime'));

        $this->add_column('duration', array('Type' => 'varchar(7)'));
                        
        $this->add_column('active', array('Type' => 'boolean', 'Key' => 'INDEX'));

        $this->add_column('visible', array('Type' => 'boolean', 'Key' => 'INDEX'));        
        
        // for translation
        
        $this->add_column('interact', array('Type' => 'varchar(31)'));

        $this->add_column('access', array('Type' => 'varchar(31)'));
        
        $this->add_column('numviews', array('Type' => 'int unsigned'));
        
        $this->add_column('choalg', array('Type' => 'int unsigned'));

        $this->add_column('require', array('Type' => 'text'));

        $this->add_column('price',       array('Type' => 'money'));
        
//        $this->cache_find_all = TRUE;        
        
        // Telemost provider
        $this->add_column('telemost_provider', array('Type' => 'varchar(128)'));
        
        // Hangouts
        $this->add_column('hangouts_secret_key', array('Type' => 'varchar(128)'));
        $this->add_column('hangouts_url', array('Type' => 'varchar(256)'));

        $this->add_column('hangouts_test_secret_key', array('Type' => 'varchar(128)'));
        $this->add_column('hangouts_test_url', array('Type' => 'varchar(256)'));
    }
    

    /**
     * Find product by condition
     * Load additional properties
     *
     * @param Model $model
     * @param string|array|Database_Condition_Where $condition
     * @param array $params
     * @param Database_Query_Builder_Select $query
     */
    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $table = $this->table_name();
        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->from($table);
        }
       
        
        if (is_array($condition) && isset($condition['section']))
        {
            // Find a product which is bound to the specified section
            $prodsec_table = DbTable::instance('ProductSection')->table_name();
            $query
                ->and_where("$prodsec_table.section_id", '=', (int) $condition['section']->id);

            $params['join_productsection'] = TRUE;
            
            unset($condition['section']);
        }

        if (is_array($condition) && isset($condition['section_active']))
        {
            $section_table = Model_Mapper::factory('Model_Section_Mapper')->table_name();
            
            $query->where("$section_table.active", '=', (int) $condition['section_active']);

            $params['join_section'] = TRUE;
            
            unset($condition['section_active']);
        }
        
        if ( is_array($condition) && isset($condition['site_id']) )
        {
            $params['join_sectiongroup'] = TRUE;
        }

        if ( ! empty($params['with_image']))
        {
            // Select images for product via join
            $image_table = Model_Mapper::factory('Model_Image_Mapper')->table_name();

            // Number of the thumbnail to select
            $i = $params['with_image'];

            // Additinal columns
            $query->select(
                array("$image_table.image$i",  'image'),
                array("$image_table.width$i",  'image_width'),
                array("$image_table.height$i", 'image_height')
            );

            $query
                ->join($image_table, 'LEFT')
                    ->on("$image_table.owner_id", '=', "$table.id")
                    ->on("$image_table.owner_type", '=', DB::expr("'product'"))

               ->join(array($image_table, 'img'), 'LEFT')
                    ->on(DB::expr("img.owner_id"), '=', "$image_table.owner_id")
                    ->on(DB::expr("img.owner_type"), '=', DB::expr("'product'"))
                    ->on(DB::expr("img.position"), '<', "$image_table.position")
               ->where(DB::expr('ISNULL(img.id)'), NULL, NULL);

        }
        
        if ( ! isset($params['with_properties']) || ! empty($params['with_properties']))
        {            
            // Select product with additional (non-system) property values
            // @TODO: use only properties for product's section - load the product, and then load the properties??
            $properties = Model::fly('Model_Property')->find_all_by_site_id_and_system(Model_Site::current()->id, 0, array(
                'columns'  => array('id', 'name'),
                'order_by' => 'position',
                'desc'     => FALSE,
                'as_array' => TRUE
            ));

            $table = $this->table_name();
            $propertyvalue_table = Model_Mapper::factory('PropertyValue_Mapper')->table_name();

            foreach ($properties as $property)
            {
                $id = (int) $property['id'];

                // Add column to query
                $query->select(array(DB::expr("propval$id.value"), $property['name']));

                $query->join(array($propertyvalue_table, "propval$id"), 'LEFT')
                    ->on(DB::expr("propval$id.property_id"), '=', DB::expr($id))
                    ->on(DB::expr("propval$id.product_id"), '=', "$table.id");
            }
        }

        // ----- joins
        $this->_apply_joins($query, $params);

        return parent::find_by($model, $condition, $params, $query);
    }

    /**
     * Build database query condition from search params
     *
     * @param  Model_Product $product
     * @param  array $search_params
     * @return (Database_Expression_Where|NULL, array)
     */
    public function search_condition(Model_Product $product, array $search_params = NULL)
    {
        $condition = DB::where();
        $params    = array();

        $table = $this->table_name();

        // Search text
        if (isset($search_params['search_text']) && isset($search_params['search_fields']))
        {
            $words = preg_split('/\s+/', $search_params['search_text'], NULL, PREG_SPLIT_NO_EMPTY);
            foreach ($words as $word)
            {
                $condition->and_where_open();
                foreach ($search_params['search_fields'] as $field)
                {
                    if (strpos($field, '.') === FALSE)
                    {
                        $field = "$table.$field";
                    }
                    $condition->or_where($field, 'LIKE', "%$word%");
                }
                $condition->and_where_close();
            }
        }

        // Search text
        if (isset($search_params['search_date']))
        {
            $date = date(Kohana::config('datetime.db_date_format'),$search_params['search_date']);
            $condition->and_where('datetime', 'LIKE', "%$date%");
        }
        
        // Condition for fields
        if ( ! empty($search_params))
        {
            foreach ($search_params as $name => $value)
            {
                if ($this->has_column($name))
                {
                    $column = $this->get_column($name);
                }
                elseif ($this->has_virtual_column($name))
                {
                    $column = $this->get_virtual_column($name);
                }
                else
                {
                    continue;
                }

                $type_info = $this->_parse_column_type($column['Type']);
                switch ($type_info['type'])
                {
                    case 'boolean':
                        if ($value != -1)
                        {
                            $condition->and_where("$table.$name", '=', (int) $value);
                        }
                        break;
                    default:
                        $condition->and_where("$table.$name", '=', (int)$value);
                        break;
                }
            }
        }
        
        if (isset($search_params['section']) && $search_params['section']->id !== NULL)
        {            
            // find products from specified section
            $section = $search_params['section'];

            $prodsec_table = DbTable::instance('ProductSection')->table_name();
            $condition
                ->and_where("$prodsec_table.section_id", '=', (int) $section->id);

            $params['join_productsection'] = TRUE;
        }
        if (isset($search_params['sectiongroup']))
        {            
            $sectiongroup_table = Model_Mapper::factory('Model_SectionGroup_Mapper')->table_name();

            $condition->and_where("$sectiongroup_table.id", '=', $search_params['sectiongroup']->id);
            
            $params['join_sectiongroup']   = TRUE;
        }
        
        if ( APP =='FRONTEND') {
            $condition->and_where("$table.visible", '=', TRUE);

            $condition->and_where("$table.active", '=', TRUE);

            $today_datetime = new DateTime("now");
            $today_datetime->setTimezone(new DateTimeZone("UTC"));

            $today_datetime->sub(new DateInterval(Model_Product::DURATION_1));

			if(!(isset($search_params['calendar']) && $search_params['calendar'] == Model_Product::CALENDAR_ARCHIVE))
				$condition->and_where(DB::expr('TIMEDIFF('.$this->_db->table_prefix()."$table.datetime".','.$this->_db->quote($today_datetime->format(Kohana::config('datetime.db_datetime_format'))).')>0'));        
            else
			{
				$condition->and_where(DB::expr('TIMEDIFF('.$this->_db->quote($today_datetime->format(Kohana::config('datetime.db_datetime_format'))).','.$this->_db->table_prefix()."$table.datetime".')>0'));
				$params['join_telemost'] = TRUE;
			}
            
            if (Modules::registered('area') && !isset($search_params['all_towns']))
            {
                $telemost_table = Model_Mapper::factory('Model_Telemost_Mapper')->table_name();
                $place_table = Model_Mapper::factory('Model_Place_Mapper')->table_name();
                $telemost_place_table = $place_table."_telemost";
                
                $condition->and_where_open()
                        ->or_where("$place_table.town_id", '=', Model_Town::current()->id)                
                        ->or_where_open()
                            ->and_where(DB::expr("$telemost_place_table.town_id"), '=', Model_Town::current()->id)
                            ->and_where(DB::expr($this->_db->table_prefix().$telemost_table.'.active'), '=', TRUE)
                        ->or_where_close()
                        ->and_where_close();

                $params['join_place']   = TRUE;               
                $params['join_telemostplace']   = TRUE;
            }            
           
        }
        
        
        if (isset($search_params['section_active']))
        {
            $section_table = Model_Mapper::factory('Model_Section_Mapper')->table_name();
            $condition->and_where("$section_table.active", '=', (int) $search_params['section_active']);
            
            $params['join_section'] = TRUE;
        }

        if (isset($search_params['calendar']))
        {
            $key = $search_params['calendar'];
            if($key != Model_Product::CALENDAR_TODAY && array_key_exists($key,Model_Product::$_calendar_options ))
            {
                $needTime = new DateTime("now");
                $needTime->setTimezone(new DateTimeZone("UTC"));
                $needTime->add(new DateInterval($key));

                $condition->and_where(DB::expr('TIMEDIFF('.$this->_db->table_prefix()."$table.datetime".','.$this->_db->quote($needTime->format(Kohana::config('datetime.db_datetime_format'))).')>0'));
            }

        }
        
        if ( ! $condition->is_empty())
        {
            return array($condition, $params);
        }
        else
        {
            return array(NULL, $params);
        }
    }

    /**
     * Find all models by criteria and return them in {@link Models} container
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Models|array
     */
    public function find_all_by(
        Model                         $model,
                                      $condition = NULL,
        array                         $params = NULL,
        Database_Query_Builder_Select $query = NULL
    )
    {
        $table = $this->table_name();

        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->distinct('whatever')
                ->from($table);
        }
        
        // ----- process contition
        if (is_array($condition) &&  ! empty($condition['ids']))
        {
            // find product by several ids
            $query->where("$table.id", 'IN', DB::expr('(' . implode(',', $condition['ids']) . ')'));

            unset($condition['ids']);
        }

        if ( is_array($condition) && isset($condition['section_active']))
        {
            $params['join_section']        = TRUE;
            
            $section_table = Model_Mapper::factory('Model_Section_Mapper')->table_name();
            $query->where("$section_table.active", '=', (int) $condition['section_active']);

            unset($condition['section_active']);
        }
        
        if ( is_array($condition) && isset($condition['site_id']))
        {
            $params['join_sectiongroup']   = TRUE;
        }

        if (is_array($condition) && isset($condition['plist']))
        {
            // Select products from the list
            $plistproduct_table = Model_Mapper::factory('Model_PlistProduct_Mapper')->table_name();

            $query
                ->join("$plistproduct_table", 'INNER')
                    ->on("$plistproduct_table.product_id", '=', "$table.id")
                    ->on("$plistproduct_table.plist_id", '=', DB::expr((int)$condition['plist']->id));

            unset($condition['plist']);
        }
        
        if (is_array($condition) && isset($condition['section']))
        {           
            // Find a product which is bound to the specified section
            $prodsec_table = DbTable::instance('ProductSection')->table_name();
            $query
                ->where("$prodsec_table.section_id", '=', (int) $condition['section']->id);

            $params['join_productsection'] = TRUE;

            unset($condition['section']);
        }

        
        if ( ! empty($params['with_image']))
        {
            // Select images for product via join
            $image_table = Model_Mapper::factory('Model_Image_Mapper')->table_name();

            // Number of the thumbnail to select
            $i = $params['with_image'];

            // Additinal columns
            $query->select(
                array("$image_table.image$i",  'image'),
                array("$image_table.width$i",  'image_width'),
                array("$image_table.height$i", 'image_height')
            );

            $query
                ->join($image_table, 'LEFT')
                    ->on("$image_table.owner_id", '=', "$table.id")
                    ->on("$image_table.owner_type", '=', DB::expr("'product'"))

               ->join(array($image_table, 'img'), 'LEFT')
                    ->on(DB::expr("img.owner_id"), '=', "$image_table.owner_id")
                    ->on(DB::expr("img.owner_type"), '=', DB::expr("'product'"))
                    ->on(DB::expr("img.position"), '<', "$image_table.position")
               ->where(DB::expr('ISNULL(img.id)'), NULL, NULL);

        }

        if ( ! empty($params['with_properties']))
        {
            // Select product with additional (non-system) property values
            // @TODO: use only properties for current section
            $properties = Model::fly('Model_Property')->find_all_by_site_id_and_system(Model_Site::current()->id, 0, array(
                'columns'  => array('id', 'name'),
                'order_by' => 'position',
                'desc'     => FALSE,
                'as_array' => TRUE
            ));

            $table = $this->table_name();
            $propertyvalue_table = Model_Mapper::factory('PropertyValue_Mapper')->table_name();

            foreach ($properties as $property)
            {
                $id = (int) $property['id'];

                // Add column to query
                $query->select(array(DB::expr("propval$id.value"), $property['name']));
                
                $query->join(array($propertyvalue_table, "propval$id"), 'LEFT')
                    ->on(DB::expr("propval$id.property_id"), '=', DB::expr($id))
                    ->on(DB::expr("propval$id.product_id"), '=', "$table.id");
            }
        }

        if ( ! empty($params['with_sections']))
        {
            $section_table = Model_Mapper::factory('Model_Section_Mapper')->table_name();
            $prodsec_table = DbTable::instance('ProductSection')->table_name();

            $subq = DB::select('GROUP_CONCAT("s".id, \'-\', "s".sectiongroup_id)')
                ->from(array($section_table, 's'))
                ->join(array($prodsec_table, 'ps'), 'INNER')
                    ->on('"ps".section_id', '=', '"s".id')
                    ->on('"ps".distance', '=', DB::expr('0'))
                ->where('"ps".product_id', '=', DB::expr($this->get_db()->quote_identifier("$table.id")))
                ->group_by('"ps".product_id');

            $query->select(array($subq, 'sections'));
        }
        
        // ----- joins
        $this->_apply_joins($query, $params);
        
        return parent::find_all_by($model, $condition, $params, $query);
    }

    /**
     * Count products by condition
     * 
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @return integer
     */
    public function count_by(Model $model, $condition = NULL, array $params = NULL)
    {
        if ( is_array($condition) && isset($condition['owner'])) {
            return parent::count_by($model, $condition);
        }
        $table = $this->table_name();
        
        $query = DB::select(array('COUNT(DISTINCT "' . $table . '.id")', 'total_count'))
            ->from($table);
        
        // ----- condition
        if ( is_array($condition) && isset($condition['site_id']))
        {
            $params['join_sectiongroup']   = TRUE;
        }
        
        // ----- joins
        $this->_apply_joins($query, $params);

        if ($condition !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $query->where($condition, NULL, NULL);
        }

        $count = $query->execute($this->get_db())
            ->get('total_count');

        return (int) $count;
    }

    /**
     * Apply joins to the query
     *
     * @param Database_Query_Builder_Select $query
     * @param array $params
     */
    protected function _apply_joins(Database_Query_Builder_Select $query, array $params = NULL)
    {
        $table = $this->table_name();

        if ( ! empty($params['join_sectiongroup']))
        {
            $params['join_productsection'] = TRUE;
            $params['join_section']        = TRUE;
        }
        elseif ( ! empty($params['join_section']))
        {
            $params['join_productsection'] = TRUE;
        }
        
        if ( ! empty($params['join_productsection']))
        {
            $prodsec_table = DbTable::instance('ProductSection')->table_name();

            $query
                ->join($prodsec_table, 'INNER')
                    ->on("$prodsec_table.product_id", '=', "$table.id");

            if ( ! empty($params['join_section']))
            {
                $section_table = Model_Mapper::factory('Model_Section_Mapper')->table_name();

                $query
                    ->join($section_table, 'INNER')
                        ->on("$section_table.id", '=', "$prodsec_table.section_id");

                //@WARN: DO NOT join sectiongroup on $prodsec_table - sectiongroup_id column of $prodsec_table is corrupted
                // and can contain NULL values
                if ( ! empty($params['join_sectiongroup']))
                {
                    $sectiongroup_table = Model_Mapper::factory('Model_SectionGroup_Mapper')->table_name();

                    $query
                        ->join($sectiongroup_table, 'INNER')
                            ->on("$sectiongroup_table.id", '=', "$section_table.sectiongroup_id");
                }
            }
        }
        if ( ! empty($params['join_telemostplace'])) 
        {        
            $telemost_table = Model_Mapper::factory('Model_Telemost_Mapper')->table_name();
            $place_table = Model_Mapper::factory('Model_Place_Mapper')->table_name();
            $telemost_place_table = $place_table."_telemost";             

            $query
                ->join($telemost_table, 'LEFT')
                    ->on("$telemost_table.product_id", '=', "$table.id")
                ->join(array($place_table,$telemost_place_table), 'LEFT')
                    ->on(DB::expr("$telemost_place_table.id"), '=', "$telemost_table.place_id");
        }
        if ( ! empty($params['join_place'])) 
        {        
            $place_table = Model_Mapper::factory('Model_Place_Mapper')->table_name();

            $query
                ->join($place_table, 'LEFT')
                    ->on("$place_table.id", '=', "$table.place_id");
        }      
        if ( ! empty($params['join_telemost'])) 
        {        
            $telemost_table = Model_Mapper::factory('Model_Telemost_Mapper')->table_name();

            $query
                ->join($telemost_table, 'inner')
                    ->on("$telemost_table.product_id", '=', "$table.id");
        }
		
    }
    
    /**
     * Tie product to the selected section and it's parents
     *
     * @param Model_Product $product
     * @param Model_Section $section
     * @param boolean $check_already_linked
     */
    public function link_to_section(Model_Product $product, Model_Section $section, $check_already_linked = TRUE)
    {
        // section parents
        $sections = $section->get_parents(array('columns' => array('id', 'lft', 'rgt', 'level', 'sectiongroup_id'), 'as_array' => TRUE));
        // and the section itself
        $sections[] = $section->properties();

        $prodsec_table = DbTable::instance('ProductSection');

        foreach ($sections as $sec)
        {
            $distance = $section->level - $sec['level'];

            if (    $check_already_linked
                 && $prodsec_table->exists(
                        DB::where('product_id', '=', $product->id)
                      ->and_where('section_id', '=', $sec['id'])
                      ->and_where('distance', '=', $distance)
                    ) )
                 continue; // already linked to this section with the same distance

            $prodsec_table->insert(array(
                'product_id'      => $product->id,
                'section_id'      => $sec['id'],
                'distance'        => $distance,
                'sectiongroup_id' => $section->sectiongroup_id
            ));
        }        
    }

    /**
     * Link product to sections from the specified sectiongroup
     *
     * @param Model_Product $product
     * @param integer $sectiongroup_id
     * @param array $section_ids
     */
    public function link_to_sections(Model_Product $product, $sectiongroup_id, $section_ids)
    {
        DbTable::instance('ProductSection')
            ->delete_rows(DB::where('product_id', '=', $product->id)->and_where('sectiongroup_id', '=', $sectiongroup_id));

        $section = new Model_Section();
        foreach ($section_ids as $section_id)
        {
            $section->find($section_id, array('columns' => array('id', 'lft', 'rgt', 'level', 'sectiongroup_id')));
            if (isset($section->id))
            {
                $this->link_to_section($product, $section);
            }
        }
    }

    /**
     * Update links for products, linked to the given section.
     * If section is not specified, all products in the catalog are relinked
     * 
     * @param Model_Section $section
     */
    public function relink(Model_Product $product, Model_Section $section = NULL)
    {     
        $loop_prevention = 1000;
        do {

            $params = array(
                'columns' => array('id', 'section_id'),
                'with_sections' => TRUE,

                'batch' => 100                
            );
            
            if ($section !== NULL)
            {
                $products = Model::fly('Model_Product')->find_all_by_section($section, $params);                
            }
            else
            {
                $products = Model::fly('Model_Product')->find_all_by_site_id(Model_Site::current()->id, $params);
            }

            foreach ($products as $product)
            {
                $product->update_section_links();
            }

        }
        while (count($products) && $loop_prevention-- > 0);
        if ($loop_prevention <= 0)
            throw new Kohana_Exception('Possible infinite loop in :method', array(':method' => __METHOD__));
    }

    /**
     * Unlink all products from the specified section
     *
     * @param Model_Section $section
     */
    public function unlink_all_from_section(Model_Product $product, Model_Section $section)
    {
        $loop_prevention = 1000;
        do {

            $products = Model::fly('Model_Product')->find_all_by_section($section, array(
                'columns' => array('id', 'section_id'),
                'with_sections' => TRUE,

                'batch' => 100
            ));

            foreach ($products as $product)
            {
                $sections = $product->sections;
                unset($sections[$section->sectiongroup_id][$section->id]);
                $product->sections = $sections;
                
                $product->update_section_links();
            }

        }
        while (count($products) && $loop_prevention-- > 0);
        if ($loop_prevention <= 0)
            throw new Kohana_Exception('Possible infinite loop in :method', array(':method' => __METHOD__));
    }
}
