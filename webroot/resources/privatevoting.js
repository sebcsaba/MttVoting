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

function openPage(page,paramData,skipShowPrefix) {
	if (!paramData) paramData = {};
	if (!skipShowPrefix) {
		page = 'Show'+page;
	}
	paramData['do'] = page;
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

function onSelectNewParticipant(userId, userName) {
	var pt = $('#participant_prototype');
	var result = pt.clone();
	result.removeAttr('id');
	result.children('span').html(userName);
	var input = result.children('input');
	input.attr('name','participant[]');
	input.attr('value',userId);
	result.insertBefore(pt);
}

function onDeleteNewParticipant(icon) {
	$(icon).parent().remove();
}

function participantSearchInit() {
	$('#participant_search').autocomplete({
		minLength: 3,
		source: 'index.php?do=UserSearch',
		select: function(event,selected) {
			onSelectNewParticipant(selected.item.value, selected.item.label);
			$(event.target).val('');
			event.preventDefault();
		}
	});
}

function reloadLeftMenu(){
	myAjax({'do':'ShowLeftMenu'},function(data){
		$('#left-menu-for-privatevoting').html(data);
	});
}