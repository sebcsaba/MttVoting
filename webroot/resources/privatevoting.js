function myAjax(data, realSuccess) {
	$.ajax('index.php',{
		data: data,
		success: function(data, status, xhr) {
			if (xhr.getResponseHeader("X-Location")) {
				document.location = xhr.getResponseHeader("X-Location");
			} else if (xhr.getResponseHeader("X-Error")) {
				alert(xhr.getResponseHeader("X-Error"));
			} else {
				realSuccess(data);
			}
		},
		error: function(x,y,z) {
			alert('error: '+x.statusCode);
			alert(x);
			alert(y);
			alert(z);
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
	myAjax(form.serialize(), function(){
		$('#central-content-for-privatevoting').html(data);
	});
}
