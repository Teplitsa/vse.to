<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div class="sections">
<table>
    <tr>
        <td>
            <?php
                // Highlight current section
                if ($section_id == 0) {
                    $selected = ' selected';
                } else {
                    $selected = '';
                }
            ?>
            <a
                href="<?php echo URL::self(array('cat_section_id' => (string) 0)); ?>"
                class="section<?php echo $selected; ?>"
                title="Показать все события"
            >
                <strong>Все события</strong>
            </a>
        </td>
    </tr>
<?php
    foreach ($sections as $section)
    :
?>
    <tr>
        <td>
            <?php
                // Highlight current section
                if ($section->id == $section_id) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }

                if ( ! $section->section_active) {
                    $active = 'section_inactive';
                } elseif ( ! $section->active) {
                    $active = 'inactive';
                } else {
                    $active = '';
                }
            ?>
            <a
                href="<?php echo URL::self(array('cat_section_id' => (string) $section->id)); ?>"
                class="section <?php echo "$selected $active"; ?>"
                title="Показать все события из раздела '<?php echo HTML::chars($section->caption); ?>'"
                style="margin-left: <?php echo ($section->level-1)*15; ?>px"
            >
                <?php echo HTML::chars($section->caption); ?>
            </a>
        </td>
    </tr>
<?php
    endforeach; //foreach ($sections as $section)
?>
</table>
</div>
