<?php

session_start();

require_once "../config/database.php";

// SEARCH

$search = trim($_GET["search"] ?? "");

if (!empty($search)) {

    $searchTerm = "%" . $search . "%";

    $stmt = $conn->prepare(
        "SELECT *
         FROM books
         WHERE title LIKE ?
         OR author LIKE ?
         OR category LIKE ?
         ORDER BY created_at DESC"
    );

    $stmt->bind_param(
        "sss",
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $result = $conn->query(
        "SELECT *
         FROM books
         ORDER BY created_at DESC"
    );
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

    <title>Books - Book Management</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<div class="container">


    <!--NAVBAR-->

    <nav class="navbar">

        <div class="logo">
            📚 Book Management
        </div>


        <div class="nav-links">

            <a href="../index.php">
                Home
            </a>


            <a href="index.php">
                Books
            </a>


            <?php if (isset($_SESSION["user_id"])): ?>

                <?php if ($_SESSION["user_role"] === "admin"): ?>

                    <a href="../admin/dashboard.php">
                        Admin Dashboard
                    </a>

                    <a href="../admin/add-book.php">
                        Add Book
                    </a>

                <?php else: ?>

                    <a href="../user/dashboard.php">
                        Dashboard
                    </a>

                    <a href="../user/favorites.php">
                        Favorites
                    </a>

                    <a href="../user/profile.php">
                        Profile
                    </a>

                <?php endif; ?>


                <a href="../auth/logout.php">
                    Logout
                </a>


            <?php else: ?>

                <a href="../auth/login.php">
                    Login
                </a>

                <a href="../auth/register.php">
                    Register
                </a>

            <?php endif; ?>

        </div>

    </nav>

    <!-- PAGE HEADER -->

    <div class="card">

        <h1>
            📚 All Books
        </h1>

        <p>
            Browse and discover all available books.
        </p>

    </div>



    <!-- SEARCH -->

    <form
        method="GET"
        class="search-form"
    >

        <input
            type="text"
            name="search"
            placeholder="Search by title, author or category..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            🔍 Search
        </button>

        <?php if (!empty($search)): ?>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                Clear
            </a>

        <?php endif; ?>

    </form>



    <!-- SEARCH RESULT MESSAGE -->

    <?php if (!empty($search)): ?>

        <div class="alert alert-info">

            Search results for:

            <strong>
                <?php echo htmlspecialchars($search); ?>
            </strong>

        </div>

    <?php endif; ?>
    <!-- DELETE SUCCESS MESSAGE -->

    <?php if (isset($_GET["deleted"]) && $_GET["deleted"] == "1"): ?>

        <div class="alert alert-success">

            Book deleted successfully.

        </div>

    <?php endif; ?>



    <!-- BOOKS -->

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
                            href="details.php?id=<?php echo $book["id"]; ?>"
                            class="btn"
                        >
                            View Details
                        </a>



                        <!-- ADMIN ACTIONS -->

                        <?php if (
                            isset($_SESSION["user_role"])
                            &&
                            $_SESSION["user_role"] === "admin"
                        ): ?>


                            <!-- EDIT -->

                            <a
                                href="../admin/edit-book.php?id=<?php echo $book["id"]; ?>"
                                class="btn btn-secondary"
                            >
                                Edit
                            </a>



                            <!-- DELETE -->
                             <form
                                method="POST"
                                action="../admin/delete-book.php"
                                class="delete-form"
                                style="margin:0;"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?php echo $book["id"]; ?>"
                                >
                                <button
                                    type="submit"
                                    class="btn-danger"
                                >
                                    Delete
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>


    <?php else: ?>


        <!-- NO BOOKS -->

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
                📚
            </div>


            <h2>
                No Books Found
            </h2>


            <?php if (!empty($search)): ?>

                <p>
                    No books match your search.
                </p>

                <br>
                <a
                    href="index.php"
                    class="btn"
                >
                    Show All Books
                </a>

            <?php else: ?>

                <p>
                    There are currently no books in the system.
                </p>


                <?php if (
                    isset($_SESSION["user_role"])
                    &&
                    $_SESSION["user_role"] === "admin"
                ): ?>

                    <br>

                    <a
                        href="../admin/add-book.php"
                        class="btn btn-success"
                    >
                        + Add First Book
                    </a>

                <?php endif; ?>

            <?php endif; ?>

        </div>
    <?php endif; ?>


    <!--FOOTER-->

    <footer class="footer">
        <p>&copy;   <?php echo date("Y"); ?> Book Management System</p>
    </footer>

</div>

<!--JAVASCRIPT-->

<script src="../assets/js/script.js"></script>

</body>

</html>