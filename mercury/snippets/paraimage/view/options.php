<?
	$editable = (isset($_POST['editable'])) ? $_POST['editable'] : 'Рис.№ 1. Текст для рисунка';
	$curr_checked = (isset($_POST['align']) && $_POST['align']) ? $_POST['align'] : 'left';
	$margin_left = (isset($_POST['margin_left']) && $_POST['margin_left']) ? $_POST['margin_left'] : '0';
	$margin_right = (isset($_POST['margin_right']) && $_POST['margin_right']) ? $_POST['margin_right'] : '0';
	$margin_top = (isset($_POST['margin_top']) && $_POST['margin_top']) ? $_POST['margin_top'] : '0';
	$margin_bottom = (isset($_POST['margin_bottom']) && $_POST['margin_bottom']) ? $_POST['margin_bottom'] : '0';
	$image_width_perc = (isset($_POST['image_width_perc']) && $_POST['image_width_perc']) ? $_POST['image_width_perc'] : '100';
	$image_height_perc = (isset($_POST['image_height_perc']) && $_POST['image_height_perc']) ? $_POST['image_height_perc'] : '100';

	// $thumb = (isset($_POST['thumb']) && $_POST['thumb']) ? $_POST['thumb'] : '/mercury/assets/img/no_image.png';
	// $img_src = (isset($_POST['img_src']) && $_POST['img_src']) ? $_POST['img_src'] : '';
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
		$checked = ($row['value'] == $curr_checked) ? ' selected="selected"' : '';
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

	<?=$this->MediaLib->render('upload_image')?>
	<?=$this->MediaLib->render('image_settings')?>
	<?=$this->MediaLib->selectThumbOptions()?>


  </div>
  <div class="form-actions mercury-display-controls">
  	<?=$this->MediaLib->render('action_buttons')?>
  </div>
</form>
