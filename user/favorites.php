<?php

session_start();

require_once "../config/database.php";

// CHECK LOGIN

if (!isset($_SESSION["user_id"])) {

    header("Location: ../auth/login.php");

    exit;
}

$user_id = $_SESSION["user_id"];

$message = "";
$message_type = "";

// REMOVE FAVORITE

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $book_id = intval($_POST["book_id"] ?? 0);


    if ($book_id > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM favorites
             WHERE user_id = ? AND book_id = ?"
        );

        $stmt->bind_param(
            "ii",
            $user_id,
            $book_id
        );


        if ($stmt->execute()) {

            $message = "Book removed from favorites.";

            $message_type = "success";

        } else {

            $message = "Something went wrong.";

            $message_type = "error";
        }

    }

}


// GET FAVORITE BOOKS

$stmt = $conn->prepare(
    "SELECT
        books.id,
        books.title,
        books.author,
        books.description,
        books.category,
        books.published_year,
        books.cover,
        favorites.created_at AS favorite_date

     FROM favorites

     INNER JOIN books
        ON favorites.book_id = books.id

     WHERE favorites.user_id = ?

     ORDER BY favorites.created_at DESC"
);


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$result = $stmt->get_result();

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
        My Favorites - Book Management
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


        <div class="logo">
            📚 Book Management
        </div>


        <div class="nav-links">


            <a href="../index.php">
                Home
            </a>


            <a href="../books/index.php">
                Books
            </a>


            <a href="dashboard.php">
                Dashboard
            </a>


            <a href="favorites.php">
                Favorites
            </a>


            <a href="profile.php">
                Profile
            </a>


            <a href="../auth/logout.php">
                Logout
            </a>


        </div>

    </nav>



    <!-- HEADER -->

    <div class="card">
        <h1>
            ❤️ My Favorite Books
        </h1>
        <p>
            Here you can find all the books you have added to your favorites.
        </p>
    </div>

    <!-- MESSAGE -->

    <?php if (!empty($message)): ?>

        <div
            class="alert alert-<?php echo $message_type; ?>"
        >
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- FAVORITES -->

    <?php if ($result->num_rows > 0): ?>

        <div class="books-grid">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="book-card">

                    <!-- BOOK COVER -->

                    <?php if (!empty($book["cover"])): ?>
                        <img
                            src="../assets/images/<?php echo htmlspecialchars($book["cover"]); ?>"
                            alt="<?php echo htmlspecialchars($book["title"]); ?>"
                            class="book-cover"
                        >
                    <?php else: ?>

                        <div
                            class="book-cover"
                            style="
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-size:70px;
                                background:#f3f4f6;
                            "
                        >

                            📖

                        </div>


                    <?php endif; ?>



                    <!-- BOOK TITLE -->

                    <h2>

                        <?php echo htmlspecialchars($book["title"]); ?>

                    </h2>



                    <!-- AUTHOR -->

                    <p>

                        <strong>
                            Author:
                        </strong>

                        <?php echo htmlspecialchars($book["author"]); ?>

                    </p>

                    <!-- CATEGORY -->

                    <?php if (!empty($book["category"])): ?>


                        <p>

                            <strong>
                                Category:
                            </strong>

                            <?php echo htmlspecialchars($book["category"]); ?>

                        </p>


                    <?php endif; ?>


                    <!-- YEAR -->

                    <?php if (!empty($book["published_year"])): ?>

                        <p>

                            <strong>
                                Year:
                            </strong>

                            <?php echo htmlspecialchars($book["published_year"]); ?>

                        </p>


                    <?php endif; ?>



                    <!-- ACTIONS -->

                    <div
                        style="
                            margin-top:20px;
                            display:flex;
                            gap:10px;
                            flex-wrap:wrap;
                        "
                    >


                        <!-- VIEW DETAILS -->

                        <a
                            href="../books/details.php?id=<?php echo $book["id"]; ?>"
                            class="btn"
                        >
                            View Details
                        </a>



                        <!-- REMOVE FAVORITE -->

                        <form
                            method="POST"
                            class="remove-favorite-form"
                            style="margin:0;"
                        >


                            <input
                                type="hidden"
                                name="book_id"
                                value="<?php echo $book["id"]; ?>"
                            >


                            <button
                                type="submit"
                                class="btn-danger"
                            >
                                Remove
                            </button>


                        </form>


                    </div>


                </div>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <!-- EMPTY STATE -->

        <div
            class="card"
            style="
                text-align:center;
                padding:60px 20px;
            "
        >


            <div
                style="
                    font-size:70px;
                    margin-bottom:20px;
                "
            >
                ❤️
            </div>
            <h2>
                No Favorite Books Yet
            </h2>


            <p>
                You haven't added any books to your favorites.
            </p>


            <br>


            <a
                href="../books/index.php"
                class="btn"
            >
                Browse Books
            </a>


        </div>


    <?php endif; ?>



    <!-- QUICK ACTIONS -->

    <div
        class="dashboard-grid"
        style="margin-top:40px;"
    >

        <div class="dashboard-card">
            <h2>
                📚
            </h2>

            <p>
                Discover more books.
            </p>
            <br>
            <a
                href="../books/index.php"
                class="btn"
            >
                Browse Books
            </a>
        </div>

        <div class="dashboard-card">


            <h2>
                👤
            </h2>

            <p>
                Manage your account.
            </p>
            <br>
            <a
                href="profile.php"
                class="btn btn-secondary"
            >
                My Profile
            </a>

        </div>


        <div class="dashboard-card">
            <h2>
                🏠
            </h2>
            <p>
                Return to your dashboard.
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
            &copy;
            <?php echo date("Y"); ?>
            Book Management System
        </p>

    </footer>
</div>

<!-- JAVASCRIPT -->

<script src="../assets/js/script.js"></script>


</body>

</html>