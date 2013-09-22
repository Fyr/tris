<?
$title = (isset($_POST['first_name'])) ? $_POST['first_name'] : 'Default button name';
$editable = (isset($_POST['editable'])) ? $_POST['editable'] : 'Type title here...';
$aOptions = array(
	'btn-primary' => 'Синяя сильно заметная и выделяющаяся кнопка',
	'btn-info' => 'Используйте как альтернативу кнопке по умолчанию',
	'btn-success' => 'Обозначает позитивное действие или успешное выполнение',
	'btn-warning' => 'Обозначает какое-либо предупреждение',
	'btn-danger' => 'Обозначает негативное действие или какую-либо ошибку'
);
?>
<form action="" method="post" style="width:600px">
<input type="hidden" name="edit" value="1" />
<div class="form-inputs mercury-display-pane-container">

    <fieldset>
      <div class="control-group string optional">
        <label class="string optional control-label" for="options_first_name">Title for uneditable button</label>
        <div class="controls">
          <input class="span6 string optional" id="options_first_name" name="first_name" size="50" type="text" value="<?=$title?>">
        </div>
      </div>
    </fieldset>
    
    <fieldset>
      <div class="control-group string optional">
        <label class="string optional control-label" for="options_btn_type">Choose button type</label>
        <div class="controls">
          <select autocomplete="off" id="btn_type" name="btn_type">
<?
	foreach($aOptions as $value => $title) {
		$selected = (isset($_POST['btn_type']) && $value == $_POST['btn_type']) ? ' selected="selected"' : '';
?>
			<option value="<?=$value?>"<?=$selected?>><?=$value.': '.$title?>
<?
	}
?>
          </select>
        </div>
      </div>
    </fieldset>

  </div>
  <input type="hidden" id="custom-editable" name="editable" value="<?=$editable?>" />
  <div class="form-actions mercury-display-controls">
    <input class="btn btn-primary" name="commit" type="submit" value="Insert Snippet">
  </div>
</form>
