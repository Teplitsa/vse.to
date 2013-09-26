<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Import structure
 */
class Task_Import_Structure extends Task
{
    /**
     * Default parameters for this taks
     * @var array
     */
    public static $default_params = array(
        // sections
        'create_sections'             => FALSE,
        'update_section_parents'      => FALSE,
        'update_section_captions'     => FALSE,
        'update_section_descriptions' => FALSE,
        'update_section_images'       => FALSE,

        // products
        'create_products'             => TRUE,
        'update_product_marking'      => FALSE,
        'update_product_captions'     => FALSE,
        'update_product_descriptions' => FALSE,
        'update_product_properties'   => FALSE,
        'update_product_images'       => FALSE,

        'link_products_by' => 'caption'
    );
    
    /**
     * Construct task
     */
    public function  __construct()
    {
        parent::__construct();
        
        $this->default_params(self::$default_params);
    }
    
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
            'Импорт струтуры'
          . ' (поставщик: ' . Model_Product::supplier_caption($supplier));

        // Generate unique import id
        $loop_prevention = 500;
        do {
            $import_id = mt_rand(1, mt_getrandmax());
        }
        while (Model::fly('Model_Section')->exists_by_import_id($import_id)
                && Model::fly('Model_Product')->exists_by_import_id($import_id)
                   && $loop_prevention-- > 0);
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
     * Import Iks catalog structure
     */
    public function iks(Spreadsheet_Excel_Reader $reader, $import_id)
    {
        foreach ($reader->boundsheets as $sheet_num => $sheet_desc) {
            $sheets[$sheet_desc['name']] = $sheet_num;
        }
        
        // import sections
        if (!isset($sheets['sections'])) {
            $this->set_status_info('Каталогов не обнаружено');        
            return FALSE;
        }
        
        $sections = $this->iks_import_sections($reader,$sheets['sections'],$import_id);

        // import products
        if (!isset($sheets['products'])) {
            $this->set_status_info('Товаров не обнаружено');        
            return FALSE;
        }        
        
        $this->iks_import_products($reader,$sheets['products'],$import_id,$sections);
        
        $this->set_status_info('Импорт завершён');
        
    }
    
    public function iks_import_sections(Spreadsheet_Excel_Reader $reader,$sheet, $import_id) {
        $sections = array(0 => array());
        
        $this->set_status_info('Fetching sections');
        
        $rowcount = $reader->rowcount($sheet);
        
        for ($row = 1; $row <= $rowcount; $row++)
        {
            if ($row % 20 == 0)
            {
                $this->set_progress((int) (($row - 1) * 100 / $rowcount));
            }
            
            if ($reader->val($row, 1,$sheet) == '' || $reader->val($row, 1,$sheet) == 'import_id')
                continue; // Not a section row

            $section = array();
            
            $parent_import_id               = $reader->val($row, 2, $sheet);
            if ($parent_import_id == '') $parent_import_id = 0;
            
            $section['import_id']           = $import_id;
            $section['web_import_id']       = $reader->val($row, 1, $sheet);
            $section['caption']             = $reader->val($row, 3, $sheet);
            $section['description']         = $reader->val($row, 4, $sheet);
            $section['meta_title']          = $reader->val($row, 5, $sheet);
            $section['meta_description']    = $reader->val($row, 6, $sheet);
            $section['meta_keywords']       = $reader->val($row, 7, $sheet);
            $section['image_file']           = $reader->val($row, 8, $sheet);
            
            
            $sections[$parent_import_id][] = $section;
        }

        $sections = $this->import_sections($sections);
        $flat_sections = array();
        
        foreach ($sections as $parent_import_id => $child_sections) {
            foreach($child_sections as $section) {
                $flat_sections[$section['web_import_id']] = $section;
            }
        }
        return $flat_sections;
    }

    /**
     * Import a branch of sections
     *
     * @param array $sections
     * @param Model_Section $parent
     */
    public function import_sections(array $sections, Model_Section $parent = NULL)
    {
        if ($parent === NULL)
        {
            $count = count($sections[0]); $i = 0;
            $this->set_status_info('Importing sections');
        }

        $site_id         = 1; // @TODO: pass as parameter to task
        $sectiongroup_id = 1; // must be brands section group id @TODO: pass as parameter to task
        $folder = DOCROOT . 'public/user_data/';
        
        // get all product properties
        static $propsections;

        if ($propsections === NULL)
        {
            $properties = Model::fly('Model_Property')->find_all_by_site_id($site_id, array('columns' => array('id')));
            $propsections = array();
            foreach ($properties as $property)
            {
                $propsections[$property['id']] = array(
                    'active' => 1,
                    'filter' => 0,
                    'sort'   => 0
                );
            }
        }

        $section = new Model_Section();

        $parent_import_id = ($parent !== NULL) ? $parent->web_import_id : 0;

        if ( ! isset($sections[$parent_import_id]))
            return $sections; // No sections in branch

        foreach ($sections[$parent_import_id] as $import_id => $section_info)
        {
            $section->find_by_web_import_id_and_sectiongroup_id($section_info['web_import_id'], $sectiongroup_id);
            $creating = ( ! isset($section->id));
            
            // import_id
            $section->import_id   = $section_info['import_id'];

            // web_import_id
            $section->web_import_id   = $section_info['web_import_id'];

            // sectiongroup_id
            $section->sectiongroup_id = $sectiongroup_id;

            // parent_id
            if ($parent !== NULL && ($creating || $this->param('update_section_parents')))
            {
                $section->parent_id = $parent->id;
            }

            // caption
            if (isset($section_info['caption']) && ($creating || $this->param('update_section_captions')))
            {
                $section->caption = $section_info['caption'];
            }

            // description
            if (isset($section_info['description']) && ($creating || $this->param('update_section_descriptions')))
            {
                $section->description       = $section_info['description'];
                $section->meta_title        = $section_info['meta_title'];
                $section->meta_description  = $section_info['meta_description'];
                $section->meta_keywords     = $section_info['meta_keywords'];
                
            }

            // product properties
            if ($creating)
            {
                $section->propsections = $propsections;
            }

            if ($creating)
            {
                if ($this->param('create_sections'))
                {
                    $section->save(FALSE, TRUE, FALSE);

                    if ($parent === NULL)
                    {
                        $this->log('Создан новый раздел "' . $section->caption . '"');
                    }
                    else
                    {
                        $this->log('Создан новый подраздел "' . $section->caption . '" в разделе "' . $parent->caption . '"');
                    }
                }
                else
                {
                    if ($parent === NULL)
                    {
                        $this->log('Пропущен новый раздел "' . $section->caption . '"');
                    }
                    else
                    {
                        $this->log('Пропущен новый подраздел "' . $section->caption . '" в разделе "' . $parent->caption . '"');
                    }
                    continue;
                }
            }
            else
            {
                $section->save(FALSE, FALSE, FALSE);
            }
            $section->find($section->id); //@FIXME: we use it to obtain 'lft' and 'rgt' values for saved brand

            // save section id
            $sections[$parent_import_id][$import_id]['id'] = $section->id;

            // logo
            if (!empty($section_info['image_file'])
                 && $this->param('update_section_images'))
            {
                $image = new Model_Image();

                if ( ! $creating)
                {
                    // Delete existing images
                    $image->delete_all_by_owner_type_and_owner_id('section', $section->id);
                }
                
                $image_file = $folder.$section_info['image_file'];

                if (file_exists($image_file)) {
                    try {
                        $image->source_file = $image_file;
                        $image->owner_type  = 'section';
                        $image->owner_id    = $section->id;
                        $image->config      = 'section';
                        $image->save();
                    }
                    catch (Exception $e)
                    {}
                }                    
            }
            
            // ----- Import subsections
            $sections = $this->import_sections($sections, $section);

            if ($parent === NULL)
            {
                $i++;
                $this->set_status_info('Importing brands : ' . $i . ' of ' . $count . ' done.');
            }

        }

        if ($parent === NULL)
        {
            // Update activity info & stats for sections
            $section->mapper()->update_activity();
            $section->mapper()->update_products_count();
        }
        return $sections;
    }

    /**
     * Import products
     *
     * @param array $sections
     */
    public function iks_import_products(Spreadsheet_Excel_Reader $reader,$sheet, $import_id,$sections)
    {
        $products = array();
        
        $this->set_status_info('Fetching products');        
        
        $rowcount = $reader->rowcount($sheet);
        
        for ($row = 1; $row <= $rowcount; $row++)
        {
            if ($row % 20 == 0)
            {
                $this->set_progress((int) (($row - 1) * 100 / $rowcount));
            }
            
            if ($reader->val($row, 1,$sheet) == '' || $reader->val($row, 1,$sheet) == 'section_import_id')
                continue; // Not a product row

            $product = array();            
            
            $product['import_id'] = $import_id;
            $product['web_import_id'] = $reader->val($row, 2, $sheet);
            $product['caption'] = $reader->val($row, 4, $sheet);

            // section
            $section_import_id = $reader->val($row, 1, $sheet);
            if ($section_import_id == '') $section_import_id = 0;
            
            if (!isset($sections[$section_import_id]['id'])) {
                $this->log(
                    '[Строка ' . $row . ']: Для товара "' . $product['caption'] . ' не был создан необходимый каталог'
                , Log::ERROR);
                continue;
            }
            $section_id = $sections[$section_import_id]['id'];

            // marking
            $product['marking'] = $reader->val($row, 3, $sheet);            
            if ($product['marking'] == '')
            {
                $this->log(
                    '[Строка ' . $row . ']: Для товара "' . $product['caption'] . ' не удалось определить артикул'
                , Log::ERROR);
                continue;
            }
            
            $product['description'] = $reader->val($row, 5, $sheet);
            
            // price
            $product['price'] = trim($reader->raw($row, 6, $sheet));

            if ($product['price'] == '')
            {
                $product['price'] = $reader->val($row, 6, $sheet);
            }

            if ($product['price'] == '' || ! preg_match('/\d+([\.,]\d+)?/', $product['price']))
            {
                $this->log(
                    '[Строка ' . $row . ']: Некорретное значение цены ' . $product['price'] . ' для товара "' . $product['caption']
                , Log::ERROR);
                continue;
            }
            
            // available
            $product['available'] = $reader->val($row, 7, $sheet);
            
            // stone
            $product['stone'] = $reader->val($row, 8, $sheet);
            
            // weight
            $product['weight'] = trim($reader->raw($row, 9, $sheet));

            if ($product['weight'] == '')
            {
                $product['weight'] = $reader->val($row, 9, $sheet);
            }

            if ($product['weight'] == '' || ! preg_match('/\d+([\.,]\d+)?/', $product['weight']))
            {
                $this->log(
                    '[Строка ' . $row . ']: Некорретное значение веса ' . $product['weight'] . ' для товара "' . $product['caption']
                , Log::ERROR);
                continue;
            }
            
            $product['metal']               = $reader->val($row, 10, $sheet);
            $product['stone_char']          = $reader->val($row, 11, $sheet);
            $product['other_char']          = $reader->val($row, 12, $sheet);
            $product['image_file']          = $reader->val($row, 13, $sheet);
            
            $products[$section_id][] = $product;
        }

        return $this->import_products($products,  Model_Product::SUPPLIER_IKS);        
    }
    
    public function import_products(array $products,$supplier) {
        
        $section = new Model_Section();
        $product = new Model_Product();
        $folder = DOCROOT . 'public/user_data/';
        
        $this->set_status_info('Importing products');
    
        foreach ($products as $section_id => $section_products)
        {
            $section->find($section_id);
            
            foreach ($section_products as $product_info) {
                $product->find_by_web_import_id_and_section_id($product_info['web_import_id'],$section_id);
                $creating = (!isset($product->id));
                
                // import_id
                $product->import_id   = $product_info['import_id'];
                unset($product_info['import_id']);
                
                // web_import_id
                $product->web_import_id   = $product_info['web_import_id'];
                unset($product_info['web_import_id']);

                // section_id
                $product->section_id = $section_id;

                // suppliers
                $product->supplier = $supplier;
                
                // marking
                if (isset($product_info['marking']) && ($creating || $this->param('update_product_markings')))
                {
                    $product->marking = $product_info['marking'];
                    unset($product_info['marking']);

                }

                // caption
                if (isset($product_info['caption']) && ($creating || $this->param('update_product_captions')))
                {
                    $product->caption = $product_info['caption'];
                    unset($product_info['caption']);

                }
                
                // description
                if (isset($product_info['description']) && ($creating || $this->param('update_product_descriptions')))
                {
                    $product->description = $product_info['description'];
                    unset($product_info['description']);
                }
                
                // properties
                if ($creating || $this->param('update_product_properties'))
                {
                    // price
                    $product->price =new Money($product_info['price']);
                    unset($product_info['price']);               
                    // available
                    $product->available = $product_info['available'];
                    unset($product_info['available']);
                    
                    // additional
                    foreach ($product_info as $property => $value) {
                        $product->$property       = $value;
                    }
                }
                
                if ($creating)
                {
                    if ($this->param('create_products'))
                    {
                        $product->save();

                        $this->log('Создан новый товар "' . $product->caption . '"');
                    }
                    else
                    {
                        $this->log('Пропущен новый товар "' . $product->caption . '"');
                        continue;
                    }
                }
                else
                {
                    $product->save(FALSE, FALSE, FALSE, FALSE, FALSE);
                }

                // image
                if (!empty($product_info['image_file'])
                     && $this->param('update_product_images'))
                {
                    $image = new Model_Image();

                    if ( ! $creating)
                    {
                        // Delete existing images
                        $image->delete_all_by_owner_type_and_owner_id('product', $product->id);
                    }

                    $image_file = $folder.$product_info['image_file'];

                    if (file_exists($image_file)) {
                        try {
                            $image->source_file = $image_file;
                            $image->owner_type  = 'product';
                            $image->owner_id    = $product->id;
                            $image->config      = 'product';
                            $image->save();
                        }
                        catch (Exception $e)
                        {}
                    }                    
                }
            }            
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

        /*$price_factor = l10n::string_to_float($this->param('price_factor'));
        $sell_price = ceil($price * $price_factor);
        $product->price = new Money($sell_price);*/
        $product->price = new Money($price);
        
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
