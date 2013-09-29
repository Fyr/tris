<?
	$edit = isset($_POST['edit']) && $_POST['edit'];
	$style = '';
	$img_style = '';
	if (strpos($_POST['align'], 'float_') !== false) {
		$value = str_replace('float_', '', $_POST['align']);
		$style.= ' float: '.$value.';';
		$style.= ' width: '.$_POST['image_width_perc'].'%;';
	} else {
		$style.= 'text-align: '.$_POST['align'].';';
		$img_style.= ' width: '.$_POST['image_width_perc'].'%;';
	}

	$aMargins = array('margin_top', 'margin_right', 'margin_bottom', 'margin_left');
	$style.= ' margin:';
	foreach($aMargins as $margin) {
		$style.= (isset($_POST[$margin]) && $_POST[$margin]) ? ' '.$_POST[$margin].'px' : ' 0';
	}
	$style.= ';';
?>
<style type="text/css">
@import "<?=$assetsPath?>css/style.css";
</style>
<div style="<?=$style?>">
<img src="<?=$_POST['img_src']?>" alt="" style="<?=$img_style?>" /> <span <? if ($edit) {?>contenteditable="true" onchange="onChangeEditable(this, 'editable')"<? } ?>><?=@$_POST['editable']?></span>
</div>