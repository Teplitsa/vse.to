<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Import catalog from www.gulliver.ru
 */
class Task_Import_Gulliver extends Task_Import_Web
{
    /**
     * Construct task
     */
    public function  __construct()
    {
        parent::__construct('http://www.gulliver.ru');
    }

    /**
     * Run the task
     */
    public function run()
    {
        $this->set_progress(0);


        $brands = Kohana::cache('gulliver_brands', NULL, 7200); // 2 hour

        if ($brands === NULL)
        {
            // import brands
            $brands = $this->import_brands();

            Kohana::cache('gulliver_brands', $brands);
        }

        // import products
        $this->import_products($brands);

        $this->set_status_info('Импорт завершён');
    }

    /**
     * Import brands
     *
     * @return array $brands
     */
    public function import_brands()
    {
        $brands = array();
        $this->import_brands_branch($brands);

        // Finally update activity & stats for all sections
        Model::fly('Model_Section')->mapper()->update_stats();

        return $brands;
    }


    /**
     * Import a branch of brands recursively
     *
     * @param  array $brands
     * @param  Model_Section $parent
     * @param  array $parent_info
     */
    public function import_brands_branch(array & $brands, Model_Section $parent = NULL, array $parent_info = NULL)
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

        $brand  = new Model_Section();

        $url_path = isset($parent_info['url_path']) ? $parent_info['url_path'] : '';

        // Open brand page
        $page = $this->get('/catalogue/toys' . $url_path);

        // Find all subbrands
        $result = preg_match_all(
            '!'
          . '<td\s*class="groupFoto">\s*'
          . '<a\s*href="/catalogue/toys' . $url_path . '/((group_)?\d+)/?">\s*'
          . '<img\s*src="([^"]*)"[^>]*>.*?'
          . '<a\s*class="likeText"[^>]*>\s*([^—<]*)'
          . '!uis', $page, $matches, PREG_SET_ORDER);

        $count = count($matches); $i = 0;

        foreach ($matches as $match)
        {
            if ($parent === NULL)
            {
                // Report progress only for top-level brands
                $this->set_status_info('Importing brands: ' . $i . ' of ' . $count);
                $this->set_progress((int) (100 * $i / $count));
                $i++;
            }

            $id        = $match[1];
            $caption   = $this->decode_trim($match[4], 255);
            $image_url = trim($match[3]);

            $parent_id = isset($parent_info['id']) ? $parent_info['id'] : 0;
            $brand_info = array(
                'id'        => $id,
                'caption'   => $caption,
                'image_url' => $image_url,
                'url_path'  => $url_path . '/' . $id
            );
            $brands[$parent_id][$id] = $brand_info;

            // get brand description
            $page = $this->get('/catalogue/toys' . $url_path . '/' . $id . '/?mode=info');

            if (preg_match('!<div id="Content">\s*<h3[^>]*>.*?</h3>(.*?)</div>!uis', $page, $m))
            {
                // cut out images, links and empty <p> from description
                $description = trim($m[1]);
                $description = preg_replace('!<img[^>]*>!', '', $description);
                $description = preg_replace('!<a[^>]*>.*?</a>!', '', $description);
                $description = preg_replace('!<p[^>]*>(\s|&nbsp;)*</p>!', '', $description);
            }
            else
            {
                $description = '';
            }

            if ($parent === NULL)
            {
                $brand->find_by_caption_and_sectiongroup_id($caption, $sectiongroup_id);
            }
            else
            {
                $brand->find_by_caption_and_parent($caption, $parent);
            }
            $creating = ( ! isset($brand->id));

            $brand->sectiongroup_id = $sectiongroup_id;
            if ($parent !== NULL)
            {
                $brand->parent_id = $parent->id;
            }
            $brand->caption     = $caption;
            $brand->description = $description;

            // Select all additional properties
            $brand->propsections = $propsections;

            $brand->save(FALSE, FALSE);
            // Reload brand to obtain correct id, lft and rgt values
            $brand->find($brand->id, array('columns' => array('id', 'lft', 'rgt', 'level')));

            // Brand logo
            if ($creating)
            {
                $image_file = $this->download_cached($image_url);

                if ($image_file)
                {
                    $image = new Model_Image();

                    /*
                    if ( ! $creating)
                    {
                        // Delete possible existing images for section
                        $image->delete_all_by_owner_type_and_owner_id('section', $brand->id);
                    }
                     */

                    try {
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

            // ----- subbrans
            $this->import_brands_branch($brands, $brand, $brand_info);

        } // foreach $brand

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
        $product  = new Model_Product();

        // Caclulate progress info
        $count = count($brands, COUNT_RECURSIVE) - count($brands); $i = 0;
        $this->set_status_info('Importing products');

        $_imported = array(/*
            '225', 'group_46527', '226', 'group_41053', 'group_56132', 'group_46989',
            '231', 'group_46487', 'group_46581', '241', 'group_51213', 'group_51814',
            'group_47677', '222', '232', 'group_54376', 'group_55896', '27292',
            '243', '239', '240', '239', '240', 'group_57187' , 'group_57469', 'group_58056',
            'group_58187', 'group_58326', 'group_45062', 'group_45131', 'group_53175',
            'group_41112', 'group_41113', 'group_41114', 'group_46367', 'group_54258',
            'group_48693', 'group_57566', 'group_32473', 'group_32474', 'group_32475',
            'group_32476', 'group_32478', 'group_32479', 'group_32480', 'group_38544',
            'group_49452', 'group_46096', 'group_46211', 'group_35451', 'group_35553',
            'group_35806', 'group_35804', 'group_35805', 'group_35802', 'group_35803'
             *
             */
        );
        $status_info = '';

        foreach ($brands as $brands_branch)
        {
            foreach ($brands_branch as $brand_info)
            {
                if (in_array($brand_info['id'], $_imported))
                    continue;

                $status_info .= $brand_info['id'] . "\n";
                $this->set_status_info($status_info);

                $this->set_progress((int) (100 * $i / $count));
                $i++;


                $brand->find_by_caption_and_sectiongroup_id($brand_info['caption'], $sectiongroup_id, array('columns' => array('id', 'lft', 'rgt', 'level')));

                // Obtain first page of the paginated results
                $page = $this->get('/catalogue/toys' . $brand_info['url_path']);

                // Detect number of pages if products display is paginated
                $offset     = 0;
                $max_offset = 0;
                $per_page   = 24;

                preg_match_all('!<a.*?href="[^"]*from=(\d+)!', $page, $matches);
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
                        $page = $this->get('/catalogue/toys' . $brand_info['url_path'] . '/?from=' . $offset);
                    }

                    // detect maximum page number on every page
                    preg_match_all('!<a.*?href="[^"]*from=(\d+)!', $page, $matches);
                    if ( ! empty($matches[1]))
                    {
                        $new_max_offset = max($matches[1]);
                        if ($new_max_offset > $max_offset)
                        {
                            $max_offset = $new_max_offset;
                        }
                    }

                    $result = preg_match_all(
                        '!'
                      . '<td\s*class="goodFoto">\s*'
                      . '<a\s*href="/catalogue/toys' . $brand_info['url_path'] . '/([^"/]*)'
                      . '!uis', $page, $matches);

                    foreach (array_unique($matches[1]) as $product_id)
                    {
                        $url = '/catalogue/toys' . $brand_info['url_path'] . '/' . $product_id;
                        $page = $this->get($url);

                        // caption
                        if (preg_match('!<div\s*id="Content">\s*<h3[^>]*>\s*([^<]+)!i', $page, $m))
                        {
                            $caption = $this->decode_trim($m[1], 255);
                        }
                        else
                        {
                            FlashMessages::add('Unable to determine caption for product ' . $url, FlashMessages::ERROR);
                            continue;
                            //throw new Kohana_Exception('Unable to determine caption for product :url ', array(':url' => $url));
                        }

                        $product->find_by_caption_and_section_id($caption, $brand->id, array('with_properties' => FALSE));
                        $creating = ( ! isset($product->id));

                        // Set caption
                        $product->caption = $caption;

                        // Link to brand
                        $product->section_id = $brand->id;

                        // Marking
                        if (preg_match('!Арт.\s*([^<]*)!', $page, $m))
                        {
                            $product->marking = $this->decode_trim($m[1], 63);
                        }

                        // Description
                        if (preg_match('!<img\s*class="goodFotoBig"[^>]*>(.*?)</div>!is', $page, $m))
                        {
                            $product->description = trim($m[1]);
                        }

                        $product->save();

                        // Product images
                        $image = new Model_Image();

                        if ($creating)
                        {

                            /*
                            if ( ! $creating)
                            {
                                // Delete already existing images for product
                                $image->delete_all_by_owner_type_and_owner_id('product', $product->id);
                            }
                             */

                            // main image
                            preg_match_all(
                                '!<img\s*class="goodFotoBig"\s*src="([^"]*)"!',
                                $page, $m1);

                            // additional popup images
                            preg_match_all(
                                '!<a\s*href="([^"]*)"\s*onclick="wop\(!',
                                $page, $m2);

                            // additional non-popup images
                            if (preg_match('!<div class="goodGalery">.*?</div>!is', $page, $m))
                            {
                                preg_match_all('~<img\s*src="([^"]*)"[^>]*>\s*(?!</a>)~i', $m[0], $m3);
                            }
                            else
                            {
                                $m3 = array(1 => array());
                            }

                            foreach (array_unique(array_merge($m1[1], $m2[1], $m3[1])) as $image_url)
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

                    } // foreach $offset
                } // foreach $product_id
            } //foreach $brand
        }// foreach $brands_branch
    }
}
