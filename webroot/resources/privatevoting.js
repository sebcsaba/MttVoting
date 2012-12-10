function decoteString(input) {
	if (/^=\?UTF-8\?Q\?(.*)\?\=/.test(input)) {
		return decodeURIComponent(input.substring(10,input.length-2).replace(/=/g,'%'));
	} else {
		return input;
	}
}

function myAjax(data, realSuccess, method) {
	if (!method) method='get';
	$.ajax('index.php',{
		type: method,
		data: data,
		success: function(data, status, xhr) {
			if (xhr.getResponseHeader("X-Location")) {
				document.location = xhr.getResponseHeader("X-Location");
			} else if (xhr.getResponseHeader("X-Error")) {
				alert(decoteString(xhr.getResponseHeader("X-Error")));
			} else {
				realSuccess(data);
			}
		},
		error: function(x,y,z) {
			alert('error: '+x.statusCode);
		}
	});
}

function openPage(page,paramData) {
	if (!paramData) paramData = {};
	paramData['do'] = 'Show'+page;
	myAjax(paramData,function(data){
		$('#central-content-for-privatevoting').html(data);
	});
}

function submitForm(form) {
	myAjax(form.serialize(), function(data){
		$('#central-content-for-privatevoting').html(data);
	},'post');
}

function addNewAnswerField() {
	var pt = $('#answer_prototype');
	var index = pt.parent().children('input').length-1;
	var result = pt.clone();
	result.attr('name','answer['+index+']');
	result.removeAttr('id');
	result.removeAttr('onfocus');
	result.insertBefore(pt);
	result.focus();
}
