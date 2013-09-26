<?php defined('SYSPATH') or die('No direct script access.');

class PropertyValue_Mapper extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('product_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('property_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('value',       array('Type' => 'varchar(' . Model_Property::MAXLENGTH . ')'));
    }

    /**
     * Update additional property values for given product
     * 
     * @param Model_Product $product
     */
    public function update_values_for_product(Model_Product $product)
    {        
        foreach ($product->properties as $property)
        {
            $name = $property->name;
            $value = $product->__get($name);

            if ($value === NULL)
                continue;
            
            $where = DB::where('product_id', '=', (int) $product->id)
                ->and_where('property_id', '=', (int) $property->id);

            if ($this->exists($where))
            {
                $this->update(array('value' => $value), $where);
            }
            else
            {
                $this->insert(array(
                    'product_id'  => (int) $product->id,
                    'property_id' => (int) $property->id,
                    'value'       => $value
                ));
            }
        }
    }
}