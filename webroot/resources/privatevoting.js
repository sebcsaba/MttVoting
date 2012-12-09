function openPage(page,paramData) {
	if (!paramData) paramData = {};
	paramData['do'] = 'Show'+page;
	$.ajax('index.php',{
		data: paramData,
		success: function(data, status, xhr) {
			var redirect = xhr.getResponseHeader("X-Location");
			if (redirect) {
				document.location = redirect;
			} else {
				$('#central-content-for-privatevoting').html(data);
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
