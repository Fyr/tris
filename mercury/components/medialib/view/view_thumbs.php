<?
	$img_ids = explode(',', Request::POST('img_ids'));
	foreach($images as $id => $img) {
		$selected = (in_array($id, $img_ids)) ? ' selected' : '';
		// list($fname, $format) = explode('.', $img['file']);
?>
				<img id="thumb_<?=$id?>" data-extras="<?=str_replace('"', "'", json_encode($img['extras']))?>" data-file="<?=$img['file']?>" class="choose-thumb<?=$selected?>" src="<?=$img['shop_thumbnail']?>" onclick="Mercury.Snippet.API.selectThumbs(this, '<?=$lMultiSelect?>')" alt=""/>
				<?// ($lMultiSelect) ? 'selectThumbList(this)' : 'selectThumb(this)'?>
<?
	}
