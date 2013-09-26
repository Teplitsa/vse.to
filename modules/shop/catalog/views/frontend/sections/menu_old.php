<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="caption">
    <?php
    echo
        '<a href="' . URL::site($sectiongroup->uri_frontend()) . '">'
      .     (($sectiongroup->name == 'brand') ? 'вернуться к списку брендов' : 'вернуться к категориям')
      . '</a> &raquo;<br />';
    ?>
    <div>
        <a href="<?php echo URL::site($root->uri_frontend()); ?>" class="section">
            <?php echo HTML::chars($root->caption); ?>
        </a>&nbsp;:
    </div>    
</div>

<?php
if ($sections->has_children($root))
:
?>
    <div class="caption2">Подразделы:</div>

    <div class="navigation"><ul>
    <?php
    foreach ($sections->children($root) as $subsection)
    {
        echo
            '<li>'
          .     '<a href="' . URL::site($subsection->uri_frontend()) . '"' . ($subsection->id == $current->id ? ' class="active"' : '') . '>'
          .         HTML::chars($subsection->caption)
          .         ($subsection->products_count > 0 ? ' (' . $subsection->products_count . ')' : '')
          .     '</a>'
          . '</li>';
    }
    ?>
    </ul></div>
<?php
endif;
?>

<!--
<div class="caption2">
Товары LEGO присутствуют в категориях:
</div>

<div class="navigation">
<ul>
<li><a href="#">Конструкторы</a></li>
<li><a href="#">Настолные игры</a></li>
</ul>
</div>
-->