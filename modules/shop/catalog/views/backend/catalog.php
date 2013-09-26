<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
if (isset($caption))
{
    echo '<h1>' . HTML::chars($caption) . '</h1>';
}

if (isset($tabs))
{
    echo $tabs;
}
?>

<div class="panel">
    <div class="content">
        <table class="layout"><tr>
            <td class="layout">
                    <?php echo $products; ?>
            </td>

            <td class="layout sections_cell">
                    <?php echo $sections; ?>
            </td>
        </tr></table>
    </div>
</div>