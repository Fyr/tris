<fieldset>
	<legend>&nbsp;Загрузка&nbsp;</legend>
	<div class="string optional">
		<div class="controls">
			<div class="uploadImages" style="height: 65px">
				<div id="form">
					<span class="small">Загружать можно только файлы в формате JPG, PNG, GIF</span>
					<div>
						<div class="input_file_fake" style="display: none;">Загрузить файл</div>
						<input type="file" id="upload-image" name="image" onchange="Mercury.Snippet.API.enableUpload(this, true)" />
					</div>
					<div align="right">
						<button type="button" class="btn btn-primary btn-mini upload-btn disabled" disabled="disabled" onclick="Mercury.Snippet.API.uploadImage(this, '#upload-image')">Загрузить</button>
					</div>
				</div>
				<div class="loader" style="display: none; padding-top: 20px;" align="center">
					<img src="/mercury/assets/img/ajax-loader3.gif" alt="Подождите, идет загрузка..."/> Подождите, идет загрузка...
				</div>
			</div>
		</div>
	</div>
</fieldset>