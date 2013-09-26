<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Image manipulation support. Allows images to be thumbnailed, resized, cropped, etc.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Image extends Kohana_Image {

    /**
     * Create a thumbnail from image.
     * Difference from resize:
     *  1)  Doesn't scale image if it is already smaller, than needed
     *
     * @param  integer $width
     * @param  integer $height
     * @param  integer $master
     * @return Image
     */
    public function thumbnail($width = NULL, $height = NULL, $master = NULL)
    {
        // No resizing is needed
        if ($width == 0 && $height == 0)
            return $this;
        
        // Image is already smaller, than required
        if ($width >= $this->width && $height >= $this->height)
            return $this;

        $this->resize($width, $height, $master);

        if ($master === Image::INVERSE)
        {
            // Crop the image to the desired size
            $this->crop($width, $height);
        }
        
        return $this;
    }

}