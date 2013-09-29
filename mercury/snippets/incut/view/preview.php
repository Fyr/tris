<?
	$edit = isset($_POST['edit']) && $_POST['edit'];
	$style = (isset($_POST['margin_left']) && $_POST['margin_left']) ? ' margin-left: '.$_POST['margin_left'].'%;' : '';
	$style.= (isset($_POST['margin_right']) && $_POST['margin_right']) ? ' margin-right: '.$_POST['margin_right'].'%;' : '';
?>
<style type="text/css">
@import "<?=$assetsPath?>css/style.css";
</style>
<div class="incut-body" align="<?=@$_POST['align']?>" style="<?=$style?>">
	<div class="incut-header"><img src="<?=$assetsPath?>img/icon.png" alt="" />
		<span <? if ($edit) {?>contenteditable="true" onchange="onChangeEditable(this, 'title')"<? } ?>><?=@$_POST['title']?></span>
	</div>
	<span <? if ($edit) {?>contenteditable="true" onchange="onChangeEditable(this, 'editable')"<? } ?>><?=@$_POST['editable']?></span>
</div>