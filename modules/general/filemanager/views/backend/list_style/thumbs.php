<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="files thumbs">
<?php

$file = new Model_File();
foreach ($files as $properties)
:
    $file->path = $properties['path'];

    if ($file->is_dot() || $file->is_hidden())
    {
        // Skip hidden
        continue;
    }

    $relative_path_encoded = URL::encode($file->relative_path);

    if ($file->is_dir())
    {
        $class = 'folder';

        $target = '';

        $url = str_replace('${path}', $relative_path_encoded, $dir_url);
    }
    else
    {
        // Strip unknown symbols
        $class = preg_replace('[^\w-]', '', $file->ext);

        if (strlen($class) && ctype_digit($class[0]))
        {
            // If extentsion starts with digit - prepend css class with "_"
            $class = '_' . $class;
        }

        // Add "file_link" class
        $class= "file_link $class";

        $target = '';

        // Url to file
        if (in_array(strtolower($file->ext), Kohana::config('filemanager.ext_editable')))
        {
            // File can be edited in backend
            $url = str_replace('${path}', $relative_path_encoded, $editfile_url);
        }
        else
        {
            // Url to view file file
            $url = File::url($file->path);
            // Open/download files in new window
            $target = 'target="_blank"';
        }
    }

    $_edit_url = str_replace('${path}', $relative_path_encoded, $edit_url);
    $_del_url  = str_replace('${path}', $relative_path_encoded, $delete_url);
?>

    <div class="file_thumb">
        <div class="ctl">
            <?php echo View_Helper_Admin::image_control($_edit_url, 'Изменить имя файла', 'controls/edit.gif', 'Переименовать'); ?>
            <?php echo View_Helper_Admin::image_control($_del_url, 'Удалить файл', 'controls/delete.gif', 'Удалить'); ?>
        </div>

        <div class="file">
            <?php
            // Render thumbnails for images
            $thumb_path = $file->get_thumb_path('preview');
            if ($thumb_path !== NULL)
            {
                $thumb_url = File::url($thumb_path);
                if ($thumb_url !== NULL)
                {
                    $img = ' style="background-image: url(' . FILE::url($thumb_path) . ');"';
                }
            }
            else
            {
                $img = '';
            }


            if ($url !== NULL)
            {
                echo '<a href="' . $url . '" class="' . $class . '" ' . $target . $img . '></a>';
            }
            else
            {
                echo '<span class="' . $class . '"' . $img . '>' . $img . '</span>';
            }
            ?>
        </div>

        <div class="file_caption">
            <?php
            if ($url !== FALSE)
            {
                echo '<a href="' . $url . '" class="file_link" ' . $target . '>' . HTML::chars($file->base_name) . '</a>';
            }
            else
            {
                echo '<span>' . HTML::chars($file->base_name) . '</span>';
            }
            ?>
        </div>
    </div>
<?php
endforeach;
?>
</div>