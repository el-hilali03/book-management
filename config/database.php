<?php


// DATABASE CONFIGURATION
$host = "localhost";
$username = "root";
$password = "";
$database = "book_management";

// DATABASE CONNECTION
$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");


// Create CSRF token if it doesn't exist
if (!isset($_SESSION["csrf_token"])) {

    $_SESSION["csrf_token"] = bin2hex(
        random_bytes(32)
    );
}


// Return CSRF token
function csrf_token()
{
    return $_SESSION["csrf_token"];
}


// Generate hidden CSRF input
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(
            $_SESSION["csrf_token"],
            ENT_QUOTES,
            "UTF-8"
        ) .
        '">';
}


// Verify CSRF token
function verify_csrf_token()
{
    if (
        !isset($_POST["csrf_token"]) ||
        !isset($_SESSION["csrf_token"]) ||
        !hash_equals(
            $_SESSION["csrf_token"],
            $_POST["csrf_token"]
        )
    ) {
        die("Invalid security token.");
    }
}

?>