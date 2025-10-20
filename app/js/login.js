// Check Location Input - Native JavaScript
function switch_location_input()
{
	const selectElement = document.getElementById('location_input_select');
	const textElement = document.getElementById('location_input_text');
	
	// Show text input if "other" is selected
	if (selectElement && selectElement.value === "other") {
		if (textElement) {
			textElement.style.display = 'block';
			textElement.focus(); // Focus on the text input for better UX
		}
	} else {
		// Hide text input if any other option is selected
		if (textElement) {
			textElement.style.display = 'none';
			textElement.value = ''; // Clear the text input
		}
	}
}

// Initialize location input behavior on page load
document.addEventListener('DOMContentLoaded', function() {
	const textElement = document.getElementById('location_input_text');
	const selectElement = document.getElementById('location_input_select');
	
	// Hide text input by default
	if (textElement) {
		textElement.style.display = 'none';
	}
	
	// Add event listener to select element
	if (selectElement) {
		selectElement.addEventListener('change', switch_location_input);
	}
});