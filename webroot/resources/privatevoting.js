function openPage(page,id) {
	$.ajax('index.php',{
		data: {
			'do':'Show'+page
		},
		success: function(data) {
			$('#central-content-for-privatevoting').html(data);
		},
		error: function(data) {
			alert('error: '+data);
		}
	});
}
