<?
	$curr_checked = (isset($_POST['align']) && $_POST['align']) ? $_POST['align'] : 'float_none';
	$slider_checked = (isset($_POST['autoplay']) && $_POST['autoplay']) ? $_POST['autoplay'] : 'auto';

	$margin_left = (isset($_POST['margin_left']) && $_POST['margin_left']) ? $_POST['margin_left'] : '0';
	$margin_right = (isset($_POST['margin_right']) && $_POST['margin_right']) ? $_POST['margin_right'] : '0';
	$margin_top = (isset($_POST['margin_top']) && $_POST['margin_top']) ? $_POST['margin_top'] : '0';
	$margin_bottom = (isset($_POST['margin_bottom']) && $_POST['margin_bottom']) ? $_POST['margin_bottom'] : '0';

	$slider_changepage_speed = (isset($_POST['slider_changepage_speed']) && $_POST['slider_changepage_speed']) ? $_POST['slider_changepage_speed'] : '7000';
	$slider_changeframe_speed = (isset($_POST['slider_changeframe_speed']) && $_POST['slider_changeframe_speed']) ? $_POST['slider_changeframe_speed'] : '800';

	$slider_width = (isset($_POST['slider_width']) && $_POST['slider_width']) ? $_POST['slider_width'] : '50';
	$slider_height = (isset($_POST['slider_height']) && $_POST['slider_height']) ? $_POST['slider_height'] : '200';

	$editable = (isset($_POST['editable']) && $_POST['editable']) ? $_POST['editable'] : 'Подпись под слайдером';

	$img_ids = Request::POST('img_ids');
?>
<form class="mercury-options-panel" action="" method="post" style="width:600px">
<input type="hidden" name="edit" value="1" />
<div class="form-inputs mercury-display-pane-container">
	<div id="chooseThumb" title="Выберите изображение для вставки"></div>
    <fieldset>
      <legend>&nbsp;Общие&nbsp;</legend>
      <div class="string optional">
        <div class="controls">
			<label class="string optional control-label inline" for="slider_width" style="margin-left: 0">Ширина</label>
			<input type="text" class="string optional" id="slider_width" name="slider_width" value="<?=$slider_width?>" style="width: 25px"> %
			<label class="string optional control-label inline" for="slider_height" style="margin-left: 0">Высота</label>
			<input type="text" class="string optional" id="slider_height" name="slider_height" value="<?=$slider_height?>" style="width: 25px"> px
			<label class="string optional control-label inline" for="align">Положение</label>
			<select name="align">
				<?
					$aOptions = array(
						array('title' => 'Влево', 'value' => 'float_rowleft'),
						array('title' => 'По центру', 'value' => 'float_rowcenter'),
						array('title' => 'Вправо', 'value' => 'float_rowright'),
						array('title' => 'Слева, обтекание текста справа/снизу', 'value' => 'float_left'),
						array('title' => 'Справа, обтекание текста слева/снизу', 'value' => 'float_right')
					);
					foreach($aOptions as $row) {
						$checked = ($row['value'] == $curr_checked) ? ' selected="selected"' : '';
				?>
						<option value="<?=$row['value']?>"<?=$checked?>><?=$row['title']?></option>
				<?
					}
				?>
			</select>
			<br />
			<label class="string optional control-label inline" for="editable" style="margin-left: 0">Название</label>
    		<input type="text" class="string optional" id="custom-editable" name="editable" value="<?=$editable?>" style="width: 480px" />
        </div>
      </div>
    </fieldset>
    <fieldset>
      <legend>&nbsp;Отступы&nbsp;</legend>
      <div class="string optional">
        <div class="controls">
          <label class="string optional control-label inline" for="margin_right">Сверху</label>
          <input type="text" class="string optional" id="margin_top" name="margin_top" value="<?=$margin_top?>" style="width: 30px">px
          <label class="string optional control-label inline" for="margin_right">Справа</label>
          <input type="text" class="string optional" id="margin_right" name="margin_right" value="<?=$margin_right?>" style="width: 30px">px
          <label class="string optional control-label inline" for="margin_left">Снизу</label>
          <input type="text" class="string optional" id="margin_bottom" name="margin_bottom" value="<?=$margin_bottom?>" style="width: 30px">px
          <label class="string optional control-label inline" for="margin_left">Слева</label>
          <input type="text" class="string optional" id="margin_left" name="margin_left" value="<?=$margin_left?>" style="width: 30px">px
        </div>
      </div>
    </fieldset>
	<fieldset>
		<legend>&nbsp;Проигрывание&nbsp;</legend>
		<div class="string optional">
			<div class="controls">
				<label class="string optional control-label inline" for="autoplay" style="margin-left: 0">Автопроигрывание</label>
				<select name="autoplay">
				<?
					$aOptions = array(
						array('title' => 'Автоматически', 'icon' => 'icon-align-left', 'value' => 'true'),
						array('title' => 'Вручную', 'icon' => 'icon-align-center', 'value' => 'false')
					);
					foreach($aOptions as $row) {
						$checked = ($row['value'] == $slider_checked) ? 'selected="selected"' : '';
				?>
								<option value="<?=$row['value']?>"<?=$checked?>><?=$row['title']?></option>
				<?
					}
				?>
				</select>
				<label class="string optional control-label inline" for="effect" style="margin-left: 0">Эффект</label>
				<select name="effect">
				<?
					$aOptions = array(
						array('title' => 'Fade', 'icon' => 'icon-align-left', 'value' => 'fade')
					);
					foreach($aOptions as $row) {
						$checked = ($row['value'] == $slider_checked) ? 'selected="selected"' : '';
				?>
						<option value="<?=$row['value']?>"<?=$checked?>><?=$row['title']?></option>
				<?
					}
				?>
				</select>
				<br />
				<label class="string optional control-label inline" for="slider_changeframe_speed" style="margin-left: 0">Смена кадров</label>
				<input type="text" class="string optional" id="slider_changeframe_speed" name="pagetime" value="<?=$slider_changeframe_speed?>" style="width: 40px"> мс
				<label class="string optional control-label inline" for="slider_changepage_speed" style="margin-left: 0">Скорость анимации</label>
				<input type="text" class="string optional" id="slider_changepage_speed" name="slidetime" value="<?=$slider_changepage_speed?>" style="width: 40px"> мс
			</div>
		</div>
	</fieldset>


    <fieldset>
      <legend>&nbsp;Загрузка&nbsp;</legend>
      <div class="string optional">
        <div class="controls">
        	<!--
			<span class="btn btn-success fileinput-button">
		        <i class="icon-plus icon-white"></i>
		        <span>Select files...</span>
		        <input id="fileupload" type="file" name="files[]" multiple>
		    </span>
		    <div id="progress" class="progress progress-success progress-striped">
		        <div class="bar"></div>
		    </div>
		    -->
        	<div class="uploadImages" style="height: 65px">
				<div id="form">
					<span class="small">Загружать можно только файлы в формате JPG, PNG, GIF</span>
					<div>
						<div class="input_file_fake" style="display: none;">Загрузить файл</div>
						<input type="file" id="upload-image" name="image" onchange="Mercury.Snippet.API.enableUpload(this, true)" />
					</div>
					<div align="right">
						<button type="button" class="btn btn-primary btn-mini upload-btn disabled" disabled="disabled" onclick="Mercury.Snippet.API.uploadImage(this, '#upload-image', '<?=$img_src?>')">Загрузить</button>
					</div>
				</div>
				<div class="loader" style="display: none; padding-top: 20px;" align="center">
					<img src="/mercury/assets/img/ajax-loader3.gif" alt="Подождите, идет загрузка..."/> Подождите, идет загрузка...
				</div>
			</div>

        </div>
      </div>
    </fieldset>

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


  </div>
  <input type="hidden" id="img_ids" name="img_ids" value="<?=$img_ids?>">
  <div class="form-actions mercury-display-controls">
  	<span class="actions">
 <?
 	if ($img_ids) {
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
  </div>
</form>