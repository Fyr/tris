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
	this.sendActionRequest = function(e, snippet, action, data, successFn, reqOptions) {
		self.sendRequest(e, 'snippet', snippet, action, data, successFn, reqOptions);
	},
	this.sendComponentRequest = function(e, snippet, action, data, successFn, reqOptions) {
		self.sendRequest(e, 'component', snippet, action, data, successFn, reqOptions);
	},
	this.sendRequest = function(e, type, snippet, action, data, successFn, reqOptions) {
		var panel = self.getJQContext(e);
		self.beforeRequest(panel);
		ajaxOptions = {
			url: '/mercury/index.php?snippet=' + snippet + '&type=' + type + '&action=' + action + '&para_id=' + paraID,
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
		};
		if (reqOptions) {
			ajaxOptions = $.extend(ajaxOptions, reqOptions);
		}
		$.ajax(ajaxOptions);
	},
	this.checkResponse = function(response) {
		if (response && response.status && response.status == 'ERROR') {
			alert(response.errMsg);
			return false;
		}
		return true;
	},
	this.getSelectedThumbsIDs = function(e) {
		var panel = self.getJQContext(e);

		var ids = new Array();
		$('.choose-thumb.selected', panel).each(function(){
			ids.push(this.id.replace(/thumb_/, ''));
		});
		return ids.join();
	},
	this.getJSON = function(json_extras) {
		return JSON.parse(json_extras.replace(/\'/g, '"'));
	},
	this.updateImageModeSizes = function(e, w, h) {
		var panel = self.getJQContext(e);

		$('.ipad-size', panel).html(parseInt(w * 80 / 100) + 'x' + parseInt(h * 80 / 100));
		$('.mobile-size', panel).html(parseInt(w * 50 / 100) + 'x' + parseInt(h * 50 / 100));
	},
	this.updateSizeOptions = function(e) {
		var panel = self.getJQContext(e);
		var extras = self.getJSON($(e).data('extras'));
		var original = extras.size.original, desktop = extras.size.desktop;

		$('.original-size', panel).html(original.w + 'x' + original.h);
		var aFile = $(e).data('file').split('.');
		$('.image-format', panel).html(aFile[1].toUpperCase());

		$('#desktop_width', panel).val(desktop.w);
		$('#desktop_height', panel).val(desktop.h);

		self.updateImageModeSizes(e, desktop.w, desktop.h);
	},
	this.selectThumbs = function(e, lMultiSelect) {
		var panel = self.getJQContext(e);
		if (lMultiSelect) {
			if ($(e).hasClass('selected')) {
				$(e, panel).removeClass('selected');
			} else {
				$(e).addClass('selected');
			}
		} else {
			$('.choose-thumb', panel).removeClass('selected');
			$(e).addClass('selected');

			self.updateSizeOptions(e);
			$('.image-info', panel).show();
			$('#desktop_width', panel).attr('disabled', false);
			$('#desktop_height', panel).attr('disabled', false);
			self.enableButton(e, false, '.imageSettings .regen-btn');
		}
		self.enableActions(e, $('.choose-thumb.selected', panel).size());
		$('#img_ids', panel).val(self.getSelectedThumbsIDs(e));
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
	this.enableButton = function(e, lEnable, selector) {
		var panel = self.getJQContext(e);
		if (lEnable) {
			$(selector, panel).removeClass('disabled');
			$(selector, panel).get(0).disabled = false;
		} else {
			$(selector, panel).addClass('disabled');
			$(selector, panel).get(0).disabled = true;
		}
	},
	this.enableUpload = function(e, lEnable) {
		var panel = self.getJQContext(e);
		self.enableButton(e, lEnable, '.uploadImages .upload-btn');
	},
	this.deleteImage = function (e) {
		var panel = self.getJQContext(e);
		// var id = $('.choose-thumb.selected', panel).attr('id').replace(/thumb_/, '');
		self.sendComponentRequest(e, 'medialib', 'deleteImage', {img_ids: self.getSelectedThumbsIDs(e)}, function(panel, response){
			$('.chooseThumb', panel).html(response.thumbsHTML);
			self.enableActions(e, false);
		});
	},
	this.uploadImage = function(e, selector) {
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
					formdata.append("img_ids", self.getSelectedThumbsIDs(e));
				}

				if (formdata) {
					// begin upload
					var snippet = 'medialib', action = 'upload', type = 'component';
					/*
					self.sendComponentRequest(e, snippet, action, formdata, function(response) {
						$('.uploadImages #form', panel).show();
						$('.uploadImages .loader', panel).hide();

						$(input).val('');
						self.enableUpload(e, false);
						$('.chooseThumb', panel).html(response.thumbsHTML);
					}, {processData: false, contentType: false});
					*/
					$.ajax({
						url: '/mercury/index.php?snippet=' + snippet + '&type=' + type + '&action=' + action + '&para_id=' + paraID,
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

	},
	this.changeImageSettings = function(e) {
		function recalc(x1, x2, y1) {
			k = x2 / x1;
			return parseInt(y1 * k);
		}
		var panel = self.getJQContext(e);
		var orig_size = $('.original-size', panel).html().split('x'), w, h;

		if ($(e).attr('id') == 'desktop_width') {
			w = $(e).val();
			h = recalc(orig_size[0], w, orig_size[1]);
			$('#desktop_height', panel).val(h);
		} else if ($(e).attr('id') == 'desktop_height') {
			h = $(e).val();
			w = recalc(orig_size[1], h, orig_size[0]);
			$('#desktop_width', panel).val(w);
		}
		self.updateImageModeSizes(e, w, h);
		self.enableButton(e, true, '.imageSettings .regen-btn');
	},
	this.saveImageSettings = function(e) {
		var panel = self.getJQContext(e);
		// self.sendComponentRequest(e, 'medialib', 'saveImageSettings', {id: , w: , h: }, successFn)
		$('.imageSettings .form', panel).hide();
		$('.imageSettings .loader', panel).show();

		var snippet = 'medialib', action = 'saveImageSettings', type = 'component';
		$.ajax({
			url: '/mercury/index.php?snippet=' + snippet + '&type=' + type + '&action=' + action + '&para_id=' + paraID,
			type: "POST",
			data: {
				w: $('#desktop_width', panel).val(),
				h: $('#desktop_height', panel).val(),
				img_ids: self.getSelectedThumbsIDs(e)
			},
			dataType: 'json',
			success: function (response) {
				$('.imageSettings .form', panel).show();
				$('.imageSettings .loader', panel).hide();
				if (self.checkResponse(response)) {
					self.enableButton(e, false, '.imageSettings .regen-btn');
					$('.chooseThumb', panel).html(response.thumbsHTML);
				}
			}
		});
	}
}
Mercury.Snippet.API = new SnippetAPI();
}