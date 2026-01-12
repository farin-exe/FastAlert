<?php
session_start();
include 'db_connect.php'; // Ensure you have this file connected

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Guardian Phone Numbers for the SOS
$user_id = $_SESSION['user_id'];
$phones = [];
$sql = "SELECT phone FROM guardians WHERE user_id = $user_id";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $phones[] = $row['phone']; // Store numbers in array
}
// Convert to JSON for JavaScript to use
$guardian_phones_json = json_encode($phones);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastAlert - Women's Security</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sos-animations.css">

    <style>
        /* Your Existing Styles Preserved */
        @media (min-width: 768px) {
            .app-container {
                max-width: 480px;
                margin: 40px auto;
                background: white;
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 1rem 3rem rgba(0, 0, 0, .1);
                min-height: 80vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            body {
                background-color: #e9ecef;
            }
        }

        @media (max-width: 767px) {
            .app-container {
                padding-bottom: 90px;
            }
        }

        .nav-link-desktop {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            margin: 0 15px;
            text-decoration: none;
        }

        .nav-link-desktop:hover,
        .nav-link-desktop.active {
            color: #fff;
            border-bottom: 2px solid #fff;
        }

        /* SOS Active State - Flashing Background */
        body.sos-active {
            animation: bg-flash 1s infinite;
        }

        @keyframes bg-flash {
            0% {
                background-color: #f8f9fa;
            }

            50% {
                background-color: #ffe6e6;
            }

            100% {
                background-color: #f8f9fa;
            }
        }

        /* Pulse Animation for Button Visuals */
        .sos-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }

        .sos-button {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(145deg, #ff4757, #ff6b81);
            border: none;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            z-index: 10;
            position: relative;
        }

        .sos-button:active {
            transform: scale(0.95);
        }
    </style>
</head>

<body class="bg-light">

    <audio id="sirenSound" loop>
        <source src="https://assets.mixkit.co/active_storage/sfx/2844/2844-preview.mp3" type="audio/mpeg">
    </audio>

    <nav class="navbar navbar-expand-md navbar-dark bg-purple sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-shield-halved me-2"></i>FirstAlert</a>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <div class="navbar-nav d-none d-md-flex">
                    <a href="index.php" class="nav-link-desktop active">Dashboard</a>
                    <a href="map.php" class="nav-link-desktop">Live Map</a>
                    <a href="contacts.php" class="nav-link-desktop">Guardians</a>
                    <a href="profile.php" class="nav-link-desktop">Profile</a>
                </div>
            </div>

            <div class="d-none d-md-flex align-items-center text-white">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light rounded-circle dropdown-toggle" type="button"
                        data-bs-toggle="dropdown"><i class="fas fa-user"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="app-container d-flex flex-column justify-content-center align-items-center">

            <div class="text-center mb-5 fade-in">
                <h5 class="text-muted">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h5>
                <span id="statusBadge" class="badge bg-success px-4 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> System Active
                </span>
            </div>

            <div class="sos-container position-relative mb-4">
                <button id="sosBtn" class="sos-button shadow-lg" onmousedown="startSOS()" onmouseup="cancelSOS()"
                    ontouchstart="startSOS()" ontouchend="cancelSOS()">
                    SOS
                </button>
            </div>

            <p id="instructionText" class="text-center text-muted mt-2 mb-5 small">Press & Hold for 3s to Alert
                Guardians</p>

            <div class="row w-100 g-3">
                <div class="col-6">
                    <button
                        class="btn btn-white w-100 py-3 shadow-sm rounded-4 text-purple fw-bold border-0 h-100 d-flex flex-column align-items-center justify-content-center"
                        onclick="markSafe()">
                        <i class="fas fa-user-check fa-2x mb-2"></i> I'm Safe
                    </button>
                </div>
                <div class="col-6">
                    <button
                        class="btn btn-white w-100 py-3 shadow-sm rounded-4 text-danger fw-bold border-0 h-100 d-flex flex-column align-items-center justify-content-center"
                        onclick="toggleSiren()">
                        <i id="sirenIcon" class="fas fa-bullhorn fa-2x mb-2"></i> Siren
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed-bottom bg-white shadow-lg rounded-top-4 border-top d-md-none">
        <div class="d-flex justify-content-around py-3">
            <a href="index.php" class="nav-item text-purple active"><i class="fas fa-home fa-lg"></i></a>
            <a href="map.php" class="nav-item text-muted"><i class="fas fa-map-marked-alt fa-lg"></i></a>
            <a href="contacts.php" class="nav-item text-muted"><i class="fas fa-user-group fa-lg"></i></a>
            <a href="profile.php" class="nav-item text-muted"><i class="fas fa-user fa-lg"></i></a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. Get Guardians from PHP
        const guardianPhones = <?php echo $guardian_phones_json; ?>;

        let sosTimer;
        let isSending = false;
        const statusBadge = document.getElementById('statusBadge');
        const instructionText = document.getElementById('instructionText');

        // --- SOS BUTTON LOGIC (Hold 3s) ---
        function startSOS() {
            if (isSending) return;

            // Visual feedback
            statusBadge.className = 'badge bg-danger px-4 py-2 rounded-pill shadow-sm';
            statusBadge.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Hold for 3s...';
            document.body.classList.add('sos-active'); // Flashing background

            // Start Timer
            sosTimer = setTimeout(() => {
                triggerSOS();
            }, 3000); // 3 Seconds
        }

        function cancelSOS() {
            clearTimeout(sosTimer);
            if (!isSending) {
                // Reset visuals
                statusBadge.className = 'badge bg-success px-4 py-2 rounded-pill shadow-sm';
                statusBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> System Active';
                document.body.classList.remove('sos-active');
            }
        }

        // --- LOCATION & SMS LOGIC ---
        function triggerSOS() {
            isSending = true;
            statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Getting GPS...';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(sendAlert, showError, {
                    enableHighAccuracy: true,
                    timeout: 10000
                });
            } else {
                alert("Geolocation is not supported by your browser.");
                resetSystem();
            }
        }

        function sendAlert(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Google Maps Link
            const mapLink = `https://www.google.com/maps?q=${lat},${lng}`;

            // --- THE UPDATED URGENT MESSAGE ---
            const message = `🚨 EMERGENCY ALERT 🚨\n\nI need help immediately! I am in danger.\n\nMy current location:\n${mapLink}\n\nPlease contact me or send help ASAP.`;

            if (guardianPhones.length > 0) {
                // Join phone numbers (Some phones support comma, others semicolon)
                const phoneString = guardianPhones.join(',');

                // Open SMS App
                window.location.href = `sms:${phoneString}?body=${encodeURIComponent(message)}`;

                statusBadge.innerHTML = 'Alert Generated!';
            } else {
                alert("No Guardians found! Please add contacts in the Guardian tab.");
            }

            // Reset after a delay
            setTimeout(resetSystem, 5000);
        }

        function showError(error) {
            alert("GPS Error: " + error.message);
            resetSystem();
        }

        function resetSystem() {
            isSending = false;
            document.body.classList.remove('sos-active');
            statusBadge.className = 'badge bg-success px-4 py-2 rounded-pill shadow-sm';
            statusBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> System Active';
        }

        // --- SIREN LOGIC ---
        let sirenPlaying = false;
        const sirenAudio = document.getElementById('sirenSound');
        const sirenIcon = document.getElementById('sirenIcon');

        function toggleSiren() {
            if (sirenPlaying) {
                sirenAudio.pause();
                sirenAudio.currentTime = 0;
                sirenIcon.classList.remove('text-danger', 'fa-beat-fade');
                sirenPlaying = false;
            } else {
                sirenAudio.play();
                sirenIcon.classList.add('text-danger', 'fa-beat-fade');
                sirenPlaying = true;
            }
        }

        // --- SAFE LOGIC ---
        function markSafe() {
            alert("Safe status logged. (In a full app, this would notify guardians you are safe).");
            resetSystem();
        }
    </script>
</body>

</html>