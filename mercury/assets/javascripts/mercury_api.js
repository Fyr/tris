function initMercuryAPI() {

SnippetAPI = function() {
	var self = this;

	this.getJQContext = function (e) {
		return $(e).closest('.mercury-options-panel');
	},
	this.beforeRequest = function(panel) {
		$('.form-actions .actions', panel).hide();
		$('.form-actions .loader', panel).show();
	},
	this.afterRequest = function(panel) {
		$('.form-actions .actions', panel).show();
		$('.form-actions .loader', panel).hide();
	},
	this.sendRequest = function(e, snippet, action, data, successFn) {
		var panel = self.getJQContext(e);
		self.beforeRequest(panel);
		$.ajax({
			url: '/mercury/snippets/index.php?snippet=' + snippet + '&action=' + action + '&para_id=' + paraID,
			data: data,
			type: 'post',
			dataType: 'json',
			success: function(response) {
				self.afterRequest(panel);
				if (self.checkResponse(response)) {
					if (successFn) {
						successFn(panel, response);
					}
				}
			}
		});
	},

	this.checkResponse = function(response) {
		if (response && response.status && response.status == 'ERROR') {
			alert(response.errMsg);
			return false;
		}
		return true;
	},
	this.selectThumb = function(e, img_src) {
		var panel = self.getJQContext();
		// Select thumb
		$('.choose-thumb', panel).removeClass('selected');
		$(e).addClass('selected');

		self.enableActions(e, true);

		// Remember ing_src option
		$('#img_src', panel).val(img_src);
	},
	this.enableActions = function (e, lEnable) {
		var panel = self.getJQContext(e);
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
	this.enableUpload = function(e, lEnable) {
		var panel = self.getJQContext(e);
		if (lEnable) {
			$('.uploadImages .upload-btn', panel).removeClass('disabled');
			$('.uploadImages .upload-btn', panel).get(0).disabled = false;
		} else {
			$('.uploadImages .upload-btn', panel).addClass('disabled');
			$('.uploadImages .upload-btn', panel).get(0).disabled = true;
		}
	},
	this.deleteImage = function (e) {
		var panel = self.getJQContext(e);
		var id = $('.choose-thumb.selected', panel).attr('id').replace(/thumb_/, '');
		self.sendRequest(e, 'paraimage', 'deleteImage', {id: id, img_src: $('#img_src', panel).val()}, function(panel, response){
			$('.chooseThumb', panel).html(response.thumbsHTML);
			self.enableActions(e, false);
		});
	},
	this.uploadImage = function(e, selector, img_src) {
		var panel = self.getJQContext(e);

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
							self.enableUpload(e, false);
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
Mercury.Snippet.API = new SnippetAPI();
}