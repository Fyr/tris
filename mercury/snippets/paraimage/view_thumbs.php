<?
	foreach($images as $id => $img) {
		$selected = ($img['large'] == $img_src) ? ' selected' : '';
?>
				<img id="thumb_<?=$id?>" class="choose-thumb<?=$selected?>" src="<?=$img['shop_thumbnail']?>" alt="" onclick="Mercury.Snippet.API.selectThumb(this, '<?=$img['large']?>')"/>
<?
	}
