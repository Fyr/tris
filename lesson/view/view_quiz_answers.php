<span class="quiz-answer-header">Правильный ответ:</span>
<p><?=nl2br(htmlspecialchars($rightAnswer))?></p>
<span class="quiz-answer-header">Ваш ответ:</span>
<p><?=nl2br(htmlspecialchars($yourAnswer))?></p>
<span class="quiz-answer-header">Ответы других пользователей:</span>
<div class="quiz-user-answers">
<?
	foreach($aUserAnswers['Answer'] as $row) {
?>
<?=userDate($row['created'])?> <b><?=$aUserAnswers['User'][$row['user_id']]['display_name']?></b>:
<p><?=nl2br(htmlspecialchars($row['body']))?></p>
<?
	}
?>
</div>