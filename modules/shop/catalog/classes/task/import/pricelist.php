<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Import pricelist
 */
class Task_Import_Pricelist extends Task
{

    /**
     * Run the task
     */
    public function run()
    {
        $this->set_progress(0);
        $supplier = (int) $this->param('supplier');
        if ( !  array_key_exists($supplier, Model_Product::suppliers()))
        {
            $this->log('Указан неверный поставщик', Log::ERROR);
            return;
        }

        $file = $this->param('file');
        if ( ! is_readable($file))
        {
            $this->log('Не удалось прочитать файл "' . $file . '"', Log::ERROR);
            return;
        }

        $this->set_status_info('Reading xls file...');
        
        $reader = XLS::reader();
        $reader->setOutputEncoding('UTF-8');
        $reader->read($file);

        $this->set_status_info(
            'Импорт прайслиста'
          . ' (поставщик: ' . Model_Product::supplier_caption($supplier)
          . ', коэффициент для цен: ' . $this->param('price_factor')
          . ')');

        // Generate unique import id
        $loop_prevention = 500;
        do {
            $import_id = mt_rand(1, mt_getrandmax());
        }
        while (Model::fly('Model_Product')->exists_by_import_id($import_id) && $loop_prevention-- > 0);
        if ($loop_prevention <= 0)
            throw new Kohana_Exception('Possible infinite loop in :method', array(':method' =>__METHOD__));

        switch ($supplier)
        {
            case Model_Product::SUPPLIER_IKS:
                $this->iks($reader, $import_id);
                break;
            
            case Model_Product::SUPPLIER_ISV:
                $this->isv($reader, $import_id);
                break;
        }

        $this->process_not_imported_products($supplier, $import_id);

        // update stats
        Model::fly('Model_Section')->mapper()->update_products_count();

        $this->set_status_info('Import finished. (' . date('Y-m-d H:i:s') . ')');

        //@FIXME: Unlink only temporary files
        //unlink($file);
    }

    /**
     * Import toysfest pricelist
     */
    public function iks(Spreadsheet_Excel_Reader $reader, $import_id)
    {
        $site_id = 1; //@TODO: pass via params

        $rowcount = $reader->rowcount();

        for ($row = 1; $row <= $rowcount; $row++)
        {
            if ($row % 20 == 0)
            {
                $this->set_progress((int) (($row - 1) * 100 / $rowcount));
            }
            
            if ($reader->val($row, 1) == '' || $reader->val($row, 1) == 'import_id')
                continue; // Not a product row

            // ----- caption & brand_caption
            $caption       = $reader->val($row, 5);
            $brand_caption = $reader->val($row, 2);
            
            // ----- marking
            $marking = $reader->val($row, 4);

            /*
            if ($row == 2459)
            {
                echo 'caption: ' . $caption . '<br /><br />';
                echo 'val: ' . $reader->val($row, 4) . '<br />';
                echo 'raw: ' . $reader->raw($row, 4) . '<br />';
                echo 'format: ' . $reader->format($row, 4) . '<br />';
                echo 'formatIndex: ' . $reader->formatIndex($row, 4) . '<br />';
                
                $raw = $reader->raw($row, 4);
                $format = $reader->format($row, 4);
                $formatIndex = $reader->formatIndex($row, 4);
                $formatted = $reader->_format_value($format, $raw, $formatIndex, TRUE);
                echo 'formatted via _format_valua(): ' . $formatted['string'];
                die;
            }
             */

            if ($marking == '')
            {
                $this->log(
                    '[Строка ' . $row . ']: Для товара "' . $caption . '", ' . $brand . ' не удалось определить артикул' //. "\n"
                  //. 'val: ' . $reader->val($row, 4) . "\n"
                  //. 'raw: ' . $reader->raw($row, 4) . "\n"
                , Log::ERROR);
                continue;
            }

            // ----- price
            $price = trim($reader->raw($row, 8));
            if ($price == '')
            {
                $price = $reader->val($row, 8);
            }

            if ($price == '' || ! preg_match('/\d+([\.,]\d+)?/', $price))
            {
                $this->log(
                    '[Строка ' . $row . ']: Некорретное значение цены ' . $price . ' для товара "' . $caption . '", ' . $brand //. "\n"
                  //. 'val: ' . $reader->val($row, 4) . "\n"
                  //. 'raw: ' . $reader->raw($row, 4) . "\n"
                , Log::ERROR);
                continue;
            }
            $this->process_product($marking, Model_Product::SUPPLIER_IKS, $price, $caption, $brand_caption, $import_id);
        }
    }

    /**
     * Import saks pricelist
     */
    public function saks($reader, $import_id)
    {
        $site_id = 1; //@TODO: pass via params

        $rowcount = $reader->rowcount();
        for ($row = 1; $row <= $rowcount; $row++)
        {
            if ($row % 20 == 0)
            {
                $this->set_progress((int) (($row - 1) * 100 / $rowcount));
            }

            if ($reader->val($row, 7) == '' || $reader->val($row, 7) == 'Штрих-код')
                continue; // Not a product row

            // ----- caption
            $caption = $reader->val($row, 3);
            
            // ----- marking
            $marking = $reader->val($row, 2);
            $marking = trim($marking, " '");

            if ($marking == '')
            {
                $this->log(
                    '[Строка ' . $row . ']: Для товара "' . $caption . '" не удалось определить артикул' //. "\n"
                  //. 'val: ' . $reader->val($row, 4) . "\n"
                  //. 'raw: ' . $reader->raw($row, 4) . "\n"
                , Log::ERROR);
                continue;
            }

            // ----- price
            $price = trim($reader->raw($row, 4));
            if ($price == '')
            {
                $price = $reader->val($row, 4);
            }

            if ($price == '' || ! preg_match('/\d+([\.,]\d+)?/', $price))
            {
                $this->log(
                    '[Строка ' . $row . ']: Некорретное значение цены ' . $price . ' для товара "' . $caption . '"' //. "\n"
                  //. 'val: ' . $reader->val($row, 4) . "\n"
                  //. 'raw: ' . $reader->raw($row, 4) . "\n"
                , Log::ERROR);
                continue;
            }
            
            $this->process_product($marking, Model_Product::SUPPLIER_SAKS, $price, $caption, NULL, $import_id);
        }
    }

    /**
     * Import gulliver pricelist
     */
    public function gulliver($reader, $import_id)
    {
        $site_id = 1; //@TODO: pass via params

        $rowcount = $reader->rowcount();
        for ($row = 1; $row <= $rowcount; $row++)
        {
            if ($row % 20 == 0)
            {
                $this->set_progress((int) (($row - 1) * 100 / $rowcount));
            }

            if ($reader->val($row, 5) == '' || $reader->val($row, 5) == 'Цена')
                continue; // Not a product row

            // ----- caption
            $caption = $reader->val($row, 3);
            
            // ----- marking
            $marking = $reader->val($row, 2);

            if ($marking == '')
            {
                $this->log(
                    '[Строка ' . $row . ']: Для товара "' . $caption . '" не удалось определить артикул' //. "\n"
                  //. 'val: ' . $reader->val($row, 4) . "\n"
                  //. 'raw: ' . $reader->raw($row, 4) . "\n"
                , Log::ERROR);
                continue;
            }

            // ----- price
            $price = trim($reader->raw($row, 5));
            if ($price == '')
            {
                $price = $reader->val($row, 5);
            }

            if ($price == '' || ! preg_match('/\d+([\.,]\d+)?/', $price))
            {
                $this->log(
                    '[Строка ' . $row . ']: Некорретное значение цены ' . $price . ' для товара "' . $caption . '"' //. "\n"
                  //. 'val: ' . $reader->val($row, 4) . "\n"
                  //. 'raw: ' . $reader->raw($row, 4) . "\n"
                , Log::ERROR);
                continue;
            }
            
            $this->process_product($marking, Model_Product::SUPPLIER_GULLIVER, $price, $caption, NULL, $import_id);
        }
    }
    
    /**
     * Update price for product with given marking and supplier
     */
    public function process_product($marking, $supplier, $price, $caption, $brand_caption, $import_id, $marking_like = FALSE)
    {
        $site_id = 1; //@FIXME: pass via task params

        static $brands;
        if ($brands === NULL)
        {
            $brands = Model::fly('Model_Section')->find_all_by_sectiongroup_id(1, array(
                'columns' => array('id', 'lft', 'rgt', 'level', 'caption'),
                'as_tree' => TRUE
            ));
        }

        $products = Model::fly('Model_Product')->find_all_by_marking_and_supplier_and_site_id(
            $marking, $supplier, $site_id,
            array(
                'columns' => array('id', 'marking', 'caption')
            )
        );

        if (count($products) <= 0)
        {
            $this->log('Товар из прайслиста с артикулом ' . $marking . ' ("' . $caption . '", ' . $brand_caption . ') не найден в каталоге сайта', Log::WARNING);
            return;
        }

        if (count($products) >= 2)
        {
            // Two or more products with the same marking
            $brand_products = array();
            if ($brand_caption != '')
            {                
                // Try to guess the product by brand
                foreach ($products as $product)
                {
                    $brand_ids = $product->get_section_ids(1);
                    foreach ($brand_ids as $brand_id)
                    {
                        // Find top-level brand
                        $brand = $brands->ancestor($brand_id, 1);

                        // Product belongs to the brand
                        if ($brand_caption == $brand->caption)
                        {
                            $brand_products[] = $product->id;
                        }
                    }                    
                }                
            }

            if (count($brand_products) != 1)
            {
                // Failed to distinguish duplicate markings by brand...
                $msg = '';
                foreach ($products as $product)
                {
                    $msg .= $product->marking . ' ("' . $product->caption . '")' . "\n";
                }
                $this->log(
                    'Артикул ' . $marking . ' ("' . $caption . '", ' . $brand_caption . ') не является уникальным в каталоге сайта:' . "\n"
                  . trim($msg, ' ",')
                , Log::ERROR);
                
                return;
            }

            $product = $products[$brand_products[0]];
        }
        else // count($products) == 1
        {
            $product = $products->at(0);
        }

        $product->active = 1;

        $price_factor = l10n::string_to_float($this->param('price_factor'));
        $sell_price = ceil($price * $price_factor);
        $product->price = new Money($sell_price);

        $product->import_id = $import_id;
        $product->save(FALSE, FALSE, FALSE, FALSE, FALSE);
    }

    /**
     * Display warnings about products that are present in the catalog but are not present in the pricelist
     */
    public function process_not_imported_products($supplier, $import_id)
    {
        $site_id = 1; //@FIXME: pass via task params

        $loop_prevention = 1000;
        do {
            $products = Model::fly('Model_Product')->find_all_by(
                array(
                    'supplier'  => $supplier,
                    'site_id'   => $site_id,
                    'import_id' => array('<>', $import_id)
                ),
                array(
                    'columns' => array('id', 'marking', 'caption'),

                    'batch' => 100
                )
            );

            foreach ($products as $product)
            {
                $this->log('Деактивирован товар с артикулом ' . $product->marking . ' ("' . $product->caption . '"). Товар есть в каталоге сайта, но отсутствует в прайс-листе', Log::WARNING);
                $product->active = 0;
                $product->save(FALSE, FALSE, FALSE, FALSE, FALSE);
            }
        }
        while (count($products) && $loop_prevention-- > 0);
        if ($loop_prevention <= 0)
            throw new Kohana_Exception('Possible infinite loop in :method', array(':method' => __METHOD__));
    }

}
