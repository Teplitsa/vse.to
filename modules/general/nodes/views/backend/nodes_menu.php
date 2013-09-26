<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
// Add style to the layout
Layout::instance()->add_style(Modules::uri('nodes') . '/public/css/backend/nodes.css');

// Current route name
$current_route = Route::name(Request::current()->route);
?>

<?php
if ( ! count($nodes))
{
    // No nodes yet
    echo '<i>страниц нет</i>';
    return;
}

?>

<div class="nodes_menu">
<?php
    foreach ($nodes as $node)
    :
        $type_info = Model_Node::node_type($node->type);

        // Is current node selected?
        $selected = FALSE;
        if (isset($type_info['model']))
        {
            $selected = ($node->id == $node_id);
        }
        else
        {
            $selected = ($current_route == $type_info['backend_route']);
        }

        $url = $node->backend_url;

        
        // Highlight current node
        if ($selected) {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        // Highlight inactive nodes
        if ( ! $node->node_active) {
            $active = 'node_inactive';
        } elseif ( ! $node->active) {
            $active = 'inactive';
        } else {
            $active = '';
        }
?>
    <div class="node <?php echo "$selected $active";?>" style="padding-left: <?php echo ($node->level-1)*15; ?>px">
        <a class="node_caption" href="<?php echo $url;?>">
            <?php echo HTML::chars($node->caption); ?>
        </a>
    </div>
<?php
    endforeach;
?>
</div>
