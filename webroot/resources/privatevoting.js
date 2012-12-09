function openPage(page,id) {
	$.ajax('index.php',{
		data: {
			'do':'Show'+page
		},
		success: function(data, status, xhr) {
			var redirect = xhr.getResponseHeader("X-Location");
			if (redirect) {
				document.location = redirect;
			} else {
				$('#central-content-for-privatevoting').html(data);
			}
		},
		error: function(data) {
			alert('error: '+data);
		}
	});
}
