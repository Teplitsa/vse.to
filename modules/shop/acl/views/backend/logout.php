<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$logout_url = URL::to('backend/acl', array('action' => 'logout'));
?>
<div class="logout">
    <a href="<?php echo $logout_url; ?>">&raquo; Выход</a>
</div>
