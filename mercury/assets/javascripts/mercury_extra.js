var lEditMode = true;
function toggleMercury() {
	if (typeof(Mercury) == 'undefined') {
		alert("Sorry, but Mercury Editor isn't supported by your current browser.\n\nBrowsers that support the required HTML5 spec:\n\n  Chrome 10+\n  Firefox 4+\n  Safari 5+\n  Opera 11.64+\n  Mobile Safari (iOS 5+)");
	} else {
		Mercury.trigger('toggle:interface');
		lEditMode = !lEditMode;
		if (lEditMode) {
			$('.edit-mode').hide();
			$('.view-mode').show();
		} else {
			$('.edit-mode').show();
			$('.view-mode').hide();
		}
	}
}

function initMercurySave() {
	Mercury.PageEditor.prototype.save = function() {
		var data = this.serialize();
		// var lightview = Mercury.lightview(null, {title: 'Saving', closeButton: true/*, content: '<div class="save-content" align="center" style="margin-top: 50px;">Wait, while your content is been saving...</div>'*/});
		$('#saveMsg div').html('<img src="./lesson/assets/img/ajax-loader2.gif" alt="" />');
		$('#saveMsg').dialog({
			dialogClass: 'saveMsg',
			closeOnEscape: false,
			buttons: [],
			modal: true,
			resizable: false,
			draggable: false
		});
		$.post('lesson.php?action=saveContent&id=' + lessonID + '&p=' + paraID, data, function(response){
			$('#saveMsg').dialog('close');
			$('#saveMsg div').html('Страница сохранена');
			$('#saveMsg').dialog({
				dialogClass: 'saveMsg',
				buttons: [ { text: "Ok", click: function() { $('#saveMsg').dialog('close'); } } ],
				resizable: false,
				draggable: false
			});
			if (response.status == 'OK') {
				// alert('Страница сохранена');
			} else {
				alert('Ошибка сохранения страницы!');
			}
		}, 'JSON');
	}
}

function activateNoOptionsSnippet() {
	$('img[data-snippet]').remove();
}

function activateEditable() {
	$('body').on('focus', '[contenteditable="true"]', function() {
	    var $this = $(this);
	    $this.data('before', $this.html());
	    return $this;
	}).on('blur', '[contenteditable]', function() {
	    var $this = $(this);
	    if ($this.data('before') !== $this.html()) {
	        $this.data('before', $this.html());
	        $this.trigger('change');
	    }
	    return $this;
	});
}

function onChangeEditable(e, optionName) {
	var snippetID = $(e).closest('[data-snippet]').data('snippet');
	var snippet = Mercury.Snippet.find(snippetID);
	snippet.options[optionName] = $(e).html();
	// console.log('Changed snippet ' + snippetID);
}

