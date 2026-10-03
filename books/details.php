<?php

session_start();

require_once "../config/database.php";


// CHECK BOOK ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid book ID.");
}

$book_id = (int) $_GET["id"];

// ADD / REMOVE FAVORITE
if (
    isset($_SESSION["user_id"]) &&
    isset($_POST["favorite_action"])
) {

    $user_id = $_SESSION["user_id"];
    $action = $_POST["favorite_action"];


    // Add favorite
    if ($action === "add") {

        $stmt = $conn->prepare(
            "INSERT IGNORE INTO favorites (user_id, book_id)
             VALUES (?, ?)"
        );

        $stmt->bind_param("ii", $user_id, $book_id);

        $stmt->execute();

        $stmt->close();
    }


    // Remove favorite
    elseif ($action === "remove") {

        $stmt = $conn->prepare(
            "DELETE FROM favorites
             WHERE user_id = ? AND book_id = ?"
        );

        $stmt->bind_param("ii", $user_id, $book_id);

        $stmt->execute();

        $stmt->close();
    }


    // Refresh page
    header("Location: details.php?id=" . $book_id);
    exit;
}

// GET BOOK

$stmt = $conn->prepare(
    "SELECT *
     FROM books
     WHERE id = ?"
);

$stmt->bind_param("i", $book_id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {
    die("Book not found.");
}

$book = $result->fetch_assoc();

$stmt->close();

// CHECK FAVORITE

$is_favorite = false;

if (isset($_SESSION["user_id"])) {

    $user_id = $_SESSION["user_id"];

    $stmt = $conn->prepare(
        "SELECT id
         FROM favorites
         WHERE user_id = ? AND book_id = ?"
    );

    $stmt->bind_param("ii", $user_id, $book_id);

    $stmt->execute();

    $favorite_result = $stmt->get_result();

    if ($favorite_result->num_rows > 0) {
        $is_favorite = true;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($book["title"]) ?> - Book Management
    </title>

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

            <a href="index.php">
                Books
            </a>


            <?php if (isset($_SESSION["user_id"])) { ?>

                <?php if ($_SESSION["user_role"] === "admin") { ?>

                    <a href="../admin/dashboard.php">
                        Admin Dashboard
                    </a>

                <?php } else { ?>

                    <a href="../user/dashboard.php">
                        Dashboard
                    </a>

                <?php } ?>


                <a href="../user/favorites.php">
                    ❤️ Favorites
                </a>


                <a href="../user/profile.php">
                    Profile
                </a>


                <a href="../auth/logout.php">
                    Logout
                </a>


            <?php } else { ?>

                <a href="../auth/login.php">
                    Login
                </a>

                <a href="../auth/register.php">
                    Register
                </a>

            <?php } ?>

        </div>

    </nav>

    <!-- BOOK DETAILS-->

    <div class="book-details">
        <?php if (!empty($book["cover"])) { ?>
            <img
                src="<?= htmlspecialchars($book["cover"]) ?>"
                alt="<?= htmlspecialchars($book["title"]) ?>"
            >
        <?php } else { ?>
            <div
                style="
                    width:250px;
                    height:350px;
                    background:#e5e7eb;
                    border-radius:10px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:80px;
                    margin-bottom:20px;
                "
            >
                📖
            </div>
        <?php } ?>
        <h1>
            <?= htmlspecialchars($book["title"]) ?>
        </h1>
        <p>
            <strong>Author:</strong>
            <?= htmlspecialchars($book["author"]) ?>
        </p>
        <?php if (!empty($book["category"])) { ?>
            <p>
                <strong>Category:</strong>
                <?= htmlspecialchars($book["category"]) ?>
            </p>
        <?php } ?>
        <?php if (!empty($book["published_year"])) { ?>
            <p>
                <strong>Published Year:</strong>
                <?= htmlspecialchars($book["published_year"]) ?>
            </p>
        <?php } ?>
        <br>
        <h2>
            Description
        </h2>
        <p>
            <?= nl2br(
                htmlspecialchars(
                    $book["description"] ?? "No description available."
                )
            ) ?>
        </p>
        <br>
        <hr>
        <br>
        <!-- ========================================
             FAVORITES
        ========================================= -->

        <?php if (isset($_SESSION["user_id"])) { ?>
            <?php if (!$is_favorite) { ?>
                <form method="POST">
                    <input
                        type="hidden"
                        name="favorite_action"
                        value="add"
                    >
                    <button type="submit">
                        ❤️ Add to Favorites
                    </button>
                </form>
            <?php } else { ?>
                <div class="alert alert-info">
                    ❤️ This book is already in your favorites.
                </div>
                <form method="POST">

                    <input
                        type="hidden"
                        name="favorite_action"
                        value="remove"
                    >

                    <button
                        type="submit"
                        class="btn-danger"
                    >
                        Remove from Favorites
                    </button>
                </form>
                <br>
                <a
                    href="../user/favorites.php"
                    class="btn"
                >
                    My Favorites
                </a>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-info">

                You need to login to add this book to your favorites.

            </div>
            <a
                href="../auth/login.php"
                class="btn"
            >
                Login
            </a>
        <?php } ?>
        <br><br>
        <a
            href="index.php"
            class="btn btn-secondary"
        >
            ← Back to Books
        </a>
    </div>
    <footer class="footer">
        <p>
            © <?= date("Y") ?> Book Management System
        </p>
    </footer>
</div>

<script src="../assets/js/script.js"></script>

</body>

</html>