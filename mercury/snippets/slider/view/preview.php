<script type="text/javascript">
	var gzwSlider = new MercurySlider($('.mercury_slider.uninitialized'), false);
</script>
<?
	$edit = isset($_POST['edit']) && $_POST['edit'];
	$style = '';
	$row = 0;
	
	if (!strstr($_POST['align'], 'float_row')) {
		$value = str_replace('float_', '', $_POST['align']);
		$style.= ' float: '.$value.';';
	} else {
		$value = str_replace('float_row', '', $_POST['align']);
		if ($value != 'center') $style .= ' float: '.$value.';';
		else $style .= ' display: inline-block;';
		$row = 1;
	}
	
	$aMargins = array('margin_top', 'margin_right', 'margin_bottom', 'margin_left');
	$style.= ' margin:';
	foreach($aMargins as $margin) {
		$style.= (isset($_POST[$margin]) && $_POST[$margin]) ? ' '.$_POST[$margin].'px' : ' 0';
	}
	$style.= ';';
	
	$autoplay = (isset($_GET['edit']) && !empty($_GET['edit'])) ? 'false' : $_POST['autoplay'];
?>
<style type="text/css">
@import "<?=$assetsPath?>css/main.css";
</style>

<? if ($row) : ?>
<div class="row" style="width: 100%; float: left; text-align: center;">
<? endif; ?>

<div class="slider-wrapper">
	<div class="mercury_slider uninitialized" data-slidetime="<?=$_POST['slidetime'];?>" data-pagetime="<?=$_POST['pagetime'];?>" data-width="<?=$_POST['slider_width'];?>" data-height="<?=$_POST['slider_height'];?>" data-autoplay="<?=$autoplay;?>" style="<?=$style?>">
		<div class="mainrow headrow">
			<div class="controls">
				<div class="left button"></div>
				<div class="right button"></div>
			</div>
			<div class="scene">
				<?
					foreach ($images as $imgID => $img) :
				?>
					<div class="item" style="background: url(<?=$img['large']?>)  center center no-repeat">
						<img style="width: 100%; text-align: center; vertical-align: middle;" src="<?=$img['large'];?>"/>
					</div>
				<? endforeach; ?>
			</div>
		</div>
	</div>
	<div class="mainrow" style="text-align: <?=$value;?>;">
		<span class="notify" <? if ($edit) {?>contenteditable="true" onchange="onChangeEditable(this, 'editable')"<? } ?>><?=@$_POST['editable']?></span>	
	</div>	
</div>

<? if ($row) : ?>
</div>
<? endif; ?>