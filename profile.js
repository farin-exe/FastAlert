// Get Toggles
const audioToggle = document.getElementById('audioToggle');
const cameraToggle = document.getElementById('cameraToggle');

// Load Settings from LocalStorage
document.addEventListener('DOMContentLoaded', () => {
    const settings = JSON.parse(localStorage.getItem('userSettings')) || {
        audio: true,
        camera: false
    };

    audioToggle.checked = settings.audio;
    cameraToggle.checked = settings.camera;
});

// Save Settings on Change
function saveSettings() {
    const settings = {
        audio: audioToggle.checked,
        camera: cameraToggle.checked
    };
    localStorage.setItem('userSettings', JSON.stringify(settings));
    console.log("Settings Saved:", settings);
}

audioToggle.addEventListener('change', saveSettings);
cameraToggle.addEventListener('change', saveSettings);

// Fake Call Simulation
function triggerFakeCall() {
    // Simulating a delay
    setTimeout(() => {
        alert("Incoming Call: 'Mom' \n(Ring Ring...)");
    }, 2000);
}