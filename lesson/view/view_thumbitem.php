<?
if ($addLesson) {
	$url = 'javascript:void(0)';
	$title = '';
	$link_text = '';
}
?>
	<tr>
		<td>
			<a href="<?=$url?>" title="<?=$title?>"><img alt="<?=$thumb_alt?>" src="<?=$thumb_src?>"></a>
		</td>
		<td>
<?
	if ($last_visited) {
?>

			<span class="small">Дата просмотра:</span><br />
			<?=userDate($last_visited)?><br/>
<?
	}
	if ($addLesson) {
?>
			<a class="btn btn-mini btn-primary" href="javascript:void(0)" onclick="lesson.lessonAdd(<?=$_lessonID?>)"><i class="icon icon-plus"></i> Урок</a>
<?
	}
?>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="lesson-title">
			<a href="<?=$url?>" title="<?=$title?>"><?=$link_text?></a>
		</td>
	</tr>