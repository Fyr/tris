<?
	$edit = isset($_POST['edit']) && $_POST['edit'];
	$style = (isset($_POST['margin_left']) && $_POST['margin_left']) ? ' margin-left: '.$_POST['margin_left'].'%;' : '';
	$style.= (isset($_POST['margin_right']) && $_POST['margin_right']) ? ' margin-right: '.$_POST['margin_right'].'%;' : '';
?>
<style type="text/css">
@import "<?=$assetsPath?>css/style.css";
</style>
<div align="<?=@$_POST['align']?>" style="<?=$style?>"> <h2> <span <? if ($edit) {?>contenteditable="true" onchange="onChangeEditable(this, 'editable')"<? } ?>><?=@$_POST['editable']?></span></h2></div>
