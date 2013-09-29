<!--h3></h3-->
<!--p>Все поля формы обязательны для заполнения</p-->
<b style="display: block; margin: 20px 0 10px 0">Оставить свой комментарий</b>
<form action="" class="form-post" method="post">
	<fieldset>
		<input type="hidden" name="para_id" value="<?=$paraID?>" />
		<textarea name="body" rows="3" cols="40" onblur="$('.form-post .body.error').html('')"></textarea>
		<div align="center">
			<button type="submit" class="btn btn-mini btn-primary">Отправить</button>
		</div>
	</fieldset>
</form>
<div class="samplePost" style="display: none;">
	<form action="" method="post">
		<input type="hidden" name="id" value="" />
		<textarea name="body"></textarea>
		<div align="center">
			<button type="button" class="btn btn-primary btn-mini" data-btn="save">Сохранить</button>
			<button type="button" class="btn btn-mini" data-btn="cancel">Отмена</button>
		</div>
	</form>
</div>