<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$url_template = URL::to('frontend/catalog/products', array(
    'sectiongroup_name' => $sectiongroup->name,
    'path'              => '{{path}}'
)) . '/';
?>

<div class="toptext">
<div>
<?php echo Widget::render_widget('blocks', 'block', 'sec_top'); ?>
</div>
</div>

<?php
if ( ! count($sections->children()))
{
    echo '<i>Категорий нет</i>';
    return;
}
?>

<table cellpadding="0" cellspacing="0" class="brands">
<?php
foreach ($sections->children() as $section)
:
    $path = $section->alias;
    $url = URL::site($section->uri_frontend());
    //$url = str_replace('{{path}}', $path, $url_template);
?>
    <tr>
        <td>
            <a href="<?php echo $url; ?>">
                <?php
                if (isset($section->image))
                {
                    echo HTML::image('public/data/' . $section->image, array('alt' => $section->caption));
                }
                else
                {
                    echo HTML::image('public/css/logos/no-logo.gif', array('alt' => $section->caption));
                }
                ?>
            </a>
        <td>
            <h4>
                <?php
                echo
                    '<a href="' . $url . '">'
                  .     HTML::chars($section->caption)
                  .     ($section->products_count > 0 ? ' [' . $section->products_count . ']' : '')
                  . '</a>'
                ?>
            </h4>
            <div>
                <?php
                $subsections = $sections->children($section);
                $count = count($subsections); $i = 0;
                foreach ($subsections as $subsection)
                {
                    $subpath = $path . '/' . $subsection->alias;
                    $url = URL::site($subsection->uri_frontend());
                    //$url = str_replace('{{path}}', $subpath, $url_template);
                    echo
                        '<a href="' . $url . '">'
                      .     HTML::chars($subsection->caption)
                      .     ($subsection->products_count > 0 ? ' [' . $subsection->products_count . ']' : '')
                      . '</a>';
                    
                    if ($i < $count - 1)
                    {
                        echo '&nbsp;|&nbsp; ';
                    }
                    $i++;
                }
                ?>
            </div>
        </td>
    </tr>
<?php
endforeach;
?>
</table>