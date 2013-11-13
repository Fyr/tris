	<span class="actions">
<?
	if (Request::POST('img_ids')) {
?>
	<input class="btn btn-danger pull-left" name="delete" type="button" value="Удалить" onclick="Mercury.Snippet.API.deleteImage(this)">
	<input class="btn btn-primary" name="commit" type="submit" value="Вставить">
<?
 	} else {
?>
	<input class="btn btn-danger pull-left disabled" name="delete" type="button" value="Удалить" disabled="true" onclick="Mercury.Snippet.API.deleteImage(this)">
	<input class="btn btn-primary disabled" name="commit" type="submit" value="Вставить" disabled="true">
<?
 	}
?>
	</span>
	<div class="loader" style="display: none;">
		<img src="/mercury/assets/img/ajax-loader3.gif" alt="Подождите, идет загрузка..."/> Подождите, идет загрузка...
	</div>