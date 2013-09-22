<?
	// determine prev.paragraph
	foreach($aParaInfo as $currPara => $para) {
		if ($para['id'] == $paraID) {
			break;
		}
	}
?>
<!DOCTYPE html>
<html class="main-book">
<head>
	<meta content="320" name="MobileOptimized">
	<meta content="width=device-width, maximum-scale=1.0, user-scalable=1, target-densitydpi=device-dpi" name="viewport">
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Территория Русь</title>
    <link rel="icon" href="<?=PUBLIC_DIR?>img/favicon.ico">
<?
	foreach (array('bootstrap.min', 'smoothness/jquery-ui-1.10.3.custom.min', 'styles', 'jquery.fileupload-ui', 'extra', 'slider') as $css) {
?>
	<link rel="stylesheet" href="<?=PUBLIC_DIR?>css/<?=$css?>.css" type="text/css" media="screen, projection">
<?
	}
?>
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<script src="<?=PUBLIC_DIR?>js/jquery.js"></script>
<?
	if ($lEditMode) {
?>
	<script src="/mercury/assets/javascripts/mercury_loader.js?src=/mercury/assets&pack=bundled&visible=true" type="text/javascript"></script>
	<script src="/mercury/assets/javascripts/mercury_extra.js" type="text/javascript"></script>
	<script src="/mercury/assets/javascripts/mercury_api.js" type="text/javascript"></script>
<?
	}
?>
<script type="text/javascript">
lessonID = <?=$lessonID?>;
paraID = <?=$paraID?>;
aParaInfo = <?=json_encode($aParaInfo)?>;
lEditMode = <?=($lEditMode) ? 'true' : 'false'?>;

$(document).ready(function() {
	$('.autocompleteOff').attr('autocomplete', 'off');

	initNavi(paraID);
	initThumbAccordion(false);

	setTimeout(function() {
		$('.content-tab').click();
		$('.tab-btn .overlay').click();
	}, 50);

	$('.nav-links .favorite').attr('title', ($('.nav-links .favorite').hasClass('active')) ? 'Удалить из Избранного' : 'Добавить в Избранное');
	$('.nav-links .favorite').click(function(){
		lesson.setFavorite(paraID, ($('.nav-links .favorite').hasClass('active')) ? 0 : 1);
	});

	lesson.ajaxForm('.search-panel form', 'search', function(response){
		$('#search-results').html(response.searchHTML);
	});

	lesson.ajaxForm('.form-post', 'post', function(response) {
		$('.form-post textarea').val('');
		$('.postsContainer').html(response.postsHTML);
		$('.postsCount').html((response.postsHTML) ? '<em>' + response.postsCount + '</em>' : '')
	});

	lesson.ajaxForm('.form-notes', 'note', function(response) {
		$('.form-notes textarea').val('');
		$('.notesContainer').html(response.notesHTML);
		$('.notesCount').html((response.notesCount) ? '<em>' + response.notesCount + '</em>' : '');
	});

});

<?
	if (!$lEditMode) {
?>
$(document).ready(function() {
	initQuiz();
});
<?
	}
	if ($lEditMode) {
?>
jQuery(window).on('mercury:ready', function() {
	initMercuryAPI();
	initMercurySave();
<?
	foreach ($aSnippetOptions as $snippetID => $options) {
		$name = $options['_snippet_name'];
		unset($options['_snippet_name']);
		$_options = array();
		foreach ($options as $key => $val) {
			$_options[] = $key.': "'.$val.'"';
		}
		if ($_options) {
?>
	Mercury.Snippet.load({
		<?=$snippetID?>: {name: '<?=$name?>', <?=implode(', ', $_options)?>}
	});
<?
		} else {
?>
	Mercury.Snippet.load({
		<?=$snippetID?>: {name: '<?=$name?>'}
	});
<?
		}
	}
?>
	activateEditable();
	// toggleMercury();
});
<?
	}
?>
</script>

</head>
<!--[if IE 7]><body class="ie7 oldie"><![endif]-->
<!--[if IE 8]><body class="ie8 oldie"><![endif]-->
<!--[if gt IE 8]><!--><body><!--<	![endif]-->
<div class="wrapper">
	<div class="header navbar">
		<div class="holder">
			<strong class="logo"><a href="/"></a></strong>
			<h1>Русская Школа Технологий Творчества</h1>
			<a class="jqm-navmenu-link" href="#">Navigation</a>
			<div class="head_loader processing" style="display: none"></div>
			<div class="col">
				<form action="" class="form-search" method="post">
					<fieldset>
						<input placeholder="поиск" type="search" id="q" name="q">
						<input value="поиск" type="submit">
					</fieldset>
				</form>
				<!--div class="user-info">
					<div class="ava">
						<img src="<?=PUBLIC_DIR?>img/img8.jpg" alt="image description">
						<button data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar pull-right" type="button"></button>
					</div>
					<div class="serv">
						<a href="#" class="link"><?=$user_identity?></a>
						<ul>
							<li><a href="#">Вход</a></li>
							<li><a href="#">Регистрация</a></li>
							<li><a href="#">Я забыл пароль</a></li>
						</ul>
					</div>
				</div-->
			</div>
		</div>
		<div class="serv nav-collapse collapse">
			<ul class="nav">
				<li><a href="#">Вход</a></li>
				<li><a href="#">Регистрация</a></li>
				<li><a href="#">Я забыл пароль</a></li>
			</ul>
		</div>
	</div>
	<div class="main">
		<div class="subnav-fixed subnav-fixed-shadow subnav-fixed-top">
			<div class="subnav-fixed-holder">
				<div class="subnav-fixed-frame">
					<span class="nav-links">
						<a href="javascript:void(0)" class="up" onclick="navigatePrev()" title="К предыдущему параграфу"></a>
<?
	$favorite = ($paragraph && $paragraph['favorite']) ? 'active' : '';
?>
						<!--a href="#" class="share"></a>
						<a href="#" class="play"></a-->
						<a href="javascript:void(0)" class="favorite <?=$favorite?>"></a>
<?
	if (!$audio) {
		$style = 'display: none;';
	}
?>
						<a href="javascript:void(0)" class="play" style="<?=$style?>"></a>
<?
	/* if ($lEditMode) {
?>
						<a href="javascript:void(0)" class="view-mode" onclick="toggleMercury()"></a>
						<a href="javascript:void(0)" class="edit-mode" onclick="toggleMercury()" style="display: none;"></a>
<?
	} */
?>
					</span>
					<h2><a href="javascript:void(0)"></a>&nbsp;</h2>
				</div>
			</div>
		</div>
		<div id="lesson-container" class="container">
			<? include('view_content.php')?>
		</div>
		<div class="subnav-fixed subnav-fixed-shadow subnav-fixed-bottom">
			<div class="subnav-fixed-holder">
				<div class="subnav-fixed-frame">
					<span class="nav-links">
						<a href="javascript:void(0)" class="bottom" onclick="navigateNext()" title="К следующему параграфу"></a>
						<!--a href="#" class="share"></a>
						<a href="#" class="play"></a-->
					</span>
					<h2><a href="javascript:void(0)"></a>&nbsp;</h2>
				</div>
			</div>
		</div>
	</div>
	<div class="left-box">
		<ul class="tabset">
			<li><a href="#tab-1" class="tab tab-1 thumbs-tab"><span></span></a></li>
			<li><a href="#tab-2" class="tab tab-2 content-tab"><span></span></a></li>
			<li><a href="#tab-3" class="tab tab-3 favorite-tab"><span class="favoriteCount"><?=($favoriteCount) ? '<em>'.$favoriteCount.'</em>' : ''?></span></a></li>
			<li><a href="#tab-4" class="tab tab-4 search-tab"><span></span></a></li>
			<li><a href="#tab-5" class="tab tab-5 comments-tab"><span class="postsCount"><?=($postsCount) ? '<em>'.$postsCount.'</em>' : ''?></span></a></li>
			<li><a href="#tab-6" class="tab tab-6 notes-tab"><span class="notesCount"><?=($notesCount) ? '<em>'.$notesCount.'</em>' : ''?></span></a></li>
			<!--li><a href="#tab-7" class="tab tab-7"><span></span></a></li-->
		</ul>
		<div class="tab-content">
			<div class="tab-btn">
				<a class="close" href="#"></a>
				<a class="overlay" href="#"></a>
			</div>
			<div id="tab-1" class="tab thumbs-panel">
				<div class="heading">
					<h3><span>Курсы</span></h3>
				</div>
				<div class="tab-scroll">
					<div class="tab-holder">
						<?//include('view_thumbs.php')?>
						<?=$thumbHTML?>
					</div>
				</div>
			</div>
			<div id="tab-2" class="tab content-panel">
				<div class="heading">
					<h3><span>Оглавление</span></h3>
				</div>
				<div class="tab-preambula">
					<img class="main" src="<?=$lessonInfo['Thumb']['thumb']?>" alt="<?=$lessonInfo['Lesson']['lesson_title']?>">
					<b><?=$lessonInfo['Lesson']['course_name']?>:</b>
					<h3><?=$lessonInfo['Lesson']['lesson_title']?></h3>
				</div>
				<div class="tab-scroll">
					<div class="tab-holder">
						<span class="chaptersContainer">
							<?=$chaptersHTML?>
						</span>
					</div>
				</div>
			</div>
			<div id="tab-3" class="tab favorite-panel">
				<div class="heading">
					<h3><span>Избранное</span></h3>
				</div>
				<div class="tab-scroll">
					<div class="tab-holder">
						<?=$favoriteHTML?>
					</div>
				</div>
			</div>
			<div id="tab-4" class="tab search-panel">
				<div class="heading">
					<h3><span>Поиск</span></h3>
				</div>
				<div class="tab-scroll">
					<div class="tab-holder">
						<form action="" method="post">
						<div class="input-append">
							<input type="text" id="q" size="16" name="q" value="" placeholder="поиск" style="width: 150px;">
							<button class="btn" type="submit"><i class="icon-search"></i></button>
						</div>
						</form>
						<? include ('view_search.php')?>
					</div>
				</div>
			</div>
			<div id="tab-5" class="tab comments-panel">
				<div class="heading">
					<h3><span>Обсуждение</span></h3>
				</div>
				<div class="tab-scroll">
					<div class="tab-holder">
						<span class="postsContainer">
							<?=$postsHTML?>
						</span>
						<? include('view_postform.php')?>
					</div>
				</div>
			</div>
			<div id="tab-6" class="tab notes-panel">
				<div class="heading">
					<h3><span>Заметки</span></h3>
				</div>
				<div class="tab-scroll">
					<div class="tab-holder">
						<span class="notesContainer">
							<?=$notesHTML?>
						</span>
						<? include('view_notesform.php')?>
					</div>
				</div>
			</div>
			<div id="tab-7" class="tab">
				<div class="heading">
					<h3><span>tab-7</span></h3>
				</div>
				<div class="tab-scroll">
					<div class="tab-holder">
						7
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
						Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
						Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
						Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="right-slider">
		<div class="right-slider-in">
			<div class="right-slider-in-in">
				<div class="right-slider-in-in-in">
					<div class="slider-container pull-right" style="margin: 10px 0px 0 0;"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="play-box">
		<div class="play-box-holder">
<?
	if ($audio && isset($audio['file'])) {
?>
			<audio class="audio" controls="controls" preload="">
                <source src="<?=AUDIO_DIR.$audio['file']?>">
            </audio>
<?
	}
?>
		</div>
	</div>
<?
	$scripts = array(
		'jquery-ui-1.10.3.custom.min',
		'bootstrap-fileupload',
		'bootstrap-collapse',
		'bootstrap-transition',
		'bootstrap-carousel',
		'bootstrap-slider',
		'bootstrap.min',
		'scripts',
		'jquery.main',
		'jquery.form.min',
		'jquery.iframe-transport',
		'jquery.fileupload',
		'lesson'
	);
	foreach ($scripts as $js) {
?>
	<script src="<?=PUBLIC_DIR?>js/<?=$js?>.js"></script>
<?
	}
?>
	<form id="extra-nav" action="" method="get" style="display: none;">
		<input type="hidden" name="id" value="<?=$lessonID?>" />
		<input id="p" type="hidden" name="p" value="<?=$paraID?>" />
	</form>
<?
	if ($lEditMode) {
?>
	<div id="saveMsg" title="Сохранение страницы..." style="display: none;">
		<div style="margin-top: 30px; text-align: center;">

		</div>

	</div>
<?
	}
?>
</div>
</body>
</html>