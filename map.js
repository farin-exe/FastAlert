// Initialize Map
// Default view: Dhaka, Bangladesh (Lat, Lng, Zoom Level)
var map = L.map('map').setView([23.8103, 90.4125], 13);

// Add OpenStreetMap Tile Layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);

// Custom Icons
var userIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149059.png', // Placeholder User Avatar
    iconSize: [40, 40],
    iconAnchor: [20, 20],
    popupAnchor: [0, -20],
    className: 'rounded-circle border border-3 border-white shadow'
});

var marker;
var circle;

// Function to Get User Location
function getUserLocation() {
    if (!navigator.geolocation) {
        console.log("Your browser doesn't support geolocation feature!");
    } else {
        navigator.geolocation.getCurrentPosition(getPosition);

        // Real-time tracking (Updates every 5 seconds)
        setInterval(() => {
            navigator.geolocation.getCurrentPosition(updatePosition);
        }, 5000);
    }
}

// Initial Position Setup
function getPosition(position) {
    var lat = position.coords.latitude;
    var long = position.coords.longitude;
    var accuracy = position.coords.accuracy;

    // Create Marker
    marker = L.marker([lat, long], { icon: userIcon }).addTo(map);

    // Create Accuracy Circle
    circle = L.circle([lat, long], {
        color: '#6f42c1', // Purple theme
        fillColor: '#6f42c1',
        fillOpacity: 0.15,
        radius: accuracy
    }).addTo(map);

    // Zoom to user
    map.fitBounds(circle.getBounds());

    console.log("Location found: " + lat + ", " + long);
}

// Update Position on Move
function updatePosition(position) {
    var lat = position.coords.latitude;
    var long = position.coords.longitude;
    var accuracy = position.coords.accuracy;

    if (marker) {
        marker.setLatLng([lat, long]);
        circle.setLatLng([lat, long]);
        circle.setRadius(accuracy);
    }
}

// Center Map Button Function
function centerMap() {
    navigator.geolocation.getCurrentPosition((position) => {
        map.setView([position.coords.latitude, position.coords.longitude], 16);
    });
}

// Start Location Service on Load
getUserLocation();