<?php defined('SYSPATH') or die('No direct script access.');

class Model_PropertySection_Mapper extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('property_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('section_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('active', array('Type' => 'boolean', 'Key' => 'INDEX'));
        $this->add_column('sort',   array('Type' => 'boolean'));
        $this->add_column('filter', array('Type' => 'boolean'));
    }

    /**
     * Link property to given sections
     * 
     * @param Model_Property $property
     * @param array $propsections
     */
    public function link_property_to_sections(Model_Property $property, array $propsections)
    {
        $this->delete_rows(DB::where('property_id', '=', (int) $property->id));
        foreach ($propsections as $propsection)
        {
            $propsection['property_id'] = $property->id;
            $this->insert($propsection);
        }
    }

    /**
     * Link section to given properties
     *
     * @param Model_Section $section
     * @param array $propsections
     */
    public function link_section_to_properties(Model_Section $section, array $propsections)
    {
        $this->delete_rows(DB::where('section_id', '=', (int) $section->id));
        foreach ($propsections as $propsection)
        {
            $propsection['section_id'] = $section->id;
            $this->insert($propsection);
        }
    }

    /**
     * Find all property-section infos by given section
     *
     * @param  Model_PropertySection $propsec
     * @param  Model_Section $section
     * @param  array $params
     * @return Models
     */
    public function find_all_by_section(Model_PropertySection $propsec, Model_Section $section, array $params = NULL)
    {
        $propsec_table  = $this->table_name();
        $property_table = Model_Mapper::factory('Model_Property_Mapper')->table_name();
        $section_table  = Model_Mapper::factory('Model_Section_Mapper')->table_name();

        $columns = isset($params['columns'])
                        ? $params['columns']
                        : array(
                            array("$property_table.id", 'property_id'),
                            "$property_table.caption",
                            "$property_table.system",

                            "$propsec_table.active",
                            "$propsec_table.filter",
                            "$propsec_table.sort"
                        );

        $query = DB::select_array($columns)
            ->from($property_table)
            ->join($propsec_table, 'LEFT')
                ->on("$propsec_table.property_id", '=', "$property_table.id")
                ->on("$propsec_table.section_id", '=', DB::expr((int) $section->id));

        $data = parent::select(NULL, $params, $query);

        // Add section properties
        foreach ($data as & $properties)
        {
            $properties['section_id']      = $section->id;
            $properties['section_caption'] = $section->caption;
        }

        return new Models(get_class($propsec), $data);
    }

    /**
     * Find all property-section infos by given property
     *
     * @param  Model_PropertySection $propsec
     * @param  Model_Property $property
     * @param  array $params
     * @return Models
     */
    public function find_all_by_property(Model_PropertySection $propsec, Model_Property $property, array $params = NULL)
    {
        $propsec_table  = $this->table_name();
        $property_table = Model_Mapper::factory('Model_Property_Mapper')->table_name();
        $section_table  = Model_Mapper::factory('Model_Section_Mapper')->table_name();
        $sectiongroup_table = Model_Mapper::factory('Model_SectionGroup_Mapper')->table_name();

        $columns = isset($params['columns'])
                        ? $params['columns']
                        : array(
                            array("$section_table.id", 'section_id'),
                            array("$section_table.caption", 'section_caption'),

                            "$propsec_table.active",
                            "$propsec_table.filter",
                            "$propsec_table.sort"
                        );

        $query = DB::select_array($columns)
            ->from($section_table)
            ->join($sectiongroup_table, 'INNER')
                ->on("$sectiongroup_table.id", '=', "$section_table.sectiongroup_id")
            ->join($propsec_table, 'LEFT')
                ->on("$propsec_table.section_id", '=', "$section_table.id")
                ->on("$propsec_table.property_id", '=', DB::expr((int) $property->id));            

        $data = parent::select(DB::where("$sectiongroup_table.site_id", '=', $property->site_id), $params, $query);

        // Add property properties
        foreach ($data as & $properties)
        {
            $properties['property_id'] = $property->id;
            $properties['caption']     = $property->caption;
            $properties['system']      = $property->system;
        }
        
        return new Models(get_class($propsec), $data);
    }
}