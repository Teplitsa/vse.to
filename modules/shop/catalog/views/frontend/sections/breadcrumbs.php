<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if ( ! count($sections))
{
    return '';
}
?>

<div class="sections_breadcrumbs">
    <a href="<?php echo URL::site(''); ?>">Главная</a>
    <?php
    foreach ($sections as $section)
    :
        $_url = URL::site($section->uri_frontend());
    ?>
        &raquo;
        <a href="<?php echo $_url; ?>">
            <?php echo HTML::chars($section->caption); ?>
        </a>
    <?php
    endforeach;
    ?>
</div>