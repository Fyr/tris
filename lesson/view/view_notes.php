<?
	if ($aNotes) {
?>
<b>Заметки для текущего параграфа</b>
<?
		foreach($aNotes as $post) {
?>
<div id="note_<?=$post['id']?>" class="post-rec">
	<?=userDate($post['created'])?></b>
	<div align="left" class="btn-group pull-right">
		<a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-mini dropdown-toggle pull-right">
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu pull-right">
			<li>
				<a href="javascript:void(0)" onclick="lesson.noteUpdate(<?=$post['id']?>)"><i class="icon-edit"></i> Изменить</a>
			</li>
			<li>
				<a href="javascript:void(0)" onclick="lesson.noteDelete(<?=$post['id']?>)"><i class="icon-remove"></i> Удалить</a>
			</li>
		</ul>
	</div>
	<div class="note"><?=nl2br(htmlspecialchars($post['body']))?></div>
	<div class="editnote"></div>
</div>
<?
		}
	} else {
?>
<p>Нет заметок для текущего параграфа</p>
<?
	}
	if ($aResults) {
?>
<b>Остальные заметки</b>
<?
		foreach($aResults as $post) {
?>
<div id="note_<?=$post['id']?>" class="post-rec">
	<?=userDate($post['created'])?><br />
	<a href="javascript:void(0)" onclick="lesson.switchPara(<?=$post['para_id']?>)"><?=$post['para_title']?></a>
	<div align="left" class="btn-group pull-right">
		<a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-mini dropdown-toggle pull-right">
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu pull-right">
			<li>
				<a href="javascript:void(0)" onclick="lesson.noteUpdate(<?=$post['id']?>)"><i class="icon-edit"></i> Изменить</a>
			</li>
			<li>
				<a href="javascript:void(0)" onclick="lesson.noteDelete(<?=$post['id']?>)"><i class="icon-remove"></i> Удалить</a>
			</li>
		</ul>
	</div>
	<div class="note"><?=nl2br(htmlspecialchars($post['body']))?></div>
	<div class="editnote"></div>
</div>
<?
		}
	}
?>

