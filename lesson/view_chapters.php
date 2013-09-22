					<form action="" method="post">
						<div class="accordion">
<?
	$count = 0;
	foreach ($aChapters as $chapter) {
?>
								<h4 id="chapter_<?=$chapter['id']?>"><?=$chapter['title']?></h4>
								<div>
<?
		if (isset($aParagraphs[$chapter['id']])) {
?>

<?
			foreach ($aParagraphs[$chapter['id']] as $para) {
				if ($currPara && $currPara == $para['id']) {
					$para['title'] = '<b>'.$para['title'].'</b>';
				}
?>
										<a id="para_<?=$para['id']?>" href="javascript:void(0)" onclick="lesson.switchPara(<?=$para['id']?>)"><?=$para['title']?></a>
<?
				if (isset($para['subheaders']) && $para['subheaders'] && $currPara && $currPara == $para['id']) {
					$subheaders = unserialize($para['subheaders']);
					echo '<ul class="subheaders">';
					foreach($subheaders as $id => $title) {
?>
						<li><a href="#<?=$id?>"><?=$title?></a></li>
<?
					}
					echo '</ul>';
				}

			}
		}
?>
								</div>
<?
		$count++;
	}
?>
						</div>
					</form>
