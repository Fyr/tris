<?
	if ($aResults) {
		foreach($aResults as $post) {
?>
<div id="post_<?=$post['id']?>" class="post-rec">
	<?=userDate($post['created'])?> <b><?=$post['username']?></b>
<?
	if ($post['user_id'] == $user_ID) {
?>
	<div align="left" class="btn-group pull-right">
		<a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-mini dropdown-toggle pull-right">
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu pull-right">
			<li>
				<a href="javascript:void(0)" onclick="lesson.postUpdate(<?=$post['id']?>)"><i class="icon-edit"></i> Изменить</a>
			</li>
			<li>
				<a href="javascript:void(0)" onclick="lesson.postDelete(<?=$post['id']?>)"><i class="icon-remove"></i> Удалить</a>
			</li>
		</ul>
	</div>
<?
	}
?>
	<div class="post"><?=nl2br(htmlspecialchars($post['body']))?></div>
	<div class="editpost"></div>
</div>
<?
		}
	} else {
?>
<p>Для данного урока пока нет комментариев</p>
<?
	}
?>