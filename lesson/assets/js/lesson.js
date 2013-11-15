var lesson = {
	process: false,

	beforeSendRequest: function() {
		$('.processing').show();
		lesson.process = true;
	},

	afterSendRequest: function() {
		$('.processing').hide();
		lesson.process = false;
	},

	sendRequest: function(action, data, successFn, method) {
		method = (typeof(method) == 'undefined' || method == 'get') ? 'get' : 'post';
		if (!lesson.process) {
			lesson.beforeSendRequest();
			$.ajax({
				url: 'lesson.php?action=' + action + '&id=' + lessonID, // + '&chapter=' + chapterID,
				data: data,
				type: method,
				dataType: 'json',
				success: function(data){
					lesson.afterSendRequest();
					if (lesson.checkResponse(data)) {
						if (lEditMode && data.html || action == 'switchPara') {
							$('.chaptersContainer').html(data.html);
						}
						if (successFn) {
							successFn(data);
						}
					}
				}
			});
		}
	},

	ajaxForm: function(selector, action, successFn) {
		$(selector).ajaxForm({
			dataType: 'json',
			url: 'lesson.php?id=' + lessonID + '&action=' + action,
			beforeSubmit: function(arr, $form, options) {
				lesson.beforeSendRequest();
				return true;
			},
			success: function(response) {
				lesson.afterSendRequest();
				if (lesson.checkResponse(response)) {
					if (successFn) {
						successFn(response);
					}
				} else {
					for(var field in response.errFields) {
						$(selector + ' .' + field + '.error').html(response.errFields[field]);
					}
				}
			}
		});
	},

	checkResponse: function(data) {
		if (data && data.status && data.status == 'ERROR') {
			alert(data.errMsg);
			return false;
		}
		return true;
	},

	lessonUpdate: function(lesson_id) {
		if (!lesson.process) {
			var title = prompt('Введите название урока', $('#lesson_' + lesson_id).html());
			if (title) {
				lesson.sendRequest('lessonUpdate', {'id': lesson_id, 'title': title}, function(response){
					// var activeOption = $(".thumbs-panel .accordion").accordion('option', 'active');
					$('.thumbs-panel .tab-holder').html(response.thumbHTML);
					// initThumbAccordion(activeOption);
				}, 'post');
			}
		}
	},

	lessonDelete: function(id) {
		if (!lesson.process && confirm('Вы действительно хотите удалить урок "' + $('#lesson_' + id).html() + '"?')) {
			lesson.sendRequest('lessonDelete', {'id': id}, function(response){
				$('.thumbs-panel .tab-holder').html(response.thumbHTML);
			}, 'post');
		}
	},

	lessonImageUpload: function(id) {
		if (!lesson.process) {
			var form = $('.sampleUploadLessonImage').html();
			form = form.replace(/data-btn="cancel"/, '/onclick="lesson.lessonImageUploadCancel(' + id +')"');
			$('#lessonImage_' + id).html(form);
			$('#lessonImage_' + id + ' input[type=hidden]').val(id);

			lesson.ajaxForm('#lessonImage_' + id + ' form', 'lessonImageUpload', function(response){
				$('#lessonImage_' + id).html('');
				$('.thumbs-panel .tab-holder').html(response.thumbHTML);
				if (lessonID == id) {
					$('.content-panel .tab-preambula img.main').attr('src', response.Thumb);
				}
				/*
				if (paraID == id) {
					$('.nav-links .play').show();
					$('.audio source').attr('src', response.file);
				}
				*/
			});
		}
	},

	lessonImageUploadCancel: function(id) {
		$('#lessonImage_' + id).html('');
	},

	chapterUpdate: function(id) {
		if (!lesson.process) {
			var title = prompt('Введите название главы', $('#chapter_' + id).html());
			if (title) {
				lesson.sendRequest('chapterUpdate', {'id': id, 'title': title}, function(data){
					$('#chapter_' + id).html(title);
				}, 'post');
			}
		}
	},

	chapterDelete: function(id) {
		if (!lesson.process && confirm('Вы действительно хотите удалить главу "' + $('#chapter_' + id).html() + '"?')) {
			lesson.sendRequest('chapterDelete', {'id': id}, null, 'post');
		}
	},

	chapterReorder: function() {
		lesson.reorder();
		// $('.chaptersContainer form').submit();
	},

	chapterSort: function(id, dir) {
		lesson.sendRequest('chapterSort', {'id': id, 'dir': dir}, null, 'post');
	},

	paraUpdate: function(id, chapterID) {
		if (!lesson.process) {
			var title = prompt('Введите название параграфа', $('#para_' + id).html()); //
			if (title) {
				lesson.sendRequest('paraUpdate', {'id': id, 'chapter_id': chapterID, 'title': title}, function(response){
					aParaInfo = response.aParaInfo;
					initNavi(paraID);
				}, 'post');
			}
		}
	},

	paraDelete: function (id) {
		if (!lesson.process && confirm('Вы действительно хотите удалить параграф "' + $('#para_' + id).html() + '"?')) {
			lesson.sendRequest('paraDelete', {'id': id}, null, 'post');
		}
	},

	paraReorder: function() {
		// $('.chaptersContainer form').submit();
		lesson.reorder();
	},

	paraSort: function(id, dir) {
		lesson.sendRequest('paraSort', {'id': id, 'dir': dir}, null, 'post');
	},

	reorder: function () {
		lesson.sendRequest('reorder', $('.chaptersContainer form').serialize(), null, 'post');
	},

	getCurrParaIndex: function(paraID) {
		for(var i = 0; i < aParaInfo.length; i++) {
			if (aParaInfo[i].id == paraID) {
				return i;
			}
		}
		return 0;
	},

	getCurrChapterIndex: function(paraID) {
		var chapter = 0, chapterI = -1;
		for(var i = 0; i < aParaInfo.length; i++) {
			if (chapter != aParaInfo[i].chapter_id) {
				chapterI++;
				chapter = aParaInfo[i].chapter_id;
			}
			if (paraID == aParaInfo[i].id) {
				return chapterI;
			}
		}
		return 0;
	},

	setFavorite: function(_paraID, lFav) {
		if (paraID == _paraID) {
			$('.nav-links .favorite').removeClass('active');
			if (lFav) {
				$('.nav-links .favorite').addClass('active');
			}
		}
		$('#fav-list-item_' + _paraID).remove();
		lesson.sendRequest('setFavorite', {'id': _paraID, 'favorite': lFav}, function(response){
			$('.favorite-panel .tab-holder').html(response.favoriteHTML);
			$('.favoriteCount').html((response.favCount) ? '<em>' + response.favCount + '</em>' : '');
		}, 'post');
	},

	postInit: function(response) {
		$('.postsContainer').html(response.postsHTML);
		$('.postsCount').html((response.postsCount) ? '<em>' + response.postsCount + '</em>' : '');

		$('.form-post input[name=para_id]').val(paraID);
	},

	postUpdate: function(id) {
		if (!$('#post_' + id + ' .editpost').html()) {
			var form = $('.samplePost').html();
			form = form.replace(/data-btn="save"/, '/onclick="lesson.postUpdateSave(' + id +')"');
			form = form.replace(/data-btn="cancel"/, '/onclick="lesson.postUpdateCancel(' + id +')"');
			$('#post_' + id + ' .editpost').html(form);
			$('#post_' + id + ' .editpost form input[name=id]').val(id);
			$('#post_' + id + ' .editpost form textarea').html($('#post_' + id + ' .post').html().replace(/<br>/, ''));
			$('#post_' + id + ' .post').hide();
		}
	},

	postUpdateCancel: function(id) {
		$('#post_' + id + ' .editpost').html('');
		$('#post_' + id + ' .post').show();
	},

	postUpdateSave: function(id) {
		var body = $('#post_' + id + ' .editpost form textarea').val();
		if (body.replace(/\s/, '')) {
			lesson.sendRequest('postUpdate', {'id': id, 'body': body}, function(response){
				lesson.postInit(response);
			}, 'post');
		}
	},

	postDelete: function(id) {
		if (!lesson.process && confirm('Вы действительно хотите удалить комментарий?')) {
			lesson.sendRequest('postDelete', {'id': id}, function(response){
				lesson.postInit(response);
			}, 'post');
		}
	},

	notesInit: function(response) {
		$('.notesContainer').html(response.notesHTML);
		$('.notesCount').html((response.notesCount) ? '<em>' + response.notesCount + '</em>' : '');

		$('.form-notes input[name=para_id]').val(paraID);
	},

	noteUpdate: function(id) {
		if (!$('#note_' + id + ' .editnote').html()) {
			var form = $('.sampleNote').html();
			form = form.replace(/data-btn="save"/, '/onclick="lesson.noteUpdateSave(' + id +')"');
			form = form.replace(/data-btn="cancel"/, '/onclick="lesson.noteUpdateCancel(' + id +')"');
			$('#note_' + id + ' .editnote').html(form);
			$('#note_' + id + ' .editnote form input[name=id]').val(id);
			$('#note_' + id + ' .editnote form textarea').html($('#note_' + id + ' .note').html().replace(/<br>/, ''));
			$('#note_' + id + ' .note').hide();
		}
	},

	noteUpdateCancel: function(id) {
		$('#note_' + id + ' .editnote').html('');
		$('#note_' + id + ' .note').show();
	},

	noteUpdateSave: function(id) {
		var body = $('#note_' + id + ' .editnote form textarea').val();
		if (body.replace(/\s/, '')) {
			lesson.sendRequest('noteUpdate', {'id': id, 'body': body, 'para_id': paraID}, function(response){
				lesson.notesInit(response);
			}, 'note');
		}
	},

	noteDelete: function(id) {
		if (!lesson.process && confirm('Вы действительно хотите удалить заметку?')) {
			$('#note_' + id).remove();
			lesson.sendRequest('noteDelete', {'id': id, 'para_id': paraID}, function(response){
				lesson.notesInit(response);
			}, 'note');
		}
	},

	audioUpload: function(id) {
		if (!lesson.process) {
			var form = $('.sampleUploadAudio').html();
			form = form.replace(/data-btn="cancel"/, '/onclick="lesson.audioUploadCancel(' + id +')"');
			$('#audio_' + id).html(form);
			$('#audio_' + id + ' input[type=hidden]').val(id);

			lesson.ajaxForm('#audio_' + id + ' form', 'audioUpload', function(response){
				$('#audio_' + id).html('');
				if (paraID == id) {
					$('.nav-links .play').show();
					$('.audio source').attr('src', response.file);
				}
			});
		}
	},

	audioDelete: function(id) {
		if (!lesson.process && confirm('Вы действительно хотите удалить аудио?')) {
			lesson.sendRequest('audioDelete', {'id': id}, function(response){
				if (paraID == id) {
					$('.nav-links .play').hide();
					$('.audio source').attr('src', '');
				}
			}, 'post');
		}
	},

	audioUploadCancel: function(id) {
		$('#audio_' + id).html('');
	},

	switchPara: function(_paraID) {
		if (lEditMode) {
			$('#p').val(_paraID);
			$('#extra-nav').submit();
		} else {
			lesson.sendRequest('switchPara', {'id': _paraID}, function(response){
				paraID = _paraID;
				$('#lesson-container').html(response.content);

				lesson.postInit(response);
				lesson.notesInit(response);

				initContentHeight();
				initNavi(paraID);
				initQuiz();
				initGallery();
				initSnippetSlider();
				initTopIcons(parseInt(response.favorite));
			}, 'post');
		}
	},

}

function initSlider(paraID) {
	if (aParaInfo && aParaInfo.length > 1) {
		$('.slider-container').html('');
		$('.slider-container').append('<input type="text" value="">');
		$('.slider-container input').slider({
			min: 0,
			orientation: "vertical",
			value: lesson.getCurrParaIndex(paraID),
			max: aParaInfo.length - 1,
			formater: function(value) {
				return aParaInfo[value].title;
			}
		})
		.on('slideStop', function(e){
			lesson.switchPara(aParaInfo[e.value].id);
		});
	}
}

function initNavi(paraID) {
	var currPara = lesson.getCurrParaIndex(paraID);
	if (currPara > 0) {
		$('.subnav-fixed-top .nav-links .up').show();
		var a = '<a href="javascript:void(0)" onclick="lesson.switchPara(' + aParaInfo[currPara - 1].id + ')">' + aParaInfo[currPara - 1].title + '</a>';
		$('.subnav-fixed-top h2').html(a);
	} else {
		$('.subnav-fixed-top .nav-links .up').hide();
		$('.subnav-fixed-top h2').html('&nbsp;');
	}

	if (currPara < (aParaInfo.length - 1)) {
		$('.subnav-fixed-bottom .nav-links .bottom').show();
		var a = '<a href="javascript:void(0)" onclick="lesson.switchPara(' + aParaInfo[currPara + 1].id + ')">' + aParaInfo[currPara + 1].title + '</a>';
		$('.subnav-fixed-bottom h2').html(a);
	} else {
		$('.subnav-fixed-bottom .nav-links .bottom').hide();
		$('.subnav-fixed-bottom h2').html('&nbsp;');
	}

	initSlider(paraID);
	$(".content-panel .accordion").accordion({
		active: false,
		collapsible: true,
		heightStyle: "content"
	});
	$(".content-panel .accordion").accordion('option', 'active', lesson.getCurrChapterIndex(paraID));

	// scroll to actove paragraph
	setTimeout(function() {
			scrollerTop = $('#para_' + paraID).closest('.tab-scroll').position().top;
			paraTop = $('#para_' + paraID).position().top;

			deviation = paraTop - scrollerTop;

			$('#para_' + paraID).closest('.tab-scroll').animate(
				{
					'scrollTop' : deviation
				},
				1000
			);
		},
		800
	);
}

function initTopIcons(lFav) {
	$('.nav-links .favorite').removeClass('active');
	if (lFav) {
		$('.nav-links .favorite').addClass('active');
	}
}

function initThumbAccordion(activeOption) {
	$(".thumbs-panel .accordion").accordion({
		active: activeOption,
		collapsible: true,
		heightStyle: "content"
	});
}

function navigateNext() {
	var currPara = lesson.getCurrParaIndex(paraID);
	if (currPara < (aParaInfo.length - 1)) {
		lesson.switchPara(aParaInfo[currPara + 1].id);
	}
}

function navigatePrev() {
	var currPara = lesson.getCurrParaIndex(paraID);
	if (currPara > 0) {
		lesson.switchPara(aParaInfo[currPara - 1].id);
	}
}

function quizSubmit(e) {
	var snippet = $(e).closest('.quiz-snippet');
	body = $('textarea', snippet).val();
	lesson.sendRequest('quizAnswer', {para_id: paraID, snippet_id: snippet.data('snippet').replace(/snippet_/, ''), body: body}, function(response){
		$('.quiz-answer', snippet).html(response.answers);
		$('.quiz-body form', snippet).remove();
	}, 'post');
}

function initQuiz() {
	$('.quiz-snippet').each(function(){
		var snippet = $(this);
		lesson.sendRequest('quizAnswer', {para_id: paraID, snippet_id: snippet.data('snippet').replace(/snippet_/, '')}, function(response){
			if (response.answers) {
				$('.quiz-answer', snippet).html(response.answers);
				$('.quiz-body form', snippet).remove();
			}
		}, 'post');
	});

}

function initGallery() {
	$('.gallery-snippet').each(function(){
		var snippet = $(this);
		$('.gallery-thumb', snippet).attr('rel', 'photoalbum_' + snippet.data('snippet').replace(/snippet_/, ''));
	});
	$('.gallery-thumb').fancybox({
		padding: 5
	});
}

function initSnippetSlider() {
	var sliders = new MercurySliders();
}