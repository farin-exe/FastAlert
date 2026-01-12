<?php
session_start();
include 'db_connect.php'; // Ensure this file exists and works

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Handle Add Contact (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = trim($_POST['name']);

    // Combine Country Code and Phone Number
    $country_code = $_POST['country_code'];
    $raw_phone = trim($_POST['phone']);
    $full_phone = $country_code . ' ' . $raw_phone;

    $relation = $_POST['relation'];

    // Check limit (Max 5 contacts)
    $count_query = "SELECT COUNT(*) as c FROM guardians WHERE user_id = '$user_id'";
    $count_result = mysqli_query($conn, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $count = $count_row['c'];

    if ($count < 5 && !empty($name) && !empty($raw_phone)) {
        // Prepare Statement to prevent SQL Injection
        $stmt = $conn->prepare("INSERT INTO guardians (user_id, name, phone, relation) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $name, $full_phone, $relation);

        if ($stmt->execute()) {
            // Success
        } else {
            // Error handling (optional)
        }
        $stmt->close();
    }

    // Refresh page to show new contact
    header("Location: contacts.php");
    exit();
}

// 3. Handle Delete Contact (GET)
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']); // Security: Force integer
    $conn->query("DELETE FROM guardians WHERE id=$del_id AND user_id=$user_id");
    header("Location: contacts.php");
    exit();
}

// 4. Fetch Contacts
$sql = "SELECT * FROM guardians WHERE user_id = $user_id ORDER BY id DESC";
$result = $conn->query($sql);
$guardian_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Guardians - SafeGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-purple: #6c5ce7;
            --light-purple-bg: #e2d9f3;
        }

        body {
            background-color: #f8f9fa;
            padding-bottom: 80px;
            /* Space for bottom nav */
        }

        .bg-purple {
            background: linear-gradient(135deg, #6c5ce7, #5b4cc4);
        }

        .text-purple {
            color: var(--primary-purple) !important;
        }

        .btn-purple {
            background-color: var(--primary-purple);
            color: white;
            border: none;
        }

        .btn-purple:hover {
            background-color: #5b4cc4;
            color: white;
        }

        .btn-outline-purple {
            color: var(--primary-purple);
            border: 1px dashed var(--primary-purple);
            background: white;
        }

        .nav-item.active {
            color: var(--primary-purple) !important;
        }

        /* Mobile empty state icon */
        .empty-state-icon {
            font-size: 4rem;
            color: #d1c4e9;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-purple sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-arrow-left me-2"></i> My Guardians
            </a>
            <span class="badge bg-white text-purple rounded-pill shadow-sm">
                <?php echo $guardian_count; ?>/5
            </span>
        </div>
    </nav>

    <div class="container py-4">

        <div class="alert alert-light border-start border-4 border-purple shadow-sm small text-muted mb-4">
            <div class="d-flex">
                <i class="fas fa-info-circle me-2 mt-1 text-purple"></i>
                <div>
                    Guardians receive your <strong>Live Location</strong> and <strong>Audio</strong> when SOS is
                    triggered.
                </div>
            </div>
        </div>

        <button class="btn btn-outline-purple w-100 py-3 mb-4 rounded-3 fw-bold shadow-sm" data-bs-toggle="modal"
            data-bs-target="#addContactModal" <?php echo ($guardian_count >= 5) ? 'disabled' : ''; ?>>
            <i class="fas fa-plus-circle me-2"></i>
            <?php echo ($guardian_count >= 5) ? 'Limit Reached (Max 5)' : 'Add New Guardian'; ?>
        </button>

        <div class="row g-3">

            <?php if ($guardian_count == 0): ?>
                <div class="text-center mt-5 pt-4">
                    <i class="fas fa-user-plus empty-state-icon mb-3"></i>
                    <h6 class="text-muted fw-bold">No Guardians Added</h6>
                    <p class="small text-muted px-4">Add family or close friends to ensure your safety alerts reach someone.
                    </p>
                </div>
            <?php endif; ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                // Logic to choose icon/color based on relation
                $icon = "fa-user";
                $bg_color = "#e2d9f3"; // Default light purple
            
                if ($row['relation'] === "Official") {
                    $icon = "fa-building-shield";
                    $bg_color = "#cfe2ff"; // Light blue for police
                }
                if ($row['relation'] === "Family") {
                    $icon = "fa-house-user";
                    $bg_color = "#f8d7da"; // Light red for family
                }
                ?>

                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                    style="width: 50px; height: 50px; background-color: <?php echo $bg_color; ?>; color: #444;">
                                    <i class="fas <?php echo $icon; ?> fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </h6>
                                    <div class="small text-muted">
                                        <i class="fas fa-phone-alt fa-xs me-1"></i>
                                        <?php echo htmlspecialchars($row['phone']); ?>
                                    </div>
                                    <span class="badge bg-light text-secondary border mt-1">
                                        <?php echo htmlspecialchars($row['relation']); ?>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <a href="contacts.php?delete_id=<?php echo $row['id']; ?>"
                                    class="btn btn-sm btn-light text-danger border rounded-circle shadow-sm"
                                    style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                    onclick="return confirm('Remove <?php echo htmlspecialchars($row['name']); ?> from guardians?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="modal fade" id="addContactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-purple">New Guardian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="contacts.php" method="POST">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="fas fa-user text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-start-0 bg-light" required
                                    placeholder="e.g. Mom">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Phone Number</label>
                            <div class="input-group">
                                <select class="form-select bg-light" name="country_code" style="max-width: 110px;">
                                    <option value="+880" selected>🇧🇩 +880</option>
                                    <option value="+60">🇲🇾 +60</option>
                                    <option value="+1">🇺🇸 +1</option>
                                    <option value="+44">🇬🇧 +44</option>
                                    <option value="+91">🇮🇳 +91</option>
                                </select>
                                <input type="tel" name="phone" class="form-control bg-light" required
                                    placeholder="1700000000">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Relationship</label>
                            <select class="form-select bg-light" name="relation">
                                <option value="Family">Family</option>
                                <option value="Friend">Friend</option>
                                <option value="Partner">Partner</option>
                                <option value="Official">Police/Official</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-purple rounded-pill shadow-sm py-2 fw-bold">Save
                                Contact</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed-bottom bg-white shadow-lg rounded-top-4 border-top">
        <div class="d-flex justify-content-around py-3">
            <a href="index.php" class="nav-item text-muted"><i class="fas fa-home fa-lg"></i></a>
            <a href="map.php" class="nav-item text-muted"><i class="fas fa-map-marked-alt fa-lg"></i></a>
            <a href="contacts.php" class="nav-item text-purple active"><i class="fas fa-user-group fa-lg"></i></a>
            <a href="profile.php" class="nav-item text-muted"><i class="fas fa-user fa-lg"></i></a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>