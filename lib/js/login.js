// Check Location Input
function switch_location_input() {
    const locationInputSelect = document.getElementById('location_input_select');
    const locationInputText = document.getElementById('location_input_text');

    if (
        (locationInputSelect.value === "other" && locationInputSelect.offsetParent !== null) ||
        (locationInputText.value === "" && locationInputText.offsetParent !== null)
    ) {
        switch_login();
    }
}

// Switch Login Location Input
function switch_login() {
    const locationInputSelect = document.getElementById('location_input_select');
    const locationInputText = document.getElementById('location_input_text');

    if (locationInputSelect.offsetParent !== null) {
        // If the select input is visible
        locationInputText.style.display = 'block';
        locationInputSelect.value = '';
        locationInputSelect.style.display = 'none';
    } else if (locationInputText.offsetParent !== null) {
        // If the text input is visible
        locationInputText.style.display = 'none';
        locationInputSelect.style.display = 'block';
    }
}

