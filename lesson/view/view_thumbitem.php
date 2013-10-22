
	<tr>
		<td colspan="2" style="padding-top: 20px;">
			<a id="lesson_<?=$_lessonID?>" href="<?=$url?>" title="<?=$title?>"><?=$link_text?></a>
<?
	if ($lEditMode) {
?>
			<div align="left" class="btn-group pull-right">
				<a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-mini dropdown-toggle pull-right">
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right">
					<li>
						<a href="javascript:void(0)" onclick="lesson.lessonUpdate(<?=$_lessonID?>)"><i class="icon-edit"></i> Изменить название</a>
					</li>
					<li>
						<a href="javascript:void(0)" onclick="lesson.lessonImageUpload(<?=$_lessonID?>)"><i class="icon-picture"></i> Загрузить изобр.</a>
					</li>
					<li>
						<a href="javascript:void(0)" onclick="lesson.lessonDelete(<?=$_lessonID?>)"><i class="icon-remove"></i> Удалить</a>
					</li>
				</ul>
			</div>
			<div id="lessonImage_<?=$_lessonID?>"></div>
<?
	}
?>
		</td>
	</tr>
	<tr>
		<td>
<?
	if ($thumb)	{
?>
			<a href="<?=$url?>" title="<?=$title?>"><img alt="<?=$link_text?>" src="<?=$thumb?>"></a>
<?
	}
?>
		</td>
		<td>
<?
	if ($last_visited) {
?>

			<span class="small">Дата просмотра:</span><br />
			<?=userDate($last_visited)?><br/>
<?
	}
?>
		</td>
	</tr>
