<?
	$desktop_width = Request::POST('desktop_width', '');
	$desktop_height = Request::POST('desktop_height', '');
?>
<fieldset>
	<legend>&nbsp;Настройка изображения<span class="image-info">&nbsp;(<span class="image-format"></span> <span class="original-size"></span>px)</span>&nbsp;</legend>
	<div class="string optional">
		<div class="controls">
			<div class="imageSettings" style="height: 58px">
				<div class="form">
					<label class="string optional control-label inline" for="margin_right">Размер для Desktop</label>
					<input type="text" class="string optional" id="desktop_width" name="desktop_width" value="<?=$desktop_width?>" disabled="disabled" onfocus="this.select()" onchange="Mercury.Snippet.API.changeImageSettings(this)" style="width: 30px"> x
					<input type="text" class="string optional" id="desktop_height" name="desktop_height" value="<?=$desktop_height?>" disabled="disabled" onfocus="this.select()" onchange="Mercury.Snippet.API.changeImageSettings(this)" style="width: 30px"> px
					<span class="image-info">
					<span class="show-size">iPad: <span class="ipad-size"></span>px</span>
					<span class="show-size">Mobile: <span class="mobile-size"></span>px</span>
					</span>
					<div align="right">
						<button type="button" class="btn btn-primary btn-mini regen-btn disabled" disabled="disabled" onclick="Mercury.Snippet.API.saveImageSettings(this)">Пересохранить</button>
					</div>
				</div>
				<div class="loader" style="display: none; padding-top: 20px;" align="center">
					<img src="/mercury/assets/img/ajax-loader3.gif" alt="Подождите, идет загрузка..."/> Подождите, идет загрузка...
				</div>
			</div>
		</div>
	</div>
</fieldset>