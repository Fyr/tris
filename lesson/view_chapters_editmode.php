					<form action="" method="post">
						<ol>
<?
	$count = 0;
	foreach ($aChapters as $chapter) {
		$count++;
?>
							<li>
								<a id="chapter_<?=$chapter['id']?>" href="#"><?=$chapter['title']?></a>
<?
		if ($lEditMode) {
?>
								<div align="left" class="btn-group pull-right">
									<a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-mini dropdown-toggle pull-right">
										<span class="caret"></span>
									</a>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="javascript:void(0)" onclick="lesson.paraUpdate(0, <?=$chapter['id']?>)"><i class="icon-plus"></i> Добавить параграф</a>
										</li>
										<li>
											<a href="javascript:void(0)" onclick="lesson.chapterUpdate(<?=$chapter['id']?>)"><i class="icon-edit"></i> Изменить название</a>
										</li>
										<li>
											<a href="javascript:void(0)" onclick="lesson.chapterDelete(<?=$chapter['id']?>)"><i class="icon-remove"></i> Удалить</a>
										</li>
<?
			if ($count > 1) {
?>
										<li>
											<a href="javascript:void(0)" onclick="lesson.chapterSort(<?=$chapter['id']?>, 'up')"><i class="icon-arrow-up"></i> Изменить порядок</a>
										</li>
<?
			}
			if ($count < count($aChapters)) {
?>
										<li>
											<a href="javascript:void(0)" onclick="lesson.chapterSort(<?=$chapter['id']?>, 'down')"><i class="icon-arrow-down"></i> Изменить порядок</a>
										</li>
<?
			}
?>
									</ul>
								</div>
<?
		}
		if (isset($aParagraphs[$chapter['id']])) {
?>
								<ol>
<?
			$_count = 0;
			foreach ($aParagraphs[$chapter['id']] as $para) {
				$_count++;
?>
									<li>
										<a id="para_<?=$para['id']?>" href="javascript:void(0)" onclick="lesson.switchPara(<?=$para['id']?>)"><?=$para['title']?></a>
<?
				if ($lEditMode) {
?>
										<div align="left" class="btn-group pull-right">
											<a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-mini dropdown-toggle pull-right">
												<span class="caret"></span>
											</a>
											<ul class="dropdown-menu pull-right">
												<li>
													<a href="javascript:void(0)" onclick="lesson.paraUpdate(<?=$para['id']?>, <?=$para['chapter_id']?>)"><i class="icon-edit"></i> Изменить название</a>
												</li>
												<li>
													<a href="javascript:void(0)" onclick="lesson.paraDelete(<?=$para['id']?>)"><i class="icon-remove"></i> Удалить</a>
												</li>
<?
					if ($_count > 1) {
?>
												<li>
													<a href="javascript:void(0)" onclick="lesson.paraSort(<?=$para['id']?>, 'up')"><i class="icon-arrow-up"></i> Изменить порядок</a>
												</li>
<?
					}
					if ($_count < count($aParagraphs[$chapter['id']])) {
?>
												<li>
													<a href="javascript:void(0)" onclick="lesson.paraSort(<?=$para['id']?>, 'down')"><i class="icon-arrow-down"></i> Изменить порядок</a>
												</li>
<?
					}
					if ($para['favorite']) {
?>
												<li>
													<a href="javascript:void(0)" onclick="lesson.setFavorite(<?=$para['id']?>, 0)"><i class="icon-star-empty"></i> Удалить из Избр.</a>
												</li>
<?
					} else {
?>
												<li>
													<a href="javascript:void(0)" onclick="lesson.setFavorite(<?=$para['id']?>, 1)"><i class="icon-star"></i> Добавить в Избр.</a>
												</li>
<?
					}
					if ($para['audio_file']) {
?>
												<li>
													<a href="javascript:void(0)" onclick="lesson.audioDelete(<?=$para['audio_id']?>)"><i class="icon-volume-up"></i> Удалить аудио</a>
												</li>
<?
					} else {
?>
												<li>
													<a href="javascript:void(0)" onclick="lesson.audioUpload(<?=$para['id']?>)"><i class="icon-volume-up"></i> Загрузить аудио</a>
												</li>
<?
					}
?>
											</ul>
										</div>
										<div id="audio_<?=$para['id']?>" class="uploadAudio"></div>
<?
				}
?>
									</li>
<?
			}
?>
								</ol>
<?
		}
?>
							</li>
<?
	}
?>
						</ol>
<?
	if ($lEditMode) {
?>
						<div align="center" style="margin: 10px 0 30px 0">
							<button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="lesson.chapterUpdate(0)"><i class="icon-plus"></i> Добавить главу</button>
						</div>
<?
	}
?>
					</form>

<?
	if ($lEditMode) {
?>
<div class="sampleUploadAudio" style="display: none">
	<form action="" method="post" enctype="multipart/form-data">
		<span class="small">Загружать можно только файлы в формате WAV</span>
		<div class="input_file_place">
			<div class="input_file_fake">Загрузить файл</div>
			<input type="file" name="audio" />
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