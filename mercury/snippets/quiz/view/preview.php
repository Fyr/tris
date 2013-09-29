<?
	$edit = isset($_POST['edit']) && $_POST['edit'];
	$style = (isset($_POST['margin_left']) && $_POST['margin_left']) ? ' margin-left: '.$_POST['margin_left'].'%;' : '';
	$style.= (isset($_POST['margin_right']) && $_POST['margin_right']) ? ' margin-right: '.$_POST['margin_right'].'%;' : '';
?>
<style type="text/css">
@import "<?=$assetsPath?>css/style.css";
</style>
<div class="quiz-body" align="<?=@$_POST['align']?>" style="<?=$style?>">
	<div class="quiz-header">
		<span <? if ($edit) {?>contenteditable="true" onchange="onChangeEditable(this, 'title')"<? } ?>><?=@$_POST['title']?></span>
	</div>
	<span <? if ($edit) {?>contenteditable="true" onchange="onChangeEditable(this, 'editable')"<? } ?>><?=@$_POST['editable']?></span>
	<div class="quiz-answer">
		<span class="quiz-answer-header">Правильный ответ:</span>
		<span class="quiz-right-answer"<? if ($edit) {?> contenteditable="true" onchange="onChangeEditable(this, 'answer')"<? } ?>><?=@$_POST['answer']?></span>
	</div>
	<form action="" method="post">
		<textarea name="quiz_post"></textarea>
		<div align="right">
			<button class="btn btn-inverse btn-small" type="button" data-quiz_onsubmit="">Отправить</button>
		</div>
	</form>
</div>