<?php

session_start();

require_once "../config/database.php";

// REGISTRATION

$message = "";
$message_type = "";

$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // VALIDATION

    if (
        empty($name)
        ||
        empty($email)
        ||
        empty($password)
        ||
        empty($confirm_password)
    ) {

        $message = "All fields are required.";

        $message_type = "error";


    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

        $message_type = "error";


    } elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters.";

        $message_type = "error";


    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

        $message_type = "error";


    } else {


        // CHECK EMAIL

        $stmt = $conn->prepare(
            "SELECT id
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param(
            "s",
            $email
        );

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows > 0) {

            $message = "This email is already registered.";

            $message_type = "error";


        } else {

            // HASH PASSWORD

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // CREATE USER

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password)
                VALUES (?, ?, ?)"
            );


            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );


            if ($stmt->execute()) {

                $message =
                    "Account created successfully. You can now login.";

                $message_type = "success";

                // Clear fields

                $name = "";
                $email = "";
                $password = "";

            } else {

                $message =
                    "Something went wrong. Please try again.";

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Register - Book Management
    </title>


    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>


<div class="container">


    <div class="auth-container">


        <h1>
            📝 Register
        </h1>

        <p style="text-align:center; margin-bottom:25px;">
            Create your Book Management account.
        </p>

        <!-- MESSAGE -->
        <?php if (!empty($message)): ?>
            <div
                class="alert alert-<?php echo $message_type; ?>"
            >
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- REGISTER FORM -->
        <form method="POST">

            <!-- NAME -->

            <div class="form-group">

                <label for="name">
                    Name
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your name"
                    value="<?php echo htmlspecialchars($name); ?>"
                    required
                >

            </div>

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
                    value="<?php echo htmlspecialchars($email); ?>"
                    required
                >
            </div>
            <!-- PASSWORD -->

            <div class="form-group">

                <label for="register-password">
                    Password
                </label>
                <div style="display:flex; gap:8px;">

                    <input
                        type="password"
                        id="register-password"
                        name="password"
                        placeholder="Minimum 6 characters"
                        required
                    >
                    <button
                        type="button"
                        class="toggle-password"
                        data-target="register-password"
                        style="width:auto; white-space:nowrap;"
                    >
                        Show
                    </button>

                </div>

            </div>
            <!-- CONFIRM PASSWORD -->

            <div class="form-group">

                <label for="confirm-password">
                    Confirm Password
                </label>
                <div style="display:flex; gap:8px;">

                    <input
                        type="password"
                        id="confirm-password"
                        name="confirm_password"
                        placeholder="Repeat your password"
                        required
                    >
                    <button
                        type="button"
                        class="toggle-password"
                        data-target="confirm-password"
                        style="width:auto; white-space:nowrap;"
                    >
                        Show
                    </button>

                </div>

            </div>
            <!-- SUBMIT -->

            <button type="submit">
                Create Account
            </button>
        </form>

        <!-- LOGIN -->

        <p style="text-align:center; margin-top:20px;">

            Already have an account?

            <a href="login.php">
                Login
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