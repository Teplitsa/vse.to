<?php defined('SYSPATH') or die('No direct script access.'); ?>

<table class="faq"><tr>
    <td class="questions_cell">
        
        <div class="questions">
            <div class="question">
                <div class="q"><strong>Вопрос:</strong> <?php echo HTML::chars($question->question); ?></div>
                <div class="a"><strong>Ответ:</strong> <?php echo HTML::chars($question->answer); ?></div>
                <div class="date"><strong><?php echo $question->date; ?></strong></div>
            </div>
        </div>
        <br />
        <a href="<?php echo $faq_url; ?>">&laquo; К списку вопросов</a>
    </td>
    
    <td class="faq_form_cell">
        <?php echo $form; ?>
    </td>
</tr></table>