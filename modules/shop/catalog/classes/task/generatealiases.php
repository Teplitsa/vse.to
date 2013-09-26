<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Regenerate aliases for products and sections
 */
class Task_GenerateAliases extends Task
{
    /**
     * Run the task
     */
    public function run()
    {
        // ----- Sections
        $this->set_status_info('Генерация алиасов для разделов');

        $section = new Model_Section;

        $count = $section->count(); $i = 0;
        
        $loop_prevention = 1000;
        do {
            $sections = $section->find_all(array(
                'batch' => 100,
                'columns' => array('id', 'lft', 'rgt', 'level', 'caption', 'section_active'),
                'as_array' => TRUE
            ));

            foreach ($sections as $properties)
            {
                // update progress for every 10th section
                if ($i % 10 == 0)
                {
                    $this->set_status_info('Генерация алиасов для разделов : ' . $i . ' из ' . $count);
                    $this->set_progress((int) (100 * $i / $count));
                }
                $i++;


                // alias will be regenerated on save
                $section->init($properties);

                // [!!!] @FIXME: save breaks property-section links
                //$section->save(FALSE, FALSE);
            }
        }
        while (count($sections) && ($loop_prevention-- > 0));

        if ($loop_prevention <= 0)
            throw new Kohana_Exception ('Possible infinite loop in :method', array(':method' => __METHOD__));

        // ----- Products
        $this->set_status_info('Генерация алиасов для товаров');

        $product = new Model_Product;

        $count = $product->count(); $i = 0;

        $loop_prevention = 1000;
        do {
            $products = $product->find_all(array(
                'batch' => 100,
                'columns' => array('id', 'section_id', 'marking', 'price', 'active'),
                'as_array' => TRUE
            ));
            

            foreach ($products as $properties)
            {
                // update progress for every 50th product
                if ($i % 50 == 0)
                {
                    $this->set_status_info('Генерация алиасов для товаров : ' . $i . ' из ' . $count);
                    $this->set_progress((int) (100 * $i / $count));
                }
                $i++;


                // alias will be regenerated on save
                $product->init($properties);
                $product->save(FALSE, TRUE);
            }
        }
        while (count($products) && ($loop_prevention-- > 0));

        if ($loop_prevention <= 0)
            throw new Kohana_Exception ('Possible infinite loop in :method', array(':method' => __METHOD__));
        
        $this->set_status_info('Генерация завершёна');
    }

}
