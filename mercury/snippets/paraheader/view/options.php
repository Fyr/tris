<?
	$editable = (isset($_POST['editable'])) ? $_POST['editable'] : 'Текст определения...';
	$curr_checked = (isset($_POST['align']) && $_POST['align']) ? $_POST['align'] : 'left';
	$margin_left = (isset($_POST['margin_left']) && $_POST['margin_left']) ? $_POST['margin_left'] : '0';
	$margin_right = (isset($_POST['margin_right']) && $_POST['margin_right']) ? $_POST['margin_right'] : '0';
?>
<form action="" method="post" style="width:600px">
<input type="hidden" name="edit" value="1" />
<div class="form-inputs mercury-display-pane-container">

    <fieldset>
      <div class="control-group string optional">
        <label class="string optional control-label" for="align">Выравнивание</label>
        <div class="controls">
<?
	$aOptions = array(
		array('title' => 'Слева', 'icon' => 'icon-align-left', 'value' => 'left'),
		array('title' => 'По центру', 'icon' => 'icon-align-center', 'value' => 'center'),
		array('title' => 'Справа', 'icon' => 'icon-align-right', 'value' => 'right'),
		array('title' => 'По ширине', 'icon' => 'icon-align-right', 'value' => 'justify')
	);
	foreach($aOptions as $row) {
		$checked = ($row['value'] == $curr_checked) ? 'checked="checked"' : '';
?>
          <input type="radio" name="align" value="<?=$row['value']?>" <?=$checked?>><i class="<?=$row['icon']?>"></i> <?=$row['title']?> &nbsp;
<?
	}
?>
        </div>
      </div>
    </fieldset>

    <fieldset>
      <div class="control-group string optional">
        <label class="string optional control-label" for="options_first_name">Отступ слева</label>
        <div class="controls">
          <input type="text" class="string optional" id="margin_left" name="margin_left" value="<?=$margin_left?>" style="width: 30px">%
        </div>
      </div>
      <div class="control-group string optional">
        <label class="string optional control-label" for="options_first_name">Отступ справа</label>
        <div class="controls">
          <input type="text" class="string optional" id="margin_right" name="margin_right" value="<?=$margin_right?>" style="width: 30px">%
        </div>
      </div>
    </fieldset>

  </div>
  <input type="hidden" id="custom-editable" name="editable" value="<?=$editable?>" />
  <div class="form-actions mercury-display-controls">
    <input class="btn btn-primary" name="commit" type="submit" value="Вставить">
  </div>
</form>
