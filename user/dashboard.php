<?php

session_start();

require_once "../config/database.php";

// CHECK LOGIN

if (!isset($_SESSION["user_id"])) {

    header("Location: ../auth/login.php");
    exit;

}


$user_id = $_SESSION["user_id"];

// GET USER

$stmt = $conn->prepare(
    "SELECT name, email, role, created_at
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

// COUNT FAVORITES

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM favorites
     WHERE user_id = ?"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$favorite_result = $stmt->get_result();

$favorite_data = $favorite_result->fetch_assoc();

$total_favorites = $favorite_data["total"];

$stmt->close();


// COUNT BOOKS

$books_result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM books"
);

$books_data = $books_result->fetch_assoc();

$total_books = $books_data["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>User Dashboard - Book Management</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<div class="container">


    <!-- NAVBAR -->

    <nav class="navbar">

        <a
            href="../index.php"
            class="logo"
        >
            📚 Book Management
        </a>


        <div class="nav-links">

            <a href="../index.php">
                Home
            </a>

            <a href="../books/index.php">
                Books
            </a>

            <a href="favorites.php">
                ❤️ Favorites
            </a>

            <a href="profile.php">
                Profile
            </a>

            <a href="../auth/logout.php">
                Logout
            </a>

        </div>

    </nav>


    <!-- WELCOME -->

    <div class="hero">

        <h1>
            Welcome, <?= htmlspecialchars($user["name"]) ?> 👋
        </h1>

        <p>
            Manage your account and discover new books.
        </p>

        <a
            href="../books/index.php"
            class="btn"
        >
            Browse Books
        </a>

    </div>


    <!-- STATISTICS -->

    <div class="dashboard-grid">


        <!-- Total Books -->

        <div class="dashboard-card">

            <h2>
                📚 <?= $total_books ?>
            </h2>

            <p>
                Available Books
            </p>

        </div>


        <!-- Favorites -->

        <div class="dashboard-card">

            <h2>
                ❤️ <?= $total_favorites ?>
            </h2>

            <p>
                My Favorites
            </p>

            <br>

            <a
                href="favorites.php"
                class="btn"
            >
                View Favorites
            </a>

        </div>


        <!-- Profile -->

        <div class="dashboard-card">

            <h2>
                👤
            </h2>

            <p>
                Manage My Profile
            </p>

            <br>

            <a
                href="profile.php"
                class="btn"
            >
                My Profile
            </a>

        </div>


    </div>
    <!--ACCOUNT INFORMATION-->

    <div class="card">

        <h2>
            Account Information
        </h2>


        <p>
            <strong>Name:</strong>
            <?= htmlspecialchars($user["name"]) ?>
        </p>


        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($user["email"]) ?>
        </p>


        <p>
            <strong>Role:</strong>
            <?= htmlspecialchars($user["role"]) ?>
        </p>


        <p>
            <strong>Member since:</strong>
            <?= htmlspecialchars($user["created_at"]) ?>
        </p>


    </div>


    <!--QUICK ACTIONS-->

    <h2>
        Quick Actions
    </h2>


    <div class="dashboard-grid">


        <div class="dashboard-card">

            <h2>📖</h2>

            <h3>
                Browse Books
            </h3>

            <p>
                Explore the available book collection.
            </p>

            <br>

            <a
                href="../books/index.php"
                class="btn"
            >
                Browse
            </a>

        </div>


        <div class="dashboard-card">

            <h2>❤️</h2>

            <h3>
                My Favorites
            </h3>

            <p>
                View the books you have saved.
            </p>

            <br>

            <a
                href="favorites.php"
                class="btn"
            >
                Favorites
            </a>

        </div>


        <div class="dashboard-card">

            <h2>⚙️</h2>

            <h3>
                Profile Settings
            </h3>

            <p>
                Update your personal information.
            </p>

            <br>

            <a
                href="profile.php"
                class="btn"
            >
                Settings
            </a>

        </div>


    </div>

    <!--FOOTER-->

    <footer class="footer">

        <p>
            © <?= date("Y") ?> Book Management System
        </p>
        <p>
            Built with PHP, MySQL, HTML, CSS and JavaScript.
        </p>
    </footer>

</div>

<script src="../assets/js/script.js"></script>

</body>

</html>