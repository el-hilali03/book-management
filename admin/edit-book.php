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
    header("Location: ../index.php");
    exit;
}

// Get book ID
$id = intval($_GET["id"] ?? $_POST["id"] ?? 0);

if ($id <= 0) {
    header("Location: ../books/index.php");
    exit;
}

// Get current book
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$book = $result->fetch_assoc();

if (!$book) {
    header("Location: ../books/index.php");
    exit;
}

$error = "";
$success = "";

// Update book
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $published_year = trim($_POST["published_year"] ?? "");

    // Validation
    if (empty($title) || empty($author)) {

        $error = "Title and author are required.";

    } elseif (
        !empty($published_year) &&
        (!is_numeric($published_year) ||
        $published_year < 1000 ||
        $published_year > date("Y"))
    ) {

        $error = "Please enter a valid published year.";

    } else {

        $newCover = $book["cover"];

        // Check if a new image was uploaded
        if (
            isset($_FILES["cover"]) &&
            $_FILES["cover"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES["cover"]["error"] !== UPLOAD_ERR_OK) {

                $error = "There was an error uploading the image.";

            } else {

                $file = $_FILES["cover"];

                // Maximum size: 5 MB
                if ($file["size"] > 5 * 1024 * 1024) {

                    $error = "Image size must be less than 5 MB.";

                } else {

                    // Check MIME type
                    $allowedTypes = [
                        "image/jpeg" => "jpg",
                        "image/png" => "png",
                        "image/webp" => "webp"
                    ];

                    $mimeType = mime_content_type($file["tmp_name"]);

                    if (!isset($allowedTypes[$mimeType])) {

                        $error = "Only JPG, PNG and WEBP images are allowed.";

                    } else {

                        $extension = $allowedTypes[$mimeType];

                        $newFileName =
                            uniqid("book_", true) . "." . $extension;

                        $uploadPath =
                            "../assets/images/" . $newFileName;

                        if (move_uploaded_file(
                            $file["tmp_name"],
                            $uploadPath
                        )) {

                            $newCover = $newFileName;

                        } else {

                            $error = "Failed to save the uploaded image.";
                        }
                    }
                }
            }
        }

        // Update database
        if (empty($error)) {

            $stmt = $conn->prepare(
                "UPDATE books
                 SET title = ?,
                     author = ?,
                     description = ?,
                     category = ?,
                     published_year = ?,
                     cover = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "ssssisi",
                $title,
                $author,
                $description,
                $category,
                $published_year,
                $newCover,
                $id
            );

            if ($stmt->execute()) {

                // Delete old cover if a new one was uploaded
                if (
                    $newCover !== $book["cover"] &&
                    !empty($book["cover"])
                ) {
                    $oldCoverPath =
                        "../assets/images/" . basename($book["cover"]);

                    if (
                        is_file($oldCoverPath) &&
                        realpath($oldCoverPath) !== false
                    ) {
                        unlink($oldCoverPath);
                    }
                }

                $success = "Book updated successfully.";

                // Update local book data
                $book["title"] = $title;
                $book["author"] = $author;
                $book["description"] = $description;
                $book["category"] = $category;
                $book["published_year"] = $published_year;
                $book["cover"] = $newCover;

            } else {

                // If database update failed and a new image was uploaded,
                // delete the newly uploaded image.
                if (
                    $newCover !== $book["cover"] &&
                    !empty($newCover)
                ) {

                    $newCoverPath =
                        "../assets/images/" . basename($newCover);

                    if (is_file($newCoverPath)) {
                        unlink($newCoverPath);
                    }
                }

                $error = "Failed to update the book.";
            }
        }
    }
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

    <title>Edit Book</title>

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

            <a href="users.php">
                Users
            </a>

            <a href="../auth/logout.php">
                Logout
            </a>

        </div>

    </nav>


    <!-- PAGE TITLE -->

    <h1>
        ✏️ Edit Book
    </h1>


    <!-- SUCCESS MESSAGE -->

    <?php if (!empty($success)): ?>

        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>

    <?php endif; ?>


    <!-- ERROR MESSAGE -->

    <?php if (!empty($error)): ?>

        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <!-- EDIT FORM -->

    <div class="card">

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $book["id"] ?>"
            >


            <!-- TITLE -->

            <div class="form-group">

                <label for="title">
                    Book Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= htmlspecialchars($book["title"]) ?>"
                    required
                >

            </div>


            <!-- AUTHOR -->

            <div class="form-group">

                <label for="author">
                    Author
                </label>

                <input
                    type="text"
                    id="author"
                    name="author"
                    value="<?= htmlspecialchars($book["author"]) ?>"
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
                ><?= htmlspecialchars($book["description"] ?? "") ?></textarea>

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
                    value="<?= htmlspecialchars($book["category"] ?? "") ?>"
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
                    max="<?= date("Y") ?>"
                    value="<?= htmlspecialchars($book["published_year"] ?? "") ?>"
                >

            </div>


            <!-- CURRENT COVER -->

            <?php if (!empty($book["cover"])): ?>

                <div class="form-group">

                    <label>
                        Current Cover
                    </label>

                    <img
                        src="../assets/images/<?= htmlspecialchars($book["cover"]) ?>"
                        alt="Book Cover"
                        class="book-cover"
                    >

                </div>

            <?php endif; ?>


            <!-- NEW COVER -->

            <div class="form-group">

                <label for="cover">
                    Replace Cover
                </label>

                <input
                    type="file"
                    id="cover"
                    name="cover"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small>
                    JPG, PNG or WEBP — maximum 5 MB.
                </small>

            </div>


            <!-- BUTTONS -->

            <button
                type="submit"
                id="edit-book-button"
            >
                💾 Update Book
            </button>

            <a
                href="../books/index.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    </div>


    <!-- FOOTER -->

    <div class="footer">

        <p>
            &copy; <?= date("Y") ?> Book Management System
        </p>

    </div>

</div>


<script src="../assets/js/script.js"></script>

</body>

</html>