<?php

session_start();

require_once "../config/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check if user is admin
if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../user/dashboard.php");
    exit;
}


// Count books
$result = $conn->query("SELECT COUNT(*) AS total FROM books");
$books_count = $result->fetch_assoc()["total"];


// Count users
$result = $conn->query("SELECT COUNT(*) AS total FROM users");
$users_count = $result->fetch_assoc()["total"];


// Count favorites
$result = $conn->query("SELECT COUNT(*) AS total FROM favorites");
$favorites_count = $result->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - Book Management</title>

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

            <a href="dashboard.php">Admin Dashboard</a>

            <a href="add-book.php">Add Book</a>

            <a href="users.php">Users</a>

            <a href="../auth/logout.php">Logout</a>

        </div>

    </nav>


    <!-- HERO -->

    <section class="hero">

        <h1>
            🛠️ Admin Dashboard
        </h1>

        <p>
            Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>.
            Manage your Book Management System from here.
        </p>

        <a
            href="add-book.php"
            class="btn"
        >
            + Add New Book
        </a>

    </section>


    <!-- STATISTICS -->

    <h2>
        System Statistics
    </h2>

    <div class="dashboard-grid">

        <!-- BOOKS -->

        <div class="dashboard-card">

            <h2>
                📚 <?php echo $books_count; ?>
            </h2>

            <p>
                Total Books
            </p>

            <br>

            <a
                href="../books/index.php"
                class="btn"
            >
                Manage Books
            </a>

        </div>


        <!-- USERS -->

        <div class="dashboard-card">

            <h2>
                👥 <?php echo $users_count; ?>
            </h2>

            <p>
                Registered Users
            </p>

            <br>

            <a
                href="users.php"
                class="btn"
            >
                Manage Users
            </a>

        </div>


        <!-- FAVORITES -->

        <div class="dashboard-card">

            <h2>
                ❤️ <?php echo $favorites_count; ?>
            </h2>

            <p>
                Total Favorites
            </p>

            <br>

            <a
                href="../books/index.php"
                class="btn btn-secondary"
            >
                View Books
            </a>

        </div>

    </div>


    <!-- ADMIN ACTIONS -->

    <h2>
        Management
    </h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">

            <h2>➕</h2>

            <p>
                Add a new book to the system.
            </p>

            <br>

            <a
                href="add-book.php"
                class="btn btn-success"
            >
                Add Book
            </a>

        </div>


        <div class="dashboard-card">

            <h2>📖</h2>

            <p>
                View, edit or delete books.
            </p>

            <br>

            <a
                href="../books/index.php"
                class="btn"
            >
                Manage Books
            </a>

        </div>


        <div class="dashboard-card">

            <h2>👥</h2>
            <p>
                Manage registered users and their roles.
            </p>

            <br>

            <a
                href="users.php"
                class="btn"
            >
                Manage Users
            </a>

        </div>

    </div>


    <!-- ADMIN INFORMATION -->

    <div class="card">

        <h2>
            Admin Account
        </h2>

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
        </p>

        <p>
            <strong>Role:</strong>
            Administrator
        </p>

        <br>

        <a
            href="../index.php"
            class="btn btn-secondary"
        >
            Back to Home
        </a>

        <a
            href="../auth/logout.php"
            class="btn btn-danger"
        >
            Logout
        </a>

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