<?
	if (!$aFavorite) {
?>
		<b>Вы не добавили в Избранное ни одного параграфа</b>
		<br /><br />
		Нажмите на иконку звездочки в правом верхнем углу чтобы добавить текущий параграф в Избранное.
<?
	}
	foreach($aFavorite as $para) {
?>
		<div id="fav-list-item_<?=$para['id']?>" class="fav-list-item">
			<span class="small"><?=$para['chapter_title']?></span>
			<a href="javascript:void(0)" title="Удалить из Избранного" onclick="lesson.setFavorite(<?=$para['id']?>, 0)"><i class="icon-remove"></i></a>&nbsp;
			<a href="javascript:void(0)" onclick="lesson.switchPara(<?=$para['id']?>)"><?=$para['title']?></a>
		</div>
<?
	}
?>