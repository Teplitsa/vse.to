<?php defined('SYSPATH') or die('No direct script access.'); ?>


<?php
if ($user->id && count($fields)) {
// ----- Set up urls
$user_url = URL::to('frontend/acl/users/control', array('action' => 'control','user_id' => $user->id), TRUE);
?>

<table class="user_card">
    <tr>
        <?php foreach ($fields as $field) {
            switch ($field) {
                case 'image':
                    $image_info = $user->image(4);
                    echo '<td class="image"><a href="' . $user_url . '" class="user_select" id="user_' . $user->id. '">' 
                            . HTML::image('public/data/' . $image_info['image'], array('width' => $image_info['width'],'height' => $image_info['height'])) . '</a>';                
                    break;
                case 'name':
                    echo '<td class="name"><a href="' . $user_url . '" class="user_select" id="user_' . $user->id. '">' 
                            . $user->name . '</a></td>';
                    break;
            }
        } ?>
    </tr>
</table>
<?php } ?>
