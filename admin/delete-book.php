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
    die("Access denied. Admins only.");
}

// Check book ID
$id = $_POST["id"] ?? 0;

if (!is_numeric($id) || $id <= 0) {
    die("Invalid book ID.");
}

// Delete book
$stmt = $conn->prepare(
    "DELETE FROM books WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    $stmt->close();

    header("Location: ../books/index.php?deleted=1");
    exit;

} else {

    $error = $stmt->error;

    $stmt->close();

    die("Error deleting book: " . $error);
}