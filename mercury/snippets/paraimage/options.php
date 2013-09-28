<?
require_once('../../../wp-config.php');

require_once('../../../lesson/request.php');
require_once('../../../lesson/db_adapter.php');
require_once('../../../lesson/model.php');

define('UPLOADS_DIR', '/wp-content/uploads/');
define('UPLOAD_DIR', '../../lesson/files/');

$lessonModel = new LessonModel();
$images = $lessonModel->getImageList(array(), array('shop_thumbnail', 'large'));

	$editable = (isset($_POST['editable'])) ? $_POST['editable'] : 'Рис.№ 1. Текст для рисунка';
	$curr_checked = (isset($_POST['align']) && $_POST['align']) ? $_POST['align'] : 'left';
	$margin_left = (isset($_POST['margin_left']) && $_POST['margin_left']) ? $_POST['margin_left'] : '0';
	$margin_right = (isset($_POST['margin_right']) && $_POST['margin_right']) ? $_POST['margin_right'] : '0';
	$margin_top = (isset($_POST['margin_top']) && $_POST['margin_top']) ? $_POST['margin_top'] : '0';
	$margin_bottom = (isset($_POST['margin_bottom']) && $_POST['margin_bottom']) ? $_POST['margin_bottom'] : '0';
	$image_width_perc = (isset($_POST['image_width_perc']) && $_POST['image_width_perc']) ? $_POST['image_width_perc'] : '100';
	$image_height_perc = (isset($_POST['image_height_perc']) && $_POST['image_height_perc']) ? $_POST['image_height_perc'] : '100';

	// $thumb = (isset($_POST['thumb']) && $_POST['thumb']) ? $_POST['thumb'] : '/mercury/assets/img/no_image.png';
	$img_src = (isset($_POST['img_src']) && $_POST['img_src']) ? $_POST['img_src'] : '';
	// $img_id = (isset($_POST['img_id']) && $_POST['img_id']) ? $_POST['img_id'] : '';
?>
<form class="mercury-options-panel" action="" method="post" style="width:600px">
<input type="hidden" name="edit" value="1" />
<div class="form-inputs mercury-display-pane-container">
	<div id="chooseThumb" title="Выберите изображение для вставки"></div>
    <fieldset>
      <legend>&nbsp;Общие&nbsp;</legend>
      <div class="string optional">
        <label class="string optional control-label" for="align"></label>
        <div class="controls">
			<label class="string optional control-label inline" for="image_width_perc" style="margin-left: 0">Ширина</label>
    		<input type="text" class="string optional" id="image_width_perc" name="image_width_perc" value="<?=$image_width_perc?>" style="width: 25px">%
    		<label class="string optional control-label inline" for="image_height_perc">Высота</label>
    		<input type="text" class="string optional" id="image_height_perc" name="image_height_perc" value="<?=$image_height_perc?>" style="width: 25px">%
    		<label class="string optional control-label inline" for="align">Положение</label>
			<select name="align">
<?
	$aOptions = array(
		array('title' => 'Влево', 'icon' => 'icon-align-left', 'value' => 'left'),
		array('title' => 'По центру', 'icon' => 'icon-align-center', 'value' => 'center'),
		array('title' => 'Вправо', 'icon' => 'icon-align-right', 'value' => 'right'),
		array('title' => 'Слева, обтекание текста справа/снизу', 'icon' => 'icon-align-left', 'value' => 'float_left'),
		array('title' => 'Справа, обтекание текста слева/снизу', 'icon' => 'icon-align-left', 'value' => 'float_right')
	);
	foreach($aOptions as $row) {
		$checked = ($row['value'] == $curr_checked) ? 'selected="selected"' : '';
?>
				<option value="<?=$row['value']?>"<?=$checked?>><?=$row['title']?></option>
<?
	}
?>
			</select>
			<br />
			<label class="string optional control-label inline" for="image_height_perc" style="margin-left: 0">Название</label>
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
/*
	foreach($images as $id => $img) {
		$selected = ($img['large'] == $img_src) ? ' selected' : '';
?>
				<img id="thumb_<?=$id?>" class="choose-thumb<?=$selected?>" src="<?=$img['shop_thumbnail']?>" alt="" onclick="Mercury.Snippet.API.selectThumb(this, '<?=$img['large']?>')"/>
<?
	}
	*/
?>
        	</div>
        </div>
      </div>

    </fieldset>


  </div>
  <input type="hidden" id="img_src" name="img_src" value="<?=$img_src?>" />
  <!-- input type="hidden" id="img_id" name="img_id" value="<?=$img_id?>" -->
  <div class="form-actions mercury-display-controls">
  	<span class="actions">
 <?
 	if ($img_src) {
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
<script>
$(function () {

});
</script>