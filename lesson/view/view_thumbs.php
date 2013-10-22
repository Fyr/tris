<?
	if ($thumbInfo['LastVisited']) {
?>
	<!--b>Последние просмотренные уроки</b-->
	<table width="100%">

<?
		foreach($thumbInfo['LastVisited'] as $row) {
			$_lessonID = $row['id'];
			$thumb = (isset($thumbInfo['Thumb'][$_lessonID])) ? $thumbInfo['Thumb'][$_lessonID] : false;

			$url = 'lesson.php?id='.$_lessonID; // .'&p='.$row['para_id'];
			$title = ($row['chapter_title'] && $row['para_title']) ? 'Перейти к '.$row['chapter_title'].': '.$row['para_title'] : 'Перейти к '.$row['title'];
			$last_visited = $row['last_visited'];
			$link_text = $row['title'];
			include('view_thumbitem.php');
		}
	}
?>
	</table>
<?
	if ($lEditMode) {
?>
	<div align="center" style="margin-top: 10px; margin-bottom: 30px">
		<a class="btn btn-mini btn-primary" href="javascript:void(0)" onclick="lesson.lessonUpdate()"><i class="icon icon-plus"></i> Добавить урок</a>
	</div>
	<div class="sampleUploadLessonImage" style="display: none">
		<form action="" method="post" enctype="multipart/form-data">
			<span class="small">Загружать можно только файлы в формате JPG, PNG, GIF</span>
			<div class="input_file_place">
				<div class="input_file_fake">Загрузить файл</div>
				<input type="file" name="media" />
			</div>
			<input type="hidden" name="id" value="" />
			<div align="right">
				<button type="submit" class="btn btn-primary btn-mini upload-btn">Загрузить</button>
				<button type="button" class="btn btn-mini" data-btn="cancel">Отмена</button>
			</div>
		</form>
	</div>

<?
	}
?>