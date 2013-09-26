<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Import catalog from www.toysfest.ru
 */
class Task_Import_Toysfest extends Task_Import_Web
{
    /**
     * Construct task
     */
    public function  __construct()
    {
        parent::__construct('http://www.toysfest.ru');

        // Set defaults for task parameters
        $this->default_params(array(
            'create_brands'   => FALSE,
            'create_series'   => FALSE,
            'create_products' => TRUE,

            'update_brands_captions'     => FALSE,
            'update_brands_descriptions' => FALSE,
            'update_brands_images'       => FALSE,

            'update_products_images' => FALSE,

            'link_brands_by' => 'import_id',

            'link_products_by' => 'id'
        ));
    }

    /**
     * Run the task
     */
    public function run()
    {
        $this->set_progress(0);
        
        // import brands
        //$this->import_brands();

        // import products
        //$this->import_products();

        // update stats
        Model::fly('Model_Section')->mapper()->update_activity();
        Model::fly('Model_Section')->mapper()->update_products_count();

        $this->set_status_info('Импорт завершён');
    }

    /**
     * Import brands
     */
    public function import_brands()
    {
        $site_id = 1; // @TODO: pass as parameter to task
        $sectiongroup_id = 1; // must be brands section group id @TODO: pass as parameter to task

        // ----- brands --------------------------------------------------------
        $brands = array();

        // Retrieve list of all brands with links from catalog page ...
        $this->set_status_info('Parsing brands');

        $page = $this->get('/catalog/');

        preg_match_all(
            '!<li><a\s+href="(/catalog/\?filter\[manuf\]=([^"]+))">([^<]+)!',
            $page, $matches, PREG_SET_ORDER);

        foreach ($matches as $match)
        {
            $import_id = strtolower(substr(trim($match[2]), 0, 31));
            $caption   = $this->decode_trim($match[3], 255);
            $url       = trim($match[1]);

            $brands[$import_id] = array(
                'import_id' => $import_id,
                'caption'   => $caption,
                'url'       => $url
            );
        }

        // ... and, additionally, from brands page with image urls
        $this->set_status_info('Retrieving more brands and image urls for brands');

        $page = $this->get('/?tab=4');

        preg_match_all(
            '|<div><a\s+href="(/catalog/\?filter\[manuf\]=([^"]+))"><img\s+src="([^"]+)"\s+/></a></div>([^<]+)|',
            $page, $matches, PREG_SET_ORDER);

        foreach ($matches as $match)
        {
            $import_id = strtolower(substr(trim($match[2]), 0, 31));
            $caption   = $this->decode_trim($match[4], 255);
            $image_url = trim($match[3]);
            $url       = trim($match[1]);

            if (isset($brands[$import_id]))
            {
                // This brands was already imported in catalog page
                $brands[$import_id]['image_url'] = $image_url;
            }
            else
            {
                // Brand, that for some reason was not present at catalog page (i.e. Rubie's)
                $brands[$import_id] = array(
                    'import_id' => $import_id,
                    'caption'   => $caption,
                    'url'       => $url,
                    'image_url' => $image_url
                );
            }
        }

        // Import brands
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

        $count = count($brands); $i = 0;
        $this->set_status_info('Importing ' . $count . ' brands');

        $brand = new Model_Section();
        foreach ($brands as $brand_info)
        {
            if ($this->param('link_brands_by') == 'caption')
            {
                $brand->find_by_caption_and_level_and_sectiongroup_id($brand_info['caption'], 1, $sectiongroup_id);
            }
            else
            {
                $brand->find_by_web_import_id_and_level_and_sectiongroup_id($brand_info['import_id'], 1, $sectiongroup_id);
            }
            $creating = ( ! isset($brand->id));

            $brand->web_import_id   = $brand_info['import_id'];
            $brand->sectiongroup_id = $sectiongroup_id;
            if ($creating || $this->param('update_brands_captions'))
            {
                $brand->caption = $brand_info['caption'];
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

            $i++;
            $this->set_status_info('Importing brands : ' . $i . ' of ' . $count . ' done.');
        }
    }

    /**
     * Import products
     *
     * @param array $brands
     */
    public function import_products()
    {
        $site_id = 1; // @TODO: pass as parameter to task
        $sectiongroup_id = 1; //@FIXME: pass as parameter to task
        //
        // ----- Import products
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

        $brand   = new Model_Section();
        $seria = new Model_Section();
        $product = new Model_Product();

        // Detect the total number of pages
        $page = $this->get('/catalog/');
        preg_match_all(
            '!<a\s*href="/catalog/(index.php)?\?PAGEN_1=(\d+)!i',
            $page, $matches);
        if ( ! empty($matches[2]))
        {
            $total_pages = (int) max($matches[2]);
        }
        else
        {
            $total_pages = 1;
        }

        // Iterate over paginated results

        for ($page_n = 1; $page_n <= $total_pages; $page_n++)
        {
            $this->set_status_info('Importing products: page ' . $page_n . ' of ' . $total_pages);
            $this->set_progress((int) (100 * ($page_n - 1) / $total_pages));

            $page = $this->get('/catalog/?PAGEN_1=' . $page_n);
            preg_match_all(
                '!href="/product/(\d+)/!',
                $page, $import_ids_matches);

            foreach (array_unique($import_ids_matches[1]) as $import_id)
            {
                switch ($this->param('link_products_by'))
                {
                    case 'web_import_id':
                        $product->find_by_web_import_id($import_id, array('with_properties' => FALSE));
                        break;
                    case 'web_import_id_and_supplier':
                        $product->find_by_web_import_id_and_supplier($import_id, Model_Product::SUPPLIER_TOYSFEST, array('with_properties' => FALSE));
                        break;
                    default: // link by id
                        $product->find($import_id, array('with_properties' => FALSE));
                }
                $creating = ! isset($product->id);

                $product->web_import_id = $import_id;
                $product->supplier = Model_Product::SUPPLIER_TOYSFEST;

                $page = $this->get('/product/' . $import_id . '/');

                // ----- brand for product
                $brand->init();
                if (preg_match('!<div\s+class="gttl">\s*<div>\s*<a\s+href="/catalog/\?filter\[manuf\]=([^"]+)"!is', $page, $matches))
                {
                    $brand_import_id = strtolower(substr(trim($matches[1]), 0, 31));
                    $brand->find_by_web_import_id_and_level_and_sectiongroup_id($brand_import_id, 1, $sectiongroup_id);
                }

                if ( ! isset($brand->id))
                {
                    $this->log('ERROR: Unable to determine brand for product "' . $import_id . '"');
                    continue; // to the next product
                }

                // ----- parse subbrands for product
                $sections = array($sectiongroup_id => array());
                $series = array();
                $section_id = 0;

                // from series
                if (preg_match('!Серия:<span[^>]*>(.*?)</span>!', $page, $matches))
                {
                    $strs = explode(',', $matches[1]);
                    foreach ($strs as $str)
                    {
                        if (preg_match('!<a\s*href="/catalog/\?filter\[seria\]=([^"]*)">([^<]*)!', $str, $m))
                        {
                            $seria_import_id = strtolower(substr(trim($m[1]), 0, 31));
                            $seria_caption   = trim($m[2]);
                            $series[] = array(
                                'import_id' => $seria_import_id,
                                'caption'   => $seria_caption
                            );
                        }
                    }
                }

                // from licenses
                if (preg_match('!Лицензии:<span[^>]*>(.*?)</span>!', $page, $matches))
                {
                    $strs = explode(',', $matches[1]);
                    foreach ($strs as $str)
                    {
                        if (preg_match('!<a\s*href="/catalog/\?filter\[license\]=([^"]*)">([^<]*)!', $str, $m))
                        {
                            $license_import_id = strtolower(substr(trim($m[1]), 0, 31));
                            $license_caption   = trim($m[2]);
                            $series[] = array(
                                'import_id' => $license_import_id,
                                'caption'   => $license_caption
                            );
                        }
                    }
                }

                foreach ($series as $seria_info)
                {
                    // Create/update subbrands
                    if ($this->param('link_brands_by') == 'caption')
                    {
                        $seria->find_by_caption_and_parent($seria_info['caption'], $brand);
                    }
                    else {
                        $seria->find_by_web_import_id_and_parent($seria_info['import_id'], $brand);
                    }

                    $seria->web_import_id   = $seria_info['import_id'];
                    $seria->sectiongroup_id = $sectiongroup_id;
                    $seria->parent_id       = $brand->id;
                    if ( ! isset($seria->id) || $this->param('update_brands_captions'))
                    {
                        $seria->caption = $seria_info['caption'];
                    }

                    // Select all additional properties
                    $seria->propsections = $propsections;

                    if ( ! isset($seria->id))
                    {
                        if ($this->param('create_series'))
                        {
                            $this->log('Creating seria "' . $seria->caption . '" for brand "' . $brand->caption . '"');
                            $seria->save(FALSE, TRUE, FALSE);
                        }
                        else
                        {
                            $this->log('Skipped new seria "' . $seria->caption . '" for brand "' . $brand->caption . '"');
                            continue; // to the next seria
                        }
                    }
                    else
                    {
                        $seria->save(FALSE, TRUE, FALSE);
                    }

                    if ($section_id == 0)
                    {
                        // If it is a first seria - select it as a main section
                        $section_id = $seria->id;
                    }
                    else
                    {
                        // Additional brands
                        $sections[$sectiongroup_id][$seria->id] = 1;
                    }
                }

                if ($section_id == 0)
                {
                    // no series for product - link product to brand
                    $section_id = $brand->id;
                }

                $product->section_id = $section_id;
                $product->sections   = $sections;

                // caption
                if (preg_match('!<h1>([^<]+)</h1>\s*<div\s+class="brend">!', $page, $matches))
                {
                    $product->caption = $this->decode_trim($matches[1], 255);
                }

                // description
                if (preg_match('!<div\s*class="tocartl\s*cb">.*?(?<=</div>)\s*(<p>.*?)(?<=</p>)!s', $page, $matches))
                {
                    $product->description = $matches[1];
                }

                // price
                if (preg_match('!<div\s+class="gprc">\s*Цена:\s*<span>\s*(\d*)!i', $page, $matches))
                {
                    $product->price = new Money((int) trim($matches[1]));
                }

                // marking
                if (preg_match('!Артикул:<span[^>]*>\s*([^<]*)!i', $page, $matches))
                {
                    $marking = trim($matches[1]);
                    if (strlen($marking) > 127)
                    {
                        $this->log('WARNING: Marking for product "' . $product->caption .'" : "' . $marking . '" is too long and will be truncated!');
                    }
                    $product->marking = substr(trim($matches[1]), 0, 127);
                }

                // age
                if (preg_match('!Возраст:\s*<a[^>]*>([^<]*)!i', $page, $matches))
                {
                    $product->age = trim($matches[1]);
                }

                // volume
                if (preg_match('!Объем\s*товара\s*:<span[^>]*>\s*([^<]*)!i', $page, $matches))
                {
                    $product->volume = l10n::string_to_float(trim($matches[1]));
                }

                // weight
                if (preg_match('!Вес\s*товара\s*:<span[^>]*>\s*([^<]*)!i', $page, $matches))
                {
                    // convert kilogramms to gramms
                    $product->weight = l10n::string_to_float(trim($matches[1])) * 1000;
                }

                if ( ! isset($product->id))
                {
                    if ($this->param('create_products'))
                    {
                        $this->log('Creating product "' . $product->caption . '" for brand "' . $brand->caption . '"');
                        $product->save(FALSE, TRUE, TRUE, FALSE, FALSE);
                    }
                    else
                    {
                        $this->log('Skipped new product "' . $product->caption . '" for brand "' . $brand->caption . '"');
                        continue; // to the next product
                    }
                }
                else
                {
                    $product->save(FALSE, TRUE, TRUE, FALSE, FALSE);
                }

                // images for new products
                if ($creating || $this->param('update_products_images'))
                {
                    preg_match_all(
                        '!<a\s+href="([^"]*)"\s+rel="lightbox!',
                        $page, $m);

                    foreach (array_unique($m[1]) as $image_url)
                    {
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
            }

        } // for ($page_n = 1; ...)

    }
}
