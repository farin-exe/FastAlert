<?php
include 'db_connect.php';
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $pin = $_POST['pin'];

    // Create Secure Hash for PIN
    $pin_hash = password_hash($pin, PASSWORD_DEFAULT);

    // Check if phone exists
    $check = $conn->query("SELECT id FROM users WHERE phone='$phone'");
    if ($check->num_rows > 0) {
        $msg = "<div class='alert alert-danger'>Phone number already registered!</div>";
    } else {
        $sql = "INSERT INTO users (name, phone, pin_hash) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $phone, $pin_hash);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Registration Successful! <a href='login.php' class='fw-bold'>Login here</a></div>";
        } else {
            $msg = "<div class='alert alert-danger'>Error registering user.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SafeGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* New Background Styling */
        body {
            /* High-quality Unsplash image representing safety/city lights at night */
            background: url('https://images.unsplash.com/photo-1494515843206-f3117d3f51b7?q=80&w=2072&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Dark overlay to make text pop */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(76, 44, 150, 0.85), rgba(44, 26, 88, 0.9));
            z-index: -1;
        }

        /* Glassmorphism Card Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-control:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }

        /* Floating Labels adjustment for better UX */
        .input-group-text {
            color: var(--primary-purple);
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">

                <div class="text-center mb-4 text-white fade-in">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg"
                        style="width: 70px; height: 70px;">
                        <i class="fas fa-shield-halved fa-2x text-purple"></i>
                    </div>
                    <h3 class="fw-bold mt-2">Join SafeGuard</h3>
                    <p class="small opacity-75">Your Safety, Your Priority</p>
                </div>

                <div class="card glass-card border-0 rounded-4 shadow-lg">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-center mb-4 text-purple">Create Account</h4>
                        <?php echo $msg; ?>

                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-user"></i></span>
                                    <input type="text" name="name" class="form-control bg-white border-start-0"
                                        placeholder="e.g. Sadia Rahman" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-phone"></i></span>
                                    <input type="tel" name="phone" class="form-control bg-white border-start-0"
                                        placeholder="017..." required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="small fw-bold text-muted mb-1">Create Security PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="fas fa-lock"></i></span>
                                    <input type="password" name="pin" class="form-control bg-white border-start-0"
                                        maxlength="4" placeholder="****" required>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">* 4-digit code for quick
                                    login</small>
                            </div>

                            <button type="submit"
                                class="btn btn-purple w-100 py-3 rounded-pill fw-bold text-white shadow-sm transition-hover">
                                Create Account
                            </button>
                        </form>

                        <div class="text-center mt-4 border-top pt-3">
                            <p class="small text-muted mb-0">Already have an account?</p>
                            <a href="login.php" class="fw-bold text-purple text-decoration-none">Login here</a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 text-white-50 small">
                    <i class="fas fa-lock me-1"></i> Secure & Encrypted Data
                </div>

            </div>
        </div>
    </div>
</body>

</html>