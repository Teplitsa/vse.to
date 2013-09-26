<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Import catalog from www.saks.ru
 */
class Task_Import_Iks extends Task
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
        // parse sections
        $sections = $this->parse_sections();

        // import sections
        $this->import_sections($sections);

        // import products
        $this->import_products($sections);

        $this->set_status_info('Импорт завершён');
    }

    /**
     * Parse sections
     *
     * @return array
     */
    public function parse_sections()
    {
        // ----- parse brands and series ---------------------------------------
        $sections = array(0 => array());

        // Retrieve list of all brands with links from catalog page ...
        $this->set_status_info('Parsing collections and categories');

        // ----- parse brands
        $page = $this->get('/section18/');

        if ( ! preg_match_all('!<a\s*href="/section18/ctg(\d+)/\?descript">\s*<strong>([^<]*)</strong>(<br[^>]*>)?(.*?)</a>!is', $page, $matches, PREG_SET_ORDER))
        {
            $this->log('No brands found', Log::WARNING);
            return;
        }

        foreach ($matches as $match)
        {
            $import_id   = trim($match[1]);
            $caption     = $this->decode_trim($match[2], 255);
            $description = $this->decode_trim($match[4]);
            $url         = '/section18/ctg' . $import_id . '/';

            $sections[0][$import_id] = array(
                'import_id'   => $import_id,
                'caption'     => $caption,
                'description' => $description,
                'url'         => $url
            );
        }

        // brand logos
        preg_match_all('!<a\s*href="/section18/ctg(\d+)/">\s*<img\s*src="([^"]*)!is', $page, $matches, PREG_SET_ORDER);
        foreach ($matches as $match)
        {
            $import_id = trim($match[1]);
            $image_url = trim($match[2]);

            if (isset($sections[0][$import_id]))
            {
                $sections[0][$import_id]['image_url'] = $image_url;
            }
        }

        // ----- parse series
        $page = $this->get('/map/');
        
        foreach ($sections[0] as $section_info)
        {
            preg_match_all('!<a href="(/section18/ctg' . $section_info['import_id'] . '/ctg(\d+)/)">(.*?)</a>!is', $page, $matches, PREG_SET_ORDER);

            foreach ($matches as $match)
            {
                $import_id = trim($match[2]);
                $caption   = $this->decode_trim($match[3], 255);
                $url       = trim($match[1]);

                $sections[$section_info['import_id']][$import_id] = array(
                    'import_id' => $import_id,
                    'caption'   => $caption,
                    'url'       => $url
                );
            }
        }

        return $sections;
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
            $this->set_status_info('Importing brands and series');
        }

        $site_id         = 1; // @TODO: pass as parameter to task
        $sectiongroup_id = 1; // must be brands section group id @TODO: pass as parameter to task

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
            return; // No sections in branch

        foreach ($sections[$parent_import_id] as $import_id => $section_info)
        {
            $section->find_by_web_import_id_and_sectiongroup_id($section_info['import_id'], $sectiongroup_id);
            $creating = ( ! isset($section->id));

            // web_import_id
            $section->web_import_id   = $section_info['import_id'];

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
                $section->description = $section_info['description'];
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
            if (    isset($section_info['image_url'])
                 && ($creating || $this->param('update_section_images')))
            {
                $image = new Model_Image();

                if ( ! $creating)
                {
                    // Delete existing images
                    $image->delete_all_by_owner_type_and_owner_id('section', $section->id);
                }

                $image_file = $this->download_cached($section_info['image_url']);

                if ($image_file)
                {
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
            $this->import_sections($sections, $section);

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
    }

    /**
     * Import products
     *
     * @param array $sections
     */
    public function import_products(array $sections)
    {
        $site_id         = 1; // @TODO: pass as parameter to task
        $sectiongroup_id = 1; // must be brands section group id @TODO: pass as parameter to task

        $section = new Model_Section();
        $product = new Model_Product();

        // Caclulate progress info
        $count = count($sections, COUNT_RECURSIVE) - count($sections) - count($sections[0]); $i = 0;
        $this->set_status_info('Importing products');

        $status_info = '';

        foreach ($sections as $sections_branch)
        {
            foreach ($sections_branch as $section_info)
            {
                $this->set_progress((int) (100 * $i / $count));
                $i++;
                
                if (isset($sections[$section_info['import_id']]))
                    continue; // Import products only for the leaf sections

                if ( ! isset($section_info['id']))
                    continue;

                die('Trolololololololololololololololololo!');

                $section->find($section_info['id']);

                if ( ! isset($section->id))
                    throw new Kohana_Exception('Section was not found by id :id', array(':id' => $section_info['id']));

                if ( ! isset($section_info['url']))
                    throw new Kohana_Exception('Section url is not set for section :id', array(':id' => $section_info['id']));

                $offset     = 0;
                $max_offset = 0;
                $per_page   = 10;

                while ($offset <= $max_offset)
                {
                    $url = ($offset == 0) ? $section_info['url'] : $section_info['url'] . '?p=' . $offset;
                    $page = $this->get($url);

                    echo $page;
                    die;

                    // Detect maximum offset and number of products per page if the result is paginated
                    preg_match_all('!<a\s*href="[^"]*\?p=(\d+)!', $page, $matches);
                    if ( ! empty($matches[1]))
                    {
                        $max_offset = max($matches[1]);
                        if ($offset == 0)
                        {
                            $per_page = min($matches[1]);
                        }

                        var_dump($max_offset);
                        var_dump($per_page);
                        die;
                    }

                    $offset += $per_page;
                }

            }
        }
/*
            $section->find_by_caption_and_sectiongroup_id($brand_info['caption'], $sectiongroup_id);

            foreach ($sections[$brand_info['id']] as $subbrand_info)
            {
                $this->set_progress((int) (100 * $i / $count));
                $i++;

                $subbrand->find_by_caption_and_parent($subbrand_info['caption'], $section);

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

                            if ( ! $creating)
                            {
                                // Delete already existing images for product
                                $image->delete_all_by_owner_type_and_owner_id('product', $product->id);
                            }

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
 */
    }

}
