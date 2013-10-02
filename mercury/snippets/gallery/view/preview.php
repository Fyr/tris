<?
	$lEditMode = Request::POST('edit');
	$style = '';
	$box_style = '';
	if (strpos($_POST['align'], 'float_') !== false) {
		$value = str_replace('float_', '', $_POST['align']);
		$style.= ' float: '.$value.';';
		$style.= 'text-align: '.$value.';';
		$style.= ' width: '.$_POST['box_width_perc'].'%;';
	} else {
		$style.= 'text-align: '.$_POST['align'].';';
		$box_style.= ' width: '.$_POST['box_width_perc'].'%;';
	}

	$thumb_style = '';
	if (in_array($_POST['align'], array('left', 'right', 'float_left', 'float_right'))) {
		$thumb_style = 'float: '.str_replace('float_', '', $_POST['align']);
		$div_clear = '<div class="clear"></div>';
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
<span <? if ($lEditMode) {?>contenteditable="true" onchange="onChangeEditable(this, 'editable')"<? } ?>><?=@$_POST['editable']?></span>
	<div style="<?=$box_style?>; display: inline-block;">
<?
	foreach($images as $imgID => $img) {
		$thumb = '/thumb.php?id='.$imgID.'&file=image.png&w=90&h=90';
?>
		<a class="gallery-thumb" href="<?=($lEditMode) ? '#' : $img['large']?>" rel="photoalbum" style="<?=$thumb_style?>"><img src="<?=$img['shop_thumbnail']?>" alt="" /></a>
<?
	}
?>
		<?//$div_clear?>
	</div>
</div>