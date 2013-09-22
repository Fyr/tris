<div id="search-results">
<?
	if (isset($aResults)) {
		if ($aResults) {
			foreach($aResults as $row) {
?>
	<a href="javascript:void(0)" onclick="lesson.switchPara(<?=$row['id']?>)"><?=highlight($row['title'], $q)?></a><br />
	<p><?=around(strip_all_tags($row['content_cached']), $q)?></p>
<?
			}
		} else {
?>
		<b>По заданным критериям поиска не найдено ни одной записи</b>
<?
		}
	}
?>
</div>