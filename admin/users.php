<?php

session_start();

require_once "../config/database.php";

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check admin
if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../user/dashboard.php");
    exit;
}

$message = "";
$message_type = "";


// Change user role
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = intval($_POST["user_id"] ?? 0);
    $new_role = $_POST["role"] ?? "";

    // Prevent changing own role
    if ($user_id == $_SESSION["user_id"]) {

        $message = "You cannot change your own role.";
        $message_type = "error";

    } elseif (!in_array($new_role, ["user", "admin"])) {

        $message = "Invalid role.";
        $message_type = "error";

    } elseif ($user_id <= 0) {

        $message = "Invalid user.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare(
            "UPDATE users SET role = ? WHERE id = ?"
        );

        $stmt->bind_param("si", $new_role, $user_id);

        if ($stmt->execute()) {

            $message = "User role updated successfully.";
            $message_type = "success";

        } else {

            $message = "Failed to update user role.";
            $message_type = "error";
        }
    }
}


// Get users
$result = $conn->query(
    "SELECT id, name, email, role, created_at
     FROM users
     ORDER BY created_at DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Users - Book Management</title>

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

            <a href="add-book.php">Add Book</a>

            <a href="users.php">Users</a>

            <a href="../auth/logout.php">Logout</a>

        </div>

    </nav>


    <!-- HEADER -->

    <div class="card">

        <h1>👥 Manage Users</h1>

        <p>
            View registered users and manage their roles.
        </p>

    </div>


    <!-- MESSAGE -->

    <?php if (!empty($message)): ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- USERS TABLE -->

    <?php if ($result->num_rows > 0): ?>

        <div style="overflow-x:auto;">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Role</th>

                        <th>Created</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($user = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $user["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["email"]); ?>
                        </td>

                        <td>

                            <?php if ($user["role"] === "admin"): ?>

                                <span style="
                                    display:inline-block;
                                    padding:5px 10px;
                                    border-radius:20px;
                                    background:#dbeafe;
                                    color:#1e40af;
                                    font-weight:600;
                                ">
                                    Admin
                                </span>

                            <?php else: ?>

                                <span style="
                                    display:inline-block;
                                    padding:5px 10px;
                                    border-radius:20px;
                                    background:#f3f4f6;
                                    color:#374151;
                                    font-weight:600;
                                ">
                                    User
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["created_at"]); ?>
                        </td>

                        <td>

                            <?php if ($user["id"] == $_SESSION["user_id"]): ?>

                                <span style="color:#6b7280; font-weight:600;">
                                    Current account
                                </span>

                            <?php else: ?>

                                <form method="POST" style="margin:0;">

                                    <input
                                        type="hidden"
                                        name="user_id"
                                        value="<?php echo $user["id"]; ?>"
                                    >

                                    <?php if ($user["role"] === "admin"): ?>

                                        <input
                                            type="hidden"
                                            name="role"
                                            value="user"
                                        >

                                        <button
                                            type="submit"
                                            class="btn-danger"
                                        >
                                            Make User
                                        </button>

                                    <?php else: ?>

                                        <input
                                            type="hidden"
                                            name="role"
                                            value="admin"
                                        >

                                        <button
                                            type="submit"
                                            class="btn-success"
                                        >
                                            Make Admin
                                        </button>

                                    <?php endif; ?>

                                </form>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="card" style="text-align:center;">

            <h2>No Users Found</h2>

            <p>
                There are currently no registered users.
            </p>

        </div>

    <?php endif; ?>


    <!-- QUICK ACTIONS -->

    <div class="dashboard-grid" style="margin-top:40px;">

        <div class="dashboard-card">

            <h2>📚</h2>

            <p>
                Manage all books.
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

            <h2>➕</h2>
            <p>
                Add a new book.
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

            <h2>📊</h2>

            <p>
                Return to the admin dashboard.
            </p>

            <br>

            <a
                href="dashboard.php"
                class="btn btn-secondary"
            >
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