<?php defined('SYSPATH') or die('No direct script access.'); ?>


    <?php if (isset($user)) {
        $logout_url = URL::to('frontend/acl', array('action' => 'logout'));
        $profile_url = URL::to('frontend/acl/users/control',array('action' => 'control'));
    ?>
		<a href="<?php echo $profile_url; ?>">Моя страница</a>
        <!-- Вы зашли как: <a href="<?php echo $profile_url; ?>"> <?php echo $user->email ?> --></a>
    <a href="<?php echo $logout_url; ?>">&raquo; Выход</a>
    <?php } else {
        $reg_url = URL::to('frontend/acl/users/control',array('action' => 'create'));        
        //echo '<a data-toggle="modal" href="#enterModal">Войти</a><span class="dash">|</span><a data-toggle="modal" href="#regModal">Регистрация</a>';
        echo '<a data-toggle="modal" href="#enterModal">Войти</a><span class="dash">|</span><a href="'.$reg_url.'">Стать представителем</a>';
    }?>
    
