<?
	$img_ids = explode(',', Request::POST('img_ids'));
	foreach($images as $id => $img) {
		$selected = (in_array($id, $img_ids)) ? ' selected' : '';

?>
				<img id="thumb_<?=$id?>" class="choose-thumb<?=$selected?>" src="<?=$img['shop_thumbnail']?>" alt="" onclick="Mercury.Snippet.API.selectThumbs(this, '<?=$lMultiSelect?>')"/>
				<?// ($lMultiSelect) ? 'selectThumbList(this)' : 'selectThumb(this)'?>
<?
	}
