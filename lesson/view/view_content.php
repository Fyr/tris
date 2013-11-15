<?
	$sampleContent = '<div style="height: 300px" class="container-scroll-box"><div class="container-scroll-box-holder"> <div align="center">Подождите, выполняется загрузка...</div> </div></div>';
	$contentHTML = ($contentHTML) ? $contentHTML : $sampleContent;
?>
			<div class="container-scroll" id="mercury-content" data-mercury="full"><?=$contentHTML?></div>
