<fieldset>
	<legend>&nbsp;Изображения&nbsp;</legend>
	<div class="string optional">
		<div class="controls">
			<div id="chooseThumb" class="chooseThumb" align="center" style="height: 180px; overflow-y: auto">
<?
	include('view_thumbs.php');
?>
			</div>
		</div>
	</div>

</fieldset>
<input type="hidden" id="img_ids" name="img_ids" value="<?=Request::POST('img_ids')?>" >