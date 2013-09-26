<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Import catalog from www.saks.ru
 */
class Task_Import_Saks_Old extends Task_Import_Web
{
    /**
     * Construct task
     */
    public function  __construct()
    {
        parent::__construct('http://www.saks.ru');
        
        // Set defaults for task parameters
        $this->params(array(
            // brands & series
            'create_brands'             => FALSE,
            'create_series'             => FALSE,
            'update_brand_captions'     => FALSE,
            'update_brand_descriptions' => FALSE,
            'update_brand_images'       => FALSE,

            // products
            'create_products'             => TRUE,
            'update_product_captions'     => FALSE,
            'update_product_descriptions' => FALSE,
            'update_product_images'       => FALSE,

            'link_products_by' => 'caption'
        ));
    }

    /**
     * Run the task
     */
    public function run()
    {
        $this->set_progress(0);

        // tmp
        //$this->fix_main_sections();
        //$this->fix_suppliers();
        $this->fix_images();
        die;

        // import brands
        $brands = $this->import_brands();

        // import products
        //$this->import_products($brands);

        $this->set_status_info('Импорт завершён');
    }

    /**
     * Import brands
     */
    public function import_brands()
    {
        $site_id = 1; // @TODO: pass as parameter to task
        $sectiongroup_id = 1; // must be brands section group id @TODO: pass as parameter to task

        // get all properties
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

        // ----- brands --------------------------------------------------------
        $brands = array(0 => array());

        // Retrieve list of all brands with links from catalog page ...
        $this->set_status_info('Parsing brands');

        $page = $this->get('/catalog/');

        preg_match_all(
            '!<li><div><a\s*href="/catalog/about_m/\?brend=(\d+)">([^<]*)!',
            $page, $matches, PREG_SET_ORDER);

        foreach ($matches as $match)
        {
            $import_id     = trim($match[1]);
            $brand_caption = $this->decode_trim($match[2], 255);

            $brands[0][$import_id] = array(
                'import_id' => $import_id,
                'caption'   => $brand_caption
            );
        }

        // Retrieve brand description and image urls & import brands
        $count = count($brands[0]); $i = 0;
        $this->set_status_info('Retrieving brands info and importing');

        $brand    = new Model_Section();
        $subbrand = new Model_Section();

        foreach ($brands[0] as $brand_info)
        {
            $page = $this->get('/catalog/about_m/?brend=' . $brand_info['import_id']);

            if (preg_match(
                '!<tr\s*class="noborder">\s*<td>\s*(<img\s*src="([^"]*)"[^>]*>)?(.*?)(?=</td>)!s',
                $page, $matches))
            {
                $brand_info['image_url']   = $matches[2];
                $brand_info['description'] = $matches[3];
            }

            $brand->find_by_web_import_id_and_sectiongroup_id($brand_info['import_id'], $sectiongroup_id);
            $creating = ( ! isset($brand->id));

            $brand->web_import_id   = $brand_info['import_id'];
            $brand->sectiongroup_id = $sectiongroup_id;
            if ($this->param('update_brands_captions'))
            {
                $brand->caption = $brand_info['caption'];
            }
            if ($this->param('update_brands_descriptions') && isset($brand_info['description']))
            {
                $brand->description = $brand_info['description'];
            }

            // Select all additional properties
            $brand->propsections = $propsections;

            if ($creating)
            {
                if ($this->param('create_brands'))
                {
                    $this->log('Creating new brand: "' . $brand->caption . '"');
                    $brand->save(FALSE, TRUE, FALSE);
                }
                else
                {
                    $this->log('Skipped new brand: "' . $brand->caption . '"');
                    continue;
                }
            }
            else
            {
                $brand->save(FALSE, TRUE, FALSE);
            }
            $brand->find($brand->id); //@FIXME: we use it to obtain 'lft' and 'rgt' values

            if (    isset($brand_info['image_url'])
                 && ($creating || $this->param('update_brands_images')))
            {
                if ( ! $creating)
                {
                    // Delete existing images
                    $image->delete_all_by_owner_type_and_owner_id('section', $brand->id);
                }

                $image_file = $this->download_cached($brand_info['image_url']);

                if ($image_file)
                {
                    try {
                        $image = new Model_Image();
                        $image->source_file = $image_file;
                        $image->owner_type = 'section';
                        $image->owner_id   = $brand->id;
                        $image->config     = 'section';
                        $image->save();
                    }
                    catch (Exception $e)
                    {}
                }
            }

            // ----- Importing subbrands
            preg_match_all(
                '!<li><div><a\s*href="((/catalog)?/(section)?' . $brand_info['id'] . '/(item_)?(\d+)/\?brend=' . $brand_info['id'] . ')">([^<]*)!',
                $page, $matches, PREG_SET_ORDER);

            foreach ($matches as $match)
            {
                $subbrand_import_id = trim($match[5]);
                $subbrand_caption   = $this->decode_trim($match[6], 255);

                $brands[$brand_info['id']][$subbrand_import_id] = array(
                    'id'      => $subbrand_import_id,
                    'caption' => $subbrand_caption,
                    'url'     => $match[1]
                );

                $subbrand->find_by_web_import_id_and_parent($subbrand_import_id, $brand);

                $subbrand->web_import_id = $subbrand_import_id;
                $subbrand->sectiongroup_id = $sectiongroup_id;
                $subbrand->parent_id = $brand->id;
                //$subbrand->caption = $subbrand_caption;

                // Select all additional properties
                $subbrand->propsections = $propsections;

                // do not recalculate activity for now
                if ( ! isset($subbrand->id))
                {
                    if ($this->param('create_series'))
                    {
                        $this->log('Creating new seria: "' . $subbrand->caption .'" for brand "' . $brand->caption . '"');
                        $subbrand->save(FALSE, TRUE, FALSE);
                    }
                    else
                    {
                        $this->log('Skipped new seria: "' . $subbrand->caption .'" for brand "' . $brand->caption . '"');
                        continue;
                    }
                }
                else
                {
                    $subbrand->save(FALSE, TRUE, FALSE);
                }
            }

            $i++;
            $this->set_status_info('Importing brands : ' . $i . ' of ' . $count . ' done.');
        }

        // Update activity info & stats for sections
        $brand->mapper()->update_activity();
        $brand->mapper()->update_products_count();

        return $brands;
    }

    /**
     * Import products
     *
     * @param array $brands
     */
    public function import_products(array $brands)
    {
        $site_id = 1; // @TODO: pass as parameter to task
        $sectiongroup_id = 1; // must be brands section group id @TODO: pass as parameter to task

        $brand    = new Model_Section();
        $subbrand = new Model_Section();
        $product  = new Model_Product();

        // Caclulate progress info
        $count = count($brands, COUNT_RECURSIVE) - count($brands) - count($brands[0]); $i = 0;
        $this->set_status_info('Importing products');

        $status_info = '';
        $_imported = array(/*
            '9846', '16018', '132', '144', '13656', '12133', '134', '12548',
            '141', '13915', '16234', '16846', '16998', '17035', */
        );

        foreach ($brands[0] as $brand_info)
        {
            /*
            if (in_array($brand_info['id'], $_imported))
                continue;

            $status_info .= $brand_info['id'] . "\n";
            $this->set_status_info($status_info);
             */

            $brand->find_by_caption_and_sectiongroup_id($brand_info['caption'], $sectiongroup_id);

            foreach ($brands[$brand_info['id']] as $subbrand_info)
            {
                $this->set_progress((int) (100 * $i / $count));
                $i++;

                $subbrand->find_by_caption_and_parent($subbrand_info['caption'], $brand);

                // Obtain first page
                $subbrand_page = $this->get($subbrand_info['url']);

                // Detect number of pages if products display is paginated
                $offset     = 0;
                $max_offset = 0;
                $per_page   = 24;

                preg_match_all('!<a\s*href="[^"]*from=(\d+)!', $subbrand_page, $matches);
                if ( ! empty($matches[1]))
                {
                    $per_page   = min($matches[1]);
                    $max_offset = max($matches[1]);
                }

                // Iterate over pages
                for ($offset = 0; $offset <= $max_offset; $offset += $per_page)
                {
                    if ($offset > 0)
                    {
                        // obtain next page
                        $subbrand_page = $this->get($subbrand_info['url'] . '&from=' . $offset);
                    }

                    preg_match_all(
                        '!<a\s*href="([^"]*)">Перейти\s*на\s*страницу\s*товара!',
                        $subbrand_page, $matches);

                    foreach (array_unique($matches[1]) as $url)
                    {
                        $page = $this->get($url);

                        // caption
                        if (preg_match('!<td>\s*<h6[^>]*>\s*([^<]+)!', $page, $m))
                        {
                            $caption = $this->decode_trim($m[1], 255);
                        }
                        else
                        {
                            throw new Kohana_Exception('Unable to determine caption for product :ulr', array(':url' => $url));
                        }

                        $product->find_by_caption_and_section_id($caption, $subbrand->id, array('with_properties' => FALSE));
                        $creating = ( ! isset($product->id));

                        // Set caption
                        $product->caption = $caption;

                        // Link to subbrand
                        $product->section_id = $subbrand->id;

                        // marking, age & description
                        if (preg_match(
                                '!'
                              . 'Артикул:([^<]*)(<br>\s*)+'
                              . '(Возраст:([^<]*))?(<br>\s*)'
                              . '(.*?)'
                              . '(<p>\s*(январь|февраль|март|апрель|май|июнь|июль|август|сентябрь|октябрь|ноябрь|декабрь|новинка).*?</p>\s*)?'
                              . '(<p>Рекомендованная\s*розничная.*?</p>\s*)?'
                              . '<ul\s*class="links">'
                              . '!is'
                                , $page, $m))
                        {
                            $product->marking     = $this->decode_trim($m[1], 63);
                            $product->age         = $this->decode_trim($m[4]);

                            // cut out images & links from description
                            $description = trim($m[6]);
                            $description = preg_replace('!<img[^>]*>!', '', $description);
                            $description = preg_replace('!<a[^>]*>.*?</a>!', '', $description);
                            $product->description = $description;
                        }

                        // price
                        if (preg_match('!цена:\s*<span[^>]*>([^<]*)!', $page, $m))
                        {
                            $product->price = new Money(l10n::string_to_float(trim($m[1])));
                        }

                        // dimensions
                        if (preg_match(
                               '!'
                             . 'Размеры:\s*([\d\.,]+)\s*.\s*([\d\.,]+)\s*.\s*([\d\.,]+)\s*</em>\s*'
                             . '</p>\s*<tr>\s*<td\s*class="link">\s*'
                             . '<a\s*href="' . str_replace(array('?'), array('\?'), $url)
                             . '!is', $subbrand_page, $m))
                        {
                            $product->width  = l10n::string_to_float(trim($m[1]));
                            $product->height = l10n::string_to_float(trim($m[2]));
                            $product->depth  = l10n::string_to_float(trim($m[3]));
                            $product->volume = round($product->width * $product->height * $product->depth, 1);
                        }

                        $product->save();

                        if ($creating)
                        {
                            // Product images
                            $image = new Model_Image();

                            /*
                            if ( ! $creating)
                            {
                                // Delete already existing images for product
                                $image->delete_all_by_owner_type_and_owner_id('product', $product->id);
                            }
                             *
                             */

                            preg_match_all(
                                '!<img\s*src="([^"]*_big_[^"]*)"!',
                                $page, $m);

                            foreach (array_unique($m[1]) as $image_url)
                            {
                                $image_url = trim($image_url);

                                $image_file = $this->download_cached($image_url);

                                if ($image_file)
                                {
                                    try {
                                        $image = new Model_Image();
                                        $image->source_file = $image_file;
                                        $image->owner_type = 'product';
                                        $image->owner_id   = $product->id;
                                        $image->config     = 'product';
                                        $image->save();

                                        unset($image);
                                    }
                                    catch (Exception $e)
                                    {}
                                }
                            }
                        }

                    } // foreach $product_id

                } // foreach $offset
            }// foreach $subbrand
        }// foreach $brand
    }

    /**
     * TEMP
     */
    public function update_import_ids()
    {
        $rows = DB::select('id', 'web_import_id')
            ->from('section_tmp')
            ->where('web_import_id', 'RLIKE', '[[:digit:]]+')
            ->execute();

        foreach ($rows as $row)
        {
            DB::update('section')
                ->set(array('web_import_id' => $row['web_import_id']))
                ->where('id', '=', $row['id'])
                ->execute();
        }
    }

    /**
     * TEMP
     */
    public function fix_main_sections()
    {
        $sectiongroup_id = 1;

        $brands = Model::fly('Model_Section')->find_all_by_sectiongroup_id($sectiongroup_id, array(
            'columns' => array('id', 'lft', 'rgt', 'level', 'caption'),
            
            'as_tree' => TRUE
        ));

        $loop_protection = 500;        
        do {
            $products = Model::fly('Model_Product')->find_all(array(
                'with_sections' => TRUE,
                'columns' => array('id', 'section_id', 'caption', 'web_import_id'),
                
                'batch' => 1000
            ));

            foreach ($products as $product)
            {
                if ($product->web_import_id == '' && $product->section_id == 1)
                {
                    if ( ! isset($product->sections[$sectiongroup_id]))
                    {
                        $this->log('Product "' . $product->caption . '" (' . $product->id . ') is not linked to any brand at all');
                        $product->delete(FALSE);
                    }
                    else
                    {
                        $this->log('Product "' . $product->caption . '" (' . $product->id . ') has invalid main section value');
                        $section_ids = array_keys(array_filter($product->sections[$sectiongroup_id]));

                        $product->section_id = $section_ids[0];
                        $product->save(FALSE, TRUE, FALSE, FALSE, FALSE);
                    }
                }
            }
        }
        while (count($products) && $loop_protection-- > 0);

        if ($loop_protection <= 0)
            throw new Kohana_Exception('Possible infinite loop in :method', array(':method' => __METHOD__));

        Model::fly('Model_Section')->mapper()->update_products_count();
    }

    /**
     * 
     */
    public function fix_suppliers()
    {
        $saks_brands = array(
            'LEGO', 'Moxie', 'AURORA', 'Zapf Creation', 'BRATZ', 'WELLY', 'CHAP MEI',
            'Little Tikes', 'Peg-Perego', 'Taf Toys', 'Disney', 'NICI', 'Bakugan',
            'Cubicfun', 'BEN10', 'Daesung', 'Legend of Nara', 'TRON', 'Hello Kitty'
        );

        $gulliver_brands = array(
            'K\'s Kids', 'Sylvanian Families', 'Tiny Love', 'Sonya', 'Tomy',
            'Ouaps', 'Gulliver', 'Gulliver (0-3 лет)', 'Gulliver (Collecta)',
            'Gulliver коляски', 'Gulliver «Чаепитие»', 'Gulliver рюкзаки',
            'Bruder', 'Keenway', 'Silverlit', 'Imaginary Play', 'Wader',
            'Hap-P-Kid', 'Fenbo', 'I\'m Toy', 'Hello!', 'Wow', 'Tomica',
            'The Colored World', 'Gormiti', 'In my pocket'
        );


        $sectiongroup_id = 1;

        $brands = Model::fly('Model_Section')->find_all_by_sectiongroup_id($sectiongroup_id, array(
            'columns' => array('id', 'lft', 'rgt', 'level', 'caption'),

            'as_tree' => TRUE
        ));

        $loop_protection = 500;
        do {
            $products = Model::fly('Model_Product')->find_all(array(
                'with_sections' => TRUE,
                'columns' => array('id', 'section_id', 'supplier', 'caption'),

                'batch' => 1000
            ));

            foreach ($products as $product)
            {
                if ($product->supplier == 0)
                {
                    $brand = $brands->ancestor($product->section_id, 1, TRUE);
                    
                    if (in_array($brand['caption'], $saks_brands))
                    {
                        $this->log('Setting supplier SAKS for product "' . $product->caption . '", brand "' . $brand['caption'] . '"');

                        $product->supplier = Model_Product::SUPPLIER_SAKS;
                        $product->save(FALSE, FALSE, FALSE, FALSE, FALSE);
                    }
                    elseif (in_array($brand['caption'], $gulliver_brands))
                    {
                        $this->log('Setting supplier GULLIVER for product "' . $product->caption . '", brand "' . $brand['caption'] . '"');

                        $product->supplier = Model_Product::SUPPLIER_GULLIVER;
                        $product->save(FALSE, FALSE, FALSE, FALSE, FALSE);
                    }
                    else
                    {
                        $this->log('WARNING: Unable to detect supplier for product "' . $product->caption . '", brand ' . $brand['caption'] . '"');
                    }
                }
            }
        }
        while (count($products) && $loop_protection-- > 0);

        if ($loop_protection <= 0)
            throw new Kohana_Exception('Possible infinite loop in :method', array(':method' => __METHOD__));
    }

    /**
     * Delete unused images
     */
    public function fix_images()
    {
        /*
        $loop_protection = 1000;
        do {
            
            $images = Model::fly('Model_Image')->find_all(array(
                'batch' => '100'
            ));

            foreach ($images as $image)
            {
                if ($image->owner_type == 'product')
                {
                    if ( ! Model::fly('Model_Product')->exists_by_id($image->owner_id))
                    {
                        $this->log('Unused image: ' . $image->image1);
                    }
                }
            }
        }
        while (count($images) && $loop_protection-- > 0);

        if ($loop_protection <= 0)
            throw new Kohana_Exception('Possible infinite loop in :method', array(':method' => __METHOD__));
         *
         */

        $iterator = new DirectoryIterator(DOCROOT . 'public/data');
        $counter = 0;
        foreach ($iterator as $file)
        {
            if (preg_match('/_product(\d+)_/', $file, $matches))
            {
                if ( ! Model::fly('Model_Product')->exists_by_id($matches[1]))
                {
                    echo 'Deleting ' . $file . '<br />';
                    unlink(DOCROOT . 'public/data/' . $file);
                }
            }
            $counter++;
        }
        echo $counter . ' files processed<br />';
    }
}
