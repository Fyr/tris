<?
	if ($thumbInfo['LastVisited']) {
?>
	<b>Последние просмотренные уроки</b>
	<table>

<?
		foreach($thumbInfo['LastVisited'] as $row) {
			$_lessonID = $row['lesson_id'];
			$thumb = $thumbInfo['Thumb'][$_lessonID];

			$url = 'lesson.php?id='.$_lessonID.'&p='.$row['para_id'];
			$title = 'Перейти к '.$row['chapter_title'].': '.$row['para_title'];
			$thumb_src = $thumb['thumb'];
			$thumb_alt = $thumb['post_title'];
			$last_visited = $row['last_visited'];
			$link_text = $thumb['post_title'];
			include('view_thumbitem.php');
		}
	}
?>
	</table>
	<div>
		<b>Все курсы</b>
		<div class="accordion">
<?
	foreach($thumbInfo['Course'] as $courseID => $lessons) {
?>
			<h4><?=$lessons[0]['course_name']?></h4>
			<div>
				<table>
<?
		foreach($lessons as $lesson) {
			$_lessonID = $lesson['lesson_id'];


			$url = 'lesson.php?id='.$_lessonID;
			$title = 'Перейти к '.$lesson['course_title'].': '.$lesson['lesson_title'];
			$last_visited = ''; // $row['last_visited'];
			$link_text = $lesson['lesson_title'];

			$thumb_src = PUBLIC_DIR.'img/no_image.png';
			$thumb_alt = $lesson['lesson_title'];
			if (isset($thumbInfo['Thumb'][$lesson['lesson_id']])) {
				$thumb = $thumbInfo['Thumb'][$lesson['lesson_id']];
				$thumb_src = $thumb['thumb'];
				$thumb_alt = $thumb['post_title'];
			}
			$addLesson = !$lesson['chapter_id'];
			include('view_thumbitem.php');
		}
?>
				</table>
			</div>
<?
	}
?>


		</div>
	</div>
