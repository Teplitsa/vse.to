<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Relink products to sections
 */
class Task_UpdateLinks extends Task
{
    /**
     * Run the task
     */
    public function run()
    {
        $site_id = 1;

        $count = Model::fly('Model_Product')->count_by_site_id($site_id);
        
        $percents = 0; $i = 0;
        $this->set_status_info('Обновление привязки товаров к разделам');
        $this->set_progress($percents);        

        $loop_prevention = 1000;
        do {
            if ($i * 100.0 / $count >= $percents + 2)
            {
                $percents = round($i * 100.0 / $count);
                
                $this->set_status_info('Обновление привязки товаров к разделам : ' . $i . ' из ' . $count);
                $this->set_progress($percents);
            }

            $params = array(
                'columns' => array('id', 'section_id'),
                'with_sections' => TRUE,

                'batch' => 100
            );
            
            $products = Model::fly('Model_Product')->find_all_by_site_id($site_id, $params);

            foreach ($products as $product)
            {
                $product->update_section_links();
                $i++;
            }
        }
        while (count($products) && $loop_prevention-- > 0);
        if ($loop_prevention <= 0)
            throw new Kohana_Exception('Possible infinite loop in :method', array(':method' => __METHOD__));

        //
        Model::fly('Model_Section')->mapper()->update_products_count();
        
        $this->set_status_info('Обновление привязки товаров к разделам завершено');
    }

}
