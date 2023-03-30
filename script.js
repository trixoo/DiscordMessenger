$(document).ready(function() {
	$('#message-form').submit(function(e) {
		e.preventDefault(); // prevent the form from submitting normally
		var form_data = $(this).serialize(); // get the form data
		$.ajax({
			type: 'POST',
			url: 'send.php',
			data: form_data,
			success: function(response) {
				$('#result').html(response); // display the response in the result div
			}
		});
	});
});
