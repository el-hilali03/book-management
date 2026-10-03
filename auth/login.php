<?php

session_start();

require_once "../config/database.php";

// LOGIN
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    // Validation

    if (empty($email) || empty($password)) {

        $error = "Email and password are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        // Find user
        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();


            // Verify password
            if (password_verify($password, $user["password"])) {

                // Create session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_role"] = $user["role"];


                // Redirect
                if ($user["role"] === "admin") {

                    header("Location: ../admin/dashboard.php");

                } else {

                    header("Location: ../user/dashboard.php");
                }

                exit;

            } else {

                $error = "Incorrect email or password.";
            }

        } else {

            $error = "Incorrect email or password.";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Book Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="container">
    <div class="auth-container">
        <h1>
            🔐 Login
        </h1>
        <p style="text-align:center; margin-bottom:25px;">
            Login to your Book Management account.
        </p>
        <!-- ERROR -->
        <?php if (!empty($error)): ?>

            <div class="alert alert-error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form method="POST">
            <!-- EMAIL -->
            <div class="form-group">

                <label for="email">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($email ?? ""); ?>"
                    required
                >
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label for="login-password">
                    Password
                </label>
                <div style="display:flex; gap:8px;">

                    <input type="password" id="login-password" name="password" placeholder="Enter your password" required>

                    <button type="button" class="toggle-password" data-target="login-password" style="width:auto; white-space:nowrap;">
                        Show
                    </button>
                </div>
            </div>

            <!-- SUBMIT -->
            <button type="submit">
                Login
            </button>
        </form>

        <!-- REGISTER -->
        <p style="text-align:center; margin-top:20px;">
            Don't have an account?

            <a href="register.php">
                Create one
            </a>

        </p>

        <!-- HOME -->
        <p style="text-align:center; margin-top:10px;">

            <a href="../index.php">
                ← Back to Home
            </a>
        </p>
    </div>
</div>

<!-- JAVASCRIPT -->
<script src="../assets/js/script.js"></script>


</body>

</html>