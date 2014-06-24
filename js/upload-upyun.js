(function(jQuery) {
	jQuery.upyunDefaultSetting = {
		domain : "http://v0.api.upyun.com/",
		secureUri : false,
		error : function(code,msg) {
			alert(msg);
		},
		timeout : 30
	}
	/*
	 * options:{bucket: fileSelector:string, policy, signature,
	 * [domain:],success,error }
	 */
	jQuery.upload2upyun = function(options) {
		options = jQuery.extend(jQuery.ajaxSettings, jQuery.upyunDefaultSetting, options);
		if (!options.bucket || !options.fileSelector || !options.policy
				|| !options.signature) {
			throw new Error("upload2upyun arguments error");
		}
		var actionUrl = options.domain + "/" + options.bucket;
		var id = new Date().getTime();
		var frameId = 'jUploadUpyunFrame' + id;
		var formId = 'jUploadUpyunForm' + id;
		var uploadData = {
			policy : policy,
			signature : signature
		}
		var requestDone;
		var timer = null;
		if (options.timeout > 0) {
			timer = setTimeout(function() {
				if (!requestDone)
					uploadCallback("timeout");
			}, options.timeout * 1000);
		}
		var form = createUploadForm(formId, options.fileSelector, uploadData);
		var frame = createUploadIframe(frameId, options.secureUri);
		var uploadCallback = function(status) {
			jQuery(frame).unbind(); 
			clearTimeout(timer);
			var code = -1;
			requestDone = true;
			if (status != "timeout") {
				var return_url = frame.contentWindow.location.href;
				var msg = getQueryString(return_url, "message");
				code = getQueryString(return_url, "code");
				var url = getQueryString(return_url, "url");
				var time = getQueryString(return_url, "time");
				var sign = getQueryString(return_url, "sign");
				if (code == 200) {
					if (options.success)
						options.success(url, time, sign);
					if (options.global)
						jQuery.event.trigger("ajaxSuccess", [ options ]);
				} else {
					options.error(code, msg);
				}
			} else
				options.error(-1, status);

			if (options.global)
				jQuery.event.trigger("ajaxComplete", [ options ]);
			if (options.global && !--jQuery.active)
				jQuery.event.trigger("ajaxStop");
			if (options.complete)
				options.complete(code, status);
			setTimeout(function() {
				jQuery(frame).remove();
				jQuery(form).remove();
			}, 100);
		}

		if (options.global && !jQuery.active++) {
			jQuery.event.trigger("ajaxStart");
		}
		if (options.global)
			jQuery.event.trigger("ajaxSend", options);

		jQuery('#' + frameId).load(uploadCallback); 
		jQuery(form).attr('action', actionUrl);
		jQuery(form).attr('method', 'POST');
		jQuery(form).attr('target', frameId);
		if (form.encoding) {
			jQuery(form).attr('encoding', 'multipart/form-data');
		} else {
			jQuery(form).attr('enctype', 'multipart/form-data');
		}
		jQuery(form).submit();

		return {
			abort : function() {
			}
		};

	}
	function createUploadIframe(frameId, uri) {
		var iframeHtml = '<iframe id="' + frameId + '" name="' + frameId
				+ '" style="position:absolute; top:-9999px; left:-9999px"';
		if (window.ActiveXObject) {
			if (typeof uri == 'boolean') {
				iframeHtml += ' src="' + 'javascript:false' + '"';

			} else if (typeof uri == 'string') {
				iframeHtml += ' src="' + uri + '"';

			}
		}
		iframeHtml += ' />';
		jQuery(iframeHtml).appendTo(document.body);
		return document.getElementById(frameId);
	}

	function createUploadForm(formId, fileSelector, data) {
		var fileId = formId + "_" + fileSelector;
		var form = jQuery('<form  action="" method="POST" name="' + formId
				+ '" id="' + formId + '" enctype="multipart/form-data"></form>');

		var oldElement = jQuery(fileSelector);
		var newElement = jQuery(oldElement).clone();
		jQuery(oldElement).attr('id', fileId);
		jQuery(oldElement).before(newElement);
		jQuery(oldElement).appendTo(form);
		if (data) {
			for ( var i in data) {
				jQuery(
						'<input type="hidden" name="' + i + '" value="'
								+ data[i] + '" />').appendTo(form);
			}
		}
		jQuery(form).css('position', 'absolute');
		jQuery(form).css('top', '-1200px');
		jQuery(form).css('left', '-1200px');
		jQuery(form).appendTo('body');
		return form;
	}
	function getQueryString(url, name) {
		var reg = new RegExp("(^|&|\\?)" + name + "=([^&]*)(&|$)", "i");
		var r = url.match(reg);
		if (r != null)
			return unescape(r[2]);
		return null;
	}
})(jQuery);