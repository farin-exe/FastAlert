<?php
session_start();
// Ensure this path matches where you saved your file (e.g., 'db_connect.php' or 'api/db_connect.php')
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $pin = $_POST['pin'];

    $sql = "SELECT id, name, pin_hash FROM users WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify PIN
        if (password_verify($pin, $user['pin_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid PIN code.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SafeGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Shared Background with Register Page */
        body {
            background: url('https://images.unsplash.com/photo-1494515843206-f3117d3f51b7?q=80&w=2072&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Dark overlay for readability */
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

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Input Styling */
        .form-control:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }

        .input-group-text {
            color: var(--primary-purple);
            background-color: white;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">

                <div class="text-center mb-4 text-white">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow"
                        style="width: 80px; height: 80px;">
                        <i class="fas fa-shield-halved fa-3x text-purple"></i>
                    </div>
                    <h2 class="fw-bold mt-3">SafeGuard</h2>
                    <p class="opacity-75 small">Women's Safety & Security</p>
                </div>

                <div class="card glass-card border-0 rounded-4 shadow-lg">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-center mb-4 text-purple">Welcome Back</h4>

                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center p-2 small shadow-sm">
                                <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0"><i class="fas fa-phone"></i></span>
                                    <input type="tel" name="phone" class="form-control border-start-0 bg-white"
                                        placeholder="017..." required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Security PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="pin" class="form-control border-start-0 bg-white"
                                        placeholder="****" maxlength="4" required>
                                </div>
                            </div>

                            <button type="submit"
                                class="btn btn-purple w-100 py-3 rounded-pill fw-bold shadow-sm text-white"
                                style="background-color: var(--primary-purple);">
                                Login
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="small text-muted mb-0">Don't have an account?</p>
                            <a href="register.php" class="fw-bold text-purple text-decoration-none">Register here</a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <p class="text-white-50 small mb-2">In immediate danger?</p>
                    <button
                        class="btn btn-danger w-100 py-3 rounded-4 fw-bold shadow-lg border-2 border-white d-flex align-items-center justify-content-center"
                        style="background: linear-gradient(45deg, #dc3545, #ff6b6b);">
                        <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                        QUICK EMERGENCY SOS
                    </button>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>