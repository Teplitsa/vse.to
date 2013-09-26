<?php defined('SYSPATH') or die('No direct script access.');

class Model_Image extends Model
{
    // Maximum allowed number of image size variants
    const MAX_SIZE_VARIANTS = 4;
    
    /**
     * Return settings for image thumbs
     * 
     * @return array
     */
    public function get_thumb_settings()
    {
        if ($this->config === NULL)
        {
            throw new Kohana_Exception('"config" is not defined for image');
        }

        $settings = Kohana::config('images.' . $this->config);

        if ( ! is_array($settings))
        {
            throw new Kohana_Exception('Config entry ":config" not found for image',
                array(':config' => $this->config));
        }
        return $settings;
    }

    /**
     * Generate file name for image size variant
     * 
     * @param integer $i
     */
    public function gen_filename($i, $type = 'jpg')
    {
        $rnd = Text::random('numeric', 3);
        return 'img'.$this->id.'_'.$this->owner_type.$this->owner_id.'_size'.$i.'_'.$rnd.'.'.$type;
    }

    /**
     * Get full path to specified image size variant
     * 
     * @param  integer $i
     * @return string
     */
    public function image($i = 1)
    {
        if (isset($this->_properties["image$i"]))
        {
            $folder = DOCROOT . 'public/data';
            return $folder . '/' . $this->_properties["image$i"];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get uri of the specified image size variant
     *
     * @param  integer $i
     * @return string
     */
    public function uri($i = 1)
    {
        return 'public/data/' . $this->_properties["image$i"];
    }
    
    /**
     * Get uri of the specified image size variant
     *
     * @param  integer $i
     * @return string
     */
    public function url($i = 1)
    {
        return URL::base(FALSE) . $this->uri($i);
    }


    /**
     * Get the source file to use for creating / updating image
     *
     * This property can be specified explicitly:
     *  $image->image_file = '/path/to/some_image.jpg'
     *
     * If this property is not specified, than a 'tmp_file' proprtety will be returned
     */
    public function get_source_file()
    {
        if (isset($this->_properties['source_file']))
        {
            return $this->_properties['source_file'];
        }
        else
        {
            return $this->tmp_file;
        }
    }
    
    /**
     * Move an uploaded file from PHP temp dir to another temporary directory, were
     * it can be processed.
     *
     * We have to do it because sometimes PHP security settings prohibit operating
     * on temporary uploaded files directly.
     */    
    public function get_tmp_file()
    {
        if ( ! isset($this->_properties['tmp_file']))
        {
            if (is_array($this->file))
            {
                $tmp_file = File::upload($this->file);
            }
            else
            {
                $tmp_file = FALSE;
            }
            $this->_properties['tmp_file'] = $tmp_file;
        }
        return $this->_properties['tmp_file'];
    }

    /**
     * Save an uploaded image
     */
    public function save($force_create = FALSE)
    {
        try {

            $creating = ( ! isset($this->id) || $force_create);
            
            if ( ! isset($this->id))
            {
                // Dummy save for new images to obtain image id
                parent::save($force_create);
            }


            
            $source_file = $this->source_file;
            if ( ! empty($source_file))
            {
                // ----- An image was uploaded

                // Delete previous thumbnails (if any)
                for ($i = 1; $i <= Model_Image::MAX_SIZE_VARIANTS; $i++)
                {
                    $path = $this->image($i);
                    if ($path != '')
                    {
                        @unlink($path);
                    }
                }

                // Create new thumbnails from uploaded image                
                $img = new Image_GD($source_file);

                $i = 1;
                foreach ($this->get_thumb_settings() as $settings)
                {
                    $thumb = clone $img;

                    $thumb->thumbnail($settings['width'], $settings['height'], (isset($settings['master']) ? $settings['master'] : NULL));

                    // Generate thumbnail file name
                    $this->__set("image$i", $this->gen_filename($i));

                    // Save thumbnail to file
                    $thumb->save($this->image($i));

                    $this->__set("width$i", $thumb->width);
                    $this->__set("height$i", $thumb->height);
                    
                    $i++;
                }

                // Save image
                parent::save();
            }

        }
        catch (Exception $e)
        {
            // Shit happened while saving image
            if ($creating)
            {
                $this->delete();
            }

            if (Kohana::$environment === Kohana::DEVELOPMENT)
            {
                throw $e;
            }
            else
            {
                $this->error($e->getMessage());
            }
        }
    }

    /**
     * Delete all image files for image
     */
    protected function _delete_files(Model_Image $image)
    {
        // Delete thumbnails
        for ($i = 1; $i <= Model_Image::MAX_SIZE_VARIANTS; $i++)
        {
            $path = $image->image($i);
            if ($path != '')
            {
                @unlink($path);
            }
        }
    }

    /**
     * Delete image
     */
    public function delete()
    {
        // Delete image files
        $this->_delete_files($this);
        // Delete from DB
        parent::delete();
    }

    /**
     * Delete images by owner
     *
     * @param string $owner_type
     * @param integer $owner_id
     */
    public function delete_all_by_owner_type_and_owner_id($owner_type, $owner_id)
    {
        // Delete files for all images
        $images = $this->find_all_by_owner_type_and_owner_id($owner_type, $owner_id);
        foreach ($images as $image)
        {
            $this->_delete_files($image);
        }

        // Delete images from DB
        $this->mapper()->delete_all_by_owner_type_and_owner_id($this, $owner_type, $owner_id);
    }
    
    /**
     * Model destructor
     */
    public function  __destruct()
    {
        // Don't forget to delete temporary file, if it exists
        if ( ! empty($this->_properties['tmp_file']))
        {
            @unlink($this->_properties['tmp_file']);
        }
    }
}