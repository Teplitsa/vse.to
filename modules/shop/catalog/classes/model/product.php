<?php defined('SYSPATH') or die('No direct script access.');

class Model_Product extends Model_Res
{
    const SHORT_DESC_WORDS = 25;

    const LINKS_LENGTH = 36;

    //Time before starting the event when Video Platform becomes available (in minutes)
    const SHOW_TIME = 10;
    
    // TRUE if the video provider is COMDI
    const COMDI = TRUE;

    // available phases of COMDI events
    const DEF_STAGE = 'wait';
    const ACTIVE_STAGE = 'active';    
    const START_STAGE = 'start';
    const STOP_STAGE = 'stop';
    
    // duration options
    const DURATION_1 = 'PT30M';
    const DURATION_2 = 'PT1H';
    const DURATION_3 = 'PT1H30M';
    const DURATION_4 = 'PT2H';
    const DURATION_5 = 'PT2H30M';
    const DURATION_6 = 'PT3H';
    const DURATION_7 = 'PT3H30M';
    const DURATION_8 = 'PT4H';
    const DURATION_9 = 'PT4H30M';
    const DURATION_10 = 'PT5H';

    // theme options    
    const THEME_1 = 1;
    const THEME_2 = 2;
    const THEME_3 = 3;
    const THEME_4 = 4;
    const THEME_5 = 5;
    const THEME_6 = 6;
    const THEME_7 = 7;
    const THEME_8 = 8;
    const THEME_9 = 9;
    const THEME_10 = 10;    
    const THEME_11 = 11;    
    const THEME_12 = 12;    
    const THEME_13 = 13;    

    // FORMAT options    
    const FORMAT_1 = 1;
    const FORMAT_2 = 2;
    const FORMAT_3 = 3;
    const FORMAT_4 = 4;
    const FORMAT_5 = 5;
    const FORMAT_6 = 6;
    const FORMAT_7 = 7;
    const FORMAT_8 = 8;
    const FORMAT_9 = 9;
    const FORMAT_10 = 10;  
    
    // CALENDAR options    
    const CALENDAR_1 = 'P1W';
    const CALENDAR_2 = 'P2W';
    const CALENDAR_3 = 'P1M';
    
    const INTERACT_LIVE = 1;
    const INTERACT_STREAM = 2;

    const CHOALG_RANDOM = 1;
    const CHOALG_MANUAL = 2;    
    public static $date_as_timestamp = FALSE;       

    public static $_duration_options = array(
        self::DURATION_1    => '0.30 ч.',
        self::DURATION_2    => '1.00 ч.',
        self::DURATION_3    => '1.30 ч.',
        self::DURATION_4    => '2.00 ч.',
        self::DURATION_5    => '2.30 ч.',
        self::DURATION_6    => '3.00 ч.',
        self::DURATION_7    => '3.30 ч.',
        self::DURATION_8    => '4.00 ч.',
        self::DURATION_9    => '4.30 ч.',
        self::DURATION_10   => '5.00 ч.'
     ); 
      
    public static $_theme_options = array(
        self::THEME_1    => 'дизайн',
        self::THEME_2    => 'архитектура',
        self::THEME_3    => 'медиа',
        self::THEME_4    => 'общество',
        self::THEME_5    => 'искусство',
        self::THEME_6    => 'бизнес',
        self::THEME_7    => 'город',
        self::THEME_8    => 'технологии',
        self::THEME_9    => 'наука',
        self::THEME_10   => 'языки',
        self::THEME_11   => 'культура',
        self::THEME_12   => 'фотография',
        self::THEME_13   => 'кино'
     ); 

    public static $_format_options = array(
        self::FORMAT_1    => 'лекция',
        self::FORMAT_2    => 'мастер-класс',
        self::FORMAT_3    => 'экскурсия',
        self::FORMAT_4    => 'показ',
        self::FORMAT_5    => 'круглый стол',
        self::FORMAT_6    => 'обсуждение',
        self::FORMAT_7    => 'конференция',
        self::FORMAT_8    => 'семинар',
        self::FORMAT_9    => 'встреча',
        self::FORMAT_10   => 'дебаты'
     ); 
   
    public static $_calendar_options = array(
        self::CALENDAR_1    => 'через неделю',
        self::CALENDAR_2    => 'через две недели',
        self::CALENDAR_3    => 'через месяц',
     );    
    
    public static $_interact_options = array(
        self::INTERACT_LIVE            => 'Телемост',                
        self::INTERACT_STREAM          => 'Стриминг'
    );    
    
    public static $_numviews_options = array(
        1          => '1',                
        2          => '2',
        3          => '3',
        4          => '4',
        5          => '5'
    );
    
    public static $_choalg_options = array(
        self::CHOALG_RANDOM     => 'алгоритм(случайный выбор)',
        self::CHOALG_MANUAL     => 'автор анонса'
    );
    
    public $backup = array('sections');

    /**
     * Get currently selected product (FRONTEND)
     *
     * @param  Request $request (if NULL, current request will be used)
     * @return Model_Product
     */
    public static function current(Request $request = NULL)
    {
        if ($request === NULL)
        {
            $request = Request::current();
        }

        // cache?
        $product = $request->get_value('product');
        if ($product !== NULL)
            return $product;

        $product = new Model_Product();

        if ($request->param('alias') != '' && Model_Section::current()->id !== NULL)
        {
            $product->find_by_alias_and_section_and_active(
                $request->param('alias'),
                Model_Section::current(),
                1,
                array('with_image' => '3')
            );
        }

        $request->set_value('product', $product);
        return $product;
    }   

    /**
     * Get frontend uri to the product
     */
    public function uri_frontend(Model_Section $parent_section = NULL, $stage = NULL)
    {        
        $uri_params['sectiongroup_name'] = '{{sectiongroup_name}}';
        $uri_params['path'] = '{{path}}';
        $uri_params['alias'] = '{{alias}}';
        if ($stage) $uri_params['stage'] = $stage;

        $uri_template = URL::uri_to('frontend/catalog/product', $uri_params);
        
        if ($parent_section !== NULL)
        {
            $sections = Model::fly('Model_Section')->find_all_active_cached($parent_section->sectiongroup_id, FALSE);

            // Select the section that will be used to build an uri to product
            // (there can be several sections to which this product is linked, so we have to select only one)
            if (isset($this->sections[$parent_section->sectiongroup_id]) && isset($sections))
            {
                // Choose the first section to which this product is linked and which is also a descendant of $parent_section
                $section_ids = array_keys(array_filter($this->sections[$parent_section->sectiongroup_id]));

                foreach ($section_ids as $section_id)
                {
                    $sec = $sections[$section_id];
                    if ($sec->id == $parent_section->id || $sections->is_descendant($sec, $parent_section))
                    {
                        $section = $sec;
                        break;
                    }
                }
            }
        }
        
        if ( ! isset($section))
        {
            // Igonre parent_section and simply choose the main section for product

            // try cache
//            foreach (Model::fly('Model_SectionGroup')->find_all_by_site_id(Model_Site::current()->id) as $sectiongroup)
            foreach (Model::fly('Model_SectionGroup')->find_all_cached() as $sectiongroup)
            {
                $sections = Model::fly('Model_Section')->find_all_active_cached($sectiongroup->id, FALSE);
                if (isset($sections[$this->section_id]))
                {
                    $section = $sections[$this->section_id];
                    break;
                }
            }
            
            if ( ! isset($section))
            {
                // finally, load from db
                $section = new Model_Section();
                $section->find($this->section_id);
            }
        }

        return  str_replace(
            array('{{sectiongroup_name}}', '{{path}}', '{{alias}}'),
            array($section->sectiongroup_name, $section->full_alias, $this->alias),
            $uri_template
        );
    }
    
    /**
     * Make alias for product from it's marking or id
     *
     * @return string
     */
    public function make_alias()
    {
        $marking_alias = str_replace(' ', '_', strtolower(l10n::transliterate($this->marking)));
        $marking_alias = preg_replace('/[^a-z0-9_-]/', '', $marking_alias);

        if ($marking_alias == '')
        {
            // If there is no marking - use product's id
            $marking_alias = 'event_' . rand(1,9999);//$this->id;
        }

        $i = 0;
        $loop_prevention = 1000;
        do {

            if ($i > 0)
            {
                $alias = substr($marking_alias, 0, 30);
                $alias .= $i;
            }
            else
            {
                $alias = substr($marking_alias, 0, 31);
            }

            $i++;
        }
        while ($this->exists_another_by_alias($alias) && ($loop_prevention-- > 0));

        if ($loop_prevention <= 0)
            throw new Kohana_Exception ('Possible infinite loop in :method', array(':method' => __METHOD__));

        return $alias;
    }
    /**
     * @return boolean
     */
    public function default_active()
    {
        if (APP === 'BACKEND')
        {
            return TRUE;
        }        
        return FALSE;
    }

    public function default_interact()
    {
        return self::INTERACT_LIVE;
    }    
    
    public function default_choalg()
    {
        return self::CHOALG_RANDOM;
    }      
    
    /**
     * @return boolean
     */
    public function default_visible()
    {        
        return TRUE;
    }


    /**
     * @return integer
     */
    public function default_import_id()
    {
        return 0;
    }

    /**
     * Get additional sections this product belongs to for each sectiongroup
     *
     * @return array array(sectiongroup_id=>section_id=>1/0)
     */
    public function get_sections()
    {   
        if ( ! isset($this->_properties['sections']))
        {            
            $result = array();

            if (isset($this->id))
            {
                $sections = Model::fly('Model_Section')->find_all_by_product($this, array(
                    'order_by' => 'lft',
                    'desc' => FALSE,
                    'columns' => array('id', 'sectiongroup_id'))
                );
                
                foreach($sections as $section)
                {
                    $result[$section->sectiongroup_id][$section->id] = 1;
                }
            }

            $this->_properties['sections'] = $result;
        }
        elseif (is_string($this->_properties['sections']))
        {
            // section ids were concatenated with GROUP_CONCAT
            $sections = array();
            
            $ids = explode(',', $this->_properties['sections']);
            foreach ($ids as $id_gid)
            {
                $section_id = strtok($id_gid, '-');
                $sectiongroup_id = strtok('-');

                $sections[$sectiongroup_id][$section_id] = TRUE;
            }
            
            $this->_properties['sections'] = $sections;
        }

        // Add main section, if neccessary
        if (is_array($this->_properties['sections']))
        {
            $add_main_section = TRUE;
            
            foreach ($this->_properties['sections'] as $sectiongroup_id => $sections)
            {
                if ( ! empty($sections[$this->section_id]))
                {
                    // Main section is already present in the linked sections
                    $add_main_section = FALSE;
                }
            }
            if ($add_main_section)
            {
                $this->_properties['sections'][$this->section->sectiongroup_id][$this->section_id] = 1;
            }
        }
        
        return $this->_properties['sections'];
    }

    /**
     * Get product section ids
     *
     * @param  integer $sectiongroup_id If specified then section ids only for this section group are returned
     * @return array
     */
    public function get_section_ids($sectiongroup_id = NULL)
    {
        if (isset($sectiongroup_id))
        {
            if (!isset($this->sections[$sectiongroup_id])) return array();
            return array_keys(array_filter($this->sections[$sectiongroup_id]));
        }
        else
        {
            $section_ids = array();
            if (isset($this->sections))
            {
                foreach ($this->sections as $sectiongroup_id => $sections)
                {
                    $section_ids[$sectiongroup_id] = array_keys(array_filter($sections));
                }
            }
            return $section_ids;
        }
    }

    /**
     * Get active[!] additional properties for this product
     */
    public function get_properties()
    {        
        if ( ! isset($this->_properties['properties']))
        {
            if ( ! empty($this->_properties['section_id']))
            {
                $properties = Model::fly('Model_Property')->find_all_by_section_id_and_active_and_system(
                    $this->_properties['section_id'], 1, 0, 
                    array('order_by' => 'position', 'desc' => FALSE)
                );

                $this->_properties['properties'] = $properties;
            }
            else
            {
                return array();
            }
        }
        return $this->_properties['properties'];
    }

    /**
     * Get main section for product
     *
     * @param  array $params
     * @return Model_Section
     */
    public function get_section(array $params = NULL)
    {
        if ( ! isset($this->_properties['section']))
        {
            $section = new Model_Section();
            $section->find((int) $this->section_id, $params);
            $this->_properties['section'] = $section;
        }
        return $this->_properties['section'];
    }   

    public function get_place(array $params = NULL)
    {
        if ( ! isset($this->_properties['place']))
        {
            $place = new Model_Place();
            $place->find((int) $this->place_id, $params);
            $this->_properties['place'] = $place;
        }
        return $this->_properties['place'];
    } 
    
    public function get_short_desc() {
        return TEXT::limit_words($this->description, self::SHORT_DESC_WORDS);
    }
    /**
     * Get product list ids to which this product belongs
     *
     * @return array array(plist_id=>1/0)
     */
    public function get_plist_ids()
    {
        if ( ! isset($this->_properties['plist_ids']))
        {
            $plist_ids = array();

            if (isset($this->id))
            {
                $plistproducts = Model::fly('Model_PListProduct')->find_all_by_product_id(
                    (int) $this->id,
                    array('columns' => array('plist_id'), 'as_array' => TRUE)
                );

                foreach ($plistproducts as $plistproduct)
                {
                    $plist_ids[$plistproduct['plist_id']] = 1;
                }
            }

            $this->_properties['plist_ids'] = $plist_ids;
        }
        return $this->_properties['plist_ids'];
    }

    /**
     * Set event datetime
     *
     * @param DateTime|string $value
     */
    public function set_datetime($value)
    {
        if ($value instanceof DateTime)
        {
            $this->_properties['datetime'] = clone $value;
        }
        else
        {
            $this->_properties['datetime'] = new DateTime($value);
        }
    }

    /**
     * Get event datetime
     * 
     * @return DateTime
     */
    public function get_datetime()
    {
        if ( ! isset($this->_properties['datetime']))
        {
            if (empty($this->_properties['id']))
            {
                // Default value for a new event
                $this->_properties['datetime'] = new DateTime();
            }
            else
            {
                return NULL;
            }
        }
        return clone $this->_properties['datetime'];
    }
    
    /**
     * Get event date as string
     *
     * @return string
     */
    public function get_date()
    {
        return $this->datetime->format(Kohana::config('datetime.datetime_format'));
    }    

    /**
     * Get event time as string
     *
     * @return string
     */
    public function get_time()
    {
        return $this->datetime->format(Kohana::config('datetime.time_format'));
    }
    
    public function get_date_front()
    {
        return l10n::rdate(Kohana::config('datetime.date_format_front'),$this->datetime->format('U'));
    }

    public function get_datetime_front()
    {
        return l10n::rdate(Kohana::config('datetime.datetime_format_front'),$this->datetime->format('U'));
    }    

    public function get_weekday()
    {
        return l10n::rdate(' D',$this->datetime->format('U'));
    }
    
    public function get_time_front()
    {
        return l10n::rdate(Kohana::config('datetime.time_format_front'),$this->datetime->format('U'));
    }
    
    
    /**
     * Get the event lecturer (if this event is personal)
     * 
     * @return Model_Lecturer
     */
    public function get_lecturer()
    {
        $lecturer = new Model_Lecturer();
        $lecturer->find((int) $this->lecturer_id);
        return $lecturer;
    }
    
    /**
     * Get the event lecturer name (if this event is personal)
     * 
     * @return Model_Lecturer
     */
    public function get_lecturer_name()
    {
        if ( ! isset($this->_properties['lecturer_name'])) {
            $this->_properties['lecturer_name'] = $this->get_lecturer()->name; 
        }
             
        return $this->_properties['lecturer_name'];
    }

    /**
     * Get the event lecturer (if this event is personal)
     * 
     * @return Model_Organizer
     */
    public function get_organizer()
    {
        $organizer = new Model_Organizer();
        $organizer->find((int) $this->organizer_id);
        return $organizer;
    }
    
    /**
     * Get the organizator of the event
     * 
     * @return Model_Lecturer
     */
    public function get_organizer_name()
    {
        if ( ! isset($this->_properties['organizer_name'])) {
            $this->_properties['organizer_name'] = $this->get_organizer()->name; 
        }
             
        return $this->_properties['organizer_name'];
    }
    
    /*public function show_time()
    {
        $numMinutes = self::SHOW_TIME;
        $current_datetime = new DateTime();
        $current_datetime->modify("+{$numMinutes} minutes");
        return ($current_datetime->format('U') > $this->datetime->format('U'));
    }*/
    
    /*public function stage()
    {
        if ( ! isset($this->_properties['stage']))
        {
            $stage = self::DEF_STAGE;
            if ($this->show_time() && self::COMDI && isset($this->event_id)) {
                $stage = self::START_STAGE;
                $actual_stage = TaskManager::start('comdi_status',
                    Task_Comdi_Base::mapping(array('event_id' => $this->event_id)));

                if ($actual_stage !== NULL) {
                    $stage = $actual_stage;
                }
            }
            $this->_properties['stage'] = $stage;
        }
        return $this->_properties['stage'];
    }*/

    public function stage()
    {
        $stage = self::START_STAGE;
        $actual_stage = TaskManager::start('comdi_status',
            Task_Comdi_Base::mapping(array('event_id' => $this->event_id)));

        if ($actual_stage !== NULL) {
            $stage = $actual_stage;
        }
        return $stage;
    }
    
    public function change_stage($stage) {
        if (!self::COMDI || $this->user_id != Model_User::current()->id) {
            return FALSE;
        }
        $event_id = FALSE;

        switch ($stage) {
            case Model_Product::START_STAGE:
                if ($this->stage() == Model_Product::ACTIVE_STAGE) {
                    $event_id = TaskManager::start('comdi_start',
                            Task_Comdi_Base::mapping(array('event_id' => $this->event_id)));
                }
                break;
            case Model_Product::STOP_STAGE:
                if ($this->stage() == Model_Product::START_STAGE) {
                    $event_id = TaskManager::start('comdi_stop',
                            Task_Comdi_Base::mapping(array('event_id' => $this->event_id)));
                }
                if ($event_id == $this->event_id) {
                    $this->active = 0;
                    $this->save(FALSE,FALSE,FALSE,TRUE);
                }
                break;
            default:
                return FALSE;
        }
        return $event_id;
    }
    /**
     * Link/unlink product to/from given section WITHOUT updating stats
     *
     * @param string $mode
     * @param Model_Section $section
     */
    public function link($mode, Model_Section $section)
    {
        $sections = $this->sections;

        switch ($mode)
        {
            case 'link':
                // link product to section
                $sections[$section->sectiongroup_id][$section->id] = 1;

                break;

            case 'link_main':
                // unlink from the old main section
                unset($sections[$this->section->sectiongroup_id][$this->section->id]);

                // link to the new main section
                $sections[$section->sectiongroup_id][$section->id] = 1;
                $this->section_id = $section->id;

                break;

            case 'unlink':
                // unlink product from section, if it is not its main section
                if ($section->id != $this->section_id)
                {
                    unset($sections[$section->sectiongroup_id][$section->id]);
                }
                break;

            default:
                throw new Kohana_Exception('Unknown mode ":mode" passed to :method',
                    array(':mode' => $mode, ':method' => __METHOD__));
        }

        $this->sections = $sections;
        $this->save(FALSE, TRUE, FALSE, FALSE, FALSE);
    }

    public function image($size = NULL) {
        $image_info = array();
        $image = Model::fly('Model_Image')->find_by_owner_type_and_owner_id('product', $this->id, array(
            'order_by' => 'position',
            'as_array' => true,
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

    /**
     * Validate creation/updation of product
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {  
        $valid = parent::validate($newvalues);
        if (!$valid) return FALSE;
        
        if (!isset($newvalues['organizer_id']) || $newvalues['organizer_id'] == NULL) {
            $this->error('Указана несуществующая организация!');
            return FALSE;
        }

        if (!isset($newvalues['place_id']) || $newvalues['place_id'] == NULL) {
            $this->error('Указана несуществующая площадка!');
            return FALSE;
        }
        
        if (isset($newvalues['user_id']) && isset($newvalues['place_id'])) {
            $user = Model::fly('Model_User')->find($newvalues['user_id']);
            $place = Model::fly('Model_Place')->find($newvalues['place_id']);
            
            if ($user->town_id != $place->town_id) 
            {
                $this->error('Площадка должна располагаться в городе представителя!');                
                return FALSE;
            }
        }
        
        $allvalues = array_merge($this->values(),$newvalues);
        $event_id = NULL;
        $comdi_uri = NULL;
        
        $dummy_product = new Model_Product($allvalues);
        
        // Create COMDI FOR ANNOUNCE
        if (Model_Product::COMDI === TRUE) {

            if ($dummy_product->event_id == NULL) { 
                $event_id = TaskManager::start('comdi_create', Task_Comdi_Base::mapping($allvalues));               
                $username = isset($dummy_product->user->organization_name)?$dummy_product->user->organization_name:$dummy_product->user->email;
                $event_uri  = TaskManager::start('comdi_register', Task_Comdi_Base::mapping(array(
                    'event_id' => $event_id,
                    'role' => 'administrator',
                    'username' => $username,
                )));                
                if ($event_id === TRUE) {
                    return FALSE;
                }            
                if ($event_id === NULL || $event_uri === NULL) {
                    $this->error('Сервис COMDI временно не работает!');
                    return FALSE;
                }
                $this->event_id = $event_id;
                $this->event_uri = $event_uri;
            } else {
                $event_id = TaskManager::start('comdi_update', Task_Comdi_Base::mapping($allvalues));
                if ($event_id === TRUE) {
                    return FALSE;
                }            
                if ($event_id === NULL) {
                    $this->error('Сервис COMDI временно не работает!');
                    return FALSE;
                }                
            }
            
        // change datetime_locale according to the town of representative        
                        
            /**
             * @todo
             * 
             * Uncomment When Problem in COMDI API will be Fixed
             */ 
            //$this->event_id = $event_id;
        }
        
        // Create COMDI for EVENT
        ////SROCHNO
        /*if (Model_Product::COMDI === TRUE && $dummy_product->id ==NULL && $dummy_product->type == Model_SectionGroup::TYPE_EVENT &&
                $dummy_product->parent()->event_id != NULL) {
            $username = isset($dummy_product->role->organization)?$dummy_product->role->organization:$dummy_product->role->login;
            $event_uri  = TaskManager::start('comdi_register', Task_Comdi_Base::mapping(array(
                'role'     => 'guest',
                'username' => $username,
                'event_id' => $dummy_product->parent()->event_id
            )));            
            if ($event_uri === NULL) {
                $this->error('Сервис COMDI временно не работает!');
                return FALSE;
            }
            $this->event_uri = $event_uri;
            $this->event_id = $dummy_product->parent()->event_id;
        }*/
        
//        if ((!$dummy_product->id) || (isset($newvalues['change_main_properties']) &&
//            $newvalues['change_main_properties'] == 1 )) {
//            $this->active = 0;
//            if (APP == 'FRONTEND') {
//                FlashMessages::add('Анонс события "'.$dummy_product->caption .'" отправлен на модерацию', FlashMessages::MESSAGE);        
//            }
//            if (APP == 'BACKEND') {
//                FlashMessages::add('Анонс события"'.$dummy_product->caption .'" сохранен', FlashMessages::MESSAGE);        
//            }
//        } elseif ($dummy_product->id) {
//            FlashMessages::add('Анонс события"'.$dummy_product->caption .'" изменен', FlashMessages::MESSAGE);        
//        }
        
        return TRUE;
    }
    
    /**
     * Save product and link it to selected sections
     */
    public function save(
        $create = FALSE,
        $update_section_links = TRUE,
        $update_additional_properties = TRUE,
        $save_parent_photos = TRUE,                        
        $update_stats = TRUE
    )
    {  
        $create_flag = ($this->id === NULL) || $create;
        
        if ($create_flag) {
            // Generate alias from product's marking
            $this->alias = $this->make_alias();            
        }

        parent::save($create);
        
        if ($this->file['name']) {
            // Delete product images
            Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('product', $this->id);
            
            $image = new Model_Image();
            $image->file = $this->file;
            $image->owner_type = 'product';
            $image->owner_id = $this->id;
            $image->config = 'product';
            $image->save();
        }
        
        if ($update_section_links)
        {
            // Link product to the selected sections
            $this->update_section_links();
        }

        if ($update_additional_properties)
        {
            // Update values for additional properties
            Model_Mapper::factory('PropertyValue_Mapper')->update_values_for_product($this);
        }
        
                
        if ($update_stats)
        {
            // Update product count for affected sections
            $section_ids = array();

            foreach ($this->previous()->sections as $sections)
            {
                $section_ids = array_merge($section_ids, array_keys(array_filter($sections)));
            }
            foreach ($this->sections as $sections)
            {
                $section_ids = array_merge($section_ids, array_keys(array_filter($sections)));
            }

            $section_ids = array_unique($section_ids);

            Model::fly('Model_Section')->mapper()->update_products_count($section_ids);
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
        // Delete COMDI event        
        if (Model_Product::COMDI === TRUE && $this->event_id != NULL) 
        {
            $arr['event_id'] = $this->event_id;
           $event_id = TaskManager::start('comdi_delete', Task_Comdi_Base::mapping($arr));

            if ($event_id === TRUE) {
                return FALSE;
            }               
            if ($event_id === NULL) {
                $this->error('Сервис COMDI временно не работает!');
                return FALSE;
            }
                        
        }
        return parent::validate_delete($newvalues);
    }
    
    /**
     * Delete product
     */
    public function delete($update_stats = TRUE)
    {
        // Delete product telemosts
        $telemosts = Model::fly('Model_Telemost')->find_all_by_product_id($this->id);
        foreach ($telemosts as $telemost) {
            $telemost->delete();
        }
        
        // Delete product images
        Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('product', $this->id);

        // Delete property values for this product
        Model_Mapper::factory('PropertyValue_Mapper')->delete_all_by_product_id($this, $this->id);

        if ($update_stats)
        {
            // Update product count for affected sections
            $section_ids = array();

            foreach ($this->sections as $sections)
            {
                $section_ids = array_merge($section_ids, array_keys(array_filter($sections)));
            }

            $section_ids = array_unique($section_ids);
        }

        // Delete from DB
        parent::delete();

        if ($update_stats)
        {
            // Update product count for affected sections
            Model::fly('Model_Section')->mapper()->update_products_count($section_ids);
        }
    }

    /**
     * Update links to the sections for the product
     */
    public function update_section_links()
    {
        if ( ! isset($this->sections))
            return;

        // Link property to the selected sections
        foreach (array_keys($this->sections) as $sectiongroup_id)
        {
            $this->link_to_sections($sectiongroup_id, $this->get_section_ids($sectiongroup_id));
        }
    }

    /**
     * Delete products by section id
     *
     * @param integer $section_id
     */
    public function delete_all_by_section_id($section_id)
    {
        $loop_prevention = 10000;

        do {
            $products = $this->find_all_by_section_id($section_id, array(
                'columns' => array('id'),
                'batch' => 100
            ));

            foreach ($products as $product)
            {
                // Delete product images
                Model::fly('Model_Image')->delete_all_by_owner_type_and_owner_id('product', $product->id);

                // Delete property values for this product
                Model_Mapper::factory('PropertyValue_Mapper')->delete_all_by_product_id($product, $product->id);               
            }

            $loop_prevention--;
        }
        while (count($products) && $loop_prevention > 0);

        if ($loop_prevention <= 0)
        {
            throw new Kohana_Exception('Possibly an infinte loop while deleting section');
        }

        // Delete products from DB in one query
        $this->mapper()->delete_all_by_section_id($this, $section_id);
    }

    /**
     * Get telemost applications for this product
     */
    public function get_app_telemosts()
    {
        return Model::fly('Model_Telemost')->find_all_by_product_id_and_active((int) $this->id,FALSE);
    }
    
    /**
     * Get telemosts for this product
     */
    public function get_telemosts($town = NULL)
    {
        $conditions['product_id'] = (int)$this->id;
        $conditions['active'] = TRUE;
        if ($town ) $conditions['town'] = $town;

        return Model::fly('Model_Telemost')->find_all_by($conditions);
    }    
    /**
     * Get comments for this product
     */
    public function get_comments()
    {
        return Model::fly('Model_ProductComment')->find_all_by_product_id((int) $this->id);
    }       
}