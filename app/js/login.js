// Check Location Input
function switch_location_input()
{
	if( ($('#location_input_select').val() == "other" && $('#location_input_select').is(':visible')) || ($('#location_input_text').val() == "" && $('#location_input_text').is(':visible')))
		switch_login();
}

// Switch Login Location Input
function switch_login()
{
	if($('#location_input_select').is(':visible'))
	{
		$('#location_input_text').show();
		$('#location_input_select').val('');
		$('#location_input_select').hide();
	}
	else if($('#location_input_text').is(':visible'))
	{
		$('#location_input_text').hide();
		$('#location_input_select').show();
	}
}