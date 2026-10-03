<?php

session_start();

require_once "../config/database.php";

// CHECK LOGIN
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

// CHECK ADMIN
if ($_SESSION["user_role"] !== "admin") {
    header("Location: ../user/dashboard.php");
    exit;
}

$message = "";
$message_type = "";
// ADD BOOK
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $published_year = trim($_POST["published_year"] ?? "");
    $cover = "";
    
    // VALIDATION
    if (empty($title) || empty($author)) {

        $message = "Title and author are required.";
        $message_type = "error";

    } elseif (
        !empty($published_year)
        &&
        (
            !is_numeric($published_year)
            ||
            $published_year < 1000
            ||
            $published_year > date("Y")
        )
    ) {

        $message = "Please enter a valid publication year.";
        $message_type = "error";

    } else {


        // IMAGE UPLOAD
        if (
            isset($_FILES["cover"])
            &&
            $_FILES["cover"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES["cover"]["error"] !== UPLOAD_ERR_OK) {

                $message = "There was a problem uploading the cover.";
                $message_type = "error";

            } else {

                $allowed_types = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];

                $file_type = mime_content_type(
                    $_FILES["cover"]["tmp_name"]
                );


                if (!in_array($file_type, $allowed_types)) {

                    $message = "Only JPG, PNG and WEBP images are allowed.";
                    $message_type = "error";

                } elseif ($_FILES["cover"]["size"] > 5 * 1024 * 1024) {

                    $message = "The image must be smaller than 5 MB.";
                    $message_type = "error";

                } else {

                    $extension = strtolower(
                        pathinfo(
                            $_FILES["cover"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );

                    $cover = uniqid("book_", true) . "." . $extension;

                    $upload_path =
                        "../assets/images/" . $cover;


                    if (!move_uploaded_file(
                        $_FILES["cover"]["tmp_name"],
                        $upload_path
                    )) {

                        $message = "Failed to save the cover image.";
                        $message_type = "error";

                        $cover = "";
                    }
                }
            }
        }


        // INSERT BOOK
        if (empty($message)) {

            if ($published_year === "") {

                $published_year = null;
            }


            $stmt = $conn->prepare(
                "INSERT INTO books
                (
                    title,
                    author,
                    description,
                    category,
                    published_year,
                    cover
                )
                VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "ssssss",
                $title,
                $author,
                $description,
                $category,
                $published_year,
                $cover
            );


            if ($stmt->execute()) {

                $message = "Book added successfully.";
                $message_type = "success";

                // Clear fields
                $title = "";
                $author = "";
                $description = "";
                $category = "";
                $published_year = "";
                $cover = "";

            } else {

                $message = "Failed to add the book.";
                $message_type = "error";
            }
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Add Book - Book Management
    </title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="container">


    <!-- ==========================================
         NAVBAR
    ========================================== -->
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
            <a href="add-book.php">
                Add Book
            </a>
            <a href="users.php">
                Users
            </a>
            <a href="../auth/logout.php">
                Logout
            </a>
        </div>
    </nav>


    <div class="card">
        <h1>
            ➕ Add New Book
        </h1>
        <p>
            Add a new book to the Book Management System.
        </p>
    </div>

    <?php if (!empty($message)): ?>
        <div
            class="alert alert-<?php echo $message_type; ?>"
        >
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card">


        <form
            method="POST"
            enctype="multipart/form-data"
            id="add-book-form"
        >
            <!-- TITLE -->
            <div class="form-group">
                <label for="title">
                    Book Title *
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Enter book title"
                    value="<?php echo htmlspecialchars($title ?? ""); ?>"
                    required
                >
            </div>

            <!-- AUTHOR -->
            <div class="form-group">

                <label for="author">
                    Author *
                </label>

                <input
                    type="text"
                    id="author"
                    name="author"
                    placeholder="Enter author name"
                    value="<?php echo htmlspecialchars($author ?? ""); ?>"
                    required
                >

            </div>

            <!-- DESCRIPTION -->
            <div class="form-group">

                <label for="description">
                    Description
                </label>
                <textarea
                    id="description"
                    name="description"
                    placeholder="Enter book description"
                ><?php echo htmlspecialchars($description ?? ""); ?></textarea>

            </div>

            <!-- CATEGORY -->
            <div class="form-group">

                <label for="category">
                    Category
                </label>

                <input
                    type="text"
                    id="category"
                    name="category"
                    placeholder="Example: Programming"
                    value="<?php echo htmlspecialchars($category ?? ""); ?>"
                >

            </div>



            <!-- YEAR -->

            <div class="form-group">

                <label for="published_year">
                    Published Year
                </label>

                <input
                    type="number"
                    id="published_year"
                    name="published_year"
                    min="1000"
                    max="<?php echo date("Y"); ?>"
                    placeholder="Example: 2024"
                    value="<?php echo htmlspecialchars($published_year ?? ""); ?>"
                >

            </div>

            <!-- COVER -->
            <div class="form-group">

                <label for="cover">
                    Book Cover
                </label>

                <input
                    type="file"
                    id="cover"
                    name="cover"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small>
                    Allowed: JPG, PNG, WEBP — Maximum 5 MB.
                </small>

            </div>
            <!-- ACTIONS -->
            <div
                style="
                    display:flex;
                    gap:10px;
                    flex-wrap:wrap;
                    margin-top:25px;
                "
            >
                <button
                    type="submit"
                    id="add-book-button"
                >
                    ➕ Add Book
                </button>
                <a
                    href="../books/index.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>
        </form>
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

<script src="../assets/js/script.js"></script>


</body>

</html>