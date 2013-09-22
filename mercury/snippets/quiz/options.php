<?
	$editable = (isset($_POST['editable'])) ? $_POST['editable'] : 'Попробуйте самостоятельно решить эту задачу.<br/>Есть идеи? Запишите свои решения.';
	$title = (isset($_POST['title'])) ? $_POST['title'] : 'Вопрос читателю';
	$answer = (isset($_POST['answer'])) ? $_POST['answer'] : 'Текст правильного ответа...';

	$curr_checked = (isset($_POST['align']) && $_POST['align']) ? $_POST['align'] : 'left';
	$margin_left = (isset($_POST['margin_left']) && $_POST['margin_left']) ? $_POST['margin_left'] : '0';
	$margin_right = (isset($_POST['margin_right']) && $_POST['margin_right']) ? $_POST['margin_right'] : '0';
?>
<form action="" method="post" style="width:600px">
<input type="hidden" name="edit" value="1" />
<div class="form-inputs mercury-display-pane-container">

    <fieldset>
      <legend>&nbsp;Общие&nbsp;</legend>
      <div class="string optional">
        <div class="controls">
        	<label class="string optional control-label inline" for="align">Положение</label>
        	<select name="align">
<?
	$aOptions = array(
		array('title' => 'Слева', 'icon' => 'icon-align-left', 'value' => 'left'),
		array('title' => 'По центру', 'icon' => 'icon-align-center', 'value' => 'center'),
		array('title' => 'Справа', 'icon' => 'icon-align-right', 'value' => 'right'),
		array('title' => 'По ширине', 'icon' => 'icon-align-right', 'value' => 'justify')
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
			<label class="string optional control-label inline" for="editable" style="width: 66px">Текст вопроса</label>
    		<input type="text" class="string optional" id="custom-editable" name="editable" value="<?=$editable?>" style="width: 470px" />
    		<br>
			<label class="string optional control-label inline" for="answer"style="width: 66px">Текст ответа</label>
    		<input type="text" class="string optional" id="custom-editable3" name="answer" value="<?=$answer?>" style="width: 470px" />
        </div>
      </div>
    </fieldset>

    <fieldset>
      <legend>&nbsp;Отступы&nbsp;</legend>
      <div class="string optional">
        <div class="controls">
          <label class="string optional control-label inline" for="margin_left">Слева</label>
          <input type="text" class="string optional" id="margin_left" name="margin_left" value="<?=$margin_left?>" style="width: 30px">%
          <label class="string optional control-label inline" for="margin_right">Справа</label>
          <input type="text" class="string optional" id="margin_right" name="margin_right" value="<?=$margin_right?>" style="width: 30px">%
        </div>
      </div>
    </fieldset>

  </div>
  <input type="hidden" id="custom-editable2" name="title" value="<?=$title?>" />
  <div class="form-actions mercury-display-controls">
    <input class="btn btn-primary" name="commit" type="submit" value="Вставить">
  </div>
</form>
