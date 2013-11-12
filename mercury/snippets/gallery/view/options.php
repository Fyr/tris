<?
	$editable = (isset($_POST['editable'])) ? $_POST['editable'] : 'Текст заголовка для галереи...';
	$curr_checked = (isset($_POST['align']) && $_POST['align']) ? $_POST['align'] : 'left';
	$margin_left = (isset($_POST['margin_left']) && $_POST['margin_left']) ? $_POST['margin_left'] : '0';
	$margin_right = (isset($_POST['margin_right']) && $_POST['margin_right']) ? $_POST['margin_right'] : '0';
	$margin_top = (isset($_POST['margin_top']) && $_POST['margin_top']) ? $_POST['margin_top'] : '0';
	$margin_bottom = (isset($_POST['margin_bottom']) && $_POST['margin_bottom']) ? $_POST['margin_bottom'] : '0';
	$box_width_perc = (isset($_POST['box_width_perc']) && $_POST['box_width_perc']) ? $_POST['box_width_perc'] : '100';

	// $thumb = (isset($_POST['thumb']) && $_POST['thumb']) ? $_POST['thumb'] : '/mercury/assets/img/no_image.png';
	// $img_src = (isset($_POST['img_src']) && $_POST['img_src']) ? $_POST['img_src'] : '';
?>
<form class="mercury-options-panel" action="" method="post" style="width:600px">
<input type="hidden" name="edit" value="1" />
<div class="form-inputs mercury-display-pane-container">
    <fieldset>
      <legend>&nbsp;Настройки контейнера&nbsp;</legend>
      <div class="string optional">
        <label class="string optional control-label" for="align"></label>
        <div class="controls">
			<label class="string optional control-label inline" for="box_width_perc" style="margin-left: 0">Ширина</label>
    		<input type="text" class="string optional" id="box_width_perc" name="box_width_perc" value="<?=$box_width_perc?>" style="width: 25px">%
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
			<label class="string optional control-label inline" for="image_height_perc" style="margin-left: 0">Заголовок</label>
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

    <?=$this->MediaLib->render('load_image')?>
    <?=$this->MediaLib->options()?>

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
  <!--input type="hidden" id="img_src" name="img_src[]" value="<?=$img_src?>" /-->
  <input type="hidden" id="img_ids" name="img_ids" value="<?=Request::POST('img_ids')?>" >
  <div class="form-actions mercury-display-controls">
  	<span class="actions">
 <?
 	if (Request::POST('img_ids')) {
 ?>
 	<!--input class="btn btn-danger pull-left" name="delete" type="button" value="Удалить" onclick="Mercury.Snippet.API.deleteImage(this)"-->
    <input class="btn btn-primary" name="commit" type="submit" value="Вставить">
<?
 	} else {
?>
	<!--input class="btn btn-danger pull-left disabled" name="delete" type="button" value="Удалить" disabled="true" onclick="Mercury.Snippet.API.deleteImage(this)"-->
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