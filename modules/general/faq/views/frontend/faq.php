<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php
$url = URL::to('frontend/faq', array('action' => 'question', 'id' => '{{id}}'));
?>

<table class="faq"><tr>
    <td class="questions_cell">
<?php
if ( ! count($questions)):
    echo '<i>Вопросов пока нет</i>';
else:
?>

<?php if (isset($pagination)) echo $pagination; ?>
        
<div class="questions">
<?php
foreach ($questions as $question)
:
    $_url = str_replace('{{id}}', $question->id, $url);
?>
    <div class="question">
        <div class="q"><strong>Вопрос:</strong> <?php echo HTML::chars($question->question); ?></div>
        <div class="a"><strong>Ответ:</strong> <?php echo HTML::chars($question->answer); ?></div>
        <div class="date"><strong><?php echo $question->date; ?></strong> <a href="<?php echo $_url; ?>">Постоянная ссылка на этот вопрос</a></div>
    </div>
<?php
endforeach;
?>
</div>

<?php if (isset($pagination)) echo $pagination; ?>

<?php
endif;
?>
    </td>
    
    <td class="faq_form_cell">
        <?php echo $form; ?>
    </td>
</tr></table>