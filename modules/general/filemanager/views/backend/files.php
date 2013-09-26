<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Set up urls
$mkdir_url  = URL::to('backend/filemanager',
                array('action' => 'mkdir', 'fm_path' => URL::encode($dir->relative_path), 'fm_tinymce' => $in_tinymce, 'fm_root' => $root),
                TRUE
            );
$dir_url    = URL::self(array('fm_path' => '${path}'));
$edit_url   = URL::to('backend/filemanager', array('action' => 'rename', 'fm_path' => '${path}', 'fm_tinymce' => $in_tinymce, 'fm_root' => $root), TRUE);
$delete_url = URL::to('backend/filemanager', array('action' => 'delete', 'fm_path' => '${path}', 'fm_tinymce' => $in_tinymce, 'fm_root' => $root), TRUE);

$editfile_url = URL::to('backend/filemanager', array('action' => 'edit', 'fm_path' => '${path}', 'fm_tinymce' => $in_tinymce, 'fm_root' => $root), TRUE);

// ----- Split current path into elements
$path_elements = array();

// First - root path (relative to docroot) to make it more clear where you are now
$parts = explode('/', File::relative_path($dir->root_path, DOCROOT));
foreach ($parts as $i => $path_element)
{
    if ($i < count($parts) - 1)
    {
        $path_elements[] = '<strong>' . $path_element . '</strong>';
    }
    else
    {
        // Last path element is navigatable
        $path_elements[] = '<a href="' . URL::self(array('fm_path' => '')) . '">' . $path_element . '</a>';
    }
}

// Relative path - make it navigatable

$tmp_path = '';
$parent_path = '';
foreach (explode('/', $dir->relative_path) as $path_element)
{
    $parent_path = $tmp_path;

    if ($tmp_path != '')
    {
        $tmp_path .= '/' . $path_element;
    }
    else
    {
        $tmp_path = $path_element;
    }

    $path_elements[] = '<a href="' . str_replace('${path}', URL::encode($tmp_path), $dir_url) .'">' . $path_element . '</a>';
}

//array_unshift($path_elements, '<a href="' . URL::self(array('fm_path' => '')) . '">&raquo;</a>');

?>

<div class="buttons">
    <a href="<?php echo $mkdir_url; ?>" class="button button_add">Создать директорию</a>
</div>


<?php
// ----- File upload form
echo $file_upload;
?>

<!-- File list display styles -->
<div class="list_styles_select">
    Отображение:
    <?php
    foreach (array(
            'list'   => 'список',
            'thumbs' => 'миниатюры'
        ) as $style => $label)
    {
        $selected = ($style == $list_style) ? ' selected' : '';
        echo
            '<a href="' . URL::self(array('fm_style' => $style)) . '" class="as_' . $style . $selected . '">'
          .     $label
          . '</a>';
    }
    ?>
</div>

<!-- Current address -->
<div class="current_path">
    Адрес:
    <?php echo implode('&nbsp;/&nbsp;', $path_elements); ?>

    <?php
    if ($parent_path == '')
    {
        $parent_url = URL::self(array('fm_path' => ''));
    }
    else
    {
        $parent_url = str_replace('${path}', URL::encode($parent_path), $dir_url);
    }

    //echo View_Helper_Admin::image_control($parent_url, 'Вверх', 'images/up_folder.gif', 'Вверх', 'filemanager');
    ?>
</div>

<!-- Files list -->
<?php require('list_style/' . $list_style . '.php'); ?>