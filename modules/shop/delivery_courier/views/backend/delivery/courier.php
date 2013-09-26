<?php defined('SYSPATH') or die('No direct script access.'); ?>


<div class="simple_panel">
<?php
if (isset($caption))
{
    echo '<div class="simple_panel_caption">' . $caption . '</div>';
}
?>

    <table class="content_layout"><tr>
        <td class="content_layout" style="width: 500px;">
            <?php echo $form; ?>
        </td>

        <td class="content_layout">
            <?php echo $zones; ?>
        </td>
    </tr></table>
</div>