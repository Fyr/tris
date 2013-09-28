function initMercuryAPI() {
Mercury.Snippet.API = {
	getJQContext: function (e) {
		return $(e).closest('.mercury-options-panel');
	},
	sendRequest: function(e, snippet, action, data, successFn) {
		Mercury.Snippet.API.beforeRequest(e);
		$.ajax({
			url: '/mercury/snippets/index.php?snippet=' + snippet + '&action=' + action + '&para_id=' + paraID,
			data: data,
			type: 'post',
			dataType: 'json',
			success: function(response) {
				Mercury.Snippet.API.afterRequest(e);
				if (Mercury.Snippet.API.checkResponse(response)) {
					if (successFn) {
						successFn($(e).closest('.mercury-options-panel'), response);
					}
				}
			}
		});
	},
	beforeRequest: function(e) {
		var panel = $(e).closest('.mercury-options-panel');
		$('.form-actions .actions', panel).hide();
		$('.form-actions .loader', panel).show();
	},
	afterRequest: function(e) {
		var panel = $(e).closest('.mercury-options-panel');
		$('.form-actions .actions', panel).show();
		$('.form-actions .loader', panel).hide();
	},
	checkResponse: function(response) {
		if (response && response.status && response.status == 'ERROR') {
			alert(response.errMsg);
			return false;
		}
		return true;
	},
	selectThumb: function(e, img_src) {
		var panel = $(e).closest('.mercury-options-panel');

		// Select thumb
		$('.choose-thumb', panel).removeClass('selected');
		$(e).addClass('selected');

		Mercury.Snippet.API.enableActions(e, true);

		// Remember ing_src option
		$('#img_src', panel).val(img_src);
	},
	enableActions: function (e, lEnable) {
		var panel = Mercury.Snippet.API.getJQContext(e);
		lEnable = (typeof(lEnable) == 'undefined') ? true : lEnable;
		$('.form-actions .btn', panel).each(function(){
			this.disabled = !lEnable;
			if (lEnable) {
				$(this).removeClass('disabled');
			} else {
				$(this).addClass('disabled');
			}
		});
	},
	chooseThumb: function(e)  {
		Mercury.Snippet.API.sendRequest(e, 'paraimage', 'getThumbs', null, function(panel, response) {
			var panel = $(e).closest('.mercury-options-panel');
			console.log(response);
			html = 'Choose image<br>';
			for(var i in response.images) {
				html += '<img src="' + response.images[i].shop_thumbnail + '" alt="" />';
			}
			$('#chooseThumb', panel).html(html);
			alert($('#chooseThumb', panel).html());
			$('#chooseThumb', panel).dialog({
				buttons: [
					{ text: "Choose", click: function() { $('#chooseThumb', panel).dialog( "close" ); } },
					{ text: "Cancel", click: function() { $('#chooseThumb', panel).dialog( "close" ); } }
				],
				draggable: false,
				modal: true,
				height: 400,
				width: 600
			});
			alert('!');
		});
	},
	enableUpload: function(e, lEnable) {
		var panel = $(e).closest('.mercury-options-panel');
		if (lEnable) {
			$('.uploadImages .upload-btn', panel).removeClass('disabled');
			$('.uploadImages .upload-btn', panel).get(0).disabled = false;
		} else {
			$('.uploadImages .upload-btn', panel).addClass('disabled');
			$('.uploadImages .upload-btn', panel).get(0).disabled = true;
		}
	},
	deleteImage: function (e) {
		var panel = $(e).closest('.mercury-options-panel');
		var id = $('.choose-thumb.selected', panel).attr('id').replace(/thumb_/, '');
		Mercury.Snippet.API.sendRequest(e, 'paraimage', 'deleteImage', {id: id, img_src: $('#img_src', panel).val()}, function(panel, response){
			$('.chooseThumb', panel).html(response.thumbsHTML);
			Mercury.Snippet.API.enableActions(e, false);
		});
	},
	uploadImage: function(e, selector, img_src) {
		var panel = $(e).closest('.mercury-options-panel');

		$('.uploadImages #form', panel).hide();
		$('.uploadImages .loader', panel).show();

		var input = $(selector, panel).get(0);

		var formdata = false;
		if (window.FormData) {
			formdata = new FormData();
		}

		var reader, file;

		for (var i = 0; i < input.files.length; i++) {
			file = input.files[i];

			if (!!file.type.match(/image.*/)) {
				if (window.FileReader) {
					reader = new FileReader();
					reader.onloadend = function (e) {
						//showUploadedItem(e.target.result, file.fileName);
					};
					reader.readAsDataURL(file);
				}

				if (formdata) {
					formdata.append(input.name, file);
					formdata.append("lesson_id", lessonID);
					formdata.append("img_src", img_src);
				}

				if (formdata) {
					// begin upload
					var snippet = 'paraimage', action = 'upload';
					$.ajax({
						url: '/mercury/snippets/index.php?snippet=' + snippet + '&action=' + action + '&para_id=' + paraID,
						type: "POST",
						data: formdata,
						dataType: 'json',
						processData: false, // !!!
						contentType: false,
						success: function (response) {
							$('.uploadImages #form', panel).show();
							$('.uploadImages .loader', panel).hide();

							$(input).val('');
							Mercury.Snippet.API.enableUpload(e, false);
							$('.chooseThumb', panel).html(response.thumbsHTML);
						}
					});
				}
			} else {
				alert('Не корректное изображение!');
			}
		}

	}
}
}