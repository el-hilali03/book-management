<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";

// Update profile
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if (empty($name) || empty($email)) {

        $message = "Name and email are required.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        // Check if email belongs to another user
        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? AND id != ?"
        );

        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already used by another account.";
            $message_type = "error";

        } else {

            // Update user
            $stmt = $conn->prepare(
                "UPDATE users SET name = ?, email = ? WHERE id = ?"
            );

            $stmt->bind_param("ssi", $name, $email, $user_id);

            if ($stmt->execute()) {

                $_SESSION["user_name"] = $name;

                $message = "Profile updated successfully.";
                $message_type = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }
        }
    }
}

// Get current user information
$stmt = $conn->prepare(
    "SELECT name, email, role, created_at
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile - Book Management</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="container">

    <!-- NAVBAR -->

    <nav class="navbar">

        <div class="logo">
            📚 Book Management
        </div>

        <div class="nav-links">

            <a href="../index.php">Home</a>

            <a href="../books/index.php">Books</a>

            <a href="dashboard.php">Dashboard</a>

            <a href="favorites.php">Favorites</a>

            <a href="profile.php">Profile</a>

            <a href="../auth/logout.php">Logout</a>

        </div>

    </nav>


    <!-- PROFILE -->

    <div class="card">

        <h1>My Profile</h1>

        <p>Manage your account information.</p>

    </div>


    <!-- ALERT -->

    <?php if (!empty($message)): ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- PROFILE INFORMATION -->

    <div class="card">

        <h2>Account Information</h2>

        <form method="POST">

            <div class="form-group">

                <label for="name">
                    Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($user["name"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($user["email"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Role
                </label>

                <input
                    type="text"
                    value="<?php echo htmlspecialchars($user["role"]); ?>"
                    disabled
                >

            </div>


            <div class="form-group">

                <label>
                    Account Created
                </label>

                <input
                    type="text"
                    value="<?php echo htmlspecialchars($user["created_at"]); ?>"
                    disabled
                >

            </div>


            <button type="submit">
                Update Profile
            </button>

        </form>

    </div>


    <!-- QUICK ACTIONS -->

    <div class="dashboard-grid">

        <div class="dashboard-card">

            <h2>📚</h2>

            <p>
                Browse all available books.
            </p>

            <a href="../books/index.php" class="btn">
                Browse Books
            </a>

        </div>


        <div class="dashboard-card">

            <h2>❤️</h2>

            <p>
                View your favorite books.
            </p>

            <a href="favorites.php" class="btn">
                My Favorites
            </a>

        </div>


        <div class="dashboard-card">

            <h2>🏠</h2>

            <p>
                Return to your dashboard.
            </p>

            <a href="dashboard.php" class="btn btn-secondary">
                Dashboard
            </a>

        </div>

    </div>


    <!-- FOOTER -->

    <footer class="footer">

        <p>
            &copy; <?php echo date("Y"); ?> Book Management System
        </p>

    </footer>

</div>

<script src="../assets/js/script.js"></script>

</body>

</html>